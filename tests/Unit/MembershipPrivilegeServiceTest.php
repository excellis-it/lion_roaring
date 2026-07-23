<?php

namespace Tests\Unit;

use App\Models\MembershipTier;
use App\Models\User;
use App\Models\UserType;
use App\Services\MembershipPrivilegeService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class MembershipPrivilegeServiceTest extends TestCase
{
    private MembershipPrivilegeService $service;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => false,
            ],
            'permission.cache.expiration_time' => \DateInterval::createFromDateString('0 seconds'),
        ]);

        DB::purge('sqlite');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->createSchema();
        $this->service = new MembershipPrivilegeService();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('membership_tiers');
        Schema::dropIfExists('user_types');
        Schema::dropIfExists('users');
        DB::purge('sqlite');

        parent::tearDown();
    }

    public function test_same_tier_with_extra_permissions_preserves_role_permissions(): void
    {
        [$tier1] = $this->seedTiers();
        $memberType = $this->seedMemberSovereign();
        $view = Permission::create(['name' => 'View Reports', 'guard_name' => 'web']);
        $manage = Permission::create(['name' => 'Manage Partners', 'guard_name' => 'web']);

        $user = $this->makeUser([
            'user_type' => 'Regional',
            'user_type_id' => $memberType->id,
        ]);
        $role = Role::create(['name' => 'john-doe', 'guard_name' => 'web', 'type' => '2', 'is_ecclesia' => 0]);
        $role->syncPermissions([$view->name, $manage->name]);
        $user->assignRole($role);

        $this->service->applyAfterTierChange($user->fresh(), $tier1, (int) $tier1->id);

        $perms = $role->fresh()->permissions->pluck('name')->sort()->values()->all();
        $this->assertSame(['Manage Partners', 'View Reports'], $perms);
    }

    public function test_same_tier_matching_permissions_is_noop(): void
    {
        [$tier1] = $this->seedTiers();
        $memberType = $this->seedMemberSovereign();
        Permission::create(['name' => 'View Reports', 'guard_name' => 'web']);

        $user = $this->makeUser([
            'user_type' => 'Regional',
            'user_type_id' => $memberType->id,
        ]);
        $role = Role::create(['name' => 'plain-member', 'guard_name' => 'web', 'type' => '2', 'is_ecclesia' => 0]);
        $role->syncPermissions(['View Reports']);
        $user->assignRole($role);

        $this->service->applyAfterTierChange($user->fresh(), $tier1, (int) $tier1->id);

        $this->assertSame(['View Reports'], $role->fresh()->permissions->pluck('name')->all());
    }

    public function test_upgrade_syncs_new_tier_permissions(): void
    {
        [$tier1, $tier2] = $this->seedTiers();
        $memberType = $this->seedMemberSovereign();
        Permission::create(['name' => 'View Reports', 'guard_name' => 'web']);
        Permission::create(['name' => 'View Gold Features', 'guard_name' => 'web']);

        $user = $this->makeUser([
            'user_type' => 'Regional',
            'user_type_id' => $memberType->id,
        ]);
        $role = Role::create(['name' => 'upgrader', 'guard_name' => 'web', 'type' => '2', 'is_ecclesia' => 0]);
        $role->syncPermissions(['View Reports']);
        $user->assignRole($role);

        $this->service->applyAfterTierChange($user->fresh(), $tier2, (int) $tier1->id);

        $this->assertSame(['View Gold Features'], $role->fresh()->permissions->pluck('name')->all());
    }

    public function test_downgrade_from_ecclesia_with_one_manage_sets_ecclesia_id(): void
    {
        [$tier1, $tier2] = $this->seedTiers();
        $memberType = $this->seedMemberSovereign();
        $ecclesiaType = UserType::create([
            'name' => 'ECCLESIA',
            'type' => '2',
            'is_ecclesia' => 1,
            'is_admin' => 0,
        ]);
        Permission::create(['name' => 'View Reports', 'guard_name' => 'web']);
        Permission::create(['name' => 'Manage Ecclesia', 'guard_name' => 'web']);

        $user = $this->makeUser([
            'user_type' => 'G_R',
            'user_type_id' => $ecclesiaType->id,
            'is_ecclesia_admin' => 1,
            'manage_ecclesia' => '42',
            'ecclesia_id' => null,
        ]);
        $role = Role::create(['name' => 'ecc-admin', 'guard_name' => 'web', 'type' => '2', 'is_ecclesia' => 1]);
        $role->syncPermissions(['Manage Ecclesia']);
        $user->assignRole($role);

        $this->service->applyAfterTierChange($user->fresh(), $tier1, (int) $tier2->id);

        $user->refresh();
        $this->assertSame('Regional', $user->user_type);
        $this->assertSame($memberType->id, $user->user_type_id);
        $this->assertSame(0, (int) $user->is_ecclesia_admin);
        $this->assertNull($user->manage_ecclesia);
        $this->assertSame(42, (int) $user->ecclesia_id);
        $this->assertSame(0, (int) $role->fresh()->is_ecclesia);
        $this->assertSame(['View Reports'], $role->fresh()->permissions->pluck('name')->all());
    }

    public function test_downgrade_with_multiple_manage_ecclesia_clears_ecclesia_id(): void
    {
        [$tier1, $tier2] = $this->seedTiers();
        $this->seedMemberSovereign();
        $ecclesiaType = UserType::create([
            'name' => 'ECCLESIA',
            'type' => '2',
            'is_ecclesia' => 1,
            'is_admin' => 0,
        ]);
        Permission::create(['name' => 'View Reports', 'guard_name' => 'web']);

        $user = $this->makeUser([
            'user_type' => 'G_R',
            'user_type_id' => $ecclesiaType->id,
            'is_ecclesia_admin' => 1,
            'manage_ecclesia' => '10,20',
            'ecclesia_id' => null,
        ]);
        $role = Role::create(['name' => 'multi-ecc', 'guard_name' => 'web', 'type' => '2', 'is_ecclesia' => 1]);
        $user->assignRole($role);

        $this->service->applyAfterTierChange($user->fresh(), $tier1, (int) $tier2->id);

        $user->refresh();
        $this->assertNull($user->ecclesia_id);
        $this->assertSame('Regional', $user->user_type);
    }

    public function test_downgrade_global_keeps_existing_ecclesia_id(): void
    {
        [$tier1, $tier2] = $this->seedTiers();
        $memberType = $this->seedMemberSovereign();
        $otherType = UserType::create([
            'name' => 'WAREHOUSE_ADMIN',
            'type' => '2',
            'is_ecclesia' => 0,
            'is_admin' => 0,
        ]);
        Permission::create(['name' => 'View Reports', 'guard_name' => 'web']);
        Permission::create(['name' => 'Warehouse Access', 'guard_name' => 'web']);

        $user = $this->makeUser([
            'user_type' => 'Global',
            'user_type_id' => $otherType->id,
            'is_ecclesia_admin' => 0,
            'manage_ecclesia' => null,
            'ecclesia_id' => 7,
        ]);
        $role = Role::create(['name' => 'global-assigned', 'guard_name' => 'web', 'type' => '2', 'is_ecclesia' => 0]);
        $role->syncPermissions(['Warehouse Access']);
        $user->assignRole($role);

        $this->service->applyAfterTierChange($user->fresh(), $tier1, (int) $tier2->id);

        $user->refresh();
        $this->assertSame('Regional', $user->user_type);
        $this->assertSame($memberType->id, $user->user_type_id);
        $this->assertSame(7, (int) $user->ecclesia_id);
        $this->assertSame(['View Reports'], $role->fresh()->permissions->pluck('name')->all());
    }

    public function test_resolve_change_type_matrix(): void
    {
        [$tier1, $tier2] = $this->seedTiers();

        $this->assertSame(
            MembershipPrivilegeService::CHANGE_NEW,
            $this->service->resolveChangeType($tier1, null)
        );
        $this->assertSame(
            MembershipPrivilegeService::CHANGE_SAME,
            $this->service->resolveChangeType($tier1, (int) $tier1->id)
        );
        $this->assertSame(
            MembershipPrivilegeService::CHANGE_UPGRADE,
            $this->service->resolveChangeType($tier2, (int) $tier1->id)
        );
        $this->assertSame(
            MembershipPrivilegeService::CHANGE_DOWNGRADE,
            $this->service->resolveChangeType($tier1, (int) $tier2->id)
        );
    }

    public function test_upgrade_to_empty_permissions_clears_custom_role(): void
    {
        [$tier1] = $this->seedTiers();
        $emptyTier = MembershipTier::create([
            'name' => 'Empty Tier',
            'slug' => 'empty',
            'permissions' => null,
        ]);
        $memberType = $this->seedMemberSovereign();
        Permission::create(['name' => 'View Reports', 'guard_name' => 'web']);

        $user = $this->makeUser([
            'user_type' => 'Regional',
            'user_type_id' => $memberType->id,
        ]);
        $role = Role::create(['name' => 'clear-me', 'guard_name' => 'web', 'type' => '2', 'is_ecclesia' => 0]);
        $role->syncPermissions(['View Reports']);
        $user->assignRole($role);

        $this->service->applyAfterTierChange($user->fresh(), $emptyTier, (int) $tier1->id);

        $this->assertSame([], $role->fresh()->permissions->pluck('name')->all());
    }

    public function test_soft_deleted_previous_tier_still_downgrades_correctly(): void
    {
        [$tier1, $tier2] = $this->seedTiers();
        $memberType = $this->seedMemberSovereign();
        Permission::create(['name' => 'View Reports', 'guard_name' => 'web']);
        Permission::create(['name' => 'View Gold Features', 'guard_name' => 'web']);

        $user = $this->makeUser([
            'user_type' => 'Global',
            'user_type_id' => $memberType->id,
        ]);
        $role = Role::create(['name' => 'soft-del-user', 'guard_name' => 'web', 'type' => '2', 'is_ecclesia' => 0]);
        $role->syncPermissions(['View Gold Features']);
        $user->assignRole($role);

        $previousId = (int) $tier2->id;
        $tier2->delete();

        $this->assertSame(
            MembershipPrivilegeService::CHANGE_DOWNGRADE,
            $this->service->resolveChangeType($tier1, $previousId)
        );

        $this->service->applyAfterTierChange($user->fresh(), $tier1, $previousId);
        $user->refresh();
        $this->assertSame('Regional', $user->user_type);
        $this->assertSame(['View Reports'], $role->fresh()->permissions->pluck('name')->all());
    }

    private function createSchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('user_name')->nullable();
            $table->string('password')->nullable();
            $table->string('user_type')->default('Regional');
            $table->unsignedBigInteger('user_type_id')->nullable();
            $table->unsignedBigInteger('ecclesia_id')->nullable();
            $table->integer('is_ecclesia_admin')->default(0)->nullable();
            $table->string('manage_ecclesia')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('2');
            $table->integer('is_ecclesia')->default(0)->nullable();
            $table->tinyInteger('is_admin')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('membership_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('permissions')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->string('type')->default('2')->nullable();
            $table->integer('is_ecclesia')->default(0)->nullable();
            $table->timestamps();
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['role_id', 'model_id', 'model_type']);
        });

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['permission_id', 'model_id', 'model_type']);
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->primary(['permission_id', 'role_id']);
        });
    }

    /** @return array{0: MembershipTier, 1: MembershipTier} */
    private function seedTiers(): array
    {
        $tier1 = MembershipTier::create([
            'name' => 'Tier 1',
            'slug' => 'tier-1',
            'permissions' => 'View Reports',
        ]);
        $tier2 = MembershipTier::create([
            'name' => 'Tier 2',
            'slug' => 'tier-2',
            'permissions' => 'View Gold Features',
        ]);

        return [$tier1, $tier2];
    }

    private function seedMemberSovereign(): UserType
    {
        return UserType::create([
            'name' => 'MEMBER_SOVEREIGN',
            'type' => '2',
            'is_ecclesia' => 0,
            'is_admin' => 0,
        ]);
    }

    private function makeUser(array $attrs): User
    {
        $user = new User();
        $user->email = $attrs['email'] ?? ('user' . uniqid() . '@example.com');
        $user->user_name = $attrs['user_name'] ?? ('user' . uniqid());
        $user->password = bcrypt('secret');
        $user->user_type = $attrs['user_type'] ?? 'Regional';
        $user->user_type_id = $attrs['user_type_id'] ?? null;
        $user->ecclesia_id = $attrs['ecclesia_id'] ?? null;
        $user->is_ecclesia_admin = $attrs['is_ecclesia_admin'] ?? 0;
        $user->manage_ecclesia = $attrs['manage_ecclesia'] ?? null;
        $user->save();

        return $user;
    }
}
