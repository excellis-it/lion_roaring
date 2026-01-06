<?php

namespace App\Mail;

use App\Models\EventPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventPaymentReceipt extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $payment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(EventPayment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Payment Receipt: ' . $this->payment->event->title)
            ->view('emails.event-payment-receipt')
            ->with([
                'payment' => $this->payment,
                'event' => $this->payment->event,
                'user' => $this->payment->user,
                'accessUrl' => route('event.access', $this->payment->event_id),
            ]);
    }
}
