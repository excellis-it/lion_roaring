<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Country;
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
            $user = auth()->user();
            $user_type = $user->user_type;
            $user_country = $user->country;

            if (!$user->hasNewRole('SUPER ADMIN')) {
                $currentCountry = Country::findByCurrentRequest();
                $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

                if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
                    $filesQuery = File::orderBy('id', 'desc')->where('type', 'Becoming a Leader')
                        ->whereHas('country', function ($query) {
                            $query->where('code', 'GL');
                        })
                        ->whereHas('user', function ($query) {
                            $query->whereIn('user_type', ['Global', 'G_R']);
                        });
                    if (isset($request->topic)) {
                        $new_topic = $request->topic;
                        $files = $filesQuery->where('topic_id', $request->topic)->paginate(15);
                    } else {
                        $files = $filesQuery->paginate(15);
                        $new_topic = '';
                    }
                    $topics = Topic::orderBy('topic_name', 'asc')->where('education_type', 'Becoming a Leader')->whereHas('country', function ($query) {
                        $query->where('code', 'GL');
                    })->get();
                } else {
                    $filesQuery = File::orderBy('id', 'desc')->where('type', 'Becoming a Leader')
                        ->where('country_id', $user_country)
                        ->whereHas('user', function ($query) {
                            $query->whereIn('user_type', ['Regional', 'G_R']);
                        });

                    // Ecclesia filtering
                    if ($user->is_ecclesia_admin == 1) {
                        $manage_ecclesia_ids = is_array($user->manage_ecclesia)
                            ? $user->manage_ecclesia
                            : explode(',', $user->manage_ecclesia ?? '');
                        $filesQuery->where(function ($q) use ($manage_ecclesia_ids, $user) {
                            $q->whereHas('user', function ($uq) use ($manage_ecclesia_ids) {
                                $uq->whereIn('ecclesia_id', $manage_ecclesia_ids);
                            })->orWhere('user_id', $user->id);
                        });
                    }

                    if (isset($request->topic)) {
                        $new_topic = $request->topic;
                        $files = $filesQuery->where('topic_id', $request->topic)->paginate(15);
                    } else {
                        $files = $filesQuery->paginate(15);
                        $new_topic = '';
                    }
                    $topics = Topic::orderBy('topic_name', 'asc')->where('education_type', 'Becoming a Leader')->where('country_id', $user_country)->get();
                }
            } else {
                if (isset($request->topic)) {
                    $new_topic = $request->topic;
                    $files = File::orderBy('id', 'desc')->where('type', 'Becoming a Leader')->where('topic_id', $request->topic)->paginate(15);
                } else {
                    $files = File::orderBy('id', 'desc')->where('type', 'Becoming a Leader')->paginate(15);
                    $new_topic = '';
                }
                $topics = Topic::orderBy('topic_name', 'asc')->where('education_type', 'Becoming a Leader')->get();
            }
            return view('user.leadership-development.list')->with(compact('files', 'topics', 'new_topic'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function upload()
    {
        if (auth()->user()->can('Upload Becoming a Leader')) {
            $user = auth()->user();
            $user_type = $user->user_type;
            $user_country = $user->country;

            if (!$user->hasNewRole('SUPER ADMIN')) {
                $currentCountry = Country::findByCurrentRequest();
                $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

                if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
                    $topics = Topic::orderBy('topic_name', 'asc')->where('education_type', 'Becoming a Leader')->whereHas('country', function ($query) {
                        $query->where('code', 'GL');
                    })->get();
                } else {
                    $topics = Topic::orderBy('topic_name', 'asc')->where('education_type', 'Becoming a Leader')->where('country_id', $user_country)->get();
                }
            } else {
                $topics = Topic::orderBy('topic_name', 'asc')->where('education_type', 'Becoming a Leader')->get();
            }
            $countries = Country::orderBy('name', 'asc')->get();
            return view('user.leadership-development.upload')->with(compact('topics', 'countries'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $user_type = $user->user_type;
        $user_country = $user->country;
        $country_id_ex = null;
        $currentCountry = Country::findByCurrentRequest();
        $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

        if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
            $country = Country::where('code', 'GL')->first();
            $country_id_ex = $country->id;
        } else {
            $country_id_ex = $user_country;
        }

        $country_id = auth()->user()->hasNewRole('SUPER ADMIN') ? $request->country_id : $country_id_ex;

        $request->merge(['country_id' => $country_id]);

        $valdate = $request->validate([
            'file' => 'required|file',
            'topic_id' => 'required|exists:topics,id', // 'exists' checks if the value exists in the 'topics' table 'id' column
            'country_id' => 'required|exists:countries,id',
        ]);

        $file_name = $request->file('file')->getClientOriginalName();
        $file_extension = $request->file('file')->getClientOriginalExtension();
        $file_upload = $this->imageUpload($request->file('file'), 'files');

        if (!$user->hasNewRole('SUPER ADMIN')) {
            if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
                $check = File::where('file_name', $file_name)->where('file_extension', $file_extension)->whereHas('country', function ($query) {
                    $query->where('code', 'GL');
                })->first();
            } else {
                $check = File::where('file_name', $file_name)->where('file_extension', $file_extension)->where('country_id', $country_id)->first();
            }
        } else {
            $check = File::where('file_name', $file_name)->where('file_extension', $file_extension)->first();
        }

        // get the same name validation error
        if ($check) {
            return redirect()->back()->withErrors(['file' => 'The file name has already been taken.'])->withInput();
        }

        $file = new File();
        $file->user_id = auth()->id();
        $file->file_name = $file_name;
        $file->file_extension = $file_extension;
        $file->topic_id = $request->topic_id;
        $file->country_id = $country_id;
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
                return redirect()->route('leadership-development.index', ['topic' =>  $new_topic])->with('message', 'File deleted successfully.');
            } else {
                return redirect()->route('leadership-development.index', ['topic' =>  $new_topic])->with('error', 'File not found.');
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
                        ->orWhere('file_extension', 'like', '%' . $query . '%')
                        ->orWhereHas('country', function ($q) use ($query) {
                            $q->where('name', 'like', '%' . $query . '%');
                        });
                });

            if ($request->topic_id) {
                $files->whereHas('topic', function ($q) use ($request) {
                    $q->where('id', $request->topic_id);
                });
                $new_topic = $request->topic_id;
            } else {
                $new_topic = '';
            }

            $user = auth()->user();
            $user_type = $user->user_type;
            $user_country = $user->country;

            if (!$user->hasNewRole('SUPER ADMIN')) {
                $currentCountry = Country::findByCurrentRequest();
                $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

                if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
                    $files->whereHas('country', function ($query) {
                        $query->where('code', 'GL');
                    })->whereHas('user', function ($query) {
                        $query->whereIn('user_type', ['Global', 'G_R']);
                    });
                } else {
                    $files->where('country_id', $user_country)
                        ->whereHas('user', function ($query) {
                            $query->whereIn('user_type', ['Regional', 'G_R']);
                        });

                    // Ecclesia filtering
                    if ($user->is_ecclesia_admin == 1) {
                        $manage_ecclesia_ids = is_array($user->manage_ecclesia)
                            ? $user->manage_ecclesia
                            : explode(',', $user->manage_ecclesia ?? '');
                        $files->where(function ($q) use ($manage_ecclesia_ids, $user) {
                            $q->whereHas('user', function ($uq) use ($manage_ecclesia_ids) {
                                $uq->whereIn('ecclesia_id', $manage_ecclesia_ids);
                            })->orWhere('user_id', $user->id);
                        });
                    }
                }
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
            $user = auth()->user();
            $user_type = $user->user_type;
            $user_country = $user->country;

            if (!$user->hasNewRole('SUPER ADMIN')) {
                $currentCountry = Country::findByCurrentRequest();
                $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

                if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
                    $file = File::whereHas('country', function ($query) {
                        $query->where('code', 'GL');
                    })->findOrFail($id);
                } else {
                    $file = File::where('country_id', $user_country)->findOrFail($id);
                }
            } else {
                $file = File::findOrFail($id);
            }

            if (isset($request->topic)) {
                $new_topic = $request->topic;
            } else {
                $new_topic = '';
            }

            if ($file) {
                if (!$user->hasNewRole('SUPER ADMIN')) {
                    if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
                        $topics = Topic::orderBy('topic_name', 'asc')->where('education_type', 'Becoming a Leader')->whereHas('country', function ($query) {
                            $query->where('code', 'GL');
                        })->get();
                    } else {
                        $topics = Topic::orderBy('topic_name', 'asc')->where('education_type', 'Becoming a Leader')->where('country_id', $user_country)->get();
                    }
                } else {
                    $topics = Topic::orderBy('topic_name', 'asc')->where('education_type', 'Becoming a Leader')->get();
                }
                $countries = Country::orderBy('name', 'asc')->get();
                return view('user.leadership-development.edit')->with(compact('file', 'topics', 'new_topic', 'countries'));
            } else {
                return redirect()->route('leadership-development.index')->with('error', 'File not found.');
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $user_type = $user->user_type;
        $user_country = $user->country;

        $country_id_ex = null;
        $currentCountry = Country::findByCurrentRequest();
        $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

        if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
            $country = Country::where('code', 'GL')->first();
            $country_id_ex = $country->id;
        } else {
            $country_id_ex = $user_country;
        }

        $country_id = auth()->user()->hasNewRole('SUPER ADMIN') ? $request->country_id : $country_id_ex;

        $request->merge(['country_id' => $country_id]);

        $validated = $request->validate([
            'topic_id' => 'required|exists:topics,id', // 'exists' checks if the value exists in the 'topics' table 'id' column
            'country_id' => 'required|exists:countries,id',
        ]);

        $user_type = auth()->user()->user_type;
        if (!$user->hasNewRole('SUPER ADMIN')) {
            if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
                $file = File::whereHas('country', function ($query) {
                    $query->where('code', 'GL');
                })->findOrFail($id);
            } else {
                $file = File::where('country_id', $user_country)->findOrFail($id);
            }
        } else {
            $file = File::findOrFail($id);
        }

        if ($request->hasFile('file')) {
            $file_name = $request->file('file')->getClientOriginalName();
            $file_extension = $request->file('file')->getClientOriginalExtension();
            $file_upload = $this->imageUpload($request->file('file'), 'files');

            if (!$user->hasNewRole('SUPER ADMIN')) {
                if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
                    $check = File::where('file_name', $file_name)->where('file_extension', $file_extension)->whereHas('country', function ($query) {
                        $query->where('code', 'GL');
                    })->first();
                } else {
                    $check = File::where('file_name', $file_name)->where('file_extension', $file_extension)->where('country_id', $country_id)->first();
                }
            } else {
                $check = File::where('file_name', $file_name)->where('file_extension', $file_extension)->first();
            }
            if ($check) {
                return redirect()->back()->withErrors(['file' => 'The file name has already been taken.'])->withInput();
            }
            $file->file_name = $file_name;
            $file->file_extension = $file_extension;
            $file->file = $file_upload;
        }
        $file->type = 'Becoming a Leader';
        $file->topic_id = $request->topic_id;
        $file->country_id = $country_id;
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
