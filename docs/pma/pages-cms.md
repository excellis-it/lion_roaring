---
title: Pages (CMS)
updated: 2026-07-27
status: ready
sidebar_key: pages
---

# Pages (CMS)

## Overview

Grouped Admin Portal page editors that power most of the **Website Frontend** content.

**Controllers:** under `User\Admin\` — HomeCms, Details, Organization, AboutUs, Faq, Gallery, EcclesiaAssociation, PrincipleAndBusiness, ArticleOfAssociation, Footer, RegisterAgreement, PmaDisclaimer (PMA Terms), PrivacyPolicy, TermsAndCondition, etc.  
**Routes:** `user.admin.*` under `/user/admin/pages/...` and related paths

## Features

### Editable pages

- Home, Details, Organization CMS, About Us, FAQs, Gallery.
- Ecclesia Association, Principle and Business Model, Articles of Association.
- Footer, Register Page Agreements, PMA Terms, Privacy Policy, Terms and Conditions, Contact Us CMS.

### Home CMS

- Content is edited per **Content Country** (Global admins) or the regional admin’s own country.
- **Banner Image** includes a **Show banner image for this country** checkbox (`show_banner_image`). The website hero overlay image is shown only when this flag is on for the visitor’s country content. Existing rows default to on for all countries except Global (`GL`), matching the previous hardcoded rule.
- **Section 1 Video** and **Book Section** fields are no longer shown in the Home CMS form (those blocks are unused on the website frontend). Existing database values are left unchanged.

### US content prefill (single-row and multi-row CMS)

Applies to every editor listed under **Editable pages** above (Home, About Us, Footer, Organization, Ecclesia Association, Principle and Business, Article of Association, Register Agreement, PMA Terms, Privacy Policy, Terms and Conditions, Contact Us CMS) as well as the multi-row pages (FAQs, Gallery, Testimonials, Details, Our Organization, Our Governance):

- **Single-row pages:** if the selected/regional country has no row yet, the editor loads the **US** row as a read-only-id draft (US content shown, but saving always creates a brand-new row scoped to the selected country — the original US row is never overwritten) and displays a banner explaining the content is being previewed from US. Implemented via `Helper::resolveCmsEditCountryCode()` + `Helper::loadCmsRowForEdit()` in each controller's `index()`/`store()`/`update()`, and the shared `resources/views/user/admin/partials/cms-us-prefill-banner.blade.php` partial in each form.
- **Multi-row pages (list/page CMS):** if a non-US country has **no rows yet** for a list/page, the editor shows the **US** content as read-only **drafts** (a "US draft" badge, no edit/delete) plus the same prefill banner.
- Regional admins always edit their own country's content; Global admins (or Super Admins in the global domain context) can pick the **Content Country** to edit via a selector, shown only when `Helper::canSelectCmsContentCountry()` is true.
- Saving/updating an existing row is only ever applied when the row's `id` **and** `country_code` both match the resolved edit country — otherwise a new row is created for that country, preventing cross-country overwrites.
- Our Governance's drag-to-reorder ignores draft rows (no id yet) since they don't exist in the database until saved.

### Footer CMS fields

Footer admin exposes fields that drive the website footer: logo, flag, title, address fields, phone, email, newsletter title, and copyright text.

Play Store link/icon, App Store link/icon, and Social Link rows are **not** shown in the Footer admin form — those settings are unused on the website frontend (BUG-058).

## Permissions and conditions

Parent sidebar visible if any Manage-* page permission is present, including:

- `Manage Home Page`, `Manage Details Page`, `Manage Organizations Page`, `Manage About Us Page`
- `Manage Faq` (+ Create/Edit/Delete), `Manage Gallery` (+ CRUD)
- `Manage Ecclesia Association Page`, `Manage Principle and Business Page`, `Manage Article of Association Page`
- `Manage Footer`, `Manage Register Page Agreement Page`, `Manage PMA Terms Page`
- `Manage Privacy Policy Page`, `Manage Terms and Conditions Page`

Most content uses Global `content_country_code` (default US) vs Regional own country code.