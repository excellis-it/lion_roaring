<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddMembershipMenuItems extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $membershipMenus = [
            ['key' => 'membership', 'default_name' => 'Membership'],
            ['key' => 'membership_management', 'default_name' => 'Membership Management'],
            ['key' => 'membership_plan_list', 'default_name' => 'Plan List'],
            ['key' => 'membership_create_plan', 'default_name' => 'Create Plan'],
            ['key' => 'membership_members', 'default_name' => 'Members'],
            ['key' => 'membership_all_payments', 'default_name' => 'All Payments'],
            ['key' => 'membership_settings', 'default_name' => 'Settings'],
        ];

        foreach ($membershipMenus as $menu) {
            // Check if the menu item already exists
            $exists = DB::table('menu_items')->where('key', $menu['key'])->exists();

            if (!$exists) {
                DB::table('menu_items')->insert([
                    'key' => $menu['key'],
                    'default_name' => $menu['default_name'],
                    'name' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
