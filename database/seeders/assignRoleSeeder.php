<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class assignRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'SUPER ADMIN',
                'guard_name' => 'web',
            ],
            [
                'name' => 'MEMBER_NON_SOVEREIGN',
                'guard_name' => 'web',
            ],
            [
                'name' => 'LEADER',
                'guard_name' => 'web',
            ],
            [
                'name' => 'ECCLESIA',
                'guard_name' => 'web',
            ]
        ];

        foreach ($roles as $key => $value) {
            Role::create($value);
        }
    }
}
