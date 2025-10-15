<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductVariationImage;
use App\Models\WarehouseProductImage;
use App\Models\GlobalImage;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class VariationImageSeeder extends Seeder
{
    public function run()
    {
        $disk = Storage::disk('public');

        $variationImages = ProductVariationImage::all();

        foreach ($variationImages as $pvImage) {
            $originalPath = $pvImage->image_path;

            if (! $disk->exists($originalPath)) {
                $this->command->warn("Image not found: {$originalPath}");
                continue;
            }

            $filePath = storage_path('app/public/' . $originalPath);

            try {
                $img = Image::make($filePath);

                // Skip small images
                $minWidth = 700;
                $minHeight = 700;
                $minSize = 150 * 1024; // 150 KB

                $origWidth = $img->width();
                $origHeight = $img->height();
                $origSize = filesize($filePath);

                $compressedPath = $originalPath;

                if ($origWidth > $minWidth && $origHeight > $minHeight && $origSize > $minSize) {

                    $maxWidth = 2000;
                    $maxHeight = 2000;
                    $img->resize($maxWidth, $maxHeight, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                    $ext = strtolower(pathinfo($originalPath, PATHINFO_EXTENSION));
                    $outputExt = in_array($ext, ['jpg','jpeg','png']) ? $ext : 'jpg';
                    $quality = $outputExt === 'png' ? 70 : 60;

                    $compressedFilename = 'compressed_' . pathinfo($originalPath, PATHINFO_FILENAME) . '.' . $outputExt;
                    $compressedPath = 'product_variation/' . $compressedFilename;

                    $disk->put($compressedPath, (string) $img->encode($outputExt, $quality));

                    // Optional: WebP
                    try {
                        $webpFilename = 'compressed_' . pathinfo($originalPath, PATHINFO_FILENAME) . '.webp';
                        $webpPath = 'product_variation/' . $webpFilename;
                        $disk->put($webpPath, (string) $img->encode('webp', 60));
                        $compressedPath = $webpPath;
                    } catch (\Exception $e) {
                        // ignore
                    }
                }

                // Store in global_images
                GlobalImage::create([
                    'original_path' => $originalPath,
                    'compressed_path' => $compressedPath,
                ]);

                // Update ProductVariationImage
                $pvImage->image_path = $compressedPath;
                $pvImage->save();

                // Update corresponding WarehouseProductImage
                $wpImages = WarehouseProductImage::where('image_path', $originalPath)->get();
                foreach ($wpImages as $wpImage) {
                    $wpImage->image_path = $compressedPath;
                    $wpImage->save();
                }

                $this->command->info("Processed image: {$originalPath}");

            } catch (\Exception $e) {
                $this->command->error("Failed for image {$originalPath}: " . $e->getMessage());
            }
        }

        $this->command->info('All variation images compressed successfully!');
    }
}
