<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\SendElearningNewsletterJob;
use App\Models\ElearningEcomNewsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ElearningNewsletterController extends Controller
{
    public function list()
    {
        if (auth()->user()->hasNewRole('SUPER ADMIN')) {
            $newsletters = ElearningEcomNewsletter::orderBy('id', 'desc')->paginate(10);
            return view('user.elearning-newsletter.list')->with('newsletters', $newsletters);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function fetchData(Request $request)
    {
        $sort_by = $request->get('sortby', 'id');
        $sort_type = $request->get('sorttype', 'asc');
        $query = $request->get('query', '');
        $perPage = $request->get('per_page', 10); // default 10

        $query = str_replace(' ', '%', $query);

        $newsletters = ElearningEcomNewsletter::query()
            ->where(function ($q) use ($query) {
                $q->where('id', 'like', '%' . $query . '%')
                    ->orWhere('name', 'like', '%' . $query . '%')
                    ->orWhere('email', 'like', '%' . $query . '%');
            })
            ->orderBy($sort_by, $sort_type)
            ->paginate($perPage);

        return response()->json([
            'data' => view('user.elearning-newsletter.table', compact('newsletters'))->render()
        ]);
    }

    // delete
    public function delete($id)
    {
        if (auth()->user()->hasNewRole('SUPER ADMIN')) {
            $newsletter = ElearningEcomNewsletter::find($id);
            Log::info(($newsletter->id ?? $id) . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
            if ($newsletter) {
                $newsletter->delete();
                return redirect()->back()->with('message', 'Newsletter deleted successfully.');
            } else {
                return redirect()->back()->with('error', 'Newsletter not found.');
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function sendEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'integer|exists:elearning_ecom_newsletters,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ], [
            'selected_ids.required' => 'No recipients selected.',
            'selected_ids.*.exists' => 'One of the selected recipients is invalid.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $selectedIds = $request->input('selected_ids', []);
        $subject = $request->input('subject');
        $body = $request->input('body');

        // Dispatch job to handle emailing (queue)
        SendElearningNewsletterJob::dispatch($selectedIds, $subject, $body)->onQueue('emails');

        return response()->json(['message' => 'Emails have been queued for sending.']);
    }
}
