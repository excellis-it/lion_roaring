<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Strategy;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class StrategyController extends Controller
{
    use ImageTrait;

    public function index()
    {
        if (auth()->user()->can('Manage Strategy')) {
            $user = auth()->user();
            $user_type = $user->user_type;
            $user_country = $user->country;

            if (!$user->hasNewRole('SUPER ADMIN')) {
                $currentCountry = Country::findByCurrentRequest();
                $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

                if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
                    $strategies = Strategy::orderBy('id', 'desc')->whereHas('country', function ($query) {
                        $query->where('code', 'GL');
                    })->whereHas('user', function ($query) {
                        $query->whereIn('user_type', ['Global', 'G_R']);
                    })->paginate(15);
                } else {
                    $strategiesQuery = Strategy::where('country_id', $user_country)->orderBy('id', 'desc')->whereHas('user', function ($query) {
                        $query->whereIn('user_type', ['Regional', 'G_R']);
                    });

                    if ($user->is_ecclesia_admin == 1) {
                        $manage_ecclesia_ids = is_array($user->manage_ecclesia)
                            ? $user->manage_ecclesia
                            : explode(',', $user->manage_ecclesia ?? '');
                        $strategiesQuery->where(function ($q) use ($manage_ecclesia_ids, $user) {
                            $q->whereHas('user', function ($uq) use ($manage_ecclesia_ids) {
                                $uq->whereIn('ecclesia_id', $manage_ecclesia_ids);
                            })->orWhere('user_id', $user->id);
                        });
                    }
                    $strategies = $strategiesQuery->paginate(15);
                }
            } else {
                $strategies = Strategy::orderBy('id', 'desc')->paginate(15);
            }
            return view('user.strategy.list')->with(compact('strategies'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function upload()
    {
        if (auth()->user()->can('Upload Strategy')) {
            $countries = Country::orderBy('name', 'asc')->get();
            return view('user.strategy.upload')->with(compact('countries'));
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

        $country_id = $user->hasNewRole('SUPER ADMIN') ? $request->country_id : $country_id_ex;

        $request->merge(['country_id' => $country_id]);
        $validated = Validator::make($request->all(), [
            'file' => 'required|array', // Ensure strategy is an array
            'file.*' => 'required', // Ensure each strategy is valid
            'country_id' => 'required',
        ]);

        // Check if validation fails
        if ($validated->fails()) {
            return redirect()->back()->withErrors($validated)->withInput();
        }

        // Loop through each strategy and process it
        foreach ($request->file('file') as $file) {
            $file_name = $file->getClientOriginalName();
            $file_extension = $file->getClientOriginalExtension();
            $file_path = $this->imageUpload($file, 'strategies');

            // Check if a strategy with the same name and extension already exists for the user
            $check = Strategy::where('file_name', $file_name)
                ->where('file_extension', $file_extension)
                ->first();

            // Return validation error if strategy already exists
            if ($check) {
                return redirect()->back()->withErrors(['file' => 'The strategy name "' . $file_name . '" has already been taken.'])->withInput();
            }

            // Save the new strategy details to the database
            $file_upload = new Strategy();
            $file_upload->user_id = auth()->id();
            $file_upload->file_name = $file_name;
            $file_upload->file_extension = $file_extension;
            $file_upload->file = $file_path;
            $file_upload->country_id = $country_id;
            $file_upload->save();
        }

        // notify users
        $userName = Auth::user()->getFullNameAttribute();
        $noti = NotificationService::notifyAllUsers('New Strategy created by ' . $userName, 'strategy');

        // Redirect with success message
        return redirect()->route('strategy.index')->with('message', 'Strategy(s) uploaded successfully.');
    }

    public function delete($id)
    {
        $strategy = Strategy::find($id);
        if ((auth()->user()->can('Delete Strategy') && $strategy->user_id == auth()->user()->id) ||
            auth()->user()->hasNewRole('SUPER ADMIN')
        ) {

            Log::info($strategy->id . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
            if ($strategy) {

                // delete strategy from storage
                // Storage::disk('public')->delete($strategy->file);
                // delete strategy from storage folder if exists
                if (Storage::disk('public')->exists($strategy->file)) {
                    Storage::disk('public')->delete($strategy->file);
                }
                $strategy->delete();

                return redirect()->route('strategy.index')->with('message', 'Strategy deleted successfully.');
            } else {
                return redirect()->route('strategy.index')->with('error', 'Strategy not found.');
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function download($id)
    {
        if (auth()->user()->can('Download Strategy')) {
            $user = auth()->user();
            $user_type = $user->user_type;
            $user_country = $user->country;

            if (!$user->hasNewRole('SUPER ADMIN')) {
                $currentCountry = Country::findByCurrentRequest();
                $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

                if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
                    $strategy = Strategy::whereHas('country', function ($query) {
                        $query->where('code', 'GL');
                    })->where('id', $id)->first();
                } else {
                    $strategy = Strategy::where('country_id', $user_country)->where('id', $id)->first();
                }
            } else {
                $strategy = Strategy::where('id', $id)->first();
            }

            if ($strategy) {
                $filePath = Storage::disk('public')->path($strategy->file); // ensure using 'public' disk
                if (file_exists($filePath)) {
                    return response()->download($filePath, $strategy->file_name);
                } else {
                    return redirect()->route('strategy.index')->with('error', 'Strategy not found.');
                }
            } else {
                return redirect()->route('strategy.index')->with('error', 'Strategy not found.');
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function view($id)
    {
        if (auth()->user()->can('View Strategy')) {
            $user = auth()->user();
            $user_type = $user->user_type;
            $user_country = $user->country;

            if (!$user->hasNewRole('SUPER ADMIN')) {
                $currentCountry = Country::findByCurrentRequest();
                $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

                if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
                    $strategy = Strategy::whereHas('country', function ($query) {
                        $query->where('code', 'GL');
                    })->where('id', $id)->first();
                } else {
                    $strategy = Strategy::where('country_id', $user_country)->where('id', $id)->first();
                }
            } else {
                $strategy = Strategy::where('id', $id)->first();
            }

            if ($strategy) {
                return view('user.strategy.view')->with('strategy', $strategy);
            } else {
                return redirect()->route('strategy.index')->with('error', 'File not found.');
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

            $strategies = Strategy::query()
                ->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhere('file_name', 'like', '%' . $query . '%')
                        ->orWhere('file_extension', 'like', '%' . $query . '%')
                        ->orWhereHas('country', function ($q) use ($query) {
                            $q->where('name', 'like', '%' . $query . '%');
                        });
                });
            if ($request->topic_id) {
                $strategies->whereHas('topic', function ($q) use ($request) {
                    $q->where('id', $request->topic_id);
                });
            }
            if ($request->type) {
                $strategies->where('type', $request->type);
            }

            $user = auth()->user();
            $user_type = $user->user_type;
            $user_country = $user->country;

            if (!$user->hasNewRole('SUPER ADMIN')) {
                $currentCountry = Country::findByCurrentRequest();
                $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

                if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
                    $strategies->whereHas('country', function ($query) {
                        $query->where('code', 'GL');
                    })->whereHas('user', function ($query) {
                        $query->whereIn('user_type', ['Global', 'G_R']);
                    });
                } else {
                    $strategies->where('country_id', $user_country)->whereHas('user', function ($query) {
                        $query->whereIn('user_type', ['Regional', 'G_R']);
                    });

                    if ($user->is_ecclesia_admin == 1) {
                        $manage_ecclesia_ids = is_array($user->manage_ecclesia)
                            ? $user->manage_ecclesia
                            : explode(',', $user->manage_ecclesia ?? '');
                        $strategies->where(function ($q) use ($manage_ecclesia_ids, $user) {
                            $q->whereHas('user', function ($uq) use ($manage_ecclesia_ids) {
                                $uq->whereIn('ecclesia_id', $manage_ecclesia_ids);
                            })->orWhere('user_id', $user->id);
                        });
                    }
                }
            }

            $strategies = $strategies->orderBy($sort_by, $sort_type)
                ->paginate(15);

            return response()->json(['data' => view('user.strategy.table', compact('strategies'))->render()]);
        }
    }
}
