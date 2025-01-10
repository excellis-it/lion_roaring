<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Notification;
use App\Models\User;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class ChatController extends Controller
{
    use ImageTrait;

    public function chats()
    {
        if (auth()->user()->can('Manage Chat')) {
            $users = User::with('roles', 'chatSender')->where('id', '!=', auth()->id())->where('status', 1)->whereHas('roles', function ($query) {
                $query->whereIn('type', [1, 2]);
            })->get()->toArray();
            // return user orderBy latest message
            $users = array_map(function ($user) {
                $user['last_message'] = Chat::where(function ($query) use ($user) {
                    $query->where('sender_id', $user['id'])->where('reciver_id', auth()->id())->where('deleted_for_reciver', 0)->where('delete_from_receiver_id', 0);
                })->orWhere(function ($query) use ($user) {
                    $query->where('sender_id', auth()->id())->where('reciver_id', $user['id'])->where('deleted_for_sender', 0)->where('delete_from_sender_id', 0);
                })->orderBy('created_at', 'desc')->first();
                return $user;
            }, $users);

            // Sort users based on the latest message
            usort($users, function ($a, $b) {
                if ($a['last_message'] === null) {
                    return 1; // Move users with no messages to the end
                }
                if ($b['last_message'] === null) {
                    return -1; // Move users with no messages to the end
                }

                return $b['last_message']->created_at <=> $a['last_message']->created_at; // Sort by latest message timestamp
            });

            // foreach ($users as $user) {
            //     $user['user_role'] = User::find($user['id'])->roles()->first();
            // }

            // return $users;

            return view('user.chat.list')->with(compact('users'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function chatsList()
    {
        if (auth()->user()->can('Manage Chat')) {
            $users = User::with('roles', 'chatSender')->where('id', '!=', auth()->id())->where('status', 1)->whereHas('roles', function ($query) {
                $query->whereIn('type', [1, 2]);
            })->get()->toArray();
            // return user orderBy latest message
            $users = array_map(function ($user) {
                $user['last_message'] = Chat::where(function ($query) use ($user) {
                    $query->where('sender_id', $user['id'])->where('reciver_id', auth()->id())->where('deleted_for_reciver', 0)->where('delete_from_receiver_id', 0);
                })->orWhere(function ($query) use ($user) {
                    $query->where('sender_id', auth()->id())->where('reciver_id', $user['id'])->where('deleted_for_sender', 0)->where('delete_from_sender_id', 0);
                })->orderBy('created_at', 'desc')->first();
                return $user;
            }, $users);

            // Sort users based on the latest message
            usort($users, function ($a, $b) {
                if ($a['last_message'] === null) {
                    return 1; // Move users with no messages to the end
                }
                if ($b['last_message'] === null) {
                    return -1; // Move users with no messages to the end
                }

                return $b['last_message']->created_at <=> $a['last_message']->created_at; // Sort by latest message timestamp
            });
            return view('user.chat.chat_list')->with(compact('users'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }


    public function load(Request $request)
    {
        try {
            $is_chat = true;
            $chats = Chat::where(function ($query) use ($request) {
                $query->where('sender_id', $request->sender_id)
                    ->where('reciver_id', $request->reciver_id)
                    ->where('deleted_for_sender', 0)
                    ->where('delete_from_sender_id', 0);
            })
                ->orWhere(function ($query) use ($request) {
                    $query->where('sender_id', $request->reciver_id)
                        ->where('reciver_id', $request->sender_id)
                        ->where('deleted_for_reciver', 0)
                        ->where('delete_from_receiver_id', 0);
                })
                ->orderBy('created_at', 'asc') // Assuming you want to order chats by timestamp
                ->get();

            $unseen_chat = Chat::where('sender_id', $request->reciver_id)
                ->where('reciver_id', $request->sender_id)
                ->where('seen', 0)
                ->where('delete_from_receiver_id', 0)
                ->get();
            // dd($unseen_chat);
            // seen chat
            $chats = $chats->map(function ($chat) {
                if ($chat->reciver_id == auth()->id()) {
                    $chat->update(['seen' => 1]);
                }
                return $chat;
            });
            // return $chats;
            $chat_count = count($chats);
            $reciver = User::find($request->reciver_id);

            return response()->json(['message' => 'Show Chat', 'status' => true, 'chat_count' => $chat_count, 'unseen_chat' => $unseen_chat, 'view' => (string)View::make('user.chat.chat_body')->with(compact('chats', 'is_chat', 'reciver'))]);
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage(), 'status' => false]);
        }
    }

    public function send(Request $request)
    {
        try {
            // count chat
            $chat_count = Chat::where(function ($query) use ($request) {
                $query->where('sender_id', $request->sender_id)->where('reciver_id', $request->reciver_id);
            })->orWhere(function ($query) use ($request) {
                $query->where('sender_id', $request->reciver_id)->where('reciver_id', $request->sender_id);
            })->count();
            $themessage = $request->message;
            if (!empty($themessage)) {
                $themessage = $request->message;
            } else {
                $themessage = ' ';
            }
            if ($request->file) {
                // check the file size
                // if ($request->file('file')->getSize() > 10240) {
                //     return response()->json(['msg' => 'File size is too large', 'success' => false]);
                // }
                $file = $this->imageUpload($request->file('file'), 'chat');
                $chatData = Chat::create([
                    'sender_id' => $request->sender_id,
                    'reciver_id' => $request->reciver_id,
                    'message' => $themessage,
                    'attachment' => $file
                ]);
            } else {
                $chatData = Chat::create([
                    'sender_id' => $request->sender_id,
                    'reciver_id' => $request->reciver_id,
                    'message' => $themessage,
                    'attachment' => ''
                ]);
            }

            // get chat data with sender and reciver
            $chat = Chat::with('sender', 'reciver')->find($chatData->id);
            $chat->created_at_formatted = $chat->created_at->setTimezone('America/New_York')->format('Y-m-d H:i:s');
            // return $chat;
            // dd($chat);
            $users = User::with('roles', 'chatSender')->where('id', '!=', auth()->id())->where('status', 1)->whereHas('roles', function ($query) {
                $query->whereIn('type', [1, 2]);
            })->get()->toArray();
            // return user orderBy latest message
            $users = array_map(function ($user) {
                $user['last_message'] = Chat::where(function ($query) use ($user) {
                    $query->where('sender_id', $user['id'])->where('reciver_id', auth()->id())->where('deleted_for_reciver', 0)->where('delete_from_receiver_id', 0);
                })->orWhere(function ($query) use ($user) {
                    $query->where('sender_id', auth()->id())->where('reciver_id', $user['id'])->where('deleted_for_sender', 0)->where('delete_from_sender_id', 0);
                })->orderBy('created_at', 'desc')->first();

                if ($user['last_message']) {
                    $user['last_message']->created_at = $user['last_message']->created_at->format('Y-m-d H:i:s'); // Format to string
                }

                return $user;
            }, $users);

            // Sort users based on the latest message
            usort($users, function ($a, $b) {
                if ($a['last_message'] === null) {
                    return 1; // Move users with no messages to the end
                }
                if ($b['last_message'] === null) {
                    return -1; // Move users with no messages to the end
                }

                return $b['last_message']->created_at <=> $a['last_message']->created_at; // Sort by latest message timestamp
            });

            $reciver_id = $request->reciver_id; // Corrected the variable name to match the request
            $receiver_users = User::with('roles', 'chatSender') // Assuming 'chatSender' is the relationship to the Chat model
                // ->role('MEMBER')
                ->where('id', '!=', $reciver_id)
                ->where('status', 1)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('type', [1, 2]);
                })
                ->get()
                ->toArray();

            $receiver_users = array_map(function ($user) use ($reciver_id) {
                $user['last_message'] = Chat::where(function ($query) use ($user, $reciver_id) {
                    $query->where('sender_id', $user['id'])->where('reciver_id', $reciver_id)->where('deleted_for_reciver', 0)->where('delete_from_receiver_id', 0); // Corrected 'receiver_id' variable
                })->orWhere(function ($query) use ($user, $reciver_id) {
                    $query->where('sender_id', $reciver_id)->where('reciver_id', $user['id'])->where('deleted_for_sender', 0)->where('delete_from_sender_id', 0); // Corrected 'receiver_id' variable
                })->orderBy('created_at', 'desc')->first();

                if ($user['last_message']) {
                    $user['last_message']->created_at = $user['last_message']->created_at->format('Y-m-d H:i:s'); // Format to string
                }
                // count unseen chat
                $user['unseen_chat'] = Chat::where('sender_id',  $user['id'])
                    ->where('reciver_id', $reciver_id)
                    ->where('delete_from_receiver_id', 0)
                    ->where('seen', 0)
                    ->count();

                return $user;
            }, $receiver_users);

            // Sort users based on the latest message
            usort($receiver_users, function ($a, $b) {
                if ($a['last_message'] === null) {
                    return 1; // Move users with no messages to the end
                }
                if ($b['last_message'] === null) {
                    return -1; // Move users with no messages to the end
                }

                return $b['last_message']->created_at <=> $a['last_message']->created_at; // Sort by latest message timestamp
            });


            return response()->json(['msg' => 'Message sent successfully', 'chat' => $chat, 'users' => $users, 'receiver_users' => $receiver_users, 'chat_count' => $chat_count, 'success' => true]);
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage(), 'success' => false]);
        }
    }

    // public function clear(Request $request)
    // {
    //     $sender_id = $request->sender_id;
    //     $reciver_id = $request->reciver_id;

    //     Chat::where('sender_id', $sender_id)
    //         ->update(['delete_from_sender_id' => 1]);

    //     Chat::where('reciver_id', $reciver_id)
    //         ->update(['delete_from_receiver_id' => 1]);

    //     return response()->json(['msg' => 'Chat cleared successfully', 'success' => true]);
    // }

    public function clear(Request $request)
    {
        $sender_id = $request->sender_id;
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

        return response()->json(['msg' => 'Chat cleared successfully for you', 'success' => true]);
    }




    public function seen(Request $request)
    {
        $sender_id = $request->sender_id;
        $reciver_id = $request->reciver_id;

        $chats = Chat::where('id', $request->chat_id)
            ->update(['seen' => 1]);

        $last_chat = Chat::findOrfail($request->chat_id);

        return response()->json(['msg' => 'Chat seen successfully', 'status' => true, 'last_chat' => $last_chat]);
    }


    public function remove(Request $request)
    {
        $chat_id = $request->chat_id;
        $del_from = $request->del_from;
        $chat = Chat::where('id', $chat_id)->first();
        if ($del_from == 'everyone') {
            Chat::where('id', $chat_id)->delete();
        } else {
            Chat::where('id', $chat_id)->update(['deleted_for_sender' => 1]);
        }

        return response()->json(['msg' => 'Chat removed successfully', 'status' => true, 'chat' => $chat]);
    }

    public function notification(Request $request)
    {
        if ($request->ajax()) {
            $user_id = $request->user_id;
            $sender_id = $request->sender_id;
            $chat_id = $request->chat_id;
            $sender = User::find($sender_id);
            // return $request->is_delete;
            if (isset($request->is_delete)) {
                Notification::where('user_id', $user_id)->where('chat_id', $chat_id)->update(['is_delete' => 1]);
                return response()->json(['msg' => 'Notification deleted successfully', 'status' => true]);
            }

            $count = Notification::where(function ($query) use ($request) {
                $query->where('user_id', $request->user_id)->where('is_read', 0)->where('chat_id', $request->chat_id)->where('type', 'Chat');
            })->count();
            if ($count > 0) {
                $notification = Notification::where('user_id', $request->user_id)->where('is_read', 0)->where('chat_id', $request->chat_id)->where('type', 'Chat')->first();
                $notification_count = Notification::where('user_id', $user_id)->where('is_read', 0)->where('is_delete', 0)->count();
                return response()->json(['msg' => 'Notification already sent', 'status' => true, 'notification_count' => $notification_count, 'notification' => $notification]);
            } else {
                $notification = new Notification();
                $notification->user_id =  $user_id;
                $notification->chat_id = $chat_id;
                $notification->message = 'You have a <b>new message</b> from ' . $sender->full_name;
                $notification->type = 'Chat';
                $notification->save();
                $notification_count = Notification::where('user_id', $user_id)->where('is_read', 0)->where('is_delete', 0)->count();
                return response()->json(['msg' => 'Notification sent successfully', 'status' => true, 'notification_count' => $notification_count, 'notification' => $notification]);
            }
        }

        return abort(404); // Optional: return a 404 response if not an AJAX request
    }
}
