<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectText;
    public $bodyHtml;
    public $recipientName;

    public function __construct($subject, $bodyHtml, $recipientName = null)
    {
        $this->subjectText = $subject;
        $this->bodyHtml = $bodyHtml;
        $this->recipientName = $recipientName;
    }

    public function build()
    {
        return $this
            ->subject($this->subjectText)
            ->view('user.emails.newsletter_plain') // or ->markdown('emails.newsletter_md')
            ->with([
                'bodyHtml' => $this->bodyHtml,
                'recipientName' => $this->recipientName,
            ]);
    }
}
