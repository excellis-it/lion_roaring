---
title: Website Frontend
updated: 2026-07-24
status: ready
sidebar_key: website_frontend
---

# Website Frontend

## Overview

Public marketing and information site at the domain root (`/`). No login required for browsing. Content is country-aware via visitor country helpers (fallback **US**). Managed from the PMA **Admin Portal** (Pages, Donations, Organizations, etc.).

**Layout:** `resources/views/frontend/layouts/master.blade.php`  
**Controllers:** `Frontend\CmsController`, `Frontend\DonationController`, `Frontend\MembershipController`  
**Middleware (public group):** `userActivity` only

## Features

### Home and country routing

- Routes: `home` (`/`), `home.country` (`/{cc}`).
- Domain / path country resolution via `Country` records, `Helper::isUsaInstance()`, session visitor country keys.
- Global middleware also applies canonical country URL and instance access checks.

### Marketing / content pages

- Gallery (`gallery`), FAQ (`faq`), Contact Us (`contact-us`), About Us (`about-us`), Details (`details`).
- Principle and Business, Ecclesia Covenant (`ecclesia-associations`), Organization listing.
- Org hierarchy: Our Organization → Organization Centers (`features/{slug}`) → Services (`service/{slug}`).
- Our Governance (`our-governance/{slug}`).
- Terms and Conditions, Privacy Policy.
- Membership marketing page (`membership`) — tiers display; purchase/manage under `/user/membership`.

### Forms and donations

- Newsletter POST (`newsletter`) emails site contact.
- Contact form (`contact-us.form`) requires **reCAPTCHA** (`RECAPTCHA_*` env).
- Donations POST (`donation`) via Stripe Charge (USD); guests allowed; thank-you page (`thankyou`).

### Chatbot on public site

- When `CHATBOT=AI`, RAG widget loads (env: `RAG_WIDGET_URL`, `RAG_API_BASE`, `RAG_BOT_ID`, `RAG_AUTH_TOKEN`).
- Otherwise in-app chatbot partials are used.
- Widget/API routes under `/chatbot/*`.

### Auth adjacent to frontend

- Login, register, OTP, event pages live in the same public route group but are not marketing CMS pages.

## Permissions and conditions

- Public pages: **no** Spatie permission; open to visitors.
- CMS edits: PMA Admin Portal gates (see Pages CMS, Donations, Testimonials, Organizations sections).
- Content filtered by `Helper::getVisitorCmsContent()` / visitor `country_code`, fallback `US`.
- Account must be active (`status == 1`) only when hitting authenticated areas.

## Related PMA menus

Donations, Newsletters, Testimonials, Our Governance, Our Organizations, Organization Center, Services, Pages (CMS), Countries, Site Settings, Chatbot Assistant.