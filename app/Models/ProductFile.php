<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'file_location',
    ];

    /**
     * Relationship: ProductFile belongs to Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function normalizeFileLocation(): ?string
    {
        if (!$this->file_location) {
            return null;
        }

        $location = str_replace('\\', '/', trim($this->file_location));

        if (str_contains($location, '://')) {
            $path = parse_url($location, PHP_URL_PATH);
            $location = $path ? ltrim($path, '/') : $location;
        }

        $location = ltrim($location, '/');
        $location = preg_replace('#^(storage/|public/)#', '', $location);

        return $location ?: null;
    }

    public function getAbsolutePath(): ?string
    {
        $location = $this->normalizeFileLocation();
        if (!$location) {
            return null;
        }

        if (Storage::disk('public')->exists($location)) {
            return Storage::disk('public')->path($location);
        }

        if (Storage::disk('local')->exists($location)) {
            return Storage::disk('local')->path($location);
        }

        $publicPath = storage_path('app/public/' . $location);
        if (is_file($publicPath)) {
            return $publicPath;
        }

        return null;
    }

    /**
     * @return array{disk: string, path: string, name: string}|null
     */
    public function getStorageAttachment(): ?array
    {
        $location = $this->normalizeFileLocation();
        if (!$location) {
            return null;
        }

        if (Storage::disk('public')->exists($location)) {
            return [
                'disk' => 'public',
                'path' => $location,
                'name' => basename($location),
            ];
        }

        if (Storage::disk('local')->exists($location)) {
            return [
                'disk' => 'local',
                'path' => $location,
                'name' => basename($location),
            ];
        }

        $absolutePath = $this->getAbsolutePath();
        if ($absolutePath) {
            return [
                'disk' => null,
                'path' => $absolutePath,
                'name' => basename($location),
            ];
        }

        return null;
    }

    /**
     * @return array<int, array{disk: string|null, path: string, name: string}>
     */
    public static function attachmentsForProducts(iterable $products): array
    {
        $attachments = [];

        foreach ($products as $product) {
            if (!$product || $product->product_type !== 'digital') {
                continue;
            }

            $product->loadMissing('files');

            foreach ($product->files as $file) {
                $attachment = $file->getStorageAttachment();
                if ($attachment) {
                    $attachments[] = $attachment;
                } else {
                    Log::warning('Digital product file not found for email attachment', [
                        'product_id' => $product->id,
                        'file_id' => $file->id,
                        'file_location' => $file->file_location,
                    ]);
                }
            }
        }

        return $attachments;
    }

    /**
     * @return array<int, array{disk: string|null, path: string, name: string}>
     */
    public static function attachmentsForOrder(EstoreOrder $order): array
    {
        $order->loadMissing('orderItems.product.files');

        return self::attachmentsForProducts(
            $order->orderItems->map(fn ($item) => $item->product)->filter()
        );
    }

    public static function downloadLinksHtml(iterable $products): string
    {
        $html = '';

        foreach ($products as $product) {
            if (!$product || $product->product_type !== 'digital') {
                continue;
            }

            $product->loadMissing('files');

            foreach ($product->files as $file) {
                $downloadUrl = route('e-store.download-file', $file->id);
                $fileName = basename($file->normalizeFileLocation() ?? $file->file_location);
                $html .= "<p style='margin-top:8px;'><a href='" . $downloadUrl . "' style='display:inline-block;padding:10px 20px;background:#28a745;color:#fff;text-decoration:none;border-radius:5px;'>Download " . e($fileName) . "</a></p>";
            }
        }

        return $html;
    }

    public static function emailExtrasForProducts(iterable $products): array
    {
        $attachments = self::attachmentsForProducts($products);
        $downloadLinks = self::downloadLinksHtml($products);

        $attachmentNotice = '';
        if (!empty($attachments)) {
            $attachmentNotice = "<p style='margin-top:12px;'><strong>Your product file(s) are attached to this email.</strong></p>";
        } elseif ($downloadLinks !== '') {
            $attachmentNotice = "<p style='margin-top:12px;'><strong>Click the button below to download your product file(s).</strong></p>";
        }

        return [
            'attachments' => $attachments,
            'html' => $attachmentNotice . $downloadLinks,
        ];
    }

    public static function emailExtrasForOrder(EstoreOrder $order): array
    {
        $order->loadMissing('orderItems.product.files');
        $products = $order->orderItems->map(fn ($item) => $item->product)->filter();

        return self::emailExtrasForProducts($products);
    }
}
