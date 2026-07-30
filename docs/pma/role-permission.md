---
title: Role Permission
updated: 2026-07-30
status: ready
sidebar_key: role_permission
---

# Role Permission

## Overview

Manage panel roles (`user_types`) and their permission maps (`user_type_permissions`), separate from Spatie's seeded permission names used in `Gate::check`.

**Controller:** `User\RolePermissionsController`  
**Routes:** `roles` resource, `roles.delete`, `roles.affected-users`

## Features

### Role CRUD

- Create/edit roles and attach permissions.
- Sidebar gate label: `Manage Role Permission` (may be legacy/DB-only; controller primarily uses **user_type.type** hierarchy).
- **Audit logging** — Successful template create, update, and delete write rows to `role_permission_audit_logs` via `RolePermissionAuditLogger` (`source: pma`):
  - Create → `role_template_created` (new permission set; `meta.affected_users` = 0).
  - Update → `role_template_updated` (name, permissions, `is_admin` / `is_ecclesia` diffs; `meta.affected_users` = users on that template).
  - Delete → `role_template_deleted` (final permission set before removal; `meta.affected_users` = 0).
- These entries appear in the Members List **Audit Logs** UI (global and per-member views where applicable). See **All Members** for access gate, visibility, and export.

### Affected users

- Preview users impacted by role changes before destructive actions.

## Permissions and conditions

| Auth user type | Can manage |
|----------------|------------|
| type `1` (Super Admin) | UserTypes type 2 and 3 |
| type `2` or `3` | type `2` only |

- Cannot delete a role named `SUPER ADMIN`.
- Cannot delete a role that still has assigned users.
- Setting `is_admin = 1` bulk-sets those users' `user_type` to `G_R`.