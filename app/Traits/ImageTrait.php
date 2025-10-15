<?php

namespace App\Traits;

use App\Models\GlobalImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

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

        if ($compress) {
            $img = \Intervention\Image\ImageManagerStatic::make($file->getRealPath());

            // Resize if too large
            $maxWidth = 2000;
            $maxHeight = 2000;
            $img->resize($maxWidth, $maxHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $ext = strtolower($file->getClientOriginalExtension());
            if ($ext === 'png') $ext = 'jpg'; // convert PNG to JPG

            // Encode compressed image
            $imgStream = $img->encode($ext, 60);
            $compressedFilename = 'compressed_' . pathinfo($filename, PATHINFO_FILENAME) . '.' . $ext;
            $disk->put("$path/$compressedFilename", $imgStream);
            $compressedPath = "$path/$compressedFilename";

            // Optional: WebP
            try {
                $webpStream = $img->encode('webp', 60);
                $webpFilename = 'compressed_' . pathinfo($filename, PATHINFO_FILENAME) . '.webp';
                $disk->put("$path/$webpFilename", $webpStream);
                $compressedPath = "$path/$webpFilename";
            } catch (\Exception $e) {
                // ignore
            }
        }

        // Store in global table
        GlobalImage::create([
            'original_path' => $originalPath,
            'compressed_path' => $compressedPath,
        ]);

        return $compressedPath ?? $originalPath;
    }
}
