<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserActivity;
use App\Services\UserActivityExportService;
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
            $countryQuery = UserActivity::query()
                ->leftJoin('users', 'user_activities.user_id', '=', 'users.id')
                ->leftJoin('countries', 'users.country', '=', 'countries.id');

            $stats = [
                'total_activities' => UserActivity::count(),
                'activities_by_country_count' => (clone $countryQuery)
                    ->whereNotNull('countries.id')
                    ->selectRaw('COUNT(DISTINCT countries.id) as aggregate')
                    ->value('aggregate') ?? 0,
                'activities_by_user_count' => UserActivity::whereNotNull('user_id')
                    ->distinct('user_id')
                    ->count('user_id'),
                'activities_by_type_count' => UserActivity::selectRaw('activity_type, COUNT(*) as count')
                    ->groupBy('activity_type')
                    ->having('count', '>', 0)
                    ->count(),
                'visits_today' => UserActivity::where('activity_description', 'LIKE', 'Visited%')
                    ->whereDate('activity_date', now())
                    ->count(),
                'visits_month' => UserActivity::where('activity_description', 'LIKE', 'Visited%')
                    ->whereYear('activity_date', now()->year)
                    ->whereMonth('activity_date', now()->month)
                    ->count(),
                'visits_year' => UserActivity::where('activity_description', 'LIKE', 'Visited%')
                    ->whereYear('activity_date', now()->year)
                    ->count(),
                'logins_today' => UserActivity::where('activity_type', 'LIKE', 'LOGIN%')
                    ->whereDate('activity_date', now())
                    ->count(),
                'logins_month' => UserActivity::where('activity_type', 'LIKE', 'LOGIN%')
                    ->whereYear('activity_date', now()->year)
                    ->whereMonth('activity_date', now()->month)
                    ->count(),
                'logins_year' => UserActivity::where('activity_type', 'LIKE', 'LOGIN%')
                    ->whereYear('activity_date', now()->year)
                    ->count(),
            ];

            $filters = [
                'countries' => (clone $countryQuery)
                    ->whereNotNull('countries.id')
                    ->selectRaw('DISTINCT countries.name as country_name')
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
            $countryQuery = UserActivity::query()
                ->leftJoin('users', 'user_activities.user_id', '=', 'users.id')
                ->leftJoin('countries', 'users.country', '=', 'countries.id');

            $filters = [
                'countries' => (clone $countryQuery)
                    ->whereNotNull('countries.id')
                    ->selectRaw('DISTINCT countries.name as country_name')
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

        $exportService = app(UserActivityExportService::class);

        $activities = $exportService->filteredQuery($request->all())
            ->paginate($request->get('per_page', 10));
        $activities->getCollection()->transform(function ($activity) use ($exportService) {
            return $exportService->mapActivityCountry($activity);
        });

        // Debug log to see query results
        Log::info('Activities Query Result Count:', ['count' => $activities->total()]);

        return response()->json($activities);
    }

    /**
     * Start a chunked CSV export of the filtered activity list.
     */
    public function exportStart(Request $request)
    {
        if (!Auth::user()->can('Manage User Activity')) {
            abort(403, 'You do not have permission to access this page.');
        }

        return response()->json(
            app(UserActivityExportService::class)->start(Auth::id(), $request->all())
        );
    }

    /**
     * Append the next chunk of rows to the export file.
     */
    public function exportChunk(Request $request)
    {
        if (!Auth::user()->can('Manage User Activity')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'export_id' => 'required|string',
        ]);

        return response()->json(
            app(UserActivityExportService::class)->processChunk(Auth::id(), $request->export_id)
        );
    }

    /**
     * Cancel an in-progress export and delete its temp file.
     */
    public function exportCancel(Request $request)
    {
        if (!Auth::user()->can('Manage User Activity')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'export_id' => 'required|string',
        ]);

        return response()->json(
            app(UserActivityExportService::class)->cancel(Auth::id(), $request->export_id)
        );
    }

    /**
     * Download a completed export.
     */
    public function exportDownload(string $exportId)
    {
        if (!Auth::user()->can('Manage User Activity')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $download = app(UserActivityExportService::class)->download(Auth::id(), $exportId);

        return response()->download($download['path'], $download['filename'], [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Get paginated statistics by country
     */
    public function getActivitiesByCountry(Request $request)
    {
        if (!Auth::user()->can('Manage User Activity')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = UserActivity::leftJoin('users', 'user_activities.user_id', '=', 'users.id')
            ->leftJoin('countries', 'users.country', '=', 'countries.id')
            ->selectRaw('countries.name as country_name, countries.code as country_code, COUNT(*) as count')
            ->whereNotNull('countries.id')
            ->groupBy('countries.name', 'countries.code')
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
     * Get active members list (distinct users with activity count)
     */
    public function getActiveMembers(Request $request)
    {
        if (!Auth::user()->can('Manage User Activity')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = UserActivity::leftJoin('users', 'user_activities.user_id', '=', 'users.id')
            ->leftJoin('countries', 'users.country', '=', 'countries.id')
            ->selectRaw('user_activities.user_id, MAX(user_activities.user_name) as user_name, MAX(user_activities.email) as email, MAX(countries.name) as country_name, MAX(countries.code) as country_code, MAX(user_activities.activity_date) as last_seen')
            ->whereNotNull('user_id')
            ->groupBy('user_activities.user_id');

        $data = $query->orderBy('last_seen', 'desc')->paginate($request->get('per_page', 10));

        return response()->json($data);
    }

    /**
     * Get active countries list (distinct countries with activity count)
     */
    public function getActiveCountries(Request $request)
    {
        if (!Auth::user()->can('Manage User Activity')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = UserActivity::leftJoin('users', 'user_activities.user_id', '=', 'users.id')
            ->leftJoin('countries', 'users.country', '=', 'countries.id')
            ->selectRaw('countries.name as country_name, countries.code as country_code, COUNT(DISTINCT user_activities.user_id) as member_count, MAX(user_activities.activity_date) as last_activity')
            ->whereNotNull('countries.id')
            ->groupBy('countries.name', 'countries.code');

        $data = $query->orderBy('member_count', 'desc')->paginate($request->get('per_page', 10));

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
