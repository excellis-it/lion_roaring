<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class SendUserNameMail extends Mailable
{
    use Queueable;

    protected array $check;

    protected array $details;

    public function __construct(array $check, array $details)
    {
        $this->check = $check;
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('user.emails.SendUserNameMail')->with(['check' => $this->check, 'details' => $this->details]);
    }
}
