<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;

class SystemNotification extends DatabaseNotification
{
    protected $table = 'system_notifications'; // Custom table for Laravel notifications
}
