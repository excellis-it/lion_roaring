<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\SendMail as MailSendMail;
use App\Models\MailUser;
use App\Models\Notification;
use App\Models\SendMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * @group Email
 */

class EmailController extends Controller
{

    /**
     * Inbox Email List
     *
     * Retrieves a paginated list of emails for the authenticated user, excluding sent emails, and returns the data in JSON format.
     * @authenticated
     *
     * @response 200 {
     *    "data": [
     *        {
     *       "id": 85,
     *       "reply_of": 84,
     *       "form_id": 38,
     *       "to": "test@mail.net",
     *       "cc": null,
     *       "subject": "Test Email",
     *       "message": "<p>ddddd</p>",
     *       "attachment": null,
     *       "is_draft": 0,
     *       "is_delete": 0,
     *       "deleted_at": null,
     *       "created_at": "2024-11-06T14:07:53.000000Z",
     *       "updated_at": "2024-11-06T14:07:53.000000Z",
     *       "ownUserMailInfo": {
     *              "is_read": 0,
     *              "is_starred": 0,
     *              "is_delete": 0
     *          }
     *        },
     *        // additional emails
     *    ],
     *    "total": 45,
     *    "perPage": 15,
     *    "currentPage": 1,
     *    "lastPage": 3
     * }
     */
    public function inboxEmailList(Request $request)
    {
        // Retrieve paginated list of emails where the authenticated user is not the sender and has not deleted the email
        $mails = SendMail::whereHas('mailUsers', function ($q) {
            $q->where('user_id', auth()->id())
                ->where('is_from', '!=', 1)
                ->where('is_delete', 0);
        })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Add user-specific mail info to each mail item
        $mails->each(function ($mail) {
            $mail->ownUserMailInfo = MailUser::where('send_mail_id', $mail->id)
                ->where('user_id', auth()->id())
                ->first(['is_read', 'is_starred', 'is_delete']);
        });

        // Return the paginated data in JSON format
        return response()->json([
            'data' => $mails->items(), // Only email data with additional user-specific info
            'total' => $mails->total(),
            'perPage' => $mails->perPage(),
            'currentPage' => $mails->currentPage(),
            'lastPage' => $mails->lastPage()
        ]);
    }


    /**
     * Sent Email List
     *
     * Retrieves a paginated list of sent emails for the authenticated user, excluding deleted emails, and returns the data in JSON format.
     * @authenticated
     *
     * @response 200 {
     *    "data": [
     *        {
     *       "id": 85,
     *       "reply_of": 84,
     *       "form_id": 38,
     *       "to": "test@mail.net",
     *       "cc": null,
     *       "subject": "Test Email",
     *       "message": "<p>ddddd</p>",
     *       "attachment": null,
     *       "is_draft": 0,
     *       "is_delete": 0,
     *       "deleted_at": null,
     *       "created_at": "2024-11-06T14:07:53.000000Z",
     *       "updated_at": "2024-11-06T14:07:53.000000Z",
     *       "ownUserMailInfo": {
     *              "is_read": 1,
     *              "is_starred": 0,
     *              "is_delete": 0
     *          }
     *        },
     *        // additional emails
     *    ],
     *    "total": 30,
     *    "perPage": 15,
     *    "currentPage": 1,
     *    "lastPage": 2
     * }
     */
    public function sentEmailList(Request $request)
    {
        // Retrieve paginated list of sent emails where the authenticated user is the sender and has not deleted the email
        $mails = SendMail::whereHas('mailUsers', function ($q) {
            $q->where('user_id', auth()->id())
                ->where('is_from', 1)
                ->where('is_delete', 0);
        })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Add user-specific mail info to each mail item
        $mails->each(function ($mail) {
            $mail->ownUserMailInfo = MailUser::where('send_mail_id', $mail->id)
                ->where('user_id', auth()->id())
                ->first(['is_read', 'is_starred', 'is_delete']);
        });

        // Return the paginated data in JSON format
        return response()->json([
            'data' => $mails->items(), // Only email data with additional user-specific info
            'total' => $mails->total(),
            'perPage' => $mails->perPage(),
            'currentPage' => $mails->currentPage(),
            'lastPage' => $mails->lastPage()
        ]);
    }



    /**
     * Starred Email List
     *
     * Retrieves a paginated list of starred emails for the authenticated user, excluding deleted emails, and returns the data in JSON format.
     * @authenticated
     *
     * @response 200 {
     *    "data": [
     *        {
     *       "id": 85,
     *       "reply_of": 84,
     *       "form_id": 38,
     *       "to": "test@mail.net",
     *       "cc": null,
     *       "subject": "Test Email",
     *       "message": "<p>ddddd</p>",
     *       "attachment": null,
     *       "is_draft": 0,
     *       "is_delete": 0,
     *       "deleted_at": null,
     *       "created_at": "2024-11-06T14:07:53.000000Z",
     *       "updated_at": "2024-11-06T14:07:53.000000Z",
     *       "ownUserMailInfo": {
     *              "is_read": 1,
     *              "is_starred": 1,
     *              "is_delete": 0
     *          }
     *        },
     *        // additional emails
     *    ],
     *    "total": 20,
     *    "perPage": 15,
     *    "currentPage": 1,
     *    "lastPage": 2
     * }
     */
    public function starEmailList(Request $request)
    {
        // Retrieve paginated list of starred emails for the authenticated user, excluding deleted emails
        $mails = SendMail::whereHas('mailUsers', function ($q) {
            $q->where('user_id', auth()->id())
                ->where('is_starred', 1)
                ->where('is_delete', 0);
        })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Add user-specific mail info to each mail item
        $mails->each(function ($mail) {
            $mail->ownUserMailInfo = MailUser::where('send_mail_id', $mail->id)
                ->where('user_id', auth()->id())
                ->first(['is_read', 'is_starred', 'is_delete']);
        });

        // Return the paginated data in JSON format
        return response()->json([
            'data' => $mails->items(), // Only email data with additional user-specific info
            'total' => $mails->total(),
            'perPage' => $mails->perPage(),
            'currentPage' => $mails->currentPage(),
            'lastPage' => $mails->lastPage()
        ]);
    }


    /**
     * Trash Email List
     *
     * Retrieves a paginated list of emails in the trash for the authenticated user and returns the data in JSON format.
     * @authenticated
     *
     * @response 200 {
     *    "data": [
     *        {
     *       "id": 85,
     *       "reply_of": 84,
     *       "form_id": 38,
     *       "to": "test@mail.net",
     *       "cc": null,
     *       "subject": "Test Email",
     *       "message": "<p>ddddd</p>",
     *       "attachment": null,
     *       "is_draft": 0,
     *       "is_delete": 0,
     *       "deleted_at": null,
     *       "created_at": "2024-11-06T14:07:53.000000Z",
     *       "updated_at": "2024-11-06T14:07:53.000000Z",
     *       "ownUserMailInfo": {
     *              "is_read": 1,
     *              "is_starred": 0,
     *              "is_delete": 1
     *          }
     *        },
     *        // additional trashed emails
     *    ],
     *    "total": 5,
     *    "perPage": 15,
     *    "currentPage": 1,
     *    "lastPage": 1
     * }
     */
    public function trashEmailList(Request $request)
    {
        // Retrieve paginated list of trashed emails for the authenticated user
        $mails = SendMail::whereHas('mailUsers', function ($q) {
            $q->where('user_id', auth()->id())
                ->where('is_delete', 1);
        })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Add user-specific mail info to each mail item
        $mails->each(function ($mail) {
            $mail->ownUserMailInfo = MailUser::where('send_mail_id', $mail->id)
                ->where('user_id', auth()->id())
                ->first(['is_read', 'is_starred', 'is_delete']);
        });

        // Return the paginated data in JSON format
        return response()->json([
            'data' => $mails->items(), // Only email data with additional user-specific info
            'total' => $mails->total(),
            'perPage' => $mails->perPage(),
            'currentPage' => $mails->currentPage(),
            'lastPage' => $mails->lastPage()
        ]);
    }

    /**
     * View Email Details
     *
     * Retrieves the details of a specific email along with its replies and user-specific information for the authenticated user.
     * @authenticated
     *
     * @bodyParam mail_id int required The email ID to view. Example: 1
     *
     * @response 200 {
     *    "message": "Email details retrieved successfully.",
     *    "status": true,
     *    "data": {
     *        "mail_details": {
     *            "id": 72,
     *            "reply_of": null,
     *            "form_id": 37,
     *            "to": "swarnadwip@excellisit.net",
     *            "cc": null,
     *            "subject": "Test Email 10",
     *            "message": "<p>10 msg<\/p>",
     *            "attachment": "[{\"original_name\":\"p3.pdf\",\"encrypted_name\":\"email_files\\\/MG0hSy05bAv8Codx59IoDWjNxvid173ie4oWOgIh.pdf\"},{\"original_name\":\"image.png\",\"encrypted_name\":\"email_files\\\/O5ouE0bgXqG0TDXMhaz1loGLVwUXalYGprPn62K9.png\"},{\"original_name\":\"dummy - 2.pdf\",\"encrypted_name\":\"email_files\\\/l6PwXoc6aGTX80y1LoVb82QadlKnCEx9W4N25zlI.pdf\"}]",
     *            "is_draft": 0,
     *            "is_delete": 0,
     *            "deleted_at": null,
     *            "created_at": "2024-11-06T05:27:05.000000Z",
     *            "updated_at": "2024-11-06T05:27:05.000000Z",
     *            "user": {
     *                "id": 37,
     *                "ecclesia_id": 4,
     *                "created_id": null,
     *                "user_name": "masum1",
     *                "first_name": "Test",
     *                "middle_name": null,
     *                "last_name": "User",
     *                "email": "masum@excellisit.net",
     *                "phone": "+91 9123456789",
     *                "email_verified_at": "2024-10-28T08:35:17.000000Z",
     *                "profile_picture": "profile_picture\/sLWWnksqS6PHYMdZeBQ4OK3SnbVA0oMc9oykPbCn.webp",
     *                "address": "kolkata",
     *                "city": "kolkata",
     *                "state": "41",
     *                "address2": "kolkata",
     *                "country": "101",
     *                "zip": "700001",
     *                "status": 1,
     *                "created_at": "2024-10-28T08:35:17.000000Z",
     *                "updated_at": "2024-11-06T07:42:16.000000Z"
     *            }
     *        },
     *        "ownUserMailInfo": {
     *            "is_read": 1,
     *            "is_starred": 0,
     *            "is_delete": 0
     *        },
     *        "reply_mails": [
     *            {
     *                "id": 73,
     *                "reply_of": 72,
     *                "form_id": 37,
     *                "to": "swarnadwip@excellisit.net,masum@excellisit.net",
     *                "cc": null,
     *                "subject": "Test Email 10",
     *                "message": "<p>reply own 1<\/p>",
     *                "attachment": "[{\"original_name\":\"Print Email.pdf\",\"encrypted_name\":\"email_files\\\/zWoX12bv6XEDP2kBpU8t3sIk9Z6s5fdxz4EwLXpZ.pdf\"},{\"original_name\":\"dummy - 2.pdf\",\"encrypted_name\":\"email_files\\\/1lgiAan2XVrTAuSjZn6Zi6nU5d6xMU3VLLR0nAci.pdf\"}]",
     *                "is_draft": 0,
     *                "is_delete": 0,
     *                "deleted_at": null,
     *                "created_at": "2024-11-06T05:36:28.000000Z",
     *                "updated_at": "2024-11-06T05:36:28.000000Z",
     *                "ownUserMailInfo": {
     *                    "is_read": 1,
     *                    "is_starred": 0
     *                },
     *                "user": {
     *                    "id": 37,
     *                    "ecclesia_id": 4,
     *                    "created_id": null,
     *                    "user_name": "masum1",
     *                    "first_name": "Test",
     *                    "middle_name": null,
     *                    "last_name": "User",
     *                    "email": "masum@excellisit.net",
     *                    "phone": "+91 9123456789",
     *                    "email_verified_at": "2024-10-28T08:35:17.000000Z",
     *                    "profile_picture": "profile_picture\/sLWWnksqS6PHYMdZeBQ4OK3SnbVA0oMc9oykPbCn.webp",
     *                    "address": "kolkata",
     *                    "city": "kolkata",
     *                    "state": "41",
     *                    "address2": "kolkata",
     *                    "country": "101",
     *                    "zip": "700001",
     *                    "status": 1,
     *                    "created_at": "2024-10-28T08:35:17.000000Z",
     *                    "updated_at": "2024-11-06T07:42:16.000000Z"
     *                }
     *            }
     *        ],
     *        "allMailIds": [           
     *            {
     *                "id": 12,
     *                "email": "swarnadwip@excellisit.net"
     *            },
     *            {
     *                "id": 13,
     *                "email": "john@yopmail.com"
     *            },           
     *            {
     *                "id": 38,
     *                "email": "masum2@excellisit.net"
     *            }
     *        ],
     *        "replyMailIds": [
     *            "masum@excellisit.net"
     *        ]
     *    }
     *}
     */
    public function view(Request $request)
    {
        try {
            $id = $request->mail_id;

            // Find the initial email or the main email if this is a reply
            $init_mail = SendMail::findOrFail($id);
            $fetch_mailId = !empty($init_mail->reply_of) ? $init_mail->reply_of : $id;

            // Mark the email as read for the authenticated user
            $init_ownUserMailInfo = MailUser::where('send_mail_id', $id)
                ->where('user_id', auth()->id())
                ->first();
            if ($init_ownUserMailInfo) {
                $init_ownUserMailInfo->is_read = 1;
                $init_ownUserMailInfo->save();
            }

            // Fetch the main email details along with sender info
            $mail_details = SendMail::with('user')->findOrFail($fetch_mailId);

            // Retrieve user-specific mail info for the main email
            $ownUserMailInfo = MailUser::where('send_mail_id', $fetch_mailId)
                ->where('user_id', auth()->id())
                ->first(['is_read', 'is_starred', 'is_delete']);

            // Fetch replies to the main email and add user-specific info to each
            $reply_mails = SendMail::with('user')
                ->where('reply_of', $fetch_mailId)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($reply) {
                    $reply->ownUserMailInfo = MailUser::where('send_mail_id', $reply->id)
                        ->where('user_id', auth()->id())
                        ->first(['is_read', 'is_starred']);
                    return $reply;
                });

            // List of all user emails except the authenticated user
            $allMailIds = User::where('status', true)
                ->where('id', '!=', auth()->id())
                ->get(['id', 'email']);

            // Collect emails involved in the thread (main email + replies)
            $replyMailIds = collect([$mail_details->user->email])
                ->merge($reply_mails->pluck('user.email'))
                ->unique();

            // Return the email details as a JSON response
            return response()->json([
                'message' => 'Email details retrieved successfully.',
                'status' => true,
                'data' => [
                    'mail_details' => $mail_details,
                    'ownUserMailInfo' => $ownUserMailInfo,
                    'reply_mails' => $reply_mails,
                    'allMailIds' => $allMailIds,
                    'replyMailIds' => $replyMailIds
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage(), 'status' => false], 201);
        }
    }


    /**
     * Compose Emails
     *
     * This endpoint retrieves a list of active users, excluding the currently authenticated user, 
     * that can be recipients of a new mail.
     *
     * @authenticated
     * @response 200 {
     *   "message": "Users loaded successfully.",
     *   "status": true,
     *   "users": [
     *     {"id": 1, "email": "user1@example.com"},
     *     {"id": 2, "email": "user2@example.com"}
     *   ]
     * }
     * @response 201 {
     *   "message": "You do not have permission to access this page.",
     *   "status": false
     * }
     * @response 201 {
     *   "message": "An error occurred while loading the compose mail users.",
     *   "status": false,
     *   "error": "Exception message details"
     * }
     */
    public function composeMailUsers()
    {
        try {
            $users = User::where('status', true)->where('id', '!=', auth()->id())->get(['id', 'email']);
            return response()->json(['message' => 'Users loaded successfully.', 'status' => true, 'users' => $users], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while loading the compose mail users.', 'status' => false, 'error' => $e->getMessage()], 201);
        }
    }

    /**
     * Send New Email
     *
     * This endpoint allows the authenticated user to send an email to specified recipients, 
     * with the option to include CC recipients and attachments. Notifications are sent to recipients as well.
     *
     * @authenticated
     * 
     * @bodyParam to string required JSON-encoded array of recipient emails in the format [{"value": "masum2@excellisit.net"}, {"value": "user2@example.com"}].
     * @bodyParam cc string JSON-encoded array of CC recipient emails in the same format as the "to" field. Optional.
     * @bodyParam subject string required The subject of the email.
     * @bodyParam message string required The body content of the email.
     * @bodyParam attachments file[] Attachments in file format. Accepted formats: jpg, jpeg, png, pdf, doc, docx. Max size: 2MB each. Optional.
     * 
     * @response 200 {
     *   "message": "Mail sent successfully.",
     *   "status": true,
     *   "send_to_ids": [1, 2, 3],
     *   "notification_message": "You have a <b>new mail</b> from sender@example.com"
     * }
     * @response 201 {
     *   "message": "Failed to send mail. Please try again later.",
     *   "status": false,
     *   "error": "Error message here"
     * }
     * @response 422 {
     *   "message": "Validation error",
     *   "errors": {
     *     "to": ["The to field is required."],
     *     "subject": ["The subject field is required."],
     *     "message": ["The message field is required."]
     *   }
     * }
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMail(Request $request)
    {
        try {
            $request->validate([
                'to' => 'required|json',
                'subject' => 'required|string',
                'message' => 'required|string',
                'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048'
            ]);

            $toEmails = json_decode($request->to, true);
            $to = array_column($toEmails, 'value');

            $cc = [];
            if ($request->cc) {
                $ccEmails = json_decode($request->cc, true);
                $cc = array_column($ccEmails, 'value');
            }

            $invalidEmails = [];
            foreach (array_merge($to, $cc) as $email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $invalidEmails[] = $email;
                }
            }

            if (!empty($invalidEmails)) {
                return response()->json([
                    'message' => 'Invalid email(s): ' . implode(', ', $invalidEmails),
                    'status' => false
                ], 201);
            }

            $cc = array_diff($cc, $to);

            $mail = new SendMail();
            $mail->form_id = auth()->id();
            $mail->to = implode(',', $to);
            $mail->cc = empty($cc) ? null : implode(',', $cc);
            $mail->subject = $request->subject;
            $mail->message = $request->message;

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

            $notification_message = 'You have a <b>new mail</b> from ' . auth()->user()->email;
            $cc_id = [];
            foreach ($cc as $email) {
                $user = User::where('email', $email)->first();
                if ($user) {
                    $cc_id[] = $user->id;
                    $mail_user = new MailUser();
                    $mail_user->user_id = $user->id;
                    $mail_user->send_mail_id = $mail->id;
                    $mail_user->is_cc = 1;
                    $mail_user->save();

                    $notification = new Notification();
                    $notification->user_id = $user->id;
                    $notification->message = $notification_message;
                    $notification->type = 'Mail';
                    $notification->save();
                }
            }

            $to_id = [];
            foreach ($to as $email) {
                $user = User::where('email', $email)->first();
                if ($user) {
                    $to_id[] = $user->id;
                    $mail_user = new MailUser();
                    $mail_user->user_id = $user->id;
                    $mail_user->send_mail_id = $mail->id;
                    $mail_user->is_to = 1;
                    $mail_user->save();

                    $notification = new Notification();
                    $notification->user_id = $user->id;
                    $notification->message = $notification_message;
                    $notification->type = 'Mail';
                    $notification->save();
                }
            }

            $mail_user = new MailUser();
            $mail_user->user_id = auth()->id();
            $mail_user->send_mail_id = $mail->id;
            $mail_user->is_from = 1;
            $mail_user->save();

            $sender_user = auth()->user();
            Mail::to($to)->cc($cc)->send(new MailSendMail($mail, $sender_user->email, $sender_user->full_name));

            return response()->json([
                'message' => 'Mail sent successfully.',
                'status' => true,
                'send_to_ids' => array_merge($cc_id, $to_id),
                'notification_message' => $notification_message
            ], 200);
        } catch (\Exception $e) {
            // \Log::error('Mail sending failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send mail. Please try again later.',
                'status' => false,
                'error' => $e->getMessage()
            ], 201);
        }
    }


    /**
     * Send Reply Email
     *
     * This endpoint allows the authenticated user to reply to an existing email thread, 
     * including attachments and notifications to recipients.
     *
     * @authenticated
     *
     * @bodyParam to string required JSON-encoded array of recipient emails in the format [{"value": "user1@example.com"}, {"value": "user2@example.com"}].
     * @bodyParam cc string JSON-encoded array of CC recipient emails in the same format as the "to" field. Optional.
     * @bodyParam subject string required The subject of the reply email.
     * @bodyParam message string required The body content of the reply email.
     * @bodyParam attachments file[] Attachments in file format. Accepted formats: jpg, jpeg, png, pdf, doc, docx. Max size: 2MB each. Optional.
     * @bodyParam main_mail_id integer required The ID of the main email thread being replied to.
     *
     * @response 200 {
     *   "message": "Mail sent successfully.",
     *   "status": true,
     *   "send_to_ids": [1, 2, 3],
     *   "notification_message": "You have a <b>new mail</b> from sender@example.com"
     * }
     * @response 201 {
     *   "message": "Failed to send mail. Please try again later.",
     *   "status": false,
     *   "error": "Error message here"
     * }
     * @response 422 {
     *   "message": "Validation error",
     *   "errors": {
     *     "to": ["The to field is required."],
     *     "subject": ["The subject field is required."],
     *     "message": ["The message field is required."],
     *     "main_mail_id": ["The main_mail_id field is required."]
     *   }
     * }
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMailReply(Request $request)
    {
        try {
            $request->validate([
                'to' => 'required|json',
                'subject' => 'required|string',
                'message' => 'required|string',
                'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
                'main_mail_id' => 'required|integer|exists:send_mails,id'
            ]);

            $toEmails = json_decode($request->to, true);
            $to = array_column($toEmails, 'value');

            $cc = [];
            if ($request->cc) {
                $ccEmails = json_decode($request->cc, true);
                $cc = array_column($ccEmails, 'value');
            }

            $invalidEmails = [];
            foreach (array_merge($to, $cc) as $email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $invalidEmails[] = $email;
                }
            }

            if (!empty($invalidEmails)) {
                return response()->json([
                    'message' => 'Invalid email(s): ' . implode(', ', $invalidEmails),
                    'status' => false
                ], 201);
            }

            $cc = array_diff($cc, $to);

            $mail = new SendMail();
            $mail->reply_of = $request->main_mail_id;
            $mail->form_id = auth()->id();
            $mail->to = implode(',', $to);
            $mail->cc = empty($cc) ? null : implode(',', $cc);
            $mail->subject = $request->subject;
            $mail->message = $request->message;

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

            $notification_message = 'You have a <b>new mail</b> from ' . auth()->user()->email;
            $cc_id = [];
            foreach ($cc as $email) {
                $user = User::where('email', $email)->first();
                if ($user) {
                    $cc_id[] = $user->id;
                    $mail_user = new MailUser();
                    $mail_user->user_id = $user->id;
                    $mail_user->send_mail_id = $mail->id;
                    $mail_user->is_cc = 1;
                    $mail_user->save();

                    $notification = new Notification();
                    $notification->user_id = $user->id;
                    $notification->message = $notification_message;
                    $notification->type = 'Mail';
                    $notification->save();
                }
            }

            $to_id = [];
            foreach ($to as $email) {
                $user = User::where('email', $email)->first();
                if ($user) {
                    $to_id[] = $user->id;
                    $mail_user = new MailUser();
                    $mail_user->user_id = $user->id;
                    $mail_user->send_mail_id = $mail->id;
                    $mail_user->is_to = 1;
                    $mail_user->save();

                    $notification = new Notification();
                    $notification->user_id = $user->id;
                    $notification->message = $notification_message;
                    $notification->type = 'Mail';
                    $notification->save();
                }
            }

            $mail_user = new MailUser();
            $mail_user->user_id = auth()->id();
            $mail_user->send_mail_id = $mail->id;
            $mail_user->is_from = 1;
            $mail_user->save();

            $sender_user = auth()->user();
            Mail::to($to)->cc($cc)->send(new MailSendMail($mail, $sender_user->email, $sender_user->full_name));

            return response()->json([
                'message' => 'Mail sent successfully.',
                'status' => true,
                'send_to_ids' => array_merge($cc_id, $to_id),
                'notification_message' => $notification_message
            ], 200);
        } catch (\Exception $e) {
            //  \Log::error('Mail sending failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send mail. Please try again later.',
                'status' => false,
                'error' => $e->getMessage()
            ], 201);
        }
    }


    /**
     * Forward Email
     *
     * This endpoint allows the authenticated user to forward an existing email to a list of recipients, 
     * including optional CC recipients and attachments.
     *
     * @authenticated
     *
     * @bodyParam to string required JSON-encoded array of recipient emails in the format [{"value": "user1@example.com"}, {"value": "user2@example.com"}].
     * @bodyParam cc string JSON-encoded array of CC recipient emails in the same format as the "to" field. Optional.
     * @bodyParam subject string required The subject of the forwarded email.
     * @bodyParam message string required The body content of the forwarded email.
     * @bodyParam attachments file[] Attachments in file format. Accepted formats: jpg, jpeg, png, pdf, doc, docx. Max size: 2MB each. Optional.
     *
     * @response 200 {
     *   "message": "Mail sent successfully.",
     *   "status": true,
     *   "send_to_ids": [1, 2, 3],
     *   "notification_message": "You have a <b>new mail</b> from sender@example.com"
     * }
     * @response 201 {
     *   "message": "Failed to send mail. Please try again later.",
     *   "status": false,
     *   "error": "Error message here"
     * }
     * @response 422 {
     *   "message": "Validation error",
     *   "errors": {
     *     "to": ["The to field is required."],
     *     "subject": ["The subject field is required."],
     *     "message": ["The message field is required."]
     *   }
     * }
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMailForward(Request $request)
    {
        try {
            $request->validate([
                'to' => 'required|json',
                'subject' => 'required|string',
                'message' => 'required|string',
                'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048'
            ]);

            $toEmails = json_decode($request->to, true);
            $to = array_column($toEmails, 'value');

            $cc = [];
            if ($request->cc) {
                $ccEmails = json_decode($request->cc, true);
                $cc = array_column($ccEmails, 'value');
            }

            $invalidEmails = [];
            foreach (array_merge($to, $cc) as $email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $invalidEmails[] = $email;
                }
            }

            if (!empty($invalidEmails)) {
                return response()->json([
                    'message' => 'Invalid email(s): ' . implode(', ', $invalidEmails),
                    'status' => false
                ], 201);
            }

            $cc = array_diff($cc, $to);

            $mail = new SendMail();
            $mail->form_id = auth()->id();
            $mail->to = implode(',', $to);
            $mail->cc = empty($cc) ? null : implode(',', $cc);
            $mail->subject = $request->subject;
            $mail->message = $request->message;

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

            $notification_message = 'You have a <b>new mail</b> from ' . auth()->user()->email;
            $cc_id = [];
            foreach ($cc as $email) {
                $user = User::where('email', $email)->first();
                if ($user) {
                    $cc_id[] = $user->id;
                    $mail_user = new MailUser();
                    $mail_user->user_id = $user->id;
                    $mail_user->send_mail_id = $mail->id;
                    $mail_user->is_cc = 1;
                    $mail_user->save();

                    $notification = new Notification();
                    $notification->user_id = $user->id;
                    $notification->message = $notification_message;
                    $notification->type = 'Mail';
                    $notification->save();
                }
            }

            $to_id = [];
            foreach ($to as $email) {
                $user = User::where('email', $email)->first();
                if ($user) {
                    $to_id[] = $user->id;
                    $mail_user = new MailUser();
                    $mail_user->user_id = $user->id;
                    $mail_user->send_mail_id = $mail->id;
                    $mail_user->is_to = 1;
                    $mail_user->save();

                    $notification = new Notification();
                    $notification->user_id = $user->id;
                    $notification->message = $notification_message;
                    $notification->type = 'Mail';
                    $notification->save();
                }
            }

            $mail_user = new MailUser();
            $mail_user->user_id = auth()->id();
            $mail_user->send_mail_id = $mail->id;
            $mail_user->is_from = 1;
            $mail_user->save();

            $sender_user = auth()->user();
            Mail::to($to)->cc($cc)->send(new MailSendMail($mail, $sender_user->email, $sender_user->full_name));

            return response()->json([
                'message' => 'Mail sent successfully.',
                'status' => true,
                'send_to_ids' => array_merge($cc_id, $to_id),
                'notification_message' => $notification_message
            ], 200);
        } catch (\Exception $e) {
            //   \Log::error('Mail sending failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send mail. Please try again later.',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete Emails
     *
     * This endpoint allows the authenticated user to delete multiple emails by marking them as deleted.
     * The emails are logically deleted by setting the `is_delete` flag and `deleted_at` timestamp.
     *
     * @authenticated
     *
     * @bodyParam mailIds array required An array of mail IDs to be deleted. Example: [1, 2, 3]
     *
     * @response 200 {
     *   "message": "Mail deleted successfully.",
     *   "status": true
     * }
     * @response 404 {
     *   "message": "Mail not found.",
     *   "status": false
     * }
     * @response 422 {
     *   "message": "Validation error.",
     *   "errors": {
     *     "mailIds": ["The mailIds field is required."]
     *   }
     * }
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        try {
            $request->validate([
                'mailIds' => 'required|array',
                'mailIds.*' => 'integer|exists:mail_users,send_mail_id'
            ]);

            foreach ($request->mailIds as $mailId) {
                $mail = MailUser::where('send_mail_id', $mailId)->where('user_id', auth()->id())->first();
                if ($mail) {
                    $mail->is_delete = 1;
                    $mail->deleted_at = now();
                    $mail->save();
                }
            }

            return response()->json(['message' => 'Mail deleted successfully.', 'status' => true], 200);
        } catch (\Exception $e) {
            // \Log::error('Failed to delete mail: '. $e->getMessage());
            return response()->json(['message' => 'Failed to delete mail. Please try again later.', 'status' => false], 201);
        }
    }


    /**
     * Delete Sent Emails
     *
     * This endpoint allows the authenticated user to delete multiple sent emails by marking them as deleted.
     * The emails are logically deleted by setting the `is_delete` flag and `deleted_at` timestamp.
     *
     * @authenticated
     *
     * @bodyParam mailIds array required An array of mail IDs to be deleted. Example: [1, 2, 3]
     *
     * @response 200 {
     *   "message": "Mail deleted successfully.",
     *   "status": true
     * }
     * @response 404 {
     *   "message": "Mail not found.",
     *   "status": false
     * }
     * @response 422 {
     *   "message": "Validation error.",
     *   "errors": {
     *     "mailIds": ["The mailIds field is required."]
     *   }
     * }
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSentsMail(Request $request)
    {
        try {
            $request->validate([
                'mailIds' => 'required|array',
                'mailIds.*' => 'integer|exists:send_mails,id'
            ]);

            foreach ($request->mailIds as $mailId) {
                $mail = MailUser::where('send_mail_id', $mailId)->where('user_id', auth()->id())->first();
                if ($mail) {
                    $mail->is_delete = 1;
                    $mail->deleted_at = now();
                    $mail->save();
                }
            }

            return response()->json(['message' => 'Mail deleted successfully.', 'status' => true], 200);
        } catch (\Exception $e) {
            // \Log::error('Failed to delete sent mail: '. $e->getMessage());
            return response()->json(['message' => 'Failed to delete sent mail. Please try again later. ' . $e->getMessage(), 'status' => false], 201);
        }
    }

    /**
     * Restore Deleted Mails
     *
     * @authenticated
     * @bodyParam mailIds array required The IDs of the mails to restore. Example: [1, 2, 3]
     *
     * @response 200 {
     *   "message": "Mail restored successfully.",
     *   "status": true
     * }
     * @response 201 {
     *   "message": "Failed to restore mail. Please try again later.",
     *   "status": false
     * }
     */
    public function restore(Request $request)
    {
        try {

            $mailIds = $request->mailIds;
            foreach ($mailIds as $mailId) {
                $mail = MailUser::where('send_mail_id', $mailId)->where('user_id', auth()->id())->first();
                if ($mail) {
                    $mail->is_delete = 0;
                    $mail->save();
                }
            }
            return response()->json(['message' => 'Mail restored successfully.', 'status' => true], 200);
        } catch (\Exception $e) {
            //  \Log::error('Mail restore failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to restore mail. Please try again later.', 'status' => false], 201);
        }
    }


    /**
     * Star Mark on a Mail
     *
     * @authenticated
     * @bodyParam mail_id int required The ID of the mail to be starred or unstarred. Example: 1
     * @bodyParam start_value int required The star value (1 for starred, 0 for unstarred). Example: 1
     *
     * @response 200 {
     *   "message": "Mail Starred Success!",
     *   "status": true
     * }
     * @response 200 {
     *   "message": "Mail Star Mark Removed!",
     *   "status": true
     * }
     * @response 201 {
     *   "message": "Failed to update star status. Please try again later.",
     *   "status": false
     * }
     */
    public function star(Request $request)
    {
        try {
            $mail_id = $request->mail_id;
            $start_value = $request->start_value; // 1 or 0
            $msg = '';

            $mail = MailUser::where('send_mail_id', $mail_id)->where('user_id', auth()->id())->first();
            if ($mail) {
                $mail->is_starred = $start_value;
                $mail->save();
            }

            if ($start_value == 1) {
                $msg = "Mail Starred Success!";
            } else {
                $msg = "Mail Star Mark Removed!";
            }

            return response()->json(['message' => $msg, 'status' => true], 200);
        } catch (\Exception $e) {
            //  \Log::error('Mail star/unstar failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update star status. Please try again later.', 'status' => false], 201);
        }
    }

    /**
     * Delete Single Email
     *
     * @authenticated
     * @bodyParam mail_id int required The ID of the mail to be deleted. Example: 1
     *
     * @response 200 {
     *   "message": "Mail deleted successfully.",
     *   "status": true
     * }
     * @response 500 {
     *   "message": "Failed to delete mail. Please try again later.",
     *   "status": false
     * }
     */
    public function deleteSingleMail(Request $request)
    {
        try {

            $mailid = $request->mail_id;

            $mail = MailUser::where('send_mail_id', $mailid)->where('user_id', auth()->id())->first();
            if ($mail) {
                $mail->is_delete = 1;
                $mail->deleted_at = now();
                $mail->save();
            }

            return response()->json(['message' => 'Mail deleted successfully.', 'status' => true], 200);
        } catch (\Exception $e) {
            //  \Log::error('Mail deletion failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete mail. Please try again later.', 'status' => false], 500);
        }
    }

    /**
     * Restore Single Email
     *
     * @authenticated
     * @bodyParam mail_id int required The ID of the mail to be restored. Example: 1
     *
     * @response 200 {
     *   "message": "Mail restored successfully.",
     *   "status": true
     * }
     * @response 201 {
     *   "message": "Failed to restore mail. Please try again later.",
     *   "status": false
     * }
     */
    public function restoreSingleMail(Request $request)
    {
        try {

            $mailid = $request->mail_id;

            $mail = MailUser::where('send_mail_id', $mailid)->where('user_id', auth()->id())->first();
            if ($mail) {
                $mail->is_delete = 0;
                $mail->save();
            }

            return response()->json(['message' => 'Mail restored successfully.', 'status' => true], 200);
        } catch (\Exception $e) {
            //  \Log::error('Mail restoration failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to restore mail. Please try again later.', 'status' => false], 201);
        }
    }


    /**
     * Print Email
     *
     * @authenticated
     * @urlParam id int required The ID of the mail to be printed. Example: 1
     *
     * @response 200 {
     *   "message": "Mail details fetched successfully.",
     *   "status": true
     * }
     * @response 201 {
     *   "message": "Failed to fetch mail details. Please try again later.",
     *   "status": false
     * }
     */
    public function printMail($id)
    {
        try {
            // Fetch the main mail details
            $mail_details = SendMail::with('user')->findOrFail($id);

            // Fetch the current user's mail information for the mail
            $ownUserMailInfo = MailUser::where('send_mail_id', $id)->where('user_id', auth()->id())->first();

            // Fetch replies to this mail
            $reply_mails = SendMail::with('user')->where('reply_of', $id)->orderBy('created_at', 'asc')->get();

            // Attach the current user's mail information for each reply
            $reply_mails->each(function ($reply) {
                $reply->ownUserMailInfo = MailUser::where('send_mail_id', $reply->id)->where('user_id', auth()->id())->first();
            });

            // Pass the mail details to the print view
            return view('user.mail.mail-print', compact('mail_details', 'ownUserMailInfo', 'reply_mails'));
        } catch (\Exception $e) {
            // Log the error and return a 500 response
         //   \Log::error('Failed to fetch mail details: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to fetch mail details. Please try again later.',
                'status' => false
            ], 201);
        }
    }










    //
}
