<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\SendMail as MailSendMail;
use App\Models\SendMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SendMailController extends Controller
{
    public function list()
    {
        if (auth()->user()->can('Manage Email')){
            $mails = SendMail::where('form_id', auth()->id())->orderBy('id', 'desc')->paginate(15);
            return view('user.mail.list')->with('mails', $mails);
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
        if (auth()->user()->can('Manage Email')){
            return view('user.mail.compose');
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

        if ($request->cc) {
            // check the cc email is valid or not
            $cc = explode(',', $request->cc);
            foreach ($cc as $email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return redirect()->back()->with('error', 'CC email is not valid.');
                }
            }
        }

        if ($request->to) {
            // check the to email is valid or not
            $to = explode(',', $request->to);
            foreach ($to as $email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return redirect()->back()->with('error', 'To email is not valid.');
                }
            }
        }

        // dd($request->all());
        $mail = new SendMail();
        $mail->form_id = auth()->id();
        $mail->to = $request->to;
        $mail->cc = $request->cc;
        $mail->subject = $request->subject;
        $mail->message = $request->message;
        $mail->save();

        // Mail::to($request->to)->send(new MailSendMail($mail));
        // send multiple mail to cc at a time
        $cc = explode(',', $request->cc);
        $to = explode(',', $request->to);
        Mail::to($to)->cc($cc)->send(new MailSendMail($mail));

        return redirect()->route('mail.index')->with('message', 'Mail sent successfully.');
    }

    public function view(Request $request)
    {
        $id = $request->id;
        $viewMail = SendMail::findOrFail($id);
        return response()->json(['view' => view('user.mail.model_body', compact('viewMail'))->render(), 'status' => true]);
    }
    // delete
    public function delete($id)
    {
        if (auth()->user()->can('Manage Email')){
            $id = $id;
            $mail = SendMail::findOrFail($id);
            $mail->delete();
            return redirect()->back()->with('message', 'Mail deleted successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
