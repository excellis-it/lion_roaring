<?php

namespace App\Mail;

use App\Models\EventRsvp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventRsvpConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $rsvp;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(EventRsvp $rsvp)
    {
        $this->rsvp = $rsvp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('RSVP Confirmed: ' . $this->rsvp->event->title)
            ->view('emails.event-rsvp-confirmation')
            ->with([
                'rsvp' => $this->rsvp,
                'event' => $this->rsvp->event,
                'user' => $this->rsvp->user,
                'accessUrl' => route('event.access', $this->rsvp->event_id),
            ]);
    }
}
