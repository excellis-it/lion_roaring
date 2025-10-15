<?php

namespace Database\Seeders;

use App\Models\ProductColorImage;
use App\Models\WarehouseProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class WareHouseProductImageToProductColorImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WarehouseProductImage::with('warehouseProduct')
            ->chunkById(500, function ($images) {
                $created = 0;

                foreach ($images as $image) {
                    $warehouseProduct = $image->warehouseProduct;

                    if (!$warehouseProduct || !$warehouseProduct->product_id || !$warehouseProduct->color_id || !$image->image_path) {
                        continue;
                    }

                    $exists = ProductColorImage::where('product_id', $warehouseProduct->product_id)
                        ->where('color_id', $warehouseProduct->color_id)
                        ->where('image_path', $image->image_path)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    ProductColorImage::create([
                        'product_id' => $warehouseProduct->product_id,
                        'color_id'   => $warehouseProduct->color_id,
                        'image_path' => $image->image_path,
                    ]);

                    $created++;
                }

                if ($created > 0) {
                    Log::info("WareHouseProductImageToProductColorImageSeeder created {$created} records in current chunk.");
                }
            });
    }
}
