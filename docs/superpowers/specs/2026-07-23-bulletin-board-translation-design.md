# Bulletin Board Content Translation — Design

**Date:** 2026-07-23  
**Status:** Approved (product direction); pending engineer review of this spec  
**Scope:** Web Bulletin Board only (`user/bulletin-board`)

## Problem

The site language tool (Google Website Translator) assumes the page base language is English. Selecting English clears the `googtrans` cookie and reloads the **original** HTML. User-generated bulletin posts written in Spanish, German, or other languages therefore stay untranslated when the visitor wants English.

Forcing the whole page to translate into English also risks mangling proper names (e.g. “Daud” → “David”).

## Goal

When a visitor selects a language (including English), each Bulletin Board post’s **title** and **description** are machine-translated into that language. Author display names are never translated.

## Non-goals

- Messaging / chat / other UGC surfaces
- Flutter app translation layer
- Changing how menus/UI use Google Website Translator (English → other languages remains as today)
- Paid Google Cloud Translation API (unless later required)

## Approach

**Bulletin-only server translation** with auto language detection and caching.

1. Resolve the visitor’s target language from the existing language cookie:
   - If `googtrans=/auto/{code}` is present → target = `{code}`
   - Otherwise → target = `en`
2. On Bulletin Board `list` and AJAX `load`, translate each bulletin’s `title` and `description` into the target language via a shared service.
3. Mark author name nodes with `notranslate` / `translate="no"` so page-level Google Translate does not alter names when the UI language is non-English.
4. On translation failure, show the original text unchanged.

## Architecture

| Piece | Responsibility |
|-------|----------------|
| `App\Services\ContentTranslationService` | Auto-detect source → translate to target; chunk long text; Laravel cache by `(hash(text), target)` |
| `BulletinBoardController` | Resolve target lang; run title/description through the service before rendering |
| `user/bulletin-board/show-bulletin.blade.php` | Render translated fields; protect `.name_bull` from page translate |
| Google free translate HTTP endpoint | Same class of free endpoint used by the Flutter `translator` package; no new paid API key |

### Target language resolution (server)

```php
$target = 'en';
if (!empty($_COOKIE['googtrans']) && preg_match('#/auto/([^;/]+)#', $_COOKIE['googtrans'], $m)) {
    $target = $m[1];
}
```

### Caching

- Key: `content_tr:{target}:{sha1(text)}`
- TTL: 30 days (configurable)
- Skip outbound API call when cached
- Empty / whitespace-only strings: return as-is (no API call)

### Failure behavior

- Log warning with bulletin id / target when possible
- Return original string so the board still renders

## UX / product rules

- Names: never translated (HTML `notranslate`)
- Only title + description of bulletins on the board
- Spanish → English, German → English, Spanish → German all supported via auto-detect → target
- If source already matches target, service may return original (detect or API no-op)

## Testing

- Unit: `ContentTranslationService` cache hit/miss, empty input, failure fallback (mock HTTP)
- Manual: Bulletin Board with Spanish post + English selected shows English body; author name unchanged; switch to another language and reload/AJAX load updates body

## Out of scope follow-ups

- Apply same pattern to messaging
- Align Flutter `TranslationService` to auto-detect (currently forces `from: 'en'` and skips when locale is English)
