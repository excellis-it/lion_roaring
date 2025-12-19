<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\File;
use App\Models\Topic;
use App\Traits\ImageTrait;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class FileController extends Controller
{
    use ImageTrait;

    public function index()
    {
        if (auth()->user()->can('Manage File')) {
            $user_type = auth()->user()->user_type;
            $user_country = auth()->user()->country;

            if ($user_type == 'Global') {
                $files = File::orderBy('id', 'desc')->paginate(15);
                $topics = Topic::orderBy('topic_name', 'asc')->get();
            } else {
                $files = File::orderBy('id', 'desc')->where('country_id', $user_country)->paginate(15);
                $topics = Topic::orderBy('topic_name', 'asc')->get();
            }

            return view('user.file.list')->with(compact('files', 'topics'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function upload()
    {
        if (auth()->user()->can('Upload File')) {
            $user_type = auth()->user()->user_type;
            $user_country = auth()->user()->country;

            if ($user_type == 'Global') {
                $topics = Topic::orderBy('topic_name', 'asc')->get();
            } else {
                $topics = Topic::orderBy('topic_name', 'asc')->where('country_id', $user_country)->get();
            }
            $countries = Country::orderBy('name', 'asc')->get();
            return view('user.file.upload')->with(compact('topics', 'countries'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function store(Request $request)
    {
        $country_id = auth()->user()->user_type === 'Global'
            ? $request->country_id
            : auth()->user()->country;

        $request->merge(['country_id' => $country_id]);
        // return $request->type;
        $validated = FacadesValidator::make($request->all(), [
            'topic_id' => 'required|exists:topics,id', // 'exists' checks if the value exists in the 'topics' table 'id' column
            'file' => 'required|array', // Ensure file is an array
            'file.*' => 'required|file', // Ensure each file is valid
            'type' => 'required',
            'country_id' => 'required|exists:countries,id',
        ]);

        // Check if validation fails
        if ($validated->fails()) {
            return redirect()->back()->withErrors($validated)->withInput();
        }

        // Loop through each file and process it
        foreach ($request->file('file') as $file) {
            $file_name = $file->getClientOriginalName();
            $file_extension = $file->getClientOriginalExtension();
            $file_path = $this->imageUpload($file, 'files');

            // Check if a file with the same name and extension already exists for the user
            $user_type = auth()->user()->user_type;
            $user_country = auth()->user()->country;
            if ($user_type == 'Global') {
                $check = File::where('file_name', $file_name)
                    ->where('file_extension', $file_extension)
                    ->first();
            } else {
                $check = File::where('file_name', $file_name)
                    ->where('file_extension', $file_extension)
                    ->where('country_id', $user_country)
                    ->first();
            }

            // Return validation error if file already exists
            if ($check) {
                return redirect()->back()->withErrors(['file' => 'The file name "' . $file_name . '" has already been taken.'])->withInput();
            }

            // Save the new file details to the database
            $file_upload = new File();
            $file_upload->user_id = auth()->id();
            $file_upload->file_name = $file_name;
            $file_upload->file_extension = $file_extension;
            $file_upload->topic_id = $request->topic_id;
            $file_upload->type = $request->type;
            $file_upload->country_id = $request->country_id;
            $file_upload->file = $file_path;
            $file_upload->save();
        }

        $userName = Auth::user()->getFullNameAttribute();
        $noti = NotificationService::notifyAllUsers('New File created by ' . $userName, 'file');

        // Redirect with success message
        return redirect()->route('file.index')->with('message', 'File(s) uploaded successfully.');
    }

    public function delete($id)
    {
        if (auth()->user()->can('Delete File')) {
            $file = File::find($id);
            if ($file) {
                Log::info($file->file_name . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
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
                return response()->download($filePath, $file->file_name);
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
                ->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhere('file_name', 'like', '%' . $query . '%')
                        ->orWhere('file_extension', 'like', '%' . $query . '%')
                        ->orWhereHas('country', function ($q) use ($query) {
                            $q->where('name', 'like', '%' . $query . '%');
                        });
                });
            if ($request->topic_id) {
                $files->whereHas('topic', function ($q) use ($request) {
                    $q->where('id', $request->topic_id);
                });
            }
            if ($request->type) {
                $files->where('type', $request->type);
            }

            $user_type = auth()->user()->user_type;
            $user_country = auth()->user()->country;

            if ($user_type == 'Regional') {
                $files->where('country_id', $user_country);
            }

            $files = $files->orderBy($sort_by, $sort_type)
                ->paginate(15);

            return response()->json(['data' => view('user.file.table', compact('files'))->render()]);
        }
    }

    public function edit($id)
    {
        if (auth()->user()->can('Edit File')) {
            $user_type = auth()->user()->user_type;
            $user_country = auth()->user()->country;

            if ($user_type == 'Global') {
                $file = File::findOrFail($id);
            } else {
                $file = File::where('country_id', $user_country)->findOrFail($id);
            }

            if ($file) {
                if ($user_type == 'Global') {
                    $countries = Country::orderBy('name', 'asc')->get();
                    $topics = Topic::orderBy('topic_name', 'asc')->get();
                } else {
                    $countries = Country::orderBy('name', 'asc')->where('id', $user_country)->get();
                    $topics = Topic::orderBy('topic_name', 'asc')->where('country_id', $user_country)->get();
                }

                return view('user.file.edit')->with(compact('file', 'topics', 'countries'));
            } else {
                return redirect()->route('file.index')->with('error', 'File not found.');
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function update(Request $request, $id)
    {
        $user_type = auth()->user()->user_type;
        $user_country = auth()->user()->country;

        $country_id = auth()->user()->user_type === 'Global'
            ? $request->country_id
            : auth()->user()->country;

        $request->merge(['country_id' => $country_id]);

        // Validate the request
        $validated = FacadesValidator::make($request->all(), [
            'topic_id' => 'required|exists:topics,id', // 'exists' checks if the value exists in the 'topics' table 'id' column
            'file' => 'nullable|file', // Ensure file validation if provided
            'type' => 'required',
        ]);

        // Check if validation fails
        if ($validated->fails()) {
            return redirect()->back()->withErrors($validated)->withInput();
        }

        // Find the file to update
        $file = File::findOrFail($id);

        // Handle file upload if a new file is provided
        if ($request->hasFile('file')) {
            $file_name = $request->file('file')->getClientOriginalName();
            $file_extension = $request->file('file')->getClientOriginalExtension();
            $file_upload = $this->imageUpload($request->file('file'), 'files');

            // Check if a file with the same name and extension already exists for the current user
            if ($user_type == 'Global') {
                $check = File::where('file_name', $file_name)
                    ->where('file_extension', $file_extension)
                    ->first();
            } else {
                $check = File::where('file_name', $file_name)
                    ->where('file_extension', $file_extension)
                    ->where('country_id', $user_country)
                    ->first();
            }

            // Return validation error if file already exists
            if ($check) {
                return redirect()->back()->withErrors(['file' => 'The file name has already been taken.'])->withInput();
            }

            // Update file details
            $file->file_name = $file_name;
            $file->file_extension = $file_extension;
            $file->file = $file_upload;
        }

        // Update other details
        $file->topic_id = $request->topic_id;
        $file->type = $request->type;
        $file->save();

        // Redirect with success message
        return redirect()->route('file.index')->with('message', 'File updated successfully.');
    }

    public function getTopics(Request $request, $type)
    {
        $user_type = auth()->user()->user_type;
        $user_country = auth()->user()->country;
        if ($user_type == 'Global') {
            $country_id = $request->country_id;
        } else {
            $country_id = $user_country;
        }
        $topics = Topic::where('education_type', $type)
            ->when($country_id, function ($q) use ($country_id, $user_type) {
                return $q->where('country_id', $country_id);
            })
            ->orderBy('topic_name', 'asc')->get();
        return response()->json(['data' => $topics]);
    }
}
