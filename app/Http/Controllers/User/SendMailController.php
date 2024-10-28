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

class SendMailController extends Controller
{
    public function list()
    {
        if (auth()->user()->can('Manage Email')) {
            $mails = SendMail::whereHas('mailUsers', function ($q) {
                $q->where('user_id', auth()->id())->where('is_delete', 0);
            })->orderBy('created_at', 'desc')->paginate(15);
            $allMailIds = User::where('status', true)->where('id', '!=', auth()->id())->get(['id', 'email']);
           // dd($to_users);
           // return view('user.mail.list')->with('mails', $mails);
           return view('user.mail.list', ['mails' => $mails, 'allMailIds' => $allMailIds]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function inboxEmailList()
    {
        $mails = SendMail::whereHas('mailUsers', function ($q) {
            $q->where('user_id', auth()->id())->where('is_delete', 0);
        })->orderBy('created_at', 'desc')->paginate(15);
        return response()->json(['data' => view('user.mail.partials.inbox-email-list', compact('mails'))->render()]);
    }

    public function sent()
    {
        if (auth()->user()->can('Manage Email')) {
            $mails = SendMail::where('form_id', auth()->id())->where('is_delete', 0)->orderBy('created_at', 'desc')->paginate(15);
            $allMailIds = User::where('status', true)->where('id', '!=', auth()->id())->get(['id', 'email']);
           // return view('user.mail.sent')->with('mails', $mails);
           return view('user.mail.sent', ['mails' => $mails, 'allMailIds' => $allMailIds]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
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
            $users = User::where('status', true)->where('id', '!=', auth()->id())->get(['id', 'email']); // Adjust fields as needed
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



                    $notification = new Notification();
                    $notification->user_id =  $user->id;
                    $notification->message = $notification_message;
                    $notification->type = 'Mail';
                    $notification->save();
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

                $notification = new Notification();
                $notification->user_id =  $user->id;
                $notification->message = $notification_message;
                $notification->type = 'Mail';
                $notification->save();
            }
        }

        // Mail::to($to)->cc($cc)->send(new MailSendMail($mail));

        // session()->flash('message', 'Your mail has been sent Successfully');

        // return response()->json(['message' => 'Mail sent successfully.', 'status' => true, 'send_to_ids' => array_merge($cc_id, $to_id), 'notification_message' => $notification_message]);

        try {
            
            Mail::to($to)->cc($cc)->send(new MailSendMail($mail));
            
            
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



    public function view($id)
    {
        $id = base64_decode($id);
        $mail = MailUser::where('send_mail_id', $id)->where('user_id', auth()->id())->first();
        if ($mail) {
            $mail->is_read = 1;
            $mail->save();
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
        $mail = SendMail::findOrFail($id);
        return view('user.mail.mail-details')->with(compact('mail'));
    }

    // sentMailView
    public function sentMailView($id)
    {
        $id = base64_decode($id);
        $mail = SendMail::findOrFail($id);
        return view('user.mail.mail-details')->with(compact('mail'));
    }
    // delete
    public function delete(Request $request)
    {
        if (auth()->user()->can('Manage Email')) {
            $mailIds = $request->mailIds;
            foreach ($mailIds as $mailId) {
                $mail = MailUser::where('send_mail_id', $mailId)->where('user_id', auth()->id())->first();
                if ($mail) {
                    $mail->is_delete = 1;
                    $mail->deleted_at = now();
                    $mail->save();
                }
            }
            return response()->json(['message' => 'Mail deleted successfully.', 'status' => true]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
