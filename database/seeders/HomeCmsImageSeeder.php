<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use App\Models\EcomHomeCms;
use App\Models\GlobalImage;

class HomeCmsImageSeeder extends Seeder
{
    /**
     * Thresholds: adjust as needed
     */
    protected $minWidthForCompression  = 1000;          // px
    protected $minHeightForCompression = 1000;          // px
    protected $minSizeForCompression   = 150 * 1024;    // bytes (150 KB)
    protected $maxWidth                = 2000;          // px
    protected $maxHeight               = 2000;          // px

    public function run()
    {
        $disk = Storage::disk('public');

        $cmsList = EcomHomeCms::all();
        if ($cmsList->isEmpty()) {
            $this->command->info('No EcomHomeCms entries found.');
            return;
        }

        foreach ($cmsList as $cms) {
            $this->command->info("Processing CMS id: {$cms->id}");

            // Fields to process (single-image fields)
            $singleFields = [
                'header_logo',
                'shop_now_image',
                'about_section_image',
                'banner_image',
                'banner_image_small',
                'product_category_image',
                'featured_product_image',
                'new_product_image',
                'new_arrival_image',
            ];

            foreach ($singleFields as $field) {
                if ($cms->{$field}) {
                    $newPath = $this->processImageField($cms->{$field}, $disk);
                    if ($newPath !== null && $newPath !== $cms->{$field}) {
                        $this->command->info(" - Updated {$field}: {$cms->{$field}} -> {$newPath}");
                        $cms->{$field} = $newPath;
                    } else {
                        $this->command->info(" - Skipped {$field} (no compression needed or failed): {$cms->{$field}}");
                    }
                }
            }

            // slider_data (array of slides with image key)
            if ($cms->slider_data) {
                $sliderArray = is_string($cms->slider_data)
                    ? json_decode($cms->slider_data, true)
                    : (is_array($cms->slider_data) ? $cms->slider_data : null);
                if (is_array($sliderArray)) {
                    $changed = false;
                    foreach ($sliderArray as $idx => $slide) {
                        if (!empty($slide['image'])) {
                            $newPath = $this->processImageField($slide['image'], $disk);
                            if ($newPath !== null && $newPath !== $slide['image']) {
                                $sliderArray[$idx]['image'] = $newPath;
                                $changed = true;
                                $this->command->info(" - slider_data image updated for CMS {$cms->id} slide {$idx}");
                            } else {
                                $this->command->info(" - slider_data slide {$idx} skipped/unchanged");
                            }
                        }
                    }
                    if ($changed) {
                        $cms->slider_data = json_encode($sliderArray);
                    }
                }
            }

            // slider_data_second
            if ($cms->slider_data_second) {
                $sliderArray = is_string($cms->slider_data_second)
                    ? json_decode($cms->slider_data_second, true)
                    : (is_array($cms->slider_data_second) ? $cms->slider_data_second : null);
                if (is_array($sliderArray)) {
                    $changed = false;
                    foreach ($sliderArray as $idx => $slide) {
                        if (!empty($slide['image'])) {
                            $newPath = $this->processImageField($slide['image'], $disk);
                            if ($newPath !== null && $newPath !== $slide['image']) {
                                $sliderArray[$idx]['image'] = $newPath;
                                $changed = true;
                                $this->command->info(" - slider_data_second image updated for CMS {$cms->id} slide {$idx}");
                            } else {
                                $this->command->info(" - slider_data_second slide {$idx} skipped/unchanged");
                            }
                        }
                    }
                    if ($changed) {
                        $cms->slider_data_second = json_encode($sliderArray);
                    }
                }
            }

            $cms->save();
            $this->command->info("Saved CMS id: {$cms->id}");
        }

        $this->command->info('EcomHomeCms images compression completed.');
    }

    /**
     * Process a single image field path:
     * - Skip external URLs
     * - Skip if file not present
     * - If large enough / high res, compress & create webp copy (prefer webp)
     * - Store original path in GlobalImage with compressed path (if any)
     *
     * @param string $originalPath  // path relative to storage/app/public
     * @param \Illuminate\Filesystem\FilesystemAdapter $disk
     * @return string|null  // new (compressed) path to use in DB, or original path if unchanged, or null on failure
     */
    protected function processImageField(string $originalPath, $disk)
    {
        // Normalize and skip full URLs
        $originalPath = ltrim($originalPath, '/');
        if (preg_match('#^https?://#i', $originalPath)) {
            // external URL — skip
            return null;
        }

        $existingGlobal = GlobalImage::where('original_path', $originalPath)->first();
        if ($existingGlobal && $existingGlobal->compressed_path) {
            if ($disk->exists($existingGlobal->compressed_path)) {
                return $existingGlobal->compressed_path;
            }
        }

        // Ensure file exists in storage/app/public
        if (! $disk->exists($originalPath)) {
            $this->command->warn("File not found in storage: {$originalPath}");
            return null;
        }

        $fullPath = storage_path('app/public/' . $originalPath);

        try {
            $img = Image::make($fullPath);
        } catch (\Exception $e) {
            $this->command->error("Intervention failed to read file: {$originalPath} — " . $e->getMessage());
            // Still store original to GlobalImage (since user wanted original path stored)
            GlobalImage::firstOrCreate(
                ['original_path' => $originalPath],
                ['original_path' => $originalPath, 'compressed_path' => null]
            );
            return null;
        }

        $origWidth  = $img->width();
        $origHeight = $img->height();
        $origSize   = @filesize($fullPath) ?: null;

        // Determine whether to compress or skip (avoid compressing low-res or small images)
        $isLowResolution = ($origWidth <= $this->minWidthForCompression || $origHeight <= $this->minHeightForCompression);
        $isSmallFile     = ($origSize !== null && $origSize <= $this->minSizeForCompression);

        if ($isLowResolution || $isSmallFile) {
            // store original path in GlobalImage and do not change DB reference
            GlobalImage::firstOrCreate(
                ['original_path' => $originalPath],
                ['original_path' => $originalPath, 'compressed_path' => null]
            );
            return $originalPath;
        }

        // Resize to limits (keep aspect ratio)
        $img->resize($this->maxWidth, $this->maxHeight, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Decide output extension based on original extension
        $ext = strtolower(pathinfo($originalPath, PATHINFO_EXTENSION));
        $validExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tiff'];
        $outputExt = in_array($ext, $validExts) ? $ext : 'jpg';

        // Prepare compressed filename in the same folder as original (to keep tidy)
        $dirname = pathinfo($originalPath, PATHINFO_DIRNAME);
        if ($dirname === '.' || $dirname === '') {
            $dirname = ''; // root of public storage
        }
        $baseFilename = pathinfo($originalPath, PATHINFO_FILENAME);

        // First create a compressed JPG/PNG (or keep PNG if original was PNG)
        $targetExt = $outputExt === 'png' ? 'png' : 'jpg';
        $quality = $targetExt === 'png' ? 80 : 60;
        $compressedFilename = 'compressed_' . $baseFilename . '.' . $targetExt;
        $compressedPath = ($dirname ? ($dirname . '/') : '') . $compressedFilename;

        try {
            $disk->put($compressedPath, (string) $img->encode($targetExt, $quality));
        } catch (\Exception $e) {
            $this->command->error("Failed to write compressed file for {$originalPath}: " . $e->getMessage());
            // still save original path
            GlobalImage::firstOrCreate(
                ['original_path' => $originalPath],
                ['original_path' => $originalPath, 'compressed_path' => null]
            );
            return $originalPath;
        }

        // Try creating a webp version and prefer it when successful
        try {
            $webpFilename = 'compressed_' . $baseFilename . '.webp';
            $webpPath = ($dirname ? ($dirname . '/') : '') . $webpFilename;
            $disk->put($webpPath, (string) $img->encode('webp', 60));
            // prefer webp
            $finalPath = $webpPath;
        } catch (\Exception $e) {
            // webp failed; use the previously created compressed jpg/png
            $finalPath = $compressedPath;
        }

        // Save original and compressed in GlobalImage
        GlobalImage::firstOrCreate(
            ['original_path' => $originalPath],
            ['original_path' => $originalPath, 'compressed_path' => $finalPath]
        );

        return $finalPath;
    }
}
