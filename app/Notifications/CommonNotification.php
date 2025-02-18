<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\SystemNotification;

class CommonNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $message;
    public $type;

    public function __construct($message, $type)
    {
        $this->message = $message;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['database']; // Future: Add 'fcm' for Firebase push notifications
    }

    public function toDatabase($notifiable)
    {
        return [
            'notifiable_id' => $notifiable->id,
            'notifiable_type' => get_class($notifiable),
            'message' => $this->message,
            'type' => $this->type,
        ];
    }

    public function databaseType()
    {
        return SystemNotification::class;
    }
}
