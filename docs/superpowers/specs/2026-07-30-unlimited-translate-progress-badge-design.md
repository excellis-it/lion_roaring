# Unlimited Translate + Progress Badge — Design

**Date:** 2026-07-30  
**Repo:** `lion_roaring` (Laravel web)  
**Status:** Approved; implemented 2026-07-30  
**PMA docs:** Updated Website Frontend / User PMA / Bulletins  

---

## Goal

1. **Always translate** when a non-Original language is selected — no per-user or monthly character budgets that stop API calls.
2. **Cost control via DB cache only** — reuse `translation_cache` forever; usage stats remain for visibility.
3. **Header progress badge everywhere** `LrTranslate` loads — show page translation % while a pass runs; independent of language switcher presence.
4. **Fix pages that never translate** (e.g. `/user/bulletin-board`) — SSR + client cache-poison and UGC reload gaps.

Admin stays English-only (unchanged).

---

## Decisions (locked)

| Topic | Choice |
|---|---|
| Budgets | **B** — remove monthly + session character limits; always call Google on DB miss |
| Abuse guard | Keep mild route `throttle` only (not a user quota) |
| DB cache | Keep + continue writing on successful API responses |
| Progress badge | On every surface that boots `LrTranslate`, whether or not a language switcher exists |
| Language switcher | Leave placement as today (public frontend); badge does not depend on it |
| Architecture | Approach 1 — engine fixes + JS-injected badge; SSR bulletins for any active `content_lang` |

---

## Section 1 — Limits & cache

### Remove blocking budgets

- `TranslateController`: delete `withinVisitorBudget()` gate and `budget_exhausted` early return.
- `GoogleTranslateService::callApiForMissing`: stop checking `budgetRemaining()`; always request misses.
- Config / `.env`: set `TRANSLATE_MONTHLY_CHAR_LIMIT=0` and `TRANSLATE_SESSION_DAILY_CHAR_LIMIT=0` to mean **unlimited** (or ignore when ≤ 0). Prefer “≤ 0 = unlimited” so existing env keys stay valid without removing them.
- Keep `translation_usage` recording and `translate:stats` for cost visibility (non-blocking).

### Client cache integrity

- On batch response: **only** `cacheSet` when `data.ok === true` and the translated string differs from passthrough failure modes.
- Never persist originals from `ok: false` into memory/localStorage (fixes “page forever stays English” after quota exhaustion).
- Optional: bump `TRANSLATE_CACHE_VERSION` so poisoned client caches are discarded once.

### Server DB cache (unchanged contract)

- Hash = `sha1(normalize(text))` + `target_lang`
- Upsert on successful Google responses
- Cache hits never call Google

---

## Section 2 — Progress badge

### Behavior

- Visible when active language ≠ Original **and** a translation pass has work (or briefly at 100% then fade out ~800ms).
- Label: `Translating… N%` (English UI chrome; badge marked `notranslate` / `data-nt`).
- Progress = `(resolved_jobs / total_jobs) * 100` for the current pass (local cache hits count as resolved immediately; network jobs update as chunks complete).
- MutationObserver / `refresh()` re-passes show the badge again for new strings only.

### Placement

- Prefer mount point `[data-lr-translate-badge]` if present in header.
- Else inject into `.app-header nav ul.navbar-nav.ms-auto` (User PMA) or public header language area / first `header` / `nav`.
- Fallback: `position: fixed; top: …; right: …; z-index` high enough to sit above chrome.
- Implemented once in `lr-translate.js` so every page that includes `google_translate.blade.php` gets it with zero per-page logic.

### Events (optional for diagnostics)

- Dispatch `lr:translate-progress` with `{ lang, done, total, percent }`.
- Existing `lr:translated` fires when a full pass completes.

---

## Section 3 — Bulletin board & UGC reloads

### Server (`BulletinBoardController`)

- `applyTranslations`: if `content_lang` is set and not Original, resolve target and call `ContentTranslationService::translateBulletinFields` for **that** language (not only `en`).
- Skip when Original / empty cookie.
- Source language remains `auto` for UGC (unknown author language).

### Client

- After known AJAX injects that replace `#show-bulletin` (and similar), call `LrTranslate.refresh(root)` when available.
- Rely on MutationObserver as the general path; explicit refresh is a safety net for jQuery `.html()` races.

### Double work

- SSR may translate UGC into the active language; client walker may still see those nodes. DB + client cache make re-requests cheap/free. Acceptable.

---

## Files to change (implementation scope)

| File | Change |
|---|---|
| `app/Http/Controllers/Frontend/TranslateController.php` | Remove visitor/monthly budget gates |
| `app/Services/GoogleTranslateService.php` | Unlimited when limit ≤ 0; always translate misses |
| `config/services.php` / `.env.example` | Document 0 = unlimited |
| `.env` | Set both limits to `0` |
| `public/frontend_assets/js/lr-translate.js` | Progress badge, progress tracking, no poison cache |
| `resources/views/frontend/includes/google_translate.blade.php` | Badge CSS; bump cache version if needed |
| `resources/views/user/includes/header.blade.php` (+ other headers if easy) | Optional `data-lr-translate-badge` slot |
| `app/Http/Controllers/User/BulletinBoardController.php` | Translate for any active lang |
| `resources/views/user/layouts/master.blade.php` | `LrTranslate.refresh` after bulletin AJAX |
| `docs/plans/google-cloud-translate-plan.md` | Note budget decision change |
| `docs/pma/*.md` | Brief note: no user char quota; progress badge |

Out of scope: Flutter (Phase 7), admin translation, redesign of language switcher placement.

---

## Success criteria

- [ ] With language ≠ Original, every included surface shows a temp progress badge and reaches 100%.
- [ ] Hitting `/translate/batch` never returns `visitor_quota_exceeded` or `budget_exhausted`.
- [ ] Cache miss → Google → DB row; second request for same string → DB hit only.
- [ ] `/user/bulletin-board` titles and bodies translate (SSR and/or client); names stay protected.
- [ ] Failed API responses do not poison `localStorage` translations.
- [ ] `TRANSLATE_ENABLED=false` still kill-switches the engine.

---

## Risks

| Risk | Mitigation |
|---|---|
| Unbounded Google spend | DB cache + monitoring via `translate:stats` / usage table; kill switch |
| Public endpoint abuse | Keep `throttle:120,1` + language allowlist |
| Badge flicker on heavy AJAX pages | Debounce; only show when `total_jobs > 0` |
