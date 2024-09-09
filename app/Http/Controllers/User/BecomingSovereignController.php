<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Topic;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BecomingSovereignController extends Controller
{
    use ImageTrait;

    public function index(Request $request)
    {
        if (auth()->user()->can('Manage Becoming Sovereigns')) {
            if (isset($request->topic)) {
                $new_topic = $request->topic;
                $files = File::orderBy('id', 'desc')->where('type', 'Becoming Sovereign')->where('topic_id', $request->topic)->paginate(15);
            } else {
                $files = File::orderBy('id', 'desc')->where('type', 'Becoming Sovereign')->paginate(15);
                $new_topic = '';
            }
            $topics = Topic::orderBy('topic_name', 'asc')->where('education_type', 'Becoming Sovereign')->get();
            return view('user.becoming-sovereign.list')->with(compact('files', 'topics', 'new_topic'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function upload()
    {
        if (auth()->user()->can('Upload Becoming Sovereigns')) {
            $topics = Topic::orderBy('topic_name', 'asc')->where('education_type', 'Becoming Sovereign')->get();
            return view('user.becoming-sovereign.upload')->with('topics', $topics);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function store(Request $request)
    {

        $validated = Validator::make($request->all(), [
            'file' => 'required|file',  // Ensure file validation
            'topic_id' => 'required|exists:topics,id', // 'exists' checks if the value exists in the 'topics' table 'id' column
        ]);

        // Check if validation fails
        if ($validated->fails()) {
            return redirect()->back()->withErrors($validated)->withInput();
        }

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
        $fileModel->type = 'Becoming Sovereign';
        $fileModel->file = $file_upload;
        $fileModel->save();

        // Redirect with success message
        return redirect()->route('becoming-sovereign.index')->with('message', 'File uploaded successfully.');
    }

    public function delete(Request $request, $id)
    {
        if (auth()->user()->can('Delete Becoming Sovereigns')) {
            $file = File::find($id);
            if (isset($request->topic)) {
                $new_topic = $request->topic;
            } else {
                $new_topic = '';
            }
            if ($file) {
                $file->delete();
                // delete file from storage
                Storage::disk('public')->delete($file->file);
                return redirect()->route('becoming-sovereign.index', ['topic'=>  $new_topic])->with('message', 'File deleted successfully.');
            } else {
                return redirect()->route('becoming-sovereign.index', ['topic'=>  $new_topic])->with('error', 'File not found.');
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
                return redirect()->route('becoming-sovereign.index')->with('error', 'File not found.');
            }
        } else {
            return redirect()->route('becoming-sovereign.index')->with('error', 'File not found.');
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

            $files = $files->where('type', 'Becoming Sovereign')
                ->orderBy($sort_by, $sort_type)
                ->paginate(15);

            return response()->json(['data' => view('user.becoming-sovereign.table', compact('files', 'new_topic'))->render()]);
        }
    }

    public function edit(Request $request, $id)
    {
        if (auth()->user()->can('Edit Becoming Sovereigns')) {
            $file = File::find($id);

            if (isset($request->topic)) {
                $new_topic = $request->topic;
            } else {
                $new_topic = '';
            }

            if ($file) {
                $topics = Topic::orderBy('topic_name', 'asc')->where('education_type', 'Becoming Sovereign')->get();
                return view('user.becoming-sovereign.edit')->with(compact('file', 'topics', 'new_topic'));
            } else {
                return redirect()->route('becoming-sovereign.index', ['topic'=>  $new_topic])->with('error', 'File not found.');
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
            // Validate the file input
            $request->validate([
                'file' => 'required|file', // Ensure file validation
            ]);

            // Get file details
            $file_name = $request->file('file')->getClientOriginalName();
            $file_extension = $request->file('file')->getClientOriginalExtension();
            $file_upload = $this->imageUpload($request->file('file'), 'files');

            // Check if a file with the same name and extension already exists
            $check = File::where('file_name', $file_name)->where('file_extension', $file_extension)->first();
            if ($check) {
                return redirect()->back()->withErrors(['file' => 'The file name has already been taken.'])->withInput();
            }

            // Update file details
            $file->file_name = $file_name;
            $file->file_extension = $file_extension;
            $file->file = $file_upload;
        }

        // Update topic_id
        $file->topic_id = $request->topic_id;
        $file->save();
        if (isset($request->new_topic)) {
            $new_topic = $request->new_topic;
        } else {
            $new_topic = '';
        }
        // Redirect with success message
        return redirect()->route('becoming-sovereign.index', ['topic' => $new_topic])->with('message', 'File updated successfully.');
    }

    public function view(Request $request, $id)
    {
        if (auth()->user()->can('View Becoming Sovereigns')) {
            $file = File::findOrFail($id);
            if (isset($request->topic)) {
                $new_topic = $request->topic;
            } else {
                $new_topic = '';
            }
            if ($file) {
                return view('user.becoming-sovereign.view')->with(compact('file', 'new_topic'));
            } else {
                return redirect()->route('becoming-sovereign.index')->with('error', 'File not found.');
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
