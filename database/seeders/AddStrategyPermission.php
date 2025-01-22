<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AddStrategyPermission extends Seeder
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
                'name' => 'Manage Strategy',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Upload Strategy',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Download Strategy',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'View Strategy',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Delete Strategy',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($arrPermissions as $permission) {
            \DB::table('permissions')->insert($permission);
        }

        $role = \DB::table('roles')->where('name', 'SUPER ADMIN')->first();
        $permissions = \DB::table('permissions')->whereIn('name', ['Manage Strategy', 'Upload Strategy', 'Download Strategy', 'View Strategy', 'Delete Strategy'])->get();

        foreach ($permissions as $permission) {
            \DB::table('role_has_permissions')->insert([
                'permission_id' => $permission->id,
                'role_id' => $role->id,
            ]);
        }

        $leader_role = \DB::table('roles')->where('name', 'LEADER')->first();

        foreach ($permissions as $permission) {
            \DB::table('role_has_permissions')->insert([
                'permission_id' => $permission->id,
                'role_id' => $leader_role->id,
            ]);
        }
    }
}
