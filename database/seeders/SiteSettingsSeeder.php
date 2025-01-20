<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class SiteSettingsSeeder extends Seeder
{
    public function run()
    {
        SiteSetting::create([
            'SITE_NAME' => 'Lion Roaring',
            'SITE_LOGO' => 'user_assets/images/logo.png',
            'SITE_CONTACT_EMAIL' => 'admin@lionroaring.us',
            'SITE_CONTACT_PHONE' => '1 (240)-982-0054',
        ]);
    }
}
