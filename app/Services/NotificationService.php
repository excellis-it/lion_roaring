<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Notifications\CommonNotification;
use Illuminate\Support\Facades\Notification as LaravelNotification;

class NotificationService
{
    /**
     * Send notification to all users.
     */
    public static function notifyAllUsers($message, $type = 'general')
    {
        try {
            $users = User::where('status', 1)->get(); // Get all active users
            // return $users;

            $notified_users = [];
            foreach ($users as $user) {
                self::saveNotification($user->id, $message, $type);
                //  $user->notify(new CommonNotification($message, $type)); // Laravel notification system
                $notified_users[] = $user->id;
            }
            // return $notified_users;

            return response()->json(['message' => 'Notification sent to all users'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 201);
        }
    }

    /**
     * Send notification from one user to another.
     */
    public static function notifyUser($toUserId, $message, $type = 'personal')
    {
        try {
            $user = User::find($toUserId);

            if ($user) {
                self::saveNotification($user->id, $message, $type);
                //  $user->notify(new CommonNotification($message, $type)); // Laravel notification system

                return response()->json(['message' => 'Notification sent successfully'], 200);
            }

            return response()->json(['error' => 'User not found'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 201);
        }
    }

    /**
     * Save notification in database.
     */
    private static function saveNotification($userId, $message, $type)
    {
        return Notification::create([
            'user_id' => $userId,
            'message' => $message,
            'status' => 1,
            'type' => $type,
            'is_read' => 0,
            'is_delete' => 0
        ]);
    }
}


// usage

// Now, you can use NotificationService in any controller.

// 1️. Send a Notification to All Users

// use App\Services\NotificationService;

// public function sendToAll()
// {
//     return NotificationService::notifyAllUsers('New update available!', 'announcement');
// }

// 2️. Send a Notification from One User to Another

// use App\Services\NotificationService;

// public function sendToUser($userId)
// {
//     return NotificationService::notifyUser($userId, 'You have a new message', 'chat');
// }
