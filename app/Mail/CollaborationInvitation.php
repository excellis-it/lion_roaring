<?php

namespace App\Mail;

use App\Models\PrivateCollaboration;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CollaborationInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $collaboration;
    public $invitedUser;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PrivateCollaboration $collaboration, User $invitedUser)
    {
        $this->collaboration = $collaboration;
        $this->invitedUser = $invitedUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('You have been invited to a Private Collaboration')
            ->view('emails.collaboration_invitation')
            ->with([
                'collaboration' => $this->collaboration,
                'invitedUser' => $this->invitedUser,
                'creator' => $this->collaboration->user,
            ]);
    }
}
