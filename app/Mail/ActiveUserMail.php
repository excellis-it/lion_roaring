<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActiveUserMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    Public $maildata;

    public function __construct($maildata)
    {
        $this->maildata = $maildata;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->maildata['status'] == 1) {
            return $this->markdown('user.emails.ActiveUserMail')->subject('Active User Mail')->with('maildata', $this->maildata);
        } else {
            return $this->markdown('user.emails.ActiveUserMail')->subject('Pending User Mail')->with('maildata', $this->maildata);
        }
    }
}
