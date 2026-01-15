<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class assignAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::firstOrCreate(
            ['email' => 'main@yopmail.com'],
            [
                'user_name' => 'admin',
                'first_name' => 'Admin',
                'last_name' => 'Admin',
                'password' => bcrypt('12345678'),
                'status' => true,
            ]
        );

        $role = Role::firstOrCreate([
            'name' => 'SUPER ADMIN',
            'guard_name' => 'web',
        ]);

        $admin->assignRole($role);
    }
}
