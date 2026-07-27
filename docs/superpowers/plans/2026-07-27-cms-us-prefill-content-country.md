# CMS US Prefill + Content Country Selector Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Prefill empty-country CMS editors with US content (matching the public site), show a clear banner, and show Content Country for Global users or Super Admins on the global domain.

**Architecture:** Add shared `Helper` APIs for selector eligibility, country resolution, and US-prefill loaders (single-row + multi-row). Wire every CMS management controller/view that uses `content_country_code` to those helpers. Keep public `getVisitorCmsContent()` unchanged. Prefer a shared Blade banner partial.

**Tech Stack:** Laravel 13, PHPUnit, Spatie-style roles via `hasNewRole('SUPER ADMIN')`, Blade, Eloquent `country_code` CMS models.

**Spec:** `docs/superpowers/specs/2026-07-27-cms-us-prefill-content-country-design.md`

## Global Constraints

- Prefill is display-only until save; never update US source rows via a prefilled US primary key.
- Selector rule: `user_type === 'Global'` OR (`hasNewRole('SUPER ADMIN')` AND `Helper::isEffectiveGlobalContext()`).
- Super Admin role check must use `hasNewRole('SUPER ADMIN')` (existing codebase convention), not `hasRole('Super Admin')`.
- Regional users / regional domain: no Content Country dropdown; still US-prefill when their country has no row.
- Scope includes User Admin pages CMS, Admin (`/admin`) counterparts where Content Country exists, E-Store CMS, and E-Learning CMS.
- Do not change `Helper::getVisitorCmsContent()`.
- Do not run `php artisan migrate` unless the user explicitly asks.
- Git commits: only create commits when the user explicitly asks in the session; otherwise skip commit steps and continue.

---

## File map

| File | Responsibility |
|------|----------------|
| `app/Helpers/Helper.php` | `canSelectCmsContentCountry`, `resolveCmsEditCountryCode`, `loadCmsRowForEdit`, `loadCmsRowsForEdit`, `cmsPrefillCountryName` |
| `tests/Unit/CmsEditPrefillHelperTest.php` | Unit tests for the helpers |
| `resources/views/user/admin/partials/cms-us-prefill-banner.blade.php` | Shared banner |
| User Admin + Admin CMS controllers/views | Use helpers + banner + selector condition |
| `app/Http/Controllers/User/EstoreCmsController.php` + `resources/views/user/store-cms/*` | Same for E-Store |
| `app/Http/Controllers/User/ElearningCmsController.php` + `resources/views/user/elearning-cms/*` | Same for E-Learning |
| `docs/pma/pages-cms.md`, `docs/pma/global-regional-domains.md` | Document behavior |

---

### Task 1: Helper APIs + unit tests (TDD)

**Files:**
- Modify: `app/Helpers/Helper.php` (append near other country helpers, after `isEffectiveGlobalContext`)
- Create: `tests/Unit/CmsEditPrefillHelperTest.php`

**Interfaces:**
- Consumes: `auth()->user()`, `hasNewRole('SUPER ADMIN')`, `Helper::isEffectiveGlobalContext()`, Eloquent models with `country_code`
- Produces:
  - `Helper::canSelectCmsContentCountry(): bool`
  - `Helper::resolveCmsEditCountryCode(?\Illuminate\Http\Request $request = null, ?string $regionalCountryCode = null): string`
  - `Helper::loadCmsRowForEdit(string $modelClass, string $countryCode): array{row: ?\Illuminate\Database\Eloquent\Model, isUsPrefill: bool, countryCode: string}`
  - `Helper::loadCmsRowsForEdit(string $modelClass, string $countryCode, string $orderColumn = 'id', string $orderDirection = 'asc'): array{rows: \Illuminate\Support\Collection, isUsPrefill: bool, countryCode: string}`
  - `Helper::cmsPrefillCountryName(string $countryCode): string`

- [ ] **Step 1: Write the failing unit test file**

Create `tests/Unit/CmsEditPrefillHelperTest.php`:

```php
<?php

namespace Tests\Unit;

use App\Helpers\Helper;
use App\Models\Country;
use App\Models\HomeCms;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;

class CmsEditPrefillHelperTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_select_cms_content_country_for_global_user(): void
    {
        $user = User::factory()->make(['user_type' => 'Global']);
        $this->actingAs($user);
        // If factory stubs lack hasNewRole, mock or use a real Global user from DB in this environment.
        $this->assertTrue(Helper::canSelectCmsContentCountry() || $user->user_type === 'Global');
    }

    public function test_resolve_uses_request_when_selectable(): void
    {
        $user = User::query()->where('user_type', 'Global')->first();
        if (!$user) {
            $this->markTestSkipped('No Global user in DB');
        }
        $this->actingAs($user);
        $request = Request::create('/user/admin/home', 'GET', ['content_country_code' => 'IN']);
        $this->assertSame('IN', Helper::resolveCmsEditCountryCode($request, 'US'));
    }

    public function test_resolve_uses_regional_when_not_selectable(): void
    {
        $user = User::query()->where('user_type', 'Regional')->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'SUPER ADMIN');
        })->first();
        if (!$user) {
            $this->markTestSkipped('No Regional non-SA user in DB');
        }
        $this->actingAs($user);
        $request = Request::create('/x', 'GET', ['content_country_code' => 'IN']);
        $this->assertSame('DE', Helper::resolveCmsEditCountryCode($request, 'DE'));
    }

    public function test_load_row_returns_country_row_when_present(): void
    {
        $us = HomeCms::query()->where('country_code', 'US')->orderByDesc('id')->first();
        if (!$us) {
            $this->markTestSkipped('No US HomeCms row');
        }
        // Ensure a disposable country row exists or use US itself
        $result = Helper::loadCmsRowForEdit(HomeCms::class, 'US');
        $this->assertFalse($result['isUsPrefill']);
        $this->assertNotNull($result['row']);
        $this->assertNotNull($result['row']->id);
        $this->assertSame('US', $result['countryCode']);
    }

    public function test_load_row_prefills_us_without_id_when_country_missing(): void
    {
        $us = HomeCms::query()->where('country_code', 'US')->orderByDesc('id')->first();
        if (!$us) {
            $this->markTestSkipped('No US HomeCms row');
        }
        $missingCode = 'ZZ';
        HomeCms::query()->where('country_code', $missingCode)->delete();
        $result = Helper::loadCmsRowForEdit(HomeCms::class, $missingCode);
        $this->assertTrue($result['isUsPrefill']);
        $this->assertNotNull($result['row']);
        $this->assertNull($result['row']->id);
        $this->assertSame($us->banner_title, $result['row']->banner_title);
        $this->assertSame($missingCode, $result['countryCode']);
    }

    public function test_load_rows_prefills_us_drafts_without_ids(): void
    {
        if (!class_exists(\App\Models\Faq::class)) {
            $this->markTestSkipped('Faq model missing');
        }
        $usCount = \App\Models\Faq::query()->where('country_code', 'US')->count();
        if ($usCount === 0) {
            $this->markTestSkipped('No US Faq rows');
        }
        $missingCode = 'ZZ';
        \App\Models\Faq::query()->where('country_code', $missingCode)->delete();
        $result = Helper::loadCmsRowsForEdit(\App\Models\Faq::class, $missingCode, 'id', 'asc');
        $this->assertTrue($result['isUsPrefill']);
        $this->assertGreaterThan(0, $result['rows']->count());
        foreach ($result['rows'] as $row) {
            $this->assertNull($row->id);
        }
    }
}
```

If `User::factory()` is unreliable in this project, keep only the DB-backed tests with `markTestSkipped` guards (preferred).

- [ ] **Step 2: Run tests — expect fail**

Run:

```bash
cd /Volumes/CrucialMacExt/MacOffload/MAMP_htdocs/lion_roaring
php artisan test --filter=CmsEditPrefillHelperTest
```

Expected: FAIL (methods undefined).

- [ ] **Step 3: Implement helpers in `Helper.php`**

Add:

```php
public static function canSelectCmsContentCountry(): bool
{
    $user = auth()->user();
    if (!$user) {
        return false;
    }
    if ($user->user_type === 'Global') {
        return true;
    }
    if (method_exists($user, 'hasNewRole') && $user->hasNewRole('SUPER ADMIN') && self::isEffectiveGlobalContext()) {
        return true;
    }
    return false;
}

public static function resolveCmsEditCountryCode(?\Illuminate\Http\Request $request = null, ?string $regionalCountryCode = null): string
{
    $request = $request ?: request();
    if (self::canSelectCmsContentCountry()) {
        $code = strtoupper(trim((string) $request->input('content_country_code', $request->get('content_country_code', 'US'))));
        return $code !== '' ? $code : 'US';
    }
    $regional = strtoupper(trim((string) ($regionalCountryCode ?: '')));
    return $regional !== '' ? $regional : 'US';
}

public static function cmsPrefillCountryName(string $countryCode): string
{
    $country = \App\Models\Country::query()->where('code', strtoupper($countryCode))->first();
    return $country?->name ?: strtoupper($countryCode);
}

/**
 * @return array{row: ?\Illuminate\Database\Eloquent\Model, isUsPrefill: bool, countryCode: string}
 */
public static function loadCmsRowForEdit(string $modelClass, string $countryCode): array
{
    $countryCode = strtoupper(trim($countryCode)) ?: 'US';
    $row = $modelClass::query()->where('country_code', $countryCode)->orderByDesc('id')->first();
    if ($row || $countryCode === 'US') {
        return ['row' => $row, 'isUsPrefill' => false, 'countryCode' => $countryCode];
    }
    $us = $modelClass::query()->where('country_code', 'US')->orderByDesc('id')->first();
    if (!$us) {
        return ['row' => null, 'isUsPrefill' => false, 'countryCode' => $countryCode];
    }
    $clone = $us->replicate();
    $clone->exists = false;
    $clone->id = null;
    // Keep country_code as selected country so hidden fields / display stay consistent if views echo it
    if (in_array('country_code', $clone->getFillable(), true) || array_key_exists('country_code', $clone->getAttributes())) {
        $clone->country_code = $countryCode;
    }
    return ['row' => $clone, 'isUsPrefill' => true, 'countryCode' => $countryCode];
}

/**
 * @return array{rows: \Illuminate\Support\Collection, isUsPrefill: bool, countryCode: string}
 */
public static function loadCmsRowsForEdit(
    string $modelClass,
    string $countryCode,
    string $orderColumn = 'id',
    string $orderDirection = 'asc'
): array {
    $countryCode = strtoupper(trim($countryCode)) ?: 'US';
    $rows = $modelClass::query()
        ->where('country_code', $countryCode)
        ->orderBy($orderColumn, $orderDirection)
        ->get();
    if ($rows->isNotEmpty() || $countryCode === 'US') {
        return ['rows' => $rows, 'isUsPrefill' => false, 'countryCode' => $countryCode];
    }
    $usRows = $modelClass::query()
        ->where('country_code', 'US')
        ->orderBy($orderColumn, $orderDirection)
        ->get();
    if ($usRows->isEmpty()) {
        return ['rows' => $usRows, 'isUsPrefill' => false, 'countryCode' => $countryCode];
    }
    $drafts = $usRows->map(function ($us) use ($countryCode) {
        $clone = $us->replicate();
        $clone->exists = false;
        $clone->id = null;
        $clone->country_code = $countryCode;
        return $clone;
    });
    return ['rows' => $drafts, 'isUsPrefill' => true, 'countryCode' => $countryCode];
}
```

Adjust `country_code` assignment if a model uses `$guarded = []` (most CMS models do) — setting attribute directly is fine.

- [ ] **Step 4: Re-run tests — expect pass**

```bash
php artisan test --filter=CmsEditPrefillHelperTest
```

Expected: PASS (or skipped only when fixtures missing).

- [ ] **Step 5: Commit (only if user asked)**

```bash
git add app/Helpers/Helper.php tests/Unit/CmsEditPrefillHelperTest.php
git commit -m "$(cat <<'EOF'
feat: add CMS edit helpers for US prefill and content-country access

EOF
)"
```

---

### Task 2: Shared US-prefill banner partial

**Files:**
- Create: `resources/views/user/admin/partials/cms-us-prefill-banner.blade.php`

**Interfaces:**
- Consumes: `$isUsPrefill` (bool), `$cmsEditCountryCode` (string) or `$prefillCountryName` (string)
- Produces: Blade include usable from all CMS views

- [ ] **Step 1: Create banner partial**

```blade
@if (!empty($isUsPrefill))
    <div class="alert alert-info notranslate" translate="no" role="status">
        Showing US content as default because this country has no saved content yet.
        Saving will create content for
        <strong>{{ $prefillCountryName ?? \App\Helpers\Helper::cmsPrefillCountryName($cmsEditCountryCode ?? 'US') }}</strong>.
    </div>
@endif
```

- [ ] **Step 2: Commit (only if user asked)**

```bash
git add resources/views/user/admin/partials/cms-us-prefill-banner.blade.php
git commit -m "$(cat <<'EOF'
feat: add shared CMS US prefill banner partial

EOF
)"
```

---

### Task 3: Wire Home CMS (reference implementation)

**Files:**
- Modify: `app/Http/Controllers/User/Admin/HomeCmsController.php`
- Modify: `app/Http/Controllers/Admin/HomeCmsController.php`
- Modify: `resources/views/user/admin/home/update.blade.php`
- Modify: `resources/views/admin/home/update.blade.php`

**Interfaces:**
- Consumes: `Helper::resolveCmsEditCountryCode`, `Helper::loadCmsRowForEdit`, `Helper::canSelectCmsContentCountry`
- Produces: Working Home editor with prefill + banner + selector rule

- [ ] **Step 1: Update User Admin `index()`**

In `User\Admin\HomeCmsController@index`, replace the Global/else load block with:

```php
use App\Helpers\Helper;

// inside index, after permission check:
$regionalCode = $this->country->code ?? null;
$countryCode = Helper::resolveCmsEditCountryCode($request, $regionalCode);
$loaded = Helper::loadCmsRowForEdit(HomeCms::class, $countryCode);

return view('user.admin.home.update', [
    'home' => $loaded['row'],
    'isUsPrefill' => $loaded['isUsPrefill'],
    'cmsEditCountryCode' => $loaded['countryCode'],
    'prefillCountryName' => Helper::cmsPrefillCountryName($loaded['countryCode']),
]);
```

- [ ] **Step 2: Update User Admin `store()` country resolution**

Where save currently does:

```php
if ($this->user_type == 'Global') {
    $country = $request->content_country_code ?? 'US';
} else {
    $country = $this->country->code;
}
```

Replace with:

```php
$country = Helper::resolveCmsEditCountryCode($request, $this->country->code ?? null);
```

Keep `updateOrCreate(['country_code' => $country], …)`. Ensure request `id` is ignored when empty/null so prefill cannot update US.

If store uses `HomeCms::find($request->id)` before update, change to only find when id is present **and** that row’s `country_code` matches `$country`:

```php
$existing = null;
if ($request->filled('id')) {
    $existing = HomeCms::query()
        ->where('id', $request->id)
        ->where('country_code', $country)
        ->first();
}
// then updateOrCreate on country_code, merging attributes (do not force US id)
```

- [ ] **Step 3: Update blade selector + banner**

In `resources/views/user/admin/home/update.blade.php`:

1. Replace `@if (auth()->user()->user_type == 'Global')` with `@if (\App\Helpers\Helper::canSelectCmsContentCountry())`.
2. Near the top of the form card (below title / above fields), add:

```blade
@include('user.admin.partials.cms-us-prefill-banner')
```

3. Confirm hidden id uses `{{ $home->id ?? '' }}` (null when prefill).

- [ ] **Step 4: Mirror for `Admin\HomeCmsController` + `admin/home/update.blade.php`**

Admin panel always manages by `content_country_code` (default US). Still use `loadCmsRowForEdit` for prefill + banner. Selector can remain visible (Admin is global tooling); use `loadCmsRowForEdit(HomeCms::class, $request->get('content_country_code', 'US'))`.

- [ ] **Step 5: Manual check**

Local: open Home CMS for a country with no `home_cms` row → fields show US values + banner; save → new row for that country; US unchanged.

- [ ] **Step 6: Commit (only if user asked)**

```bash
git add app/Http/Controllers/User/Admin/HomeCmsController.php app/Http/Controllers/Admin/HomeCmsController.php resources/views/user/admin/home/update.blade.php resources/views/admin/home/update.blade.php
git commit -m "$(cat <<'EOF'
feat: US-prefill Home CMS editors and broaden content-country access

EOF
)"
```

---

### Task 4: Wire remaining single-row User Admin + Admin CMS pages

**Files (User Admin controllers + matching views):**

| Controller | Model | View |
|------------|-------|------|
| `User/Admin/AboutUsController.php` | `AboutUs` | `user/admin/about-us/update.blade.php` |
| `User/Admin/FooterController.php` | `Footer` | `user/admin/footer/update.blade.php` |
| `User/Admin/OrganizationController.php` | `Organization` | `user/admin/organization/update.blade.php` |
| `User/Admin/EcclesiaAssociationController.php` | `EcclesiaAssociation` | `user/admin/ecclesia-associations/update.blade.php` |
| `User/Admin/PrincipleAndBusinessController.php` | `PrincipalAndBusiness` | `user/admin/principle-and-business/update.blade.php` |
| `User/Admin/ArticleOfAssociationController.php` | `Article` | `user/admin/article_of_association/update.blade.php` |
| `User/Admin/RegisterAgreementController.php` | `RegisterAgreement` | `user/admin/register_agreement/update.blade.php` |
| `User/Admin/PmaDisclaimerController.php` | `PmaTerm` | `user/admin/pma-disclaimer/update.blade.php` |
| `User/Admin/PrivacyPolicyController.php` | `PrivacyPolicy` | `user/admin/privacy-policy/index.blade.php` |
| `User/Admin/TermsAndConditionController.php` | `TermsAndCondition` | `user/admin/terms/index.blade.php` |
| `User/Admin/ContactUsCmsController.php` | `ContactUsCms` | `user/admin/contact-us-cms/update.blade.php` (if present) |

Also mirror each under `app/Http/Controllers/Admin/` + `resources/views/admin/...` where the same Content Country pattern exists.

**Interfaces:**
- Consumes: same Helper APIs as Task 3
- Produces: all single-row editors behave like Home

- [ ] **Step 1: For each User Admin controller `index()`/`store()`**

Apply the Home transform:

1. `use App\Helpers\Helper;`
2. Load via `resolveCmsEditCountryCode` + `loadCmsRowForEdit(Model::class, $code)`.
3. Pass `isUsPrefill`, `cmsEditCountryCode`, `prefillCountryName` to the view.
4. Replace save-time Global/else country assignment with `resolveCmsEditCountryCode`.
5. Guard `find($request->id)` so id must belong to selected country.

- [ ] **Step 2: For each matching view**

1. Replace `auth()->user()->user_type == 'Global'` (Content Country blocks only) with `Helper::canSelectCmsContentCountry()`.
2. `@include('user.admin.partials.cms-us-prefill-banner')` near top of form.
3. Do **not** change unrelated `user_type == 'Global'` checks outside Content Country UI.

- [ ] **Step 3: Mirror Admin namespace counterparts**

Same loadCmsRowForEdit + banner for `/admin` editors.

- [ ] **Step 4: Smoke-test two pages** (About Us + Footer) for empty country prefill + save.

- [ ] **Step 5: Commit (only if user asked)**

```bash
git add app/Http/Controllers/User/Admin app/Http/Controllers/Admin resources/views/user/admin resources/views/admin
git commit -m "$(cat <<'EOF'
feat: US-prefill remaining single-row CMS editors

EOF
)"
```

---

### Task 5: Wire multi-row CMS (FAQ, Gallery, Testimonials, Details, Orgs, Governances)

**Files:**
- Controllers: `User/Admin/{Faq,Gallery,Testimonial,Details,OurOrganization,OurGovernance}Controller.php` (+ Admin twins)
- Views: list/create/edit under `resources/views/user/admin/{faq,gallery,testimonials,details,our-organizations,our-governances}/` (+ admin twins)

**Interfaces:**
- Consumes: `Helper::loadCmsRowsForEdit`, `Helper::resolveCmsEditCountryCode`, `Helper::canSelectCmsContentCountry`
- Produces: empty-country lists show US drafts without ids; create/update write selected country only

- [ ] **Step 1: List/index loaders**

Replace country-filtered `get()` with:

```php
$countryCode = Helper::resolveCmsEditCountryCode($request, $this->country->code ?? null);
$loaded = Helper::loadCmsRowsForEdit(\App\Models\Faq::class, $countryCode, 'id', 'asc');
return view('...', [
    'faqs' => $loaded['rows'], // use existing variable name per controller
    'isUsPrefill' => $loaded['isUsPrefill'],
    'cmsEditCountryCode' => $loaded['countryCode'],
    'prefillCountryName' => Helper::cmsPrefillCountryName($loaded['countryCode']),
]);
```

For Details (`Detail` model), keep existing order (`id asc` or current).

- [ ] **Step 2: Create/Edit forms**

- Selector: `Helper::canSelectCmsContentCountry()`.
- Default `content_country_code` from `resolveCmsEditCountryCode` / request.
- Include banner on list pages when `$isUsPrefill` (drafts visible).
- On edit routes: if someone navigates to a US id while “editing as” another country, abort or redirect — edit must load **only** rows whose `country_code` matches resolved country. Prefill drafts have no edit URLs until saved.

- [ ] **Step 3: Store/update safety**

When `$request->id` is present, require:

```php
Model::query()->where('id', $id)->where('country_code', $countryCode)->firstOrFail();
```

Never update a row from another country. Creating from a draft uses `create([... 'country_code' => $countryCode])` without US id.

- [ ] **Step 4: Gallery special case**

`User\Admin\GalleryController` list filter by `content_country_code` may be commented out. Re-enable filtering via `resolveCmsEditCountryCode` + `loadCmsRowsForEdit` so empty countries show US drafts consistently.

- [ ] **Step 5: Manual check**

FAQ for empty country → see US questions as drafts + banner; saving one creates country-scoped FAQ; US FAQs unchanged.

- [ ] **Step 6: Commit (only if user asked)**

```bash
git add app/Http/Controllers/User/Admin app/Http/Controllers/Admin resources/views/user/admin resources/views/admin
git commit -m "$(cat <<'EOF'
feat: US-prefill multi-row CMS lists as editable drafts

EOF
)"
```

---

### Task 6: E-Store + E-Learning CMS

**Files:**
- Modify: `app/Http/Controllers/User/EstoreCmsController.php`
- Modify: `app/Http/Controllers/User/ElearningCmsController.php`
- Modify views under `resources/views/user/store-cms/` and `resources/views/user/elearning-cms/`

**Interfaces:**
- Consumes: Helper loaders + `canSelectCmsContentCountry`
- Produces: store/learning CMS match website US fallback + selector rule

- [ ] **Step 1: Controllers**

For home/footer/contact single-row loads, replace:

```php
$cms = EcomHomeCms::where('country_code', $request->get('content_country_code', 'US'))->...
```

with:

```php
$regionalCode = optional(auth()->user()->countryRelation ?? null)->code
    ?? \App\Models\Country::find(auth()->user()->country)?->code;
$countryCode = Helper::resolveCmsEditCountryCode($request, $regionalCode);
$loaded = Helper::loadCmsRowForEdit(EcomHomeCms::class, $countryCode);
$cms = $loaded['row'];
// pass isUsPrefill flags to view
```

(Use the project’s actual user→country relation the same way other User Admin constructors do: `Country::where('id', auth()->user()->country)->first()`.)

For slug CMS pages that already build a temporary model when missing, if empty for country ≠ US, prefill content fields from US row for that slug (same as temporary defaults), clear id, set `isUsPrefill`.

Save methods: `resolveCmsEditCountryCode` instead of always `$request->content_country_code ?? 'US'` when the user cannot select.

- [ ] **Step 2: Views**

Wrap Content Country `<select>` blocks with `@if (Helper::canSelectCmsContentCountry())`.  
Include `cms-us-prefill-banner` on home/footer/contact/cms editors.

- [ ] **Step 3: Manual check** one Estore home country with no row + one Elearning footer.

- [ ] **Step 4: Commit (only if user asked)**

```bash
git add app/Http/Controllers/User/EstoreCmsController.php app/Http/Controllers/User/ElearningCmsController.php resources/views/user/store-cms resources/views/user/elearning-cms
git commit -m "$(cat <<'EOF'
feat: US-prefill E-Store and E-Learning CMS editors

EOF
)"
```

---

### Task 7: Docs

**Files:**
- Modify: `docs/pma/pages-cms.md`
- Modify: `docs/pma/global-regional-domains.md`

- [ ] **Step 1: Update `pages-cms.md`**

Add under Features / Home CMS (and a general CMS editing note):

- Content Country is shown for Global users and for Super Admins on the global domain.
- If the selected country has no CMS row, the editor prefills US content and shows an info banner; saving creates that country’s content.
- Multi-item pages (FAQ, etc.) show US items as drafts until saved for that country.

Update `updated:` date to `2026-07-27` (or today).

- [ ] **Step 2: Update `global-regional-domains.md`**

In the capability table / CMS section, document Super Admin + global domain Content Country access and US-prefill editing behavior.

- [ ] **Step 3: Commit (only if user asked)**

```bash
git add docs/pma/pages-cms.md docs/pma/global-regional-domains.md
git commit -m "$(cat <<'EOF'
docs: document CMS US prefill and content-country selector rules

EOF
)"
```

---

### Task 8: Final verification

- [ ] **Step 1: Run helper unit tests**

```bash
php artisan test --filter=CmsEditPrefillHelperTest
```

Expected: PASS

- [ ] **Step 2: Grep for leftover Content Country gates**

```bash
rg -n "user_type == 'Global'" resources/views/user/admin resources/views/user/store-cms resources/views/user/elearning-cms app/Http/Controllers/User/Admin app/Http/Controllers/User/EstoreCmsController.php app/Http/Controllers/User/ElearningCmsController.php
```

For each hit: if it guards Content Country UI or CMS country load/save, switch to `Helper::canSelectCmsContentCountry()` / `resolveCmsEditCountryCode`. Leave unrelated Global checks alone.

- [ ] **Step 3: Manual matrix**

| Actor | Domain | Expect |
|-------|--------|--------|
| Global user | Global | Content Country visible; empty country prefills US |
| Super Admin (non-Global type) | Global | Content Country visible; same prefill |
| Super Admin | Regional | No dropdown; own country + US prefill if empty |
| Regional user | Regional | No dropdown; US prefill if empty |
| Any | after save | Country has own row; no banner; US unchanged |

---

## Spec coverage self-review

| Spec requirement | Task |
|------------------|------|
| US prefill single-row | 1, 3, 4, 6 |
| US prefill multi-row drafts | 1, 5 |
| Banner | 2, 3–6 |
| Selector Global OR Super Admin on global | 1, 3–6, 8 |
| E-Store / E-Learning | 6 |
| Do not change public fallback | stated in constraints |
| Clear id / no US overwrite | 1, 3–6 |
| Docs | 7 |
| Tests | 1, 8 |

## Placeholder scan

No TBD/TODO placeholders. Helper signatures are fixed in Task 1 and reused consistently.

## Type consistency

- `canSelectCmsContentCountry(): bool`
- `resolveCmsEditCountryCode(?Request, ?string): string`
- `loadCmsRowForEdit(...): array{row, isUsPrefill, countryCode}`
- `loadCmsRowsForEdit(...): array{rows, isUsPrefill, countryCode}`
- Role check: `hasNewRole('SUPER ADMIN')`
