<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class ChatController extends Controller
{
    public function chats()
    {
        if (auth()->user()->can('View Chat')) {
            $users = User::with('chatSender')->role('CUSTOMER')->where('id', '!=', auth()->id())->where('status', 1)->get()->toArray();
            // return user orderBy latest message
            $users = array_map(function ($user) {
                $user['last_message'] = Chat::where(function ($query) use ($user) {
                    $query->where('sender_id', $user['id'])->where('reciver_id', auth()->id());
                })->orWhere(function ($query) use ($user) {
                    $query->where('sender_id', auth()->id())->where('reciver_id', $user['id']);
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
            return view('user.chat.list')->with(compact('users'));
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
                    ->where('reciver_id', $request->reciver_id);
            })
                ->orWhere(function ($query) use ($request) {
                    $query->where('sender_id', $request->reciver_id)
                        ->where('reciver_id', $request->sender_id);
                })
                ->orderBy('created_at', 'asc') // Assuming you want to order chats by timestamp
                ->get();

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

            return response()->json(['message' => 'Show Chat', 'status' => true, 'chat_count' => $chat_count, 'view' => (string)View::make('user.chat.chat_body')->with(compact('chats', 'is_chat', 'reciver'))]);
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

            $chatData = Chat::create([
                'sender_id' => $request->sender_id,
                'reciver_id' => $request->reciver_id,
                'message' => $request->message
            ]);
            // get chat data with sender and reciver
            $chat = Chat::with('sender', 'reciver')->find($chatData->id);
            $users = User::with('chatSender')->role('CUSTOMER')->where('id', '!=', auth()->id())->where('status', 1)->get()->toArray();
            // return user orderBy latest message
            $users = array_map(function ($user) {
                $user['last_message'] = Chat::where(function ($query) use ($user) {
                    $query->where('sender_id', $user['id'])->where('reciver_id', auth()->id());
                })->orWhere(function ($query) use ($user) {
                    $query->where('sender_id', auth()->id())->where('reciver_id', $user['id']);
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

            $reciver_id = $request->reciver_id; // Corrected the variable name to match the request
            $receiver_users = User::with('chatSender') // Assuming 'chatSender' is the relationship to the Chat model
                ->role('CUSTOMER')
                ->where('id', '!=', $reciver_id)
                ->where('status', 1)
                ->get()
                ->toArray();

            $receiver_users = array_map(function ($user) use ($reciver_id) {
                $user['last_message'] = Chat::where(function ($query) use ($user, $reciver_id) {
                    $query->where('sender_id', $user['id'])->where('reciver_id', $reciver_id); // Corrected 'receiver_id' variable
                })->orWhere(function ($query) use ($user, $reciver_id) {
                    $query->where('sender_id', $reciver_id)->where('reciver_id', $user['id']); // Corrected 'receiver_id' variable
                })->orderBy('created_at', 'desc')->first();

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
}
