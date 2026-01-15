<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AddSuperAdminGlobal extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::role('SUPER ADMIN')
            ->whereNull('user_type')
            ->update(['user_type' => 'Global']);
    }
}
