<?php

namespace App\Mail;

use App\Models\SupportReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportReportSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public SupportReport $report;

    public function __construct(SupportReport $report)
    {
        $this->report = $report;
    }

    public function build(): self
    {
        return $this->subject('New Support Report Submitted: ' . $this->report->subject)
            ->view('emails.support_report_submitted')
            ->with(['report' => $this->report]);
    }
}
