<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Topic;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;


class LeadershipDevelopmentController extends Controller
{
    use ImageTrait;

    public function index(Request $request)
    {
        if (auth()->user()->can('Manage Becoming a Leader')) {
            if (isset($request->topic)) {
                $new_topic = $request->topic;
                $files = File::orderBy('id', 'desc')->where('type', 'Becoming a Leader')->where('topic_id', $request->topic)->paginate(15);
            } else {
                $files = File::orderBy('id', 'desc')->where('type', 'Becoming a Leader')->paginate(15);
                $new_topic = '';
            }

            $topics = Topic::orderBy('topic_name', 'asc')->where('education_type', 'Becoming a Leader')->get();
            return view('user.leadership-development.list')->with(compact('files', 'topics', 'new_topic'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function upload()
    {
        if (auth()->user()->can('Upload Becoming a Leader')) {
            $topics = Topic::orderBy('topic_name', 'asc')->where('education_type', 'Becoming a Leader')->get();
            return view('user.leadership-development.upload')->with('topics', $topics);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function store(Request $request)
    {
        $valdate = $request->validate([
            'file' => 'required',
            'topic_id' => 'required|exists:topics,id', // 'exists' checks if the value exists in the 'topics' table 'id' column
        ]);



        $file_name = $request->file('file')->getClientOriginalName();
        $file_extension = $request->file('file')->getClientOriginalExtension();
        $file_upload = $this->imageUpload($request->file('file'), 'files');

        $check = File::where('file_name', $file_name)->where('file_extension', $file_extension)->first();

        // get the same name validation error
        if ($check) {
            return redirect()->back()->withErrors(['file' => 'The file name has already been taken.'])->withInput();
        }


        $file = new File();
        $file->user_id = auth()->id();
        $file->file_name = $file_name;
        $file->file_extension = $file_extension;
        $file->topic_id = $request->topic_id;
        $file->type = 'Becoming a Leader';
        $file->file = $file_upload;
        $file->save();

        $userName = Auth::user()->getFullNameAttribute();
        $noti = NotificationService::notifyAllUsers('New Becoming a Leader created by ' . $userName, 'becoming_a_leader');

        return redirect()->route('leadership-development.index')->with('message', 'File uploaded successfully.');
    }

    public function delete(Request $request, $id)
    {
        if (auth()->user()->can('Delete Becoming a Leader')) {
            $file = File::find($id);
            Log::info($file->file_name . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
            if (isset($request->topic)) {
                $new_topic = $request->topic;
            } else {
                $new_topic = '';
            }
            if ($file) {
                $file->delete();
                // delete file from storage
                Storage::disk('public')->delete($file->file);
                return redirect()->route('leadership-development.index', ['topic'=>  $new_topic])->with('message', 'File deleted successfully.');
            } else {
                return redirect()->route('leadership-development.index', ['topic'=>  $new_topic])->with('error', 'File not found.');
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function download($id)
    {
        $file = File::where('id', $id)->first();
        if ($file) {
            $filePath = Storage::disk('public')->path($file->file); // ensure using 'public' disk
            if (file_exists($filePath)) {
                return response()->download($filePath);
            } else {
                return redirect()->route('leadership-development.index')->with('error', 'File not found.');
            }
        } else {
            return redirect()->route('leadership-development.index')->with('error', 'File not found.');
        }
    }

    public function fetchData(Request $request)
    {
        if ($request->ajax()) {
            $sort_by = $request->get('sortby', 'id'); // Default sort by 'id'
            $sort_type = $request->get('sorttype', 'asc'); // Default sort type 'asc'
            $query = $request->get('query', '');
            $query = str_replace(" ", "%", $query);

            $files = File::query()
                ->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhere('file_name', 'like', '%' . $query . '%')
                        ->orWhere('file_extension', 'like', '%' . $query . '%');
                });

            if ($request->topic_id) {
                $files->whereHas('topic', function ($q) use ($request) {
                    $q->where('id', $request->topic_id);
                });
                $new_topic = $request->topic_id;
            } else {
                $new_topic = '';
            }

            $files = $files->where('type', 'Becoming a Leader')
                ->orderBy($sort_by, $sort_type)
                ->paginate(15);

            return response()->json(['data' => view('user.leadership-development.table', compact('files', 'new_topic'))->render()]);
        }
    }

    public function edit(Request $request, $id)
    {
        if (auth()->user()->can('Edit Becoming a Leader')) {
            $file = File::findOrFail($id);

            if (isset($request->topic)) {
                $new_topic = $request->topic;
            } else {
                $new_topic = '';
            }

            if ($file) {
                $topics = Topic::orderBy('topic_name', 'asc')->where('education_type', 'Becoming a Leader')->get();
                return view('user.leadership-development.edit')->with(compact('file', 'topics', 'new_topic'));
            } else {
                return redirect()->route('leadership-development.index')->with('error', 'File not found.');
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'topic_id' => 'required|exists:topics,id', // 'exists' checks if the value exists in the 'topics' table 'id' column
        ]);

        $file = File::findOrFail($id);
        if ($request->hasFile('file')) {
            $file_name = $request->file('file')->getClientOriginalName();
            $file_extension = $request->file('file')->getClientOriginalExtension();
            $file_upload = $this->imageUpload($request->file('file'), 'files');
            $check = File::where('file_name', $file_name)->where('file_extension', $file_extension)->first();
            if ($check) {
                return redirect()->back()->withErrors(['file' => 'The file name has already been taken.'])->withInput();
            }
            $file->file_name = $file_name;
            $file->file_extension = $file_extension;
            $file->file = $file_upload;
        }
        $file->type = 'Becoming a Leader';
        $file->topic_id = $request->topic_id;
        $file->save();

        if (isset($request->new_topic)) {
            $new_topic = $request->new_topic;
        } else {
            $new_topic = '';
        }

        return redirect()->route('leadership-development.index', ['topic' => $new_topic])->with('message', 'File updated successfully.');
    }

    public function view(Request $request, $id)
    {
        if (auth()->user()->can('View Becoming a Leader')) {
            $file = File::findOrFail($id);

            if (isset($request->topic)) {
                $new_topic = $request->topic;
            } else {
                $new_topic = '';
            }

            if ($file) {
                return view('user.leadership-development.view')->with(compact('file', 'new_topic'));
            } else {
                return redirect()->route('leadership-development.index')->with('error', 'File not found.');
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
