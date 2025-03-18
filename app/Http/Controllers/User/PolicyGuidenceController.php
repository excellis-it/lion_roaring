<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use App\Services\NotificationService;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PolicyGuidenceController extends Controller
{
    use ImageTrait;

    public function index()
    {
        if (auth()->user()->can('Manage Policy')) {
            $policies = Policy::orderBy('id', 'desc')->paginate(15);
            return view('user.policy.list')->with(compact('policies'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function upload()
    {
        if (auth()->user()->can('Upload Policy')) {
            return view('user.policy.upload');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'file' => 'required|array', // Ensure policy is an array
            'file.*' => 'required', // Ensure each policy is valid
        ]);

        // Check if validation fails
        if ($validated->fails()) {
            return redirect()->back()->withErrors($validated)->withInput();
        }

        // Loop through each policy and process it
        foreach ($request->file('file') as $file) {
            $file_name = $file->getClientOriginalName();
            $file_extension = $file->getClientOriginalExtension();
            $file_path = $this->imageUpload($file, 'policies');

            // Check if a policy with the same name and extension already exists for the user
            $check = Policy::where('file_name', $file_name)
                ->where('file_extension', $file_extension)
                ->first();

            // Return validation error if policy already exists
            if ($check) {
                return redirect()->back()->withErrors(['file' => 'The policy name "' . $file_name . '" has already been taken.'])->withInput();
            }

            // Save the new policy details to the database
            $file_upload = new Policy();
            $file_upload->user_id = auth()->id();
            $file_upload->file_name = $file_name;
            $file_upload->file_extension = $file_extension;
            $file_upload->file = $file_path;
            $file_upload->save();
        }

        // notify users
        $userName = Auth::user()->getFullNameAttribute();
        $noti = NotificationService::notifyAllUsers('New Policy created by ' . $userName, 'policy');

        // Redirect with success message
        return redirect()->route('policy-guidence.index')->with('message', 'Policy(s) uploaded successfully.');
    }

    public function delete($id)
    {
        $policy = Policy::find($id);
        if ((auth()->user()->can('Delete Policy') && $policy->user_id == auth()->user()->id) ||
            auth()->user()->hasRole('SUPER ADMIN')
        ) {

            Log::info($policy->id . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
            if ($policy) {

                // delete policy from storage
                // Storage::disk('public')->delete($policy->file);
                // delete policy from storage folder if exists
                if (Storage::disk('public')->exists($policy->file)) {
                    Storage::disk('public')->delete($policy->file);
                }
                $policy->delete();

                return redirect()->route('policy-guidence.index')->with('message', 'Policy deleted successfully.');
            } else {
                return redirect()->route('policy-guidence.index')->with('error', 'Policy not found.');
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function download($id)
    {
        if (auth()->user()->can('Download Policy')) {
            $policy = Policy::where('id', $id)->first();
            if ($policy) {
                $filePath = Storage::disk('public')->path($policy->file); // ensure using 'public' disk
                if (file_exists($filePath)) {
                    return response()->download($filePath);
                } else {
                    return redirect()->route('policy-guidence.index')->with('error', 'Policy not found.');
                }
            } else {
                return redirect()->route('policy-guidence.index')->with('error', 'Policy not found.');
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function view($id)
    {
        if (auth()->user()->can('View Policy')) {
            $policy = Policy::findOrFail($id);
            if ($policy) {
                return view('user.policy.view')->with('policy', $policy);
            } else {
                return redirect()->route('policy-guidence.index')->with('error', 'File not found.');
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function fetchData(Request $request)
    {
        if ($request->ajax()) {
            $sort_by = $request->get('sortby', 'id'); // Default sort by 'id'
            $sort_type = $request->get('sorttype', 'asc'); // Default sort type 'asc'
            $query = $request->get('query', '');
            $query = str_replace(" ", "%", $query);

            $policies = Policy::query()
                ->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhere('file_name', 'like', '%' . $query . '%')
                        ->orWhere('file_extension', 'like', '%' . $query . '%');
                });
            if ($request->topic_id) {
                $policies->whereHas('topic', function ($q) use ($request) {
                    $q->where('id', $request->topic_id);
                });
            }
            if ($request->type) {
                $policies->where('type', $request->type);
            }
            $policies = $policies->orderBy($sort_by, $sort_type)
                ->paginate(15);

            return response()->json(['data' => view('user.policy.table', compact('policies'))->render()]);
        }
    }
}
