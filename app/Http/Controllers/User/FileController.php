<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\File;
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
            return view('user.file.list')->with('files', $files);
        } else {
            return redirect()->route('user.dashboard')->with('error', 'Permission denied.');
        }
    }

    public function upload()
    {
        if (auth()->user()->can('Upload File')) {
            return view('user.file.upload');
        } else {
            return redirect()->route('user.dashboard')->with('error', 'Permission denied.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|max:2048',
        ]);



        foreach ($request->file('file') as $file) {
            $file_name = $file->getClientOriginalName();
            $file_extension = $file->getClientOriginalExtension();
            $file = $this->imageUpload($file, 'files');

            $file_upload = new File();
            $file_upload->user_id = auth()->id();
            $file_upload->file_name = $file_name;
            $file_upload->file_extension = $file_extension;
            $file_upload->file = $file;
            $file_upload->save();
        }

        return redirect()->route('file.index')->with('message', 'File uploaded successfully.');
    }

    public function delete($id)
    {
        if (auth()->user()->can('Delete File')) {
            $file = File::find($id)->where('user_id', auth()->id())->first();
            if ($file) {
                $file->delete();
                // delete file from storage
                Storage::disk('public')->delete($file->file);
                return redirect()->route('file.index')->with('message', 'File deleted successfully.');
            } else {
                return redirect()->route('file.index')->with('error', 'File not found.');
            }
        } else {
            return redirect()->route('user.dashboard')->with('error', 'Permission denied.');
        }
    }

    public function download($id)
    {
        $file = File::where('id', $id)->where('user_id', auth()->id())->first();
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
                })
                ->orderBy($sort_by, $sort_type)
                ->paginate(15);

            return response()->json(['data' => view('user.file.table', compact('files'))->render()]);
        }
    }
}
