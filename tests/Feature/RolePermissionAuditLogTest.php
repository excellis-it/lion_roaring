<?php

namespace Tests\Feature;

use App\Models\MembershipTier;
use App\Models\RolePermissionAuditLog;
use App\Models\User;
use App\Models\UserType;
use App\Models\UserTypePermission;
use App\Services\MembershipPrivilegeService;
use App\Services\RolePermissionAuditLogger;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
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

    private function regionalManager(int $countryId): User
    {
        Permission::findOrCreate('Manage Partners');

        $user = $this->createApiUser([
            'user_type' => 'Regional',
            'country' => $countryId,
            'membership_excluded' => true,
            'status' => 1,
        ]);
        $user->givePermissionTo('Manage Partners');

        return $user->fresh();
    }

    private function roleTemplateAdmin(): User
    {
        $adminType = new UserType();
        $adminType->name = 'SUPER ADMIN TEST ' . Str::random(6);
        $adminType->type = '1';
        $adminType->guard_name = 'web';
        $adminType->is_ecclesia = 0;
        $adminType->is_admin = 1;
        $adminType->save();

        return $this->createApiUser([
            'user_type' => 'Global',
            'user_type_id' => $adminType->id,
            'membership_excluded' => true,
        ])->fresh();
    }

    private function pmaRoleTemplate(array $overrides = []): UserType
    {
        $role = new UserType();
        $role->name = $overrides['name'] ?? ('Audit PMA Role ' . Str::random(6));
        $role->type = $overrides['type'] ?? '2';
        $role->guard_name = $overrides['guard_name'] ?? 'web';
        $role->is_ecclesia = $overrides['is_ecclesia'] ?? 0;
        $role->is_admin = $overrides['is_admin'] ?? 0;
        $role->save();

        return $role;
    }

    public function test_user_without_permission_forbidden(): void
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
            'action' => 'member_updated',
            'source' => 'pma',
            'target_user_id' => $target->id,
            'target_user_name' => 'Mem Ber',
            'target_user_email' => $target->email,
            'target_country_id' => $target->country,
            'old_role_name' => 'A',
            'new_role_name' => 'B',
            'old_permissions' => [],
            'new_permissions' => ['Manage Chat'],
            'field_changes' => [
                [
                    'field' => 'email',
                    'label' => 'Email',
                    'old' => 'old@example.com',
                    'new' => $target->email,
                ],
            ],
        ]);

        $this->asAuditUser($manager)
            ->get(route('partners.audit-logs.member', Crypt::encrypt($target->id)))
            ->assertOk()
            ->assertSee('Member Updated')
            ->assertSee('Mem Ber')
            ->assertSee('Email');
    }

    public function test_regional_manager_cannot_view_other_country_member_audit(): void
    {
        // PartnerVisibility compares users.country only — no Country table rows required.
        $manager = $this->regionalManager(99001);
        $target = $this->createApiUser([
            'first_name' => 'Other',
            'last_name' => 'Country',
            'user_type' => 'Regional',
            'country' => 99002,
            'membership_excluded' => true,
            'status' => 1,
        ]);

        $this->asAuditUser($manager)
            ->get(route('partners.audit-logs.member', Crypt::encrypt($target->id)))
            ->assertForbidden();
    }

    public function test_export_returns_successful_download(): void
    {
        $user = $this->manager();
        $response = $this->asAuditUser($user)
            ->get(route('partners.audit-logs.export'));

        $response->assertOk();

        $disposition = $response->headers->get('content-disposition');
        $this->assertNotNull($disposition);
        $this->assertStringContainsString('role_permission_audit', $disposition);
        $this->assertStringContainsString('.xlsx', $disposition);
    }

    public function test_member_export_returns_successful_download(): void
    {
        $manager = $this->manager();
        $target = $this->createApiUser([
            'first_name' => 'Mem',
            'last_name' => 'Ber',
            'user_type' => 'Global',
            'membership_excluded' => true,
        ]);

        $response = $this->asAuditUser($manager)
            ->get(route('partners.audit-logs.member.export', Crypt::encrypt($target->id)));

        $response->assertOk();

        $disposition = $response->headers->get('content-disposition');
        $this->assertNotNull($disposition);
        $this->assertStringContainsString('role_permission_audit', $disposition);
        $this->assertStringContainsString('.xlsx', $disposition);
    }

    public function test_role_template_store_creates_audit_log(): void
    {
        $admin = $this->roleTemplateAdmin();
        $perm = Permission::findOrCreate('Audit Test Role Template Perm');
        $roleName = 'Audit Created Role ' . Str::random(6);

        $this->asAuditUser($admin)
            ->post(route('roles.store'), [
                'role_name' => $roleName,
                'is_ecclesia' => '0',
                'is_admin' => '0',
                'permissions' => [$perm->name],
            ])
            ->assertRedirect(route('roles.index'));

        $role = UserType::where('name', $roleName)->first();
        $this->assertNotNull($role);

        $this->assertDatabaseHas('role_permission_audit_logs', [
            'action' => 'role_template_created',
            'source' => 'pma',
            'role_template_id' => $role->id,
            'role_template_name' => $roleName,
            'actor_id' => $admin->id,
        ]);
    }

    public function test_role_template_update_creates_audit_log(): void
    {
        $admin = $this->roleTemplateAdmin();
        $perm = Permission::findOrCreate('Audit Test Role Update Perm');
        $role = $this->pmaRoleTemplate();
        $oldName = $role->name;

        UserTypePermission::create([
            'user_type_id' => $role->id,
            'permission_id' => $perm->id,
        ]);

        $newName = 'Audit Updated Role ' . Str::random(6);

        $this->asAuditUser($admin)
            ->put(route('roles.update', Crypt::encrypt($role->id)), [
                'role_name' => $newName,
                'is_ecclesia' => '0',
                'is_admin' => '0',
                'permissions' => [$perm->name],
            ])
            ->assertRedirect(route('roles.index'));

        $this->assertDatabaseHas('role_permission_audit_logs', [
            'action' => 'role_template_updated',
            'source' => 'pma',
            'role_template_id' => $role->id,
            'role_template_name' => $newName,
            'old_role_name' => $oldName,
            'new_role_name' => $newName,
            'actor_id' => $admin->id,
        ]);
    }

    public function test_sync_tier_permissions_creates_membership_privilege_synced_audit_log(): void
    {
        $permOld = Permission::findOrCreate('Audit Test Perm Old');
        $permNew = Permission::findOrCreate('Audit Test Perm New');

        $tier = MembershipTier::create([
            'name' => 'Audit Test Tier ' . Str::random(4),
            'slug' => 'audit-test-tier-' . Str::lower(Str::random(8)),
            'permissions' => $permNew->name,
        ]);

        $user = $this->createApiUser(['membership_excluded' => true]);
        $user->syncPermissions([$permOld->name]);

        app(MembershipPrivilegeService::class)->syncTierPermissions($user->fresh(), $tier);

        $this->assertDatabaseHas('role_permission_audit_logs', [
            'action' => 'membership_privilege_synced',
            'source' => 'membership_sync',
            'target_user_id' => $user->id,
            'new_membership_tier_id' => $tier->id,
        ]);

        $log = RolePermissionAuditLog::where('action', 'membership_privilege_synced')
            ->where('target_user_id', $user->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertContains($permNew->name, $log->permissions_added);
        $this->assertContains($permOld->name, $log->permissions_removed);
    }

    protected function tearDown(): void
    {
        User::query()->where('email', 'like', '%@api-contract.test')->forceDelete();
        parent::tearDown();
    }
}
