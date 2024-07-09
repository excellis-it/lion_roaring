<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Topic;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    use ImageTrait;

    public function index()
    {
        if (auth()->user()->can('Manage File')) {
            $files = File::where('user_id', auth()->id())->orderBy('id', 'desc')->paginate(15);
            $topics = Topic::orderBy('topic_name', 'asc')->get();
            return view('user.file.list')->with(compact('files', 'topics'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function upload()
    {
        if (auth()->user()->can('Upload File')) {
            $topics = Topic::orderBy('topic_name', 'asc')->get();
            return view('user.file.upload')->with('topics', $topics);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'topic_id' => 'required|exists:topics,id', // 'exists' checks if the value exists in the 'topics' table 'id' column
            'file' => 'required|max:2048',
            'type' => 'required|in:Becoming Sovereign,Becoming Christ Like,Leadership Development',
        ]);



        foreach ($request->file('file') as $file) {
            $file_name = $file->getClientOriginalName();
            $file_extension = $file->getClientOriginalExtension();
            $file = $this->imageUpload($file, 'files');

            $file_upload = new File();
            $file_upload->user_id = auth()->id();
            $file_upload->file_name = $file_name;
            $file_upload->file_extension = $file_extension;
            $file_upload->topic_id = $request->topic_id;
            $file_upload->type = $request->type;
            $file_upload->file = $file;
            $file_upload->save();
        }

        return redirect()->route('file.index')->with('message', 'File uploaded successfully.');
    }

    public function delete($id)
    {
        if (auth()->user()->can('Delete File')) {
            $file = File::find($id);
            if ($file) {
                $file->delete();
                // delete file from storage
                Storage::disk('public')->delete($file->file);
                return redirect()->route('file.index')->with('message', 'File deleted successfully.');
            } else {
                return redirect()->route('file.index')->with('error', 'File not found.');
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
                return redirect()->route('file.index')->with('error', 'File not found.');
            }
        } else {
            return redirect()->route('file.index')->with('error', 'File not found.');
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
                ->where('user_id', auth()->id())
                ->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhere('file_name', 'like', '%' . $query . '%')
                        ->orWhere('file_extension', 'like', '%' . $query . '%');
                });
                if ($request->topic_id) {
                    $files->whereHas('topic', function ($q) use ($request) {
                        $q->where('id', $request->topic_id);
                    });
                }
              $files = $files->orderBy($sort_by, $sort_type)
                ->paginate(15);

            return response()->json(['data' => view('user.file.table', compact('files'))->render()]);
        }
    }

    public function edit($id)
    {
        if (auth()->user()->can('Edit File')) {
            $file = File::findOrFail($id);
            if ($file) {
                $topics = Topic::orderBy('topic_name', 'asc')->get();
                return view('user.file.edit')->with(compact('file', 'topics'));
            } else {
                return redirect()->route('file.index')->with('error', 'File not found.');
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'topic_id' => 'required|exists:topics,id', // 'exists' checks if the value exists in the 'topics' table 'id' column
            'type' => 'required|in:Becoming Sovereign,Becoming Christ Like,Leadership Development',
        ]);

        $file = File::findOrFail($id);
        if ($request->hasFile('file')) {
            $file_name = $request->file('file')->getClientOriginalName();
            $file_extension = $request->file('file')->getClientOriginalExtension();
            $file_upload = $this->imageUpload($request->file('file'), 'files');
            $file->file_name = $file_name;
            $file->file_extension = $file_extension;
            $file->file = $file_upload;
        }
        $file->topic_id = $request->topic_id;
        $file->type = $request->type;
        $file->save();

        return redirect()->route('file.index')->with('message', 'File updated successfully.');
    }
}
