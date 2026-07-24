---
title: E-Learning
updated: 2026-07-24
status: ready
sidebar_key: e_learning
---

# E-Learning

## Overview

**Public catalog** at `/e-learning` and **PMA admin** under `/user` (categories, subcategories, topics, products, CMS, newsletters). Mobile adds **cart/checkout** via `/api/v3` (web catalog does not).

**Public layout:** `resources/views/elearning/layouts/master.blade.php`  
**Public controllers:** `Elearning\ElearningHomeController`, `Elearning\ElearningProductController`  
**PMA:** `User\ElearningController`, category/subcategory/topic/CMS/newsletter controllers

## Features

### Public catalog (`/e-learning`)

- Home, products, category and category/subcategory pages.
- Product details, filters, subcategory AJAX.
- Reviews auto-approved (`status = 1`) — unlike E-Store pending reviews.
- CMS pages `/e-learning/page/{slug}`, newsletter POST.
- **No** cart, checkout, wishlist, or payments on web.

### Public access rules

- Middleware: `user` + `agreement.signed` only.
- **No `member.access`** — expired members can still browse if logged in and agreement signed.
- Only `status = 1` products/categories/subcategories shown.
- CMS via visitor country (same domain rules as frontend; US fallback).

### PMA admin

- Dashboard / CMS (country-scoped, unique slug listing).
- Categories, Sub Categories, Topics, Products.
- E-learning newsletters.
- Permissions: `Manage Elearning CMS|Category|Sub Category|Topic|Product` (+ View CMS where used).
- PMA stack still requires membership + agreement unless Super Admin / excluded.

### Global vs Regional (admin CMS)

| Actor | CMS content country |
|-------|---------------------|
| Global | `content_country_code` picker (default US) |
| Regional | Locked to own country code |
| Super Admin | Typically full picker / unscoped where implemented |

### Mobile

- Full e-learning commerce (cart/checkout/library) against v3 APIs.
- Host follows selected country (`US` → us host; else org host).
- See **Mobile App** hub.

## Permissions and conditions

- Wrong domain instance still logs out via `EnsureUserInstanceAccess` before catalog use.
- Warehouse/membership rules of E-Store do **not** apply to public web e-learning browse.