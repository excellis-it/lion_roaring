<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * @authenticated
 *
 * @group Estore Colors
 *
 */
class ColorController extends Controller
{

    /**
     * Color List
     *
     * This endpoint retrieves a paginated list of all active colors.
     *
     * @queryParam page int optional The page number for pagination. Example: 1
     * @queryParam per_page int optional Number of records per page. Defaults to 10. Example: 10
     *
     * @response 200
     *     {
     *     "data": {
     *         "current_page": 1,
     *         "data": [
     *             {
     *                 "id": 1,
     *                 "color_name": "Red",
     *                 "color": "#ff0000",
     *                 "status": 1,
     *                 "created_at": "2025-08-28T14:24:03.000000Z",
     *                 "updated_at": "2025-08-28T14:24:03.000000Z"
     *             },
     *             {
     *                 "id": 2,
     *                 "color_name": "Green",
     *                 "color": "#00ff04",
     *                 "status": 1,
     *                 "created_at": "2025-08-28T14:24:18.000000Z",
     *                 "updated_at": "2025-08-28T14:24:18.000000Z"
     *             },
     *             {
     *                 "id": 3,
     *                 "color_name": "Blue",
     *                 "color": "#0066eb",
     *                 "status": 1,
     *                 "created_at": "2025-08-28T14:24:35.000000Z",
     *                 "updated_at": "2025-08-28T14:24:35.000000Z"
     *             },
     *             {
     *                 "id": 4,
     *                 "color_name": "Black",
     *                 "color": "#000000",
     *                 "status": 1,
     *                 "created_at": "2025-08-28T14:24:52.000000Z",
     *                 "updated_at": "2025-08-28T14:24:52.000000Z"
     *             },
     *             {
     *                 "id": 5,
     *                 "color_name": "Yellow",
     *                 "color": "#00d5ff",
     *                 "status": 1,
     *                 "created_at": "2025-09-01T15:06:02.000000Z",
     *                 "updated_at": "2025-09-04T16:06:29.000000Z"
     *             },
     *             {
     *                 "id": 6,
     *                 "color_name": "White",
     *                 "color": "#ffffff",
     *                 "status": 1,
     *                 "created_at": "2025-09-13T14:56:54.000000Z",
     *                 "updated_at": "2025-09-13T14:56:54.000000Z"
     *             }
     *         ],
     *         "first_page_url": "http:*127.0.0.1:8000/api/v3/user/colors?page=1",
     *         "from": 1,
     *         "last_page": 1,
     *         "last_page_url": "http:*127.0.0.1:8000/api/v3/user/colors?page=1",
     *         "links": [
     *             {
     *                 "url": null,
     *                 "label": "&laquo; Previous",
     *                 "active": false
     *             },
     *             {
     *                 "url": "http:*127.0.0.1:8000/api/v3/user/colors?page=1",
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
     *         "path": "http:*127.0.0.1:8000/api/v3/user/colors",
     *         "per_page": 10,
     *         "prev_page_url": null,
     *         "to": 6,
     *         "total": 6
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
            $colors = Color::where('status', 1)->paginate(10);
            return response()->json([
                'data' => $colors,
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
     * Color Store
     *
     * Create a new color.
     *
     * @bodyParam color_name string required The color name. Example: "Medium"
     * @bodyParam color string required The color name. Example: "Medium"
     * @bodyParam status boolean required Status of the color (1 = active, 0 = inactive). Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 12,
     *     "color_name": "Black",
     *     "color": "#000000",
     *     "status": 1,
     *     "created_at": "2025-10-17T10:00:00Z",
     *     "updated_at": "2025-10-17T10:00:00Z"
     *   },
     *   "message": "Color created successfully.",
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
            'color_name'   => 'required|string|max:255|unique:colors,color_name',
            'color' => 'required',
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
            $color = new Color();
            $color->color_name = $request->input('color_name');
            $color->color = $request->input('color');
            $color->status = $request->input('status');
            $color->save();

            return response()->json([
                'data'    => $color,
                'message' => 'Color created successfully.',
                'status'  => true
            ], 200);
        } catch (\Exception $e) {
            // Log::error('Color store failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status'  => false
            ], 201);
        }
    }


    /**
     * Color Details
     *
     * Get details of a color by ID.
     *
     * @bodyParam id int required The ID of the color. Example: 3
     *
     * @response 200 {
     *   "data": {
     *     "id": 3,
     *     "color_name": "Red",
     *     "color": "#FF0000",
     *     "status": 1,
     *     "created_at": "2025-10-17T10:00:00Z",
     *     "updated_at": "2025-10-17T12:00:00Z"
     *   },
     *   "status": true
     * }
     *
     * @response 201 {
     *   "message": "Color not found.",
     *   "status": false
     * }
     */
    public function edit(Request $request)
    {
        // Validation
        $request->validate([
            'id' => 'required|exists:colors,id',
        ]);

        try {
            $color = Color::find($request->id);

            return response()->json([
                'data'   => $color,
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            // Log::error('Failed to fetch color: ' . $e->getMessage());
            return response()->json([
                'message' => 'Color not found.',
                'status'  => false
            ], 201);
        }
    }





    /**
     * Color Update
     *
     * Update an existing color by ID.
     *
     * @urlParam id int required The ID of the color. Example: 5
     * @bodyParam color_name string required The color name. Example: "Medium"
     * @bodyParam color string required The color name. Example: "Medium"
     * @bodyParam status boolean required Status of the color (1 = active, 0 = inactive). Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 5,
     *     "color_name": "Black",
     *     "color": "#000000",
     *     "status": 1,
     *     "created_at": "2025-10-17T10:00:00Z",
     *     "updated_at": "2025-10-17T12:00:00Z"
     *   },
     *   "message": "Color updated successfully.",
     *   "status": true
     * }
     *
     * @response 201 {
     *   "message": "Color not found.",
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
                'exists:colors,id' // ensures the ID exists in the colors table
            ],
            'color' => 'required',
            'color_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('colors', 'color_name')->ignore($request->id)
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
            $color = Color::find($request->id);

            if (!$color) {
                return response()->json([
                    'message' => 'Color not found.',
                    'status'  => false
                ], 201);
            }

            $color->color_name = $request->input('color_name');
            $color->color = $request->input('color');
            $color->status = $request->input('status');
            $color->save();

            return response()->json([
                'data'    => $color,
                'message' => 'Color updated successfully.',
                'status'  => true
            ], 200);
        } catch (\Exception $e) {
            // Log::error('Color update failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status'  => false
            ], 201);
        }
    }




    /**
     * Color Delete
     *
     * Delete a color by ID.
     *
     * @urlParam id int required The ID of the color to delete. Example: 5
     *
     * @response 200 {
     *   "message": "Color deleted successfully.",
     *   "status": true
     * }
     *
     * @response 201 {
     *   "message": "Color not found.",
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
                'exists:colors,id' // ensures the ID exists in the colors table
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
            $color = Color::find($request->id);

            if (!$color) {
                return response()->json([
                    'message' => 'Color not found.',
                    'status'  => false
                ], 201);
            }

            $color->delete();

            return response()->json([
                'message' => 'Color deleted successfully.',
                'status'  => true
            ], 200);
        } catch (\Exception $e) {
            // Log::error('Color delete failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status'  => false
            ], 201);
        }
    }
}
