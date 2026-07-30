<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\TranslateLanguage;
use App\Services\GoogleTranslateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Batch translation proxy for the client-side DOM engine (lr-translate.js).
 *
 * The Google API key lives only on this side of the wire. Character budgets are
 * optional (0 = unlimited); cost control is the permanent DB cache. A mild route
 * throttle + language allowlist remain as abuse guards.
 */
class TranslateController extends Controller
{
    /**
     * Google's 128-segment limit applies to *Google*, not to this endpoint.
     * Cache hits never leave the server, so a page with thousands of strings should
     * arrive in one or two requests; GoogleTranslateService then splits only the
     * cache misses into Google-legal chunks and runs them concurrently.
     * This is what keeps heavy pages from generating a 429 storm against us.
     */
    private const MAX_ITEMS_PER_REQUEST = 2000;

    /** Guard against a single absurd payload. */
    private const MAX_CHARS_PER_REQUEST = 400000;

    public function batch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'target' => ['required', 'string', 'max:12'],
            'items' => ['required', 'array', 'min:1', 'max:' . self::MAX_ITEMS_PER_REQUEST],
            'items.*' => ['nullable', 'string'],
        ]);

        $target = GoogleTranslateService::normalizeLangCode((string) $validated['target']);
        $items = array_values($validated['items']);

        // English is a real target, not a no-op: a page can hold posts written in
        // Hindi, Spanish and English at once, and "English" must convert them all.
        if ($target === '') {
            return $this->passthrough($items, $target, 'source_language');
        }

        // Offered by us AND actually translatable by Google — our language table
        // lists dozens of codes the API rejects, which used to 400 the whole batch.
        if (!self::isAllowedLanguage($target) || !GoogleTranslateService::isSupportedLanguage($target)) {
            return $this->passthrough($items, $target, 'language_not_allowed');
        }

        if (!GoogleTranslateService::enabled()) {
            return $this->passthrough($items, $target, 'disabled');
        }

        $chars = 0;
        foreach ($items as $item) {
            $chars += mb_strlen((string) $item);
        }
        if ($chars > self::MAX_CHARS_PER_REQUEST) {
            return $this->passthrough($items, $target, 'payload_too_large');
        }

        // Public endpoint: meter each visitor on chargeable characters. Cache hits
        // cost nothing and are not counted, so real browsing never trips this — but
        // a client feeding us novel text cannot run up an unbounded Google bill.
        GoogleTranslateService::resetRequestStats();
        $startedAt = microtime(true);

        $quotaKey = $this->visitorQuotaKey($request);
        $quota = (int) config('services.google.translate.visitor_daily_billed_chars', 150000);
        // Over quota still serves everything already paid for — it just stops buying
        // anything new. Blanking the site for the rest of the day would be worse.
        $cacheOnly = $quota > 0 && (int) Cache::get($quotaKey, 0) >= $quota;

        $map = GoogleTranslateService::translateBatch(
            array_map(static fn ($v) => (string) $v, $items),
            $target,
            GoogleTranslateService::SOURCE_DETECT,
            $cacheOnly
        );

        $billed = GoogleTranslateService::billedCharsThisRequest();
        if ($quota > 0 && $billed > 0) {
            Cache::put($quotaKey, (int) Cache::get($quotaKey, 0) + $billed, now()->endOfDay());
        }

        // null means "not translated". Echoing the source text back instead would
        // make the browser cache "English → English" after any transient failure,
        // and that string would never be translated again for that visitor.
        $out = [];
        $missing = 0;
        foreach ($items as $item) {
            $key = (string) $item;
            if (array_key_exists($key, $map)) {
                $out[] = $map[$key];
            } else {
                $out[] = null;
                $missing++;
            }
        }

        $this->logSummary($request, $target, count($items), $missing, $chars, $cacheOnly, $startedAt);

        return response()->json([
            'ok' => true,
            'target' => $target,
            'translations' => $out,
            'unresolved' => $missing,
            'degraded' => $cacheOnly ? 'visitor_quota_exceeded' : null,
        ]);
    }

    /**
     * Languages the site actually offers. Prevents paying to translate into all
     * 240 supported languages when a country exposes five.
     */
    private static function isAllowedLanguage(string $target): bool
    {
        $allowed = Cache::remember('translate:allowed_langs', 3600, function () {
            return TranslateLanguage::pluck('code')
                ->map(fn ($c) => GoogleTranslateService::normalizeLangCode((string) $c))
                ->filter()
                ->unique()
                ->values()
                ->all();
        });

        return in_array($target, $allowed, true);
    }

    /**
     * One line per translated page batch: which languages, what Google answered,
     * how much was bought, how much was reused, and what it cost.
     *
     * Example:
     *   translate.page /user/profile en>hi,es>hi status=200x2 items=412 sent=8,231
     *   reused=390 identity=12 unresolved=0 cost=$0.000165 in 843ms
     */
    private function logSummary(
        Request $request,
        string $target,
        int $items,
        int $unresolved,
        int $submittedChars,
        bool $cacheOnly,
        float $startedAt
    ): void {
        if (!config('services.google.translate.log_summary', true)) {
            return;
        }

        $s = GoogleTranslateService::requestStats();

        $pairs = [];
        foreach ($s['pairs'] as $pair => $count) {
            $pairs[] = $pair . ':' . $count;
        }
        $statuses = [];
        foreach ($s['statuses'] as $code => $count) {
            $statuses[] = $code . 'x' . $count;
        }

        $referer = (string) $request->headers->get('referer', '');
        $page = $referer !== '' ? (parse_url($referer, PHP_URL_PATH) ?: '/') : '-';

        Log::info('translate.page', [
            'page' => $page,
            'surface' => $request->headers->get('x-lr-surface', '-'),
            'target' => $target,
            'pairs' => $pairs === [] ? ['(all cached)'] : $pairs,
            'api_status' => $statuses === [] ? ['(no api call)'] : $statuses,
            'items' => $items,
            'submitted_chars' => $submittedChars,
            'chars_translated' => $s['translate_chars'],
            'chars_detected' => $s['detect_chars'],
            'chars_billed' => $s['billed_chars'],
            'cache_reused' => $s['cache_hits'],
            'already_in_target' => $s['identity'],
            'unresolved' => $unresolved,
            'api_calls' => $s['translate_calls'] + $s['detect_calls'],
            'cost_usd' => '$' . number_format($s['cost_usd'], 6),
            'cache_only' => $cacheOnly,
            'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
        ]);
    }

    /** Binds a browser session, falling back to IP for cookie-less clients. */
    private function visitorQuotaKey(Request $request): string
    {
        $id = $request->hasSession() ? $request->session()->getId() : '';

        return 'translate:spend:' . sha1($id . '|' . $request->ip()) . ':' . now()->toDateString();
    }

    /**
     * @param  array<int, mixed>  $items
     */
    private function passthrough(array $items, string $target, string $reason): JsonResponse
    {
        return response()->json([
            'ok' => false,
            'reason' => $reason,
            'target' => $target,
            'translations' => array_map(static fn ($v) => (string) $v, $items),
        ]);
    }
}
