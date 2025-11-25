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
            // Calculate statistics for dashboard
            $stats = [
                'total_activities' => UserActivity::count(),
                'activities_by_country_count' => UserActivity::selectRaw('country_name, COUNT(*) as count')
                    ->groupBy('country_name')
                    ->having('count', '>', 0)
                    ->count(),
                'activities_by_user_count' => UserActivity::selectRaw('user_name, email, COUNT(*) as count')
                    ->whereNotNull('user_id')
                    ->groupBy('user_name', 'email')
                    ->having('count', '>', 0)
                    ->count(),
                'activities_by_type_count' => UserActivity::selectRaw('activity_type, COUNT(*) as count')
                    ->groupBy('activity_type')
                    ->having('count', '>', 0)
                    ->count(),
            ];

            // Get unique values for filters (dashboard may show filter counts, but filters are used by list page)
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

            return view('user.user-activity.dashboard', compact('stats', 'filters'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Display the activity list page (separate from dashboard).
     */
    public function listPage(Request $request)
    {
        if (Auth::user()->can('Manage User Activity')) {
            // Provide filters for the list page dropdowns
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

            return view('user.user-activity.list', compact('filters'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Get paginated activities via AJAX
     */
    public function getActivities(Request $request)
    {
        if (!Auth::user()->can('Manage User Activity')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Debug log to see what filters are received
        Log::info('User Activity Filters Received:', $request->all());

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

        $activities = $query->orderBy('id', 'desc')->paginate($request->get('per_page', 10));

        // Debug log to see query results
        Log::info('Activities Query Result Count:', ['count' => $activities->total()]);

        return response()->json($activities);
    }

    /**
     * Get paginated statistics by country
     */
    public function getActivitiesByCountry(Request $request)
    {
        if (!Auth::user()->can('Manage User Activity')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = UserActivity::selectRaw('country_name, COUNT(*) as count')
            ->groupBy('country_name')
            ->having('count', '>', 0)
            ->orderBy('count', 'desc')
            ->paginate($request->get('per_page', 10));

        return response()->json($data);
    }

    /**
     * Get paginated statistics by user
     */
    public function getActivitiesByUser(Request $request)
    {
        if (!Auth::user()->can('Manage User Activity')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = UserActivity::selectRaw('user_name, email, COUNT(*) as count')
            ->whereNotNull('user_id')
            ->groupBy('user_name', 'email')
            ->having('count', '>', 0)
            ->orderBy('count', 'desc')
            ->paginate($request->get('per_page', 10));

        return response()->json($data);
    }

    /**
     * Get paginated statistics by activity type
     */
    public function getActivitiesByType(Request $request)
    {
        if (!Auth::user()->can('Manage User Activity')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = UserActivity::selectRaw('activity_type, COUNT(*) as count')
            ->groupBy('activity_type')
            ->having('count', '>', 0)
            ->orderBy('count', 'desc')
            ->paginate($request->get('per_page', 10));

        return response()->json($data);
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
