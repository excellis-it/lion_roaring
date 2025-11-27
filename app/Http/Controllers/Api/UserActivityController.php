<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\UserActivity;

/**
 * @group User Activity
 */
class UserActivityController extends Controller
{
    protected $successStatus = 200;

    /**
     * Log user activity entry
     * @authenticated
     *
     * @bodyParam activity_description string optional Activity description. Example: Clicked view button
     * @bodyParam permission_access string optional Permission applied to the request. Example: Manage Bulletin
     * @bodyParam device_mac string optional Device mac if available
     * @bodyParam user_id int optional User id if logging on behalf of another user
     *
     * @response 200 {
     * "status": true,
     * "message": "Activity logged",
     * "data": {"id": 1}
     * }
     * @response 201 {
     * "status": false,
     * "message": "Validation failed"
     * }
     */
    public function log(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'activity_description' => 'nullable|string|max:500',
                'permission_access' => 'nullable|string|max:255',
                'device_mac' => 'nullable|string|max:255',
                'user_id' => 'nullable|integer|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->first()], 201);
            }

            // Use model helper to create activity record
            $data = $request->only(['activity_description', 'permission_access', 'device_mac', 'user_id']);
            UserActivity::logActivity($data);

            return response()->json(['status' => true, 'message' => 'Activity logged'], $this->successStatus);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 201);
        }
    }

    /**
     * List user activities with filters and pagination
     * @authenticated
     *
     * @bodyParam user_name string optional Filter by user name
     * @bodyParam email string optional Filter by email
     * @bodyParam user_roles string optional Filter by roles
     * @bodyParam country_name string optional Filter by country
     * @bodyParam activity_type string optional Filter by activity type
     * @bodyParam date_from date optional Filter activities from date
     * @bodyParam date_to date optional Filter activities to date
     * @bodyParam per_page int optional Number of results per page. Default 10
     *
     * @response 200 {
     *  "status": true,
     *  "message": "Activities list fetched",
     *  "data": {"current_page":1, "data":[]}
     * }
     * @response 403 {
     *  "status": false,
     *  "message": "Unauthorized"
     * }
     */
    public function list(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user?->can('Manage User Activity')) {
                return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
            }

            $query = UserActivity::query();

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

            $perPage = $request->get('per_page', 10);
            $activities = $query->orderBy('id', 'desc')->paginate($perPage);

            return response()->json(['status' => true, 'message' => 'Activities list fetched', 'data' => $activities], $this->successStatus);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 201);
        }
    }

    /**
     * Get paginated statistics by country
     * @authenticated
     *
     * @bodyParam per_page int optional Number of results per page. Default 10
     *
     * @response 200 {
     *  "status": true,
     *  "message": "Activities by country fetched",
     *  "data": {"current_page":1, "data":[]}
     * }
     */
    public function byCountry(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user?->can('Manage User Activity')) {
                return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
            }

            $perPage = $request->get('per_page', 10);
            $data = UserActivity::selectRaw('country_name, COUNT(*) as count')
                ->groupBy('country_name')
                ->having('count', '>', 0)
                ->orderBy('count', 'desc')
                ->paginate($perPage);

            return response()->json(['status' => true, 'message' => 'Activities by country fetched', 'data' => $data], $this->successStatus);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 201);
        }
    }

    /**
     * Get paginated statistics by user
     * @authenticated
     *
     * @bodyParam per_page int optional Number of results per page. Default 10
     *
     * @response 200 {
     *  "status": true,
     *  "message": "Activities by user fetched",
     *  "data": {"current_page":1, "data":[]}
     * }
     */
    public function byUser(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user?->can('Manage User Activity')) {
                return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
            }

            $perPage = $request->get('per_page', 10);
            $data = UserActivity::selectRaw('user_name, email, COUNT(*) as count')
                ->whereNotNull('user_id')
                ->groupBy('user_name', 'email')
                ->having('count', '>', 0)
                ->orderBy('count', 'desc')
                ->paginate($perPage);

            return response()->json(['status' => true, 'message' => 'Activities by user fetched', 'data' => $data], $this->successStatus);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 201);
        }
    }

    /**
     * Get paginated statistics by activity type
     * @authenticated
     *
     * @bodyParam per_page int optional Number of results per page. Default 10
     *
     * @response 200 {
     *  "status": true,
     *  "message": "Activities by type fetched",
     *  "data": {"current_page":1, "data":[]}
     * }
     */
    public function byType(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user?->can('Manage User Activity')) {
                return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
            }

            $perPage = $request->get('per_page', 10);
            $data = UserActivity::selectRaw('activity_type, COUNT(*) as count')
                ->groupBy('activity_type')
                ->having('count', '>', 0)
                ->orderBy('count', 'desc')
                ->paginate($perPage);

            return response()->json(['status' => true, 'message' => 'Activities by type fetched', 'data' => $data], $this->successStatus);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 201);
        }
    }
}
