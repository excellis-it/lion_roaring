<?php

namespace Tests\Unit;

use App\Models\RolePermissionAuditLog;
use App\Services\RolePermissionAuditLogger;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Log;
use Tests\Support\CreatesApiUsers;
use Tests\TestCase;

class RolePermissionAuditLoggerTest extends TestCase
{
    use CreatesApiUsers;
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        RolePermissionAuditLog::query()->delete();
    }

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

    public function test_build_field_changes_includes_only_changed_fields_and_masks_password(): void
    {
        $logger = new RolePermissionAuditLogger();
        $changes = $logger->buildFieldChanges(
            [
                'email' => 'a@example.com',
                'city' => 'Austin',
                'password' => null,
            ],
            [
                'email' => 'b@example.com',
                'city' => 'Austin',
                'password' => true,
            ]
        );

        $this->assertCount(2, $changes);
        $this->assertSame('email', $changes[0]['field']);
        $this->assertSame('a@example.com', $changes[0]['old']);
        $this->assertSame('b@example.com', $changes[0]['new']);
        $this->assertSame('password', $changes[1]['field']);
        $this->assertSame('(changed)', $changes[1]['new']);
    }

    public function test_log_persists_field_changes_for_member_updated(): void
    {
        $actor = $this->createApiUser(['first_name' => 'Actor', 'last_name' => 'One']);
        $this->actingAs($actor);
        $target = $this->createApiUser(['first_name' => 'Target', 'last_name' => 'Two', 'country' => 1]);

        $logger = new RolePermissionAuditLogger();
        $row = $logger->log([
            'action' => 'member_updated',
            'source' => 'pma',
            'target_user_id' => $target->id,
            'target_user_name' => 'Target Two',
            'target_user_email' => $target->email,
            'target_country_id' => $target->country,
            'field_changes' => [
                [
                    'field' => 'email',
                    'label' => 'Email',
                    'old' => 'old@example.com',
                    'new' => 'new@example.com',
                ],
            ],
        ]);

        $this->assertInstanceOf(RolePermissionAuditLog::class, $row);
        $this->assertSame('member_updated', $row->action);
        $this->assertSame('email', $row->field_changes[0]['field']);
    }

    public function test_log_persists_row_and_never_throws_on_success(): void
    {
        $actor = $this->createApiUser(['first_name' => 'Actor', 'last_name' => 'One']);
        $this->actingAs($actor);

        $target = $this->createApiUser(['first_name' => 'Target', 'last_name' => 'Two', 'country' => 1]);

        $logger = new RolePermissionAuditLogger();
        $row = $logger->log([
            'action' => 'member_updated',
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
        $this->assertSame('member_updated', $row->action);
        $this->assertSame($actor->id, $row->actor_id);
        $this->assertSame(['B'], $row->permissions_added);
        $this->assertSame([], $row->permissions_removed);
    }

    public function test_has_meaningful_change_for_rename_only_role_template_update(): void
    {
        $logger = new RolePermissionAuditLogger();

        $this->assertTrue($logger->hasMeaningfulChange([
            'action' => 'role_template_updated',
            'old_role_name' => 'OLD_TEMPLATE',
            'new_role_name' => 'NEW_TEMPLATE',
            'old_permissions' => ['Manage Chat'],
            'new_permissions' => ['Manage Chat'],
        ]));
    }

    public function test_has_meaningful_change_for_flag_only_role_template_update(): void
    {
        $logger = new RolePermissionAuditLogger();

        $this->assertTrue($logger->hasMeaningfulChange([
            'action' => 'role_template_updated',
            'old_permissions' => ['Manage Chat'],
            'new_permissions' => ['Manage Chat'],
            'meta' => [
                'old_is_admin' => false,
                'new_is_admin' => true,
                'old_is_ecclesia' => false,
                'new_is_ecclesia' => false,
            ],
        ]));
    }

    public function test_log_returns_null_and_logs_error_when_create_fails(): void
    {
        Log::shouldReceive('error')
            ->once()
            ->withArgs(function (string $message, array $context): bool {
                return str_contains($message, 'RolePermissionAuditLogger failed:')
                    && ($context['action'] ?? null) === 'member_updated';
            });

        $logger = new RolePermissionAuditLogger();
        $result = $logger->log([
            'action' => 'member_updated',
            'source' => 'pma',
            'target_user_id' => 999999999,
            'old_role_name' => 'OLD',
            'new_role_name' => 'NEW',
            'old_permissions' => ['A'],
            'new_permissions' => ['B'],
        ]);

        $this->assertNull($result);
        $this->assertSame(0, RolePermissionAuditLog::count());
    }
}
