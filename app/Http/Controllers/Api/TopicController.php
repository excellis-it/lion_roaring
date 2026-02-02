<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Topic;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;

/**
 * @authenticated
 *
 * @group Education
 *
 * @subgroup Topics
 * @subgroupDescription APIs for managing topics in the system.
 */
class TopicController extends Controller
{
    /**
     * Topic Lists
     *
     * @response 200 {
     *    "data": {
     *        "current_page": 1,
     *        "data": [
     *            {
     *                "id": 11,
     *                "topic_name": "Third Topic",
     *                "education_type": "Becoming a Leader",
     *                "created_at": "2024-09-09T11:10:22.000000Z",
     *                "updated_at": "2024-09-09T11:10:22.000000Z"
     *            },
     *            {
     *                "id": 10,
     *                "topic_name": "Third Topic",
     *                "education_type": "Becoming Christ Like",
     *                "created_at": "2024-09-09T11:05:52.000000Z",
     *                "updated_at": "2024-09-09T11:10:04.000000Z"
     *            },
     *        ],
     *        "first_page_url": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/topics?page=1",
     *        "from": 1,
     *        "last_page": 1,
     *        "last_page_url": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/topics?page=1",
     *        "links": [
     *            {
     *                "url": null,
     *                "label": "&laquo; Previous",
     *                "active": false
     *            },
     *            {
     *                "url": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/topics?page=1",
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
     *        "path": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/topics",
     *        "per_page": 15,
     *        "prev_page_url": null,
     *        "to": 8,
     *        "total": 8
     *    },
     *    "status": true
     *}
     *
     */
    public function index()
    {
        try {
            $user_type = auth()->user()->user_type ?? 'Global';
            $user_country = auth()->user()->country ?? null;

            if ($user_type == 'Global') {
                $topics = Topic::orderBy('id', 'desc')->paginate(15);
            } else {
                $topics = Topic::where('country_id', $user_country)->orderBy('id', 'desc')->paginate(15);
            }

            return response()->json([
                'data' => $topics,
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch topics: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch topics. Please try again later.',
                'status' => false
            ], 201);
        }
    }


    /**
     * Create Topic
     *
     * @bodyParam topic_name string required The name of the topic. Example: "Sample Topic"
     * @bodyParam education_type string required The type of education. Example: "General"
     *
     * @response 200 {
     *   "message": "Topic created successfully.",
     *   "status": true
     * }
     *
     * @response 400 {
     *   "message": "Validation failed for the provided data.",
     *   "status": false
     * }
     */
    public function store(Request $request)
    {
        try {
            $user_type = auth()->user()->user_type ?? 'Global';
            $user_country = auth()->user()->country ?? null;

            if ($user_type === 'Global') {
                $request->validate([
                    'topic_name' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('topics')->where(function ($query) use ($request) {
                            return $query->where('education_type', $request->education_type)
                                ->where('country_id', $request->country_id);
                        }),
                    ],
                    'education_type' => 'required|string|max:255',
                    'country_id' => 'required|exists:countries,id',
                ]);
                $country_id = $request->country_id;
            } else {
                $request->merge(['country_id' => $user_country]);
                $request->validate([
                    'topic_name' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('topics')->where(function ($query) use ($request) {
                            return $query->where('education_type', $request->education_type)
                                ->where('country_id', $request->country_id);
                        }),
                    ],
                    'education_type' => 'required|string|max:255',
                ]);
                $country_id = $user_country;
            }

            $topic = new Topic();
            $topic->topic_name = $request->topic_name;
            $topic->education_type = $request->education_type;
            $topic->country_id = $country_id;
            $topic->save();

            $userName = Auth::user()->getFullNameAttribute();
            $noti = NotificationService::notifyAllUsers('New Topic created by ' . $userName, 'topic');

            return response()->json([
                'message' => 'Topic created successfully.',
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to create topic: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create topic. Please try again later.',
                'status' => false
            ], 201);
        }
    }

    /**
     * View Topic
     *
     * @urlParam id int required The ID of the topic. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "topic_name": "Sample Topic",
     *     "education_type": "General"
     *   },
     *   "status": true
     * }
     *
     */
    public function edit($id)
    {
        try {

            $topic = Topic::findOrFail($id);
            return response()->json([
                'data' => $topic,
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch topic for editing: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch topic. Please try again later.',
                'status' => false
            ], 201);
        }
    }

    /**
     * Update Topic
     *
     * @bodyParam topic_name string required The name of the topic. Example: "Updated Topic"
     * @bodyParam education_type string required The type of education. Example: "General"
     *
     * @urlParam id int required The ID of the topic to update. Example: 1
     *
     * @response 200 {
     *   "message": "Topic updated successfully.",
     *   "status": true
     * }
     *
     *
     * @response 400 {
     *   "message": "Validation failed for the provided data.",
     *   "status": false
     * }
     */
    public function update(Request $request, $id)
    {
        try {
            $user_type = auth()->user()->user_type ?? 'Global';
            $user_country = auth()->user()->country ?? null;

            if ($user_type === 'Global') {
                $request->validate([
                    'topic_name' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('topics')->ignore($id)->where(function ($query) use ($request) {
                            return $query->where('education_type', $request->education_type)
                                ->where('country_id', $request->country_id);
                        }),
                    ],
                    'education_type' => 'required|string|max:255',
                    'country_id' => 'required|exists:countries,id',
                ]);
                $country_id = $request->country_id;
            } else {
                $request->merge(['country_id' => $user_country]);
                $request->validate([
                    'topic_name' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('topics')->ignore($id)->where(function ($query) use ($request) {
                            return $query->where('education_type', $request->education_type)
                                ->where('country_id', $request->country_id);
                        }),
                    ],
                    'education_type' => 'required|string|max:255',
                ]);
                $country_id = $user_country;
            }

            $topic = Topic::findOrFail($id);
            $topic->topic_name = $request->topic_name;
            $topic->education_type = $request->education_type;
            $topic->country_id = $country_id;
            $topic->save();

            return response()->json([
                'message' => 'Topic updated successfully.',
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to update topic: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update topic. Please try again later.',
                'status' => false
            ], 201);
        }
    }

    /**
     * Delete Topic
     *
     * @urlParam id int required The ID of the topic. Example: 1
     *
     * @response 200 {
     *   "message": "Topic deleted successfully.",
     *   "status": true
     * }
     *
     */
    public function delete(Request $request, $id)
    {
        try {

            $topic = Topic::findOrFail($id);
            Log::info($topic->topic_name . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
            $topic->delete();
            return response()->json([
                'message' => 'Topic deleted successfully.',
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete topic: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete topic. Please try again later.',
                'status' => false
            ], 201);
        }
    }
}
