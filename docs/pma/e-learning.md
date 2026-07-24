---
title: E-Learning
updated: 2026-07-24
status: ready
sidebar_key: e_learning
---

# E-Learning

## Overview

Covers the **public E-Learning catalog** (`/e-learning`) and **PMA E-Learning admin** (categories, subcategories, topics, products, CMS, newsletters).

**Public layout:** `resources/views/elearning/layouts/master.blade.php`  
**Public controllers:** `Elearning\ElearningHomeController`, `Elearning\ElearningProductController`  
**PMA controllers:** `User\ElearningController`, `ElearningCategoryController`, `ElearningSubCategoryController`, `ElearningTopicController`, `ElearningCmsController`, `ElearningNewsletterController`

## Features

### Public catalog (`/e-learning`)

- Home, all products, category and category/subcategory pages.
- Product details, AJAX filter, subcategory lookup.
- Product reviews (auto-approved: `status = 1` immediately — unlike E-Store).
- CMS pages (`/e-learning/page/{slug}`), newsletter POST.
- **No cart, checkout, wishlist, or payments** on this web surface.

### PMA admin

- E-learning Dashboard / CMS (country-scoped content, unique slug listing).
- Categories, Sub Categories, Topics, Products (`elearning` resource).
- E-learning newsletters management.

## Permissions and conditions

### Public access

- Middleware: `user` + `agreement.signed` only.
- **No `member.access`** — expired members can still open E-Learning if logged in and agreement signed.
- Only `status = 1` products/categories/subcategories are shown.

### PMA Spatie permissions

- `Manage Elearning CMS`, `Manage Elearning Category`, `Manage Elearning Sub Category`, `Manage Elearning Topic`, `Manage Elearning Product`.
- Some CMS views also check `View Elearning CMS`.
- PMA admin still requires membership + agreement (full `/user` stack) unless Super Admin / excluded.