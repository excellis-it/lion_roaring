---
title: Role Permission
updated: 2026-07-24
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