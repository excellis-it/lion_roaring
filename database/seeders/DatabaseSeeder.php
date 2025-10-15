<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            // assignRoleSeeder::class,
            // assignAdminSeeder::class,
            // CountriesTableSeeder::class,
            // CategorySeeder::class,
            // RolePermissionSeeder::class,
            // AddStrategyPermission::class,
            // StatesTableSeeder::class,
            // AddStrategyPermission::class,
            // AdminPanelRolePermissionSeeder::class,
            // OrderStatusAndEmailTemplateSeeder::class,
            // AddOrderStatusTemplatePermission::class,
            // CompressCategoryImagesSeeder::class,
            // HomeCmsImageSeeder::class,
            ProductImageSeeder::class,
            VariationImageSeeder::class
        ]);
    }
}
