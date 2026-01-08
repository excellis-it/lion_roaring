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
            $user_type = auth()->user()->user_type;
            $user_country = auth()->user()->country;

            if ($user_type == 'Global') {
                $strategies = Strategy::orderBy('id', 'desc')->paginate(15);
            } else {
                $strategies = Strategy::where('country_id', $user_country)->orderBy('id', 'desc')->paginate(15);
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
        $country_id = auth()->user()->user_type === 'Global'
            ? $request->country_id
            : auth()->user()->country;

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
            $strategy = Strategy::where('id', $id)->first();
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
            $user_type = auth()->user()->user_type;
            $user_country = auth()->user()->country;

            if ($user_type == 'Global') {
                $strategy = Strategy::findOrFail($id);
            } else {
                $strategy = Strategy::where('country_id', $user_country)->findOrFail($id);
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
            $user_type = auth()->user()->user_type;
            $country_name = auth()->user()->country;
            if ($user_type == 'Regional') {
                $strategies->where('country_name', $country_name);
            }

            $strategies = $strategies->orderBy($sort_by, $sort_type)
                ->paginate(15);

            return response()->json(['data' => view('user.strategy.table', compact('strategies'))->render()]);
        }
    }
}
