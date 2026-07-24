---
title: Global & Regional Domains
updated: 2026-07-24
status: ready
sidebar_key: domains
---

# Global & Regional Domains

## Overview

Lion Roaring is one Laravel app served on multiple country domains. Access and CMS content depend on:

- The **country** record (`is_global`, `code`, `domain`)
- The user’s **`user_type`** string: `Global`, `Regional`, or `G_R`
- Super Admin via `hasNewRole('SUPER ADMIN')` (bypasses instance checks)
- Env fallbacks: `MAIN_URL` (global), `LION_ROARING_USA` (default regional)

**Primary code:** `app/Models/Country.php`, `app/Helpers/Helper.php`, `EnsureCanonicalCountryUrl`, `EnsureUserInstanceAccess`

## Features

### Country records

| Field | Rule |
|-------|------|
| `is_global` | Exactly one Global country; code **`GL`**; display “Global (Main)” |
| `domain` | Full base URL for that instance |
| `code` | ISO-like; path countries use lowercase codes on the default regional host |
| `status` | Active/inactive |

- Global country **cannot** be deleted or status-toggled (`CountryController`).
- `Helper::getCountries()` for public dropdowns **excludes** `is_global` rows.

### Domain map

| Concept | Resolution |
|---------|------------|
| Global / main | `Country::getGlobalDomain()` or `env('MAIN_URL')` / `APP_URL` |
| Default regional (US) | US country domain or `env('LION_ROARING_USA')` |
| Other regionals | Own `domain`, or `{defaultRegionalUrl}/{code}` (e.g. `…/in`) |

**Effective country on request:**

- **Global domain:** always Global context; does **not** host regional path codes as content context.
- **Regional/US domain:** root → that country (US); `/{code}` → that regional; session may apply when no path code.
- Dedicated country domain → that country.

### Middleware

#### `EnsureCanonicalCountryUrl` (global web)

1. Consume `?cc=` handoff, then strip it.
2. Global root + regional session → redirect to regional URL.
3. Regional root + GL session → redirect to main; other codes → their URL.
4. Path country → canonical host/path.
5. Skips `api/*`, `admin/*`, `storage/*`, `set-visitor-country`.

#### `EnsureUserInstanceAccess` (global web)

- Guests pass; Super Admin passes.
- Else if session/host/country invalid for `user_type` → **logout**, clear browsing session, redirect with `instance_error` (+ `?cc=`).
- Skips login/logout/OTP/register/admin auth paths.

### User type access matrix

| Capability | Super Admin | Global | G_R on Global | G_R on Regional | Regional |
|------------|:-----------:|:------:|:-------------:|:---------------:|:--------:|
| Use Global domain | Yes | Yes | Yes | No* | No |
| Use assigned regional | Yes | No | Yes (assigned) | Yes | Assigned only |
| Other regional country | Yes | No | No | No | No |
| CMS country picker | Typically yes | Yes | Module-dependent | Own country | Own country |
| Education list on global server | Unscoped | GL scope | GL scope | Own country | Own country |

\* Wrong instance → logout + redirect (`EnsureUserInstanceAccess`).

**Registration:** Global domain → `user_type = Global`; non-global → `Regional`. Admin/partner roles with `is_admin = 1` often force **`G_R`**.

### Visitor country & public CMS

- Session keys via `Helper::setVisitorCountrySession` / `getVisitorCountryCode`.
- **US/default regional:** path code wins; else session; else auto **US**.
- **MAIN/Global:** session if set; else empty (no IP geo auto-detect) → CMS falls back to **US**.
- `Helper::getVisitorCmsContent()`: visitor code → empty → **US** fallback.
- Cross-domain handoff: `?cc=GL|us|in` consumed then stripped.

### CMS editing: `content_country_code` vs `country_id`

| Pattern | Who | Behavior |
|---------|-----|----------|
| `content_country_code` (Pages, FAQ, Gallery, Estore/Elearning CMS, …) | `user_type == Global` | Dropdown; default **US**; pick any regional content |
| Same | Regional | Locked to own country code |
| Education / Strategy / Files / Meetings (`country_id`) | Global or `G_R` on global server | Scope to **GL** country |
| Same | Regional / G_R on regional | Own `user.country` |
| API Super Admin | — | Must pass `country_id` (`AppliesPmaCountryFromRequest`) |

**Important:** Public CMS rows are usually **regional codes (US, IN, …)**, not `GL`. Global editors edit country-specific content while logged into the Global domain.

### Surfaces that inherit these rules

| Surface | Bound to domain? | Content source |
|---------|------------------|----------------|
| `/` Website | Yes | Visitor CMS → US fallback |
| `/user` PMA | Yes (instance access) | Editor country / `content_country_code` / education `country_id` |
| `/e-store` | Yes | Visitor CMS + warehouse geo; membership required |
| `/e-learning` | Yes | Visitor CMS; **no** membership middleware on public catalog |
| Flutter | Logical country → host | `US` → lionroaring.us; `GL`/others → lionroaring.org; APIs `/api/v3` |

## Permissions and conditions

- Managing countries requires `Manage Countries`.
- Instance violations never silently show wrong-country data — users are logged out.
- Header badge: `Helper::getDisplayCountry()` (`badge-global` / `badge-regional`).