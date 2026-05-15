<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ProductFile extends Model
{
    use HasFactory, SoftDeletes;

    /** Max size (bytes) to attach to email — larger files use download link only */
    public const MAX_EMAIL_ATTACHMENT_BYTES = 10485760; // 10 MB

    public const SIGNED_DOWNLOAD_EXPIRY_DAYS = 30;

    protected $fillable = [
        'product_id',
        'file_location',
    ];

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

    public function getFileSize(): ?int
    {
        $location = $this->normalizeFileLocation();
        if (!$location) {
            return null;
        }

        if (Storage::disk('public')->exists($location)) {
            return Storage::disk('public')->size($location);
        }

        if (Storage::disk('local')->exists($location)) {
            return Storage::disk('local')->size($location);
        }

        $absolutePath = storage_path('app/public/' . $location);
        if (is_file($absolutePath)) {
            return filesize($absolutePath);
        }

        return null;
    }

    public function shouldAttachToEmail(): bool
    {
        $size = $this->getFileSize();

        return $size !== null && $size > 0 && $size <= self::MAX_EMAIL_ATTACHMENT_BYTES;
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
        if (!$this->shouldAttachToEmail()) {
            return null;
        }

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

    public function getSignedDownloadUrl(EstoreOrder $order): string
    {
        return URL::temporarySignedRoute(
            'e-store.guest-download-file',
            now()->addDays(self::SIGNED_DOWNLOAD_EXPIRY_DAYS),
            [
                'order' => $order->id,
                'file' => $this->id,
            ]
        );
    }

    public function streamDownloadResponse()
    {
        $location = $this->normalizeFileLocation();
        if (!$location) {
            return null;
        }

        $fileName = basename($location);

        if (Storage::disk('public')->exists($location)) {
            return Storage::disk('public')->download($location, $fileName);
        }

        if (Storage::disk('local')->exists($location)) {
            return Storage::disk('local')->download($location, $fileName);
        }

        $absolutePath = $this->getAbsolutePath();
        if ($absolutePath && is_file($absolutePath)) {
            return response()->download($absolutePath, $fileName);
        }

        return null;
    }

    public function belongsToOrder(EstoreOrder $order): bool
    {
        $order->loadMissing('orderItems');

        return $order->orderItems->contains('product_id', $this->product_id);
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
                }
            }
        }

        return $attachments;
    }

    public static function downloadLinksHtml(iterable $products, ?EstoreOrder $order = null): string
    {
        $html = '';

        foreach ($products as $product) {
            if (!$product || $product->product_type !== 'digital') {
                continue;
            }

            $product->loadMissing('files');

            foreach ($product->files as $file) {
                $downloadUrl = $order
                    ? $file->getSignedDownloadUrl($order)
                    : route('e-store.download-file', $file->id);

                $fileName = basename($file->normalizeFileLocation() ?? $file->file_location);
                $html .= "<p style='margin-top:8px;'><a href='" . e($downloadUrl) . "' style='display:inline-block;padding:10px 20px;background:#28a745;color:#fff;text-decoration:none;border-radius:5px;'>Download " . e($fileName) . "</a></p>";
            }
        }

        return $html;
    }

    public static function emailExtrasForProducts(iterable $products, ?EstoreOrder $order = null): array
    {
        $attachments = self::attachmentsForProducts($products);
        $downloadLinks = self::downloadLinksHtml($products, $order);

        $attachmentNotice = '';
        if ($downloadLinks !== '') {
            if (!empty($attachments)) {
                $attachmentNotice = "<p style='margin-top:12px;'><strong>Small file(s) are attached. For larger files, use the Download button below.</strong></p>";
            } else {
                $attachmentNotice = "<p style='margin-top:12px;'><strong>Click the Download button below to get your file(s). No login required — link valid for " . self::SIGNED_DOWNLOAD_EXPIRY_DAYS . " days.</strong></p>";
            }
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

        return self::emailExtrasForProducts($products, $order);
    }
}
