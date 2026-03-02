<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class GlobalCountrySeeder extends Seeder
{
    /**
     * Seed the GLOBAL country entry.
     *
     * This entry represents the main/global domain.
     * - It cannot be edited or deleted from the admin panel.
     * - Global users see data from this entry only.
     */
    public function run()
    {
        Country::updateOrCreate(
            ['code' => 'GL'],
            [
                'name'       => 'Global (Main)',
                'code'       => 'GL',
                'domain'     => env('MAIN_URL', env('APP_URL', 'http://127.0.0.1:8000')),
                'is_global'  => true,
                'status'     => 1,
                'flag_image' => null,
            ]
        );

        $this->command->info('✅ GLOBAL country entry seeded successfully.');
    }
}
