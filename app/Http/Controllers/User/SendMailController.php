<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\SendMail as MailSendMail;
use App\Models\MailUser;
use App\Models\Notification;
use App\Models\SendMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\FCMService;
use Illuminate\Support\Facades\Log;
use Exception;

class SendMailController extends Controller
{
    protected $fcmService;

    public function __construct(FCMService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    public function list()
    {
        if (auth()->user()->can('Manage Email')) {

            $user = auth()->user();
            $user_type = $user->user_type;
            $country_name = $user->country;
            $isSuperAdmin = $user->hasNewRole('SUPER ADMIN');

            $allMailIdsQuery = User::with('roles')->where('status', true)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('type', [1, 2, 3]);
                })->where('id', '!=', $user->id);

            if (!$isSuperAdmin) {
                if ($user_type == 'Global') {
                    $allMailIdsQuery->where('user_type', 'Global');
                } else {
                    $allMailIdsQuery->where('user_type', 'Regional')->where('country', $country_name);
                }
            }

            $allMailIds = $allMailIdsQuery->get(['id', 'email']);

            return view('user.mail.list', ['allMailIds' => $allMailIds]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function sentList()
    {
        if (auth()->user()->can('Manage Email')) {

            $user = auth()->user();
            $user_type = $user->user_type;
            $country_name = $user->country;
            $isSuperAdmin = $user->hasNewRole('SUPER ADMIN');

            $allMailIdsQuery = User::with('roles')->where('status', true)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('type', [1, 2, 3]);
                })->where('id', '!=', $user->id);

            if (!$isSuperAdmin) {
                if ($user_type == 'Global') {
                    $allMailIdsQuery->where('user_type', 'Global');
                } else {
                    $allMailIdsQuery->where('user_type', 'Regional')->where('country', $country_name);
                }
            }

            $allMailIds = $allMailIdsQuery->get(['id', 'email']);

            return view('user.mail.sent', ['allMailIds' => $allMailIds]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function starList()
    {
        if (auth()->user()->can('Manage Email')) {

            $user = auth()->user();
            $user_type = $user->user_type;
            $country_name = $user->country;
            $isSuperAdmin = $user->hasNewRole('SUPER ADMIN');

            $allMailIdsQuery = User::with('roles')->where('status', true)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('type', [1, 2, 3]);
                })->where('id', '!=', $user->id);

            if (!$isSuperAdmin) {
                if ($user_type == 'Global') {
                    $allMailIdsQuery->where('user_type', 'Global');
                } else {
                    $allMailIdsQuery->where('user_type', 'Regional')->where('country', $country_name);
                }
            }

            $allMailIds = $allMailIdsQuery->get(['id', 'email']);

            return view('user.mail.star', ['allMailIds' => $allMailIds]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function trashList()
    {
        if (auth()->user()->can('Manage Email')) {

            $user = auth()->user();
            $user_type = $user->user_type;
            $country_name = $user->country;
            $isSuperAdmin = $user->hasNewRole('SUPER ADMIN');

            $allMailIdsQuery = User::with('roles')->where('status', true)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('type', [1, 2, 3]);
                })->where('id', '!=', $user->id);

            if (!$isSuperAdmin) {
                if ($user_type == 'Global') {
                    $allMailIdsQuery->where('user_type', 'Global');
                } else {
                    $allMailIdsQuery->where('user_type', 'Regional')->where('country', $country_name);
                }
            }

            $allMailIds = $allMailIdsQuery->get(['id', 'email']);

            return view('user.mail.trash', ['allMailIds' => $allMailIds]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }


    public function inboxEmailList(Request $request)
    {
        $type = $request->get('type');
        $subQuery = SendMail::whereHas('mailUsers', function ($q) {
            $q->where('user_id', auth()->id())
                ->where('is_from', '!=', 1)
                ->where('is_delete', 0);
        })
            ->selectRaw('COALESCE(reply_of, id) as mail_group, MAX(id) as max_id')
            ->groupBy('mail_group');

        $mails = SendMail::joinSub($subQuery, 'grouped_mails', function ($join) {
            $join->on('send_mails.id', '=', 'grouped_mails.max_id');
        })
            ->with([
                'userSender',
                'lastReply' => function ($query) {
                    $query->latest('created_at')->take(1);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $mails->each(function ($mail) {
            $mail->ownUserMailInfo = MailUser::where('send_mail_id', $mail->id)->where('user_id', auth()->id())->first();
            $emails = explode(',', $mail->to);
            // dd($mail->userSender);
            // Extract the last reply message and date
            $lastReply = $mail->lastReply->first(); // Use the defined relationship
            $mail->lastReplyMessage = $lastReply ? $lastReply->message : $mail->message;
            $mail->lastReplyDate = $lastReply ? $lastReply->created_at : $mail->created_at;
            // Fetch the name of the sendTo
            $names = User::whereIn('email', $emails)
                ->select('first_name', 'middle_name', 'last_name')
                ->get()
                ->map(function ($user) {
                    return trim("{$user->full_name}");
                })
                ->toArray();
            // Combine the full names into a comma-separated string
            $mail->userToNames = implode(', ', $names);
        });

        $mails->type = $type;

        //  return response()->json(['data' => view('user.mail.partials.inbox-email-list', compact('mails'))->render()]);
        return response()->json([
            'data' => view('user.mail.partials.main-email-list', compact('mails'))->render(),
            'total' => $mails->total(),
            'perPage' => $mails->perPage(),
            'currentPage' => $mails->currentPage(),
            'lastPage' => $mails->lastPage()
        ]);
    }

    public function sentEmailList(Request $request)
    {
        $type = $request->get('type');

        $subQuery = SendMail::whereHas('mailUsers', function ($q) {
            $q->where('user_id', auth()->id())
                ->where('is_from', 1)
                ->where('is_delete', 0);
        })
            ->selectRaw('COALESCE(reply_of, id) as mail_group, MAX(id) as max_id')
            ->groupBy('mail_group');

        $mails = SendMail::joinSub($subQuery, 'grouped_mails', function ($join) {
            $join->on('send_mails.id', '=', 'grouped_mails.max_id');
        })
            ->with([
                'userSender',
                'lastReply' => function ($query) {
                    $query->latest('created_at')->take(1);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $mails->each(function ($mail) {
            $mail->ownUserMailInfo = MailUser::where('send_mail_id', $mail->id)->where('user_id', auth()->id())->first();
            $emails = explode(',', $mail->to);
            // Extract the last reply message and date
            $lastReply = $mail->lastReply->first(); // Use the defined relationship
            $mail->lastReplyMessage = $lastReply ? $lastReply->message : $mail->message;
            $mail->lastReplyDate = $lastReply ? $lastReply->created_at : $mail->created_at;
            // Fetch the name of the sendTo
            $names = User::whereIn('email', $emails)
                ->select('first_name', 'middle_name', 'last_name')
                ->get()
                ->map(function ($user) {
                    return trim("{$user->full_name}");
                })
                ->toArray();
            // Combine the full names into a comma-separated string
            $mail->userToNames = implode(', ', $names);
        });
        $mails->type = $type;

        // return $mails;


        return response()->json([
            'data' => view('user.mail.partials.main-email-list', compact('mails'))->render(),
            'total' => $mails->total(),
            'perPage' => $mails->perPage(),
            'currentPage' => $mails->currentPage(),
            'lastPage' => $mails->lastPage()
        ]);
    }

    public function starEmailList(Request $request)
    {
        $type = $request->get('type');
        $subQuery = SendMail::whereHas('mailUsers', function ($q) {
            $q->where('user_id', auth()->id())
                ->where('is_starred', 1)
                ->where('is_delete', 0);
        })
            ->selectRaw('COALESCE(reply_of, id) as mail_group, MAX(id) as max_id')
            ->groupBy('mail_group');

        $mails = SendMail::joinSub($subQuery, 'grouped_mails', function ($join) {
            $join->on('send_mails.id', '=', 'grouped_mails.max_id');
        })
            ->with([
                'userSender',
                'lastReply' => function ($query) {
                    $query->latest('created_at')->take(1);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $mails->each(function ($mail) {
            $mail->ownUserMailInfo = MailUser::where('send_mail_id', $mail->id)->where('user_id', auth()->id())->first();
            $emails = explode(',', $mail->to);

            // Extract the last reply message and date
            $lastReply = $mail->lastReply->first(); // Use the defined relationship
            $mail->lastReplyMessage = $lastReply ? $lastReply->message : $mail->message;
            $mail->lastReplyDate = $lastReply ? $lastReply->created_at : $mail->created_at;

            // Fetch the name of the sendTo
            $names = User::whereIn('email', $emails)
                ->select('first_name', 'middle_name', 'last_name')
                ->get()
                ->map(function ($user) {
                    return trim("{$user->full_name}");
                })
                ->toArray();
            // Combine the full names into a comma-separated string
            $mail->userToNames = implode(', ', $names);
        });

        $mails->type = $type;

        return response()->json([
            'data' => view('user.mail.partials.main-email-list', compact('mails'))->render(),
            'total' => $mails->total(),
            'perPage' => $mails->perPage(),
            'currentPage' => $mails->currentPage(),
            'lastPage' => $mails->lastPage()
        ]);
    }

    public function trashEmailList(Request $request)
    {
        $type = $request->get('type');
        // $mails = SendMail::whereHas('mailUsers', function ($q) {
        //     $q->where('user_id', auth()->id())->where('is_delete', 1);
        // })
        //     ->orderBy('created_at', 'desc')
        //     ->paginate(15);
        $subQuery = SendMail::whereHas('mailUsers', function ($q) {
            $q->where('user_id', auth()->id())
                ->where('is_delete', 1);
        })
            ->selectRaw('COALESCE(reply_of, id) as mail_group, MAX(id) as max_id')
            ->groupBy('mail_group');

        $mails = SendMail::joinSub($subQuery, 'grouped_mails', function ($join) {
            $join->on('send_mails.id', '=', 'grouped_mails.max_id');
        })
            ->with([
                'userSender',
                'lastReply' => function ($query) {
                    $query->latest('created_at')->take(1);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $mails->each(function ($mail) {
            $mail->ownUserMailInfo = MailUser::where('send_mail_id', $mail->id)->where('user_id', auth()->id())->first(); // Add the MailUser instance to each mail
            $emails = explode(',', $mail->to);
            // Extract the last reply message and date
            $lastReply = $mail->lastReply->first(); // Use the defined relationship
            $mail->lastReplyMessage = $lastReply ? $lastReply->message : $mail->message;
            $mail->lastReplyDate = $lastReply ? $lastReply->created_at : $mail->created_at;
            // Fetch the name of the sendTo
            $names = User::whereIn('email', $emails)
                ->select('first_name', 'middle_name', 'last_name')
                ->get()
                ->map(function ($user) {
                    return trim("{$user->full_name}");
                })
                ->toArray();
            // Combine the full names into a comma-separated string
            $mail->userToNames = implode(', ', $names);
        });

        $mails->type = $type;


        return response()->json([
            'data' => view('user.mail.partials.main-email-list', compact('mails'))->render(),
            'total' => $mails->total(),
            'perPage' => $mails->perPage(),
            'currentPage' => $mails->currentPage(),
            'lastPage' => $mails->lastPage()
        ]);
    }


    // public function view($id)
    // {

    //     $id = base64_decode($id);

    //     $init_mail = SendMail::findOrFail($id);

    //     if (!empty($init_mail->reply_of)) {
    //         $fetch_mailId = $init_mail->reply_of;
    //     } else {
    //         $fetch_mailId = $id;
    //     }

    //     $init_ownUserMailInfo = MailUser::where('send_mail_id', $id)->where('user_id', auth()->id())->first();

    //     if ($init_ownUserMailInfo) {
    //         $init_ownUserMailInfo->is_read = 1;
    //         $init_ownUserMailInfo->save();
    //     }

    //     $mail_details = SendMail::with('user')->findOrFail($fetch_mailId);

    //     $ownUserMailInfo = MailUser::where('send_mail_id', $fetch_mailId)->where('user_id', auth()->id())->first();

    //     $replyMailIds = [];

    //     $reply_mails = SendMail::with('user')->where('reply_of', $fetch_mailId)->orderBy('created_at', 'asc')->get();
    //     $reply_mails->each(function ($reply) {
    //         $reply->ownUserMailInfo = MailUser::where('send_mail_id', $reply->id)->where('user_id', auth()->id())->first();
    //     });

    //     //return auth()->user()->email;

    //     $allMailIds = User::where('status', true)->where('id', '!=', auth()->id())->get(['id', 'email']);
    //     // $replyMailids = $replyMailids = collect([$mail_details->user->email])->merge($reply_mails->pluck('user.email'));
    //     $replyMailids = collect([$mail_details->user->email])
    //         ->merge($reply_mails->pluck('user.email'))
    //         ->reject(function ($email) {
    //             return $email === auth()->user()->email;
    //         });
    //     // dd($replyMailids);
    //     return view('user.mail.mail-details')->with(compact('mail_details', 'ownUserMailInfo', 'reply_mails', 'allMailIds', 'replyMailids'));
    // }


    public function view($id)
    {
        $id = base64_decode($id);

        // Fetch the initial mail (the main mail or the first mail in the thread)
        $init_mail = SendMail::findOrFail($id);

        // Determine the main mail's ID (if it's a reply, use the parent mail's ID)
        if (!empty($init_mail->reply_of)) {
            $fetch_mailId = $init_mail->reply_of;
        } else {
            $fetch_mailId = $id;
        }

        // Get the user's info for this mail
        $init_ownUserMailInfo = MailUser::where('send_mail_id', $id)->where('user_id', auth()->id())->first();

        // Mark the mail as read if it's the user's mail
        if ($init_ownUserMailInfo) {
            $init_ownUserMailInfo->is_read = 1;
            $init_ownUserMailInfo->save();
        }

        // Fetch the main mail details
        $mail_details = SendMail::with('user')->findOrFail($fetch_mailId);

        // Get the user's info for the main mail
        $ownUserMailInfo = MailUser::where('send_mail_id', $fetch_mailId)->where('user_id', auth()->id())->first();

        // Fetch all replies for the main mail, excluding deleted ones in MailUser table
        $reply_mails = SendMail::with('user')
            ->where('reply_of', $fetch_mailId)
            ->whereHas('mailUsers', function ($q) {
                $q->where('user_id', auth()->id())->where('is_delete', 0); // Only non-deleted replies
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Add the user's mail info for each reply
        $reply_mails->each(function ($reply) {
            $reply->ownUserMailInfo = MailUser::where('send_mail_id', $reply->id)
                ->where('user_id', auth()->id())
                ->first();
        });

        // Fetch all mail IDs for other users (to avoid sending mails to yourself)
        $user = auth()->user();
        $user_type = $user->user_type;
        $country_name = $user->country;
        $isSuperAdmin = $user->hasNewRole('SUPER ADMIN');

        $allMailIdsQuery = User::with('roles')->where('status', true)
            ->whereHas('roles', function ($query) {
                $query->whereIn('type', [1, 2, 3]);
            })->where('id', '!=', $user->id);

        if (!$isSuperAdmin) {
            if ($user_type == 'Global') {
                $allMailIdsQuery->where('user_type', 'Global');
            } else {
                $allMailIdsQuery->where('user_type', 'Regional')->where('country', $country_name);
            }
        }

        $allMailIds = $allMailIdsQuery->get(['id', 'email']);

        // return $allMailIds;

        // Collect all email addresses involved in this mail thread, excluding the current user's email
        $replyMailids = collect([$mail_details->user->email])
            ->merge($reply_mails->pluck('user.email'))
            ->reject(function ($email) {
                return $email === auth()->user()->email;
            });

        // Return the view with the relevant data
        return view('user.mail.mail-details')->with(compact('mail_details', 'ownUserMailInfo', 'reply_mails', 'allMailIds', 'replyMailids'));
    }




    public function fetchData(Request $request)
    {
        if ($request->ajax()) {
            $sort_by = $request->get('sortby', 'created_at'); // Default sorting by created_at if not specified
            $sort_type = $request->get('sorttype', 'asc'); // Default sorting type ascending if not specified
            $query = $request->get('query', '');
            $query = str_replace(" ", "%", $query);

            $mails = SendMail::where('form_id', auth()->id())
                ->where(function ($q) use ($query) {
                    $q->where('to', 'like', '%' . $query . '%')
                        ->orWhere('cc', 'like', '%' . $query . '%')
                        ->orWhere('subject', 'like', '%' . $query . '%');
                })
                ->orderBy($sort_by, $sort_type)
                ->paginate(15);

            return response()->json(['data' => view('user.mail.table', compact('mails'))->render()]);
        }
    }

    public function compose()
    {
        if (auth()->user()->can('Manage Email')) {
            $user = auth()->user();
            $user_type = $user->user_type;
            $country_name = $user->country;
            $isSuperAdmin = $user->hasNewRole('SUPER ADMIN');

            $usersQuery = User::with('roles')->where('status', true)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('type', [1, 2, 3]);
                })->where('id', '!=', $user->id);

            if (!$isSuperAdmin) {
                if ($user_type == 'Global') {
                    $usersQuery->where('user_type', 'Global');
                } else {
                    $usersQuery->where('user_type', 'Regional')->where('country', $country_name);
                }
            }

            $users = $usersQuery->get(['id', 'email']); // Adjust fields as needed
            return view('user.mail.compose')->with(compact('users'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function sendMail(Request $request)
    {
        $request->validate([
            'to' => 'required',
            'subject' => 'required',
            'message' => 'required',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:512000'
        ]);

        // Decode the JSON strings for 'to' and 'cc' fields
        $toEmails = json_decode($request->to, true);
        // Extract email addresses from the decoded arrays
        $to = array_column($toEmails, 'value');
        if ($request->cc) {
            $ccEmails = json_decode($request->cc, true);
            $cc = array_column($ccEmails, 'value');
        } else {
            $cc = [];
        }

        // Validate TO and CC emails, and check for duplicates
        $invalidEmails = [];
        foreach (array_merge($to, $cc) as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $invalidEmails[] = $email; // Collect invalid emails
            }
        }

        if (!empty($invalidEmails)) {
            return response()->json(['message' => 'Invalid email(s): ' . implode(', ', $invalidEmails), 'status' => false]);
        }

        // Remove duplicates from CC that are also in TO
        $cc = array_diff($cc, $to); // Remove duplicates from CC

        // Create and save the mail
        $mail = new SendMail();
        $mail->form_id = auth()->id();
        $mail->to = implode(',', $to);  // Convert to a comma-separated string
        $mail->cc = empty($cc) ? null : implode(',', $cc);  // Convert to a comma-separated string
        $mail->subject = $request->subject;
        $mail->message = $request->message;

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            $attachments = [];

            foreach ($request->file('attachments') as $file) {
                $filePath = $file->store('email_files', 'public');
                $attachments[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'encrypted_name' => $filePath,
                ];
            }
            $mail->attachment = json_encode($attachments);
        }

        $mail->save();

        // Save users associated with CC
        $notification_message = 'You have a <b>new mail</b> from ' . auth()->user()->email;
        $cc_id = [];
        if ($cc) {
            foreach ($cc as $email) {
                $user = User::where('email', $email)->first();
                if ($user) {
                    $cc_id[] =  $user->id;
                    $mail_user = new MailUser();
                    $mail_user->user_id = $user->id;
                    $mail_user->send_mail_id = $mail->id;
                    $mail_user->is_cc = 1;
                    $mail_user->save();

                    if ($user->id != auth()->id()) {
                        $notification = new Notification();
                        $notification->user_id =  $user->id;
                        $notification->message = $notification_message;
                        $notification->type = 'Mail';
                        $notification->save();

                        // Send FCM notification
                        if ($user->fcm_token) {
                            try {
                                $this->fcmService->sendToDevice(
                                    $user->fcm_token,
                                    'New Email from ' . auth()->user()->full_name,
                                    'Subject: ' . $request->subject,
                                    [
                                        'type' => 'mail',
                                        'email_id' => (string) $mail->id,
                                        'sender_name' => auth()->user()->full_name,
                                        'subject' => $request->subject,
                                        'sender_email' => auth()->user()->email,
                                        'notification_id' => (string) $notification->id
                                    ]
                                );
                            } catch (Exception $e) {
                                Log::error('FCM email notification failed: ' . $e->getMessage());
                            }
                        }
                    }
                }
            }
        }

        // Save users associated with TO
        $to_id = [];
        foreach ($to as $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $to_id[] =  $user->id;
                $mail_user = new MailUser();
                $mail_user->user_id = $user->id;
                $mail_user->send_mail_id = $mail->id;
                $mail_user->is_to = 1;
                $mail_user->save();

                if ($user->id != auth()->id()) {
                    $notification = new Notification();
                    $notification->user_id =  $user->id;
                    $notification->message = $notification_message;
                    $notification->type = 'Mail';
                    $notification->save();

                    // Send FCM notification
                    if ($user->fcm_token) {
                        try {
                            $this->fcmService->sendToDevice(
                                $user->fcm_token,
                                'New Email from ' . auth()->user()->full_name,
                                'Subject: ' . $request->subject,
                                [
                                    'type' => 'mail',
                                    'email_id' => (string) $mail->id,
                                    'sender_name' => auth()->user()->full_name,
                                    'subject' => $request->subject,
                                    'sender_email' => auth()->user()->email,
                                    'notification_id' => (string) $notification->id
                                ]
                            );
                        } catch (Exception $e) {
                            Log::error('FCM email notification failed: ' . $e->getMessage());
                        }
                    }
                }
            }
        }

        // Save users associated with FROM
        $mail_user = new MailUser();
        $mail_user->user_id = auth()->id();
        $mail_user->send_mail_id = $mail->id;
        $mail_user->is_from = 1;
        $mail_user->save();

        try {
            $sender_user = auth()->user();
            $senderEmail = $sender_user->email;
            $senderName = $sender_user->full_name;

            session()->flash('message', 'Your mail has been sent Successfully');

            return response()->json([
                'message' => 'Mail sent successfully.',
                'status' => true,
                'send_to_ids' => array_merge($cc_id, $to_id),
                'notification_message' => $notification_message
            ]);
        } catch (\Exception $e) {

            \Log::error('Mail sending failed: ' . $e->getMessage());


            return response()->json([
                'message' => 'Failed to send mail. Please try again later.',
                'status' => false,
                'error' => $e->getMessage() // Optionally include the error message
            ], 500);
        }
    }


    public function sendMailReply(Request $request)
    {
        $request->validate([
            'to' => 'required',
            'subject' => 'required',
            'message' => 'required',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:512000'
        ]);

        // Decode the JSON strings for 'to' and 'cc' fields
        $toEmails = json_decode($request->to, true);

        // return $toEmails;
        // Extract email addresses from the decoded arrays
        $to = array_column($toEmails, 'value');
        if ($request->cc) {
            $ccEmails = json_decode($request->cc, true);
            $cc = array_column($ccEmails, 'value');
        } else {
            $cc = [];
        }

        // Validate TO and CC emails, and check for duplicates
        $invalidEmails = [];
        foreach (array_merge($to, $cc) as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $invalidEmails[] = $email; // Collect invalid emails
            }
        }

        if (!empty($invalidEmails)) {
            return response()->json(['message' => 'Invalid email(s): ' . implode(', ', $invalidEmails), 'status' => false]);
        }

        // Remove duplicates from CC that are also in TO
        $cc = array_diff($cc, $to); // Remove duplicates from CC

        // Create and save the mail
        $mail = new SendMail();
        $mail->reply_of = $request->main_mail_id;
        $mail->form_id = auth()->id();
        $mail->to = implode(',', $to);  // Convert to a comma-separated string
        $mail->cc = empty($cc) ? null : implode(',', $cc);  // Convert to a comma-separated string
        $mail->subject = $request->subject;
        $mail->message = $request->message;


        // Handle file uploads
        if ($request->hasFile('attachments')) {
            $attachments = [];

            foreach ($request->file('attachments') as $file) {

                $filePath = $file->store('email_files', 'public');


                $attachments[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'encrypted_name' => $filePath,
                ];
            }


            $mail->attachment = json_encode($attachments);
        }

        $mail->save();


        // Save users associated with CC
        $notification_message = 'You have a <b>new mail</b> from ' . auth()->user()->email;
        $cc_id = [];
        if ($cc) {
            foreach ($cc as $email) {
                $user = User::where('email', $email)->first();
                if ($user) {
                    $cc_id[] =  $user->id;
                    $mail_user = new MailUser();
                    $mail_user->user_id = $user->id;
                    $mail_user->send_mail_id = $mail->id;
                    $mail_user->is_cc = 1;
                    $mail_user->save();


                    if ($user->id != auth()->id()) {
                        $notification = new Notification();
                        $notification->user_id =  $user->id;
                        $notification->message = $notification_message;
                        $notification->type = 'Mail';
                        $notification->save();


                        // Send FCM notification
                        if ($user->fcm_token) {
                            try {
                                $this->fcmService->sendToDevice(
                                    $user->fcm_token,
                                    'Email Reply from ' . auth()->user()->full_name,
                                    'Re: ' . $request->subject,
                                    [
                                        'type' => 'mail',
                                        'email_id' => (string) $mail->id,
                                        'main_email_id' => (string) $request->main_mail_id,
                                        'sender_name' => auth()->user()->full_name,
                                        'subject' => $request->subject,
                                        'sender_email' => auth()->user()->email,
                                        'notification_id' => (string) $notification->id
                                    ]
                                );
                            } catch (Exception $e) {
                                Log::error('FCM email reply notification failed: ' . $e->getMessage());
                            }
                        }
                    }
                }
            }
        }

        // Save users associated with TO
        $to_id = [];
        foreach ($to as $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $to_id[] =  $user->id;
                $mail_user = new MailUser();
                $mail_user->user_id = $user->id;
                $mail_user->send_mail_id = $mail->id;
                $mail_user->is_to = 1;
                $mail_user->save();

                if ($user->id != auth()->id()) {
                    $notification = new Notification();
                    $notification->user_id =  $user->id;
                    $notification->message = $notification_message;
                    $notification->type = 'Mail';
                    $notification->save();


                    // Send FCM notification
                    if ($user->fcm_token) {
                        try {
                            $this->fcmService->sendToDevice(
                                $user->fcm_token,
                                'Email Reply from ' . auth()->user()->full_name,
                                'Re: ' . $request->subject,
                                [
                                    'type' => 'mail',
                                    'email_id' => (string) $mail->id,
                                    'main_email_id' => (string) $request->main_mail_id,
                                    'sender_name' => auth()->user()->full_name,
                                    'subject' => $request->subject,
                                    'sender_email' => auth()->user()->email,
                                    'notification_id' => (string) $notification->id
                                ]
                            );
                        } catch (Exception $e) {
                            Log::error('FCM email reply notification failed: ' . $e->getMessage());
                        }
                    }
                }
            }
        }

        // Save users associated with FROM
        $mail_user = new MailUser();
        $mail_user->user_id = auth()->id();
        $mail_user->send_mail_id = $mail->id;
        $mail_user->is_from = 1;
        $mail_user->save();

        try {
            $sender_user = auth()->user();
            $senderEmail = $sender_user->email;
            $senderName = $sender_user->full_name;

            session()->flash('message', 'Your mail has been sent Successfully');

            return response()->json([
                'message' => 'Mail sent successfully.',
                'status' => true,
                'send_to_ids' => array_merge($cc_id, $to_id),
                'notification_message' => $notification_message
            ]);
        } catch (\Exception $e) {

            \Log::error('Mail sending failed: ' . $e->getMessage());


            return response()->json([
                'message' => 'Failed to send mail. Please try again later.',
                'status' => false,
                'error' => $e->getMessage() // Optionally include the error message
            ], 500);
        }
    }

    public function sendMailForward(Request $request)
    {
        $request->validate([
            'to' => 'required',
            'subject' => 'required',
            'message' => 'required',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:512000'
        ]);

        // Decode the JSON strings for 'to' and 'cc' fields
        $toEmails = json_decode($request->to, true);
        // Extract email addresses from the decoded arrays
        $to = array_column($toEmails, 'value');
        if ($request->cc) {
            $ccEmails = json_decode($request->cc, true);
            $cc = array_column($ccEmails, 'value');
        } else {
            $cc = [];
        }

        // Validate TO and CC emails, and check for duplicates
        $invalidEmails = [];
        foreach (array_merge($to, $cc) as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $invalidEmails[] = $email; // Collect invalid emails
            }
        }

        if (!empty($invalidEmails)) {
            return response()->json(['message' => 'Invalid email(s): ' . implode(', ', $invalidEmails), 'status' => false]);
        }

        // Remove duplicates from CC that are also in TO
        $cc = array_diff($cc, $to); // Remove duplicates from CC

        // Create and save the mail
        $mail = new SendMail();
        $mail->form_id = auth()->id();
        $mail->to = implode(',', $to);  // Convert to a comma-separated string
        $mail->cc = empty($cc) ? null : implode(',', $cc);  // Convert to a comma-separated string
        $mail->subject = $request->subject;
        $mail->message = $request->message;


        // Handle file uploads
        if ($request->hasFile('attachments')) {
            $attachments = [];

            foreach ($request->file('attachments') as $file) {

                $filePath = $file->store('email_files', 'public');


                $attachments[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'encrypted_name' => $filePath,
                ];
            }


            $mail->attachment = json_encode($attachments);
        }

        $mail->save();


        // Save users associated with CC
        $notification_message = 'You have a <b>new mail</b> from ' . auth()->user()->email;
        $cc_id = [];
        if ($cc) {
            foreach ($cc as $email) {
                $user = User::where('email', $email)->first();
                if ($user) {
                    $cc_id[] =  $user->id;
                    $mail_user = new MailUser();
                    $mail_user->user_id = $user->id;
                    $mail_user->send_mail_id = $mail->id;
                    $mail_user->is_cc = 1;
                    $mail_user->save();


                    if ($user->id != auth()->id()) {
                        $notification = new Notification();
                        $notification->user_id =  $user->id;
                        $notification->message = $notification_message;
                        $notification->type = 'Mail';
                        $notification->save();


                        // Send FCM notification
                        if ($user->fcm_token) {
                            try {
                                $this->fcmService->sendToDevice(
                                    $user->fcm_token,
                                    'Forwarded Email from ' . auth()->user()->full_name,
                                    'Fwd: ' . $request->subject,
                                    [
                                        'type' => 'mail',
                                        'email_id' => (string) $mail->id,
                                        'sender_name' => auth()->user()->full_name,
                                        'subject' => $request->subject,
                                        'sender_email' => auth()->user()->email,
                                        'notification_id' => (string) $notification->id
                                    ]
                                );
                            } catch (Exception $e) {
                                Log::error('FCM email forward notification failed: ' . $e->getMessage());
                            }
                        }
                    }
                }
            }
        }

        // Save users associated with TO
        $to_id = [];
        foreach ($to as $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $to_id[] =  $user->id;
                $mail_user = new MailUser();
                $mail_user->user_id = $user->id;
                $mail_user->send_mail_id = $mail->id;
                $mail_user->is_to = 1;
                $mail_user->save();

                if ($user->id != auth()->id()) {
                    $notification = new Notification();
                    $notification->user_id =  $user->id;
                    $notification->message = $notification_message;
                    $notification->type = 'Mail';
                    $notification->save();


                    // Send FCM notification
                    if ($user->fcm_token) {
                        try {
                            $this->fcmService->sendToDevice(
                                $user->fcm_token,
                                'Forwarded Email from ' . auth()->user()->full_name,
                                'Fwd: ' . $request->subject,
                                [
                                    'type' => 'mail',
                                    'email_id' => (string) $mail->id,
                                    'sender_name' => auth()->user()->full_name,
                                    'subject' => $request->subject,
                                    'sender_email' => auth()->user()->email,
                                    'notification_id' => (string) $notification->id
                                ]
                            );
                        } catch (Exception $e) {
                            Log::error('FCM email forward notification failed: ' . $e->getMessage());
                        }
                    }
                }
            }
        }

        // Save users associated with FROM
        $mail_user = new MailUser();
        $mail_user->user_id = auth()->id();
        $mail_user->send_mail_id = $mail->id;
        $mail_user->is_from = 1;
        $mail_user->save();

        try {
            $sender_user = auth()->user();
            $senderEmail = $sender_user->email;
            $senderName = $sender_user->full_name;

            session()->flash('message', 'Your mail has been sent Successfully');

            return response()->json([
                'message' => 'Mail sent successfully.',
                'status' => true,
                'send_to_ids' => array_merge($cc_id, $to_id),
                'notification_message' => $notification_message
            ]);
        } catch (\Exception $e) {

            \Log::error('Mail sending failed: ' . $e->getMessage());


            return response()->json([
                'message' => 'Failed to send mail. Please try again later.',
                'status' => false,
                'error' => $e->getMessage() // Optionally include the error message
            ], 500);
        }
    }


    // public function view($id)
    // {
    //     $id = base64_decode($id);
    //     $mail = MailUser::where('send_mail_id', $id)->where('user_id', auth()->id())->first();
    //     if ($mail) {
    //         $mail->is_read = 1;
    //         $mail->save();
    //     } else {
    //         abort(403, 'You do not have permission to access this page.');
    //     }
    //     $mail_details = SendMail::with('user')->findOrFail($id);
    //     // dd($mail_details);
    //     return view('user.mail.mail-details')->with(compact('mail_details'));
    // }

    // public function view($id)
    // {
    //     $id = base64_decode($id);
    //     $mail = MailUser::where('send_mail_id', $id)->where('user_id', auth()->id())->first();

    //     if ($mail) {
    //         $mail->is_read = 1;
    //         $mail->save();
    //     } else {
    //         abort(403, 'You do not have permission to access this page.');
    //     }

    //     // Fetch the main mail details
    //     $mail_details = SendMail::with('user')->findOrFail($id);

    //     // Fetch replies of the main mail
    //     $replies = SendMail::with('user')
    //         ->where('reply_of', $mail_details->id) // Assuming reply_of is the id of the main mail
    //         ->orderBy('created_at', 'asc') // Order replies by creation date
    //         ->get();

    //     // return($id);
    //     // dd($replies);

    //     return view('user.mail.mail-details')->with(compact('mail_details', 'replies'));
    // }






    // delete
    // public function delete(Request $request)
    // {
    //     if (auth()->user()->can('Manage Email')) {
    //         $mailIds = $request->mailIds;
    //         foreach ($mailIds as $mailId) {
    //             // $mail = MailUser::where('send_mail_id', $mailId)->where('user_id', auth()->id())->first();
    //             // if ($mail) {
    //             //     $mail->is_delete = 1;
    //             //     $mail->deleted_at = now();
    //             //     $mail->save();
    //             // }
    //             $mailUsers = MailUser::where('send_mail_id', $mailId)
    //                 ->where('user_id', auth()->id())
    //                 ->get();
    //             //  dd($mailUsers);

    //             foreach ($mailUsers as $mail) {
    //                 $mail->is_delete = 1;
    //                 $mail->deleted_at = now();
    //                 $mail->save();
    //             }
    //         }
    //         return response()->json(['message' => 'Mail deleted successfully.', 'status' => true]);
    //     } else {
    //         abort(403, 'You do not have permission to access this page.');
    //     }
    // }
    public function delete(Request $request)
    {
        if (auth()->user()->can('Manage Email')) {
            $mailIds = $request->mailIds;

            foreach ($mailIds as $mailId) {
                // Find the main mail group (either the main mail or the reply's group)
                $mainMailId = SendMail::where('id', $mailId)
                    ->orWhere('reply_of', $mailId)
                    ->value('reply_of') ?? $mailId;

                // Get all mail IDs (main and replies) in this group
                $allMailIds = SendMail::where('id', $mainMailId)
                    ->orWhere('reply_of', $mainMailId)
                    ->pluck('id');

                // Update MailUser records for the authenticated user
                MailUser::whereIn('send_mail_id', $allMailIds)
                    ->where('user_id', auth()->id())
                    ->update([
                        'is_delete' => 1,
                        'deleted_at' => now(),
                    ]);
            }

            return response()->json(['message' => 'Mail deleted successfully.', 'status' => true]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }


    // public function deleteSingleMail(Request $request)
    // {
    //     if (auth()->user()->can('Manage Email')) {
    //         $mailid = $request->mail_id;

    //         $mail = MailUser::where('send_mail_id', $mailid)->where('user_id', auth()->id())->first();
    //         if ($mail) {
    //             $mail->is_delete = 1;
    //             $mail->deleted_at = now();
    //             $mail->save();
    //         }

    //         return response()->json(['message' => 'Mail deleted successfully.', 'status' => true]);
    //     } else {
    //         abort(403, 'You do not have permission to access this page.');
    //     }
    // }

    public function deleteSingleMail(Request $request)
    {
        if (auth()->user()->can('Manage Email')) {
            $mailId = $request->mail_id;

            // Find the main mail group (either the main mail or the reply's group)
            $mainMailId = SendMail::where('id', $mailId)
                ->orWhere('reply_of', $mailId)
                ->value('reply_of') ?? $mailId;

            // Get all mail IDs (main and replies) in this group
            $allMailIds = SendMail::where('id', $mainMailId)
                ->orWhere('reply_of', $mainMailId)
                ->pluck('id');

            // Update MailUser records for the authenticated user
            MailUser::whereIn('send_mail_id', $allMailIds)
                ->where('user_id', auth()->id())
                ->update([
                    'is_delete' => 1,
                    'deleted_at' => now(),
                ]);

            return response()->json(['message' => 'Mail deleted successfully.', 'status' => true]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function restoreSingleMail(Request $request)
    {
        if (auth()->user()->can('Manage Email')) {
            $mailid = $request->mail_id;

            $mail = MailUser::where('send_mail_id', $mailid)->where('user_id', auth()->id())->first();
            if ($mail) {
                $mail->is_delete = 0;
                $mail->save();
            }

            return response()->json(['message' => 'Mail restored successfully.', 'status' => true]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }


    // public function trashEmpty(Request $request)
    // {
    //     if (auth()->user()->can('Manage Email')) {
    //         $mailIds = $request->mailIds;
    //         foreach ($mailIds as $mailId) {
    //             // $mail = MailUser::where('send_mail_id', $mailId)->where('user_id', auth()->id())->first();
    //             // if ($mail) {
    //             //     $mail->is_delete = 1;
    //             //     $mail->deleted_at = now();
    //             //     $mail->save();
    //             // }
    //             $mailUsers = MailUser::where('send_mail_id', $mailId)
    //                 ->where('user_id', auth()->id())
    //                 ->get();
    //             //  dd($mailUsers);

    //             foreach ($mailUsers as $mail) {
    //                 $mail->is_delete = 2;
    //                 $mail->deleted_at = now();
    //                 $mail->save();
    //             }
    //         }

    //         return response()->json(['message' => 'Mail deleted successfully.', 'status' => true]);
    //     } else {
    //         abort(403, 'You do not have permission to access this page.');
    //     }
    // }

    public function trashEmpty(Request $request)
    {
        if (auth()->user()->can('Manage Email')) {
            // Ensure all mails marked as "trashed" by the authenticated user are emptied
            $trashedMails = SendMail::whereHas('mailUsers', function ($query) {
                $query->where('user_id', auth()->id())
                    ->where('is_delete', 1); // Only include mails in the trash
            })->get();

            // Iterate over each trashed mail to handle main and reply mails
            foreach ($trashedMails as $mail) {
                // Identify the main mail group
                $mainMailId = $mail->reply_of ?? $mail->id;

                // Get all mails (main and replies) in this group
                $allMailIds = SendMail::where('id', $mainMailId)
                    ->orWhere('reply_of', $mainMailId)
                    ->pluck('id');

                // Mark all mails in this group as permanently deleted for the authenticated user
                MailUser::whereIn('send_mail_id', $allMailIds)
                    ->where('user_id', auth()->id())
                    ->update([
                        'is_delete' => 2, // Permanently deleted
                        'deleted_at' => now(),
                    ]);
            }

            return response()->json(['message' => 'All trash mails emptied successfully.', 'status' => true]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }




    // delete sent mail
    // public function deleteSentsMail(Request $request)
    // {
    //     if (auth()->user()->can('Manage Email')) {
    //         $mailIds = $request->mailIds;
    //         foreach ($mailIds as $mailId) {
    //             // $mail = MailUser::where('send_mail_id', $mailId)->where('user_id', auth()->id())->first();
    //             // if ($mail) {
    //             //     $mail->is_delete = 1;
    //             //     $mail->deleted_at = now();
    //             //     $mail->save();
    //             // }
    //             $mailUsers = MailUser::where('send_mail_id', $mailId)
    //                 ->where('user_id', auth()->id())
    //                 ->get();

    //             foreach ($mailUsers as $mail) {
    //                 $mail->is_delete = 1;
    //                 $mail->deleted_at = now();
    //                 $mail->save();
    //             }
    //         }
    //         return response()->json(['message' => 'Mail deleted successfully.', 'status' => true]);
    //     } else {
    //         abort(403, 'You do not have permission to access this page.');
    //     }
    // }

    public function deleteSentsMail(Request $request)
    {
        if (auth()->user()->can('Manage Email')) {
            $mailIds = $request->mailIds;

            foreach ($mailIds as $mailId) {
                // Find the main mail group (either the main mail or the reply's group)
                $mainMailId = SendMail::where('id', $mailId)
                    ->orWhere('reply_of', $mailId)
                    ->value('reply_of') ?? $mailId;

                // Get all mail IDs (main and replies) in this group
                $allMailIds = SendMail::where('id', $mainMailId)
                    ->orWhere('reply_of', $mainMailId)
                    ->pluck('id');

                // Update MailUser records for the authenticated user
                MailUser::whereIn('send_mail_id', $allMailIds)
                    ->where('user_id', auth()->id())
                    ->update([
                        'is_delete' => 1,
                        'deleted_at' => now(),
                    ]);
            }

            return response()->json(['message' => 'Mail deleted successfully.', 'status' => true]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }



    // public function restore(Request $request)
    // {
    //     if (auth()->user()->can('Manage Email')) {
    //         $mailIds = $request->mailIds;
    //         foreach ($mailIds as $mailId) {
    //             $mail = MailUser::where('send_mail_id', $mailId)->where('user_id', auth()->id())->first();
    //             if ($mail) {
    //                 $mail->is_delete = 0;
    //                 $mail->save();
    //             }
    //         }
    //         return response()->json(['message' => 'Mail restored successfully.', 'status' => true]);
    //     } else {
    //         abort(403, 'You do not have permission to access this page.');
    //     }
    // }

    public function restore(Request $request)
    {
        if (auth()->user()->can('Manage Email')) {
            $mailIds = $request->mailIds;

            foreach ($mailIds as $mailId) {
                // Find the main mail group (either the main mail or the reply's group)
                $mainMailId = SendMail::where('id', $mailId)
                    ->orWhere('reply_of', $mailId)
                    ->value('reply_of') ?? $mailId;

                // Get all mail IDs (main and replies) in this group
                $allMailIds = SendMail::where('id', $mainMailId)
                    ->orWhere('reply_of', $mainMailId)
                    ->pluck('id');

                // Restore MailUser records for the authenticated user
                MailUser::whereIn('send_mail_id', $allMailIds)
                    ->where('user_id', auth()->id())
                    ->update([
                        'is_delete' => 0,
                        'deleted_at' => null,
                    ]);
            }

            return response()->json(['message' => 'Mail restored successfully.', 'status' => true]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }



    // star
    // public function star(Request $request)
    // {
    //     if (auth()->user()->can('Manage Email')) {

    //         $mail_id = $request->mail_id;
    //         $start_value = $request->start_value;
    //         $msg = '';

    //         $mail = MailUser::where('send_mail_id', $mail_id)->where('user_id', auth()->id())->first();
    //         if ($mail) {
    //             $mail->is_starred = $start_value;
    //             $mail->save();
    //         }

    //         if ($start_value == 1) {
    //             $msg = "Mail Starred Success!";
    //         } else {
    //             $msg = "Mail Star Mark Removed!";
    //         }

    //         return response()->json(['message' => $msg, 'status' => true]);
    //     } else {
    //         abort(403, 'You do not have permission to access this page.');
    //     }
    // }

    // public function star(Request $request)
    // {
    //     if (auth()->user()->can('Manage Email')) {

    //         $mail_id = $request->mail_id;
    //         $start_value = $request->start_value;
    //         $msg = '';

    //         // Find the main mail group (either the main mail or the reply's group)
    //         $mainMailId = SendMail::where('id', $mail_id)
    //             ->orWhere('reply_of', $mail_id)
    //             ->value('reply_of') ?? $mail_id;

    //         // Get all mail IDs (main and replies) in this group
    //         $allMailIds = SendMail::where('id', $mainMailId)
    //             ->orWhere('reply_of', $mainMailId)
    //             ->pluck('id');

    //         // Update the "starred" status for the authenticated user for both main and reply mails
    //         $mailUsers = MailUser::whereIn('send_mail_id', $allMailIds)
    //             ->where('user_id', auth()->id())
    //             ->update(['is_starred' => $start_value]);

    //         if ($start_value == 1) {
    //             $msg = "Mail Starred Success!";
    //         } else {
    //             $msg = "Mail Star Mark Removed!";
    //         }

    //         return response()->json(['message' => $msg, 'status' => true]);
    //     } else {
    //         abort(403, 'You do not have permission to access this page.');
    //     }
    // }

    public function star(Request $request)
    {
        if (auth()->user()->can('Manage Email')) {
            $mail_id = $request->mail_id;
            $star_value = $request->start_value; // Corrected variable name for clarity
            $msg = '';

            // Identify the main mail (reply_of or id)
            $mainMailId = SendMail::where('id', $mail_id)
                ->orWhere('reply_of', $mail_id)
                ->value('reply_of') ?? $mail_id;

            // Update the starred status for the main mail only
            $mailUser = MailUser::where('send_mail_id', $mainMailId)
                ->where('user_id', auth()->id())
                ->first();

            if ($mailUser) {
                $mailUser->is_starred = $star_value;
                $mailUser->save();

                $msg = $star_value == 1
                    ? "Mail Starred Successfully!"
                    : "Mail Star Mark Removed!";
            } else {
                $msg = "Mail not found or you do not have permission.";
            }

            return response()->json(['message' => $msg, 'status' => true]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }



    public function printMail($id)
    {


        $mail_details = SendMail::with('user')->findOrFail($id);

        $ownUserMailInfo = MailUser::where('send_mail_id', $id)->where('user_id', auth()->id())->first();

        $replyMailIds = [];

        $reply_mails = SendMail::with('user')->where('reply_of', $id)->orderBy('created_at', 'asc')->get();
        $reply_mails->each(function ($reply) {
            $reply->ownUserMailInfo = MailUser::where('send_mail_id', $reply->id)->where('user_id', auth()->id())->first();
        });

        // Pass the mail details to the print view
        return view('user.mail.mail-print', compact('mail_details', 'ownUserMailInfo', 'reply_mails'));
    }
}
