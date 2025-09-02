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

class BecomingChristLikeController extends Controller
{
    use ImageTrait;

    public function index(Request $request)
    {
        if (auth()->user()->can('Manage Becoming Christ Like')) {
            if (isset($request->topic)) {
                $new_topic = $request->topic;
                $files = File::orderBy('id', 'desc')->where('type', 'Becoming Christ Like')->where('topic_id', $request->topic)->paginate(15);
            } else {
                $files = File::orderBy('id', 'desc')->where('type', 'Becoming Christ Like')->paginate(15);
                $new_topic = '';
            }

            $topics = Topic::orderBy('topic_name', 'asc')->where('education_type', 'Becoming Christ Like')->get();
            return view('user.becoming-christ-link.list')->with(compact('files', 'topics', 'new_topic'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function upload()
    {
        if (auth()->user()->can('Upload Becoming Christ Like')) {
            $topics = Topic::orderBy('topic_name', 'asc')->where('education_type', 'Becoming Christ Like')->get();
            return view('user.becoming-christ-link.upload')->with('topics', $topics);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'file' => 'required|file', // Ensure file validation
            'topic_id' => 'required|exists:topics,id', // 'exists' checks if the value exists in the 'topics' table 'id' column
        ]);

        // Get file details
        $file = $request->file('file');
        $file_name = $file->getClientOriginalName();
        $file_extension = $file->getClientOriginalExtension();
        $file_upload = $this->imageUpload($file, 'files');

        // Check if a file with the same name and extension already exists
        $check = File::where('file_name', $file_name)->where('file_extension', $file_extension)->first();

        // Return validation error if file already exists
        if ($check) {
            return redirect()->back()->withErrors(['file' => 'The file name has already been taken.'])->withInput();
        }

        // Save the new file details to the database
        $fileModel = new File();
        $fileModel->user_id = auth()->id();
        $fileModel->file_name = $file_name;
        $fileModel->file_extension = $file_extension;
        $fileModel->topic_id = $request->topic_id;
        $fileModel->type = 'Becoming Christ Like';
        $fileModel->file = $file_upload;
        $fileModel->save();

        $userName = auth()->user()->getFullNameAttribute();
        $noti = NotificationService::notifyAllUsers('New Becoming Christ Like created by ' . $userName, 'becoming_christ_like');

        // Redirect with success message
        return redirect()->route('becoming-christ-link.index')->with('message', 'File uploaded successfully.');
    }

    public function delete(Request $request, $id)
    {
        if (auth()->user()->can('Delete Becoming Christ Like')) {
            $file = File::find($id);
            if (isset($request->topic)) {
                $new_topic = $request->topic;
            } else {
                $new_topic = '';
            }
            if ($file) {
                Log::info($file->file_name . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
                $file->delete();
                // delete file from storage
                Storage::disk('public')->delete($file->file);

                return redirect()->route('becoming-christ-link.index', ['topic' =>  $new_topic])->with('message', 'File deleted successfully.');
            } else {
                return redirect()->route('becoming-christ-link.index', ['topic' =>  $new_topic])->with('error', 'File not found.');
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
                return redirect()->route('becoming-christ-link.index')->with('error', 'File not found.');
            }
        } else {
            return redirect()->route('becoming-christ-link.index')->with('error', 'File not found.');
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

            $files = $files->where('type', 'Becoming Christ Like')
                ->orderBy($sort_by, $sort_type)
                ->paginate(15);

            return response()->json(['data' => view('user.becoming-christ-link.table', compact('files', 'new_topic'))->render()]);
        }
    }

    public function edit(Request $request, $id)
    {
        if (auth()->user()->can('Edit Becoming Christ Like')) {
            $file = File::find($id);
            if (isset($request->topic)) {
                $new_topic = $request->topic;
            } else {
                $new_topic = '';
            }
            if ($file) {
                $topics = Topic::orderBy('topic_name', 'asc')->where('education_type', 'Becoming Christ Like')->get();
                return view('user.becoming-christ-link.edit')->with(compact('file', 'topics', 'new_topic'));
            } else {
                return redirect()->route('becoming-christ-link.index', ['topic' =>  $new_topic])->with('error', 'File not found.');
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function update(Request $request, $id)
    {

        // Validate the request
        $validated = $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'file' => 'sometimes|file' // Validate the file only if it is present
        ]);

        // Find the file by ID
        $file = File::findOrFail($id);

        // Handle file upload if a file is present in the request
        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $file_name = $uploadedFile->getClientOriginalName();
            $file_extension = $uploadedFile->getClientOriginalExtension();

            // Check for file name and extension duplication
            $check = File::where('file_name', $file_name)
                ->where('file_extension', $file_extension)
                ->first();

            // If a file with the same name and extension already exists, return an error
            if ($check) {
                return redirect()->back()->withErrors(['file' => 'The file name has already been taken.'])->withInput();
            }

            // Upload the file
            $file_upload = $this->imageUpload($uploadedFile, 'files');

            // Update file details in the database
            $file->file_name = $file_name;
            $file->file_extension = $file_extension;
            $file->file = $file_upload;
        }

        // Update topic ID
        $file->topic_id = $request->topic_id;

        // Save the file details
        $file->save();

        // Redirect with success message
        if (isset($request->new_topic)) {
            $new_topic = $request->new_topic;
        } else {
            $new_topic = '';
        }

        return redirect()->route('becoming-christ-link.index', ['topic' => $new_topic])->with('message', 'File updated successfully.');
    }

    public function view(Request $request, $id)
    {
        if (auth()->user()->can('View Becoming Christ Like')) {
            $file = File::findOrFail($id);
            if (isset($request->topic)) {
                $new_topic = $request->topic;
            } else {
                $new_topic = '';
            }
            if ($file) {
                return view('user.becoming-christ-link.view')->with(compact('file', 'new_topic'));
            } else {
                return redirect()->route('becoming-christ-link.index')->with('error', 'File not found.');
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
