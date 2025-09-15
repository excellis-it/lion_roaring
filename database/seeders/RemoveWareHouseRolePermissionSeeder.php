<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RemoveWareHouseRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $roles = Role::where('name', 'WAREHOUSE_ADMIN')->get();
        foreach ($roles as $role) {
            $role->permissions()->detach();
        }
        // delete the role
        Role::where('name', 'WAREHOUSE_ADMIN')->delete();
    }
}
