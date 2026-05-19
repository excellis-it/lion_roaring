<?php

namespace App\Mail;

use App\Models\EstoreOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OrderStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public $templateBody;

    /** @var array<int, array{disk?: string|null, path: string, name: string}> */
    private array $orderFileAttachments;

    public function __construct(EstoreOrder $order, string $templateBody, array $orderFileAttachments = [])
    {
        $this->order = $order;
        $this->templateBody = $templateBody;
        $this->orderFileAttachments = $orderFileAttachments;
    }

    public function build()
    {
        $mail = $this
            ->subject('Order Update')
            ->view('user.emails.order_status_updated')
            ->with([
                'body' => $this->templateBody,
            ]);

        foreach ($this->orderFileAttachments as $attachment) {
            $name = $attachment['name'] ?? basename($attachment['path'] ?? 'file');
            $attached = false;

            if (!empty($attachment['disk']) && !empty($attachment['path'])) {
                try {
                    if (Storage::disk($attachment['disk'])->exists($attachment['path'])) {
                        $mail->attachFromStorageDisk($attachment['disk'], $attachment['path'], $name);
                        $attached = true;
                    }
                } catch (\Throwable $e) {
                    Log::warning('Failed to attach file from storage disk', [
                        'disk' => $attachment['disk'],
                        'path' => $attachment['path'],
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if (!$attached && !empty($attachment['path']) && is_file($attachment['path'])) {
                try {
                    $mail->attach($attachment['path'], ['as' => $name]);
                } catch (\Throwable $e) {
                    Log::warning('Failed to attach file from absolute path', [
                        'path' => $attachment['path'],
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return $mail;
    }
}
