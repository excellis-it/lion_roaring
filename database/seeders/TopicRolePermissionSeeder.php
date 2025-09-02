<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TopicRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Define permissions
        $arrPermissions = [
            [
                "name" => "Manage Topic",
                "guard_name" => "web",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Edit Topic",
                "guard_name" => "web",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Create Topic",
                "guard_name" => "web",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Delete Topic",
                "guard_name" => "web",
                "created_at" => now(),
                "updated_at" => now(),
            ],
        ];

        // Insert permissions into the 'permissions' table
        foreach ($arrPermissions as $permission) {
            \DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Fetch roles
        $adminRole = \DB::table('roles')->where('name', 'SUPER ADMIN')->first();
        $leaderRole = \DB::table('roles')->where('name', 'LEADER')->first();

        // Define permissions to assign
        $permissionsToAssign = [
            'Manage Topic',
            'Edit Topic',
            'Create Topic',
            'Delete Topic'
        ];

        // Fetch permissions
        $permissions = \DB::table('permissions')->whereIn('name', $permissionsToAssign)->get();

        // Assign permissions to SUPER ADMIN role
        foreach ($permissions as $permission) {
            \DB::table('role_has_permissions')->updateOrInsert(
                [
                    'permission_id' => $permission->id,
                    'role_id' => $adminRole->id,
                ],
                [
                    'permission_id' => $permission->id,
                    'role_id' => $adminRole->id,
                ]
            );
        }

        // Assign permissions to LEADER role
        foreach ($permissions as $permission) {
            \DB::table('role_has_permissions')->updateOrInsert(
                [
                    'permission_id' => $permission->id,
                    'role_id' => $leaderRole->id,
                ],
                [
                    'permission_id' => $permission->id,
                    'role_id' => $leaderRole->id,
                ]
            );
        }
    }
}
