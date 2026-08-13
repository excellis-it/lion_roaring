---
title: All Members
updated: 2026-08-13
status: ready
sidebar_key: all_members
---

# All Members

## What this is

**All Members** is the main directory of partners and members in the User PMA. Use it to find people, open their profile, change status, export lists, review register agreements, and (with extra permission) read role and permission **audit logs**.

To **add a new person**, use **+ Add Members** on this list. That flow is documented in full under **Create Member**.

---

## Who can use it

| Need | Permission / role |
|------|-------------------|
| See the menu and list | **Manage Partners** |
| Add a member | **Create Partners** (see Create Member) |
| Edit a member | **Edit Partners** |
| View member detail | **View Partners** |
| Delete | **Delete Partners** |
| Audit Logs button / timelines | **View Member Audit Logs** **or** Super Admin |

**Important:** Manage Partners does **not** automatically include Create Partners or View Member Audit Logs. Those are separate.

---

## What you can do on the list

- Search and filter members (DataTables-style list).
- Open a member to view or edit profile, country, ecclesia, user type, role, permissions, membership flags.
- Change active/inactive status (where permitted).
- Export a report.
- Open agreement details for a member.
- Open **Audit Logs** (header) for the global timeline, or the history icon on a row for that member only.

---

## Who appears in whose list (visibility)

This is a frequent source of confusion (“I created them but my colleague cannot see them”).

| Viewer | Typically sees |
|--------|----------------|
| **Super Admin** | Everyone, including inactive |
| **Global** | Global and G_R members |
| **Regional** | Regional and G_R members in **the same country** |
| Non–Super Admin | Usually **active** members only |

If someone is missing from the list, check their User Type, country, and status — not only whether Create succeeded.

---

## Editing members — rules that matter

- Non–Super Admin editors are generally limited to the same **User Type** patterns as create, and Regional editors to their **country**.
- Roles marked **Admin** in Role Permission force stored User Type to **G_R**.
- Changing role or permissions is recorded in audit logs (when logging is available).
- Password changes in audit show as changed — never the plaintext password.

Full field-by-field create rules: **Create Member**.

---

## Audit logs (role & permission history)

### What they show

Each create or save can appear as a card: who did it, when, action, source (PMA / API / etc.), and expandable before → after field changes (name, email, phone, IDs, address, ecclesia, membership, user type, role, permissions).

Role Permission template changes and membership privilege sync can appear as their own actions.

### Who can open them

- Super Admin, **or** permission **View Member Audit Logs**.
- Not implied by Manage Partners alone.
- By default only Super Admin has the permission unless you grant it on a role or person.

### What each viewer’s audit list includes

| Viewer | Scope |
|--------|--------|
| Super Admin | All rows |
| Global | Rows for Global-visible members, plus template-only rows (no target member) |
| Regional | Rows for Regional-visible members only (no template-only rows) |

Opening a per-member audit URL also requires that member to be visible under the same list rules; otherwise access is denied.

### Filters and export

- Filter by date, action, source, actor, target member (global views), role name.
- Export filtered results to Excel (capped; includes a Field Changes column).
- Logging is forward-only from when the feature went live — older history was not backfilled.

---

## Related documentation

- **Create Member** — full add-member guide  
- **Role Permission** — templates that drive roles  
- **Membership Management** — plans and subscriptions  
- **Signup Rules** — public signup only (not this directory’s create form)  
- **Global & Regional Domains** — User Type and host rules
