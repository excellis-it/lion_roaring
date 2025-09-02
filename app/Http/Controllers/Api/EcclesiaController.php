<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Ecclesia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * @group Ecclesia Management
 *
 * APIs for managing ecclesias (houses of worship)
 * @authenticated
 */
class EcclesiaController extends Controller
{
    /**
     * List all ecclesias
     *
     * Returns a paginated list of all ecclesias.
     *
     * @queryParam search string Search query for filtering ecclesias by name. Example: "First Church"
     * @queryParam sort_by string Field to sort results by (default: id). Example: "name"
     * @queryParam sort_type string Sort direction (asc or desc, default: asc). Example: "desc"
     * @queryParam page int Page number for pagination. Example: 1
     *
     * @response 200 {
     *   "ecclesias": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "name": "First Church of Christ",
     *         "country": "USA",
     *         "created_at": "2023-10-15T10:30:00.000000Z",
     *         "updated_at": "2023-10-15T10:30:00.000000Z",
     *         "country_name": {
     *           "id": 1,
     *           "name": "United States",
     *           "code": "US"
     *         }
     *       },
     *       {
     *         "id": 2,
     *         "name": "Grace Ecclesia",
     *         "country": "UK",
     *         "created_at": "2023-10-16T11:45:00.000000Z",
     *         "updated_at": "2023-10-16T11:45:00.000000Z",
     *         "country_name": {
     *           "id": 2,
     *           "name": "United Kingdom",
     *           "code": "UK"
     *         }
     *       }
     *     ],
     *     "first_page_url": "http://example.com/api/ecclesias?page=1",
     *     "from": 1,
     *     "last_page": 1,
     *     "last_page_url": "http://example.com/api/ecclesias?page=1",
     *     "links": [
     *       {
     *         "url": null,
     *         "label": "&laquo; Previous",
     *         "active": false
     *       },
     *       {
     *         "url": "http://example.com/api/ecclesias?page=1",
     *         "label": "1",
     *         "active": true
     *       },
     *       {
     *         "url": null,
     *         "label": "Next &raquo;",
     *         "active": false
     *       }
     *     ],
     *     "next_page_url": null,
     *     "path": "http://example.com/api/ecclesias",
     *     "per_page": 15,
     *     "prev_page_url": null,
     *     "to": 2,
     *     "total": 2
     *   }
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * @response 403 {
     *   "message": "You do not have permission to access this resource."
     * }
     */
    public function index(Request $request)
    {
        if (!Auth::user()->can('Manage Role Permission')) {
            return response()->json([
                'message' => 'You do not have permission to access this resource.'
            ], 403);
        }

        try {
            $sort_by = $request->get('sort_by', 'id');
            $sort_type = $request->get('sort_type', 'asc');
            $query = $request->get('search', '');
            $query = str_replace(" ", "%", $query);

            $ecclesias = Ecclesia::query()
                ->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhere('name', 'like', '%' . $query . '%');
                })
                ->orWhereHas('countryName', function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%');
                })
                ->orderBy($sort_by, $sort_type)
                ->get();

            return response()->json([
                'ecclesias' => $ecclesias
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch ecclesias.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get ecclesia creation data
     *
     * Returns the list of countries needed for creating a new ecclesia.
     *
     * @response 200 {
     *   "countries": [
     *     {
     *       "id": 1,
     *       "name": "United States",
     *       "code": "US"
     *     },
     *     {
     *       "id": 2,
     *       "name": "United Kingdom",
     *       "code": "UK"
     *     }
     *   ]
     * }
     * @response 403 {
     *   "message": "You do not have permission to access this resource."
     * }
     */
    public function create()
    {
        if (!Auth::user()->can('Manage Role Permission')) {
            return response()->json([
                'message' => 'You do not have permission to access this resource.'
            ], 403);
        }

        try {
            $countries = Country::orderBy('name', 'asc')->get();

            return response()->json([
                'countries' => $countries
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to load creation data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new ecclesia
     *
     * Stores a newly created ecclesia in the database.
     *
     * @bodyParam name string required The name of the ecclesia. Example: "Truth Ecclesia"
     * @bodyParam country int required The country ID of the ecclesia. Example: 1
     *
     * @response 201 {
     *   "message": "Ecclesia created successfully.",
     *   "ecclesia": {
     *     "name": "Truth Ecclesia",
     *     "country": "US",
     *     "updated_at": "2023-10-28T15:34:56.000000Z",
     *     "created_at": "2023-10-28T15:34:56.000000Z",
     *     "id": 3
     *   }
     * }
     * @response 422 {
     *   "message": "The name has already been taken."
     * }
     * @response 403 {
     *   "message": "You do not have permission to access this resource."
     * }
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('Manage Role Permission')) {
            return response()->json([
                'message' => 'You do not have permission to access this resource.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:ecclesias',
            'country' => 'required|int|exists:countries,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $ecclesia = new Ecclesia();
            $ecclesia->name = $request->name;
            $ecclesia->country = $request->country;
            $ecclesia->save();

            return response()->json([
                'message' => 'Ecclesia created successfully.',
                'ecclesia' => $ecclesia
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create ecclesia.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific ecclesia
     *
     * Returns details of a specific ecclesia along with country data for editing.
     *
     * @urlParam id integer required The ID of the ecclesia. Example: 1
     *
     * @response 200 {
     *   "ecclesia": {
     *     "id": 1,
     *     "name": "First Church of Christ",
     *     "country": "US",
     *     "created_at": "2023-10-15T10:30:00.000000Z",
     *     "updated_at": "2023-10-15T10:30:00.000000Z",
     *     "country_name": {
     *       "id": 1,
     *       "name": "United States",
     *       "code": "US"
     *     }
     *   },
     *   "countries": [
     *     {
     *       "id": 1,
     *       "name": "United States",
     *       "code": "US"
     *     },
     *     {
     *       "id": 2,
     *       "name": "United Kingdom",
     *       "code": "UK"
     *     }
     *   ]
     * }
     * @response 403 {
     *   "message": "You do not have permission to access this resource."
     * }
     * @response 404 {
     *   "message": "Ecclesia not found."
     * }
     */
    public function show($id)
    {
        if (!Auth::user()->can('Manage Role Permission')) {
            return response()->json([
                'message' => 'You do not have permission to access this resource.'
            ], 403);
        }

        try {
            $ecclesia = Ecclesia::with('countryName')->findOrFail($id);
            $countries = Country::orderBy('name', 'asc')->get();

            return response()->json([
                'ecclesia' => $ecclesia,
                'countries' => $countries
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ecclesia not found.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update an ecclesia
     *
     * Updates an existing ecclesia with new information.
     *
     * @urlParam id integer required The ID of the ecclesia. Example: 1
     * @bodyParam name string required The name of the ecclesia. Example: "First Church of Christ - Updated"
     * @bodyParam country string required The country code of the ecclesia. Example: "US"
     *
     * @response 200 {
     *   "message": "Ecclesia updated successfully.",
     *   "ecclesia": {
     *     "id": 1,
     *     "name": "First Church of Christ - Updated",
     *     "country": "US",
     *     "created_at": "2023-10-15T10:30:00.000000Z",
     *     "updated_at": "2023-10-28T16:45:30.000000Z"
     *   }
     * }
     * @response 403 {
     *   "message": "You do not have permission to access this resource."
     * }
     * @response 404 {
     *   "message": "Ecclesia not found."
     * }
     * @response 422 {
     *   "message": "The name has already been taken."
     * }
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('Manage Role Permission')) {
            return response()->json([
                'message' => 'You do not have permission to access this resource.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:ecclesias,name,' . $id,
            'country' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $ecclesia = Ecclesia::findOrFail($id);
            $ecclesia->name = $request->name;
            $ecclesia->country = $request->country;
            $ecclesia->save();

            return response()->json([
                'message' => 'Ecclesia updated successfully.',
                'ecclesia' => $ecclesia
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ecclesia not found.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Delete an ecclesia
     *
     * Removes an ecclesia from the system.
     *
     * @urlParam id integer required The ID of the ecclesia. Example: 1
     *
     * @response 200 {
     *   "message": "Ecclesia deleted successfully."
     * }
     * @response 403 {
     *   "message": "You do not have permission to access this resource."
     * }
     * @response 404 {
     *   "message": "Ecclesia not found."
     * }
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('Manage Role Permission')) {
            return response()->json([
                'message' => 'You do not have permission to access this resource.'
            ], 403);
        }

        try {
            $ecclesia = Ecclesia::findOrFail($id);
            Log::info($ecclesia->name . ' deleted by ' . Auth::user()->email . ' at ' . now());
            $ecclesia->delete();

            return response()->json([
                'message' => 'Ecclesia deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete ecclesia.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
