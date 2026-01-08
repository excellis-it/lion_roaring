<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\UserType;

class MigrateRolesToUserTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable FK checks to allow truncation
        Schema::disableForeignKeyConstraints();

        // 1. Backup existing Roles and their Permissions
        $oldRoles = Role::with('permissions')->get();
        $rolePermissionsMap = [];

        $this->command->info('Migrating roles to user_types...');

        foreach ($oldRoles as $role) {
            // Create UserType
            // We use the same ID to maintain mapping easily for now, or just map manually
            $userType = UserType::create([
                'id' => $role->id,
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'type' => $role->type, // Provided column exists in recent migration
                'is_ecclesia' => $role->is_ecclesia, // Provided column exists in recent migration
                'created_at' => $role->created_at,
                'updated_at' => $role->updated_at,
            ]);

            // Store permissions for this role ID
            $rolePermissionsMap[$role->id] = $role->permissions;
        }

        // 2. Update Users with user_type_id
        $users = User::all();
        $userRoleMap = []; // UserID -> OldRoleID

        $this->command->info('Updating users with user_type_id...');

        foreach ($users as $user) {
            // Get user's current role(s). Assuming one role per user for simplified migration
            // or taking the first one if multiple.
            // We need to query direct DB or ensuring relationship works before we truncate.
            // Using existing relation:
            $role = $user->roles->first();
            if ($role) {
                $user->user_type_id = $role->id;
                $user->save();
                $userRoleMap[$user->id] = $role->id;
            }
        }

        // 3. Clear existing Roles and Assignments
        $this->command->info('Clearing old roles and assignments...');
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        Role::truncate();

        // 4. Create New Roles based on User (Partner) Slugs
        $this->command->info('Creating new partner slug roles...');

        foreach ($users as $user) {
            // "all partner have unique slug to store as a role name"
            // Using user_name as slug. Ensuring uniqueness and existence.
            $slug = $user->user_name;
            if (empty($slug)) {
                $slug = 'partner_' . $user->id; // Fallback
            }

            // Ensure slug is unique if user_name is somehow not (though it should be)
            // But Role name must be unique per guard.
            // We assume user_name is unique.

            // Determine is_ecclesia for the new role
            // "if user_type table is_ecclesia == 1 then at role table save is_ecclesia =1 with slug"
            $isEcclesia = 0;
            if ($user->user_type_id) {
                // We can look up in the static map or DB. DB is safer.
                $userType = UserType::find($user->user_type_id);
                if ($userType && $userType->is_ecclesia == 1) {
                    $isEcclesia = 1;
                }
            }

            // Create the new Role
            try {
                $newRole = Role::create([
                    'name' => $slug,
                    'guard_name' => 'web', // Defaulting to web, or use user's guard
                    'is_ecclesia' => $isEcclesia,
                    // 'type' is inherited? The prompt doesn't specify type for new roles.
                    // But column exists. default is 2.
                ]);
            } catch (\Exception $e) {
                $this->command->error("Failed to create role for user {$user->id}: " . $e->getMessage());
                continue;
            }

            // Assign this new role to the user
            $user->assignRole($newRole);

            // 5. Assign Permissions
            // "the belongning role permissions get by user role and it save with the slug at role table"
            if (isset($userRoleMap[$user->id])) {
                $oldRoleId = $userRoleMap[$user->id];
                if (isset($rolePermissionsMap[$oldRoleId])) {
                    $permissions = $rolePermissionsMap[$oldRoleId];
                    $newRole->syncPermissions($permissions);
                }
            }
        }

        Schema::enableForeignKeyConstraints();

        // Clear Permission Cache
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command->info('Migration of roles to partners completed successfully.');
    }
}
