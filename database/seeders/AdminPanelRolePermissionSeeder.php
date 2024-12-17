<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminPanelRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrPermissions = [
            [
                "name" => "Manage My Profile",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],

            [
                "name" => "Manage My Password",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Create Admin List",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Delete Admin List",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Manage Admin List",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Edit Admin List",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],

            [
                "name" => "Manage Donations",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],

            [
                "name" => "Manage Contact Us Messages",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],

            [
                "name" => "Delete Contact Us Messages",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],

            [
                "name" => "Manage Newsletters",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],

            [
                "name" => "Delete Newsletters",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],

            [
                "name" => "Create Testimonials",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Delete Testimonials",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Manage Testimonials",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Edit Testimonials",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],

            [
                "name" => "Create Our Governance",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Delete Our Governance",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Manage Our Governance",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Edit Our Governance",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],

            [
                "name" => "Create Our Organization",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Delete Our Organization",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Manage Our Organization",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Edit Our Organization",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],

            [
                "name" => "Create Organization Center",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Delete Organization Center",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Manage Organization Center",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Edit Organization Center",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],



            [
                "name" => "Manage Services",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],

            [
                "name" => "Manage Pages",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],

        ];

        // Insert all permissions into the database
        Permission::insert($arrPermissions);

        // Retrieve the LEADER and MEMBER roles
        $leaderRole = Role::where('name', 'LEADER')->first();
        $memberRole = Role::where('name', 'MEMBER')->first();

        // Fetch specific permissions for profile and password
        $profilePermission = Permission::where('name', 'Manage My Profile')->first();
        $passwordPermission = Permission::where('name', 'Manage My Password')->first();

        // Assign "Manage My Profile" and "Manage My Password" permissions to LEADER and MEMBER
        if ($leaderRole && $memberRole) {
            $leaderRole->givePermissionTo([$profilePermission, $passwordPermission]);
            $memberRole->givePermissionTo([$profilePermission, $passwordPermission]);
        }

        // Add all permissions to the ADMIN role without removing existing ones
        $adminRole = Role::where('name', 'ADMIN')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo(Permission::all());
        }
    }
}
