<?php

namespace App\Traits;

use App\Models\GlobalImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

trait ImageTrait
{
    /**
     * @param Request $request
     * @return $this|false|string
     */
    public function imageUpload($file, $path, $compress = true)
    {
        if (!$file) return null;

        $disk = Storage::disk('public');
        $filename = date('YmdHis') . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Store original
        $originalPath = $file->storeAs($path, $filename, 'public');

        $compressedPath = null;

        // Basic validation: only try to compress if it's an uploaded file and compression requested
        if ($compress && $file->isValid()) {

            // Determine extension & mime (use client extension + getMimeType as safeguard)
            $ext = strtolower($file->getClientOriginalExtension());
            $clientMime = $file->getMimeType() ?: '';

            // Allowed image extensions / mime prefix
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tiff'];
            $isImageExt = in_array($ext, $allowedExts, true);
            $isImageMime = stripos($clientMime, 'image/') === 0;

            // If it isn't recognized as an image, skip compression
            if (! $isImageExt || ! $isImageMime) {
                return $originalPath;
            }

            try {
                // Use ImageManager (Intervention v3+)
                if (extension_loaded('imagick')) {
                    $manager = ImageManager::imagick();
                } else {
                    $manager = ImageManager::gd();
                }
                $img = $manager->read($file->getRealPath());
                // Get original properties
                $origWidth  = $img->width();
                $origHeight = $img->height();
                $origSize   = $file->getSize(); // bytes

                // --- Decision: skip compression if image is already low-res or small ---
                $minWidthForCompression  = 700;    // px
                $minHeightForCompression = 700;    // px
                $minSizeForCompression   = 150 * 1024; // bytes (150 KB)

                $isLowResolution = ($origWidth <= $minWidthForCompression || $origHeight <= $minHeightForCompression);
                $isSmallFile     = ($origSize !== null && $origSize <= $minSizeForCompression);

                if ($isLowResolution || $isSmallFile) {
                    return $originalPath;
                }

                // --- Resize if too large (but keep aspect ratio and prevent upsize) ---
                $maxWidth = 2000;
                $maxHeight = 2000;
                $img->resize($maxWidth, $maxHeight, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                // Decide output extension / encoding
                if ($ext === 'png') {
                    $outputExt = 'png';
                    $quality = 70;
                } else {
                    $outputExt = in_array($ext, ['jpg', 'jpeg']) ? 'jpg' : $ext;
                    $quality = 60;
                }

                // Choose encoder instance per output extension
                if ($outputExt === 'png') {
                    $encoder = new \Intervention\Image\Encoders\PngEncoder();
                } elseif ($outputExt === 'jpg' || $outputExt === 'jpeg') {
                    $encoder = new \Intervention\Image\Encoders\JpegEncoder($quality);
                } elseif ($outputExt === 'gif') {
                    $encoder = new \Intervention\Image\Encoders\GifEncoder();
                } elseif ($outputExt === 'bmp') {
                    $encoder = new \Intervention\Image\Encoders\BmpEncoder();
                } else {
                    $encoder = new \Intervention\Image\Encoders\AutoEncoder();
                }

                // Encode compressed image
                $imgStream = $img->encode($encoder);
                $compressedFilename = 'compressed_' . pathinfo($filename, PATHINFO_FILENAME) . '.' . $outputExt;
                $disk->put("$path/$compressedFilename", (string) $imgStream);
                $compressedPath = "$path/$compressedFilename";

                // Optional: produce a WebP; if errors, ignore silently
                try {
                    $webpQuality = 60;
                    $webpEncoder = new \Intervention\Image\Encoders\WebpEncoder($webpQuality);
                    $webpStream = $img->encode($webpEncoder);
                    $webpFilename = 'compressed_' . pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                    $disk->put("$path/$webpFilename", (string) $webpStream);
                    $compressedPath = "$path/$webpFilename";
                } catch (\Exception $e) {
                    // ignore webp errors
                }
            } catch (\Exception $e) {
                // If Intervention fails (corrupt or unsupported), return original path
                return $originalPath;
            }
        }

        // Store in global table
        GlobalImage::create([
            'original_path'   => $originalPath,
            'compressed_path' => $compressedPath,
        ]);

        return $compressedPath ?? $originalPath;
    }
}
