<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\FCMService;
use Illuminate\Support\Facades\Log;

class SendFCMNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $token;
    public $title;
    public $body;
    public $data;

    /**
     * Create a new job instance.
     */
    public function __construct($token, $title, $body, $data = [])
    {
        $this->token = $token;
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(FCMService $fcmService)
    {
        try {
            $fcmService->sendToDevice($this->token, $this->title, $this->body, $this->data);
        } catch (\Exception $e) {
            Log::error('FCM notification job failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
