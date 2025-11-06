<?php

namespace App\Mail;

use App\Models\PrivateCollaboration;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CollaborationAccepted extends Mailable
{
    use Queueable, SerializesModels;

    public $collaboration;
    public $acceptedUser;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PrivateCollaboration $collaboration, User $acceptedUser)
    {
        $this->collaboration = $collaboration;
        $this->acceptedUser = $acceptedUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('A user has accepted your collaboration invitation')
            ->view('emails.collaboration_accepted')
            ->with([
                'collaboration' => $this->collaboration,
                'acceptedUser' => $this->acceptedUser,
                'creator' => $this->collaboration->user,
            ]);
    }
}
