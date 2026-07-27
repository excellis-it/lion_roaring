---
title: E-Store
updated: 2026-07-27
status: ready
sidebar_key: e_store
---

# E-Store

## Overview

**Customer storefront** `/e-store` and **PMA E-Store admin** (catalog, warehouses, orders, settings, CMS). Warehouse Store submenu appears when the user has assigned warehouses.

**Public layout:** `resources/views/ecom/layouts/master.blade.php`  
**Public controllers:** `Estore\HomeController`, `ProductController`, `DigitalCheckoutController`, `UserAddressController`  
**PMA:** products, categories, sizes, colors, warehouses, promo codes, settings, orders, Estore CMS

## Features

### Public storefront

- Catalog, search/filter, product details, cart, wishlist, addresses.
- Live search suggestions show the active sale price when set (original price struck through), matching product cards and product details.
- Physical + digital checkout (Stripe PaymentIntent).
- Promo codes, my orders, tracking, cancel window, digital download (signed guest URL exists).
- Registration assigns `ESTORE_USER`.

### Public access rules

- Middleware: `user` + `member.access` + `agreement.signed`.
- Guests redirected home (login required).
- Super Admin / `membership_excluded` bypass membership expiry.
- Store CMS blocks use visitor country (US fallback) on the current domain.

### Commerce rules

- Nearest warehouse drives physical stock; digital bypasses warehouse stock.
- Missing warehouse stock → product not available.
- Cart qty ≤ `warehouse_product.quantity`.
- Order status from `EstoreSetting.cancel_within_hours` (`pending` / `processing` / pickup variants).
- Cancel only paid + pending/processing + within cancel hours.
- Refunds: `refund_max_days` (default 10).
- Reviews **pending** until admin approval.
- Throttles on cart/checkout/promo/cancel.

### PMA admin permissions

Parent if any of: `Manage Estore CMS|Users|Category|Sizes|Colors|Products|Settings|Warehouse|Orders`, `Manage Order Status`, `Manage Email Template`.  
Warehouse admins: `isWarehouseAdmin()` for scoped product tools.

### Global vs Regional

| Actor | Behavior |
|-------|----------|
| Global user, or Super Admin on the global domain | Sees the Content Country picker (`Helper::canSelectCmsContentCountry()`) on Estore CMS editors (Home, Footer, Contact, and slug pages) |
| Regional user | No Content Country picker; editor is locked to their own country (hidden `content_country_code` field) |
| All shoppers | Bound to current domain instance; wrong `user_type` host → logout |
| Warehouse | Physical availability by nearest warehouse for that shopper context |

**US content prefill:** Opening an Estore CMS editor (Home, Footer, Contact, or a slug page) for a country with no saved row shows that page's **US** content as defaults, with a banner noting the content is a US prefill. Saving creates a new row for the selected country — the source US row is never modified. If a country already has its own row, that row is shown as-is (no prefill/banner).

### Mobile

- Full ecom feature module (cart, checkout, orders) on `/api/v3`.
- Host switch by selected country — see **Mobile App**.

## Permissions and conditions

- Membership is mandatory for storefront (unlike public e-learning browse).
- See **Warehouse Store** detail topic for warehouse-only sidebar.