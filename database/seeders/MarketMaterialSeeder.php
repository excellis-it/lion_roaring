<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MarketMaterial;

class MarketMaterialSeeder extends Seeder
{
    public function run()
    {
        $materials = [
            ['name' => 'Silver', 'code' => 'XAG', 'sort_order' => 1],
            ['name' => 'Gold', 'code' => 'XAU', 'sort_order' => 2],
            ['name' => 'Copper', 'code' => 'XCU', 'sort_order' => 3],
           // ['name' => 'Platinum', 'code' => 'XPT', 'sort_order' => 4],
        ];

        foreach ($materials as $material) {
            MarketMaterial::updateOrCreate(
                ['code' => $material['code']],
                [
                    'name' => $material['name'],
                    'is_active' => true,
                    'sort_order' => $material['sort_order'],
                ]
            );
        }
    }
}
