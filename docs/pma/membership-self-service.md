---
title: Membership (Self-Service)
updated: 2026-08-13
status: ready
sidebar_key: membership
---

# Membership (Self-Service)

## What this is

The **Membership** sidebar item is for **the logged-in person managing their own plan** — view status, upgrade, check out, renew, cancel, apply a promo, or complete token-based flows.

It is **not** the admin Membership Management area, and it does **not** create other people’s accounts.

---

## Who sees this menu

Shown when **both** are true:

1. The account is **not** marked membership-excluded.
2. The user is **not** a Super Admin.

Super Admins never need this menu (they bypass membership). Excluded users also do not see it.

No special Spatie “Manage Membership” permission is required — it is a member-facing panel.

---

## When people get sent here automatically

If membership has expired (or is missing) and the person is not Super Admin / not excluded, panel middleware can redirect them to membership self-service until they renew. That feels like “PMA is broken” but is usually the membership gate working as designed.

---

## What members can do

- See current plan and dates.
- Upgrade or choose a plan and pay (card / configured methods).
- Renew or cancel according to the flows enabled on your site.
- Enter promo codes when the purchase flow allows it.
- Use token subscribe flows when the product offers them.

Exact buttons depend on plan configuration in **Membership Management**.

---

## Rules and conditions

| Situation | What happens |
|-----------|----------------|
| Valid subscription | Full PMA (plus agreement rules) |
| Expired / missing subscription | Blocked from most PMA; nudged to this Membership area |
| `membership_excluded` | No membership wall; this menu hidden |
| Super Admin | No membership wall; this menu hidden |
| Agreement not signed | May still need to sign register agreement (separate from membership) |

Staff who need to fix someone else’s plan or expire date should use **Membership Management → Members**, not this screen.

---

## Related documentation

- **Membership Management** — admin plans, payments, expire dates  
- **Create Member** — assign plan or exclude at create time  
- **User PMA** — panel-wide membership gate
