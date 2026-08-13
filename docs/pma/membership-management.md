---
title: Membership Management
updated: 2026-08-13
status: ready
sidebar_key: membership_management
---

# Membership Management

## What this is

**Membership Management** is the **admin** toolkit for paid (or token) membership **plans**, who is subscribed, payments, promo codes, and membership settings.

It does **not** create new people. To add a person to the system, use **All Members → Add Members** (**Create Member**).

---

## Who sees this menu

The parent menu appears if you have any of:

- **Manage Membership**
- **View Membership Members**
- **View Membership Payments**
- **View Membership Settings**
- **View Promo Codes**

Sub-items each need their own permission (see below).

---

## Sub-menus explained

### Plan List

Browse existing membership tiers/plans (name, pricing, duration, benefits, permissions tied to the plan).

Needs access under **Manage Membership** (plan management area).

### Create Plan

Creates a **new plan/tier** — not a member.

Needs **Create Membership** (separate from Manage Membership).

Typical plan fields include name, pricing type (amount and/or token), cost, duration in months, benefits text, and which permissions subscribers receive.

### Members

Lists **subscriptions** (who has which plan, start/expire dates).

Needs **View Membership Members**.

- Super Admin generally sees all.
- Non–Super Admin lists may be narrower.
- Editing expire dates needs **Edit Membership Expire Date** (Super Admin oriented for bulk updates).
- This list is about subscriptions, not the full All Members directory.

### All Payments

Payment history and drills into a member’s payments.

Needs **View Membership Payments**. Super Admin sees the broadest set.

### Promo Codes

Create and manage codes that apply to tiers and/or users (scopes such as all tiers, selected tiers, all users, selected users).

Needs promo View / Create / Edit / Delete permissions as applicable.

### Settings

Membership configuration screen.

Needs **View Membership Settings**; changing values needs **Edit Membership Settings**.

---

## Rules and conditions staff ask about

1. **Create Plan ≠ Create Member** — Create Membership permission builds plans; Create Partners builds people.
2. **Assigning a plan when creating a person** happens on **Create Member** (MEMBER_SOVEREIGN + tier), not on Create Plan.
3. **Flutter / app flag `IN_APP_MEMBERSHIP`** does not turn off web membership requirements for people who still must have a plan on the website.
4. **Excluded members** (`membership_excluded`) bypass the membership gate; they may not appear as “needing” a plan the same way.
5. **Self-service Membership** menu is what **members** use for their own checkout — different from this admin block.
6. Historical members list queries may only show subscriptions created after a cutoff date used in the product — if someone older is missing, check filters and All Members.

---

## Permissions cheat sheet

| Permission | Typical use |
|------------|-------------|
| Manage Membership | Plan list / manage plans |
| Create Membership | Create Plan |
| Edit / Delete Membership | Change or remove plans |
| View Membership Members | Members (subscriptions) list |
| Edit Membership Expire Date | Change expire dates |
| View Membership Payments | Payments |
| View / Edit Membership Settings | Settings screen |
| View / Create / Edit / Delete Promo Codes | Promo tools |

---

## Related documentation

- **Create Member** — give a new MEMBER_SOVEREIGN a plan at create time  
- **Membership (Self-Service)** — member-facing buy/renew  
- **All Members** — people directory  
- **Role Permission** — how plan permissions interact with roles
