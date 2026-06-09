<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\Concerns\AppliesPmaContentScope;
use App\Http\Controllers\Api\Concerns\AppliesPmaCountryFromRequest;
use App\Http\Controllers\Controller;
use App\Models\Bulletin;
use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\NotificationService;

/**
 * @group Bulletins
 */

class BulletinController extends Controller
{
    use AppliesPmaCountryFromRequest;
    use AppliesPmaContentScope;
    /**
     * Bulletins List
     *
     * @authenticated
     * @queryParam search string optional for search. Example: "abc"
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "user_id": 1,
     *             "title": "Sample Bulletin Title",
     *             "description": "Sample Bulletin Description",
     *             "created_at": "2024-11-08T12:00:00.000000Z",
     *             "updated_at": "2024-11-08T12:00:00.000000Z"
     *         }
     *     ]
     * }
     */
    public function index(Request $request)
    {
        try {
            if (! auth()->user()->can('Manage Bulletin')) {
                return response()->json(['error' => 'Permission denied.'], 403);
            }

            $searchQuery = $request->get('search');
            $ctx = $this->pmaScopeContext();

            $bulletins = Bulletin::with(['country', 'user'])
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    $query->where(function ($subQuery) use ($searchQuery) {
                        $subQuery->where('title', 'like', "%{$searchQuery}%")
                            ->orWhere('description', 'like', "%{$searchQuery}%");
                    });
                });

            $this->applyPmaCreatorContentScope($bulletins, $ctx);

            $bulletins = $bulletins->orderBy('id', 'desc')->paginate(15);

            $isSuperAdmin = $ctx['is_super_admin'];

            $bulletins->getCollection()->transform(function ($bulletin) use ($isSuperAdmin) {
                $bulletin->country_name = $bulletin->country?->name ?? '--';

                if ($isSuperAdmin) {
                    $name = trim((string) ($bulletin->user?->full_name ?? ''));
                    $bulletin->upload_by_full_name = $name !== '' ? $name : 'Unknown';
                }

                return $bulletin;
            });

            return response()->json($bulletins, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load bulletins.'], 500);
        }
    }




    /**
     * Fetch Single Bulletin
     *
     * @authenticated
     * @urlParam id int required The ID of the bulletin to retrieve. Example: 1
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "user_id": 2,
     *         "title": "Sample Bulletin Title",
     *         "description": "Sample bulletin description",
     *         "created_at": "2024-11-08T12:00:00.000000Z",
     *         "updated_at": "2024-11-08T12:00:00.000000Z"
     *     }
     * }
     * @response 201 {
     *     "error": "Bulletin not found."
     * }
     */
    public function show($id)
    {
        try {
            if (! auth()->user()->can('Manage Bulletin')) {
                return response()->json(['error' => 'Permission denied.'], 403);
            }

            $ctx = $this->pmaScopeContext();
            $query = Bulletin::query();
            $this->applyPmaCreatorContentScope($query, $ctx);
            $bulletin = $query->find($id);

            if (!$bulletin) {
                return response()->json(['error' => 'Bulletin not found.'], 201);
            }

            return response()->json(['data' => $bulletin], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve bulletin.'], 201);
        }
    }

    /**
     * Bulletin Board
     * @authenticated
     * @queryParam search string optional for search. Example: "abc"
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "user_id": 2,
     *             "title": "Sample Bulletin Title",
     *             "description": "Sample bulletin description",
     *             "created_at": "2024-11-08T12:00:00.000000Z",
     *             "updated_at": "2024-11-08T12:00:00.000000Z",
     *             "user": {
     *                "id": 12,
     *                "ecclesia_id": 2,
     *                "created_id": "1",
     *                "user_name": "swarnadwip_nath",
     *                "first_name": "Swarnadwip",
     *                "middle_name": null,
     *                "last_name": "Nath",
     *                "email": "swarnadwip@excellisit.net",
     *                "phone": "+1 0741202022",
     *                "email_verified_at": null,
     *                "profile_picture": "profile_picture/yCvplMhdpjc0kIeKG63tfkZwhKNYbcF1ZhfQdDFO.jpg",
     *                "address": "Kokata",
     *                "city": "Kolkata",
     *                "state": "41",
     *                "address2": null,
     *                "country": "101",
     *                "zip": "700001",
     *                "status": 1,
     *                "created_at": "2024-06-21T11:31:27.000000Z",
     *                "updated_at": "2024-09-09T11:02:59.000000Z"
     *             }
     *         },
     *         {
     *             "id": 2,
     *             "user_id": 3,
     *             "title": "Another Bulletin Title",
     *             "description": "Another bulletin description",
     *             "created_at": "2024-11-08T12:00:00.000000Z",
     *             "updated_at": "2024-11-08T12:00:00.000000Z",
     *             "user": {
     *                "id": 13,
     *                "ecclesia_id": 2,
     *                "created_id": "1",
     *                "user_name": "john_doe",
     *                "first_name": "John",
     *                "middle_name": null,
     *                "last_name": "Chiera",
     *                "email": "john@yopmail.com",
     *                "phone": "07412020202",
     *                "email_verified_at": null,
     *                "profile_picture": "profile_picture/lX7sKGrvLYx22gM1qwGKXToQbPI4ILBFQHxThou7.jpg",
     *                "address": "Kokata",
     *                "city": "Kolkata",
     *                "state": "West Bengal",
     *                "address2": null,
     *                "country": "India",
     *                "zip": "700001",
     *                "status": 1,
     *                "created_at": "2024-06-21T11:32:03.000000Z",
     *                "updated_at": "2024-09-09T06:28:13.000000Z"
     *             }
     *         }
     *     ]
     * }
     * @response 201 {
     *     "error": "Failed to load bulletins."
     * }
     */
    public function allBulletins(Request $request)
    {
        try {
            if (! auth()->user()->can('Manage Bulletin')) {
                return response()->json(['error' => 'Permission denied.'], 403);
            }

            $searchQuery = $request->get('search');

            $bulletins = $this->bulletinBoardQuery()
                ->with('user')
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    $query->where(function ($q) use ($searchQuery) {
                        $q->where('title', 'like', "%{$searchQuery}%")
                            ->orWhere('description', 'like', "%{$searchQuery}%");
                    });
                })
                ->get();

            return response()->json(['data' => $bulletins], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load bulletins.'], 201);
        }
    }

    /**
     * Mirrors User\BulletinBoardController scope so the app board matches the web board.
     */
    private function bulletinBoardQuery(): Builder
    {
        $user = auth()->user();
        $user_type = $user->user_type;
        $user_country = $user->country;
        $currentCountry = Country::findByCurrentRequest();
        $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

        if ($user->hasNewRole('SUPER ADMIN')) {
            return Bulletin::orderBy('id', 'desc');
        }

        if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
            return Bulletin::orderBy('id', 'desc')
                ->whereHas('country', function ($query) {
                    $query->where('code', 'GL');
                })
                ->whereHas('user', function ($query) {
                    $query->whereIn('user_type', ['Global', 'G_R'])->where('status', 1);
                });
        }

        $bulletins = Bulletin::orderBy('id', 'desc')
            ->where('country_id', $user_country)
            ->whereHas('user', function ($query) {
                $query->whereIn('user_type', ['Regional', 'G_R'])->where('status', 1);
            });

        if ($user->is_ecclesia_admin == 1) {
            $manage_ecclesia_ids = is_array($user->manage_ecclesia)
                ? $user->manage_ecclesia
                : explode(',', $user->manage_ecclesia ?? '');
            $bulletins->where(function ($q) use ($manage_ecclesia_ids, $user) {
                $q->whereHas('user', function ($uq) use ($manage_ecclesia_ids) {
                    $uq->where(function ($sub) use ($manage_ecclesia_ids) {
                        $sub->whereIn('ecclesia_id', $manage_ecclesia_ids)->whereNotNull('ecclesia_id');
                        foreach ($manage_ecclesia_ids as $id) {
                            $sub->orWhereRaw('FIND_IN_SET(?, manage_ecclesia)', [trim($id)]);
                        }
                    });
                })->orWhere('user_id', $user->id);
            });
        }

        return $bulletins;
    }



    /**
     * Create Bulletin
     *
     * @authenticated
     * @bodyParam title string required The title of the bulletin. Example: "New Bulletin Title"
     * @bodyParam description string required The description of the bulletin. Example: "Details about the bulletin."
     * @response 200 {
     *     "message": "Bulletin created successfully.",
     *     "data": {
     *         "id": 1,
     *         "user_id": 1,
     *         "title": "New Bulletin Title",
     *         "description": "Details about the bulletin.",
     *         "created_at": "2024-11-08T12:00:00.000000Z",
     *         "updated_at": "2024-11-08T12:00:00.000000Z"
     *     }
     * }
     * @response 201 {
     *     "error": "Failed to create bulletin."
     * }
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('Create Bulletin')) {
            return response()->json(['error' => 'Permission denied.'], 403);
        }

        $country_id = $this->resolvePmaCountryId($request);
        $request->merge(['country_id' => $country_id]);

        $validated = Validator::make($request->all(), array_merge([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ], $this->pmaCountryValidationRules()));

        if ($validated->fails()) {
            return response()->json(['error' => $validated->errors()], 201);
        }

        try {
            $bulletin = Bulletin::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'country_id' => $country_id,
            ]);

            $userName = Auth::user()->getFullNameAttribute();
            $noti = NotificationService::notifyAllUsers('New Bulletin created by ' . $userName, 'bulletin');

            return response()->json(['message' => 'Bulletin created successfully.', 'data' => $bulletin], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create bulletin.'], 201);
        }
    }

    /**
     * Update Bulletin
     *
     * @authenticated
     * @urlParam id int required The ID of the bulletin to update. Example: 1
     * @bodyParam title string The new title of the bulletin. Example: "Updated Bulletin Title"
     * @bodyParam description string The new description of the bulletin. Example: "Updated details about the bulletin."
     * @response 200 {
     *     "message": "Bulletin updated successfully.",
     *     "data": {
     *         "id": 1,
     *         "user_id": 1,
     *         "title": "Updated Bulletin Title",
     *         "description": "Updated details about the bulletin.",
     *         "created_at": "2024-11-08T12:00:00.000000Z",
     *         "updated_at": "2024-11-08T12:00:00.000000Z"
     *     }
     * }
     * @response 201 {
     *     "error": "Bulletin not found or unauthorized."
     * }
     */
    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('Edit Bulletin')) {
            return response()->json(['error' => 'Permission denied.'], 403);
        }

        $country_id = $this->resolvePmaCountryId($request);
        $request->merge(['country_id' => $country_id]);

        $user_type = Auth::user()->user_type ?? 'Global';
        $user_country = Auth::user()->country ?? null;

        if ($this->requiresPmaCountryFromRequest()) {
            $validated = Validator::make($request->all(), array_merge([
                'title' => 'string|max:255',
                'description' => 'string',
            ], $this->pmaCountryValidationRules()));
        } else {
            $validated = Validator::make($request->all(), [
                'title' => 'string|max:255',
                'description' => 'string',
            ]);
        }

        if ($validated->fails()) {
            return response()->json(['error' => $validated->errors()], 201);
        }

        try {
            if (Auth::user()->hasNewRole('SUPER ADMIN')) {
                $bulletin = Bulletin::find($id);
            } else {
                if ($user_type == 'Global') {
                    $bulletin = Bulletin::where('id', $id)->where('user_id', Auth::id())->first();
                } else {
                    $bulletin = Bulletin::where('id', $id)->where('user_id', Auth::id())->where('country_id', $user_country)->first();
                }
            }

            if (!$bulletin) {
                return response()->json(['error' => 'Bulletin not found or unauthorized.'], 201);
            }

            $bulletin->title = $request->title ?? $bulletin->title;
            $bulletin->description = $request->description ?? $bulletin->description;
            $bulletin->country_id = $country_id;
            $bulletin->save();

            return response()->json(['message' => 'Bulletin updated successfully.', 'data' => $bulletin], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update bulletin.'], 201);
        }
    }

    /**
     * Remove Bulletin
     *
     * @authenticated
     * @urlParam id int required The ID of the bulletin to delete. Example: 1
     * @response 200 {
     *     "message": "Bulletin deleted successfully."
     * }
     * @response 201 {
     *     "error": "Bulletin not found or unauthorized."
     * }
     */
    public function destroy($id)
    {
        try {
            if (! auth()->user()->can('Delete Bulletin')) {
                return response()->json(['error' => 'Permission denied.'], 403);
            }

            $user_type = Auth::user()->user_type ?? 'Global';
            $user_country = Auth::user()->country ?? null;

            if (Auth::user()->hasNewRole('SUPER ADMIN')) {
                $bulletin = Bulletin::find($id);
            } else {
                if ($user_type == 'Global') {
                    $bulletin = Bulletin::where('id', $id)->where('user_id', Auth::id())->first();
                } else {
                    $bulletin = Bulletin::where('id', $id)->where('user_id', Auth::id())->where('country_id', $user_country)->first();
                }
            }

            if (!$bulletin) {
                return response()->json(['error' => 'Bulletin not found or unauthorized.'], 201);
            }

            $bulletin->delete();

            return response()->json(['message' => 'Bulletin deleted successfully.', 'data' => $bulletin], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete bulletin.'], 201);
        }
    }
}
