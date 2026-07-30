---
title: Website Frontend
updated: 2026-07-30
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
- Donations: Stripe Charge (USD) via Stripe Elements card form (not raw card fields), guests allowed → thank-you page.

### Language / Cloud Translation

- Header language switcher drives **LrTranslate** (`frontend.includes.google_translate` → `lr-translate.js` → `POST /translate/batch`), backed by Google Cloud Translation with a permanent DB cache (`translation_cache`).
- Language intent is stored in `localStorage` (`lr_content_lang`) and the `content_lang` cookie. Switching language does **not** reload the page. **Original** (`__original__`) restores source text instantly with no API calls.
- There is **no per-user or monthly character quota** that stops translation (`TRANSLATE_*_CHAR_LIMIT=0`). Cost control is cache reuse; usage is still logged in `translation_usage`.
- While a page is translating, a temporary header badge shows a blinking **Translating…** indicator (no percentage). Badge mounts into `[data-lr-translate-badge]` when present, otherwise fixed top-right. Large pages use a serial queued batcher (Google v2 max **128** strings/request) with 429 backoff so content-heavy pages still finish.
- Choosing English (`content_lang=en`) leaves English UI as-is; server-side UGC may still be translated into English when the author wrote in another language.
- **Person names and usernames are never machine-translated.** Displays use `no_translate()` (`Helper::noTranslate`) → `<span class="notranslate" translate="no">…</span>`. Name inputs and known name UI nodes (e.g. `.GroupName`) are also marked via `protect-names-from-translate.js`.
- **Translation failure diagnostics:** API/network failures leave original text readable and POST anonymized diagnostics to `POST /translation-client-log`. Surface tag: `website` (or `ecom` / `elearning` on those hosts).

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