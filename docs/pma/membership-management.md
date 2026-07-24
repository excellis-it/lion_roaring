---
title: Membership Management
updated: 2026-07-24
status: ready
sidebar_key: membership_management
---

# Membership Management

## Overview

Admin tools for membership tiers, members, payments, promo codes, and settings.

**Controllers:** `User\MembershipController`, `User\PromoCodeController`  
**Routes:** `user.membership.manage|create|…|members|payments|settings`, `user.promo-codes.*`

## Features

### Plans

- Plan list, create, edit, delete tiers.
- Pricing: amount and/or token; Stripe for card flows.

### Members and payments

- Members list, bulk/individual expire-date updates.
- Payment history; member payment drill-down.

### Promo codes / settings

- Promo scope: `all_tiers`, `selected_tiers`, `all_users`, `selected_users`.
- Membership settings screen (`View` / `Edit Membership Settings`).

## Permissions and conditions

- Gates: `Manage Membership`, Create/Edit/Delete Membership, `View Membership Settings`, `Edit Membership Settings`, `View Membership Members`, `View Membership Payments`, `Edit Membership Expire Date`, promo code View/Create/Edit/Delete.
- Super Admin: see all members/payments; bulk expire-date SA-oriented.
- Non-SA members list may be limited; expire-date edit: SA or own subscription rules as implemented.
- Members query historically filters `created_at > 2025-11-01`.
- Flutter-only env `IN_APP_MEMBERSHIP` does **not** turn off web membership for non-excluded users.