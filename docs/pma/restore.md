---
title: Restore
updated: 2026-07-24
status: ready
sidebar_key: recycle_bin
---

# Restore

## Overview

Recycle bin for soft-deleted records across mapped application tables. **Super Admin only.**

**Controller:** `User\RecycleBinController`  
**Routes:** `user.recycle-bin.*` with middleware `super_admin`

## Features

### Bin operations

- Index of tables; show deleted rows per table.
- Restore single / bulk / restore-all.
- Force-delete single / bulk; empty bin.

## Permissions and conditions

- Sidebar: `hasNewRole('SUPER ADMIN')` only (no Spatie gate).
- Route middleware: `super_admin` → 403 otherwise.
- Covers soft-deleted models (users, bulletins, CMS, store entities, etc.).
- Spatie `roles` table is excluded (no SoftDeletes).