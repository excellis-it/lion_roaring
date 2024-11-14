<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $mail;
    public $senderEmail;
    public $senderName;

    public function __construct($mail, $senderEmail = null, $senderName = null)
    {
        $this->mail = $mail;
        $this->senderEmail = $senderEmail;
        $this->senderName = $senderName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->markdown('user.mails.SendMail')->subject($this->mail->subject)->with('mail', $this->mail);
        return $this->from(
            $this->senderEmail ?? env('MAIL_FROM_ADDRESS'),
            $this->senderName ?? env('MAIL_FROM_NAME')
        )
            ->view('user.mails.SendMail')  // Use markdown email template
            ->subject($this->mail->subject)  // Set the email subject
            ->with('mail', $this->mail);
    }
}
