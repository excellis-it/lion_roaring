<?php

namespace App\Traits;

use App\Models\CmsContent;
use Illuminate\Support\Facades\Storage;

trait HasCountryCms
{
    /**
     * Boot the trait
     */
    public static function bootHasCountryCms()
    {
        // static::saved(function ($model) {
        //     $model->syncCmsContent();
        // });

        static::deleted(function ($model) {
            $model->deleteCmsContent();
        });
    }

    /**
     * Sync this model's data to cms_contents
     */
    public function syncCmsContent($countryCode = 'US')
    {
        $data = $this->toArray();

        // Optional: handle file paths - copy them to public storage if needed
        foreach ($data as $key => $value) {
            if (is_string($value) && $this->isFileField($key) && $value) {
                // Copy file to storage folder
                $newPath = $this->copyFile($value);
                $data[$key] = $newPath;
            }
        }

        CmsContent::updateOrCreate(
            [
                'page' => $this->getCmsPageName(),
                'model_name' => get_class($this),
                'slug' => $this->getCmsSlug(),
                'country_code' => $countryCode,
            ],
            [
                'content' => $data
            ]
        );
    }

    /**
     * Delete CMS content when model is deleted
     */
    public function deleteCmsContent($countryCode = 'US')
    {
        CmsContent::where('page', $this->getCmsPageName())
            ->where('model_name', get_class($this))
            ->where('slug', $this->getCmsSlug())
            ->where('country_code', $countryCode)
            ->delete();
    }

    /**
     * Determine if a field is a file (image/logo)
     */
    protected function isFileField($field)
    {
        // Add all file/image fields of the CMS here
        return in_array($field, ['footer_logo', 'banner_image', 'other_file_field']);
    }

    /**
     * Copy existing file to storage path for CMS
     */
    protected function copyFile($filePath)
    {
        // If it's already in cms_files, don't try to copy again
        if (is_string($filePath) && strpos($filePath, 'cms_files/') === 0) {
            return $filePath;
        }

        // If the source doesn't exist on the current disk, keep original path
        if (!Storage::exists($filePath)) {
            return $filePath;
        }

        $filename = basename($filePath);
        $directory = 'cms_files';

        // Ensure the target directory exists
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        $newPath = $directory . '/' . $filename;

        // Avoid Flysystem v3 "File already exists at path" errors
        if (Storage::exists($newPath)) {
            // Reuse the existing file path instead of copying again
            return $newPath;
        }

        try {
            Storage::copy($filePath, $newPath);
        } catch (\Throwable $e) {
            // Fallback: copy by reading and writing if copy fails
            try {
                $contents = Storage::get($filePath);
                Storage::put($newPath, $contents);
            } catch (\Throwable $e2) {
                // If all fails, return original path so the process doesn't break
                return $filePath;
            }
        }

        return $newPath;
    }

    /**
     * Return CMS page name
     */
    public function getCmsPageName()
    {
        // Override this method in each model if needed
        return strtolower(class_basename($this)); // e.g., 'ecomfootercms'
    }

    /**
     * Return CMS slug if applicable
     */
    public function getCmsSlug()
    {
        return $this->slug ?? null;
    }
}
