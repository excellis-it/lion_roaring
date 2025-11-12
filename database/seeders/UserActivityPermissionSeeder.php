<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserActivityPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User Activity Permissions
        $arrPermissions = [
            [
                "name" => "Manage User Activity",
                "guard_name" => "web",
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "View User Activity",
                "guard_name" => "web",
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Create User Activity",
                "guard_name" => "web",
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Edit User Activity",
                "guard_name" => "web",
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Delete User Activity",
                "guard_name" => "web",
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert all permissions into the database
        foreach ($arrPermissions as $permission) {
            Permission::firstOrCreate($permission);
        }

        $adminRole = Role::where('name', 'SUPER ADMIN')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo(Permission::all());
        }

        // ADMINISTRATOR ROLE
        $administratorsRole = Role::where('name', 'ADMINISTRATOR')->first();
        if ($administratorsRole) {
            $administratorsRole->givePermissionTo(Permission::all());
        }
    }
}
