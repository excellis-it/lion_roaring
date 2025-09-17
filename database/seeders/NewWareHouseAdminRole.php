<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class NewWareHouseAdminRole extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $roles = [
            [
                'name' => 'WAREHOUSE_ADMIN',
                'guard_name' => 'web',
            ]
        ];

        foreach ($roles as $key => $value) {
            Role::firstOrCreate($value);
        }


        $arrPermissions = [

            [
                "name" => "Manage Warehouse Manager",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
            [
                "name" => "Manage Assigned Warehouses",
                "guard_name" => "web",
                "type" => 2,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert all permissions into the database
        foreach ($arrPermissions as $permission) {
            Permission::firstOrCreate($permission);
        }





        // Assign permissions to WAREHOUSE_ADMIN role
        $warehouseAdminRole = Role::where('name', 'WAREHOUSE_ADMIN')->first();
        if ($warehouseAdminRole) {
            $warehouseAdminRole->syncPermissions([
                'Manage Profile',
                'Manage Password',
                'Manage Chat',
                'Manage Event',
                'Manage Becoming Sovereigns',
                'View Becoming Sovereigns',
                'Upload Becoming Sovereigns',
                'Manage Becoming Christ Like',
                'View Becoming Christ Like',
                'Upload Becoming Christ Like',
                'Manage Becoming a Leader',
                'View Becoming a Leader',
                'Upload Becoming a Leader',
                'Manage Job Postings',
                'View Job Postings',
                'Manage Meeting Schedule',
                'View Meeting Schedule',
                'Manage Help',
                'Manage Bulletin',
                'Create Bulletin',
                'Edit Bulletin',
                'Delete Bulletin',
                'Manage Warehouse Manager',
                'Manage Assigned Warehouses',
            ]);
        }

        // Add all permissions to the SUPER ADMIN role without removing existing ones
        $adminRole = Role::where('name', 'SUPER ADMIN')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo(Permission::all());
        }
    }
}
