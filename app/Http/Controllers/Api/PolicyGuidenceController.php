<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use App\Services\NotificationService;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * @group Policy
 *
 * @authenticated
 */
class PolicyGuidenceController extends Controller
{
    use ImageTrait;
    /**
     * Policies List
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
     *                "file": "policies/06C4cc8uACENraZzOjILXIY5QIg4QQrDESsT5Kqv.pdf",
     *                "created_at": "2024-11-11T07:02:33.000000Z",
     *                "updated_at": "2024-11-11T07:02:33.000000Z"
     *            },
     *            {
     *                "id": 6,
     *                "user_id": 1,
     *                "file_name": "partner-Photoroom.jpg",
     *                "file_extension": "jpg",
     *                "file": "policies/L4u3mwqeGc8BuCuHXR5X9ZYOPbV2SYgQcvqDIiIN.jpg",
     *                "created_at": "2024-08-27T06:34:46.000000Z",
     *                "updated_at": "2024-08-27T06:34:46.000000Z"
     *            },
     *            {
     *                "id": 5,
     *                "user_id": 1,
     *                "file_name": "A2ncCVFTo7T9zg1wjM9BPyX9u1PctUGVsPi8oEXb.jpg",
     *                "file_extension": "jpg",
     *                "file": "policies/1sk1WN3iEFUA3ztNYNCApyqR5jKg2RyLSBXHk1oP.jpg",
     *                "created_at": "2024-08-27T06:34:46.000000Z",
     *                "updated_at": "2024-08-27T06:34:46.000000Z"
     *            }
     *        ],
     *        "first_page_url": "http://127.0.0.1:8000/api/v3/user/policy/load?page=1",
     *        "from": 1,
     *        "last_page": 1,
     *        "last_page_url": "http://127.0.0.1:8000/api/v3/user/policy/load?page=1",
     *        "links": [
     *            {
     *                "url": null,
     *                "label": "&laquo; Previous",
     *                "active": false
     *            },
     *            {
     *                "url": "http://127.0.0.1:8000/api/v3/user/policy/load?page=1",
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
     *        "path": "http://127.0.0.1:8000/api/v3/user/policy/load",
     *        "per_page": 15,
     *        "prev_page_url": null,
     *        "to": 3,
     *        "total": 3
     *    }
     * }
     * @response 201 scenario="error" {"error": "Failed to fetch policies."}
     */
    public function index(Request $request)
    {
        try {
            // Get search term from the request (e.g., file name or user id)
            $searchQuery = $request->get('search');


            // Query policies with optional search functionality
            $policies = Policy::when($searchQuery, function ($query) use ($searchQuery) {
                $query->where('file_name', 'like', "%$searchQuery%")
                    ->orWhere('file_extension', 'like', "%$searchQuery%");
            })
                ->orderBy('id', 'desc')
                ->paginate(15);

            // Return success response with policy data
            return response()->json(['data' => $policies], 200);
        } catch (\Exception $e) {
            // Return error response if fetching policies fails
            return response()->json(['error' => 'Failed to fetch policies.'], 201);
        }
    }

    /**
     * Create policies
     *
     * @bodyParam file file required files to upload.
     *
     * @response 200 scenario="success" {"message": "Policy(s) uploaded successfully."}
     * @response 201 scenario="error" {"error": "Validation failed or duplicate policy found."}
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
            $file_path = $this->imageUpload($file, 'policies');

            $check = Policy::where('file_name', $file_name)
                ->where('file_extension', $file_extension)
                ->first();

            if ($check) {
                return response()->json(['error' => 'The policy name "' . $file_name . '" has already been taken.'], 201);
            }

            $policy = new Policy();
            $policy->user_id = auth()->id();
            $policy->file_name = $file_name;
            $policy->file_extension = $file_extension;
            $policy->file = $file_path;
            $policy->save();

            $userName = Auth::user()->getFullNameAttribute();
            $noti = NotificationService::notifyAllUsers('New Policy created by ' . $userName, 'policy');


            return response()->json(['message' => 'Policy(s) uploaded successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to upload policy(s).' . $e], 201);
        }
    }

    /**
     * Delete policy
     *
     * @urlParam id int required The ID of the policy to delete.
     *
     * @response 200 scenario="success" {"message": "Policy deleted successfully."}
     * @response 201 scenario="error" {"error": "Policy not found or permission denied."}
     */
    public function delete($id)
    {
        try {

            $policy = Policy::find($id);
            if ($policy) {
                if (Storage::disk('public')->exists($policy->file)) {
                    Storage::disk('public')->delete($policy->file);
                }
                Log::info($policy->file_name . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
                $policy->delete();
                return response()->json(['message' => 'Policy deleted successfully.'], 200);
            } else {
                return response()->json(['error' => 'Policy not found.'], 201);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete policy.'], 201);
        }
    }

    /**
     * Download policy file
     *
     * @response 200 scenario="success" The file download response.
     * @response 201 scenario="error" {"error": "Policy not found or permission denied."}
     */
    public function download($id)
    {
        try {

            $policy = Policy::find($id);
            if ($policy && Storage::disk('public')->exists($policy->file)) {
                return response()->download(Storage::disk('public')->path($policy->file));
            } else {
                return response()->json(['error' => 'Policy not found.'], 201);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to download policy.'], 201);
        }
    }

    /**
     * Single Policy details
     *
     * @urlParam id int required The ID of the policy to view.
     *
     * @response 200 scenario="success" {"data": {"id": 1, "file_name": "strategy1.pdf", "file_extension": "pdf", "user_id": 1, "file": "policies/strategy1.pdf"}}
     * @response 201 scenario="error" {"error": "Policy not found or permission denied."}
     */
    public function view($id)
    {
        try {

            $policy = Policy::find($id);
            if ($policy) {
                return response()->json(['data' => $policy], 200);
            } else {
                return response()->json(['error' => 'Policy not found.'], 201);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch policy details.'], 201);
        }
    }
}
