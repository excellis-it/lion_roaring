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
 * @subgroup Becoming Sovereign
 * @subgroupDescription APIs for managing becoming sovereign files and topics.
 */
class BecomingSovereignController extends Controller
{
    use ImageTrait;

    /**
     * Becoming Sovereigns List
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
     *                "type": "Becoming Sovereign",
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
     *                "type": "Becoming Sovereign",
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
     *                "type": "Becoming Sovereign",
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
     *            "education_type": "Becoming Sovereign",
     *            "created_at": "2024-11-08T09:42:48.000000Z",
     *            "updated_at": "2024-11-08T09:42:48.000000Z"
     *        },
     *        {
     *            "id": 6,
     *            "topic_name": "Leadership Development",
     *            "education_type": "Becoming Sovereign",
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
            $filesQuery = File::orderBy('id', 'desc')->where('type', 'Becoming Sovereign');

            if ($new_topic) {
                $filesQuery->where('topic_id', $new_topic);
            }

            $files = $filesQuery->paginate(15);
            $topics = Topic::orderBy('topic_name', 'asc')->where('education_type', 'Becoming Sovereign')->get();

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
     * Get List of Topics
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "topic_name": "Sample Topic",
     *       "education_type": "Becoming Sovereign",
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
            $topics = Topic::orderBy('topic_name', 'asc')->where('education_type', 'Becoming Sovereign')->get();

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






    //
}
