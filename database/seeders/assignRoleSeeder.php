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
                'name' => 'ADMIN',
                'guard_name' => 'web',
            ],
            [
                'name' => 'MEMBER',
                'guard_name' => 'web',
            ],
            [
                'name' => 'LEADER',
                'guard_name' => 'web',
            ]
        ];

        foreach ($roles as $key => $value) {
            Role::create($value);
        }

    }
}
