---
title: All Members
updated: 2026-07-30
status: ready
sidebar_key: all_members
---

# All Members

## Overview

Partners / all-members directory (create, edit, status, export, agreement details, role & permission audit logs).

**Controller:** `User\PartnerController`, `User\RolePermissionAuditLogController`  
**Routes:** `partners` resource, `partners.change-status`, `partners.fetch-data`, export, agreement-details, `partners.audit-logs`, `partners.audit-logs.export`, `partners.audit-logs.member`, `partners.audit-logs.member.export`

## Features

### Directory

- List/filter partners; DataTables-style fetch.
- Create/edit partner profile, country, ecclesia, user type.
- Change status; export; view agreement details.

### Role & permission audit logs

- **Header button** — After **Export Report**, an **Audit Logs** button (history icon) opens the global audit timeline (`partners.audit-logs`). Shown only when the viewer passes the audit access gate.
- **Per-row action** — Each member row includes a history icon linking to that member’s audit timeline (`partners.audit-logs.member`, encrypted user id).
- **Timeline UI** — Modern card timeline (not a dense table). Each save is one card with actor, time, action/source badges, and expandable field-level before → after diffs.
- **Member create/edit logging** — One row per save (`member_created` / `member_updated`) with `field_changes` JSON covering all Member Edit fields that changed (name, email, phone, IDs, address/location, ecclesia, membership, user type, role, permissions). Password changes are logged as `(changed)` only — never plaintext.
- **Still separate cards** — Role Permission template create/update/delete and membership privilege auto-sync remain their own actions.
- **Legacy rows** — Older role-only rows (without `field_changes`) still render with role/permission summaries.
- **Filters** — Date range, action, source, actor, target member (global only), role name. Pagination; newest first. Empty state: `No audit logs yet`.
- **Export** — Excel (`.xlsx`) of the current filtered result set (includes a Field Changes column), capped at 5000 rows.

## Permissions and conditions

- Gates: `Manage Partners`, Create/Edit/Delete/View Partners.
- Super Admin: may see inactive users and manage broader type/country options.
- Non-SA: typically `status = 1`; Global → Global+G_R; Regional → same country Regional+G_R.
- Create/edit enforces same `user_type` as auth (non-SA) and country for Regional.
- Admin roles force `user_type = G_R`; new partners often `status = 1`, `is_accept = 1`.

### Audit log access and visibility

- **Access gate** — Super Admin **or** `Manage Partners` **or** `Manage Role Permission`. Otherwise 403. No new Spatie permission.
- **List scoping** — Matches Members List partner visibility (`PartnerVisibility`):
  - Super Admin: all audit rows.
  - Global: rows whose `target_user_id` is in the Global-visible partner set, plus template-only rows (`target_user_id` null).
  - Regional: rows whose `target_user_id` is in the Regional-visible partner set only (no template-only rows).
- **Per-member URL** — Additionally requires the target member to be visible under Members List rules; otherwise 403.
- Logging is forward-only from go-live (no historical backfill).