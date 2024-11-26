<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Topic;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * @authenticated
 * 
 * @group Education
 *  
 * @subgroup Files
 * @subgroupDescription APIs for managing files.
 */
class FileController extends Controller
{
    use ImageTrait;

    /**
     * Files List
     *
     * @queryParam topic int The ID of the topic to filter by. Example: 1
     * 
     * @response 200 *{
     *    "data": {
     *        "current_page": 1,
     *        "data": [
     *            
     *            {
     *                "id": 60,
     *                "user_id": 1,
     *                "file_name": "topic- Back to the Basics Description and Outline.docx",
     *                "file_extension": "docx",
     *                "topic_id": 4,
     *                "type": "Becoming Christ Like",
     *                "file": "files\/qm5d3aJ5Z6esEep7WZAs19BLo4KRsjXp9w1RITYv.docx",
     *                "created_at": "2024-08-02T09:32:31.000000Z",
     *                "updated_at": "2024-08-02T09:32:31.000000Z"
     *            },
     *            {
     *                "id": 59,
     *                "user_id": 1,
     *                "file_name": "topic-Back to the Basics Part 1.pptx",
     *                "file_extension": "pptx",
     *                "topic_id": 4,
     *                "type": "Becoming Christ Like",
     *                "file": "files\/bv7C4BiH0N1s8WXrVZpPV2s6YTWN5sVUQs5PE584.pptx",
     *                "created_at": "2024-08-02T09:31:40.000000Z",
     *                "updated_at": "2024-08-02T09:31:40.000000Z"
     *            },
     *            {
     *                "id": 57,
     *                "user_id": 1,
     *                "file_name": "topic-Back to the Basics Part 3.pptx",
     *                "file_extension": "pptx",
     *                "topic_id": 4,
     *                "type": "Becoming Christ Like",
     *                "file": "files\/BDp5AHtA4ZIsqziYPUxCbRL91bcbSvAVv4m8S9Zs.pptx",
     *                "created_at": "2024-08-02T09:29:56.000000Z",
     *                "updated_at": "2024-08-02T09:29:56.000000Z"
     *            }
     *        ],
     *        "first_page_url": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/becoming-sovereign?page=1",
     *        "from": 1,
     *        "last_page": 1,
     *        "last_page_url": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/becoming-sovereign?page=1",
     *        "links": [
     *            {
     *                "url": null,
     *                "label": "&laquo; Previous",
     *                "active": false
     *            },
     *            {
     *                "url": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/becoming-sovereign?page=1",
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
     *        "path": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/becoming-sovereign",
     *        "per_page": 15,
     *        "prev_page_url": null,
     *        "to": 11,
     *        "total": 11
     *    },
     *    "topics": [
     *        {
     *            "id": 12,
     *            "topic_name": "abcd",
     *            "education_type": "Becoming Christ Like",
     *            "created_at": "2024-11-08T09:42:48.000000Z",
     *            "updated_at": "2024-11-08T09:42:48.000000Z"
     *        },
     *        {
     *            "id": 6,
     *            "topic_name": "Leadership Development",
     *            "education_type": "Becoming Christ Like",
     *            "created_at": "2024-08-02T09:17:16.000000Z",
     *            "updated_at": "2024-08-16T08:58:34.000000Z"
     *        },
     *    ],
     *    "new_topic": "",
     *    "status": true
     *}
     */
    public function index(Request $request)
    {
        try {

            $new_topic = $request->topic ?? '';
            $filesQuery = File::orderBy('id', 'desc');

            if ($new_topic) {
                $filesQuery->where('topic_id', $new_topic);
            }

            $files = $filesQuery->get()->map(function ($file) {
                // Fetch the topic name for each file
                $file->file_topic_name = Topic::where('id', $file->topic_id)->value('topic_name');
                return $file;
            });

            //$files = $filesQuery->get();
            $topics = Topic::orderBy('topic_name', 'asc')->get();

            return response()->json([
                'data' => $files,
                'topics' => $topics,
                'new_topic' => $new_topic,
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
     * List by Topic
     *
     * @queryParam sortby string The column to sort by (optional, default: id).
     * @queryParam sorttype string The sort direction, either 'asc' or 'desc' (optional, default: 'asc').
     * @queryParam query string The search query to filter files by ID, file name, or file extension (optional).
     * @queryParam topic_id int The ID of the topic to filter files by (optional).
     *
     * @response 200 {
     *    "message": "Files retrieved successfully.",
     *    "data": [
     *        {
     *            "id": 77,
     *            "user_id": 37,
     *            "file_name": "image.png",
     *            "file_extension": "png",
     *            "topic_id": 13,
     *            "type": "Becoming Christ Like",
     *            "file": "files\/WoKa3ViZP3eoU6cqhAqcBqBdVfeGgGADoPS5c8WK.png",
     *            "created_at": "2024-11-08T11:01:16.000000Z",
     *            "updated_at": "2024-11-08T11:01:16.000000Z"
     *        }
     *    ],
     *    "pagination": {
     *        "total": 1,
     *        "per_page": 15,
     *        "current_page": 1,
     *        "last_page": 1
     *    },
     *    "status": true
     * }
     */
    public function listByTopic(Request $request)
    {
        try {
            // Retrieve sorting and filtering parameters
            $sort_by = $request->get('sortby', 'id');
            $sort_type = $request->get('sorttype', 'asc');
            $query = $request->get('query', '');
            $query = str_replace(" ", "%", $query);

            // Build the file query with filtering
            $filesQuery = File::query()
                ->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhere('file_name', 'like', '%' . $query . '%')
                        ->orWhere('file_extension', 'like', '%' . $query . '%');
                });

            // Filter by topic if provided
            if ($request->has('topic_id')) {
                $filesQuery->whereHas('topic', function ($q) use ($request) {
                    $q->where('id', $request->topic_id);
                });
            }

            // Order and paginate the results
            $files = $filesQuery->orderBy($sort_by, $sort_type)->paginate(15);

            // Return paginated files in JSON format
            return response()->json([
                'message' => 'Files retrieved successfully.',
                'data' => $files->items(),
                'pagination' => [
                    'total' => $files->total(),
                    'per_page' => $files->perPage(),
                    'current_page' => $files->currentPage(),
                    'last_page' => $files->lastPage(),
                ],
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve files: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status' => false
            ], 500);
        }
    }



    /**
     * Get List of Topics
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "topic_name": "Sample Topic",
     *       "education_type": "Becoming Christ Like",
     *       "created_at": "2024-09-09T11:02:39.000000Z",
     *       "updated_at": "2024-09-09T11:02:39.000000Z"
     *     }
     *   ],
     *   "status": true
     * }
     * 
     * @response 201 {
     *   "message": "Failed to fetch topics. Please try again later.",
     *   "status": false
     * }
     */
    public function topics()
    {
        try {
            $topics = Topic::orderBy('topic_name', 'asc')->get();

            return response()->json([
                'data' => $topics,
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            //    Log::error('Failed to fetch topics: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch topics. Please try again later.',
                'status' => false
            ], 201);
        }
    }


    /**
     * Create New
     *
     * @bodyParam file file required The file to upload.
     * @bodyParam topic_id int required The ID of the topic to associate the file with. Example: topic_id=1
     *
     * @response 200 {
     *    "message": "File uploaded successfully.",
     *    "data": {
     *        "user_id": 37,
     *        "file_name": "image.png",
     *        "file_extension": "png",
     *        "topic_id": "13",
     *        "type": "Becoming Christ Like",
     *        "file": "files/WoKa3ViZP3eoU6cqhAqcBqBdVfeGgGADoPS5c8WK.png",
     *        "updated_at": "2024-11-08T11:01:16.000000Z",
     *        "created_at": "2024-11-08T11:01:16.000000Z",
     *        "id": 77
     *    },
     *    "status": true
     * }
     * 
     * @response 400 {
     *   "message": "The file name has already been taken.",
     *   "status": false
     * }
     * 
     * @response 422 {
     *   "message": "Validation errors occurred.",
     *   "errors": {
     *     "file": ["The file field is required."],
     *     "topic_id": ["The topic id field is required."]
     *   },
     *   "status": false
     * }
     */
    public function store(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'file' => 'required|file', // Ensure file validation
                'topic_id' => 'required|exists:topics,id', // 'exists' checks if the value exists in the 'topics' table 'id' column
            ]);

            // If validation fails
            if ($validated->fails()) {
                return response()->json([
                    'message' => 'Validation errors occurred.',
                    'errors' => $validated->errors(),
                    'status' => false
                ], 201);
            }

            // Get file details
            $file = $request->file('file');
            $file_name = $file->getClientOriginalName();
            $file_extension = $file->getClientOriginalExtension();
            $file_upload = $this->imageUpload($file, 'files');

            // Check if a file with the same name and extension already exists
            $check = File::where('file_name', $file_name)
                ->where('file_extension', $file_extension)
                ->first();

            if ($check) {
                return response()->json([
                    'message' => 'The file name has already been taken.',
                    'status' => false
                ], 201);
            }

            // Save the new file details to the database
            $fileModel = new File();
            $fileModel->user_id = auth()->id();
            $fileModel->file_name = $file_name;
            $fileModel->file_extension = $file_extension;
            $fileModel->topic_id = $request->topic_id;
            $fileModel->type = '';
            $fileModel->file = $file_upload;
            $fileModel->save();

            return response()->json([
                'message' => 'File uploaded successfully.',
                'data' => $fileModel,
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            //   Log::error('File upload failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status' => false
            ], 201);
        }
    }


    /**
     * View File
     *
     * @urlParam id int required The ID of the file to view. Example: 1
     * @queryParam topic int The ID of the topic to filter by (optional).
     *
     * @response 200 {
     *    "message": "File details retrieved successfully.",
     *    "data": {
     *        "id": 77,
     *        "user_id": 37,
     *        "file_name": "image.png",
     *        "file_extension": "png",
     *        "topic_id": 13,
     *        "type": "Becoming Christ Like",
     *        "file": "files/WoKa3ViZP3eoU6cqhAqcBqBdVfeGgGADoPS5c8WK.png",
     *        "created_at": "2024-11-08T11:01:16.000000Z",
     *        "updated_at": "2024-11-08T11:01:16.000000Z"
     *    },
     *    "new_topic": "",
     *    "status": true
     * }
     * @response 404 {
     *   "message": "File not found.",
     *   "status": false
     * }
     */
    public function view(Request $request, $id)
    {
        try {
            // Find the file by ID, or fail if it doesn't exist
            $file = File::findOrFail($id);

            // Check if a specific topic is provided (optional)
            $new_topic = $request->get('topic', '');

            // Return file details in JSON format
            return response()->json([
                'message' => 'File details retrieved successfully.',
                'data' => $file,
                'new_topic' => $new_topic,
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            Log::error('File retrieval failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status' => false
            ], 500);
        }
    }


    /**
     * Update File
     *
     * @bodyParam file file The new file to replace the existing one (optional).
     * @bodyParam topic_id int required The ID of the topic to associate with the file. Example: 1
     * @response 200 {
     *   "message": "File updated successfully.",
     *   "data": {
     *     "id": 1,
     *     "file_name": "updated_sample.pdf",
     *     "file_extension": "pdf",
     *     "topic_id": 1,
     *     "user_id": 1,
     *     "file": "updated_file_path_here"
     *   },
     *   "status": true
     * }
     * @response 400 {
     *   "message": "Validation failed.",
     *   "errors": {
     *     "file": ["The file name has already been taken."]
     *   },
     *   "status": false
     * }
     * @response 404 {
     *   "message": "File not found.",
     *   "status": false
     * }
     * @response 500 {
     *   "message": "Something went wrong. Please try again later.",
     *   "status": false
     * }
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate topic_id and file fields
            $validatedData = $request->validate([
                'topic_id' => 'required|exists:topics,id',
                'file' => 'file'  // File is optional in case only topic_id needs updating
            ]);

            // Find the file or return a 404 error if not found
            $file = File::findOrFail($id);

            if ($request->hasFile('file')) {
                // Retrieve new file details
                $file_name = $request->file('file')->getClientOriginalName();
                $file_extension = $request->file('file')->getClientOriginalExtension();

                // Check if a file with the same name and extension exists
                $existingFile = File::where('file_name', $file_name)
                    ->where('file_extension', $file_extension)
                    ->first();
                if ($existingFile) {
                    return response()->json([
                        'message' => 'Validation failed.',
                        'errors' => ['file' => ['The file name has already been taken.']],
                        'status' => false
                    ], 201);
                }

                // Upload and update file details
                $file_upload = $this->imageUpload($request->file('file'), 'files');
                $file->file_name = $file_name;
                $file->file_extension = $file_extension;
                $file->file = $file_upload;
            }

            // Update topic_id
            $file->topic_id = $request->topic_id;
            $file->save();

            return response()->json([
                'message' => 'File updated successfully.',
                'data' => [
                    'id' => $file->id,
                    'file_name' => $file->file_name,
                    'file_extension' => $file->file_extension,
                    'topic_id' => $file->topic_id,
                    'user_id' => $file->user_id,
                    'file' => $file->file
                ],
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to update file: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status' => false
            ], 201);
        }
    }


    /**
     * Delete File
     *
     * @urlParam id int required The ID of the file to delete. Example: 1
     * @response 200 {
     *   "message": "File deleted successfully.",
     *   "status": true
     * }
     * @response 404 {
     *   "message": "File not found.",
     *   "status": false
     * }
     * @response 500 {
     *   "message": "Something went wrong. Please try again later.",
     *   "status": false
     * }
     */
    public function delete(Request $request, $id)
    {
        try {


            $file = File::find($id);

            if (!$file) {
                return response()->json([
                    'message' => 'File not found.',
                    'status' => false
                ], 404);
            }

            // Delete file from database and storage
            Storage::disk('public')->delete($file->file);
            $file->delete();

            return response()->json([
                'message' => 'File deleted successfully.',
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete file: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status' => false
            ], 500);
        }
    }


    /**
     * Download file
     *
     * @response 200 A downloadable file response with the specified file.
     * @response 201 {
     *   "message": "File not found.",
     *   "status": false
     * }
     * @response 201 {
     *   "message": "Something went wrong. Please try again later.",
     *   "status": false
     * }
     */
    public function download($id)
    {
        try {
            $file = File::find($id);

            if (!$file) {
                return response()->json([
                    'message' => 'File not found.',
                    'status' => false
                ], 201);
            }

            $filePath = Storage::disk('public')->path($file->file);

            if (!file_exists($filePath)) {
                return response()->json([
                    'message' => 'File not found.',
                    'status' => false
                ], 201);
            }

            return response()->download($filePath);
        } catch (\Exception $e) {
            Log::error('Failed to download file: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status' => false
            ], 201);
        }
    }







    //
}
