---
title: User PMA
updated: 2026-08-03
status: ready
sidebar_key: user_pma
---

# User PMA

## Overview

Authenticated user panel under `/user/*`. Includes Messaging → Chatbot sidebar areas, membership, Restore (Super Admin), Admin Portal CMS, and this Documentation UI.

**Layout:** `resources/views/user/layouts/master.blade.php`  
**Middleware (typical):** `user`, `preventBackHistory`, `userActivity`, `member.access`, `agreement.signed`  
**Plus global:** `EnsureCanonicalCountryUrl`, `EnsureUserInstanceAccess`, active-user checks

Open **Detailed topics** on the index for each sidebar menu. Shared instance rules: **Global & Regional Domains**.

## Features

### Panel-wide gates

| Middleware | Rule |
|------------|------|
| `user` | Logged in and `status == 1` |
| `member.access` | Non-expired subscription unless Super Admin or `membership_excluded` |
| `agreement.signed` | Signature + register agreement row unless Super Admin |
| `super_admin` | Restore + Documentation only |

### Language / names

- User layout boots the same LrTranslate engine as the public site (`frontend.includes.google_translate`). Language switcher may live on the public header; intent still follows `content_lang` / `lr_content_lang` across surfaces.
- A temporary header badge shows a blinking **Translating…** indicator while a pass runs (slot: `[data-lr-translate-badge]`).
- Person names and usernames shown in the panel are excluded from translation via `no_translate()` and `protect-names-from-translate.js` (see Website Frontend).
- Translation failure popup + client logging use the same diagnostics; surface tag in logs is `user_pma`. No per-user character quota stops translation.

### Major menu groups

1. **Messaging** — Chats, Team, Mail (`Manage Chat|Team|Email`)
2. **Education** — Topics, becoming tracks, Files (SA can see parent without gates)
3. **Bulletins** — Board, jobs, meetings, events, private collaboration
4. **E-Store / Warehouse / E-Learning admin** — see E-Store and E-Learning hubs
5. **Roles, Membership, Partners, Activity, Signup Rules**
6. **Strategy / Policy** — country-scoped document libraries
7. **Membership self-service** — if `membershipPanelApplicable()`
8. **Restore** — Super Admin recycle bin
9. **Admin Portal** — Donations, Newsletters, Testimonials, Orgs, Pages CMS, Countries, Settings, Super Admin list, Chatbot
10. **Support Reports** — All users: submit/view own reports. `Manage Support Reports` permission **or Super Admin**: view all reports, update status, add admin notes. Email notifications sent on submit and status update.
11. **Change Logs** — All users: read published release notes. `Manage Change Logs` permission **or Super Admin**: create, edit, delete entries.

### Documentation UI

- Last sidebar item; Super Admin only.
- Markdown under `docs/pma/`; agents must update docs when features change.
- Index hubs include product surfaces (1–5) plus **Chatbot Guides**, **Improvement Performance Report**, **Automation Implementations**, and **E-Store & E-Learning Enhancement** (6–9).

## Permissions and conditions

### Global users in PMA

- May use Global domain only (not regional hosts).
- CMS editors get `content_country_code` picker (default US).
- Education/strategy on global server scope to **GL** `country_id`.
- Partners list: Global + G_R visibility patterns.

### Regional users in PMA

- Only assigned country instance; wrong host → logout.
- CMS locked to own country code.
- Education/files scoped to own country; ecclesia admins may further filter.

### G_R users

- Allowed on Global **or** assigned regional (not other regionals).
- On global server: often treated like Global for education GL scope.
- On regional: own-country scope.

### Super Admin

- Bypasses membership, agreement, and instance middleware.
- Unscoped lists in many controllers; can pick `country_id` / content country.
- Only role that sees Restore + Documentation.

### Spatie vs user_type

- Sidebar uses Spatie `Gate::check('Manage …')`.
- Instance access uses string `users.user_type` + Super Admin `hasNewRole`.
- Role Permission UI manages `user_types` / `user_type_permissions` with type hierarchy (SA type 1 manages 2&3).