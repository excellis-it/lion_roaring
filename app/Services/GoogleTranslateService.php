<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Google Cloud Translation API v2 client.
 *
 * Everything here exists to make translation cost converge to zero over time.
 * Cost levers, in order of impact:
 *   1. Permanent DB cache keyed by sha1(normalized text) + target — each unique
 *      string is paid for ONCE, ever, across every user and every page.
 *   2. An explicit `source` language. Google bills language detection at the same
 *      rate as translation, so omitting the source DOUBLES the bill.
 *   3. Normalization before hashing — "Save  changes\n" and "Save changes" collapse
 *      to one cache entry instead of two.
 *   4. Per-call dedupe — a string repeated 40 times on a page is sent once.
 *   5. isTranslatable() — numbers, money, dates, emails, URLs and single glyphs
 *      never reach the API. Google would happily bill for them.
 *   6. Optional monthly budget (TRANSLATE_MONTHLY_CHAR_LIMIT). 0 = unlimited.
 */
class GoogleTranslateService
{
    private const ENDPOINT = 'https://translation.googleapis.com/language/translate/v2';

    /** Google allows up to 128 `q` segments per request. */
    private const MAX_SEGMENTS_PER_CALL = 128;

    /** Keep request bodies well under Google's 30k-char limit. */
    private const MAX_CHARS_PER_CALL = 18000;

    private const BUDGET_CACHE_TTL = 60;

    /** Per-request memo so repeated lookups in one page render never hit the DB twice. */
    private static array $memo = [];

    // ── config ──────────────────────────────────────────────────────────────

    public static function enabled(): bool
    {
        return (bool) config('services.google.translate.enabled', true)
            && (string) config('services.google.translate.key', '') !== '';
    }

    private static function apiKey(): string
    {
        return (string) config('services.google.translate.key', '');
    }

    private static function maxCharsPerString(): int
    {
        return (int) config('services.google.translate.max_chars_per_string', 5000);
    }

    // ── normalization & hashing ─────────────────────────────────────────────

    /**
     * Collapse insignificant whitespace so cosmetically different copies of the
     * same sentence share one cache row (and one payment).
     */
    public static function normalize(string $text): string
    {
        $text = str_replace(["\xC2\xA0", "\r\n", "\r"], [' ', "\n", "\n"], $text);
        $text = preg_replace('/[ \t]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/\n{3,}/u', "\n\n", $text) ?? $text;

        return trim($text);
    }

    public static function hash(string $normalized): string
    {
        return sha1($normalized);
    }

    public static function normalizeLangCode(string $code): string
    {
        $code = trim($code);
        if ($code === '' || $code === '__original__') {
            return '';
        }
        if (strtolower($code) === 'auto') {
            return 'auto';
        }

        // Google expects zh-CN / zh-TW / pt-PT style casing; everything else is lowercase.
        $lower = strtolower($code);
        $keepRegion = [
            'zh-cn' => 'zh-CN', 'zh-tw' => 'zh-TW', 'pt-pt' => 'pt-PT',
            'fr-ca' => 'fr-CA', 'fa-af' => 'fa-AF',
        ];
        if (isset($keepRegion[$lower])) {
            return $keepRegion[$lower];
        }

        // Script-suffixed codes Google understands as-is (crh-Latn, mni-Mtei, …).
        if (preg_match('/^([a-z]{2,3})-([A-Z][a-z]{3})$/', $code)) {
            return $code;
        }

        if (str_contains($lower, '-')) {
            return explode('-', $lower, 2)[0];
        }

        return $lower;
    }

    public static function isEnglish(string $code): bool
    {
        $c = strtolower(trim($code));

        return $c === '' || $c === 'en' || str_starts_with($c, 'en-');
    }

    // ── translatability filter (pure cost saving) ───────────────────────────

    /**
     * Strings that carry no translatable language. Sending these to Google is
     * money burned for output identical to the input.
     */
    public static function isTranslatable(string $text): bool
    {
        $t = trim($text);

        if ($t === '' || mb_strlen($t) < 2) {
            return false;
        }
        if (mb_strlen($t) > self::maxCharsPerString()) {
            return false;
        }
        // No letters at all: numbers, punctuation, separators, "—", "•", "12/04/2025"
        if (!preg_match('/\p{L}/u', $t)) {
            return false;
        }
        // Money / measurements: "$1,200.00", "12 kg", "45%"
        if (preg_match('/^[\p{Sc}]?\s?[\d.,\s]+\s?[\p{L}%]{0,4}$/u', $t)) {
            return false;
        }
        if (filter_var($t, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        if (preg_match('#^(https?://|www\.|/)\S*$#i', $t)) {
            return false;
        }
        // Bare identifiers/slugs with no spaces: "user_name", "btn-primary", "ID-4471"
        if (!str_contains($t, ' ') && preg_match('/^[\w\-.:#]+$/u', $t) && preg_match('/[\d_\-]/', $t)) {
            return false;
        }

        return true;
    }

    // ── budget ──────────────────────────────────────────────────────────────

    public static function monthlyCharsUsed(): int
    {
        return (int) Cache::remember('translate:month_chars:' . Carbon::now()->format('Y-m'), self::BUDGET_CACHE_TTL, function () {
            return (int) DB::table('translation_usage')
                ->whereBetween('usage_date', [
                    Carbon::now()->startOfMonth()->toDateString(),
                    Carbon::now()->endOfMonth()->toDateString(),
                ])
                ->sum('billed_chars');
        });
    }

    public static function monthlyCharLimit(): int
    {
        return (int) config('services.google.translate.monthly_char_limit', 2000000);
    }

    public static function budgetRemaining(): int
    {
        $limit = self::monthlyCharLimit();
        // 0 (or negative) = unlimited — always translate cache misses.
        if ($limit <= 0) {
            return PHP_INT_MAX;
        }

        return max(0, $limit - self::monthlyCharsUsed());
    }

    public static function isBudgetUnlimited(): bool
    {
        return self::monthlyCharLimit() <= 0;
    }

    // ── main entry point ────────────────────────────────────────────────────

    /**
     * Translate a list of strings. Returns a map of ORIGINAL input string =>
     * translated string. Untranslatable / failed / over-budget items map back to
     * their original value, so callers can always render the result blindly.
     *
     * @param  array<int, string>  $texts
     * @return array<string, string>
     */
    public static function translateBatch(array $texts, string $target, string $source = 'en'): array
    {
        $target = self::normalizeLangCode($target);
        $source = self::normalizeLangCode($source);

        $out = [];
        foreach ($texts as $t) {
            $out[$t] = $t;
        }

        if ($target === '' || $target === $source || (self::isEnglish($target) && self::isEnglish($source))) {
            return $out;
        }
        if (!self::enabled()) {
            return $out;
        }

        // 1. Normalize + dedupe. hash => normalized text
        $byHash = [];
        // original input => hash (so we can map results back)
        $originalToHash = [];
        foreach ($texts as $raw) {
            $norm = self::normalize((string) $raw);
            if (!self::isTranslatable($norm)) {
                continue;
            }
            $h = self::hash($norm);
            $byHash[$h] = $norm;
            $originalToHash[$raw] = $h;
        }

        if ($byHash === []) {
            return $out;
        }

        $resolved = self::resolveHashes($byHash, $target, $source);

        // 2. Map back onto the caller's original strings, restoring surrounding
        //    whitespace that normalization stripped.
        foreach ($originalToHash as $raw => $h) {
            if (!isset($resolved[$h])) {
                continue;
            }
            $out[$raw] = self::reapplyPadding((string) $raw, $resolved[$h]);
        }

        return $out;
    }

    /**
     * @param  array<string, string>  $byHash  hash => normalized source text
     * @return array<string, string>  hash => translated text
     */
    private static function resolveHashes(array $byHash, string $target, string $source): array
    {
        $resolved = [];
        $missing = [];

        // In-process memo first (free)
        foreach ($byHash as $h => $norm) {
            $memoKey = $target . ':' . $h;
            if (isset(self::$memo[$memoKey])) {
                $resolved[$h] = self::$memo[$memoKey];
            } else {
                $missing[$h] = $norm;
            }
        }

        if ($missing === []) {
            return $resolved;
        }

        // Permanent DB cache (one indexed query for the whole batch)
        $hits = 0;
        foreach (array_chunk(array_keys($missing), 500) as $hashChunk) {
            $rows = DB::table('translation_cache')
                ->select('source_hash', 'translated_text')
                ->where('target_lang', $target)
                ->whereIn('source_hash', $hashChunk)
                ->get();

            foreach ($rows as $row) {
                $resolved[$row->source_hash] = $row->translated_text;
                self::$memo[$target . ':' . $row->source_hash] = $row->translated_text;
                unset($missing[$row->source_hash]);
                $hits++;
            }
        }

        if ($hits > 0) {
            self::recordUsage($target, 0, 0, $hits);
        }

        if ($missing === []) {
            return $resolved;
        }

        // Everything left has to be paid for.
        $fresh = self::callApiForMissing($missing, $target, $source);
        foreach ($fresh as $h => $translated) {
            $resolved[$h] = $translated;
        }

        return $resolved;
    }

    /**
     * @param  array<string, string>  $missing  hash => normalized source text
     * @return array<string, string>
     */
    private static function callApiForMissing(array $missing, string $target, string $source): array
    {
        $remaining = self::budgetRemaining();
        $resolved = [];

        foreach (self::chunkByChars($missing) as $chunk) {
            $chars = 0;
            foreach ($chunk as $text) {
                $chars += mb_strlen($text);
            }

            if (!self::isBudgetUnlimited() && $chars > $remaining) {
                Log::warning('GoogleTranslateService: monthly character budget exhausted', [
                    'target' => $target,
                    'limit' => self::monthlyCharLimit(),
                    'used' => self::monthlyCharsUsed(),
                ]);
                break;
            }

            $translated = self::requestChunk($chunk, $target, $source);
            if ($translated === null) {
                continue;
            }

            $remaining -= $chars;
            self::recordUsage($target, $chars, 1, 0);
            self::persist($translated, $chunk, $target);

            foreach ($translated as $h => $value) {
                $resolved[$h] = $value;
                self::$memo[$target . ':' . $h] = $value;
            }
        }

        return $resolved;
    }

    /**
     * @param  array<string, string>  $chunk  hash => source text
     * @return array<string, string>|null    hash => translated text
     */
    private static function requestChunk(array $chunk, string $target, string $source): ?array
    {
        $hashes = array_keys($chunk);
        $payload = [
            'q' => array_values($chunk),
            'target' => $target,
            'format' => 'text',
        ];
        // An explicit source skips language detection, which Google bills at the
        // same rate as translation. Only pass 'auto' when the source genuinely is
        // unknown (user-generated content) — it doubles the cost of that call.
        if ($source !== '' && $source !== 'auto') {
            $payload['source'] = $source;
        }

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                // JSON body, not form-encoded: `q` is a repeated field and
                // http_build_query would turn it into q[0]=… which the API rejects.
                $response = Http::asJson()
                    ->timeout(20)
                    ->post(self::ENDPOINT . '?key=' . urlencode(self::apiKey()), $payload);

                if ($response->status() === 429 || $response->status() >= 500) {
                    usleep(200000 * $attempt);
                    continue;
                }

                if (!$response->successful()) {
                    Log::warning('GoogleTranslateService: API error', [
                        'status' => $response->status(),
                        'body' => mb_substr($response->body(), 0, 500),
                        'target' => $target,
                    ]);

                    return null;
                }

                $translations = $response->json('data.translations');
                if (!is_array($translations) || count($translations) !== count($hashes)) {
                    Log::warning('GoogleTranslateService: unexpected payload shape', [
                        'expected' => count($hashes),
                        'got' => is_array($translations) ? count($translations) : 'null',
                    ]);

                    return null;
                }

                $out = [];
                foreach ($translations as $i => $item) {
                    $text = is_array($item) ? ($item['translatedText'] ?? null) : null;
                    if (is_string($text) && $text !== '') {
                        // format=text still returns a few HTML entities (&#39; &amp;)
                        $out[$hashes[$i]] = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    }
                }

                return $out;
            } catch (\Throwable $e) {
                Log::warning('GoogleTranslateService: request failed', [
                    'attempt' => $attempt,
                    'target' => $target,
                    'message' => $e->getMessage(),
                ]);
                usleep(200000 * $attempt);
            }
        }

        return null;
    }

    /**
     * @param  array<string, string>  $translated  hash => translated text
     * @param  array<string, string>  $sources     hash => source text
     */
    private static function persist(array $translated, array $sources, string $target): void
    {
        $now = Carbon::now();
        $rows = [];
        foreach ($translated as $hash => $value) {
            $source = $sources[$hash] ?? '';
            $rows[] = [
                'source_hash' => $hash,
                'target_lang' => $target,
                'source_text' => $source,
                'translated_text' => $value,
                'char_count' => mb_strlen($source),
                'hit_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows === []) {
            return;
        }

        try {
            foreach (array_chunk($rows, 200) as $batch) {
                DB::table('translation_cache')->upsert(
                    $batch,
                    ['source_hash', 'target_lang'],
                    ['translated_text', 'char_count', 'updated_at']
                );
            }
        } catch (\Throwable $e) {
            Log::warning('GoogleTranslateService: cache write failed', ['message' => $e->getMessage()]);
        }
    }

    /**
     * @param  array<string, string>  $missing
     * @return array<int, array<string, string>>
     */
    private static function chunkByChars(array $missing): array
    {
        $chunks = [];
        $current = [];
        $chars = 0;

        foreach ($missing as $hash => $text) {
            $len = mb_strlen($text);
            if ($current !== [] && ($chars + $len > self::MAX_CHARS_PER_CALL || count($current) >= self::MAX_SEGMENTS_PER_CALL)) {
                $chunks[] = $current;
                $current = [];
                $chars = 0;
            }
            $current[$hash] = $text;
            $chars += $len;
        }

        if ($current !== []) {
            $chunks[] = $current;
        }

        return $chunks;
    }

    private static function recordUsage(string $target, int $chars, int $requests, int $hits): void
    {
        if ($chars <= 0 && $requests <= 0 && $hits <= 0) {
            return;
        }

        try {
            $date = Carbon::now()->toDateString();

            // Seed an empty row, then increment. Doing it in one upsert would
            // double-count on insert (values written, then added to again).
            DB::table('translation_usage')->insertOrIgnore([[
                'usage_date' => $date,
                'target_lang' => $target,
                'billed_chars' => 0,
                'api_requests' => 0,
                'cache_hits' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]]);

            DB::table('translation_usage')
                ->where('usage_date', $date)
                ->where('target_lang', $target)
                ->update([
                    'billed_chars' => DB::raw('billed_chars + ' . (int) $chars),
                    'api_requests' => DB::raw('api_requests + ' . (int) $requests),
                    'cache_hits' => DB::raw('cache_hits + ' . (int) $hits),
                    'updated_at' => Carbon::now(),
                ]);

            if ($chars > 0) {
                Cache::forget('translate:month_chars:' . Carbon::now()->format('Y-m'));
            }
        } catch (\Throwable $e) {
            Log::warning('GoogleTranslateService: usage write failed', ['message' => $e->getMessage()]);
        }
    }

    /**
     * Normalization trimmed the caller's leading/trailing whitespace; put it back so
     * inline text nodes keep their spacing ("Hello " + name).
     */
    private static function reapplyPadding(string $original, string $translated): string
    {
        preg_match('/^\s*/u', $original, $lead);
        preg_match('/\s*$/u', $original, $tail);

        return ($lead[0] ?? '') . $translated . ($tail[0] ?? '');
    }

    /** Single-string convenience wrapper. */
    public static function translateOne(string $text, string $target, string $source = 'en'): string
    {
        $map = self::translateBatch([$text], $target, $source);

        return $map[$text] ?? $text;
    }
}
