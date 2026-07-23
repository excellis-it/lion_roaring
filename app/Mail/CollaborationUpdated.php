<?php

namespace App\Mail;

use App\Models\PrivateCollaboration;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CollaborationUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $collaboration;
    public $invitedUser;

    public function __construct(PrivateCollaboration $collaboration, User $invitedUser)
    {
        $this->collaboration = $collaboration;
        $this->invitedUser = $invitedUser;
    }

    public function build()
    {
        return $this->subject('A Private Collaboration you were invited to has been updated')
            ->view('emails.collaboration_updated')
            ->with([
                'collaboration' => $this->collaboration,
                'invitedUser' => $this->invitedUser,
                'creator' => $this->collaboration->user,
            ]);
    }
}
