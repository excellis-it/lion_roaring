<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddSidebarMenuItemsWithFrontend extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // update type of menu items
        DB::table('menu_items')->update(['type' => 'Panel Menu']);

        $sidebarMenus = [
            ['key' => 'home', 'default_name' => 'Home', 'type' => 'Frontend Sandwich Menu'],
            ['key' => 'private_ecclesia', 'default_name' => 'Private Ecclesia', 'type' => 'Frontend Sandwich Menu'],
            ['key' => 'ecclesia_covenant', 'default_name' => 'Ecclesia Covenant', 'type' => 'Frontend Sandwich Menu'],
            ['key' => 'mandate_of_kingdom_precepts_and_dominion', 'default_name' => 'Mandate of Kingdom Precepts and Dominion', 'type' => 'Frontend Sandwich Menu'],
            ['key' => 'gallery', 'default_name' => 'Gallery', 'type' => 'Frontend Sandwich Menu'],
            ['key' => 'faq', 'default_name' => 'FAQ', 'type' => 'Frontend Sandwich Menu'],
            ['key' => 'membership', 'default_name' => 'Membership', 'type' => 'Frontend Sandwich Menu'],
        ];

        foreach ($sidebarMenus as $menu) {
            // Check if the menu item already exists
            $exists = DB::table('menu_items')->where('key', $menu['key'])->exists();

            if (!$exists) {
                DB::table('menu_items')->insert([
                    'key' => $menu['key'],
                    'default_name' => $menu['default_name'],
                    'name' => $menu['default_name'],
                    'type' => $menu['type'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                // Update existing menu item
                DB::table('menu_items')
                    ->where('key', $menu['key'])
                    ->update([
                        'default_name' => $menu['default_name'],
                        'name' => $menu['default_name'],
                        'type' => $menu['type'],
                        'updated_at' => now()
                    ]);
            }
        }

        //update type

        $this->command->info('Sidebar menu items seeded successfully!');
    }
}
