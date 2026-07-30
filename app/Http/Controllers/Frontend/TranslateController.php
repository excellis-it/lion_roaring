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
 * The Google API key lives only on this side of the wire. Character budgets are
 * optional (0 = unlimited); cost control is the permanent DB cache. A mild route
 * throttle + language allowlist remain as abuse guards.
 */
class TranslateController extends Controller
{
    private const MAX_ITEMS_PER_REQUEST = 128;

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
