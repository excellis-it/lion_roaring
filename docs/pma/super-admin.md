---
title: Super Admin
updated: 2026-07-24
status: ready
sidebar_key: admin
---

# Super Admin

## Overview

Manage Super Admin user accounts (list under `/user/detail`).

**Controller:** `User\Admin\AdminController`  
**Routes:** `user.admin.index|add|store|edit|delete|admin.update`

## Features

### Admin list

- Lists users with user type name SUPER ADMIN (excludes self).
- Create forces `user_type = Global`, `status = true`, Super Admin `user_type_id`.

## Permissions and conditions

- Gates: `Manage Admin List`, Create/Edit/Delete Admin List.
- Distinct from Spatie role "Super Admin" permission dump — identity is `user_types` via `hasNewRole('SUPER ADMIN')`.