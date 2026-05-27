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
        $subjectTemplate = $this->maildata['custom_subject'] ?? 'Your subscription will expire soon';
        $customBodyTemplate = $this->maildata['custom_body'] ?? null;

        return $this->view('user.emails.subscription_reminder')
            ->subject($this->replacePlaceholders($subjectTemplate))
            ->with([
                'maildata' => $this->maildata,
                'customBodyHtml' => !empty($customBodyTemplate)
                    ? $this->replacePlaceholders($customBodyTemplate)
                    : null,
            ]);
    }

    private function replacePlaceholders(string $content): string
    {
        $replacements = [
            '{{name}}' => $this->maildata['name'] ?? 'Member',
            '{{subscription_name}}' => $this->maildata['subscription_name'] ?? 'Membership',
            '{{start_date}}' => $this->maildata['start_date'] ?? 'N/A',
            '{{expire_date}}' => $this->maildata['expire_date'] ?? 'N/A',
            '{{days_remaining}}' => (string) ($this->maildata['days_remaining'] ?? ''),
            '{{renew_url}}' => $this->maildata['renew_url'] ?? route('user.membership.index'),
            '{{app_name}}' => config('app.name'),
        ];

        return strtr($content, $replacements);
    }
}
