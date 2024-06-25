<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BecomingSovereignController extends Controller
{
    use ImageTrait;

    public function index()
    {
        if (auth()->user()->can('Manage Becomeing Sovereigns')) {
            $files = File::where('user_id', auth()->id())->orderBy('id', 'desc')->where('type', 'Becoming Sovereign')->paginate(15);
            return view('user.becoming-sovereign.list')->with('files', $files);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function upload()
    {
        if (auth()->user()->can('Upload Becomeing Sovereigns')) {
            return view('user.becoming-sovereign.upload');
        } else {
            abort(403, 'You do not have permission to access this page.');
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
            $file_upload->type = 'Becoming Sovereign';
            $file_upload->file = $file;
            $file_upload->save();
        }

        return redirect()->route('becoming-sovereign.index')->with('message', 'File uploaded successfully.');
    }

    public function delete($id)
    {
        if (auth()->user()->can('Delete Becomeing Sovereigns')) {
            $file = File::find($id);
            if ($file) {
                $file->delete();
                // delete file from storage
                Storage::disk('public')->delete($file->file);
                return redirect()->route('becoming-sovereign.index')->with('message', 'File deleted successfully.');
            } else {
                return redirect()->route('becoming-sovereign.index')->with('error', 'File not found.');
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
                ->where('user_id', auth()->id())
                ->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhere('file_name', 'like', '%' . $query . '%')
                        ->orWhere('file_extension', 'like', '%' . $query . '%');
                })
                ->where('type', 'Becoming Sovereign')
                ->orderBy($sort_by, $sort_type)
                ->paginate(15);

            return response()->json(['data' => view('user.becoming-sovereign.table', compact('files'))->render()]);
        }
    }

    public function edit($id)
    {
        if (auth()->user()->can('Edit Becomeing Sovereigns')) {
             $file = File::findOrFail($id);
            if ($file) {
                return view('user.becoming-sovereign.edit')->with('file', $file);
            } else {
                return redirect()->route('becoming-sovereign.index')->with('error', 'File not found.');
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|max:2048',
        ]);

        $file = File::findOrFail($id);
        if ($file) {
            $file_name = $request->file('file')->getClientOriginalName();
            $file_extension = $request->file('file')->getClientOriginalExtension();
            $file_upload = $this->imageUpload($request->file('file'), 'files');
            // if (is_array($file_name)) {
            //     return "dsa";
            // }
            $file->file_name = $file_name;
            $file->file_extension = $file_extension;
            $file->file = $file_upload;
            $file->save();

            return redirect()->route('becoming-sovereign.index')->with('message', 'File updated successfully.');
        } else {
            return redirect()->route('becoming-sovereign.index')->with('error', 'File not found.');
        }
    }

    public function view($id)
    {
        if (auth()->user()->can('View Becomeing Sovereigns')){
            $file = File::findOrFail($id);
            if ($file) {
                return view('user.becoming-sovereign.view')->with('file', $file);
            } else {
                return redirect()->route('becoming-sovereign.index')->with('error', 'File not found.');
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
