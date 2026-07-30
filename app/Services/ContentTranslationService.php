<?php

namespace App\Services;

/**
 * Server-side translation of user-generated content.
 *
 * The public API is unchanged from the old free-endpoint implementation so existing
 * callers (BulletinBoardController) keep working; internally everything now goes
 * through {@see GoogleTranslateService}, which owns the permanent cache, the
 * character budget and the usage accounting.
 */
class ContentTranslationService
{
    /**
     * Resolve the UGC target language.
     *
     * Returns null for Original / first visit so content stays in the author's
     * language. `content_lang` is authoritative; the second argument is only a
     * legacy fallback for visitors who still carry a stale `googtrans` cookie from
     * the old Google Translate widget.
     */
    public static function resolveTargetLanguage(?string $legacyGoogtransCookie, ?string $contentLangCookie = null): ?string
    {
        if ($contentLangCookie !== null) {
            $contentRaw = strtolower(trim(urldecode($contentLangCookie)));
            if ($contentRaw === '' || $contentRaw === '__original__') {
                return null;
            }

            $normalized = self::normalizeLangCode($contentLangCookie);
            if ($normalized !== '') {
                return $normalized;
            }
        }

        $gt = is_string($legacyGoogtransCookie) ? trim(urldecode($legacyGoogtransCookie)) : '';
        if ($gt !== '' && $gt !== '/en/en' && preg_match('#/auto/([^;/]+)#', $gt, $m)) {
            $fromAuto = self::normalizeLangCode($m[1]);
            if ($fromAuto !== '') {
                return $fromAuto;
            }
        }

        return null;
    }

    public static function normalizeLangCode(string $code): string
    {
        return GoogleTranslateService::normalizeLangCode($code);
    }

    /**
     * @param  string  $sourceLang  'auto' detects (costs double — only for UGC of
     *                              unknown origin); 'en' for site-authored copy.
     */
    public static function translate(?string $text, string $targetLang, string $sourceLang = 'auto'): string
    {
        $map = self::translateMany([$text ?? ''], $targetLang, $sourceLang);

        return $map[0] ?? ($text ?? '');
    }

    /**
     * Translate many strings at once (cache-aware, deduplicated).
     * Preserves input keys in the returned array.
     *
     * @param  array<int|string, string|null>  $texts
     * @return array<int|string, string>
     */
    public static function translateMany(array $texts, string $targetLang, string $sourceLang = 'auto'): array
    {
        $results = [];
        $payload = [];

        foreach ($texts as $key => $text) {
            if ($text === null) {
                $results[$key] = '';
                continue;
            }
            if (trim($text) === '') {
                $results[$key] = $text;
                continue;
            }
            $results[$key] = $text;
            $payload[] = $text;
        }

        if ($payload === []) {
            return $results;
        }

        $translated = GoogleTranslateService::translateBatch($payload, $targetLang, $sourceLang);

        foreach ($results as $key => $original) {
            if (isset($translated[$original])) {
                $results[$key] = $translated[$original];
            }
        }

        return $results;
    }

    public static function translateBulletinFields(object $bulletin, string $targetLang, string $sourceLang = 'auto'): void
    {
        $translated = self::translateMany([
            'title' => (string) ($bulletin->title ?? ''),
            'description' => (string) ($bulletin->description ?? ''),
        ], $targetLang, $sourceLang);

        if (isset($bulletin->title)) {
            $bulletin->title = $translated['title'] ?? $bulletin->title;
        }
        if (isset($bulletin->description)) {
            $bulletin->description = $translated['description'] ?? $bulletin->description;
        }
    }
}
