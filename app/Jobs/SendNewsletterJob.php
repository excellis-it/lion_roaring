<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\EcomNewsletter;
use App\Mail\NewsletterMailable;
use Illuminate\Support\Facades\Mail;
class SendNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ids;
    public $subject;
    public $body;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $ids, string $subject, string $body)
    {
        $this->ids = $ids;
        $this->subject = $subject;
        $this->body = $body;
        // optionally set timeout/tries:
        // $this->tries = 3;
        // $this->timeout = 120;
    }

    public function handle()
    {
        // fetch recipients in chunks (avoid loading too many at once)
        EcomNewsletter::whereIn('id', $this->ids)
            ->select('email', 'name')
            ->chunk(15, function ($rows) {
                foreach ($rows as $row) {
                    if (!$row->email) continue;
                    // queue a mailable per recipient
                    Mail::to($row->email)
                        ->queue(new NewsletterMailable($this->subject, $this->body, $row->name ?? null));
                }
            });
    }
}
