<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddSidebarMenuItems extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sidebarMenus = [
            // Donations
            ['key' => 'donations', 'default_name' => 'Donations'],

            // Contact Us Messages
            ['key' => 'contact_us_messages', 'default_name' => 'Contact Us Messages'],

            // Newsletters
            ['key' => 'newsletters', 'default_name' => 'Newsletters'],

            // Testimonials
            ['key' => 'testimonials', 'default_name' => 'Testimonials'],
            ['key' => 'testimonials_list', 'default_name' => 'Testimonials List'],
            ['key' => 'testimonials_create', 'default_name' => 'Testimonials Create'],

            // Our Governance
            ['key' => 'our_governance', 'default_name' => 'Our Governance'],
            ['key' => 'our_governance_list', 'default_name' => 'Our Governance List'],
            ['key' => 'our_governance_create', 'default_name' => 'Our Governance Create'],

            // Our Organizations
            ['key' => 'our_organizations', 'default_name' => 'Our Organizations'],
            ['key' => 'our_organizations_list', 'default_name' => 'Our Organizations List'],
            ['key' => 'our_organizations_create', 'default_name' => 'Our Organizations Create'],

            // Organization Center
            ['key' => 'organization_center', 'default_name' => 'Organization Center'],
            ['key' => 'organization_center_list', 'default_name' => 'Organization Center List'],
            ['key' => 'organization_center_create', 'default_name' => 'Organization Center Create'],

            // Services
            ['key' => 'services', 'default_name' => 'Services'],

            // Pages
            ['key' => 'pages', 'default_name' => 'Pages'],
            ['key' => 'pages_home', 'default_name' => 'Home'],
            ['key' => 'pages_details', 'default_name' => 'Details'],
            ['key' => 'pages_organization', 'default_name' => 'Organization'],
            ['key' => 'pages_organization_cms', 'default_name' => 'Organization CMS'],
            ['key' => 'pages_about_us', 'default_name' => 'About Us'],
            ['key' => 'pages_faqs', 'default_name' => 'FAQS'],
            ['key' => 'pages_gallery', 'default_name' => 'GALLERY'],
            ['key' => 'pages_ecclesia_association', 'default_name' => 'ECCLESIA ASSOCIATION'],
            ['key' => 'pages_principle_and_business', 'default_name' => 'PRINCIPLE AND BUSINESS MODEL'],
            ['key' => 'pages_contact_us', 'default_name' => 'CONTACT US'],
            ['key' => 'pages_articles_of_association', 'default_name' => 'ARTICLES OF ASSOCIATION'],
            ['key' => 'pages_footer', 'default_name' => 'Footer'],
            ['key' => 'pages_register_agreements', 'default_name' => 'REGISTER PAGE AGREEMENTS'],
            ['key' => 'pages_pma_terms', 'default_name' => 'PMA Terms'],
            ['key' => 'pages_privacy_policy', 'default_name' => 'Privacy Policy'],
            ['key' => 'pages_terms_and_conditions', 'default_name' => 'Terms and Conditions'],

            // Countries
            ['key' => 'countries', 'default_name' => 'Countries'],

            // Site Settings
            ['key' => 'site_settings', 'default_name' => 'Site Settings'],
            ['key' => 'site_settings_settings', 'default_name' => 'Settings'],
            ['key' => 'site_settings_menu_names', 'default_name' => 'Menu Names'],
        ];

        foreach ($sidebarMenus as $menu) {
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

        $this->command->info('Sidebar menu items seeded successfully!');
    }
}
