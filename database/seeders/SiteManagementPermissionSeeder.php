<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SiteManagementPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $arrPermissions = [
            [
                "name" => "Manage Privacy Policy Page",
                "guard_name" => "web",
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Manage Terms and Conditions Page",
                "guard_name" => "web",
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Manage Site Settings",
                "guard_name" => "web",
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Manage Menu Settings",
                "guard_name" => "web",
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert permissions
        foreach ($arrPermissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => $permission['guard_name']],
                $permission
            );
        }

        // Assign all new permissions to SUPER ADMIN role
        $adminRole = Role::where('name', 'SUPER ADMIN')->first();

        if ($adminRole) {
            $permissionNames = collect($arrPermissions)->pluck('name')->toArray();
            $adminRole->givePermissionTo($permissionNames);

            echo "✓ Created 4 new permissions\n";
            echo "✓ Assigned all permissions to SUPER ADMIN role\n";
        } else {
            echo "✗ SUPER ADMIN role not found\n";
        }
    }
}
