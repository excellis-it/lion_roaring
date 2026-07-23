# Bulletin Board Content Translation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Translate Bulletin Board title/description into the visitor’s selected language (including English) via server-side auto-detect translation, without translating author names.

**Architecture:** `ContentTranslationService` calls Google’s free `translate_a/single` endpoint (`sl=auto`), caches by text hash + target, and is applied in `BulletinBoardController` before rendering. Blade marks author names `notranslate`.

**Tech Stack:** Laravel, `Illuminate\Support\Facades\Http`, `Cache`, PHPUnit

## Global Constraints

- Web Bulletin Board only (`list` + AJAX `load`)
- Translate only `title` and `description`
- Author names never translated (`notranslate`)
- No paid Google Cloud Translation API key
- On failure: show original text; log a warning
- Cache TTL: 30 days
- Do not commit unless the user asks

## File map

| File | Role |
|------|------|
| `app/Services/ContentTranslationService.php` | Translate + cache |
| `app/Http/Controllers/User/BulletinBoardController.php` | Resolve target lang; translate bulletins before view |
| `resources/views/user/bulletin-board/show-bulletin.blade.php` | `notranslate` on names; render (possibly translated) fields |
| `tests/Unit/ContentTranslationServiceTest.php` | Unit tests with `Http::fake` / `Cache` |

---

### Task 1: ContentTranslationService + unit tests

**Files:**
- Create: `app/Services/ContentTranslationService.php`
- Create: `tests/Unit/ContentTranslationServiceTest.php`

**Interfaces:**
- Produces:
  - `ContentTranslationService::translate(?string $text, string $targetLang): string`
  - `ContentTranslationService::resolveTargetLanguage(?string $googtransCookie): string`
  - `ContentTranslationService::translateBulletinFields(object $bulletin, string $targetLang): void` — sets `title` / `description` in place on a model/stdClass that has those attributes

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Unit;

use App\Services\ContentTranslationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ContentTranslationServiceTest extends TestCase
{
    public function test_resolve_target_language_defaults_to_en(): void
    {
        $this->assertSame('en', ContentTranslationService::resolveTargetLanguage(null));
        $this->assertSame('en', ContentTranslationService::resolveTargetLanguage(''));
    }

    public function test_resolve_target_language_from_googtrans_cookie(): void
    {
        $this->assertSame('de', ContentTranslationService::resolveTargetLanguage('/auto/de'));
        $this->assertSame('es', ContentTranslationService::resolveTargetLanguage('/auto/es'));
    }

    public function test_translate_returns_empty_unchanged(): void
    {
        Http::fake();
        $this->assertSame('', ContentTranslationService::translate('', 'en'));
        $this->assertSame('   ', ContentTranslationService::translate('   ', 'en'));
        Http::assertNothingSent();
    }

    public function test_translate_uses_cache_and_http(): void
    {
        Cache::flush();
        Http::fake([
            'translate.googleapis.com/*' => Http::response([
                [['Royal garment', 'vestido Real', null, null, 3]],
                null,
                'es',
            ], 200),
        ]);

        $first = ContentTranslationService::translate('vestido Real', 'en');
        $this->assertSame('Royal garment', $first);

        $second = ContentTranslationService::translate('vestido Real', 'en');
        $this->assertSame('Royal garment', $second);

        Http::assertSentCount(1);
    }

    public function test_translate_falls_back_to_original_on_http_failure(): void
    {
        Cache::flush();
        Http::fake([
            'translate.googleapis.com/*' => Http::response('error', 500),
        ]);

        $out = ContentTranslationService::translate('Hola mundo', 'en');
        $this->assertSame('Hola mundo', $out);
    }

    public function test_translate_bulletin_fields_mutates_title_and_description(): void
    {
        Cache::flush();
        Http::fake([
            'translate.googleapis.com/*' => Http::sequence()
                ->push([[['Title EN', 'Titulo', null, null, 3]], null, 'es'], 200)
                ->push([[['Body EN', 'Cuerpo', null, null, 3]], null, 'es'], 200),
        ]);

        $bulletin = (object) ['title' => 'Titulo', 'description' => 'Cuerpo'];
        ContentTranslationService::translateBulletinFields($bulletin, 'en');

        $this->assertSame('Title EN', $bulletin->title);
        $this->assertSame('Body EN', $bulletin->description);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `cd /Volumes/CrucialMacExt/MacOffload/MAMP_htdocs/lion_roaring && php vendor/bin/phpunit tests/Unit/ContentTranslationServiceTest.php`
Expected: FAIL (class not found)

- [ ] **Step 3: Write minimal implementation**

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContentTranslationService
{
    private const ENDPOINT = 'https://translate.googleapis.com/translate_a/single';
    private const CACHE_TTL_SECONDS = 60 * 60 * 24 * 30;
    private const MAX_CHUNK = 3800;

    public static function resolveTargetLanguage(?string $googtransCookie): string
    {
        if ($googtransCookie && preg_match('#/auto/([^;/]+)#', $googtransCookie, $m)) {
            return strtolower($m[1]);
        }

        return 'en';
    }

    public static function translate(?string $text, string $targetLang): string
    {
        if ($text === null) {
            return '';
        }
        if (trim($text) === '') {
            return $text;
        }

        $target = strtolower(trim($targetLang)) ?: 'en';
        if (str_contains($target, '-')) {
            $target = explode('-', $target, 2)[0];
        }

        $cacheKey = 'content_tr:' . $target . ':' . sha1($text);
        $cached = Cache::get($cacheKey);
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        try {
            $chunks = self::splitText($text);
            $translatedParts = [];
            foreach ($chunks as $chunk) {
                $translatedParts[] = self::translateChunk($chunk, $target);
            }
            $translated = implode('', $translatedParts);
            if ($translated === '') {
                return $text;
            }
            Cache::put($cacheKey, $translated, self::CACHE_TTL_SECONDS);

            return $translated;
        } catch (\Throwable $e) {
            Log::warning('ContentTranslationService failed', [
                'target' => $target,
                'message' => $e->getMessage(),
            ]);

            return $text;
        }
    }

    public static function translateBulletinFields(object $bulletin, string $targetLang): void
    {
        if (isset($bulletin->title)) {
            $bulletin->title = self::translate((string) $bulletin->title, $targetLang);
        }
        if (isset($bulletin->description)) {
            $bulletin->description = self::translate((string) $bulletin->description, $targetLang);
        }
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

    private static function translateChunk(string $text, string $target): string
    {
        $response = Http::timeout(12)->get(self::ENDPOINT, [
            'client' => 'gtx',
            'sl' => 'auto',
            'tl' => $target,
            'dt' => 't',
            'q' => $text,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Translate HTTP ' . $response->status());
        }

        $json = $response->json();
        if (!is_array($json) || !isset($json[0]) || !is_array($json[0])) {
            throw new \RuntimeException('Unexpected translate payload');
        }

        $out = '';
        foreach ($json[0] as $part) {
            if (is_array($part) && isset($part[0]) && is_string($part[0])) {
                $out .= $part[0];
            }
        }

        return $out !== '' ? $out : $text;
    }
}
```

- [ ] **Step 4: Run tests to verify they pass**

Run: `php vendor/bin/phpunit tests/Unit/ContentTranslationServiceTest.php`
Expected: PASS

- [ ] **Step 5: Commit** — skip unless user asks

---

### Task 2: Wire BulletinBoardController

**Files:**
- Modify: `app/Http/Controllers/User/BulletinBoardController.php`

**Interfaces:**
- Consumes: `ContentTranslationService::resolveTargetLanguage`, `translateBulletinFields`

- [ ] **Step 1: Add private helper and call it before both views**

In `BulletinBoardController`, after `$bulletins` is loaded in `list()` and `load()`:

```php
use App\Services\ContentTranslationService;

// ...
$targetLang = ContentTranslationService::resolveTargetLanguage($_COOKIE['googtrans'] ?? null);
foreach ($bulletins as $bulletin) {
    ContentTranslationService::translateBulletinFields($bulletin, $targetLang);
}
```

Place this immediately before `return view(...)` / `return response()->json(...)`.

- [ ] **Step 2: Manual sanity** — optional live request; unit coverage is in Task 1

- [ ] **Step 3: Commit** — skip unless user asks

---

### Task 3: Protect author names in Blade

**Files:**
- Modify: `resources/views/user/bulletin-board/show-bulletin.blade.php`

- [ ] **Step 1: Add notranslate to both name blocks**

Change both `.name_bull` divs to:

```blade
<div class="name_bull notranslate" translate="no">
```

- [ ] **Step 2: Commit** — skip unless user asks

---

### Task 4: Verification

- [ ] **Step 1: Run unit tests**

`php vendor/bin/phpunit tests/Unit/ContentTranslationServiceTest.php`

- [ ] **Step 2: Smoke-check endpoint (optional)**

```bash
php -r 'require "vendor/autoload.php"; $app=require "bootstrap/app.php"; $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); echo App\Services\ContentTranslationService::translate("El vestido Real es vida", "en"), "\n";'
```

Expected: English translation of the Spanish phrase (requires network)

---

## Spec coverage

| Spec item | Task |
|-----------|------|
| Target from googtrans / default en | Task 1 |
| Translate title + description | Task 1–2 |
| Cache 30 days | Task 1 |
| Failure → original | Task 1 |
| Names notranslate | Task 3 |
| list + load | Task 2 |
| Free Google endpoint | Task 1 |
