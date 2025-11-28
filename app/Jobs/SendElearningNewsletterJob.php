<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\ElearningEcomNewsletter;
use App\Mail\NewsletterMailable;
use Illuminate\Support\Facades\Mail;

class SendElearningNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ids;
    public $subject;
    public $body;

    public function __construct(array $ids, string $subject, string $body)
    {
        $this->ids = $ids;
        $this->subject = $subject;
        $this->body = $body;
    }

    public function handle()
    {
        ElearningEcomNewsletter::whereIn('id', $this->ids)
            ->select('email', 'name')
            ->chunk(15, function ($rows) {
                foreach ($rows as $row) {
                    if (!$row->email) continue;
                    Mail::to($row->email)->queue(new NewsletterMailable($this->subject, $this->body, $row->name ?? null));
                }
            });
    }
}
