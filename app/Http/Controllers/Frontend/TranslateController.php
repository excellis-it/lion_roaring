<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\TranslateLanguage;
use App\Services\GoogleTranslateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Batch translation proxy for the client-side DOM engine (lr-translate.js).
 *
 * The Google API key lives only on this side of the wire. Every request is
 * budget-checked and rate-limited, because a public translate endpoint is
 * otherwise a free translation service for the entire internet.
 */
class TranslateController extends Controller
{
    private const MAX_ITEMS_PER_REQUEST = 120;

    public function batch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'target' => ['required', 'string', 'max:12'],
            'items' => ['required', 'array', 'min:1', 'max:' . self::MAX_ITEMS_PER_REQUEST],
            'items.*' => ['nullable', 'string'],
        ]);

        $target = GoogleTranslateService::normalizeLangCode((string) $validated['target']);
        $items = array_values($validated['items']);

        if ($target === '' || GoogleTranslateService::isEnglish($target)) {
            return $this->passthrough($items, $target, 'source_language');
        }

        if (!self::isAllowedLanguage($target)) {
            return $this->passthrough($items, $target, 'language_not_allowed');
        }

        if (!GoogleTranslateService::enabled()) {
            return $this->passthrough($items, $target, 'disabled');
        }

        $chars = 0;
        foreach ($items as $item) {
            $chars += mb_strlen((string) $item);
        }

        if (!$this->withinVisitorBudget($request, $chars)) {
            return $this->passthrough($items, $target, 'visitor_quota_exceeded');
        }

        if (GoogleTranslateService::budgetRemaining() <= 0) {
            // Cache-only mode: already-paid-for strings still translate, nothing new is bought.
            return $this->passthrough($items, $target, 'budget_exhausted');
        }

        $map = GoogleTranslateService::translateBatch(
            array_map(static fn ($v) => (string) $v, $items),
            $target,
            'en'
        );

        $out = [];
        foreach ($items as $item) {
            $key = (string) $item;
            $out[] = $map[$key] ?? $key;
        }

        return response()->json([
            'ok' => true,
            'target' => $target,
            'translations' => $out,
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
     * Per-visitor daily character cap. Keyed on session id so it survives IP
     * changes on mobile but still binds a single browser.
     */
    private function withinVisitorBudget(Request $request, int $chars): bool
    {
        $limit = (int) config('services.google.translate.session_daily_char_limit', 200000);
        if ($limit <= 0) {
            return true;
        }

        $key = 'translate:visitor:' . sha1($request->session()->getId() . '|' . $request->ip()) . ':' . now()->toDateString();
        $used = (int) Cache::get($key, 0);

        if ($used + $chars > $limit) {
            return false;
        }

        Cache::put($key, $used + $chars, now()->endOfDay());

        return true;
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
