# Google Cloud Translation API — Implementation Plan

**Date:** 2026-07-30
**Status: IMPLEMENTED** (Phases 1–6). Phase 7 (Flutter) deliberately deferred.
**Follow-up 2026-07-30:** unlimited char budgets (0 = unlimited), progress badge, bulletin SSR for any `content_lang`, client cache-poison fix. See `docs/superpowers/specs/2026-07-30-unlimited-translate-progress-badge-design.md`.
**Repo:** `lion_roaring` (Laravel web).
**Replaces:** free Google Translate Element widget (`resources/views/frontend/includes/google_translate.blade.php`, 636 lines)

---

## 0. What shipped

| Area | File |
|---|---|
| API client, cache, budget | `app/Services/GoogleTranslateService.php` |
| UGC translation (public API preserved) | `app/Services/ContentTranslationService.php` |
| Batch endpoint `POST /translate/batch` | `app/Http/Controllers/Frontend/TranslateController.php` |
| Failure logging | `app/Http/Controllers/Frontend/TranslationClientLogController.php` |
| Browser engine | `public/frontend_assets/js/lr-translate.js` |
| Failure reporting / user notice | `public/frontend_assets/js/translation-diagnostics.js` |
| Name protection (runtime) | `public/frontend_assets/js/protect-names-from-translate.js` |
| Bootstrap partial (same path, 4 layouts + 9 standalone pages) | `resources/views/frontend/includes/google_translate.blade.php` |
| Switcher with `Original` | `resources/views/frontend/includes/language_switcher.blade.php` |
| Never-translate component | `resources/views/components/nt.blade.php` |
| Cache + usage tables | `database/migrations/2026_07_30_1000*` |
| Warm / stats commands | `app/Console/Commands/TranslateWarm.php`, `TranslateStats.php` |
| Names codemod | `scripts/codemod-no-translate.php` |
| Detected-source cache | `database/migrations/2026_07_30_170000_create_translation_source_table.php` |
| Cache purge | `app/Console/Commands/TranslatePurge.php` |
| 429-storm regression test | `tests/js/lr-translate-backoff.test.js` |
| Exclusion-rule regression test | `tests/js/lr-translate.test.js` |

**Follow-up 2026-07-30 (b) — 429 storm + mixed-language sources**

Two defects found in production use, both fixed:

1. **429 storm.** The endpoint capped batches at 128 items (Google's *Google-side* limit, wrongly applied to our own API), so a heavy page needed hundreds of requests. Worse, a failed batch left its nodes untranslated, the MutationObserver re-collected them, and the loop fed itself — 1,000+ console errors. Fixed by:
   - `/translate/batch` now accepts **2,000 items / 400k chars**; `GoogleTranslateService` splits only the *cache misses* into Google-legal chunks (128 segments / 28k chars) and runs them through `Http::pool` with concurrency 8, retrying 429/5xx with exponential backoff + jitter.
   - Client keeps per-hash `pending` / `cooldown` / `waiters` registries, so a string is requested **at most once per 60s cooldown** no matter how many times the DOM is rescanned, plus a circuit breaker (5 consecutive failures → 60s pause).
   - Measured: **400 strings / 32,930 chars in one request, 2.03s cold, 385ms warm (0 API calls)**. Under a permanent-429 fault with 16 DOM bursts: **8 requests total**, page stays readable, Original still works.

2. **Mixed-language pages.** The server hardcoded `source: 'en'`, so a Hindi post was translated *as if English* (producing subtly corrupted Hindi), and selecting English was a no-op (`passthrough: source_language`). Fixed by:
   - New `translation_source` table caching the **detected language per unique string**. Detection is billed at the translation rate, so it is paid **once per string, ever** — never per page view.
   - Unambiguous scripts (Devanagari, Arabic, CJK, Thai, Hebrew, …) resolve **locally for free**; only ambiguous Latin text reaches the detect API.
   - `translateBatch` groups misses by detected source and translates each group with an explicit `source`. Strings already in the target language are stored as **identity** — the author's words are returned untouched and never round-tripped.
   - English is now a real target, not a passthrough.
   - `php artisan translate:purge` added; the existing cache was purged (2,499 rows) because it was built under the wrong-source assumption. `TRANSLATE_CACHE_VERSION` bumped to `v3`.

**Audit 2026-07-30 (c) — four defects found and fixed**

1. **Original showed translated text, not the author's words** (reported: "vestido Real"). `BulletinBoardController` server-translated bulletins whenever `content_lang` held a language, so a page loaded in English arrived already-English and the engine snapshotted *that* as the original. **Server-side bulletin translation removed** — the client engine already covers bulletins, and SSR both duplicated the cost and broke Original. Locked in by `tests/js/lr-translate-original.test.js`.
2. **Over-broad icon selector silently blocked real copy.** `[class^="icon"]` / `[class*="-icon"]` matched `.icon-heading` (e-Store contact headings "Mail us", "Our Address", "Call us") and every `.form-control.has-icon` placeholder in the checkout form — none of them ever translated. Icon detection is now split: unambiguous icon fonts skip outright; loose `*icon*` class names skip only text that is ligature-shaped (single token, no spaces). `tests/js/lr-translate-icons.test.js`.
3. **A transient detect failure poisoned the source cache permanently.** `callDetectApi` filled unresolved hashes with `'en'` and `detectLanguages` wrote that guess to `translation_source` — one timeout would permanently mislabel a Hindi post as English, and every later translation would use the wrong source. The fallback is now per-request only and never cached.
4. **Redundant detection writes.** `persistDetected` re-upserted rows it had just read, so warming a second language rewrote the whole corpus on every batch. Only newly-resolved detections are written now.

Also removed: dead `session_daily_char_limit` config (the per-visitor quota was dropped when budgets went unlimited) and a duplicated `strtolower` in `normalizeLangCode`.

**Test suites:** 54 assertions across `tests/js/*.test.js` — exclusions, 429 storm, icon selectors, Original round-trip.

**Audit 2026-07-30 (d) — second pass: 6 defects**

1. **Stale-language writes (user-visible).** Responses already in flight applied their text unconditionally, so choosing **Original while a batch was outstanding re-translated the page moments after restoring it**. Added a `langEpoch` bumped on every switch; late results are still cached (they were paid for) but never written to the DOM. `tests/js/lr-translate-race.test.js`.
2. **Single-word translations could not be restored (regression from audit (c)).** The ligature check ran against the *current* text, so `.icon-heading` "Mail us" → "Correo" (one word) was then classified as an icon and Original could never restore it. The check now always uses the original text.
3. **Unbounded spend on a public endpoint.** `/translate/batch` is unauthenticated; with budgets unlimited and the old per-visitor cap deleted, `throttle:600,1` × 400k chars allowed ~$4,800/min. Throttle lowered to `90,1` and a **per-visitor daily cap on *billed* characters** added (`TRANSLATE_VISITOR_DAILY_BILLED_CHARS=150000` ≈ $3/visitor/day). Cache hits are free and never counted.
4. **Over-quota blanked the site.** The first cut of the cap refused cached strings too, so one noisy visitor would lose all translation for the day. Over-quota now switches to **cache-only**: everything already paid for is still served, nothing new is bought.
5. **Client cached failures as translations.** The endpoint echoed the source text back for anything it could not translate, and the browser cached "English → English" permanently. The endpoint now returns `null` for unresolved items; the client skips them and puts them in cooldown instead of re-sending every pass. `tests/js/lr-translate-nullsafe.test.js`.
6. **Stale failure-reason allow-list** (`payload_too_large` missing, two removed reasons still listed) and dead `pendingPass` / `inFlight` variables; `clearCache()` did not reset the cooldown/circuit state.

**Test suites:** 66 assertions across 6 files in `tests/js/`.

**Measured:** warming every public page costs **$1.79 per language** (858 distinct strings, 89,698 chars after dedupe — down from ~4,500 raw strings). Cache hits cost nothing and are not counted against the budget.

---

## 1. Decisions (locked)

| Question | Decision |
|---|---|
| Scope now | Laravel web only |
| Architecture | Client-side DOM walker + Laravel proxy + persistent server cache |
| Surfaces | `website`, `user_pma`, `e-store`, `e-learning` |
| Admin | **Excluded** — stays English-only, no widget added |
| "Original" option | Kept — `__original__` sentinel means *no translation at all* |
| Never translate | Icons, usernames, first/last/full names |
| API key | Server-side only, never exposed to JS |

---

## 2. Why the current system fails

- **Google Translate Element is unofficial/unsupported.** No SLA, silently rate-limits, script is sometimes blocked (`google_translate_script_blocked` is already an instrumented failure mode in `translation-diagnostics.js`).
- **Cookie hell.** `googtrans` is path-scoped, `Secure`-flagged on HTTPS, and Google rewrites it. The current partial spends ~250 lines expiring the cookie across every path × domain × SameSite variant, and still needs a `pageshow` bfcache guard and a `_lr=` cache-buster hard-navigate on every language change.
- **Cookie-jar overflow.** Writing `googtrans`/`content_lang` across dozens of paths risks evicting the Laravel session cookie → random logouts (already noted in a code comment).
- **Full page reload per language switch.** `hardNavigateForLanguageChange()`.
- **Exclusions are unreliable.** Google's walker honours `translate="no"` inconsistently; only 29 `no_translate()` call sites exist against ~1441 name renders.

---

## 3. Target architecture

```
Browser                          Laravel                       Google
───────────────────────────────────────────────────────────────────────
lr-translate.js
  walk DOM → text nodes
  filter (skip rules)
  hash each string
  check localStorage cache
  batch misses (≤100/req)  ──►  POST /api/translate
                                 TranslationController
                                   ├ lookup translation_cache (DB)
                                   ├ miss? → GoogleTranslateService ──► v2/translate
                                   │           (batch q[], format=text)
                                   └ write-back to translation_cache
                            ◄──  { hash: translated }
  swap text nodes
  write localStorage
MutationObserver → repeat for AJAX/modal/DataTable content
```

Key property: **the API key never leaves the server**, and each unique string is paid for **once, ever** (across all users, forever).

---

## 4. Answer to "global function vs per-page edits"

**Global function covers everything except person names.**

| Concern | Automatic? | Why |
|---|---|---|
| Static Blade text | ✅ Yes | Walker sees all rendered text nodes |
| UGC (bulletins, posts, products) | ✅ Yes | Same — it's just text in the DOM |
| AJAX / DataTables / modals / toasts | ✅ Yes | MutationObserver |
| `<script>`, `<style>`, `<code>`, `<pre>` | ✅ Yes | Tag skip-list |
| Icons (`<i>`, `<svg>`, `.material-icons`, ligatures) | ✅ Yes | Tag + class skip-list. ~1484 occurrences, **zero file edits** |
| Emails, URLs, numbers, currency | ✅ Yes | Regex skip-list |
| Form `value`/`placeholder` on name inputs | ✅ Yes | Selector list (already exists in `protect-names-from-translate.js`) |
| **Person names in prose/tables** | ❌ **No** | After render, `{{ $user->first_name }}` is indistinguishable from ordinary text |

### The names gap — measured

- `no_translate()` call sites: **29**
- Files with `.notranslate`: **10**
- Files with `translate="no"`: **9**
- Name renders (`first_name`/`last_name`/`full_name`/`user_name`/`->name`): **~1441 across 142 blade files**

Coverage is ~2%. This must be closed by a **one-time mechanical codemod**, not per-page redesign:

1. Script scans blades for `{{ ... first_name|last_name|full_name|user_name|getFullNameAttribute ... }}`
2. Rewrites to `{!! no_translate(...) !!}`
3. Human review of the diff (~142 files, mechanical)
4. Runtime safety net: extend `protect-names-from-translate.js` selector list + add a `data-nt` attribute convention
5. Going forward: enforce via a `<x-nt>` Blade component + a CI grep check

**Effort: ~1 day of codemod + review, not 142 pages of work.**

---

## 5. Cost model

Google Cloud Translation v2 pricing: **$20 per 1M characters**.

Without persistent cache, whole-DOM translation of 517 views for every visitor is financially unbounded. With the DB cache:

- Cost is **per unique string, per target language, once**
- Estimated unique static UI strings: ~15–25k strings ≈ ~800k chars
- Per language: **~$16 one-time** for the entire static UI
- UGC grows incrementally as content is created

**Mandatory controls (build these in Phase 1, not later):**

1. `translation_cache` **DB table** — permanent reuse keyed by `source_hash` + `target_lang` (not Laravel `CACHE_DRIVER`).
2. ~~**Monthly character budget**~~ — **changed 2026-07-30:** `TRANSLATE_MONTHLY_CHAR_LIMIT=0` / `TRANSLATE_SESSION_DAILY_CHAR_LIMIT=0` mean **unlimited**. Always translate cache misses; cost control is DB reuse + usage logging (`translate:stats`). Optional non-zero limits remain supported in code for ops.
3. Mild route throttle on `/translate/batch` + language allowlist (not a user quota).
4. **`translation_usage` daily rollup table** for cost visibility.
5. **Restrict the API key** in Google Cloud Console: Cloud Translation API only + server IP allowlist.
6. Only allow target languages present in `translate_languages` — reject arbitrary `tl` values.

**UI (2026-07-30):** temporary header progress badge (`Translating… N%`) on every surface that boots LrTranslate.

---

## 6. Implementation phases

### Phase 1 — Backend foundation

| File | Action |
|---|---|
| `.env` / `.env.example` | `GOOGLE_TRANSLATE_API_KEY=`, `TRANSLATE_MONTHLY_CHAR_LIMIT=2000000`, `TRANSLATE_ENABLED=true` |
| `config/services.php` | add `google.translate_key`, `google.translate_char_limit` under existing `google` block (which already holds `maps_key`) |
| `database/migrations/*_create_translation_cache_table.php` | new — schema above |
| `database/migrations/*_create_translation_usage_table.php` | new — `date, target_lang, chars, requests` |
| `app/Services/GoogleTranslateService.php` | **new** — batch `POST https://translation.googleapis.com/language/translate/v2`, `format=text`, up to 100 `q[]` per call, retry w/ backoff, budget check, usage recording |
| `app/Services/ContentTranslationService.php` | **rewrite internals** — keep the public API (`resolveTargetLanguage`, `translate`, `translateMany`, `translateBulletinFields`) so `BulletinBoardController` keeps working; swap the free `translate_a/single` endpoint for `GoogleTranslateService`, swap `Cache::` for the DB table |
| `app/Http/Controllers/Api/TranslationController.php` | **new** — `POST /api/translate` `{ target, items:[{h,t}] }` → `{ h: translated }`. Validates target against `translate_languages`, throttled |
| `routes/web.php` | add route in the same group as the existing `translation-client-log` (line 504) |
| `app/Http/Middleware/ThrottleTranslate.php` | or use built-in `throttle:60,1` |

Success check: `php artisan tinker` → `GoogleTranslateService::translateBatch(['Hello','Welcome'],'es')` returns Spanish and writes 2 rows to `translation_cache`.

### Phase 2 — Client-side engine

`public/frontend_assets/js/lr-translate.js` (**new**, replaces the widget entirely):

- `LrTranslate.init({ endpoint, csrf, languages, active })`
- **Language intent** — reuse the proven parts of the current partial: `localStorage['lr_content_lang']` + `content_lang` cookie (server needs it for UGC/SSR paths). **Delete all `googtrans` handling** — that's the source of the cookie mess.
- **No page reload on switch.** Language change re-walks the DOM in place. Snapshot each node's original text in a `WeakMap` so `Original` restores instantly with zero network calls.
- **Walk:** `TreeWalker(NodeFilter.SHOW_TEXT)`
- **Skip rules (central, single source of truth):**
  - Ancestor tags: `script, style, code, pre, textarea, noscript, svg, i, iframe`
  - Ancestor class/attr: `.notranslate`, `[translate="no"]`, `[data-nt]`
  - Icon classes: `[class*="fa-"]`, `.material-icons`, `.material-symbols-outlined`, `.bi`, `[class*="icon"]`
  - Name selectors: existing list in `protect-names-from-translate.js` + `[data-person-name]`, `[data-user-name]`
  - Content regex: pure numbers, currency, emails, URLs, single chars, strings < 2 chars
- **Also translate:** `placeholder`, `title`, `aria-label`, `alt`, `value` on `input[type=submit|button]` — but only when the element is not name-protected
- **Batching:** ≤100 strings / ≤5000 chars per request, parallel ≤3 in flight
- **Two-tier client cache:** in-memory `Map` + `localStorage` keyed `tr:{lang}:{sha1}` with version stamp for invalidation
- **MutationObserver** on `document.body`, debounced 150ms, `childList+subtree+characterData`
- **FOUC control:** `html.lr-translating { }` + skeleton opacity on first uncached paint only; subsequent visits are cache-instant
- **Failure handling:** on API error → leave original text, log to the existing `translation-client-log` endpoint (keep `translation-diagnostics.js`)

### Phase 3 — Wire-up and removal

| File | Action |
|---|---|
| `resources/views/frontend/includes/google_translate.blade.php` | **replace body** with the ~30-line `LrTranslate.init()` bootstrap. Keeps the same filename so all four `@include`s stay valid |
| `resources/views/frontend/layouts/master.blade.php` | remove the dead `window.sessionLanguages` block at ~955–993 and the commented-out old widget; the partial sets it |
| `resources/views/user/includes/google_translate.blade.php` | **delete** — unreferenced dead file (190 lines) |
| `resources/views/frontend/includes/language_switcher.blade.php` | keep `Original` + language list; drop `googtrans` cookie parsing (lines 22–29); read intent from `content_lang` only |
| `public/frontend_assets/js/protect-names-from-translate.js` | keep and extend the selector lists |
| `public/frontend_assets/js/translation-diagnostics.js` | keep; adapt failure reasons to the new engine |

No change to: `admin/layouts/master.blade.php` (intentionally untranslated).

### Phase 4 — Names/icons codemod

1. Write `scripts/codemod-no-translate.php` (dry-run first, prints diff)
2. Run over the 142 files; review diff by area (`user/` 346 views is the bulk)
3. Extend `protect-names-from-translate.js` selectors for anything the codemod can't reach
4. Add `resources/views/components/nt.blade.php` (`<x-nt>{{ $user->full_name }}</x-nt>`)
5. Add a CI grep guard flagging new raw `{{ $*->full_name }}` renders

### Phase 5 — Warm-up & admin visibility

- `php artisan translate:warm {lang}` — crawls a route list, extracts strings, pre-populates `translation_cache` so real users never see FOUC on the top languages
- Admin page: cache size, chars used this month vs budget, per-language spend, top uncached strings, "clear cache for language X"

### Phase 6 — QA matrix

Test each on **website / user PMA / e-store / e-learning**:

- [ ] Switch language → content translates without reload
- [ ] Switch to `Original` → instant restore to source text, no network call
- [ ] Language persists across navigation and across the four surfaces
- [ ] Refresh keeps language; **session/login survives** (the old cookie-flood logout bug must not recur)
- [ ] Names never translated: profile, member lists, bulletin authors, admin tables, emails
- [ ] Icons never mangled (Font Awesome, Material ligatures)
- [ ] DataTables page-2 + search results get translated
- [ ] Modals, toasts, SweetAlert content get translated
- [ ] Form placeholders translated; name input values untouched
- [ ] RTL (Arabic, Hebrew, Urdu) renders correctly
- [ ] API failure → page stays readable in English, error logged
- [ ] Budget exceeded → cache-only mode, no crash
- [ ] Second visit is cache-instant (no visible FOUC)

---

## 7. Migration/rollback

- `TRANSLATE_ENABLED=false` in `.env` → engine no-ops, site renders English. Instant kill switch.
- Keep the old widget partial in git history; do **not** run both simultaneously (they will fight over the DOM).
- Deploy order: Phase 1 (backend, inert) → verify via tinker → Phase 2+3 behind `TRANSLATE_ENABLED` on staging → enable production.
- Clear stale cookies once on rollout: a small one-shot script expiring `googtrans` for returning visitors, removable after ~30 days.

---

## 8. Phase 7 — Flutter app (future, not now)

`lion-roaring-app` currently uses the unofficial free endpoint via the `translator` package.

| File | Future action |
|---|---|
| `lib/core/locale/translation_service.dart` | replace `GoogleTranslator()` with HTTP calls to the **same Laravel `/api/translate` endpoint** — shares the server cache, so mobile strings are mostly free, and the API key stays off-device |
| `lib/widget/t_text.dart`, `t_html.dart`, `translateable_widget.dart` | unchanged public API; only the backing service changes |
| `lib/core/locale/locale_controller.dart` | add an `Original` locale option to match web |
| — | add "never translate" handling for names/icons: a `NoTranslate` wrapper widget + skip rule in `TText` |

Reusing the Laravel endpoint is the whole point: one cache, one key, one budget, consistent translations between web and app.

---

## 9. Risks

| Risk | Mitigation |
|---|---|
| Cost overrun | DB cache + monthly budget + rate limit + usage dashboard |
| Public endpoint abused as free translation proxy | Auth-aware throttle, target-language allowlist, per-session char cap |
| FOUC on first uncached load | Warm-up command for top languages; `localStorage` makes repeat visits instant |
| Names still leaking through | Codemod + runtime selectors + CI guard; accept that this needs the one-time pass |
| MutationObserver perf on heavy DataTables pages | Debounce, batch, skip already-translated nodes via a `WeakSet` |
| `CACHE_DRIVER=file` | Explicitly **not used** for translations — dedicated DB table |
