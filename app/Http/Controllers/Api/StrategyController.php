<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Strategy;
use App\Traits\ImageTrait;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * @group Strategy
 *
 * @authenticated
 */

class StrategyController extends Controller
{
    use ImageTrait;

    /**
     * Strategies List
     * @queryParam search string optional for search. Example: "abc"
     *
     * @response 200 {
     *    "data": {
     *        "current_page": 1,
     *        "data": [
     *            {
     *                "id": 7,
     *                "user_id": 37,
     *                "file_name": "dummy (1).pdf",
     *                "file_extension": "pdf",
     *                "file": "strategies/06C4cc8uACENraZzOjILXIY5QIg4QQrDESsT5Kqv.pdf",
     *                "created_at": "2024-11-11T07:02:33.000000Z",
     *                "updated_at": "2024-11-11T07:02:33.000000Z"
     *            },
     *            {
     *                "id": 6,
     *                "user_id": 1,
     *                "file_name": "partner-Photoroom.jpg",
     *                "file_extension": "jpg",
     *                "file": "strategies/L4u3mwqeGc8BuCuHXR5X9ZYOPbV2SYgQcvqDIiIN.jpg",
     *                "created_at": "2024-08-27T06:34:46.000000Z",
     *                "updated_at": "2024-08-27T06:34:46.000000Z"
     *            },
     *            {
     *                "id": 5,
     *                "user_id": 1,
     *                "file_name": "A2ncCVFTo7T9zg1wjM9BPyX9u1PctUGVsPi8oEXb.jpg",
     *                "file_extension": "jpg",
     *                "file": "strategies/1sk1WN3iEFUA3ztNYNCApyqR5jKg2RyLSBXHk1oP.jpg",
     *                "created_at": "2024-08-27T06:34:46.000000Z",
     *                "updated_at": "2024-08-27T06:34:46.000000Z"
     *            }
     *        ],
     *        "first_page_url": "http://127.0.0.1:8000/api/v3/user/strategy/load?page=1",
     *        "from": 1,
     *        "last_page": 1,
     *        "last_page_url": "http://127.0.0.1:8000/api/v3/user/strategy/load?page=1",
     *        "links": [
     *            {
     *                "url": null,
     *                "label": "&laquo; Previous",
     *                "active": false
     *            },
     *            {
     *                "url": "http://127.0.0.1:8000/api/v3/user/strategy/load?page=1",
     *                "label": "1",
     *                "active": true
     *            },
     *            {
     *                "url": null,
     *                "label": "Next &raquo;",
     *                "active": false
     *            }
     *        ],
     *        "next_page_url": null,
     *        "path": "http://127.0.0.1:8000/api/v3/user/strategy/load",
     *        "per_page": 15,
     *        "prev_page_url": null,
     *        "to": 3,
     *        "total": 3
     *    }
     * }
     * @response 201 scenario="error" {"error": "Failed to fetch strategies."}
     */
    public function index(Request $request)
    {
        try {
            $searchQuery = $request->get('query', $request->get('search', ''));
            $searchQuery = str_replace(' ', '%', $searchQuery);

            $sortBy = $request->get('sortby', 'id');
            $sortType = $request->get('sorttype', 'desc');

            $strategies = Strategy::with(['user', 'country'])
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    $query->where(function ($search) use ($searchQuery) {
                        $search->where('id', 'like', "%$searchQuery%")
                            ->orWhere('file_name', 'like', "%$searchQuery%")
                            ->orWhere('file_extension', 'like', "%$searchQuery%")
                            ->orWhereHas('country', function ($countryQuery) use ($searchQuery) {
                                $countryQuery->where('name', 'like', "%$searchQuery%");
                            });
                    });
                })
                ->when($request->topic_id, function ($query) use ($request) {
                    $query->whereHas('topic', function ($topicQuery) use ($request) {
                        $topicQuery->where('id', $request->topic_id);
                    });
                })
                ->when($request->type, function ($query) use ($request) {
                    $query->where('type', $request->type);
                })
                ->when(!auth()->user()->hasNewRole('SUPER ADMIN'), function ($query) {
                    $user = auth()->user();
                    $userType = $user->user_type;
                    $userCountry = $user->country;
                    $currentCountry = Country::findByCurrentRequest();
                    $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

                    if ($userType === 'Global' || ($userType === 'G_R' && $isOnGlobalServer)) {
                        $query->whereHas('country', function ($countryQuery) {
                            $countryQuery->where('code', 'GL');
                        })->whereHas('user', function ($userQuery) {
                            $userQuery->whereIn('user_type', ['Global', 'G_R']);
                        });
                    } else {
                        $query->where('country_id', $userCountry)->whereHas('user', function ($userQuery) {
                            $userQuery->whereIn('user_type', ['Regional', 'G_R']);
                        });

                        if ($user->is_ecclesia_admin == 1) {
                            $manageEcclesiaIds = is_array($user->manage_ecclesia)
                                ? $user->manage_ecclesia
                                : explode(',', $user->manage_ecclesia ?? '');

                            $query->where(function ($ecclesiaQuery) use ($manageEcclesiaIds, $user) {
                                $ecclesiaQuery->whereHas('user', function ($userQuery) use ($manageEcclesiaIds) {
                                    $userQuery->whereIn('ecclesia_id', $manageEcclesiaIds);
                                })->orWhere('user_id', $user->id);
                            });
                        }
                    }
                })
                ->orderBy($sortBy, $sortType)
                ->paginate(15);

            return response()->json(['data' => $strategies], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch strategies.'], 201);
        }
    }

    /**
     * Create strategies
     *
     * @bodyParam file file[] required files to upload. Example: ["strategy1.pdf", "strategy2.pdf"]
     * @bodyParam country_id int optional Country ID for SUPER ADMIN uploads. Example: 1
     *
     * @response 200 scenario="success" {"message": "Strategy(s) uploaded successfully."}
     * @response 201 scenario="error" {"error": "Validation failed or duplicate strategy found."}
     */
    public function store(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'file' => 'required|array',
                'file.*' => 'required',
                'country_id' => 'nullable|exists:countries,id',
            ]);

            if ($validated->fails()) {
                return response()->json(['error' => $validated->errors()->first()], 201);
            }

            foreach ($request->file('file') as $file) {
                $file_name = $file->getClientOriginalName();
                $file_extension = $file->getClientOriginalExtension();
                $file_path = $this->imageUpload($file, 'strategies');

                $strategy = new Strategy();
                $strategy->user_id = auth()->id();
                $strategy->country_id = $request->country_id ?? auth()->user()->country;
                $strategy->file_name = $file_name;
                $strategy->file_extension = $file_extension;
                $strategy->file = $file_path;
                $strategy->save();
            }

            $userName = Auth::user()?->full_name ?? 'Unknown User';
            NotificationService::notifyAllUsers('New Strategy created by ' . $userName, 'strategy');

            return response()->json(['message' => 'Strategy(s) uploaded successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to upload strategy(s).' . $e], 201);
        }
    }

    /**
     * Delete strategy
     *
     * @urlParam id int required The ID of the strategy to delete.
     *
     * @response 200 scenario="success" {"message": "Strategy deleted successfully."}
     * @response 201 scenario="error" {"error": "Strategy not found or permission denied."}
     */
    public function delete($id)
    {
        try {
            $strategy = Strategy::find($id);
            if ($strategy) {
                if (Storage::disk('public')->exists($strategy->file)) {
                    Storage::disk('public')->delete($strategy->file);
                }
                Log::info($strategy->file_name . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
                $strategy->delete();
                return response()->json(['message' => 'Strategy deleted successfully.'], 200);
            } else {
                return response()->json(['error' => 'Strategy not found.'], 201);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete strategy.'], 201);
        }
    }

    /**
     * Download strategy file
     *
     * @response 200 scenario="success" The file download response.
     * @response 201 scenario="error" {"error": "Strategy not found or permission denied."}
     */
    public function download($id)
    {
        try {
            $strategy = Strategy::find($id);
            if ($strategy && Storage::disk('public')->exists($strategy->file)) {
                $filePath = storage_path('app/public/' . $strategy->file);

                return response()->download($filePath);
            }

            return response()->json(['error' => 'Strategy not found.'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to download strategy.'], 201);
        }
    }

    /**
     * Single Strategy details
     *
     * @urlParam id int required The ID of the strategy to view.
     *
     * @response 200 scenario="success" {"data": {"id": 1, "file_name": "strategy1.pdf", "file_extension": "pdf", "user_id": 1, "file": "strategies/strategy1.pdf"}}
     * @response 201 scenario="error" {"error": "Strategy not found or permission denied."}
     */
    public function view($id)
    {
        try {
            $strategy = Strategy::with(['user', 'country'])->find($id);
            if ($strategy) {
                return response()->json(['data' => $strategy], 200);
            }

            return response()->json(['error' => 'Strategy not found.'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch strategy details.'], 201);
        }
    }
}
