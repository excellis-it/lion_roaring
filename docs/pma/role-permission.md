---
title: Role Permission
updated: 2026-08-13
status: ready
sidebar_key: role_permission
---

# Role Permission

## What this is

**Role Permission** manages **role templates** (user types / templates) and which **permissions** each template includes. When you assign a role to a member, you are choosing one of these templates (and sometimes overriding permissions on the person).

This is the place to answer: “Why does this role see Membership Management but not All Members?”

---

## Who can use it

- Sidebar needs **Manage Role Permission**.
- What you are allowed to edit also depends on hierarchy (below).

---

## Hierarchy (who may manage which templates)

| Your template type | You can manage |
|--------------------|----------------|
| Type **1** (Super Admin class) | Types **2** and **3** |
| Types **2** or **3** | Type **2** only |

You cannot delete the **SUPER ADMIN** template, and you cannot delete a role that still has users assigned.

---

## Flags that change member behavior

| Flag on a role | Effect people notice |
|----------------|----------------------|
| **Admin** (`is_admin`) | Users with this role are forced to User Type **G_R** |
| **Ecclesia** (`is_ecclesia`) | Create/Edit Member requires **House Of ECCLESIA** selection |

Changing template permissions can affect many people who inherit that template. The UI may preview affected users. Changes are written to **All Members → Audit Logs** when audit logging is enabled.

---

## How this connects to Create Member

- Create Member lists roles from these templates (partner/staff types).
- MEMBER_SOVEREIGN with a membership plan often gets permissions from the **plan**, not only from free-checkbox picking.
- Granting **View Member Audit Logs** here (or on a person) is required for non–Super Admins to see audit timelines.

---

## Common confusion

1. **Editing Role Permission vs editing one member** — template change can ripple; member edit is one person.  
2. **Permission name vs menu label** — menus use Site Settings → Menu Names for display text; gates still use permission names like Manage Partners.  
3. **Create Membership vs Create Partners** — both appear in permission lists; they unlock different screens.

---

## Related documentation

- **Create Member** / **All Members** — assigning roles to people  
- **Membership Management** — plan permission lists  
- **Site Settings** — menu display names
