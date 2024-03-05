<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class assignAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = new User();
        $admin->user_name = 'admin';
        $admin->first_name = 'Admin';
        $admin->last_name = 'Admin';
        $admin->email = 'main@yopmail.com';
        $admin->password = bcrypt('12345678');
        $admin->status = true;
        $admin->save();
        $admin->assignRole('ADMIN');
    }
}
