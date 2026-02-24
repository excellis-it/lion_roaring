<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $maildata;

    /**
     * Create a new message instance.
     */
    public function __construct($maildata)
    {
        $this->maildata = $maildata;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->view('user.emails.subscription_reminder')
            ->subject('Your subscription will expire soon')
            ->with('maildata', $this->maildata);
    }
}
