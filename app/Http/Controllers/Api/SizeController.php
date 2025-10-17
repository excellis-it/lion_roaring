<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * @authenticated
 *
 * @group Estore Sizes
 *
 */
class SizeController extends Controller
{
    /**
     * Size List
     *
     * This endpoint retrieves a paginated list of all active sizes.
     *
     * @queryParam page int optional The page number for pagination. Example: 1
     * @queryParam per_page int optional Number of records per page. Defaults to 10. Example: 10
     *
     * @response 200 {
     *     "data": {
     *         "current_page": 1,
     *         "data": [
     *             {
     *                 "id": 1,
     *                 "size": "Small - US",
     *                 "status": 1,
     *                 "created_at": "2025-08-28T14:23:32.000000Z",
     *                 "updated_at": "2025-09-04T16:08:26.000000Z"
     *             },
     *             {
     *                 "id": 2,
     *                 "size": "M",
     *                 "status": 1,
     *                 "created_at": "2025-08-28T14:23:36.000000Z",
     *                 "updated_at": "2025-08-28T14:23:36.000000Z"
     *             },
     *         ],
     *         "first_page_url": "http:*127.0.0.1:8000/api/v3/user/sizes?page=1",
     *         "from": 1,
     *         "last_page": 1,
     *         "last_page_url": "http:*127.0.0.1:8000/api/v3/user/sizes?page=1",
     *         "links": [
     *             {
     *                 "url": null,
     *                 "label": "&laquo; Previous",
     *                 "active": false
     *             },
     *             {
     *                 "url": "http:*127.0.0.1:8000/api/v3/user/sizes?page=1",
     *                 "label": "1",
     *                 "active": true
     *             },
     *             {
     *                 "url": null,
     *                 "label": "Next &raquo;",
     *                 "active": false
     *             }
     *         ],
     *         "next_page_url": null,
     *         "path": "http:*127.0.0.1:8000/api/v3/user/sizes",
     *         "per_page": 10,
     *         "prev_page_url": null,
     *         "to": 4,
     *         "total": 4
     *     },
     *     "status": true
     * }
     *
     * @response 201 {
     *   "message": "Failed to fetch files. Please try again later.",
     *   "status": false
     * }
     */


    public function index()
    {
        try {
            $sizes = Size::where('status', 1)->paginate(10);
            return response()->json([
                'data' => $sizes,
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            //    Log::error('Failed to fetch files: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch files. Please try again later.',
                'status' => false
            ], 201);
        }
    }

    /**
     * Size Store
     *
     * Create a new size.
     *
     * @bodyParam name string required The size name. Example: "Medium"
     * @bodyParam status boolean required Status of the size (1 = active, 0 = inactive). Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 12,
     *     "size": "Medium",
     *     "status": 1,
     *     "created_at": "2025-10-17T10:00:00Z",
     *     "updated_at": "2025-10-17T10:00:00Z"
     *   },
     *   "message": "Size created successfully.",
     *   "status": true
     * }
     *
     * @response 201 {
     *   "message": "Validation failed.",
     *   "errors": {
     *     "name": ["The name field is required."],
     *     "status": ["The status field must be true or false."]
     *   },
     *   "status": false
     * }
     *
     * @response 201 {
     *   "message": "Something went wrong. Please try again later.",
     *   "status": false
     * }
     */
    public function store(Request $request)
    {
        // Use Validator to build a custom validation response
        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:255|unique:sizes,size',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
                'status'  => false
            ], 201);
        }

        try {
            $size = new Size();
            $size->size = $request->input('name');
            $size->status = $request->input('status');
            $size->save();

            return response()->json([
                'data'    => $size,
                'message' => 'Size created successfully.',
                'status'  => true
            ], 200);
        } catch (\Exception $e) {
            // Log::error('Size store failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status'  => false
            ], 201);
        }
    }


    /**
     * Size Edit
     *
     * Get details of a specific size by ID for editing.
     *
     * @bodyParam id int required The ID of the size. Example: 5
     *
     * @response 200 {
     *   "data": {
     *     "id": 5,
     *     "size": "Medium",
     *     "status": 1,
     *     "created_at": "2025-10-17T10:00:00Z",
     *     "updated_at": "2025-10-17T12:00:00Z"
     *   },
     *   "status": true
     * }
     *
     * @response 201 {
     *   "message": "Size not found.",
     *   "status": false
     * }
     */
    public function edit(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'id' => [
                'required',
                'exists:sizes,id' // ensures the ID exists in the sizes table
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
                'status'  => false
            ], 201);
        }

        try {
            $size = Size::find($request->id);

            if (!$size) {
                return response()->json([
                    'message' => 'Size not found.',
                    'status'  => false
                ], 201);
            }

            return response()->json([
                'data'   => $size,
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            // Log::error('Size edit fetch failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status'  => false
            ], 201);
        }
    }




    /**
     * Size Update
     *
     * Update an existing size by ID.
     *
     * @urlParam id int required The ID of the size. Example: 5
     * @bodyParam name string required The size name. Example: "Large"
     * @bodyParam status boolean required Status of the size (1 = active, 0 = inactive). Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 5,
     *     "size": "Large",
     *     "status": 1,
     *     "created_at": "2025-10-17T10:00:00Z",
     *     "updated_at": "2025-10-17T12:00:00Z"
     *   },
     *   "message": "Size updated successfully.",
     *   "status": true
     * }
     *
     * @response 201 {
     *   "message": "Size not found.",
     *   "status": false
     * }
     *
     * @response 201 {
     *   "message": "Validation failed.",
     *   "errors": {
     *     "name": ["The name field is required."],
     *     "status": ["The status field must be true or false."]
     *   },
     *   "status": false
     * }
     *
     * @response 201 {
     *   "message": "Something went wrong. Please try again later.",
     *   "status": false
     * }
     */
    public function update(Request $request)
    {

        // Validation
        $validator = Validator::make($request->all(), [
            'id' => [
                'required',
                'exists:sizes,id' // ensures the ID exists in the sizes table
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sizes', 'size')->ignore($request->id)
                // ensures the name is unique, but ignore current record
            ],
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
                'status'  => false
            ], 201);
        }

        try {
            $size = Size::find($request->id);

            if (!$size) {
                return response()->json([
                    'message' => 'Size not found.',
                    'status'  => false
                ], 201);
            }

            $size->size = $request->input('name');
            $size->status = $request->input('status');
            $size->save();

            return response()->json([
                'data'    => $size,
                'message' => 'Size updated successfully.',
                'status'  => true
            ], 200);
        } catch (\Exception $e) {
            // Log::error('Size update failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status'  => false
            ], 201);
        }
    }




    /**
     * Size Delete
     *
     * Delete a size by ID.
     *
     * @urlParam id int required The ID of the size to delete. Example: 5
     *
     * @response 200 {
     *   "message": "Size deleted successfully.",
     *   "status": true
     * }
     *
     * @response 201 {
     *   "message": "Size not found.",
     *   "status": false
     * }
     *
     * @response 201 {
     *   "message": "Something went wrong. Please try again later.",
     *   "status": false
     * }
     */
    public function delete(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'id' => [
                'required',
                'exists:sizes,id' // ensures the ID exists in the sizes table
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
                'status'  => false
            ], 201);
        }


        try {
            $size = Size::find($request->id);

            if (!$size) {
                return response()->json([
                    'message' => 'Size not found.',
                    'status'  => false
                ], 201);
            }

            $size->delete();

            return response()->json([
                'message' => 'Size deleted successfully.',
                'status'  => true
            ], 200);
        } catch (\Exception $e) {
            // Log::error('Size delete failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status'  => false
            ], 201);
        }
    }
}
