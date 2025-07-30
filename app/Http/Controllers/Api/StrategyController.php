<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Strategy;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

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
            // Get search term from the request (e.g., file name or user id)
            $searchQuery = $request->get('search');


            // Query strategies with optional search functionality
            $strategies = Strategy::when($searchQuery, function ($query) use ($searchQuery) {
                $query->where('file_name', 'like', "%$searchQuery%")
                    ->orWhere('file_extension', 'like', "%$searchQuery%");
            })
                ->orderBy('id', 'desc')
                ->paginate(15);

            // Return success response with strategy data
            return response()->json(['data' => $strategies], 200);
        } catch (\Exception $e) {
            // Return error response if fetching strategies fails
            return response()->json(['error' => 'Failed to fetch strategies.'], 201);
        }
    }

    /**
     * Create strategies
     *
     * @bodyParam file file required files to upload.
     *
     * @response 200 scenario="success" {"message": "Strategy(s) uploaded successfully."}
     * @response 201 scenario="error" {"error": "Validation failed or duplicate strategy found."}
     */
    public function store(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'file' => 'required|file',
            ]);

            if ($validated->fails()) {
                return response()->json(['error' => $validated->errors()->first()], 201);
            }

            $file = $request->file('file');

            $file_name = $file->getClientOriginalName();
            $file_extension = $file->getClientOriginalExtension();
            $file_path = $this->imageUpload($file, 'strategies');

            $check = Strategy::where('file_name', $file_name)
                ->where('file_extension', $file_extension)
                ->first();

            if ($check) {
                return response()->json(['error' => 'The strategy name "' . $file_name . '" has already been taken.'], 201);
            }

            $strategy = new Strategy();
            $strategy->user_id = auth()->id();
            $strategy->file_name = $file_name;
            $strategy->file_extension = $file_extension;
            $strategy->file = $file_path;
            $strategy->save();

            $userName = Auth::user()->getFullNameAttribute();
            $noti = NotificationService::notifyAllUsers('New Strategy created by ' . $userName, 'strategy');


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
                return response()->download(Storage::disk('public')->path($strategy->file));
            } else {
                return response()->json(['error' => 'Strategy not found.'], 201);
            }
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

            $strategy = Strategy::find($id);
            if ($strategy) {
                return response()->json(['data' => $strategy], 200);
            } else {
                return response()->json(['error' => 'Strategy not found.'], 201);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch strategy details.'], 201);
        }
    }
}
