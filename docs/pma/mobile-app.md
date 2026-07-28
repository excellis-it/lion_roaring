---
title: Mobile App
updated: 2026-07-28
status: ready
sidebar_key: mobile_app
---

# Mobile App

## Overview

Flutter app at `lion-roaring-app`. Talks to Laravel **`/api/v3`**. Country selection switches API host and mirrors web instance access rules.

**Entry:** `lib/main.dart`  
**API paths:** `lib/constant/api_path.dart`  
**Instance helper:** `lib/core/auth/instance_access_helper.dart`

## Features

### Country / host switch

| Selected code | API host |
|---------------|----------|
| `US` | `https://lionroaring.us` |
| `GL` and others | `https://lionroaring.org` |

- First launch: Global vs Regional → regional picker (default US).
- Persisted in GetStorage (`selectedCountryCode` / name).
- Home chip to change selection; no sync with web session cookies.
- Login validates `country_code` with `Helper::userCanAccessCountryContext` on API.

### Feature modules (`lib/features/`)

| Module | Contents |
|--------|----------|
| `frontend/` | Public: home, gallery, FAQ, auth, donate, orgs |
| `pma/` | Messaging, chats, team, mail, bulletins, jobs, meetings, events+RSVP, private collaboration, partners, membership, education, strategy, policy, support reports, change logs, chatbot, agreement, notifications, profile |
| `ecom/` | Full E-Store: cart, wishlist, checkout, orders, addresses |
| `country/` | Region picker + languages |
| E-learning under PMA / services | Catalog **and** cart/checkout/library (unlike web public catalog) |

### Auth and membership

- OTP login (`/api/v3/login`, verify-otp), profile, FCM.
- Membership tiers, subscribe/renew/cancel via Stripe PaymentSheet.
- Post-login agreement navigation when required.
- Drawer items gated by permission APIs.

### Support Reports & Change Logs (member)

- Drawer top-level: **Support Reports**, **Change Logs** (always listed; no management UI in the app).
- APIs: `GET/POST /api/v3/user/support-reports`, `GET /api/v3/user/support-reports/{id}`, `GET /api/v3/user/change-logs?platform=web|mobile`.
- If those routes are missing (HTTP **404**), the screen shows **Coming soon** — so the app can ship against production before the API is deployed; once the same API code reaches production, features work without an app update.
- Member parity with web (list/create/view reports with optional attachment; published change logs with Web/Mobile tabs and current version chip).

### Chatbot sidebar

- Label **Chatbot** with chat icon.
- Reads `chatbot_mode` + `mobile_chatbot_url` from `/api/v3/cms/site-settings` (env `CHATBOT`, `MOBILE_CHATBOT_URL` — no admin DB field).
- `CHATBOT=AI` + non-empty URL → JS WebView with AppBar/back.
- Otherwise → existing in-app chat assistant.

### Realtime

- Socket host configured for US (`lionroaring.us:3000` in constants).
- Chat FCM + socket services.

## Permissions and conditions

### Instance parity with web

| user_type | Allowed country context |
|-----------|-------------------------|
| Super Admin | Any |
| Global | Global (`GL`) only |
| Regional | Assigned country only |
| G_R | Global or assigned regional |

Wrong selection → restriction message / blocked API (see `InstanceAccessHelper`).

### Important web vs mobile differences

| Topic | Web | Mobile |
|-------|-----|--------|
| E-Learning commerce | Catalog only (no cart) | Cart + checkout + purchases |
| E-Store | Membership + agreement required | Same APIs; membership enforced server-side |
| Country handoff | Domains + `?cc=` | Local storage host switch |
| Documentation UI | Super Admin `/user/documentation` | Not in app |
| Support Reports / Change Logs manage | Permission-gated manage UI | Not in app (member only) |

### Education country picker

- Uses `EducationCountryHelper` for SA / Global / G_R / on-GL rules similar to web `country_id` scoping.
