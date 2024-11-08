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
     *           "is_read": 0,
     *           "is_delete": 0
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
                ->first(['is_read', 'is_delete']);
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










    //
}
