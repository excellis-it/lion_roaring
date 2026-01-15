<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\GlobalImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompressCategoryImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::whereNotNull('image')->get();

        foreach ($categories as $category) {
            // Handle main category image
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                $this->compressAndUpdateImage($category, 'image');
            }

            // Handle background image (if any)
            if ($category->background_image && Storage::disk('public')->exists($category->background_image)) {
                $this->compressAndUpdateImage($category, 'background_image');
            }
        }

        $this->command->info('✅ Category images compressed and GlobalImage records created successfully.');
    }

    /**
     * Compresses a given image field and updates the model.
     */
    protected function compressAndUpdateImage($category, $field)
    {
        $oldPath = $category->$field;
        if (!$oldPath) {
            return;
        }

        $existingGlobal = GlobalImage::where('original_path', $oldPath)->first();
        if ($existingGlobal && $existingGlobal->compressed_path) {
            $compressedExists = Storage::disk('public')->exists($existingGlobal->compressed_path);
            if ($compressedExists && $category->$field === $existingGlobal->compressed_path) {
                return;
            }
        }

        if (Str::startsWith(basename($oldPath), 'compressed_')) {
            return;
        }

        $fullPath = Storage::disk('public')->path($oldPath);
        $file = new \Illuminate\Http\UploadedFile($fullPath, basename($oldPath), null, null, true);

        // Use the same imageUpload logic
        $compressedPath = $this->imageUpload($file, 'category', true);

        if ($compressedPath && $compressedPath !== $oldPath) {
            $category->update([
                $field => $compressedPath,
            ]);
        }

        // Log in GlobalImage (if not already done in imageUpload)
        if (!$existingGlobal) {
            GlobalImage::create([
                'original_path'   => $oldPath,
                'compressed_path' => $compressedPath,
            ]);
        }

        $this->command->info("Compressed {$field} for category ID {$category->id}");
    }

    /**
     * Same imageUpload logic from your controller.
     */
    protected function imageUpload($file, $path, $compress = true)
    {
        if (!$file) return null;

        $disk = Storage::disk('public');
        $filename = date('YmdHis') . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Store original
        $originalPath = $file->storeAs($path, $filename, 'public');
        $compressedPath = null;

        if ($compress && $file->isValid()) {
            $ext = strtolower($file->getClientOriginalExtension());
            $clientMime = $file->getMimeType() ?: '';

            $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tiff'];
            $isImageExt = in_array($ext, $allowedExts, true);
            $isImageMime = stripos($clientMime, 'image/') === 0;

            if (!$isImageExt || !$isImageMime) {
                return $originalPath;
            }

            try {
                $img = \Intervention\Image\ImageManagerStatic::make($file->getRealPath());

                $origWidth  = $img->width();
                $origHeight = $img->height();
                $origSize   = $file->getSize();

                $minWidthForCompression  = 1000;
                $minHeightForCompression = 1000;
                $minSizeForCompression   = 150 * 1024;

                $isLowResolution = ($origWidth <= $minWidthForCompression || $origHeight <= $minHeightForCompression);
                $isSmallFile     = ($origSize && $origSize <= $minSizeForCompression);

                if ($isLowResolution || $isSmallFile) {
                    return $originalPath;
                }

                $maxWidth = 2000;
                $maxHeight = 2000;
                $img->resize($maxWidth, $maxHeight, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                if ($ext === 'png') {
                    $outputExt = 'png';
                    $quality = 70;
                } else {
                    $outputExt = in_array($ext, ['jpg', 'jpeg']) ? 'jpg' : $ext;
                    $quality = 60;
                }

                $imgStream = $img->encode($outputExt, $quality);
                $compressedFilename = 'compressed_' . pathinfo($filename, PATHINFO_FILENAME) . '.' . $outputExt;
                $disk->put("$path/$compressedFilename", (string) $imgStream);
                $compressedPath = "$path/$compressedFilename";

                try {
                    $webpQuality = 60;
                    $webpStream = $img->encode('webp', $webpQuality);
                    $webpFilename = 'compressed_' . pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                    $disk->put("$path/$webpFilename", (string) $webpStream);
                    $compressedPath = "$path/$webpFilename";
                } catch (\Exception $e) {
                    // ignore
                }
            } catch (\Exception $e) {
                return $originalPath;
            }
        }

        GlobalImage::firstOrCreate(
            ['original_path' => $originalPath],
            [
                'original_path'   => $originalPath,
                'compressed_path' => $compressedPath,
            ]
        );

        return $compressedPath ?? $originalPath;
    }
}
