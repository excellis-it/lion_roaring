<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CountryPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or find permission
        $permission = Permission::firstOrCreate([
            'name' => 'Manage Countries',
            'guard_name' => 'web',
        ]);

        // Assign to SUPER ADMIN role if exists
        $role = Role::where('name', 'SUPER ADMIN')->first();
        if ($role) {
            $role->givePermissionTo($permission);
        }
    }
}
