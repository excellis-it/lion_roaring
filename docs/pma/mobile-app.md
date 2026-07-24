---
title: Mobile App
updated: 2026-07-24
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
| `pma/` | Messaging, chats, team, mail, bulletins, jobs, meetings, events+RSVP, private collaboration, partners, membership, education, strategy, policy, chatbot, agreement, notifications, profile |
| `ecom/` | Full E-Store: cart, wishlist, checkout, orders, addresses |
| `country/` | Region picker + languages |
| E-learning under PMA / services | Catalog **and** cart/checkout/library (unlike web public catalog) |

### Auth and membership

- OTP login (`/api/v3/login`, verify-otp), profile, FCM.
- Membership tiers, subscribe/renew/cancel via Stripe PaymentSheet.
- Post-login agreement navigation when required.
- Drawer items gated by permission APIs.

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

### Education country picker

- Uses `EducationCountryHelper` for SA / Global / G_R / on-GL rules similar to web `country_id` scoping.
