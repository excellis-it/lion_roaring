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

/**
 * End-to-end privilege matrix using 5 demo users that map to the client's business rules.
 *
 * Demo 1 — Alice: Tier 1 Regional, no extra role
 * Demo 2 — Bob:   Tier 1 Regional, assigned role (extra perms)
 * Demo 3 — Carol: Tier 2 Regional, no extra role
 * Demo 4 — Dave:  Tier 2 G_R + Ecclesia (single / multi House of Ecclesia)
 * Demo 5 — Eve:   Tier 2 Global + assigned role
 */
class MembershipPrivilegeDemoUsersTest extends TestCase
{
    private MembershipPrivilegeService $service;
    private MembershipTier $tier1;
    private MembershipTier $tier2;
    private UserType $memberType;
    private UserType $ecclesiaType;
    private UserType $warehouseType;

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
        $this->seedCatalog();
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

    public function test_demo1_alice_tier1_regional_no_extra_role_all_paths(): void
    {
        $alice = $this->makeDemoUser('alice', [
            'user_type' => 'Regional',
            'user_type_id' => $this->memberType->id,
            'permissions' => ['Tier1 Access'],
        ]);

        // Same-tier renew → no change
        $this->service->applyAfterTierChange($alice->fresh(['roles', 'userRole']), $this->tier1, (int) $this->tier1->id);
        $this->assertRolePermissions($alice, ['Tier1 Access']);
        $this->assertSame('Regional', $alice->fresh()->user_type);
        $this->assertTrue($alice->fresh()->isMemberSovereign());

        // Upgrade Tier 1 → Tier 2 → Tier 2 privilege
        $this->service->applyAfterTierChange($alice->fresh(['roles', 'userRole']), $this->tier2, (int) $this->tier1->id);
        $this->assertRolePermissions($alice, ['Tier2 Access']);

        // Downgrade Tier 2 → Tier 1 → Tier 1 + Regional + MEMBER_SOVEREIGN
        $alice->refresh();
        $alice->user_type = 'Regional';
        $alice->save();
        $this->service->applyAfterTierChange($alice->fresh(['roles', 'userRole']), $this->tier1, (int) $this->tier2->id);
        $alice->refresh();
        $this->assertSame('Regional', $alice->user_type);
        $this->assertSame($this->memberType->id, $alice->user_type_id);
        $this->assertRolePermissions($alice, ['Tier1 Access']);
    }

    public function test_demo2_bob_tier1_with_assigned_role_all_paths(): void
    {
        $bob = $this->makeDemoUser('bob', [
            'user_type' => 'Regional',
            'user_type_id' => $this->memberType->id,
            'permissions' => ['Tier1 Access', 'Extra Admin'],
        ]);

        // Same-tier renew → Role takes precedence (extra perms kept)
        $this->service->applyAfterTierChange($bob->fresh(['roles', 'userRole']), $this->tier1, (int) $this->tier1->id);
        $this->assertRolePermissions($bob, ['Extra Admin', 'Tier1 Access']);

        // Upgrade to Tier 2 → Tier 2 privilege (replaces custom extras)
        $this->service->applyAfterTierChange($bob->fresh(['roles', 'userRole']), $this->tier2, (int) $this->tier1->id);
        $this->assertRolePermissions($bob, ['Tier2 Access']);

        // Give Bob an assigned role again on Tier 2, then same-tier renew preserves it
        $role = $this->customRoleFor($bob);
        $role->syncPermissions(['Tier2 Access', 'Extra Admin']);
        $this->service->applyAfterTierChange($bob->fresh(['roles', 'userRole']), $this->tier2, (int) $this->tier2->id);
        $this->assertRolePermissions($bob, ['Extra Admin', 'Tier2 Access']);
    }

    public function test_demo3_carol_tier2_regional_no_extra_role_all_paths(): void
    {
        $carol = $this->makeDemoUser('carol', [
            'user_type' => 'Regional',
            'user_type_id' => $this->memberType->id,
            'permissions' => ['Tier2 Access'],
        ]);

        // Same-tier renew → no change
        $this->service->applyAfterTierChange($carol->fresh(['roles', 'userRole']), $this->tier2, (int) $this->tier2->id);
        $this->assertRolePermissions($carol, ['Tier2 Access']);
        $this->assertSame('Regional', $carol->fresh()->user_type);

        // Downgrade Tier 2 → Tier 1
        $this->service->applyAfterTierChange($carol->fresh(['roles', 'userRole']), $this->tier1, (int) $this->tier2->id);
        $carol->refresh();
        $this->assertSame('Regional', $carol->user_type);
        $this->assertSame($this->memberType->id, $carol->user_type_id);
        $this->assertRolePermissions($carol, ['Tier1 Access']);

        // First subscribe (no previous plan) → sync new tier
        $this->service->applyAfterTierChange($carol->fresh(['roles', 'userRole']), $this->tier2, null);
        $this->assertRolePermissions($carol, ['Tier2 Access']);
    }

    public function test_demo4_dave_tier2_gr_ecclesia_all_paths(): void
    {
        // Dave A: single House of Ecclesia
        $daveSingle = $this->makeDemoUser('dave-single', [
            'user_type' => 'G_R',
            'user_type_id' => $this->ecclesiaType->id,
            'is_ecclesia_admin' => 1,
            'manage_ecclesia' => '101',
            'ecclesia_id' => null,
            'permissions' => ['Ecclesia Manage'],
            'role_is_ecclesia' => 1,
        ]);

        // Same-tier renew → Role (Ecclesia) takes precedence
        $this->service->applyAfterTierChange($daveSingle->fresh(['roles', 'userRole']), $this->tier2, (int) $this->tier2->id);
        $this->assertRolePermissions($daveSingle, ['Ecclesia Manage']);
        $this->assertSame('G_R', $daveSingle->fresh()->user_type);
        $this->assertSame(1, (int) $daveSingle->fresh()->is_ecclesia_admin);

        // Downgrade → Regional + MEMBER_SOVEREIGN + House = that single ecclesia
        $this->service->applyAfterTierChange($daveSingle->fresh(['roles', 'userRole']), $this->tier1, (int) $this->tier2->id);
        $daveSingle->refresh();
        $this->assertSame('Regional', $daveSingle->user_type);
        $this->assertSame($this->memberType->id, $daveSingle->user_type_id);
        $this->assertSame(0, (int) $daveSingle->is_ecclesia_admin);
        $this->assertNull($daveSingle->manage_ecclesia);
        $this->assertSame(101, (int) $daveSingle->ecclesia_id);
        $this->assertSame(0, (int) $this->customRoleFor($daveSingle)->is_ecclesia);
        $this->assertRolePermissions($daveSingle, ['Tier1 Access']);

        // Dave B: multiple Houses → NO NAME (null ecclesia_id)
        $daveMulti = $this->makeDemoUser('dave-multi', [
            'user_type' => 'G_R',
            'user_type_id' => $this->ecclesiaType->id,
            'is_ecclesia_admin' => 1,
            'manage_ecclesia' => '201,202,203',
            'ecclesia_id' => null,
            'permissions' => ['Ecclesia Manage'],
            'role_is_ecclesia' => 1,
        ]);

        $this->service->applyAfterTierChange($daveMulti->fresh(['roles', 'userRole']), $this->tier1, (int) $this->tier2->id);
        $daveMulti->refresh();
        $this->assertSame('Regional', $daveMulti->user_type);
        $this->assertSame($this->memberType->id, $daveMulti->user_type_id);
        $this->assertNull($daveMulti->ecclesia_id); // UI shows "NO NAME"
        $this->assertRolePermissions($daveMulti, ['Tier1 Access']);
    }

    public function test_demo5_eve_tier2_global_assigned_role_all_paths(): void
    {
        $eve = $this->makeDemoUser('eve', [
            'user_type' => 'Global',
            'user_type_id' => $this->warehouseType->id,
            'is_ecclesia_admin' => 0,
            'manage_ecclesia' => null,
            'ecclesia_id' => 55,
            'permissions' => ['Warehouse Access', 'Extra Admin'],
        ]);

        // Same-tier renew → Role takes precedence
        $this->service->applyAfterTierChange($eve->fresh(['roles', 'userRole']), $this->tier2, (int) $this->tier2->id);
        $this->assertRolePermissions($eve, ['Extra Admin', 'Warehouse Access']);
        $this->assertSame('Global', $eve->fresh()->user_type);
        $this->assertSame(55, (int) $eve->fresh()->ecclesia_id);

        // Downgrade → Regional + MEMBER_SOVEREIGN + keep House of Ecclesia
        $this->service->applyAfterTierChange($eve->fresh(['roles', 'userRole']), $this->tier1, (int) $this->tier2->id);
        $eve->refresh();
        $this->assertSame('Regional', $eve->user_type);
        $this->assertSame($this->memberType->id, $eve->user_type_id);
        $this->assertSame(55, (int) $eve->ecclesia_id);
        $this->assertRolePermissions($eve, ['Tier1 Access']);
    }

    public function test_all_five_demo_users_exist_and_cover_client_matrix(): void
    {
        $alice = $this->makeDemoUser('alice-matrix', [
            'user_type' => 'Regional',
            'user_type_id' => $this->memberType->id,
            'permissions' => ['Tier1 Access'],
        ]);
        $bob = $this->makeDemoUser('bob-matrix', [
            'user_type' => 'Regional',
            'user_type_id' => $this->memberType->id,
            'permissions' => ['Tier1 Access', 'Extra Admin'],
        ]);
        $carol = $this->makeDemoUser('carol-matrix', [
            'user_type' => 'Regional',
            'user_type_id' => $this->memberType->id,
            'permissions' => ['Tier2 Access'],
        ]);
        $dave = $this->makeDemoUser('dave-matrix', [
            'user_type' => 'G_R',
            'user_type_id' => $this->ecclesiaType->id,
            'is_ecclesia_admin' => 1,
            'manage_ecclesia' => '9',
            'permissions' => ['Ecclesia Manage'],
            'role_is_ecclesia' => 1,
        ]);
        $eve = $this->makeDemoUser('eve-matrix', [
            'user_type' => 'Global',
            'user_type_id' => $this->warehouseType->id,
            'ecclesia_id' => 12,
            'permissions' => ['Warehouse Access'],
        ]);

        $this->assertCount(5, User::all());

        // Assigned privilege detection matches client "Role" column
        $this->assertFalse($this->service->hasAssignedPrivilege($alice->fresh(['roles', 'userRole']), $this->tier1));
        $this->assertTrue($this->service->hasAssignedPrivilege($bob->fresh(['roles', 'userRole']), $this->tier1));
        $this->assertFalse($this->service->hasAssignedPrivilege($carol->fresh(['roles', 'userRole']), $this->tier2));
        $this->assertTrue($this->service->hasAssignedPrivilege($dave->fresh(['roles', 'userRole']), $this->tier2));
        $this->assertTrue($this->service->hasAssignedPrivilege($eve->fresh(['roles', 'userRole']), $this->tier2));

        // Change-type matrix
        $this->assertSame('same', $this->service->resolveChangeType($this->tier1, (int) $this->tier1->id));
        $this->assertSame('upgrade', $this->service->resolveChangeType($this->tier2, (int) $this->tier1->id));
        $this->assertSame('downgrade', $this->service->resolveChangeType($this->tier1, (int) $this->tier2->id));
        $this->assertSame('new', $this->service->resolveChangeType($this->tier1, null));
    }

    private function seedCatalog(): void
    {
        foreach ([
            'Tier1 Access',
            'Tier2 Access',
            'Extra Admin',
            'Ecclesia Manage',
            'Warehouse Access',
        ] as $name) {
            Permission::create(['name' => $name, 'guard_name' => 'web']);
        }

        $this->tier1 = MembershipTier::create([
            'name' => 'Tier 1',
            'slug' => 'tier-1',
            'permissions' => 'Tier1 Access',
        ]);
        $this->tier2 = MembershipTier::create([
            'name' => 'Tier 2',
            'slug' => 'tier-2',
            'permissions' => 'Tier2 Access',
        ]);

        $this->memberType = UserType::create([
            'name' => 'MEMBER_SOVEREIGN',
            'type' => '2',
            'is_ecclesia' => 0,
            'is_admin' => 0,
        ]);
        $this->ecclesiaType = UserType::create([
            'name' => 'ECCLESIA',
            'type' => '2',
            'is_ecclesia' => 1,
            'is_admin' => 0,
        ]);
        $this->warehouseType = UserType::create([
            'name' => 'WAREHOUSE_ADMIN',
            'type' => '2',
            'is_ecclesia' => 0,
            'is_admin' => 0,
        ]);
    }

    private function makeDemoUser(string $slug, array $attrs): User
    {
        $user = new User();
        $user->email = $slug . '@demo.lionroaring.test';
        $user->user_name = $slug;
        $user->password = bcrypt('Demo@123');
        $user->user_type = $attrs['user_type'];
        $user->user_type_id = $attrs['user_type_id'];
        $user->ecclesia_id = $attrs['ecclesia_id'] ?? null;
        $user->is_ecclesia_admin = $attrs['is_ecclesia_admin'] ?? 0;
        $user->manage_ecclesia = $attrs['manage_ecclesia'] ?? null;
        $user->save();

        $role = Role::create([
            'name' => $slug . '-role',
            'guard_name' => 'web',
            'type' => '2',
            'is_ecclesia' => $attrs['role_is_ecclesia'] ?? 0,
        ]);
        $role->syncPermissions($attrs['permissions'] ?? []);
        $user->assignRole($role);

        return $user->fresh(['roles', 'userRole']);
    }

    private function customRoleFor(User $user): Role
    {
        $role = $this->service->resolveCustomRole($user->fresh(['roles']));
        $this->assertNotNull($role);

        return $role;
    }

    private function assertRolePermissions(User $user, array $expected): void
    {
        $role = $this->customRoleFor($user);
        $actual = $role->fresh()->permissions->pluck('name')->sort()->values()->all();
        sort($expected);
        $this->assertSame($expected, $actual);
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
}
