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
        $ext = strtolower((string) $file->getClientOriginalExtension());
        $clientMime = (string) ($file->getMimeType() ?: '');

        // Normalize aliases so browsers can display the stored file.
        // JFIF is JPEG; HEIC/HEIF are converted to JPEG when possible.
        if ($ext === 'jfif') {
            $ext = 'jpg';
        }

        $heicConverted = false;
        $sourcePath = $file->getRealPath();

        if (in_array($ext, ['heic', 'heif'], true) || in_array($clientMime, ['image/heic', 'image/heif'], true)) {
            $converted = $this->convertHeicToJpeg($sourcePath);
            if ($converted !== null) {
                $sourcePath = $converted;
                $ext = 'jpg';
                $clientMime = 'image/jpeg';
                $heicConverted = true;
            }
        }

        // Never let the client pick the extension the public disk serves back.
        $filename = date('YmdHis') . '_' . uniqid() . '.' . \App\Helpers\Helper::safeUploadExtension($ext);

        if ($heicConverted) {
            $originalPath = $path . '/' . $filename;
            $disk->put($originalPath, file_get_contents($sourcePath));
            @unlink($sourcePath);
        } else {
            // Store original (may keep original extension when HEIC could not be converted)
            $originalPath = $file->storeAs($path, $filename, 'public');
        }

        $compressedPath = null;

        // Basic validation: only try to compress if it's an uploaded file and compression requested
        if ($compress && ($heicConverted || $file->isValid())) {

            // Allowed image extensions / mime prefix (incl. JFIF/HEIC aliases after normalize)
            $allowedExts = ['jpg', 'jpeg', 'jfif', 'png', 'gif', 'webp', 'bmp', 'tiff', 'heic', 'heif'];
            $isImageExt = in_array(strtolower((string) $file->getClientOriginalExtension()), $allowedExts, true)
                || in_array($ext, $allowedExts, true);
            $isImageMime = stripos($clientMime, 'image/') === 0
                || in_array($clientMime, ['image/heic', 'image/heif'], true);

            // HEIC without conversion is still an image attachment — skip GD compression
            if (in_array($ext, ['heic', 'heif'], true)) {
                return $originalPath;
            }

            // If it isn't recognized as an image, skip compression
            if (! $isImageExt && ! $isImageMime) {
                return $originalPath;
            }

            // Extension-only images (empty/odd mime) still compress when we know the type
            if (! $isImageExt) {
                return $originalPath;
            }

            try {
                // Use ImageManager (Intervention v3+)
                if (extension_loaded('imagick')) {
                    $manager = ImageManager::imagick();
                } else {
                    $manager = ImageManager::gd();
                }
                $img = $manager->read($heicConverted ? Storage::disk('public')->path($originalPath) : $file->getRealPath());
                // Get original properties
                $origWidth  = $img->width();
                $origHeight = $img->height();
                $origSize   = $heicConverted
                    ? $disk->size($originalPath)
                    : $file->getSize(); // bytes

                // --- Decision: skip compression if image is already low-res or small ---
                $minWidthForCompression  = 700;    // px
                $minHeightForCompression = 700;    // px
                $minSizeForCompression   = 150 * 1024; // bytes (150 KB)

                $isLowResolution = ($origWidth <= $minWidthForCompression || $origHeight <= $minHeightForCompression);
                $isSmallFile     = ($origSize !== null && $origSize <= $minSizeForCompression);

                if ($isLowResolution || $isSmallFile) {
                    return $originalPath;
                }

                // --- Resize if too large (keep aspect ratio; never upsize) ---
                // Intervention v3: use scaleDown — NOT resize(w,h) which forces exact box
                // and ignores the old v2 constraint callback (that produced 2000×2000 squares).
                $maxWidth = 2000;
                $maxHeight = 2000;
                $img->scaleDown(width: $maxWidth, height: $maxHeight);

                // Decide output extension / encoding
                if ($ext === 'png') {
                    $outputExt = 'png';
                    $quality = 70;
                } else {
                    // jfif / heic-normalized already map to jpg above
                    $outputExt = in_array($ext, ['jpg', 'jpeg', 'jfif'], true) ? 'jpg' : $ext;
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
                } elseif ($outputExt === 'webp') {
                    $encoder = new \Intervention\Image\Encoders\WebpEncoder($quality);
                } else {
                    $encoder = new \Intervention\Image\Encoders\AutoEncoder();
                }

                // Encode compressed image
                $imgStream = $img->encode($encoder);
                $compressedFilename = 'compressed_' . pathinfo($filename, PATHINFO_FILENAME) . '.' . $outputExt;
                $disk->put("$path/$compressedFilename", (string) $imgStream);
                $compressedPath = "$path/$compressedFilename";

                // Optional: produce a WebP when GD/Imagick supports it; ignore failures.
                if (function_exists('imagewebp')) {
                    try {
                        $webpQuality = 60;
                        $webpEncoder = new \Intervention\Image\Encoders\WebpEncoder($webpQuality);
                        $webpStream = $img->encode($webpEncoder);
                        $webpFilename = 'compressed_' . pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                        $disk->put("$path/$webpFilename", (string) $webpStream);
                        $compressedPath = "$path/$webpFilename";
                    } catch (\Throwable $e) {
                        // ignore webp errors — keep jpg/png compressed path
                    }
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

    /**
     * Convert HEIC/HEIF to a temporary JPEG when Imagick supports it.
     * Returns temp file path or null if conversion is unavailable.
     */
    private function convertHeicToJpeg(string $sourcePath): ?string
    {
        if (! extension_loaded('imagick') || ! is_readable($sourcePath)) {
            return null;
        }

        try {
            $imagick = new \Imagick();
            $formats = array_map('strtoupper', $imagick->queryFormats());
            if (! in_array('HEIC', $formats, true) && ! in_array('HEIF', $formats, true)) {
                return null;
            }

            $imagick->readImage($sourcePath);
            $imagick->setImageFormat('jpeg');
            $imagick->setImageCompressionQuality(85);

            $tmp = tempnam(sys_get_temp_dir(), 'heic_') . '.jpg';
            $imagick->writeImage($tmp);
            $imagick->clear();
            $imagick->destroy();

            return is_readable($tmp) ? $tmp : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
