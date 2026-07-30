# Role & Permission Audit Logs Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add forward-looking role/permission audit logs in User PMA, reachable from Members List (global button + per-member action), covering member edits, role templates, and membership privilege syncs, with Members List visibility and Excel export.

**Architecture:** Dedicated `role_permission_audit_logs` table written only through `RolePermissionAuditLogger` (never blocks business saves). `RolePermissionAuditLogController` lists/filters/exports with visibility via a shared `PartnerVisibility` helper extracted from Members List rules. UI reuses existing PMA Blade + Maatwebsite Excel patterns.

**Tech Stack:** Laravel, Blade (`user.layouts.master`), Spatie Permission (existing gates only), Maatwebsite Excel, PHPUnit Feature/Unit tests with `DatabaseTransactions` + `CreatesApiUsers`.

## Global Constraints

- Spec: `docs/superpowers/specs/2026-07-30-role-permission-audit-logs-design.md`
- Access: Super Admin (`hasNewRole('SUPER ADMIN')`) **or** `Manage Partners` **or** `Manage Role Permission` — no new Spatie permission.
- Visibility: same as Members List; template-only rows (`target_user_id` null) visible to Super Admin and Global only.
- Routes live under User PMA middleware group; name routes `partners.audit-logs*` using `partner-audit-logs` path style (like `partner-export-report`) to avoid clashing with `Route::resource('partners')`.
- Logger try/catch: failures go to Laravel log; never throw to caller.
- Skip no-op writes (no role / permissions / user_type / tier change).
- Snapshot actor/target names at write time.
- Forward-only history (no backfill).
- Blade extends `user.layouts.master`; paginate 15.
- Update `docs/pma/all-members.md` and `docs/pma/role-permission.md` in the docs task; bump frontmatter `updated`.
- Model must **not** use SoftDeletes (`BaseModel` has SoftDeletes — extend `Illuminate\Database\Eloquent\Model` instead).

---

## File Map

**Create:**
- `database/migrations/2026_07_30_120000_create_role_permission_audit_logs_table.php`
- `app/Models/RolePermissionAuditLog.php`
- `app/Services/RolePermissionAuditLogger.php`
- `app/Support/PartnerVisibility.php`
- `app/Http/Controllers/User/RolePermissionAuditLogController.php`
- `app/Exports/RolePermissionAuditLogsExport.php`
- `resources/views/user/partner/audit-logs.blade.php`
- `tests/Unit/RolePermissionAuditLoggerTest.php`
- `tests/Feature/RolePermissionAuditLogTest.php`

**Modify:**
- `routes/web.php` — register audit log routes + import controller
- `app/Http/Controllers/User/PartnerController.php` — call logger on store/update; optionally use `PartnerVisibility`
- `app/Http/Controllers/User/RolePermissionsController.php` — call logger on store/update/delete
- `app/Services/MembershipPrivilegeService.php` — call logger in `syncTierPermissions`
- `app/Services/PartnerMemberApiService.php` — call logger on role/permission sync paths
- `resources/views/user/partner/list.blade.php` — header Audit Logs button
- `resources/views/user/partner/table.blade.php` — per-row audit icon
- `docs/pma/all-members.md`
- `docs/pma/role-permission.md`

---

### Task 1: Migration + Model

**Files:**
- Create: `database/migrations/2026_07_30_120000_create_role_permission_audit_logs_table.php`
- Create: `app/Models/RolePermissionAuditLog.php`
- Test: `tests/Unit/RolePermissionAuditLoggerTest.php` (minimal schema smoke in Task 2; Task 1 verified by migrate)

**Interfaces:**
- Produces: table `role_permission_audit_logs`; model `App\Models\RolePermissionAuditLog` with fillable + JSON casts.

- [ ] **Step 1: Create migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('role_permission_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->string('source')->default('pma');
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_name')->nullable();
            $table->string('actor_email')->nullable();
            $table->foreignId('target_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('target_user_name')->nullable();
            $table->string('target_user_email')->nullable();
            $table->unsignedBigInteger('target_country_id')->nullable()->index();
            $table->unsignedBigInteger('role_template_id')->nullable()->index();
            $table->string('role_template_name')->nullable();
            $table->string('old_role_name')->nullable();
            $table->string('new_role_name')->nullable();
            $table->string('old_user_type')->nullable();
            $table->string('new_user_type')->nullable();
            $table->json('old_permissions')->nullable();
            $table->json('new_permissions')->nullable();
            $table->json('permissions_added')->nullable();
            $table->json('permissions_removed')->nullable();
            $table->unsignedBigInteger('old_membership_tier_id')->nullable();
            $table->string('old_membership_tier_name')->nullable();
            $table->unsignedBigInteger('new_membership_tier_id')->nullable();
            $table->string('new_membership_tier_name')->nullable();
            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('country_code')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('created_at');
            $table->index('action');
            $table->index('source');
            $table->index('actor_id');
            $table->index('target_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permission_audit_logs');
    }
};
```

- [ ] **Step 2: Create model (no SoftDeletes)**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermissionAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'action', 'source',
        'actor_id', 'actor_name', 'actor_email',
        'target_user_id', 'target_user_name', 'target_user_email', 'target_country_id',
        'role_template_id', 'role_template_name',
        'old_role_name', 'new_role_name', 'old_user_type', 'new_user_type',
        'old_permissions', 'new_permissions', 'permissions_added', 'permissions_removed',
        'old_membership_tier_id', 'old_membership_tier_name',
        'new_membership_tier_id', 'new_membership_tier_name',
        'ip', 'user_agent', 'country_code', 'meta', 'created_at',
    ];

    protected $casts = [
        'old_permissions' => 'array',
        'new_permissions' => 'array',
        'permissions_added' => 'array',
        'permissions_removed' => 'array',
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
```

- [ ] **Step 3: Run migration**

Run: `php artisan migrate --path=database/migrations/2026_07_30_120000_create_role_permission_audit_logs_table.php`  
Expected: Migrated successfully.

- [ ] **Step 4: Commit**

```bash
git add database/migrations/2026_07_30_120000_create_role_permission_audit_logs_table.php app/Models/RolePermissionAuditLog.php
git commit -m "feat: add role_permission_audit_logs table and model"
```

---

### Task 2: RolePermissionAuditLogger service

**Files:**
- Create: `app/Services/RolePermissionAuditLogger.php`
- Test: `tests/Unit/RolePermissionAuditLoggerTest.php`

**Interfaces:**
- Produces:
  - `RolePermissionAuditLogger::normalizePermissions(iterable $names): array` — sorted unique trimmed strings
  - `RolePermissionAuditLogger::diffPermissions(array $old, array $new): array{added: array, removed: array}`
  - `RolePermissionAuditLogger::hasMeaningfulChange(array $payload): bool`
  - `RolePermissionAuditLogger::log(array $payload): ?RolePermissionAuditLog` — never throws
- Payload keys match model fillable; `action` and `source` required. Actor auto-filled from `auth()->user()` when omitted. Request `ip` / `user_agent` auto-filled when omitted.

- [ ] **Step 1: Write failing unit tests**

```php
<?php

namespace Tests\Unit;

use App\Models\RolePermissionAuditLog;
use App\Models\User;
use App\Services\RolePermissionAuditLogger;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Support\CreatesApiUsers;
use Tests\TestCase;

class RolePermissionAuditLoggerTest extends TestCase
{
    use CreatesApiUsers;
    use DatabaseTransactions;

    public function test_diff_permissions_computes_added_and_removed(): void
    {
        $logger = new RolePermissionAuditLogger();
        $diff = $logger->diffPermissions(['A', 'B'], ['B', 'C']);

        $this->assertSame(['C'], $diff['added']);
        $this->assertSame(['A'], $diff['removed']);
    }

    public function test_log_skips_noop_when_permissions_unchanged(): void
    {
        $logger = new RolePermissionAuditLogger();
        $result = $logger->log([
            'action' => 'member_permissions_updated',
            'source' => 'pma',
            'old_permissions' => ['Manage Chat'],
            'new_permissions' => ['Manage Chat'],
            'old_role_name' => 'MEMBER_SOVEREIGN',
            'new_role_name' => 'MEMBER_SOVEREIGN',
            'old_user_type' => 'Regional',
            'new_user_type' => 'Regional',
        ]);

        $this->assertNull($result);
        $this->assertSame(0, RolePermissionAuditLog::count());
    }

    public function test_log_persists_row_and_never_throws_on_success(): void
    {
        $actor = $this->createApiUser(['first_name' => 'Actor', 'last_name' => 'One']);
        $this->actingAs($actor);

        $target = $this->createApiUser(['first_name' => 'Target', 'last_name' => 'Two', 'country' => 1]);

        $logger = new RolePermissionAuditLogger();
        $row = $logger->log([
            'action' => 'member_role_updated',
            'source' => 'pma',
            'target_user_id' => $target->id,
            'target_user_name' => 'Target Two',
            'target_user_email' => $target->email,
            'target_country_id' => $target->country,
            'old_role_name' => 'OLD',
            'new_role_name' => 'NEW',
            'old_permissions' => ['A'],
            'new_permissions' => ['A', 'B'],
        ]);

        $this->assertInstanceOf(RolePermissionAuditLog::class, $row);
        $this->assertSame('member_role_updated', $row->action);
        $this->assertSame($actor->id, $row->actor_id);
        $this->assertSame(['B'], $row->permissions_added);
        $this->assertSame([], $row->permissions_removed);
    }
}
```

- [ ] **Step 2: Run tests — expect FAIL**

Run: `php artisan test --filter=RolePermissionAuditLoggerTest`  
Expected: FAIL (class not found).

- [ ] **Step 3: Implement service**

```php
<?php

namespace App\Services;

use App\Models\RolePermissionAuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

class RolePermissionAuditLogger
{
    public function normalizePermissions(iterable $names): array
    {
        return collect($names)
            ->map(fn ($n) => trim((string) $n))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public function diffPermissions(array $old, array $new): array
    {
        $old = $this->normalizePermissions($old);
        $new = $this->normalizePermissions($new);

        return [
            'added' => array_values(array_diff($new, $old)),
            'removed' => array_values(array_diff($old, $new)),
        ];
    }

    public function hasMeaningfulChange(array $payload): bool
    {
        if (($payload['old_role_name'] ?? null) !== ($payload['new_role_name'] ?? null)) {
            return true;
        }
        if (($payload['old_user_type'] ?? null) !== ($payload['new_user_type'] ?? null)) {
            return true;
        }
        if (($payload['old_membership_tier_id'] ?? null) != ($payload['new_membership_tier_id'] ?? null)) {
            return true;
        }
        if (($payload['role_template_name'] ?? null) && in_array($payload['action'] ?? '', [
            'role_template_created', 'role_template_deleted',
        ], true)) {
            return true;
        }

        $old = $this->normalizePermissions($payload['old_permissions'] ?? []);
        $new = $this->normalizePermissions($payload['new_permissions'] ?? []);

        return $old !== $new;
    }

    public function log(array $payload): ?RolePermissionAuditLog
    {
        try {
            if (empty($payload['action']) || empty($payload['source'])) {
                return null;
            }

            $oldPerms = $this->normalizePermissions($payload['old_permissions'] ?? []);
            $newPerms = $this->normalizePermissions($payload['new_permissions'] ?? []);
            $payload['old_permissions'] = $oldPerms;
            $payload['new_permissions'] = $newPerms;

            $diff = $this->diffPermissions($oldPerms, $newPerms);
            $payload['permissions_added'] = $payload['permissions_added'] ?? $diff['added'];
            $payload['permissions_removed'] = $payload['permissions_removed'] ?? $diff['removed'];

            if (!$this->hasMeaningfulChange($payload)) {
                return null;
            }

            /** @var User|null $actor */
            $actor = auth()->user();
            if (!isset($payload['actor_id']) && $actor) {
                $payload['actor_id'] = $actor->id;
                $payload['actor_name'] = trim(($actor->first_name ?? '') . ' ' . ($actor->last_name ?? ''));
                $payload['actor_email'] = $actor->email;
            }

            if (!isset($payload['ip'])) {
                $payload['ip'] = request()->ip();
            }
            if (!isset($payload['user_agent'])) {
                $payload['user_agent'] = request()->userAgent();
            }

            $payload['created_at'] = $payload['created_at'] ?? now();

            return RolePermissionAuditLog::create($payload);
        } catch (Throwable $e) {
            Log::error('RolePermissionAuditLogger failed: ' . $e->getMessage(), [
                'action' => $payload['action'] ?? null,
            ]);

            return null;
        }
    }
}
```

- [ ] **Step 4: Run tests — expect PASS**

Run: `php artisan test --filter=RolePermissionAuditLoggerTest`  
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Services/RolePermissionAuditLogger.php tests/Unit/RolePermissionAuditLoggerTest.php
git commit -m "feat: add RolePermissionAuditLogger with diff and no-op skip"
```

---

### Task 3: PartnerVisibility helper

**Files:**
- Create: `app/Support/PartnerVisibility.php`
- Modify: `app/Http/Controllers/User/PartnerController.php` (optional reuse later; required for audit controller)

**Interfaces:**
- Produces:
  - `PartnerVisibility::canAccessAudit(?User $user): bool`
  - `PartnerVisibility::visiblePartnerIdsQuery(User $viewer): \Illuminate\Database\Eloquent\Builder` — `User` query matching Members List visibility (exclude SUPER ADMIN role members; same Global / Regional / G_R / ecclesia rules as `PartnerController@index` lines 58–178)
  - `PartnerVisibility::viewerCanSeePartner(User $viewer, User $partner): bool`
  - `PartnerVisibility::constrainAuditQuery($query, User $viewer): void` — restricts audit rows: `target_user_id` in visible partners; OR (`target_user_id` null AND viewer is Super Admin or `user_type == 'Global'`)

Copy the visibility filters from `PartnerController@index` into `visiblePartnerIdsQuery` (start from `User::query()->leftJoin('user_types as ut', ...)` same as index). Do not invent new rules.

- [ ] **Step 1: Implement `PartnerVisibility.php`** with the four methods above (full filter body mirrored from PartnerController).

- [ ] **Step 2: Commit**

```bash
git add app/Support/PartnerVisibility.php
git commit -m "feat: extract PartnerVisibility for members and audit scoping"
```

---

### Task 4: Instrument PartnerController store/update

**Files:**
- Modify: `app/Http/Controllers/User/PartnerController.php`
- Test: covered in Task 8 feature tests

**Interfaces:**
- Consumes: `RolePermissionAuditLogger::log`
- Before mutation: snapshot `old_role_name` (from `$partner->userRole->name` / Spatie custom role), `old_user_type`, `old_permissions` (custom role permission names via `MembershipPrivilegeService::resolveCustomRole` or `$user->getAllPermissions()` consistent with edit form `currentPermissions`), `old_membership_tier_id/name`.
- After successful save: `log` with `action` `member_role_created` (store) or `member_role_updated` (update), `source` => `pma`, target snapshots, new fields.

- [ ] **Step 1: In `store`, after `assignRole` / permission sync succeeds**, call:

```php
app(RolePermissionAuditLogger::class)->log([
    'action' => 'member_role_created',
    'source' => 'pma',
    'target_user_id' => $data->id,
    'target_user_name' => trim($data->first_name . ' ' . $data->last_name),
    'target_user_email' => $data->email,
    'target_country_id' => $data->country,
    'old_role_name' => null,
    'new_role_name' => $the_role->name,
    'old_user_type' => null,
    'new_user_type' => $data->user_type,
    'old_permissions' => [],
    'new_permissions' => $request->permissions ?? $data->getPermissionNames()->all(),
    'new_membership_tier_id' => $request->membership_tier_id ?? null,
    // resolve tier name if present
]);
```

- [ ] **Step 2: In `update`, capture before-state before writes; after sync**, log `member_role_updated` with old/new role, user_type, permissions, tier. Prefer one combined row (spec).

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/User/PartnerController.php
git commit -m "feat: audit member role and permission changes from Partners PMA"
```

---

### Task 5: Instrument RolePermissionsController + MembershipPrivilegeService + API

**Files:**
- Modify: `app/Http/Controllers/User/RolePermissionsController.php`
- Modify: `app/Services/MembershipPrivilegeService.php`
- Modify: `app/Services/PartnerMemberApiService.php`

**Interfaces:**
- Template store → `role_template_created` (`source=pma`, `role_template_id/name`, `new_permissions`, `meta.affected_users` = 0 or count)
- Template update → snapshot old `UserTypePermission` names before delete/recreate; `role_template_updated`; `meta.affected_users` = `User::where('user_type_id', $role->id)->count()`
- Template delete → before delete, snapshot permissions; `role_template_deleted`
- `MembershipPrivilegeService::syncTierPermissions` → before sync capture old perms; after sync `membership_privilege_synced`, `source=membership_sync`, target user + tier old/new (pass tier; old tier unknown → leave old tier null unless caller provides)
- `PartnerMemberApiService` role/permission sync → same logger, `source=api`

- [ ] **Step 1: Wire RolePermissionsController store/update/delete**

- [ ] **Step 2: Wire MembershipPrivilegeService::syncTierPermissions**

```php
// at start of syncTierPermissions
$oldPermissions = $userRole
    ? $userRole->permissions->pluck('name')->all()
    : $user->getDirectPermissions()->pluck('name')->all();

// after sync + cache clear
app(RolePermissionAuditLogger::class)->log([
    'action' => 'membership_privilege_synced',
    'source' => 'membership_sync',
    'target_user_id' => $user->id,
    'target_user_name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
    'target_user_email' => $user->email,
    'target_country_id' => $user->country,
    'old_permissions' => $oldPermissions,
    'new_permissions' => $this->tierPermissionNames($tier),
    'new_membership_tier_id' => $tier->id,
    'new_membership_tier_name' => $tier->name ?? $tier->title ?? (string) $tier->id,
]);
```

(Use the actual MembershipTier display attribute name present on the model.)

- [ ] **Step 3: Wire PartnerMemberApiService create/update permission syncs** similarly with `source=api`.

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/User/RolePermissionsController.php app/Services/MembershipPrivilegeService.php app/Services/PartnerMemberApiService.php
git commit -m "feat: audit role templates, membership sync, and API permission changes"
```

---

### Task 6: Audit controller + export + routes

**Files:**
- Create: `app/Http/Controllers/User/RolePermissionAuditLogController.php`
- Create: `app/Exports/RolePermissionAuditLogsExport.php`
- Modify: `routes/web.php`

**Interfaces:**
- `index(Request)` — global list
- `member(Request, $id)` — decrypt `$id`, 403 if `!viewerCanSeePartner`, filter `target_user_id`
- `export(Request)` / `memberExport(Request, $id)` — Excel download
- Shared private `baseQuery(Request $request, User $viewer, ?int $targetUserId = null)`

- [ ] **Step 1: Export class**

```php
<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RolePermissionAuditLogsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(protected Collection $rows) {}

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Date', 'Action', 'Source', 'Actor', 'Actor Email',
            'Target Member', 'Target Email', 'Old Role', 'New Role',
            'Old User Type', 'New User Type', 'Permissions Added',
            'Permissions Removed', 'IP',
        ];
    }

    public function map($row): array
    {
        return [
            optional($row->created_at)?->format('Y-m-d H:i:s'),
            $row->action,
            $row->source,
            $row->actor_name,
            $row->actor_email,
            $row->target_user_name,
            $row->target_user_email,
            $row->old_role_name,
            $row->new_role_name,
            $row->old_user_type,
            $row->new_user_type,
            implode(', ', $row->permissions_added ?? []),
            implode(', ', $row->permissions_removed ?? []),
            $row->ip,
        ];
    }
}
```

- [ ] **Step 2: Controller** — gate with `PartnerVisibility::canAccessAudit`; apply `constrainAuditQuery`; filters: `date_from`, `date_to`, `action`, `source`, `actor` (name/email like), `target` (like; ignore when member mode), `role` (old or new role like); `paginate(15)`; export uses `->limit(5000)->get()` with same filters.

- [ ] **Step 3: Register routes** next to other partner helper routes (~1068), and `use` the controller at top of `web.php`:

```php
use App\Http\Controllers\User\RolePermissionAuditLogController;

Route::get('/partner-audit-logs', [RolePermissionAuditLogController::class, 'index'])->name('partners.audit-logs');
Route::get('/partner-audit-logs/export', [RolePermissionAuditLogController::class, 'export'])->name('partners.audit-logs.export');
Route::get('/partner-audit-logs/{id}', [RolePermissionAuditLogController::class, 'member'])->name('partners.audit-logs.member');
Route::get('/partner-audit-logs/{id}/export', [RolePermissionAuditLogController::class, 'memberExport'])->name('partners.audit-logs.member.export');
```

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/User/RolePermissionAuditLogController.php app/Exports/RolePermissionAuditLogsExport.php routes/web.php
git commit -m "feat: add role permission audit log PMA routes and export"
```

---

### Task 7: Blade UI (list button, row icon, audit page)

**Files:**
- Create: `resources/views/user/partner/audit-logs.blade.php`
- Modify: `resources/views/user/partner/list.blade.php`
- Modify: `resources/views/user/partner/table.blade.php`

- [ ] **Step 1: Header button** in `list.blade.php` immediately after Export Report:

```blade
@if (\App\Support\PartnerVisibility::canAccessAudit(auth()->user()))
    <a href="{{ route('partners.audit-logs') }}" class="btn btn-primary">
        <i class="ti ti-history"></i> Audit Logs
    </a>
@endif
```

- [ ] **Step 2: Row icon** in `table.blade.php` actions `<div class="d-flex">` (with edit/view/delete):

```blade
@if (\App\Support\PartnerVisibility::canAccessAudit(auth()->user()))
    <a href="{{ route('partners.audit-logs.member', Crypt::encrypt($partner->id)) }}"
       class="view_icon me-2" title="Audit Logs">
        <i class="ti ti-history"></i>
    </a>
@endif
```

- [ ] **Step 3: Audit page** — extend `user.layouts.master`; title switches on `$targetUser`; filter form GET; table columns per spec; expandable `<tr>` or Bootstrap collapse for full permission lists / meta; Export button linking to export route with query string; empty state “No audit logs yet”; back link to Members List.

- [ ] **Step 4: Commit**

```bash
git add resources/views/user/partner/audit-logs.blade.php resources/views/user/partner/list.blade.php resources/views/user/partner/table.blade.php
git commit -m "feat: add Audit Logs UI on members list and audit page"
```

---

### Task 8: Feature tests

**Files:**
- Create: `tests/Feature/RolePermissionAuditLogTest.php`

**Interfaces:**
- Use `CreatesApiUsers`, `DatabaseTransactions`, `withoutMiddleware` list from `ChangeLogManageTest`.
- Grant `Manage Partners` via `Permission::findOrCreate` + `givePermissionTo`.
- For Super Admin checks use `hasNewRole` setup consistent with project (assign Spatie/UserType SUPER ADMIN as other tests do, or mark user appropriately — inspect an existing SA test if present; otherwise test Manage Partners path primarily).

- [ ] **Step 1: Write tests**

```php
<?php

namespace Tests\Feature;

use App\Models\RolePermissionAuditLog;
use App\Models\User;
use App\Services\RolePermissionAuditLogger;
use App\Support\PartnerVisibility;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Crypt;
use Spatie\Permission\Models\Permission;
use Tests\Support\CreatesApiUsers;
use Tests\TestCase;

class RolePermissionAuditLogTest extends TestCase
{
    use CreatesApiUsers;
    use DatabaseTransactions;

    private function asAuditUser(User $user)
    {
        return $this->actingAs($user)->withoutMiddleware([
            \App\Http\Middleware\CheckAgreementSigned::class,
            \App\Http\Middleware\MemberAccess::class,
            \App\Http\Middleware\PreventBackHistory::class,
            \App\Http\Middleware\UserActivityLogger::class,
            \App\Http\Middleware\EnsureUserIsActive::class,
            \App\Http\Middleware\EnsureCanonicalCountryUrl::class,
            \App\Http\Middleware\EnsureUserInstanceAccess::class,
        ]);
    }

    private function manager(): User
    {
        Permission::findOrCreate('Manage Partners');
        $user = $this->createApiUser([
            'user_type' => 'Global',
            'membership_excluded' => true,
        ]);
        $user->givePermissionTo('Manage Partners');

        return $user->fresh();
    }

    public function test_guest_without_permission_forbidden(): void
    {
        $user = $this->createApiUser(['membership_excluded' => true]);
        $this->asAuditUser($user)
            ->get(route('partners.audit-logs'))
            ->assertForbidden();
    }

    public function test_manager_can_view_audit_index(): void
    {
        $user = $this->manager();
        $this->asAuditUser($user)
            ->get(route('partners.audit-logs'))
            ->assertOk();
    }

    public function test_logger_row_appears_on_member_audit_page(): void
    {
        $manager = $this->manager();
        $target = $this->createApiUser([
            'first_name' => 'Mem',
            'last_name' => 'Ber',
            'user_type' => 'Global',
            'membership_excluded' => true,
        ]);

        $this->actingAs($manager);
        app(RolePermissionAuditLogger::class)->log([
            'action' => 'member_role_updated',
            'source' => 'pma',
            'target_user_id' => $target->id,
            'target_user_name' => 'Mem Ber',
            'target_user_email' => $target->email,
            'target_country_id' => $target->country,
            'old_role_name' => 'A',
            'new_role_name' => 'B',
            'old_permissions' => [],
            'new_permissions' => ['Manage Chat'],
        ]);

        $this->asAuditUser($manager)
            ->get(route('partners.audit-logs.member', Crypt::encrypt($target->id)))
            ->assertOk()
            ->assertSee('member_role_updated')
            ->assertSee('Mem Ber');
    }

    public function test_export_returns_successful_download(): void
    {
        $user = $this->manager();
        $this->asAuditUser($user)
            ->get(route('partners.audit-logs.export'))
            ->assertOk();
    }
}
```

Add at least one Regional visibility assertion: Regional manager must not open another country’s member audit (`assertForbidden`).

Also add a unit/feature assertion that `MembershipPrivilegeService::syncTierPermissions` creates `membership_privilege_synced` when permissions change (may live in same file or extend Unit suite).

- [ ] **Step 2: Run tests**

Run: `php artisan test --filter=RolePermissionAuditLog`  
Expected: PASS (fix PartnerVisibility / country fixtures if Regional test needs Country rows).

- [ ] **Step 3: Commit**

```bash
git add tests/Feature/RolePermissionAuditLogTest.php
git commit -m "test: cover role permission audit access, member view, and export"
```

---

### Task 9: PMA documentation

**Files:**
- Modify: `docs/pma/all-members.md`
- Modify: `docs/pma/role-permission.md`

- [ ] **Step 1: Update all-members.md** — set `updated: 2026-07-30`; document Audit Logs header button, per-row history icon, routes, access gate, visibility, export.

- [ ] **Step 2: Update role-permission.md** — set `updated: 2026-07-30`; note template create/update/delete writes to role permission audit logs viewable from Members List.

- [ ] **Step 3: Commit**

```bash
git add docs/pma/all-members.md docs/pma/role-permission.md
git commit -m "docs: document role and permission audit logs in PMA hubs"
```

---

## Spec coverage checklist

| Spec requirement | Task |
|------------------|------|
| Dedicated table + fields | 1 |
| Logger + no-op + try/catch | 2 |
| Members List visibility | 3, 6 |
| Member create/edit logging | 4 |
| Role template logging | 5 |
| Membership privilege sync logging | 5 |
| API source logging | 5 |
| Access gate SA / Manage Partners / Manage Role Permission | 3, 6 |
| Global + per-member UI + export | 6, 7 |
| Feature tests | 8 |
| PMA docs | 9 |
| No new Spatie permission | Global Constraints |
| Forward-only | Global Constraints |

## Placeholder / consistency review

- Action names and `source` values match the design spec vocabulary.
- Route names: `partners.audit-logs`, `.export`, `.member`, `.member.export`.
- Logger method is always `log(array $payload): ?RolePermissionAuditLog`.
- Model has no SoftDeletes / no `updated_at`.
