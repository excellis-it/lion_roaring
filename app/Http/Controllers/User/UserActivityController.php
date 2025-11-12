<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Services\NotificationService;


class UserActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::user()->can('Manage User Activity')) {
            $query = UserActivity::query();

            // Apply filters
            if ($request->filled('user_name')) {
                $query->where('user_name', 'like', '%' . $request->user_name . '%');
            }
            if ($request->filled('email')) {
                $query->where('email', 'like', '%' . $request->email . '%');
            }
            if ($request->filled('user_roles')) {
                $query->where('user_roles', 'like', '%' . $request->user_roles . '%');
            }
            if ($request->filled('country_name')) {
                $query->where('country_name', $request->country_name);
            }
            if ($request->filled('activity_type')) {
                $query->where('activity_type', $request->activity_type);
            }
            if ($request->filled('date_from')) {
                $query->whereDate('activity_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('activity_date', '<=', $request->date_to);
            }

            $activities = $query->orderBy('id', 'desc')->paginate(10);

            // Calculate statistics
            $stats = [
                'total_activities' => UserActivity::count(),
                'activities_by_country' => UserActivity::selectRaw('country_name, COUNT(*) as count')
                    ->groupBy('country_name')
                    ->having('count', '>', 0)
                    ->orderBy('count', 'desc')
                    ->get(),
                'activities_by_user' => UserActivity::selectRaw('user_name, email, COUNT(*) as count')
                    ->whereNotNull('user_id')
                    ->groupBy('user_name', 'email')
                    ->having('count', '>', 0)
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
                'activities_by_type' => UserActivity::selectRaw('activity_type, COUNT(*) as count')
                    ->groupBy('activity_type')
                    ->having('count', '>', 0)
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
            ];

            // Get unique values for filters
            $filters = [
                'countries' => UserActivity::selectRaw('DISTINCT country_name')
                    ->whereNotNull('country_name')
                    ->where('country_name', '!=', '')
                    ->orderBy('country_name')
                    ->pluck('country_name'),
                'activity_types' => UserActivity::selectRaw('DISTINCT activity_type')
                    ->whereNotNull('activity_type')
                    ->where('activity_type', '!=', '')
                    ->orderBy('activity_type')
                    ->pluck('activity_type'),
                'roles' => UserActivity::selectRaw('DISTINCT user_roles')
                    ->whereNotNull('user_roles')
                    ->where('user_roles', '!=', '-')
                    ->orderBy('user_roles')
                    ->pluck('user_roles'),
            ];

             return $stats;

            return view('user.user-activity.list', compact('activities', 'stats', 'filters'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->can('Create User Activity')) {
            return view('user.user-activity.create');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function delete($id)
    {
        if (Auth::user()->can('Delete User Activity')) {
            $activity = UserActivity::findOrFail(Crypt::decrypt($id));
            Log::info($activity->user_name . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
            $activity->delete();
            return redirect()->route('user-activity.index')->with('message', 'User Activity deleted successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
