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
        $this->call([
            // first time only
            CountriesTableSeeder::class,
            StatesTableSeeder::class,

            // then run these
            AddSuperAdminGlobal::class,
            MigrateRolesToUserTypesSeeder::class,
            RolePermissionSeeder::class,

            IsAcceptedStatusUpdateSeeder::class,
            AddSidebarMenuItems::class,
            AddSidebarMenuItemsWithFrontend::class,
            AddMembershipMenuItems::class,
            SiteSettingsSeeder::class,
            OrderStatusAndEmailTemplateSeeder::class,
            ChatbotSeeder::class,
            CompressCategoryImagesSeeder::class,
            HomeCmsImageSeeder::class,
            SignupRuleSeeder::class,
            MarketMaterialSeeder::class,
        ]);
    }
}
