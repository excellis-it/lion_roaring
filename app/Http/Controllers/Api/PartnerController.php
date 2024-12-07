<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

/**
 * @group Members
 * 
 * @authenticated
 */

class PartnerController extends Controller
{
    /**
     * List Of Members
     * @queryParam search string optional for search. Example: "abc"
     * 
     * @response 200 {
     *    "data": {
     *        "current_page": 1,
     *        "data": [
     *            {
     *                "id": 39,
     *                "ecclesia_id": 9,
     *                "created_id": null,
     *                "user_name": "johndoe",
     *                "first_name": "John",
     *                "middle_name": "A.",
     *                "last_name": "Doe",
     *                "email": "johndoe@example.com",
     *                "phone": "1234567890",
     *                "email_verified_at": "2024-11-06T10:49:25.000000Z",
     *                "profile_picture": null,
     *                "address": "123 Main St",
     *                "city": "Springfield",
     *                "state": "Illinois",
     *                "address2": "Apt 4B",
     *                "country": "USA",
     *                "zip": "62704",
     *                "status": 0,
     *                "created_at": "2024-11-06T10:49:25.000000Z",
     *                "updated_at": "2024-11-06T10:49:25.000000Z"
     *            },
     *            {
     *                "id": 38,
     *                "ecclesia_id": 4,
     *                "created_id": null,
     *                "user_name": "masum2",
     *                "first_name": "Masum",
     *                "middle_name": null,
     *                "last_name": "2",
     *                "email": "masum2@excellisit.net",
     *                "phone": "+91 11 1111 1111",
     *                "email_verified_at": "2024-11-05T07:17:07.000000Z",
     *                "profile_picture": null,
     *                "address": "Kolkata",
     *                "city": "Kolkata",
     *                "state": "41",
     *                "address2": null,
     *                "country": "101",
     *                "zip": "700001",
     *                "status": 1,
     *                "created_at": "2024-11-05T07:17:07.000000Z",
     *                "updated_at": "2024-11-05T07:17:07.000000Z"
     *            },
     *            {
     *                "id": 37,
     *                "ecclesia_id": 4,
     *                "created_id": null,
     *                "user_name": "masum1",
     *                "first_name": "Test",
     *                "middle_name": null,
     *                "last_name": "User",
     *                "email": "masum@excellisit.net",
     *                "phone": "+91 91234 56789",
     *                "email_verified_at": "2024-10-28T08:35:17.000000Z",
     *                "profile_picture": "profile_picture\/sLWWnksqS6PHYMdZeBQ4OK3SnbVA0oMc9oykPbCn.webp",
     *                "address": "kolkata",
     *                "city": "kolkata",
     *                "state": "41",
     *                "address2": "kolkata",
     *                "country": "101",
     *                "zip": "700001",
     *                "status": 1,
     *                "created_at": "2024-10-28T08:35:17.000000Z",
     *                "updated_at": "2024-11-08T12:59:01.000000Z"
     *            }
     *        ],
     *        "first_page_url": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/partners\/list?page=1",
     *        "from": 1,
     *        "last_page": 2,
     *        "last_page_url": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/partners\/list?page=2",
     *        "links": [
     *            {
     *                "url": null,
     *                "label": "&laquo; Previous",
     *                "active": false
     *            },
     *            {
     *                "url": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/partners\/list?page=1",
     *                "label": "1",
     *                "active": true
     *            },
     *            {
     *                "url": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/partners\/list?page=2",
     *                "label": "2",
     *                "active": false
     *            },
     *            {
     *                "url": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/partners\/list?page=2",
     *                "label": "Next &raquo;",
     *                "active": false
     *            }
     *        ],
     *        "next_page_url": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/partners\/list?page=2",
     *        "path": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/partners\/list",
     *        "per_page": 15,
     *        "prev_page_url": null,
     *        "to": 15,
     *        "total": 2
     *    }
     * }
     * 
     * @response 201 {
     *    "message": "Error occurred while fetching the partners."
     * }
     */
    public function list(Request $request)
    {
        try {
            // Get search query if available
            $searchQuery = $request->get('search');

            // Build the query with search filters (name, email, phone)
            $partners = User::whereHas('roles', function ($q) {
                $q->where('name', '!=', 'ADMIN');
            })
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    $query->where('first_name', 'like', "%{$searchQuery}%")
                        ->orWhere('last_name', 'like', "%{$searchQuery}%")
                        ->orWhere('email', 'like', "%{$searchQuery}%")
                        ->orWhere('phone', 'like', "%{$searchQuery}%")
                        ->orWhere('address', 'like', "%{$searchQuery}%")
                        ->orWhere('city', 'like', "%{$searchQuery}%")
                        ->orWhere('state', 'like', "%{$searchQuery}%")
                        ->orWhere('country', 'like', "%{$searchQuery}%");
                })
                ->orderBy('id', 'desc')
                ->paginate(15);

            // Return successful response with partner data
            return response()->json([
                'data' => $partners
            ], 200);
        } catch (\Exception $e) {
            // Return error response in case of failure
            return response()->json([
                'message' => 'Error occurred while fetching the partners.'
            ], 201);
        }
    }
}
