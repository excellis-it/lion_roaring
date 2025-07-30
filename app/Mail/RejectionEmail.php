<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RejectionEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $partner;
    public $reason;

    public function __construct(User $partner, $reason)
    {
        $this->partner = $partner;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('User Rejection Notification')
            ->view('admin.emails.RejectionEmail')
            ->with([
                'partner' => $this->partner,
                'reason' => $this->reason,
            ]);
    }
}
