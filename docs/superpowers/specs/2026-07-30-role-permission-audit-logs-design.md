# Role & Permission Audit Logs — Design Spec

**Date:** 2026-07-30  
**Status:** Approved (pending user review of written spec)  
**Approach:** Dedicated `role_permission_audit_logs` table + writer service + PMA UI  
**PMA docs impact:** `docs/pma/all-members.md`, `docs/pma/role-permission.md`

---

## Overview

Fulfill the Profile Role Management gap: **audit logs for role and permission changes** in the User PMA.

Add:

1. A global **Audit Logs** entry from the Members List header (after **Export Report**).
2. A per-member **Audit Logs** action icon on each Members List row.
3. A dedicated audit page with filters, expandable detail, and CSV/Excel export.
4. Structured logging whenever role/permission state changes across Members, Role Permission templates, and membership privilege auto-sync.

Logging is **forward-only** from go-live (no historical backfill).

---

## Decisions

| Item | Decision |
|------|----------|
| Event scope | **C — full:** member create/edit role & permissions; Role Permission template create/edit/delete; membership privilege auto-sync |
| Access | Super Admin **or** `Manage Partners` **or** `Manage Role Permission` (no new Spatie permission) |
| Visibility | **A — same as Members List** (Regional → own country / allowed members; Global → broader; Super Admin → full). Export uses the same scope. |
| Entry points | Header button after Export Report + per-row audit action icon |
| Export | Yes (CSV/Excel) in v1 |
| Storage | Dedicated table (not `user_activities`, not Spatie Activitylog) |

---

## Architecture

```
PartnerController (create/update member role/perms)
RolePermissionsController (template CRUD)
MembershipPrivilegeService (auto sync)
Registration / API paths that sync roles (same writer)
        │
        ▼
RolePermissionAuditLogger  ──try/catch──►  role_permission_audit_logs
        │
        ▼
RolePermissionAuditLogController
  - index (global)
  - memberIndex (per target user)
  - export
        │
        ▼
user.partner.audit-logs blade (shared view)
```

**Writer reliability**

- Never block the main business save: wrap insert in try/catch; on failure write to Laravel log and continue.
- Skip no-op edits (no role/permission/tier/user_type change).
- Snapshot actor/target display names at write time so history remains readable after renames.

---

## Database

### `role_permission_audit_logs`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint PK | |
| `action` | string | See action vocabulary below |
| `source` | string | `pma`, `api`, `membership_sync`, `registration`, `system` |
| `actor_id` | FK users nullable | Who made the change; null for pure system |
| `actor_name` | string nullable | Snapshot |
| `actor_email` | string nullable | Snapshot |
| `target_user_id` | FK users nullable | Affected member; null for pure template events with no single target |
| `target_user_name` | string nullable | Snapshot |
| `target_user_email` | string nullable | Snapshot |
| `target_country_id` | FK countries nullable | Used for Members List–style visibility filtering |
| `role_template_id` | unsigned bigint nullable | `user_types.id` when template involved |
| `role_template_name` | string nullable | Snapshot |
| `old_role_name` | string nullable | Profile / Spatie role before |
| `new_role_name` | string nullable | Profile / Spatie role after |
| `old_user_type` | string nullable | Global / Regional / G_R |
| `new_user_type` | string nullable | |
| `old_permissions` | JSON nullable | Full set before |
| `new_permissions` | JSON nullable | Full set after |
| `permissions_added` | JSON nullable | Diff |
| `permissions_removed` | JSON nullable | Diff |
| `old_membership_tier_id` | unsigned bigint nullable | |
| `old_membership_tier_name` | string nullable | |
| `new_membership_tier_id` | unsigned bigint nullable | |
| `new_membership_tier_name` | string nullable | |
| `ip` | string nullable | |
| `user_agent` | text nullable | |
| `country_code` | string nullable | Actor visitor/profile context when available |
| `meta` | JSON nullable | e.g. affected users count for template changes |
| `created_at` | timestamp | Event time (no `updated_at` required) |

**Indexes:** `created_at`, `action`, `actor_id`, `target_user_id`, `target_country_id`, `source`.

### Action vocabulary

- `member_role_created`
- `member_role_updated`
- `member_permissions_updated`
- `role_template_created`
- `role_template_updated`
- `role_template_deleted`
- `membership_privilege_synced`

A single member save that changes both role and permissions may emit one combined row (`member_role_updated`) with both role and permission diffs populated, rather than two near-duplicate rows.

---

## Access & visibility

### Gate

Allow if any of:

- Authenticated user is Super Admin (existing project check / type hierarchy), **or**
- `can('Manage Partners')`, **or**
- `can('Manage Role Permission')`

Otherwise 403. No new permission seed.

### List/export scoping

Reuse the same partner visibility rules as Members List (`PartnerController` index / fetch-data):

- Super Admin: all audit rows.
- Global: rows whose target members are in the Global-visible set (and template-only rows may be included for Global/SA; Regional excludes template-only rows that have no `target_user_id` / country, unless product later ties templates to a region — **v1: template-only rows visible to Super Admin and Global only**).
- Regional: only rows where `target_country_id` matches the viewer’s country (or target member is otherwise in their allowed set).

Per-member URL: additionally require that the target member is visible to the viewer under Members List rules; otherwise 403.

---

## UI

### Members List (`resources/views/user/partner/list.blade.php`)

1. **Header button** after **Export Report**: `Audit Logs` → global audit index.
2. **Per-row action icon** (history/audit): → that member’s audit page.
3. Show button/icon only when the viewer passes the access gate (and row icon only for members already present in the list).

### Audit page (shared)

- Titles:
  - Global: `Role & Permission Audit Logs`
  - Per member: `Audit Logs — {Member Name}`
- Table columns: Date/time · Action · Actor · Target member · Role (old → new) · Permissions (+/− summary) · Source · IP
- Expandable detail: full before/after permission lists, user type change, membership tier change, user agent, meta
- Filters: date range, action type, actor, target member (hidden on per-member view), source, role name
- Pagination; newest first
- Empty state: `No audit logs yet`
- **Export** button: CSV/Excel of current filtered + visibility-scoped result set

### Routes (User PMA)

| Method | Path | Name (suggested) |
|--------|------|------------------|
| GET | `/user/partners/audit-logs` | `partners.audit-logs` |
| GET | `/user/partners/audit-logs/export` | `partners.audit-logs.export` |
| GET | `/user/partners/{partner}/audit-logs` | `partners.audit-logs.member` |
| GET | `/user/partners/{partner}/audit-logs/export` | `partners.audit-logs.member.export` |

Controller: e.g. `User\RolePermissionAuditLogController` (or methods on `PartnerController` if preferred for locality — **prefer dedicated controller** for clarity).

---

## Instrumentation points

| Location | When to log |
|----------|-------------|
| `PartnerController` store | After successful create with role/permissions |
| `PartnerController` update | When role, permissions, user_type, or membership tier/privilege-related fields change |
| `RolePermissionsController` store/update/delete | Template create/update/delete (include affected-user count in `meta` when available) |
| `MembershipPrivilegeService` | When syncing/clearing permissions from tier |
| Registration / partner API paths that `syncPermissions` / `assignRole` | Same writer, `source` = `registration` or `api` |

Do **not** rely on `UserActivityLogger` (GET page visits) for this feature.

---

## Testing

- Access gate: allowed vs forbidden.
- Member role/permission edit creates audit row with correct diff.
- Role template update creates audit row.
- Membership privilege sync creates audit row.
- Global list + per-member filter work.
- Export respects filters and visibility.
- Regional user cannot open another region’s member audit URL / does not see out-of-scope rows.

---

## Documentation

- Update `docs/pma/all-members.md`: header button, row action, page, export, access, visibility.
- Update `docs/pma/role-permission.md`: template changes are audited into the same log.
- Bump frontmatter `updated` on both.

---

## Out of scope (v1)

- Backfilling historical changes
- Free-text “reason” field on edits
- New Spatie permission name
- Mobile app UI for audit logs
- Replacing or merging with existing User Activity screens

---

## Success criteria

- Client requirement “Audit logs for role and permission changes” is met for member edits, role templates, and membership auto-sync.
- Super Admins and users with Manage Partners / Manage Role Permission can open logs from Members List (global + per member).
- Regional visibility matches Members List.
- Export works.
- Failed audit writes never break member/role saves.
