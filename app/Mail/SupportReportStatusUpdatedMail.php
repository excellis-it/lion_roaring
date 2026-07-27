<?php

namespace App\Mail;

use App\Models\SupportReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportReportStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public SupportReport $report;

    public function __construct(SupportReport $report)
    {
        $this->report = $report;
    }

    public function build(): self
    {
        return $this->subject('Your Support Report Has Been Updated: ' . $this->report->subject)
            ->view('emails.support_report_status_updated')
            ->with(['report' => $this->report]);
    }
}
