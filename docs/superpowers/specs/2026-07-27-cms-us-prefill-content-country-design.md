# CMS US Prefill + Content Country Selector — Design

**Date:** 2026-07-27  
**Status:** Approved for planning  
**Branch context:** `laravel-13-new`

## Problem

1. Public website already falls back to **US** CMS content when a visitor’s country has no row (`Helper::getVisitorCmsContent()`). Admin/PMA CMS edit forms do **not**: selecting a country with no data shows empty fields, which is confusing and hard to manage.
2. **Content Country** dropdown (and matching load/save) is gated only by `user_type == 'Global'`. Super Admins on the **global domain** who are not `user_type = Global` cannot select content country even though they manage global CMS.

## Goals

- When editing CMS for a country with **no saved data**, show **US** content as the form defaults (same as the public site).
- When that country **has** data, show that country’s data.
- Saving always creates/updates the **selected** country’s rows — never accidentally overwrite US source rows.
- Show Content Country when the user is **Global**, or **Super Admin on a global domain/context**.
- Cover **all** CMS surfaces with Content Country, including E-Store and E-Learning CMS.
- For multi-item lists with no country data: show US items as **editable drafts**; saving creates copies for the selected country.
- Show a clear banner when US prefill is active.

## Non-goals

- Changing public-site fallback behavior (`getVisitorCmsContent`) — already correct.
- Auto-copying US rows into a country on open (no silent DB writes).
- Changing Regional-domain users to see a country dropdown.

## Decisions (approved)

| Topic | Choice |
|-------|--------|
| Scope | Everything with Content Country, including E-Store / E-Learning CMS |
| Multi-row empty country | Show US items as editable drafts; save creates selected-country copies |
| Prefill notice | Banner when US prefill is active |
| Architecture | Shared Helper loaders (Approach A) |
| Country selector | `user_type === 'Global'` **OR** (Super Admin **and** effective global context) |

## Behavior

### Resolve edit country code

- If `canSelectCmsContentCountry()` → use `request('content_country_code', 'US')` (or form POST on save).
- Else → regional user’s country code (existing `$this->country->code` pattern), fallback `US` if missing.

### Single-row CMS (Home, About, Footer, Contact, Privacy, Terms, Organization, Ecclesia, Principle & Business, Articles, Register Agreement, PMA Terms, plus E-Store/E-Learning single-row CMS)

1. Load row for resolved country code (`orderBy id desc` / existing query).
2. If found → use it; `$isUsPrefill = false`.
3. If missing and country ≠ `US` → load US row; if found, return a **display clone with no primary key** (`replicate()` / clear `id`); `$isUsPrefill = true`.
4. If no US row → empty form; `$isUsPrefill = false`.
5. Save paths keep existing `updateOrCreate(['country_code' => $selected], …)` (or equivalent). Prefill must **not** pass US `id` into hidden `id` fields.

### Multi-row CMS (FAQ, Gallery, Testimonials, Details, Our Organizations, Our Governances, and similar list CMS including store/learning lists if applicable)

1. If selected country has rows → show those; `$isUsPrefill = false`.
2. If empty and ≠ `US` → load US rows as drafts with cleared ids / not bound to US PKs; `$isUsPrefill = true`.
3. Create/update/delete must operate on the **selected country** only. Draft edits must not `update` US source rows by id.

### Banner

When `$isUsPrefill` is true, show:

> Showing US content as default because this country has no saved content yet. Saving will create content for **[Country Name]**.

Prefer a small shared Blade partial included on CMS management views.

### Content Country selector visibility

Replace `auth()->user()->user_type == 'Global'` (for Content Country UI and matching controller branches) with:

```text
Helper::canSelectCmsContentCountry()
  ≡ user_type === 'Global'
    OR (has Super Admin role AND Helper::isEffectiveGlobalContext())
```

Controllers that today branch load/save on `$this->user_type == 'Global'` must use the same helper so Super Admins on global domain both **see** the dropdown and **persist** via `content_country_code`.

Regional domain / Regional users: no dropdown; stay country-scoped (with US prefill when empty).

## Technical design

### New Helper APIs (`app/Helpers/Helper.php`)

| Method | Responsibility |
|--------|----------------|
| `canSelectCmsContentCountry(): bool` | Global user or Super Admin in effective global context |
| `resolveCmsEditCountryCode(?Request $request = null): string` | Selected content country or regional/default US |
| `loadCmsRowForEdit(string $modelClass, ?string $countryCode = null): array` | Returns `['row' => ?Model, 'isUsPrefill' => bool, 'countryCode' => string]` |
| `loadCmsRowsForEdit(string $modelClass, ?string $countryCode = null, ...): array` | Same for collections; drafts have no ids |

Exact signatures may be adjusted in the implementation plan; behavior above is normative.

### Controllers / views to wire

- All **User\Admin** (+ **Admin** where applicable) CMS pages that use Content Country / `content_country_code`.
- **User\EstoreCmsController** and **User\ElearningCmsController** (+ their views).
- Blade conditions for the country `<select>` and any Global-only load branches.

### Docs

- Update `docs/pma/pages-cms.md`
- Update `docs/pma/global-regional-domains.md` (CMS editing / Super Admin on global)

### Tests

- Unit: `canSelectCmsContentCountry` matrix (Global; Super Admin + global context; Super Admin + regional context; Regional).
- Unit: `loadCmsRowForEdit` / `loadCmsRowsForEdit` — missing country returns US clone without id; existing country returns own row; US country does not double-fallback.
- Optional feature test: save after prefill creates selected-country row and leaves US unchanged.

## Risks / edge cases

- **Hidden `id`:** Must clear US primary key on prefill or `find($id)` / merge can corrupt US or fail uniqueness.
- **File/image fields:** Prefill may show US image paths; saving without re-upload should copy path values onto the new country row (existing updateOrCreate attribute merge) — document in plan; do not delete US files.
- **Gallery list filter:** Some list filters are partially commented; plan should align list filtering with `resolveCmsEditCountryCode` where Content Country is shown.
- **G_R users:** Not given selector unless they are Super Admin on global context or `user_type === 'Global'`. No change to G_R regional scoping beyond US prefill when their country row is empty.

## Success criteria

- Opening CMS for a country with no data shows US field values + banner.
- Saving creates/updates that country’s content; US row unchanged.
- Re-opening that country shows its saved data (no banner).
- Super Admin on global domain sees Content Country and can manage any country’s CMS.
- Regional users still do not see the dropdown; empty country still prefills from US.
