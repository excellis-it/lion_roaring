---
title: Pages (CMS)
updated: 2026-07-24
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
- Footer, Register Page Agreements, PMA Terms, Privacy Policy, Terms and Conditions.
- Contact Us Messages / Contact CMS may exist in code but are commented out of the sidebar.

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