---
title: Website Frontend
updated: 2026-07-28
status: ready
sidebar_key: website_frontend
---

# Website Frontend

## Overview

Public marketing site at `/` (and `/{cc}` on the default regional host). No login required for browsing. Content is **country-aware** via visitor country helpers with **US fallback**. Edited from PMA Admin Portal (Pages, Donations, Organizations, …).

**Layout:** `resources/views/frontend/layouts/master.blade.php`  
**Controllers:** `Frontend\CmsController`, `Frontend\DonationController`, `Frontend\MembershipController`  
**Public middleware group:** `userActivity` (plus global country/instance middleware)

Deep domain rules: see **Global & Regional Domains**.

## Features

### Home and country routing

- `home` (`/`), `home.country` (`/{cc}`).
- Domain/path resolution via `Country`, `Helper::isUsaInstance()`, `Helper::isGlobalInstance()`.
- Global root never treats regional path codes as content context; regional sessions on global root are redirected.

### Marketing pages

- Gallery, FAQ, Contact, About, Details, Principle and Business, Ecclesia Covenant.
- Org hierarchy: Our Organization → Organization Centers (`features/{slug}`) → Services (`service/{slug}`).
- Our Governance, Terms, Privacy Policy.
- Membership marketing page (`/membership`) — purchase/manage under `/user/membership`.

### Forms and donations

- Newsletter POST → site contact email.
- Contact form requires **reCAPTCHA**.
- Donations: Stripe Charge (USD), guests allowed → thank-you page.

### Language / Google Website Translator

- Header language switcher drives Google Website Translator (`frontend.includes.google_translate`).
- **Person names and usernames are never machine-translated.** Displays use `no_translate()` (`Helper::noTranslate`) → `<span class="notranslate" translate="no">…</span>`. Name inputs and known name UI nodes (e.g. `.GroupName`) are also marked via `protect-names-from-translate.js`.

### Chatbot

- `CHATBOT=AI` → RAG widget (`RAG_*` env); else in-app chatbot.
- Mobile: same `CHATBOT` + `MOBILE_CHATBOT_URL` via `/api/v3/cms/site-settings` (`chatbot_mode`, `mobile_chatbot_url`).
- Routes under `/chatbot/*`.

## Permissions and conditions

### Global domain rules (public)

- Visitor on Global: session country if set; else empty → CMS falls back to **US**.
- No IP auto-detect on main/global.

### Regional domain rules (public)

- Root → that country (e.g. US).
- `/{code}` → that regional country when canonical.
- Path code wins over session when present.
- Default regional host may serve multi-country path codes.

### CMS visibility

- `Helper::getVisitorCmsContent()` loads rows for visitor `country_code`; empty result → US.
- Editors use PMA Pages CMS with `content_country_code` (Global editors pick country; Regional locked to own).

### Related PMA menus

Pages (CMS), Donations, Newsletters, Testimonials, Our Governance, Our Organizations, Organization Center, Services, Countries, Site Settings, Chatbot.