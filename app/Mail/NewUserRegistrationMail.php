<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The mail data for the new registration notification.
     *
     * @var array
     */
    public $maildata;

    /**
     * Create a new message instance.
     *
     * @param array $maildata
     * @return void
     */
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
        return $this->view('user.emails.NewUserRegistrationMail')
            ->subject('New User Registration - ' . config('app.name'))
            ->with('maildata', $this->maildata);
    }
}
