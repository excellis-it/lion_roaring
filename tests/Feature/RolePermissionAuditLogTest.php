<?php

namespace Tests\Feature;

use App\Models\MembershipTier;
use App\Models\RolePermissionAuditLog;
use App\Models\User;
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
            ->assertSee('Member Role Updated')
            ->assertSee('Mem Ber');
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
        $this->asAuditUser($user)
            ->get(route('partners.audit-logs.export'))
            ->assertOk();
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
