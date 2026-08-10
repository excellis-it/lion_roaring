# Member Audit Logs — Dedicated Permission Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a dedicated Spatie permission for the Members List **Audit Logs** module so access is no longer implied by `Manage Partners` or `Manage Role Permission`. Only **Super Admin** receives the permission by default; all other roles/tiers must have it explicitly checked.

**Architecture:** Introduce `View Member Audit Logs` in the existing Spatie permission system. Replace `PartnerVisibility::canAccessAudit()` gate logic to require Super Admin **or** that permission. Register the checkbox under **Management → All Members** in member/tier permission UIs. No API or mobile changes (audit logs are web PMA only).

**Tech Stack:** Laravel, Spatie Laravel Permission, Blade, PHPUnit feature tests.

## Global Constraints

- Permission name (exact): `View Member Audit Logs`
- Access gate (exact): `Super Admin` **or** `can('View Member Audit Logs')` — **remove** `Manage Partners` and `Manage Role Permission` from the audit gate.
- Do **not** add the new permission to any membership tier template by default.
- Do **not** auto-grant the new permission to existing `Manage Partners` users (breaking change by design).
- Super Admin users receive all permissions via existing `RolePermissionSeeder` `Permission::all()` loop after the permission row exists.
- Visibility scoping (`PartnerVisibility::constrainAuditQuery`, `viewerCanSeePartner`) stays unchanged.
- PMA docs impact: `docs/pma/all-members.md`, `docs/pma/role-permission.md` (bump `updated`).

---

## File Map

**Create:**
- `database/seeders/AddMemberAuditLogsPermissionSeeder.php`

**Modify:**
- `database/seeders/RolePermissionSeeder.php` — add permission to master list
- `app/Support/PartnerVisibility.php` — update `canAccessAudit()`
- `app/Http/Controllers/User/PartnerController.php` — add permission to `permissionsArray()` under All Members
- `tests/Feature/RolePermissionAuditLogTest.php` — update gate tests
- `docs/pma/all-members.md` — document new access gate
- `docs/pma/role-permission.md` — cross-reference update

**No changes needed (already call `canAccessAudit()`):**
- `app/Http/Controllers/User/RolePermissionAuditLogController.php`
- `resources/views/user/partner/list.blade.php`
- `resources/views/user/partner/table.blade.php`

---

## Task 1: Add permission to database

**Files:**
- Create: `database/seeders/AddMemberAuditLogsPermissionSeeder.php`
- Modify: `database/seeders/RolePermissionSeeder.php` (Partners section, ~line 96)

**Interfaces:**
- Produces: Spatie permission row `View Member Audit Logs` (`guard_name: web`). Super Admin role holders receive it immediately when seeder runs.

- [ ] **Step 1: Create standalone seeder**

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class AddMemberAuditLogsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permission = Permission::firstOrCreate([
            'name' => 'View Member Audit Logs',
            'guard_name' => 'web',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $superAdminType = UserType::where('name', 'SUPER ADMIN')->first();
        if (!$superAdminType) {
            return;
        }

        $superAdmins = User::query()
            ->where('user_type_id', $superAdminType->id)
            ->orWhereHas('roles', fn ($q) => $q->where('name', 'SUPER ADMIN'))
            ->get();

        foreach ($superAdmins as $admin) {
            if (!$admin->hasPermissionTo($permission)) {
                $admin->givePermissionTo($permission);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
```

- [ ] **Step 2: Add to RolePermissionSeeder `$permissions` array**

After the existing Partners block (`View Partners`), add:

```php
["name" => "View Member Audit Logs"],
```

This ensures fresh installs and full re-seeds include the permission; Super Admin sync via `givePermissionTo(Permission::all())` at end of seeder picks it up automatically.

- [ ] **Step 3: Run seeder on dev**

```bash
cd /Volumes/CrucialMacExt/MacOffload/MAMP_htdocs/lion_roaring
php artisan db:seed --class=AddMemberAuditLogsPermissionSeeder
```

Expected: Row in `permissions` table. Super Admin users have it in `model_has_permissions` or via role. Tier 1 / Tier 2 templates unchanged.

- [ ] **Step 4: Commit**

```bash
git add database/seeders/AddMemberAuditLogsPermissionSeeder.php database/seeders/RolePermissionSeeder.php
git commit -m "feat: add View Member Audit Logs permission"
```

---

## Task 2: Update access gate (TDD)

**Files:**
- Modify: `app/Support/PartnerVisibility.php`
- Test: `tests/Feature/RolePermissionAuditLogTest.php`

**Interfaces:**
- Consumes: Permission `View Member Audit Logs` from Task 1
- Produces: `PartnerVisibility::canAccessAudit(?User $user): bool` — new behavior

- [ ] **Step 1: Update failing tests first**

In `tests/Feature/RolePermissionAuditLogTest.php`:

1. Replace `manager()` helper — grant `View Member Audit Logs` instead of (or in addition to removing) `Manage Partners`:

```php
private function auditViewer(): User
{
    Permission::findOrCreate('View Member Audit Logs');

    $user = $this->createApiUser([
        'user_type' => 'Global',
        'membership_excluded' => true,
    ]);
    $user->givePermissionTo('View Member Audit Logs');

    return $user->fresh();
}
```

2. Replace `$this->manager()` calls in audit route tests with `$this->auditViewer()`.

3. Rename `regionalManager()` to `regionalAuditViewer()` and grant `View Member Audit Logs` instead of `Manage Partners`.

4. Add **`test_manage_partners_without_audit_permission_forbidden`**:

```php
public function test_manage_partners_without_audit_permission_forbidden(): void
{
    Permission::findOrCreate('Manage Partners');

    $user = $this->createApiUser([
        'user_type' => 'Global',
        'membership_excluded' => true,
    ]);
    $user->givePermissionTo('Manage Partners');

    $this->asAuditUser($user)
        ->get(route('partners.audit-logs'))
        ->assertForbidden();
}
```

5. Add **`test_manage_role_permission_without_audit_permission_forbidden`**:

```php
public function test_manage_role_permission_without_audit_permission_forbidden(): void
{
    Permission::findOrCreate('Manage Role Permission');

    $user = $this->createApiUser([
        'user_type' => 'Global',
        'membership_excluded' => true,
    ]);
    $user->givePermissionTo('Manage Role Permission');

    $this->asAuditUser($user)
        ->get(route('partners.audit-logs'))
        ->assertForbidden();
}
```

6. Add **`test_super_admin_can_view_audit_index_without_explicit_permission`** (optional if SA gets all perms from seeder — use `hasNewRole` setup):

```php
public function test_super_admin_can_view_audit_index(): void
{
    $adminType = UserType::firstOrCreate(
        ['name' => 'SUPER ADMIN'],
        ['guard_name' => 'web', 'type' => '1', 'is_ecclesia' => 0]
    );

    $user = $this->createApiUser([
        'user_type' => 'Global',
        'user_type_id' => $adminType->id,
        'membership_excluded' => true,
    ]);

    $this->asAuditUser($user)
        ->get(route('partners.audit-logs'))
        ->assertOk();
}
```

Note: Super Admin check uses `hasNewRole('SUPER ADMIN')` in gate — test user must have SUPER ADMIN user type/role mapping consistent with project test helpers.

- [ ] **Step 2: Run tests — expect failures**

```bash
php artisan test tests/Feature/RolePermissionAuditLogTest.php
```

Expected: `test_manage_partners_without_audit_permission_forbidden` may PASS only after gate change; `test_manager_can_view_audit_index` FAILS until gate updated.

- [ ] **Step 3: Update `PartnerVisibility::canAccessAudit()`**

```php
public static function canAccessAudit(?User $user): bool
{
    if ($user === null) {
        return false;
    }

    return $user->hasNewRole('SUPER ADMIN')
        || $user->can('View Member Audit Logs');
}
```

- [ ] **Step 4: Run tests — expect all pass**

```bash
php artisan test tests/Feature/RolePermissionAuditLogTest.php
```

Expected: All tests PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Support/PartnerVisibility.php tests/Feature/RolePermissionAuditLogTest.php
git commit -m "feat: gate member audit logs on dedicated permission"
```

---

## Task 3: Expose permission in UI checkboxes

**Files:**
- Modify: `app/Http/Controllers/User/PartnerController.php` — `permissionsArray()` (~line 411)

**Interfaces:**
- Produces: Checkbox visible in Member Edit, Role Template, and Membership Tier permission grids under **All Members**.

- [ ] **Step 1: Add to categorized permissions**

Change the All Members line from:

```php
Helper::getMenuName('all_members', 'All Members') => ['Create Partners', 'Edit Partners', 'Delete Partners', 'Manage Partners', 'View Partners'],
```

To:

```php
Helper::getMenuName('all_members', 'All Members') => [
    'Create Partners',
    'Edit Partners',
    'Delete Partners',
    'Manage Partners',
    'View Partners',
    'View Member Audit Logs',
],
```

This automatically surfaces the checkbox in:
- All Members → Create/Edit member
- Roles & Permissions → Create/Edit role template
- Membership → Create/Edit tier (Management section)

- [ ] **Step 2: Manual verify (dev)**

1. Log in as Super Admin → Members List → confirm **Audit Logs** button visible.
2. Edit Tier 1 → confirm **View Member Audit Logs** appears under All Members, **unchecked** by default.
3. Log in as Tier 1 user with only **View Partners** → confirm **Audit Logs** button **hidden**.
4. Grant **View Member Audit Logs** on member edit → save → confirm button appears.

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/User/PartnerController.php
git commit -m "feat: expose View Member Audit Logs in permission UI"
```

---

## Task 4: PMA documentation

**Files:**
- Modify: `docs/pma/all-members.md` — set `updated: 2026-08-10`
- Modify: `docs/pma/role-permission.md` — set `updated: 2026-08-10`

**Interfaces:**
- Documents new gate for client/support reference.

- [ ] **Step 1: Update `docs/pma/all-members.md`**

Replace the Audit log access section:

```markdown
### Audit log access and visibility

- **Permission:** `View Member Audit Logs` (under Management → All Members). Shown in member edit, role template, and membership tier permission grids.
- **Access gate:** Super Admin **or** `View Member Audit Logs`. Otherwise 403.
- **Not implied by:** `Manage Partners`, `Manage Role Permission`, or `View Partners`.
- **Default:** Only Super Admin has this permission out of the box. Membership tiers do not include it unless explicitly checked.
```

Keep existing visibility/scoping bullets unchanged.

- [ ] **Step 2: Update `docs/pma/role-permission.md`**

In the audit logging bullet, change cross-reference:

```markdown
- Audit Logs UI access requires `View Member Audit Logs` (or Super Admin). See **All Members** for gate, visibility, and export.
```

- [ ] **Step 3: Commit**

```bash
git add docs/pma/all-members.md docs/pma/role-permission.md
git commit -m "docs: member audit logs dedicated permission"
```

---

## Task 5: Deploy & client communication

**Files:** None (ops checklist)

- [ ] **Step 1: Run seeder on DEV / staging / production**

```bash
php artisan db:seed --class=AddMemberAuditLogsPermissionSeeder
php artisan permission:cache-reset
```

- [ ] **Step 2: Verify client test account**

For `ssubowo1@yahoo.com` (Tier 1, View Partners only):
- Audit Logs button should disappear after deploy (no `View Member Audit Logs` on account).
- Tier 1 template edit should show new checkbox unchecked.

- [ ] **Step 3: Client message (paste-ready)**

> Audit Logs now has its own permission: **View Member Audit Logs** (under All Members in Management). It is **not** tied to Manage Partners anymore. By default, only Super Admin has access. To grant it to a specific manager, check **View Member Audit Logs** on their member record or role template. Editing a membership tier template alone does not grant it unless that box is checked and synced to the user.

- [ ] **Step 4: Optional web changelog** (when publishing)

```
Platform: Web Version
Version: v2.1.3 (patch bump)
Type: improvement
Title: Dedicated permission for Member Audit Logs
Description:
- Audit Logs on the Members List now requires the View Member Audit Logs permission (or Super Admin).
- Manage Partners and Manage Role Permission no longer grant audit log access by default.
- Grant access explicitly via member edit, role template, or membership tier permissions.
```

Update `CHANGELOG.md` when ready to publish per team workflow.

---

## Behavior Summary (before → after)

| User | Before | After |
|------|--------|-------|
| Super Admin | Audit Logs ✓ | Audit Logs ✓ |
| Manage Partners only | Audit Logs ✓ | Audit Logs ✗ |
| Manage Role Permission only | Audit Logs ✓ | Audit Logs ✗ |
| View Partners only | Audit Logs ✗* | Audit Logs ✗ |
| View Member Audit Logs | N/A | Audit Logs ✓ |

\*Unless they also had Manage Partners / Manage Role Permission (client confusion case).

---

## Out of Scope (YAGNI)

- Separate `Export Member Audit Logs` permission (export uses same gate as view)
- Mobile/API audit endpoints (none exist)
- Auto-migrating existing Manage Partners users to the new permission
- Bulk revoking stale permissions from membership tier CSV strings (tiers never had this perm)

---

## Verification Checklist

| Check | Command / action |
|-------|------------------|
| Permission exists | `php artisan tinker` → `Permission::where('name','View Member Audit Logs')->exists()` |
| Gate tests | `php artisan test tests/Feature/RolePermissionAuditLogTest.php` |
| Manage Partners blocked | Test #4 above |
| Super Admin allowed | Manual or test |
| UI checkbox | Tier edit → All Members section |
| PMA docs | `docs/pma/all-members.md` updated |
