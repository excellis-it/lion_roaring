---
title: Donations
updated: 2026-08-13
status: ready
sidebar_key: donations
---

# Donations

## What this is

**Donations** in the User PMA is an **admin inbox of donation records** created when people donate on the **public website** (card payment via Stripe).

Staff use this screen to review what was given. Visitors never use this menu to donate — they use the public donations page.

---

## Who can use it

Permission: **Manage Donations**.

Without that permission, the menu does not appear even if you can edit other Admin Portal items.

---

## What you will see

- A list of donation records (amount, donor details as stored, country linkage, timing).  
- Tools to fetch/refresh the list.  
- Delete when your role and UI allow it.

You do not configure Stripe keys on this page (that is environment / ops configuration). You also do not build the public donation form here — that is website/frontend work.

---

## Country visibility rules

| Your User Type | Donation rows you typically see |
|----------------|----------------------------------|
| **Global** | All countries |
| **Regional** (and similar non-global staff) | Only donations tied to **your country** |

### Support scenarios

- “A donation was made in Country B but Regional staff in Country A cannot see it” — expected.  
- Global staff (or Super Admin patterns with broad access) should check the list when cross-country review is needed.  
- If the public thank-you page succeeded but the row is missing, involve technical support — do not recreate donations by hand in this list unless your process says so.

---

## Related documentation

- **Website Frontend** — how guests donate  
- **Countries** — country records  
- **Global & Regional Domains** — why Global vs Regional lists differ
