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
use App\Helpers\Helper;
use App\Services\FCMService;
use Illuminate\Support\Facades\Log;
use Exception;

class ChatController extends Controller
{
    use ImageTrait;

    protected $fcmService;

    public function __construct(FCMService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    public function chats()
    {
        if (auth()->user()->can('Manage Chat')) {
            $user_type = auth()->user()->user_type;
            $country_name = auth()->user()->country;
            $usersQuery = User::with('roles', 'chatSender')
                ->where('id', '!=', auth()->id())
                ->where('status', 1)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('type', [1, 2, 3]);
                });


            $isSuperAdmin = auth()->user()->hasNewRole('SUPER ADMIN');

            if (!$isSuperAdmin) {
                if ($user_type == 'Global') {
                    $usersQuery->where(function ($query) {
                        $query->where('user_type', 'Global')
                            ->orWhereHas('chatSender', function ($q) {
                                $q->where('reciver_id', auth()->id());
                            });
                    });
                } else {
                    $usersQuery->where(function ($query) use ($country_name) {
                        // Same country users (Regional)
                        $query->where(function ($q) use ($country_name) {
                            $q->where('user_type', 'Regional')->where('country', $country_name);
                        })
                            // OR Global users who already messaged me
                            ->orWhere(function ($q) {
                                $q->where('user_type', 'Global')
                                    ->whereHas('chatSender', function ($chat) {
                                        $chat->where('reciver_id', auth()->id());
                                    });
                            });
                    });
                }

                // Exclude Global Super Admins from non-super-admin lists unless they have messaged
                $usersQuery->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where('user_type', '!=', 'Global')
                            ->orWhereDoesntHave('userRole', function ($q) {
                                $q->where('name', 'SUPER ADMIN');
                            });
                    })->orWhereHas('chatSender', function ($q) {
                        $q->where('reciver_id', auth()->id());
                    })->orWhereHas('chatReciver', function ($q) {
                        $q->where('sender_id', auth()->id());
                    });
                });
            }

            $users = $usersQuery->get()->toArray();

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
            $user_type = auth()->user()->user_type;
            $country_name = auth()->user()->country;

            $usersQuery = User::with('roles', 'chatSender')
                ->where('id', '!=', auth()->id())
                ->where('status', 1)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('type', [1, 2, 3]);
                });

            $isSuperAdmin = auth()->user()->hasNewRole('SUPER ADMIN');

            if (!$isSuperAdmin) {
                if ($user_type == 'Global') {
                    $usersQuery->where(function ($query) {
                        $query->where('user_type', 'Global')
                            ->orWhereHas('chatSender', function ($q) {
                                $q->where('reciver_id', auth()->id());
                            });
                    });
                } else {
                    $usersQuery->where(function ($query) use ($country_name) {
                        $query->where(function ($q) use ($country_name) {
                            $q->where('user_type', 'Regional')->where('country', $country_name);
                        })->orWhere(function ($q) {
                            $q->where('user_type', 'Global')
                                ->whereHas('chatSender', function ($chat) {
                                    $chat->where('reciver_id', auth()->id());
                                });
                        });
                    });
                }

                // Exclude Global Super Admins from non-super-admin lists unless they have messaged
                $usersQuery->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where('user_type', '!=', 'Global')
                            ->orWhereDoesntHave('userRole', function ($q) {
                                $q->where('name', 'SUPER ADMIN');
                            });
                    })->orWhereHas('chatSender', function ($q) {
                        $q->where('reciver_id', auth()->id());
                    })->orWhereHas('chatReciver', function ($q) {
                        $q->where('sender_id', auth()->id());
                    });
                });
            }

            $users = $usersQuery->get()->toArray();
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

            $input_message = Helper::formatChatSendMessage($request->message);

            $themessage = $input_message;
            if (!empty($themessage)) {
                $themessage = $input_message;
            } else {
                $themessage = ' ';
            }

            // Handle multiple files
            $files = $request->file('files') ?? ($request->file('file') ? [$request->file('file')] : []);
            $chats = [];

            if (!empty($files)) {
                // If multiple files, create separate chat messages for each
                foreach ($files as $index => $file) {
                    $uploadedFile = $this->imageUpload($file, 'chat');
                    $attachmentName = $file->getClientOriginalName();
                    $message_type = $this->detectMessageType($file);

                    // Only include message text with the first file
                    $messageForFile = ($index === 0) ? $themessage : ' ';

                    $chatData = Chat::create([
                        'sender_id' => $request->sender_id,
                        'reciver_id' => $request->reciver_id,
                        'message' => $messageForFile,
                        'attachment' => $uploadedFile,
                        'attachment_name' => $attachmentName,
                    ]);
                    $chats[] = $chatData;
                }
            } else {
                // No files, just message
                $chatData = Chat::create([
                    'sender_id' => $request->sender_id,
                    'reciver_id' => $request->reciver_id,
                    'message' => $themessage,
                    'attachment' => ''
                ]);
                $message_type = 'text';
                $chats[] = $chatData;
            }

            // Process notifications and FCM for all chat messages
            $allChats = [];
            foreach ($chats as $chatData) {
                $chat = Chat::with('sender', 'reciver')->find($chatData->id);
                $chat->created_at_formatted = $chat->created_at->format('h:i a') . ' Today';
                $allChats[] = $chat;

                // Create notification (one per message)
                $notification = new Notification();
                $notification->user_id = $request->reciver_id;
                $notification->chat_id = $chat->id;
                $notification->message = 'You have a <b>new message</b> from ' . auth()->user()->full_name;
                $notification->type = 'Chat';
                $notification->save();

                // Send FCM notification to receiver for each message
                $receiver = User::find($request->reciver_id);
                if ($receiver && $receiver->fcm_token) {
                    try {
                        $hasAttachment = !empty($chat->attachment);
                        $this->fcmService->sendToDevice(
                            $receiver->fcm_token,
                            'Message from ' . auth()->user()->full_name,
                            $hasAttachment ? 'Sent an attachment' : $themessage,
                            [
                                'type' => 'chat',
                                'chat_id' => (string) $chat->id,
                                'sender_id' => (string) auth()->id(),
                                'sender_name' => auth()->user()->full_name,
                                'message' => $chat->message,
                                'attachment' => $hasAttachment ? Storage::url($chat->attachment) : '',
                                'attachment_name' => $hasAttachment ? ($chat->attachment_name ?? basename($chat->attachment)) : null,
                                'msg_type' => $hasAttachment ? $this->detectMessageType($chat->attachment) : 'text',
                                'timestamp' => $chat->created_at_formatted
                            ]
                        );
                        Log::info('FCM chat notification sent successfully', [
                            'receiver_id' => $receiver->id,
                            'chat_id' => $chat->id,
                            'message' => $themessage
                        ]);
                    } catch (Exception $e) {
                        Log::error('FCM chat notification failed: ' . $e->getMessage());
                    }
                }
            }

            // ...existing code... (users array processing)
            // $users = User::with('roles', 'chatSender')->where('id', '!=', auth()->id())->where('status', 1)->whereHas('roles', function ($query) {
            //     $query->whereIn('type', [1, 2, 3]);
            // })->get()->toArray();
            // // return user orderBy latest message
            // $users = array_map(function ($user) {
            //     $user['last_message'] = Chat::where(function ($query) use ($user) {
            //         $query->where('sender_id', $user['id'])->where('reciver_id', auth()->id())->where('deleted_for_reciver', 0)->where('delete_from_receiver_id', 0);
            //     })->orWhere(function ($query) use ($user) {
            //         $query->where('sender_id', auth()->id())->where('reciver_id', $user['id'])->where('deleted_for_sender', 0)->where('delete_from_sender_id', 0);
            //     })->orderBy('created_at', 'desc')->first();

            //     if ($user['last_message']) {
            //         $user['last_message']->created_at = $user['last_message']->created_at->format('Y-m-d H:i:s'); // Format to string
            //     }

            //     return $user;
            // }, $users);

            // // Sort users based on the latest message
            // usort($users, function ($a, $b) {
            //     if ($a['last_message'] === null) {
            //         return 1; // Move users with no messages to the end
            //     }
            //     if ($b['last_message'] === null) {
            //         return -1; // Move users with no messages to the end
            //     }

            //     return $b['last_message']->created_at <=> $a['last_message']->created_at; // Sort by latest message timestamp
            // });

            // $reciver_id = $request->reciver_id; // Corrected the variable name to match the request
            // $receiver_users = User::
            //     //with('roles', 'chatSender') // Assuming 'chatSender' is the relationship to the Chat model

            //     where('id', $reciver_id)
            //     ->where('status', 1)
            //     ->whereHas('roles', function ($query) {
            //         $query->whereIn('type', [1, 2, 3]);
            //     })
            //     ->get()
            //     ->toArray();

            // $receiver_users = array_map(function ($user) use ($reciver_id) {
            //     $user['last_message'] = Chat::where(function ($query) use ($user, $reciver_id) {
            //         $query->where('sender_id', $user['id'])->where('reciver_id', $reciver_id)->where('deleted_for_reciver', 0)->where('delete_from_receiver_id', 0); // Corrected 'receiver_id' variable
            //     })->orWhere(function ($query) use ($user, $reciver_id) {
            //         $query->where('sender_id', $reciver_id)->where('reciver_id', $user['id'])->where('deleted_for_sender', 0)->where('delete_from_sender_id', 0); // Corrected 'receiver_id' variable
            //     })->orderBy('created_at', 'desc')->first();

            //     if ($user['last_message']) {
            //         $user['last_message']->created_at = $user['last_message']->created_at->format('Y-m-d H:i:s'); // Format to string
            //         $user['last_message']->time = $user['last_message']->created_at->format('h:i A'); // Format to string
            //     }
            //     // count unseen chat
            //     $user['unseen_chat'] = Chat::where('sender_id',  $user['id'])
            //         ->where('reciver_id', $reciver_id)
            //         ->where('delete_from_receiver_id', 0)
            //         ->where('seen', 0)
            //         ->count();

            //     return $user;
            // }, $receiver_users);

            // // Sort users based on the latest message
            // usort($receiver_users, function ($a, $b) {
            //     if ($a['last_message'] === null) {
            //         return 1; // Move users with no messages to the end
            //     }
            //     if ($b['last_message'] === null) {
            //         return -1; // Move users with no messages to the end
            //     }

            //     return $b['last_message']->created_at <=> $a['last_message']->created_at; // Sort by latest message timestamp
            // });

            $reciver_id = $request->reciver_id;

            // $receiver_user = User::where('id', $reciver_id)
            //     ->where('status', 1)
            //     ->whereHas('roles', function ($query) {
            //         $query->whereIn('type', [1, 2, 3]);
            //     })
            //     ->first();


            // // Fetch last message
            // $lastMessage = Chat::where(function ($query) use ($receiver_user, $reciver_id) {
            //     $query->where('sender_id', $receiver_user->id)
            //         ->where('reciver_id', $reciver_id)
            //         ->where('deleted_for_reciver', 0)
            //         ->where('delete_from_receiver_id', 0);
            // })
            //     ->orWhere(function ($query) use ($receiver_user, $reciver_id) {
            //         $query->where('sender_id', $reciver_id)
            //             ->where('reciver_id', $receiver_user->id)
            //             ->where('deleted_for_sender', 0)
            //             ->where('delete_from_sender_id', 0);
            //     })
            //     ->orderBy('created_at', 'desc')
            //     ->first();

            // if ($lastMessage) {
            //     $lastMessage->formatted_time = $lastMessage->created_at->format('h:i A');
            //     $lastMessage->formatted_date = $lastMessage->created_at->format('Y-m-d H:i:s');
            // }

            // // Count unseen messages
            // $unseenChat = Chat::where('sender_id', $receiver_user->id)
            //     ->where('reciver_id', $reciver_id)
            //     ->where('delete_from_receiver_id', 0)
            //     ->where('seen', 0)
            //     ->count();

            // // Combine and return
            // $receiver_user = $receiver_user->toArray();
            // $receiver_user['last_message'] = $lastMessage;
            // $receiver_user['unseen_chat'] = $unseenChat;

            // Return first chat as main response (for backward compatibility)
            $chat = $allChats[0];

            return response()->json([
                'msg' => 'Message sent successfully',
                'chat' => $chat,
                'all_chats' => $allChats,  // Send all created chats
                'chat_count' => $chat_count,
                'success' => true
            ]);
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage(), 'success' => false]);
        }
    }

    /**
     * Determine message type based on file extension.
     */
    private function detectMessageType($file): string
    {
        if (!$file) {
            return 'text';
        }

        // Handle both UploadedFile objects and file paths
        if (is_string($file)) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        } else {
            $extension = strtolower($file->getClientOriginalExtension());
        }

        $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'];
        $audio_extensions = ['mp3', 'wav', 'ogg', 'aac', 'm4a'];
        $video_extensions = ['mp4', 'mov', 'avi', 'mkv', 'webm'];
        $doc_extensions   = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv'];

        return match (true) {
            in_array($extension, $image_extensions) => 'image',
            in_array($extension, $audio_extensions) => 'audio',
            in_array($extension, $video_extensions) => 'video',
            in_array($extension, $doc_extensions)   => 'doc',
            default => 'text',
        };
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

                // Send FCM notification
                // $receiver = User::find($user_id);
                // if ($receiver && $receiver->fcm_token) {
                //     try {
                //         $this->fcmService->sendToDevice(
                //             $receiver->fcm_token,
                //             'New Message',
                //             'You have a new message from ' . $sender->full_name,
                //             [
                //                 'type' => 'chat_notification',
                //                 'chat_id' => (string) $chat_id,
                //                 'sender_id' => (string) $sender_id,
                //                 'sender_name' => $sender->full_name,
                //                 'notification_id' => (string) $notification->id
                //             ]
                //         );
                //     } catch (Exception $e) {
                //         Log::error('FCM chat notification failed: ' . $e->getMessage());
                //     }
                // }

                $notification_count = Notification::where('user_id', $user_id)->where('is_read', 0)->where('is_delete', 0)->count();
                return response()->json(['msg' => 'Notification sent successfully', 'status' => true, 'notification_count' => $notification_count, 'notification' => $notification]);
            }
        }

        return abort(404); // Optional: return a 404 response if not an AJAX request
    }
}
