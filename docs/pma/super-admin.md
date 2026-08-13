---
title: Super Admin
updated: 2026-08-13
status: ready
sidebar_key: admin
---

# Super Admin

## What this is

The **Super Admin** sidebar item manages **Super Admin user accounts** (the operator list). It is not the same thing as “I have many permissions on a normal role.”

Creating someone here makes them a Super Admin account (Global user type, Super Admin template, full permission set on a personal role). Creating a normal member is **All Members → Add Members** (**Create Member**).

---

## Who can use it

| Action | Permission |
|--------|------------|
| Open list | **Manage Admin List** |
| Create | **Create Admin List** |
| Edit / Delete | Edit / Delete Admin List |

---

## What the list shows

- Users whose user-type name is Super Admin.
- Typically **excludes yourself** from the list.

---

## What happens when you create a Super Admin

- User Type is forced to **Global**.
- Account is active and accepted.
- They receive a Super Admin user-type assignment and a role with **all** permissions.
- This screen’s create flow does **not** use the Create Member form fields (no membership tier picker, no partner User Type Global/Regional/G_R chooser in the same way).

Super Admins bypass membership walls, agreement gates, and many instance restrictions. Only Super Admins see **Restore** and **Documentation**.

---

## Related documentation

- **Create Member** — create normal partners/members  
- **Role Permission** — templates (do not delete SUPER ADMIN template)  
- **User PMA** — what Super Admin bypasses  
- **Restore** / **Documentation** — SA-only tools
