<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\EcomNewsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsletterController extends Controller
{
    public function list()
    {
        if (auth()->user()->hasRole('SUPER ADMIN')) {
            $newsletters = EcomNewsletter::orderBy('id', 'desc')->paginate(10);
            return view('user.newsletter.list')->with('newsletters', $newsletters);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function fetchData(Request $request)
    {
        $sort_by = $request->get('sortby', 'id'); // Default sort by 'id'
        $sort_type = $request->get('sorttype', 'asc'); // Default sort type 'asc'
        $query = $request->get('query', '');
        $query = str_replace(" ", "%", $query);

        $newsletters = EcomNewsletter::query()
            ->where(function ($q) use ($query) {
                $q->where('id', 'like', '%' . $query . '%')
                    ->orWhere('name', 'like', '%' . $query . '%')
                    ->orWhere('email', 'like', '%' . $query . '%')
                    ->orWhere('message', 'like', '%' . $query . '%');
            });

        $newsletters = $newsletters->orderBy($sort_by, $sort_type)
            ->paginate(10);

        return response()->json(['data' => view('user.newsletter.table', compact('newsletters'))->render()]);
    }

    // delete
    public function delete($id)
    {
        if (auth()->user()->hasRole('SUPER ADMIN')) {
            $newsletter = EcomNewsletter::find($id);
            Log::info($newsletter->id . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
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
}
