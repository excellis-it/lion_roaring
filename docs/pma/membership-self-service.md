---
title: Membership (Self-Service)
updated: 2026-07-24
status: ready
sidebar_key: membership
---

# Membership (Self-Service)

## Overview

Member-facing membership panel: view plan, upgrade, checkout, renew, cancel, apply promo, token subscribe.

**Controller:** `User\MembershipController` (self-service methods)  
**Routes:** `user.membership.index|upgrade|checkout|renew|cancel|apply-promo|inline-payment|token-subscribe|…`

## Features

### Member panel

- Shown when `membershipPanelApplicable()` — user is **not** `membership_excluded` and **not** Super Admin.
- Checkout success/cancel flows; Stripe inline payment; promo apply.

## Permissions and conditions

- No Spatie gate on the sidebar item — visibility via `membershipPanelApplicable()`.
- `MemberAccess` redirects users without a valid subscription here (except SA / excluded).
- Agreement must already be signed (`agreement.signed` middleware).
- Super Admin does not see this menu (bypasses membership requirement entirely).