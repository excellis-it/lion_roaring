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

    private const DETECT_ENDPOINT = 'https://translation.googleapis.com/language/translate/v2/detect';

    /**
     * Source sentinel meaning "the text may be in any language — work it out".
     * Used for pages that mix user-generated content written in several languages.
     */
    public const SOURCE_DETECT = 'detect';

    /** Google v2 hard limit: 128 `q` segments per request. */
    private const MAX_SEGMENTS_PER_CALL = 128;

    /** Google v2 hard limit is 30,000 code points per request; stay just under. */
    private const MAX_CHARS_PER_CALL = 28000;

    /**
     * Google-legal chunks fired concurrently. The per-project quota is measured in
     * millions of characters per minute, so concurrency — not serialization — is
     * the right lever for a page with thousands of strings.
     */
    private const MAX_CONCURRENT_CALLS = 8;

    /** Retries for a chunk that came back 429 / 5xx. */
    private const MAX_CHUNK_RETRIES = 4;

    private const BUDGET_CACHE_TTL = 60;

    /** Per-request memo so repeated lookups in one page render never hit the DB twice. */
    private static array $memo = [];

    /** Characters actually sent to Google during this PHP request (cache hits are free). */
    private static int $billedThisRequest = 0;

    public const USD_PER_MILLION_CHARS = 20;

    /** Everything one HTTP request cost us, for the per-page summary log. */
    private static array $stats = [
        'translate_chars' => 0,
        'detect_chars' => 0,
        'translate_calls' => 0,
        'detect_calls' => 0,
        'cache_hits' => 0,
        'identity' => 0,
        'pairs' => [],
        'statuses' => [],
    ];

    /**
     * Chargeable characters produced so far in this request. Lets the public
     * endpoint meter a visitor on what they actually cost, not on what they submit.
     */
    public static function billedCharsThisRequest(): int
    {
        return self::$billedThisRequest;
    }

    public static function resetRequestStats(): void
    {
        self::$billedThisRequest = 0;
        self::$stats = [
            'translate_chars' => 0,
            'detect_chars' => 0,
            'translate_calls' => 0,
            'detect_calls' => 0,
            'cache_hits' => 0,
            'identity' => 0,
            'pairs' => [],
            'statuses' => [],
        ];
    }

    /** @return array<string, mixed> */
    public static function requestStats(): array
    {
        $stats = self::$stats;
        $billed = $stats['translate_chars'] + $stats['detect_chars'];
        $stats['billed_chars'] = $billed;
        $stats['cost_usd'] = round($billed / 1000000 * self::USD_PER_MILLION_CHARS, 6);

        return $stats;
    }

    private static function noteStatus(int $status): void
    {
        self::$stats['statuses'][$status] = (self::$stats['statuses'][$status] ?? 0) + 1;
    }

    private static function notePair(string $source, string $target, int $count): void
    {
        $key = ($source === '' ? 'auto' : $source) . '>' . $target;
        self::$stats['pairs'][$key] = (self::$stats['pairs'][$key] ?? 0) + $count;
    }

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
        $lowerCode = strtolower($code);
        if ($lowerCode === 'auto') {
            return 'auto';
        }
        if ($lowerCode === self::SOURCE_DETECT) {
            return self::SOURCE_DETECT;
        }

        // Google expects zh-CN / zh-TW / pt-PT style casing; everything else is lowercase.
        $lower = $lowerCode;
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

    // ── what Google will actually accept ────────────────────────────────────

    /**
     * Languages the API can translate, straight from Google.
     *
     * This list is NOT the same as the list Google can *detect*: detection happily
     * returns romanised variants such as `uk-Latn`, which the translate endpoint
     * rejects with "Bad language pair". It is also smaller than our own
     * `translate_languages` table, which offers dozens of codes Google cannot do.
     * Either mismatch 400s an entire chunk, so both directions are validated here.
     *
     * @return array<int, string> lowercase codes; empty when unknown (fail open)
     */
    public static function supportedLanguages(): array
    {
        return Cache::remember('translate:google_supported_langs', 60 * 60 * 24 * 7, function () {
            if (!self::enabled()) {
                return [];
            }
            try {
                $response = Http::timeout(20)->get(self::ENDPOINT . '/languages', ['key' => self::apiKey()]);
                if (!$response->successful()) {
                    return [];
                }
                $codes = [];
                foreach ((array) $response->json('data.languages') as $row) {
                    $code = is_array($row) ? ($row['language'] ?? null) : null;
                    if (is_string($code) && $code !== '') {
                        $codes[] = strtolower($code);
                    }
                }

                return $codes;
            } catch (\Throwable $e) {
                Log::warning('GoogleTranslateService: could not fetch supported languages', ['message' => $e->getMessage()]);

                return [];
            }
        });
    }

    /** Unknown list = fail open, so a fetch failure never blocks translation. */
    public static function isSupportedLanguage(string $code): bool
    {
        $supported = self::supportedLanguages();
        if ($supported === []) {
            return true;
        }

        return in_array(strtolower(trim($code)), $supported, true);
    }

    /**
     * Coerce a detected language into something usable as a translate `source`.
     * Falls back to the base subtag (`uk-Latn` → `uk`) and finally to '' meaning
     * "let Google auto-detect", which always works and only costs a little more.
     */
    public static function usableSourceLang(string $code): string
    {
        $code = self::normalizeLangCode($code);
        if ($code === '' || $code === 'auto' || $code === self::SOURCE_DETECT) {
            return '';
        }
        if (self::isSupportedLanguage($code)) {
            return $code;
        }

        $base = strtolower(explode('-', $code, 2)[0]);
        if ($base !== '' && $base !== strtolower($code) && self::isSupportedLanguage($base)) {
            return $base;
        }

        return '';
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
        // Bare identifiers with no spaces: "user_name", "ID-4471", "col_2".
        // A hyphen alone must NOT qualify — "E-Learning", "E-Store", "Sign-in" and
        // "non-profit" are ordinary words and were silently never translated.
        if (!str_contains($t, ' ') && preg_match('/^[\w\-.:#]+$/u', $t) && preg_match('/[\d_]/', $t)) {
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
     * Only strings that were genuinely resolved appear in the result. A caller that
     * wants a total mapping should fall back to its own input — silently returning
     * the source text as if it were a translation would let clients cache "English
     * translates to English" after a transient Google failure.
     *
     * @param  array<int, string>  $texts
     * @param  string  $source  a fixed language code, or self::SOURCE_DETECT when the
     *                          page mixes languages (each unique string is detected
     *                          once and the answer is cached forever).
     * @param  bool  $cacheOnly  never call Google; serve whatever is already paid for.
     * @return array<string, string>
     */
    public static function translateBatch(array $texts, string $target, string $source = self::SOURCE_DETECT, bool $cacheOnly = false): array
    {
        $target = self::normalizeLangCode($target);
        $source = self::normalizeLangCode($source);

        $out = [];

        if ($target === '') {
            return $out;
        }
        // With a fixed source, target == source is a no-op. With SOURCE_DETECT we
        // cannot know yet — a page may hold English, Hindi and Spanish at once, so
        // "translate everything to English" still has real work to do.
        if ($source !== self::SOURCE_DETECT && $target === $source) {
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

        $resolved = self::resolveHashes($byHash, $target, $source, $cacheOnly);

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
    private static function resolveHashes(array $byHash, string $target, string $source, bool $cacheOnly = false): array
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
            self::$stats['cache_hits'] += $hits;
            self::recordUsage($target, 0, 0, $hits);
        }

        if ($missing === []) {
            return $resolved;
        }

        // Over budget / over quota: hand back everything already paid for and stop.
        // Refusing cached strings too would blank out a site that has been correctly
        // translated for years just because one visitor was noisy today.
        if ($cacheOnly) {
            return $resolved;
        }

        // Everything left has to be paid for.
        if ($source !== self::SOURCE_DETECT) {
            foreach (self::callApiForMissing($missing, $target, $source) as $h => $translated) {
                $resolved[$h] = $translated;
            }

            return $resolved;
        }

        // Mixed-language page: split the misses by their real source language, then
        // translate each group with an explicit source. Strings already in the target
        // language are recorded as identity so they are never processed again.
        foreach (self::groupBySourceLanguage($missing) as $detected => $group) {
            // Detection can return codes translation rejects (uk-Latn). Coerce to
            // something usable, or fall back to auto-detect for just this group.
            $source = self::usableSourceLang((string) $detected);

            // Already in the requested language — record identity so this string is
            // never detected or translated again, and hand back the author's words
            // untouched rather than round-tripping them through Google.
            if ($source !== '' && self::sameLanguage($source, $target)) {
                self::$stats['identity'] += count($group);
                self::notePair($source, $target, count($group));
                self::persistIdentity($group, $target);
                foreach ($group as $h => $text) {
                    $resolved[$h] = $text;
                    self::$memo[$target . ':' . $h] = $text;
                }
                continue;
            }

            foreach (self::callApiForMissing($group, $target, $source ?: 'auto') as $h => $translated) {
                $resolved[$h] = $translated;
            }
        }

        return $resolved;
    }

    /** Google returns zh-CN/zh-TW etc.; compare on the base subtag. */
    private static function sameLanguage(string $a, string $b): bool
    {
        $base = static fn (string $c) => strtolower(explode('-', $c, 2)[0]);

        return $base($a) === $base($b);
    }

    /**
     * Text already in the target language still gets a cache row, so the next
     * visitor resolves it from the DB instead of paying to detect it again.
     *
     * @param  array<string, string>  $group  hash => text
     */
    private static function persistIdentity(array $group, string $target): void
    {
        self::persist($group, $group, $target);
    }

    /**
     * @param  array<string, string>  $missing  hash => normalized text
     * @return array<string, array<string, string>>  detected lang => (hash => text)
     */
    private static function groupBySourceLanguage(array $missing): array
    {
        $langs = self::detectLanguages($missing);

        $groups = [];
        foreach ($missing as $h => $text) {
            $lang = $langs[$h] ?? 'en';
            $groups[$lang][$h] = $text;
        }

        return $groups;
    }

    /**
     * Detected language per hash, cached permanently in `translation_source`.
     *
     * @param  array<string, string>  $byHash  hash => normalized text
     * @return array<string, string>           hash => language code
     */
    public static function detectLanguages(array $byHash): array
    {
        $out = [];
        $unknown = $byHash;

        foreach (array_chunk(array_keys($byHash), 500) as $hashChunk) {
            $rows = DB::table('translation_source')
                ->select('source_hash', 'detected_lang')
                ->whereIn('source_hash', $hashChunk)
                ->get();

            foreach ($rows as $row) {
                $out[$row->source_hash] = (string) $row->detected_lang;
                unset($unknown[$row->source_hash]);
            }
        }

        if ($unknown === []) {
            return $out;
        }

        // Only genuinely new answers are written back. Re-upserting rows we just
        // read would mean thousands of pointless writes every time a new target
        // language is warmed against an already-detected corpus.
        $fresh = [];

        // Script is decisive and free: Devanagari is never English. Resolving these
        // locally keeps the paid detect call for genuinely ambiguous Latin text.
        foreach ($unknown as $h => $text) {
            $script = self::scriptLanguage($text);
            if ($script !== null) {
                $out[$h] = $script;
                $fresh[$h] = $script;
                unset($unknown[$h]);
            }
        }

        if ($unknown !== []) {
            foreach (self::callDetectApi($unknown) as $h => $lang) {
                $out[$h] = $lang;
                $fresh[$h] = $lang;
            }
        }

        if ($fresh !== []) {
            self::persistDetected($fresh, $byHash);
        }

        // Strings detection could not resolve fall back to English for THIS request
        // only. Caching the guess would permanently mislabel a Hindi post as English
        // because of one timeout, and every later translation would use a wrong source.
        foreach (array_keys($byHash) as $h) {
            if (!isset($out[$h])) {
                $out[$h] = 'en';
            }
        }

        return $out;
    }

    /**
     * Unambiguous writing systems mapped to their dominant language. Only used when
     * the string is overwhelmingly in one script, so Latin text never matches.
     */
    private static function scriptLanguage(string $text): ?string
    {
        static $scripts = [
            'Devanagari' => 'hi',
            'Bengali' => 'bn',
            'Gurmukhi' => 'pa',
            'Gujarati' => 'gu',
            'Tamil' => 'ta',
            'Telugu' => 'te',
            'Kannada' => 'kn',
            'Malayalam' => 'ml',
            'Sinhala' => 'si',
            'Thai' => 'th',
            'Lao' => 'lo',
            'Khmer' => 'km',
            'Myanmar' => 'my',
            'Georgian' => 'ka',
            'Armenian' => 'hy',
            'Hebrew' => 'iw',
            'Hangul' => 'ko',
            'Hiragana' => 'ja',
            'Katakana' => 'ja',
        ];

        $letters = preg_match_all('/\p{L}/u', $text);
        if ($letters < 3) {
            return null;
        }

        foreach ($scripts as $script => $lang) {
            $count = preg_match_all('/\p{' . $script . '}/u', $text);
            if ($count !== false && $count >= $letters * 0.6) {
                return $lang;
            }
        }

        return null;
    }

    /**
     * @param  array<string, string>  $unknown  hash => text
     * @return array<string, string>            hash => language code
     */
    private static function callDetectApi(array $unknown): array
    {
        $out = [];
        $chunks = self::chunkByChars($unknown);

        $pendingIdx = array_keys($chunks);
        for ($attempt = 0; $attempt <= self::MAX_CHUNK_RETRIES && $pendingIdx !== []; $attempt++) {
            if ($attempt > 0) {
                usleep(min(8000000, (int) (250000 * (2 ** ($attempt - 1)))) + random_int(0, 200000));
            }

            try {
                $responses = Http::pool(function ($pool) use ($chunks, $pendingIdx) {
                    foreach ($pendingIdx as $i) {
                        $pool->as((string) $i)
                            ->asJson()
                            ->timeout(30)
                            ->post(self::DETECT_ENDPOINT . '?key=' . urlencode(self::apiKey()), [
                                'q' => array_values($chunks[$i]),
                            ]);
                    }
                }, self::MAX_CONCURRENT_CALLS);
            } catch (\Throwable $e) {
                Log::warning('GoogleTranslateService: detect pool failed', ['message' => $e->getMessage()]);
                break;
            }

            $retry = [];
            $billed = 0;
            $calls = 0;
            foreach ($pendingIdx as $i) {
                $response = $responses[(string) $i] ?? null;

                if ($response instanceof \Throwable || !$response instanceof \Illuminate\Http\Client\Response) {
                    $retry[] = $i;
                    continue;
                }
                self::noteStatus($response->status());
                if ($response->status() === 429 || $response->status() >= 500) {
                    $retry[] = $i;
                    continue;
                }
                if (!$response->successful()) {
                    Log::warning('GoogleTranslateService: detect error', [
                        'status' => $response->status(),
                        'body' => mb_substr($response->body(), 0, 300),
                    ]);
                    continue;
                }

                $detections = $response->json('data.detections');
                $hashes = array_keys($chunks[$i]);
                if (!is_array($detections)) {
                    continue;
                }

                foreach ($detections as $k => $candidates) {
                    if (!isset($hashes[$k])) {
                        continue;
                    }
                    $best = is_array($candidates) ? ($candidates[0] ?? null) : null;
                    $lang = is_array($best) ? ($best['language'] ?? null) : null;
                    if (is_string($lang) && $lang !== '') {
                        // Store only codes translation can actually consume, so a
                        // detection-only variant never gets cached and replayed.
                        $out[$hashes[$k]] = self::usableSourceLang($lang) ?: 'en';
                    }
                }

                $billed += self::chunkChars($chunks[$i]);
                $calls++;
            }

            // Detection is billed at the translation rate — count it honestly.
            if ($calls > 0) {
                self::$stats['detect_chars'] += $billed;
                self::$stats['detect_calls'] += $calls;
                self::recordUsage('detect', $billed, $calls, 0);
            }

            $pendingIdx = $retry;
        }

        // Deliberately returns only what was actually detected. Unresolved hashes are
        // handled by the caller as a per-request fallback and are never cached.
        return $out;
    }

    /**
     * @param  array<string, string>  $langs   hash => language
     * @param  array<string, string>  $byHash  hash => text
     */
    private static function persistDetected(array $langs, array $byHash): void
    {
        $now = Carbon::now();
        $rows = [];
        foreach ($langs as $hash => $lang) {
            if (!isset($byHash[$hash])) {
                continue;
            }
            $rows[] = [
                'source_hash' => $hash,
                'detected_lang' => $lang,
                'confidence' => null,
                'char_count' => mb_strlen($byHash[$hash]),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows === []) {
            return;
        }

        try {
            foreach (array_chunk($rows, 200) as $batch) {
                DB::table('translation_source')->upsert($batch, ['source_hash'], ['detected_lang', 'updated_at']);
            }
        } catch (\Throwable $e) {
            Log::warning('GoogleTranslateService: detect cache write failed', ['message' => $e->getMessage()]);
        }
    }

    /**
     * @param  array<string, string>  $missing  hash => normalized source text
     * @return array<string, string>
     */
    private static function callApiForMissing(array $missing, string $target, string $source): array
    {
        $chunks = self::chunkByChars($missing);
        $unlimited = self::isBudgetUnlimited();
        $remaining = self::budgetRemaining();

        // Gate on budget first so we never start a wave we cannot finish paying for.
        $allowed = [];
        foreach ($chunks as $chunk) {
            $chars = self::chunkChars($chunk);
            if (!$unlimited && $chars > $remaining) {
                Log::warning('GoogleTranslateService: monthly character budget exhausted', [
                    'target' => $target,
                    'limit' => self::monthlyCharLimit(),
                    'used' => self::monthlyCharsUsed(),
                ]);
                break;
            }
            $remaining -= $chars;
            $allowed[] = $chunk;
        }

        $resolved = [];
        if ($allowed === []) {
            return $resolved;
        }

        // All Google-legal chunks are submitted at once with bounded concurrency,
        // so a 3,000-string page becomes a few parallel calls instead of dozens
        // of sequential ones.
        $results = self::requestChunksConcurrently($allowed, $target, $source);

        $billedChars = 0;
        $calls = 0;
        foreach ($results as $i => $translated) {
            if ($translated === null) {
                continue;
            }
            $chunk = $allowed[$i];
            $billedChars += self::chunkChars($chunk);
            $calls++;
            self::persist($translated, $chunk, $target);

            foreach ($translated as $h => $value) {
                $resolved[$h] = $value;
                self::$memo[$target . ':' . $h] = $value;
            }
        }

        if ($calls > 0) {
            self::$stats['translate_chars'] += $billedChars;
            self::$stats['translate_calls'] += $calls;
            self::notePair($source, $target, count($resolved));
            self::recordUsage($target, $billedChars, $calls, 0);
        }

        return $resolved;
    }

    /** @param array<string, string> $chunk */
    private static function chunkChars(array $chunk): int
    {
        $chars = 0;
        foreach ($chunk as $text) {
            $chars += mb_strlen($text);
        }

        return $chars;
    }

    /**
     * Send every Google-legal chunk with bounded concurrency, retrying only the
     * chunks that came back 429 / 5xx using exponential backoff with jitter —
     * Google's documented guidance for RESOURCE_EXHAUSTED.
     *
     * @param  array<int, array<string, string>>  $wave  chunk index => (hash => text)
     * @return array<int, array<string, string>|null>    chunk index => (hash => translated)
     */
    private static function requestChunksConcurrently(array $wave, string $target, string $source): array
    {
        $results = [];
        $pending = array_keys($wave);

        for ($attempt = 0; $attempt <= self::MAX_CHUNK_RETRIES && $pending !== []; $attempt++) {
            if ($attempt > 0) {
                usleep(min(8000000, (int) (250000 * (2 ** ($attempt - 1)))) + random_int(0, 200000));
            }

            $payloads = [];
            foreach ($pending as $i) {
                $payloads[$i] = self::buildPayload($wave[$i], $target, $source);
            }

            try {
                $responses = Http::pool(function ($pool) use ($payloads) {
                    foreach ($payloads as $i => $payload) {
                        $pool->as((string) $i)
                            ->asJson()
                            ->timeout(30)
                            ->post(self::ENDPOINT . '?key=' . urlencode(self::apiKey()), $payload);
                    }
                }, self::MAX_CONCURRENT_CALLS);
            } catch (\Throwable $e) {
                Log::warning('GoogleTranslateService: pool failed', ['message' => $e->getMessage()]);
                break;
            }

            $retry = [];
            foreach ($pending as $i) {
                $response = $responses[(string) $i] ?? null;

                if ($response instanceof \Throwable) {
                    $retry[] = $i;
                    continue;
                }
                if (!$response instanceof \Illuminate\Http\Client\Response) {
                    $retry[] = $i;
                    continue;
                }
                self::noteStatus($response->status());
                if ($response->status() === 429 || $response->status() >= 500) {
                    $retry[] = $i;
                    continue;
                }
                if (!$response->successful()) {
                    $body = $response->body();

                    // "Bad language pair" means the source we supplied is not a valid
                    // translate source. Rather than lose up to 128 strings, redo this
                    // one chunk letting Google detect the source itself.
                    if ($source !== '' && $source !== 'auto' && str_contains($body, 'Bad language pair')) {
                        Log::warning('GoogleTranslateService: unusable source, retrying with auto-detect', [
                            'source' => $source,
                            'target' => $target,
                        ]);
                        $retryAuto = self::requestChunksConcurrently([$wave[$i]], $target, 'auto');
                        $results[$i] = $retryAuto[0] ?? null;
                        continue;
                    }

                    Log::warning('GoogleTranslateService: API error', [
                        'status' => $response->status(),
                        'body' => mb_substr($body, 0, 500),
                        'target' => $target,
                        'source' => $source,
                    ]);
                    $results[$i] = null;
                    continue;
                }

                $results[$i] = self::parseTranslations($response->json('data.translations'), array_keys($wave[$i]));
            }

            $pending = $retry;
        }

        foreach ($pending as $i) {
            Log::warning('GoogleTranslateService: chunk exhausted retries', ['target' => $target]);
            $results[$i] = null;
        }

        return $results;
    }

    /**
     * @param  array<string, string>  $chunk
     * @return array<string, mixed>
     */
    private static function buildPayload(array $chunk, string $target, string $source): array
    {
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

        return $payload;
    }

    /**
     * @param  array<int, string>  $hashes
     * @return array<string, string>|null
     */
    private static function parseTranslations($translations, array $hashes): ?array
    {
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

        self::$billedThisRequest += max(0, $chars);

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
