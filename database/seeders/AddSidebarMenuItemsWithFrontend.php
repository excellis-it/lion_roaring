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
            $existing = DB::table('menu_items')->where('key', $menu['key'])->first();

            if ($existing) {
                $updateData = [
                    'default_name' => $menu['default_name'],
                    'type' => $menu['type'],
                    'updated_at' => now(),
                ];

                if (empty($existing->name)) {
                    $updateData['name'] = $menu['default_name'];
                }

                DB::table('menu_items')
                    ->where('key', $menu['key'])
                    ->update([
                        'default_name' => $menu['default_name'],
                        'name' => $updateData['name'] ?? $existing->name,
                        'type' => $menu['type'],
                        'updated_at' => $updateData['updated_at'],
                    ]);
            } else {
                DB::table('menu_items')->insert([
                    'key' => $menu['key'],
                    'default_name' => $menu['default_name'],
                    'name' => $menu['default_name'],
                    'type' => $menu['type'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Sidebar menu items seeded successfully!');
    }
}
