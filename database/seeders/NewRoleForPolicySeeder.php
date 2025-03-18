<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class NewRoleForPolicySeeder extends Seeder
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
                'name' => 'Manage Policy',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Upload Policy',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Download Policy',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'View Policy',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Delete Policy',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($arrPermissions as $permission) {
            \DB::table('permissions')->insert($permission);
        }

        $adminRole = Role::where('name', 'SUPER ADMIN')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo(Permission::all());
        }
    }
}
