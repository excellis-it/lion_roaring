<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateGlobalUserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $updated = DB::table('users')
            ->where('user_type', 'Global')
            ->update(['user_type' => 'G_R']);

        $this->command->info("Updated {$updated} user(s) from user_type 'Global' to 'G_R'.");
    }
}
