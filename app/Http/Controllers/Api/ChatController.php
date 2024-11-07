<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Notification;
use App\Models\User;

/**
 * @group Chats
 */


class ChatController extends Controller
{

    protected $successStatus = 200;
    /**
     * List of Chat Users
     * 
     * Retrieves a list of users with whom the authenticated user has chatted, sorted by the most recent message.
     * @authenticated
     * @response 200 [
     *  {
     *      "id": 12,
     *      "ecclesia_id": 2,
     *      "created_id": "1",
     *      "user_name": "swarnadwip_nath",
     *      "first_name": "Swarnadwip",
     *      "middle_name": null,
     *      "last_name": "Nath",
     *      "email": "swarnadwip@excellisit.net",
     *      "phone": "+1 0741202022",
     *      "email_verified_at": null,
     *      "profile_picture": "profile_picture/yCvplMhdpjc0kIeKG63tfkZwhKNYbcF1ZhfQdDFO.jpg",
     *      "address": "Kokata",
     *      "city": "Kolkata",
     *      "state": "41",
     *      "address2": null,
     *      "country": "101",
     *      "zip": "700001",
     *      "status": 1,
     *      "created_at": "2024-06-21T11:31:27.000000Z",
     *      "updated_at": "2024-09-09T11:02:59.000000Z",
     *      "last_message": {
     *          "id": 551,
     *          "sender_id": 12,
     *          "reciver_id": 37,
     *          "message": "hello",
     *          "deleted_for_sender": 0,
     *          "deleted_for_reciver": 0,
     *          "attachment": null,
     *          "seen": 1,
     *          "created_at": "2024-11-05T11:08:58.000000Z",
     *          "updated_at": "2024-11-05T11:08:58.000000Z",
     *          "delete_from_sender_id": 0,
     *          "delete_from_receiver_id": 0
     *      }
     *  },
     *  {
     *      "id": 26,
     *      "ecclesia_id": null,
     *      "created_id": null,
     *      "user_name": "ss011",
     *      "first_name": "TEST",
     *      "middle_name": null,
     *      "last_name": "1",
     *      "email": "ss011@yopmail.com",
     *      "phone": "+1 849-804-8085",
     *      "email_verified_at": "2024-08-03T05:53:38.000000Z",
     *      "profile_picture": null,
     *      "address": "123, Main street",
     *      "city": "santa Ana",
     *      "state": "165",
     *      "address2": "TEST",
     *      "country": "4",
     *      "zip": "98377",
     *      "status": 1,
     *      "created_at": "2024-08-03T05:53:38.000000Z",
     *      "updated_at": "2024-09-09T06:27:33.000000Z",
     *      "last_message": {
     *          "id": 548,
     *          "sender_id": 26,
     *          "reciver_id": 37,
     *          "message": "dafad",
     *          "deleted_for_sender": 0,
     *          "deleted_for_reciver": 0,
     *          "attachment": null,
     *          "seen": 1,
     *          "created_at": "2024-10-28T09:48:47.000000Z",
     *          "updated_at": "2024-10-28T09:48:50.000000Z",
     *          "delete_from_sender_id": 0,
     *          "delete_from_receiver_id": 0
     *      }
     *  },
     *  {
     *      "id": 30,
     *      "ecclesia_id": null,
     *      "created_id": null,
     *      "user_name": "kalyan",
     *      "first_name": "Kalyan",
     *      "middle_name": null,
     *      "last_name": "Vaduri",
     *      "email": "kalyan@yopmail.com",
     *      "phone": "+1 (444) 444-4444",
     *      "email_verified_at": "2024-08-14T11:30:31.000000Z",
     *      "profile_picture": null,
     *      "address": "51 DN Block Merlin Infinite Building, 9th Floor, Unit, 907, Sector V, Bidhannagar, Kolkata, West Bengal 700091",
     *      "city": "Kolkata",
     *      "state": "3983",
     *      "address2": "East Riding Of Yorkshire",
     *      "country": "233",
     *      "zip": "700091",
     *      "status": 1,
     *      "created_at": "2024-08-14T11:30:31.000000Z",
     *      "updated_at": "2024-08-26T15:06:47.000000Z",
     *      "last_message": null
     *  }
     * ]
     */

    public function chats(Request $request)
    {
        $chat_users = User::where('id', '!=', auth()->id())
            ->where('status', 1)
            ->get()
            ->toArray();

        // Append the last message to each user
        $chat_users = array_map(function ($user) {
            $user['last_message'] = Chat::where(function ($query) use ($user) {
                $query->where(function ($subQuery) use ($user) {
                    $subQuery->where('sender_id', $user['id'])
                        ->where('reciver_id', auth()->id());
                })->orWhere(function ($subQuery) use ($user) {
                    $subQuery->where('sender_id', auth()->id())
                        ->where('reciver_id', $user['id']);
                });
            })->where(function ($query) {
                $query->where('deleted_for_reciver', 0)
                    ->orWhere('deleted_for_sender', 0);
            })->orderBy('created_at', 'desc')->first();

            return $user;
        }, $chat_users);

        // Sort users based on the latest message timestamp, placing users with no messages at the end
        usort($chat_users, function ($a, $b) {
            if ($a['last_message'] === null) {
                return 1; // Move users with no messages to the end
            }
            if ($b['last_message'] === null) {
                return -1; // Move users with no messages to the end
            }

            return $b['last_message']->created_at <=> $a['last_message']->created_at;
        });

        return response()->json($chat_users, 200);
    }


    /**
     * Chats with a specific user
     * 
     * Retrieves the chat history between the authenticated user and a specified recipient, marking unseen messages as seen.
     * @authenticated
     * @bodyParam sender_receiver_id integer required ID of the sender (the another user). Example: 1
     * 
     * @response 200 {
     *    "message": "Show Chat",
     *    "status": true,
     *    "chat_count": 3,
     *    "unseen_chat": [
     *        {
     *            "id": 277,
     *            "sender_id": null,
     *            "reciver_id": null,
     *            "message": null,
     *            "deleted_for_sender": 0,
     *            "deleted_for_reciver": 0,
     *            "attachment": "chat/M74Omwgv7inwepyRBswxsKeCPuKjp2jnypqeoTL5.jpg",
     *            "seen": 0,
     *            "created_at": "2024-08-20T13:00:18.000000Z",
     *            "updated_at": "2024-08-20T13:00:18.000000Z",
     *            "delete_from_sender_id": 0,
     *            "delete_from_receiver_id": 0
     *        }
     *    ],
     *    "chats": [
     *        {
     *            "id": 549,
     *            "sender_id": 37,
     *            "reciver_id": 12,
     *            "message": "hii",
     *            "deleted_for_sender": 0,
     *            "deleted_for_reciver": 0,
     *            "attachment": null,
     *            "seen": 1,
     *            "created_at": "2024-11-05T11:08:36.000000Z",
     *            "updated_at": "2024-11-05T11:08:42.000000Z",
     *            "delete_from_sender_id": 0,
     *            "delete_from_receiver_id": 0
     *        },
     *        {
     *            "id": 550,
     *            "sender_id": 12,
     *            "reciver_id": 37,
     *            "message": null,
     *            "deleted_for_sender": 0,
     *            "deleted_for_reciver": 0,
     *            "attachment": "chat/wTFuaiG3kyE7DVDZVcQ8q3eB06Zlb5Mp0VlMeCe6.pdf",
     *            "seen": 1,
     *            "created_at": "2024-11-05T11:08:52.000000Z",
     *            "updated_at": "2024-11-05T11:08:53.000000Z",
     *            "delete_from_sender_id": 0,
     *            "delete_from_receiver_id": 0
     *        },
     *        {
     *            "id": 551,
     *            "sender_id": 12,
     *            "reciver_id": 37,
     *            "message": "hello",
     *            "deleted_for_sender": 0,
     *            "deleted_for_reciver": 0,
     *            "attachment": null,
     *            "seen": 1,
     *            "created_at": "2024-11-05T11:08:58.000000Z",
     *            "updated_at": "2024-11-05T11:08:58.000000Z",
     *            "delete_from_sender_id": 0,
     *            "delete_from_receiver_id": 0
     *        }
     *    ]
     *}
     * @response 500 {
     *   "msg": "An error occurred while loading chats.",
     *   "status": false
     * }
     */
    public function load(Request $request)
    {
        try {

            $chats = Chat::where(function ($query) use ($request) {
                $query->where('sender_id', $request->sender_receiver_id)
                    ->where('reciver_id', auth()->id())
                    ->where('deleted_for_sender', 0)
                    ->where('delete_from_sender_id', 0);
            })
                ->orWhere(function ($query) use ($request) {
                    $query->where('sender_id', auth()->id())
                        ->where('reciver_id', $request->sender_receiver_id)
                        ->where('deleted_for_reciver', 0)
                        ->where('delete_from_receiver_id', 0);
                })
                ->orderBy('created_at', 'asc')
                ->get();
            // return $chats;
            // Mark unseen messages as seen
            $chats->each(function ($chat) {
                if ($chat->reciver_id == auth()->id() && $chat->seen == 0) {
                    $chat->update(['seen' => 1]);
                }
            });

            $chat_count = $chats->count();

            // Get unseen chats
            $unseen_chat = Chat::where('sender_id', $request->reciver_id)
                ->where('reciver_id', $request->sender_id)
                ->where('seen', 0)
                ->get();

            return response()->json([
                'message' => 'Show Chat',
                'status' => true,
                'chat_count' => $chat_count,
                'unseen_chat' => $unseen_chat,
                'chats' => $chats,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'An error occurred while loading chats.',
                'status' => false,
            ], 500);
        }
    }





    //
}
