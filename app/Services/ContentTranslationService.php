<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response as HttpResponse;

class ContentTranslationService
{
    private const ENDPOINT = 'https://translate.googleapis.com/translate_a/single';
    private const CACHE_TTL_SECONDS = 60 * 60 * 24 * 30;
    private const MAX_CHUNK = 3800;
    private const POOL_CHUNK = 15;

    /**
     * Resolve UGC target language.
     * Returns null when the visitor has not explicitly chosen a language
     * (first load / Original) so bulletins stay in the author's language.
     */
    public static function resolveTargetLanguage(?string $googtransCookie, ?string $contentLangCookie = null): ?string
    {
        if ($googtransCookie && preg_match('#/auto/([^;/]+)#', $googtransCookie, $m)) {
            return self::normalizeLangCode($m[1]);
        }

        if ($contentLangCookie !== null && trim($contentLangCookie) !== '') {
            $normalized = self::normalizeLangCode($contentLangCookie);
            if ($normalized !== '') {
                return $normalized;
            }
        }

        return null;
    }

    public static function normalizeLangCode(string $code): string
    {
        $code = strtolower(trim($code));
        if ($code === '' || $code === '__original__') {
            return '';
        }
        if (str_contains($code, '-')) {
            return explode('-', $code, 2)[0];
        }

        return $code;
    }

    public static function translate(?string $text, string $targetLang): string
    {
        $map = self::translateMany([$text ?? ''], $targetLang);

        return $map[0] ?? ($text ?? '');
    }

    /**
     * Translate many strings in parallel (cache-aware).
     * Preserves input indexes in the returned array.
     *
     * @param  array<int|string, string|null>  $texts
     * @return array<int|string, string>
     */
    public static function translateMany(array $texts, string $targetLang): array
    {
        $target = self::normalizeLangCode($targetLang) ?: 'en';
        $results = [];
        $pending = [];

        foreach ($texts as $key => $text) {
            if ($text === null) {
                $results[$key] = '';
                continue;
            }
            if (trim($text) === '') {
                $results[$key] = $text;
                continue;
            }

            $cacheKey = 'content_tr:' . $target . ':' . sha1($text);
            $cached = Cache::get($cacheKey);
            if (is_string($cached) && $cached !== '') {
                $results[$key] = $cached;
                continue;
            }

            $pending[$key] = $text;
        }

        if ($pending === []) {
            return $results;
        }

        foreach (array_chunk($pending, self::POOL_CHUNK, true) as $chunk) {
            try {
                $responses = Http::pool(function ($pool) use ($chunk, $target) {
                    foreach ($chunk as $key => $text) {
                        $parts = self::splitText($text);
                        // One request per text using first chunk only for pool key;
                        // long texts fall back to sequential translate() below if multi-chunk.
                        if (count($parts) === 1) {
                            $pool->as((string) $key)->timeout(12)->get(self::ENDPOINT, [
                                'client' => 'gtx',
                                'sl' => 'auto',
                                'tl' => $target,
                                'dt' => 't',
                                'q' => $text,
                            ]);
                        }
                    }
                });
            } catch (\Throwable $e) {
                Log::warning('ContentTranslationService pool failed', [
                    'target' => $target,
                    'message' => $e->getMessage(),
                ]);
                foreach ($chunk as $key => $text) {
                    $results[$key] = $text;
                }
                continue;
            }

            foreach ($chunk as $key => $text) {
                $parts = self::splitText($text);
                if (count($parts) > 1) {
                    // Rare long posts: reuse single-string path (still cached per chunk).
                    $results[$key] = self::translateLong($text, $target);
                    continue;
                }

                $response = $responses[(string) $key] ?? null;
                try {
                    // Http::pool returns a Response on success, or a Throwable
                    // (e.g. ConnectException) when the request never completed.
                    if ($response instanceof \Throwable) {
                        throw new \RuntimeException(
                            'Translate connection failed: ' . $response->getMessage(),
                            0,
                            $response
                        );
                    }
                    if (!$response instanceof HttpResponse || !$response->successful()) {
                        $status = $response instanceof HttpResponse ? $response->status() : 'none';
                        throw new \RuntimeException('Translate HTTP failed (' . $status . ')');
                    }
                    $translated = self::parseTranslatePayload($response->json(), $text);
                    $cacheKey = 'content_tr:' . $target . ':' . sha1($text);
                    Cache::put($cacheKey, $translated, self::CACHE_TTL_SECONDS);
                    $results[$key] = $translated;
                } catch (\Throwable $e) {
                    Log::warning('ContentTranslationService item failed', [
                        'target' => $target,
                        'message' => $e->getMessage(),
                    ]);
                    $results[$key] = $text;
                }
            }
        }

        return $results;
    }

    public static function translateBulletinFields(object $bulletin, string $targetLang): void
    {
        $translated = self::translateMany([
            'title' => (string) ($bulletin->title ?? ''),
            'description' => (string) ($bulletin->description ?? ''),
        ], $targetLang);

        if (isset($bulletin->title)) {
            $bulletin->title = $translated['title'] ?? $bulletin->title;
        }
        if (isset($bulletin->description)) {
            $bulletin->description = $translated['description'] ?? $bulletin->description;
        }
    }

    private static function translateLong(string $text, string $target): string
    {
        try {
            $chunks = self::splitText($text);
            $translatedParts = [];
            foreach ($chunks as $chunk) {
                $response = Http::timeout(12)->get(self::ENDPOINT, [
                    'client' => 'gtx',
                    'sl' => 'auto',
                    'tl' => $target,
                    'dt' => 't',
                    'q' => $chunk,
                ]);
                if (!$response->successful()) {
                    throw new \RuntimeException('Translate HTTP ' . $response->status());
                }
                $translatedParts[] = self::parseTranslatePayload($response->json(), $chunk);
            }
            $translated = implode('', $translatedParts);
            if ($translated === '') {
                return $text;
            }
            Cache::put('content_tr:' . $target . ':' . sha1($text), $translated, self::CACHE_TTL_SECONDS);

            return $translated;
        } catch (\Throwable $e) {
            Log::warning('ContentTranslationService long text failed', [
                'target' => $target,
                'message' => $e->getMessage(),
            ]);

            return $text;
        }
    }

    private static function parseTranslatePayload($json, string $fallback): string
    {
        if (!is_array($json) || !isset($json[0]) || !is_array($json[0])) {
            throw new \RuntimeException('Unexpected translate payload');
        }

        $out = '';
        foreach ($json[0] as $part) {
            if (is_array($part) && isset($part[0]) && is_string($part[0])) {
                $out .= $part[0];
            }
        }

        return $out !== '' ? $out : $fallback;
    }

    private static function splitText(string $text): array
    {
        if (mb_strlen($text) <= self::MAX_CHUNK) {
            return [$text];
        }

        $chunks = [];
        $start = 0;
        $length = mb_strlen($text);
        while ($start < $length) {
            $end = min($start + self::MAX_CHUNK, $length);
            if ($end < $length) {
                $slice = mb_substr($text, $start, $end - $start);
                $nl = mb_strrpos($slice, "\n");
                $sp = mb_strrpos($slice, ' ');
                if ($nl !== false && $nl > 0) {
                    $end = $start + $nl;
                } elseif ($sp !== false && $sp > 0) {
                    $end = $start + $sp;
                }
            }
            $chunks[] = mb_substr($text, $start, $end - $start);
            $start = $end;
        }

        return $chunks;
    }
}
