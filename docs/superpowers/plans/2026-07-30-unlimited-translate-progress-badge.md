# Unlimited Translate + Progress Badge Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Remove all character budgets so translation always runs, keep DB cache for cost savings, show a temp header progress badge on every LrTranslate page, and fix bulletin-board / cache-poison gaps.

**Architecture:** Server always translates DB misses via Google; client never caches failed passthroughs; `LrTranslate` tracks job progress and injects a `notranslate` badge; bulletin SSR translates for any active `content_lang`.

**Tech Stack:** Laravel, `GoogleTranslateService`, `lr-translate.js`, Blade layouts, PMA markdown docs.

## Global Constraints

- `TRANSLATE_MONTHLY_CHAR_LIMIT=0` and `TRANSLATE_SESSION_DAILY_CHAR_LIMIT=0` mean unlimited
- Keep DB `translation_cache` + `translation_usage`
- Keep route throttle + language allowlist
- Admin stays English-only
- Do not commit unless user asks
- Update `docs/pma/` for translation behavior changes

---

### Task 1: Unlimited server budgets

**Files:**
- Modify: `app/Services/GoogleTranslateService.php`
- Modify: `app/Http/Controllers/Frontend/TranslateController.php`
- Modify: `config/services.php`
- Modify: `.env.example`, `.env`

- [ ] **Step 1:** In `GoogleTranslateService`, make `budgetRemaining()` return `PHP_INT_MAX` when `monthlyCharLimit() <= 0`. In `callApiForMissing`, skip the budget break when unlimited.

- [ ] **Step 2:** In `TranslateController::batch`, remove `withinVisitorBudget` check and the `budget_exhausted` early return. Keep `withinVisitorBudget` returning true when limit ≤ 0 if method retained, or delete method entirely.

- [ ] **Step 3:** Document in `config/services.php` and `.env.example` that `0` = unlimited. Set `.env` both limits to `0`. Bump `TRANSLATE_CACHE_VERSION` to `v2` to clear poisoned client caches.

- [ ] **Step 4:** Verify with tinker that `budgetRemaining()` is huge when limit is 0 and `translateBatch(['Hello'],'es')` still works.

---

### Task 2: Client — no poison cache + progress badge

**Files:**
- Modify: `public/frontend_assets/js/lr-translate.js`
- Modify: `resources/views/frontend/includes/google_translate.blade.php`
- Modify: `resources/views/user/includes/header.blade.php` (add `[data-lr-translate-badge]` slot)
- Modify: `resources/views/frontend/includes/header.blade.php` (same slot near switcher)
- Test: `tests/js/lr-translate.test.js`

- [ ] **Step 1:** In `runChunks`, only `cacheSet` when `data.ok === true` and translated value is a non-empty string. On `ok:false`, do not write to mem/localStorage.

- [ ] **Step 2:** Add progress state: `progressDone`, `progressTotal`, `updateProgressBadge()`. In `buildJobs`/`translatePass`/`runChunks`, increment done as local hits apply and as each chunk item resolves; show `%`.

- [ ] **Step 3:** Inject badge element `#lr-translate-progress` with `notranslate translate=no data-nt`. Mount into `[data-lr-translate-badge]` or fallback fixed top-right. Hide when Original or after 100% fade.

- [ ] **Step 4:** Add minimal CSS in `google_translate.blade.php`. Add empty `<span data-lr-translate-badge></span>` in user + frontend headers.

- [ ] **Step 5:** Extend `tests/js/lr-translate.test.js` with a case that `ok:false` does not change heading text on second language switch after poisoned response simulation; run with jsdom if available.

---

### Task 3: Bulletin board always translates

**Files:**
- Modify: `app/Http/Controllers/User/BulletinBoardController.php`
- Modify: `resources/views/user/layouts/master.blade.php` (`loadBulletin` success)

- [ ] **Step 1:** Change `applyTranslations` so any resolved non-null `$targetLang` calls `translateBulletinFields($bulletin, $targetLang)` (remove `if ($targetLang !== 'en') return`).

- [ ] **Step 2:** After `$('#show-bulletin').html(resp.view)`, call `window.LrTranslate && LrTranslate.refresh(document.getElementById('show-bulletin'))`. Same for `loadBulletinTable` / `loadSingleBulletin` if those inject HTML.

---

### Task 4: Docs

**Files:**
- Modify: `docs/pma/website-frontend.md`, `docs/pma/user-pma.md`, `docs/pma/bulletins.md` (bump `updated`)
- Modify: `docs/plans/google-cloud-translate-plan.md` (budget note)
- Modify: spec status if needed

- [ ] **Step 1:** Replace Google Website Translator wording with LrTranslate / Cloud Translation; note no user char quota; progress badge; bulletin SSR for any `content_lang`.

---

### Task 5: Verification

- [ ] Hit `/translate/batch` mentally / via browser with non-English lang — no `budget_exhausted` / `visitor_quota_exceeded`
- [ ] Confirm badge appears during translate on User PMA
- [ ] Confirm `/user/bulletin-board` content translates
- [ ] Run `node tests/js/lr-translate.test.js` if jsdom present
