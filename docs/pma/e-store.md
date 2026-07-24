---
title: E-Store
updated: 2026-07-24
status: ready
sidebar_key: e_store
---

# E-Store

## Overview

Covers both the **customer storefront** (`/e-store`) and the **PMA E-Store admin** menus (dashboard, catalog, warehouses, orders, settings, CMS).

**Public layout:** `resources/views/ecom/layouts/master.blade.php`  
**Public controllers:** `Estore\HomeController`, `Estore\ProductController`, `Estore\DigitalCheckoutController`, `Estore\UserAddressController`  
**PMA controllers:** `User\ProductController`, `CategoryController`, `SizeController`, `ColorController`, `WareHouseController`, `EstoreCmsController`, `EstorePromoCodeController`, `EstoreSettingController`, store orders controllers, etc.

## Features

### Public storefront (`/e-store`)

- Home, category pages, all products, product details, live search/filter.
- Cart (add/update/remove/clear), wishlist, multi-address book.
- Physical checkout + **digital** checkout (separate PaymentIntent flows).
- Promo codes (session + `PromoCodeService`).
- My orders, order details, tracking, cancel (within window), invoice/download for digital.
- Profile / change password; contact + CMS pages (`/e-store/page/{slug}`).
- Store registration assigns role `ESTORE_USER`.
- Guest signed URL download for digital files (`e-store.guest-download-file`) outside the auth group.

### PMA admin

- E-store Dashboard / CMS pages (home, footer, contact).
- Product Categories, Sizes, Colors, Products, Warehouses, Warehouse Admins.
- Promo Codes, Store Settings, Order Status, Order Email Templates.
- Orders list: status updates, refunds, invoices, export, reports, reviews moderation.
- Warehouse Store submenu appears when the user has assigned warehouses (`warehouses->count() > 0`).

## Permissions and conditions

### Public access

- Middleware: `user` + `member.access` + `agreement.signed` → **login, active account, (usually) active membership, signed agreement**.
- Guests cannot browse the storefront (redirect home).
- Super Admin / `membership_excluded` bypass membership expiry.

### Commerce rules

- Nearest warehouse drives physical catalog/stock; digital products bypass warehouse stock.
- Physical product missing from nearest warehouse → not available.
- Cart qty capped by `warehouse_product.quantity`.
- Checkout: Stripe PaymentIntent; pickup vs delivery; initial status from `EstoreSetting.cancel_within_hours` (`pending` vs `processing`; pickup → `pickup_processing`).
- Cancel only if paid + pending/processing (incl. pickup variants) + within `cancel_within_hours`.
- Refunds window: `refund_max_days` (default 10).
- Reviews start **pending** until admin approval.
- Throttles on cart/checkout/promo/cancel routes.
- Metal/market rate helpers may affect pricing; Google Maps key used for address UI.

### PMA Spatie permissions

Parent menu if any of: `Manage Estore CMS`, `Manage Estore Users`, `Manage Estore Category`, `Manage Estore Sizes`, `Manage Estore Colors`, `Manage Estore Products`, `Manage Estore Settings`, `Manage Estore Warehouse`, `Manage Estore Orders`, `Manage Order Status`, `Manage Email Template`.  
Products may also allow warehouse admins via `isWarehouseAdmin()`.