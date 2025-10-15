<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\GlobalImage;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the seeder.
     *
     * This seeder will:
     *  - iterate products and product images
     *  - skip images that are missing or already look compressed (filename starts with "compressed_")
     *  - create a GlobalImage row storing the original path and compressed path (if created)
     *  - write compressed image (and optional webp) to the same folder in storage/app/public
     */
    public function run()
    {
        $disk = Storage::disk('public');

        // Process product background images (stored on products table)
        Product::whereNotNull('background_image')->chunk(50, function ($products) use ($disk) {
            foreach ($products as $product) {
                $path = $product->background_image;
                if (! $path) continue;

                $this->processAndReplace($product, 'background_image', $path, $disk);
            }
        });

        // Process images stored in product_images table
        ProductImage::whereNotNull('image')->chunk(200, function ($images) use ($disk) {
            foreach ($images as $pImage) {
                $path = $pImage->image;
                if (! $path) continue;

                $this->processAndReplace($pImage, 'image', $path, $disk);
            }
        });

        $this->command->info('Product images seeding/compression finished.');
    }

    /**
     * Process a single file path: create GlobalImage entry and (if appropriate) compress and replace the path on the model + update GlobalImage
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model (Product or ProductImage)
     * @param  string $attribute (column name on the model to update)
     * @param  string $path
     * @param  \Illuminate\Contracts\Filesystem\Filesystem $disk
     * @return void
     */
    protected function processAndReplace($model, $attribute, $path, $disk)
    {
        // Skip if file missing
        if (! $disk->exists($path)) {
            $this->command->warn("File not found: {$path}");
            return;
        }

        // Skip if looks already processed
        if (strpos(basename($path), 'compressed_') === 0) {
            $this->command->info("Already compressed (skipping): {$path}");
            return;
        }

        // Save original in GlobalImage (compressed_path null for now)
        $global = GlobalImage::create([
            'original_path' => $path,
            'compressed_path' => null,
        ]);

        // Physical path
        $fullPath = storage_path('app/public/' . $path);

        try {
            $img = Image::make($fullPath);

            $origWidth = $img->width();
            $origHeight = $img->height();
            $origSize = file_exists($fullPath) ? filesize($fullPath) : null;

            // thresholds (tweak as needed)
            $minWidthForCompression  = 700; // px
            $minHeightForCompression = 700; // px
            $minSizeForCompression   = 150 * 1024; // 150 KB

            $isLowResolution = ($origWidth <= $minWidthForCompression || $origHeight <= $minHeightForCompression);
            $isSmallFile     = ($origSize !== null && $origSize <= $minSizeForCompression);

            if ($isLowResolution || $isSmallFile) {
                $this->command->info("Skipping compression (small/low-res): {$path}");
                return;
            }

            // Resize to sane max dimensions keeping aspect ratio
            $maxWidth = 2000;
            $maxHeight = 2000;
            $img->resize($maxWidth, $maxHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $dir = trim(pathinfo($path, PATHINFO_DIRNAME), "/");
            $basename = pathinfo($path, PATHINFO_FILENAME);
            $origExt = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            // Choose output ext/quality
            if ($origExt === 'png') {
                $outputExt = 'png';
                $quality = 70;
            } else {
                $outputExt = in_array($origExt, ['jpg','jpeg']) ? 'jpg' : $origExt;
                $quality = 60;
            }

            $compressedFilename = 'compressed_' . $basename . '.' . $outputExt;
            $compressedPath = ($dir === '.' || $dir === '') ? $compressedFilename : ($dir . '/' . $compressedFilename);

            // write compressed
            $disk->put($compressedPath, (string) $img->encode($outputExt, $quality));

            // attempt webp as well and prefer webp if successful
            try {
                $webpFilename = 'compressed_' . $basename . '.webp';
                $webpPath = ($dir === '.' || $dir === '') ? $webpFilename : ($dir . '/' . $webpFilename);
                $disk->put($webpPath, (string) $img->encode('webp', 60));
                $finalPath = $webpPath;
            } catch (\Exception $e) {
                // fallback to compressedPath
                $finalPath = $compressedPath;
            }

            // Update GlobalImage compressed_path
            $global->update(['compressed_path' => $finalPath]);

            // Update model attribute to point to compressed file
            $model->{$attribute} = $finalPath;
            $model->save();

            $this->command->info("Compressed and updated: {$path} -> {$finalPath}");

        } catch (\Exception $e) {
            $this->command->error("Failed processing {$path}: " . $e->getMessage());
            // leave original global entry (compressed_path null) so you still have record
        }
    }
}
