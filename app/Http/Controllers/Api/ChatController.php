<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Notification;
use App\Models\User;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Helper;

/**
 * @group Chats
 */


class ChatController extends Controller
{

    protected $successStatus = 200;
    use ImageTrait;
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
        try {
            $chat_users = User::with('roles')->where('id', '!=', auth()->id())
                ->where('status', 1)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('type', [1, 2, 3]);
                })
                ->get();

            // Calculate unseen chat count and last message for each user
            $chat_users->each(function ($chat_user) {
                $chat_user->chat_count = Helper::getCountUnseenMessage(auth()->id(), $chat_user->id);

                // Get the last message
                $chat_user->last_message = Chat::where(function ($query) use ($chat_user) {
                    $query->where(function ($subQuery) use ($chat_user) {
                        $subQuery->where('sender_id', $chat_user->id)
                            ->where('reciver_id', auth()->id())->where('deleted_for_reciver', 0)->where('delete_from_receiver_id', 0);
                    })->orWhere(function ($subQuery) use ($chat_user) {
                        $subQuery->where('sender_id', auth()->id())
                            ->where('reciver_id', $chat_user->id)->where('deleted_for_sender', 0)->where('delete_from_sender_id', 0);
                    });
                })
                    // ->where(function ($query) {
                    //     $query->where('deleted_for_reciver', 0)
                    //         ->orWhere('deleted_for_sender', 0);
                    // })
                    ->orderBy('created_at', 'desc')->first();
            });

            // Convert to array
            $chat_users = $chat_users->toArray();

            // Sort users based on the latest message timestamp, placing users with no messages at the end
            usort($chat_users, function ($a, $b) {
                if ($a['last_message'] === null) {
                    return 1; // Move users with no messages to the end
                }
                if ($b['last_message'] === null) {
                    return -1; // Move users with no messages to the end
                }

                return strtotime($b['last_message']['created_at']) <=> strtotime($a['last_message']['created_at']);
            });

            return response()->json($chat_users, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving chats.',
                'error' => $e->getMessage()
            ], 201);
        }
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
     * @response 201 {
     *   "msg": "An error occurred while loading chats.",
     *   "status": false
     * }
     */
    public function load(Request $request)
    {
        try {

            $chats = Chat::where(function ($query) use ($request) {
                $query->where('sender_id', auth()->id())
                    ->where('reciver_id', $request->sender_receiver_id)
                    ->where('deleted_for_sender', 0)
                    ->where('delete_from_sender_id', 0);
            })
                ->orWhere(function ($query) use ($request) {
                    $query->where('sender_id', $request->sender_receiver_id)
                        ->where('reciver_id', auth()->id())
                        ->where('deleted_for_reciver', 0)
                        ->where('delete_from_receiver_id', 0);
                })
                ->orderBy('created_at', 'desc')
                ->get();
            // return $chats;
            // Mark unseen messages as seen
            $chats->each(function ($chat) {
                if ($chat->reciver_id == auth()->id() && $chat->seen == 0) {
                    $chat->update(['seen' => 1]);
                }
                $chat->isMe = ($chat->sender_id == auth()->id()) ? true : false;
                $chat->isSeen = ($chat->seen == 1) ? true : false;

                if ($chat->created_at->format('d M Y') == date('d M Y')) {
                    $chat->time = $chat->created_at->format('h:iA') . ' ' . 'Today';
                } elseif ($chat->created_at->format('d M Y') == date('d M Y', strtotime('-1 day'))) {
                    $chat->time = $chat->created_at->format('h:iA') . ' ' . 'Yesterday';
                } else {
                    $chat->time = $chat->created_at->format('h:iA') . ' ' . $chat->created_at->format('d M Y');
                }
            });

            $chat_count = $chats->count();

            // Get unseen chats
            $unseen_chat = Chat::where('sender_id', $request->sender_receiver_id)
                ->where('reciver_id', auth()->id())
                ->where('seen', 0)
                ->where('delete_from_receiver_id', 0)
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
            ], 201);
        }
    }



    /**
     * Send a chat message
     *
     * Allows the authenticated user to send a message or attachment to a specified receiver. Returns the latest message and message counts.
     * @authenticated
     * @bodyParam reciver_id integer required ID of the receiver (chat partner). Example: 2
     * @bodyParam message string Optional message text if sending a text message. Example: Hello there!
     * @bodyParam file file Optional file attachment for the chat message.
     *
     * @response 200 {
     *    "msg": "Message sent successfully",
     *    "chat": {
     *        "id": 564,
     *        "sender_id": 37,
     *        "reciver_id": 12,
     *        "message": "hello testing",
     *        "deleted_for_sender": 0,
     *        "deleted_for_reciver": 0,
     *        "attachment": "chat/wXKPA5V0cft8m66c3nR1QWdHnmxgZWv4NBj07zhL.pdf",
     *        "seen": 0,
     *        "created_at": "2024-11-07T07:25:54.000000Z",
     *        "updated_at": "2024-11-07T07:25:54.000000Z",
     *        "delete_from_sender_id": 0,
     *        "delete_from_receiver_id": 0,
     *        "created_at_formatted": "2024-11-07 02:25:54",
     *        "sender": {
     *            "id": 37,
     *            "ecclesia_id": 4,
     *            "created_id": null,
     *            "user_name": "masum1",
     *            "first_name": "masum",
     *            "middle_name": null,
     *            "last_name": "ali",
     *            "email": "masum@excellisit.net",
     *            "phone": "+91 96470 38098",
     *            "email_verified_at": "2024-10-28T08:35:17.000000Z",
     *            "profile_picture": "profile_picture/sLWWnksqS6PHYMdZeBQ4OK3SnbVA0oMc9oykPbCn.webp",
     *            "address": "kolkata",
     *            "city": "kolkata",
     *            "state": "41",
     *            "address2": "kolkata",
     *            "country": "101",
     *            "zip": "700001",
     *            "status": 1,
     *            "created_at": "2024-10-28T08:35:17.000000Z",
     *            "updated_at": "2024-11-06T07:42:16.000000Z"
     *        },
     *        "reciver": {
     *            "id": 12,
     *            "ecclesia_id": 2,
     *            "created_id": "1",
     *            "user_name": "swarnadwip_nath",
     *            "first_name": "Swarnadwip",
     *            "middle_name": null,
     *            "last_name": "Nath",
     *            "email": "swarnadwip@excellisit.net",
     *            "phone": "+1 0741202022",
     *            "email_verified_at": null,
     *            "profile_picture": "profile_picture/yCvplMhdpjc0kIeKG63tfkZwhKNYbcF1ZhfQdDFO.jpg",
     *            "address": "Kokata",
     *            "city": "Kolkata",
     *            "state": "41",
     *            "address2": null,
     *            "country": "101",
     *            "zip": "700001",
     *            "status": 1,
     *            "created_at": "2024-06-21T11:31:27.000000Z",
     *            "updated_at": "2024-09-09T11:02:59.000000Z"
     *        }
     *    },
     *    "chat_count": 14,
     *    "success": true
     * }
     * @response 201 {
     *   "msg": "An error occurred while sending the message.",
     *   "success": false
     * }
     */
    public function send(Request $request)
    {
        try {
            // Count chat
            $chat_count = Chat::where(function ($query) use ($request) {
                $query->where('sender_id', auth()->id())
                    ->where('reciver_id', $request->reciver_id);
            })->orWhere(function ($query) use ($request) {
                $query->where('sender_id', $request->reciver_id)
                    ->where('reciver_id', auth()->id());
            })->count();

            $input_message = Helper::formatChatSendMessage($request->message);

            $themessage = $input_message;
            if (!empty($themessage)) {
                $themessage = $request->message;
            } else {
                $themessage = ' ';
            }

            if ($request->file) {
                $file = $this->imageUpload($request->file('file'), 'chat');
                $chatData = Chat::create([
                    'sender_id' => auth()->id(),
                    'reciver_id' => $request->reciver_id,
                    'message' => $themessage,
                    'attachment' => $file
                ]);
            } else {
                $chatData = Chat::create([
                    'sender_id' => auth()->id(),
                    'reciver_id' => $request->reciver_id,
                    'message' => $themessage,
                    'attachment' => ''
                ]);
            }

            // Get chat data with sender and receiver
            $chat = Chat::with('sender', 'reciver')->find($chatData->id);
            $chat->created_at_formatted = $chat->created_at->setTimezone('America/New_York')->format('Y-m-d H:i:s');

            return response()->json([
                'msg' => 'Message sent successfully',
                'chat' => $chat,
                'chat_count' => $chat_count,
                'success' => true
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage(), 'success' => false], 201);
        }
    }



    /**
     * Clear Chat Messages
     *
     * Clears chat messages between the authenticated user and a specified receiver by marking messages as deleted from each side.
     * @authenticated
     * @bodyParam reciver_id int required The ID of the user to clear chat with. Example: 2
     *
     * @response {
     *    "msg": "Chat cleared successfully",
     *    "success": true
     * }
     */
    public function clear(Request $request)
    {
        try {
            // $sender_id = auth()->id();
            // $reciver_id = $request->reciver_id;

            // // Mark messages as deleted from the sender's side
            // Chat::where('sender_id', $sender_id)
            //     ->where('reciver_id', $reciver_id)
            //     ->update(['delete_from_sender_id' => 1]);

            // // Mark messages as deleted from the receiver's side
            // Chat::where('reciver_id', $sender_id)
            //     ->where('sender_id', $reciver_id)
            //     ->update(['delete_from_receiver_id' => 1]);
            $sender_id = auth()->id();
            $reciver_id = $request->reciver_id;
            $authUserId = auth()->id(); // Get the authenticated user's ID

            // If the authenticated user is the sender
            if ($authUserId == $sender_id) {
                // Mark all messages from sender side as deleted for the authenticated user
                Chat::where('sender_id', $sender_id)
                    ->where('reciver_id', $reciver_id)
                    ->update(['delete_from_sender_id' => 1]);

                // Mark all messages from receiver side as deleted for the authenticated user
                Chat::where('sender_id', $reciver_id)
                    ->where('reciver_id', $sender_id)
                    ->update(['delete_from_receiver_id' => 1]);
            }

            // If the authenticated user is the receiver
            if ($authUserId == $reciver_id) {
                // Mark all messages from sender side as deleted for the authenticated user
                Chat::where('sender_id', $sender_id)
                    ->where('reciver_id', $reciver_id)
                    ->update(['delete_from_receiver_id' => 1]);

                // Mark all messages from receiver side as deleted for the authenticated user
                Chat::where('sender_id', $reciver_id)
                    ->where('reciver_id', $sender_id)
                    ->update(['delete_from_sender_id' => 1]);
            }

            return response()->json(['msg' => 'Chat cleared successfully', 'success' => true], 200);
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage(), 'success' => false], 201);
        }
    }


    /**
     * Mark Chat as Seen
     *
     * Marks a specific chat message as seen by updating its `seen` status.
     * @authenticated
     * @bodyParam chat_id int required The ID of the chat message to mark as seen. Example: 10
     * @bodyParam reciver_id int required The ID of the receiver user. Example: 2
     *
     * @response {
     *    "msg": "Chat seen successfully",
     *    "status": true,
     *    "last_chat": {
     *        "id": 10,
     *        "sender_id": 1,
     *        "reciver_id": 2,
     *        "message": "Hello!",
     *        "seen": 1,
     *        "created_at": "2024-11-07T14:52:36.000000Z",
     *        "updated_at": "2024-11-07T15:05:12.000000Z"
     *    }
     * }
     */
    public function seen(Request $request)
    {
        try {
            $sender_id = auth()->id();
            $reciver_id = $request->reciver_id;

            Chat::where('id', $request->chat_id)
                // ->where('sender_id', $reciver_id)
                //  ->where('reciver_id', $sender_id)
                ->update(['seen' => 1]);

            $last_chat = Chat::findOrFail($request->chat_id);

            return response()->json(['msg' => 'Chat seen successfully', 'status' => true, 'last_chat' => $last_chat], 200);
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage(), 'success' => false], 201);
        }
    }

    /**
     * Remove Chat Message
     *
     * Removes a specific chat message for the authenticated user. The message can be removed for "everyone" or marked as deleted for the sender only.
     * @authenticated
     * @bodyParam chat_id int required The ID of the chat message to be removed. Example: 10
     * @bodyParam del_from string required Specifies if the message should be deleted for "everyone" or only for the sender ("me"). Example: "me"
     *
     * @response {
     *    "msg": "Chat removed successfully",
     *    "status": true,
     *    "chat": {
     *        "id": 10,
     *        "sender_id": 1,
     *        "reciver_id": 2,
     *        "message": "Hello!",
     *        "created_at": "2024-11-07T14:52:36.000000Z",
     *        "updated_at": "2024-11-07T15:05:12.000000Z"
     *    }
     * }
     */
    public function remove(Request $request)
    {
        try {
            $chat_id = $request->chat_id;
            $del_from = $request->del_from;

            $chat = Chat::where('id', $chat_id)->first();

            if ($del_from === 'everyone') {
                Chat::where('id', $chat_id)->delete();
            } else {
                // Mark message as deleted for the sender if not removing for everyone
                Chat::where('id', $chat_id)
                    ->where('sender_id', auth()->id())
                    ->update(['deleted_for_sender' => 1]);
            }

            return response()->json(['msg' => 'Chat removed successfully', 'status' => true, 'chat' => $chat], 200);
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage(), 'success' => false], 201);
        }
    }


    /**
     * Manage Chat Notifications
     *
     * Sends a notification to the receiver when they receive a new chat message. If `is_delete` is set, it marks the specified notification as deleted.
     * @authenticated
     * @bodyParam chat_id int required The ID of the chat message related to the notification. Example: 15
     * @bodyParam user_id int required The ID of the receiver of the chat message. Example: 5
     * @bodyParam is_delete int optional Set to 1 to mark the notification as deleted. Example: 1
     *
     * @response {
     *    "msg": "Notification deleted successfully",
     *    "status": true
     * }
     * @response 200 {
     *    "msg": "Notification already sent",
     *    "status": true,
     *    "notification_count": 3,
     *    "notification" : {
     *        "id": 45,
     *        "user_id": 3,
     *        "chat_id": 15,
     *        "message": "You have a <b>new message</b> from John Doe",
     *        "type": "Chat",
     *        "created_at": "2024-11-07T14:35:29.000000Z",
     *        "updated_at": "2024-11-07T14:35:29.000000Z"
     *    }
     * }
     * @response 200 {
     *    "msg": "Notification sent successfully",
     *    "status": true,
     *    "notification_count": 4,
     *    "notification": {
     *        "id": 46,
     *        "user_id": 3,
     *        "chat_id": 15,
     *        "message": "You have a <b>new message</b> from John Doe",
     *        "type": "Chat",
     *        "created_at": "2024-11-07T14:35:29.000000Z",
     *        "updated_at": "2024-11-07T14:35:29.000000Z"
     *    }
     * }
     */
    public function notification(Request $request)
    {
        try {
            $user_id = $request->user_id;  // Receiver's ID
            $sender_id = auth()->id();
            $chat_id = $request->chat_id;
            $sender = User::find($sender_id);

            // Check if `is_delete` is set in the request
            if (isset($request->is_delete)) {
                Notification::where('user_id', $user_id)
                    ->where('chat_id', $chat_id)
                    ->update(['is_delete' => 1]);
                return response()->json(['msg' => 'Notification deleted successfully', 'status' => true], 200);
            }

            // Check if a notification for the specific chat is already sent to the user
            $count = Notification::where('user_id', $user_id)
                ->where('is_read', 0)
                ->where('chat_id', $chat_id)
                ->where('type', 'Chat')
                ->count();

            if ($count > 0) {
                $notification = Notification::where('user_id', $user_id)
                    ->where('is_read', 0)
                    ->where('chat_id', $chat_id)
                    ->where('type', 'Chat')
                    ->first();
                $notification_count = Notification::where('user_id', $user_id)
                    ->where('is_read', 0)
                    ->where('is_delete', 0)
                    ->count();
                return response()->json([
                    'msg' => 'Notification already sent',
                    'status' => true,
                    'notification_count' => $notification_count,
                    'notification' => $notification
                ]);
            } else {
                // Send a new notification if it hasn't been sent yet
                $notification = new Notification();
                $notification->user_id = $user_id;  // Receiver's ID
                $notification->chat_id = $chat_id;
                $notification->message = 'You have a <b>new message</b> from ' . $sender->full_name;
                $notification->type = 'Chat';
                $notification->save();

                $notification_count = Notification::where('user_id', $user_id)
                    ->where('is_read', 0)
                    ->where('is_delete', 0)
                    ->count();
                return response()->json([
                    'msg' => 'Notification sent successfully',
                    'status' => true,
                    'notification_count' => $notification_count,
                    'notification' => $notification
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage(), 'success' => false], 201);
        }
    }







    //
}
