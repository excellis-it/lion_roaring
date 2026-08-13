---
title: Restore
updated: 2026-08-13
status: ready
sidebar_key: recycle_bin
---

# Restore

## What this is

**Restore** (recycle bin) lets Super Admins recover soft-deleted records or permanently erase them. It is the safety net after someone deletes a user, bulletin, CMS row, store entity, and similar items that support soft delete.

---

## Who can use it

**Super Admin only.**

- Sidebar shows only for Super Admin.
- Routes are locked with Super Admin middleware.
- There is no ordinary Spatie “Manage Restore” permission for other roles.

If you are staff and need something undeleted, ask a Super Admin.

---

## What you can do

| Action | Meaning |
|--------|---------|
| Browse by table | See deleted rows for that area |
| Restore one / bulk / all | Bring records back |
| Force delete | Permanently remove — **cannot be undone** |
| Empty bin | Clear deleted rows for that table |

The Spatie **roles** table is excluded from this tool.

---

## Rules and warnings

1. Force delete and empty bin are irreversible.  
2. Restoring a user does not automatically fix membership expiry, agreements, or permissions — verify those after restore.  
3. Some deletes may not be soft deletes; those will not appear here.  
4. Non–Super Admins will not see this menu even if they can delete items elsewhere.

---

## Related documentation

- **All Members** — people directory  
- **Bulletins** / **Pages (CMS)** — common soft-delete sources  
- **User PMA** — Super Admin bypasses
