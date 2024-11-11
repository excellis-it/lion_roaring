<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group Meetings
 * 
 * @authenticated
 */

class MeetingController extends Controller
{
    /**
     * List All Meetings
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "user_id": 2,
     *             "title": "Project Sync",
     *             "description": "Weekly project sync meeting.",
     *             "start_time": "2024-11-10T09:00:00.000000Z",
     *             "end_time": "2024-11-10T10:00:00.000000Z",
     *             "meeting_link": "https://meeting.example.com/xyz123",
     *             "created_at": "2024-11-08T12:00:00.000000Z",
     *             "updated_at": "2024-11-08T12:00:00.000000Z",
     *             "user": {
     *                 "id": 2,
     *                 "name": "John Doe",
     *                 "email": "john@example.com"
     *             }
     *         }
     *     ]
     * }
     * @response 201 {
     *     "error": "Failed to load meetings."
     * }
     */
    public function index()
    {
        try {
            $meetings = Meeting::with('user')->orderBy('id', 'desc')->get();

            return response()->json(['data' => $meetings], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load meetings.'], 201);
        }
    }

    /**
     * Create Meeting
     *
     * @bodyParam title string required The title of the meeting. Example: Project Sync
     * @bodyParam description string The description of the meeting. Example: Weekly project sync meeting.
     * @bodyParam start_time datetime required The start time of the meeting in ISO 8601 format. Example: 2024-11-22T01:25
     * @bodyParam end_time datetime required The end time of the meeting in ISO 8601 format. Example: 2024-11-22T02:25
     * @bodyParam meeting_link string The link to join the meeting. Example: https://meeting.example.com/xyz123
     *
     * @response 200 {
     *     "message": "Meeting created successfully.",
     *     "data": {
     *         "id": 1,
     *         "user_id": 2,
     *         "title": "Project Sync",
     *         "description": "Weekly project sync meeting.",
     *         "start_time": "2024-11-22T01:25",
     *         "end_time": "2024-11-22T02:25",
     *         "meeting_link": "https://meeting.example.com/xyz123",
     *         "created_at": "2024-11-08T12:00:00.000000Z",
     *         "updated_at": "2024-11-08T12:00:00.000000Z"
     *     }
     * }
     * @response 201 {
     *     "error": "Failed to create meeting."
     * }
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'meeting_link' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 201);
        }

        try {
            $meeting = Meeting::create([
                'user_id' => auth()->id(),
                'title' => $request->title,
                'description' => $request->description,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'meeting_link' => $request->meeting_link,
            ]);

            return response()->json(['message' => 'Meeting created successfully.', 'data' => $meeting], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create meeting.'], 201);
        }
    }

    /**
     * Single Meeting Details
     *
     * @urlParam id int required The ID of the meeting. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "user_id": 2,
     *         "title": "Project Sync",
     *         "description": "Weekly project sync meeting.",
     *         "start_time": "2024-11-10T09:00:00.000000Z",
     *         "end_time": "2024-11-10T10:00:00.000000Z",
     *         "meeting_link": "https://meeting.example.com/xyz123",
     *         "created_at": "2024-11-08T12:00:00.000000Z",
     *         "updated_at": "2024-11-08T12:00:00.000000Z",
     *         "user": {
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
     *         }
     *     }
     * }
     * @response 404 {
     *     "error": "Meeting not found."
     * }
     */
    public function show($id)
    {
        try {
            $meeting = Meeting::with('user')->findOrFail($id);

            return response()->json(['data' => $meeting], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Meeting not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load meeting details.'], 201);
        }
    }


     /**
     * Update Meeting
     * @urlParam id int required The ID of the meeting. Example: 1
     * @bodyParam title string required The title of the meeting. Example: Project Sync Update
     * @bodyParam description string The description of the meeting. Example: Updated project sync meeting.
     * @bodyParam start_time datetime required The updated start time in ISO 8601 format. Example: 2024-11-11T09:00:00.000000Z
     * @bodyParam end_time datetime required The updated end time in ISO 8601 format. Example: 2024-11-11T10:00:00.000000Z
     * @bodyParam meeting_link string The updated link for the meeting. Example: https://meeting.example.com/updated123
     * 
     * @response 200 {
     *   "message": "Meeting updated successfully.",
     *   "status": true
     * }
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'meeting_link' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 201);
        }

        try {
            $meeting = Meeting::where('user_id', auth()->id())->findOrFail($id);

            $meeting->update($request->only(['title', 'description', 'start_time', 'end_time', 'meeting_link']));

            return response()->json(['message' => 'Meeting updated successfully.', 'data' => $meeting], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update meeting.'], 201);
        }
    }

    /**
     * Delete Meeting
     * @urlParam id int required The ID of the meeting. Example: 1
     * 
     * @response 200 {
     *   "message": "Meeting deleted successfully.",
     *   "status": true
     * }
     */
    public function destroy($id)
    {
        try {
            $meeting = Meeting::where('user_id', auth()->id())->findOrFail($id);
            $meeting->delete();

            return response()->json(['message' => 'Meeting deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete meeting.'], 201);
        }
    }


    /**
     * Meetings Calender Data
     *
     *
     * @response 200 {
     *    "message": "Calender data fetched successfully.",
     *    "data": [
     *        {
     *            "id": 15,
     *            "title": "Project Sync",
     *            "description": "Weekly project sync meeting.",
     *            "start": "2024-11-10T09:00:00.000000Z",
     *            "end": "2024-11-10T10:00:00.000000Z",
     *            "meeting_link": "https://meeting.example.com/xyz123"
     *        },
     *        {
     *            "id": 13,
     *            "title": "fourth meeting",
     *            "description": "afd",
     *            "start": "2024-10-01T18:57",
     *            "end": "2024-10-01T18:58",
     *            "meeting_link": null
     *        },
     *        {
     *            "id": 12,
     *            "title": "Third meeting",
     *            "description": "fd",
     *            "start": "2024-10-01T18:57",
     *            "end": "2024-10-01T18:58",
     *            "meeting_link": null
     *        },
     *        {
     *            "id": 11,
     *            "title": "Second meeting",
     *            "description": "adsf",
     *            "start": "2024-10-01T18:56",
     *            "end": "2024-10-01T18:57",
     *            "meeting_link": null
     *        }
     *    ]
     * }
     * @response 404 {
     *     "error": "Meetings not found."
     * }
     */
    public function fetchCalenderData()
    {
        try {
            $meetings = Meeting::orderBy('id', 'desc')->get(['id', 'title', 'description', 'start_time as start', 'end_time as end', 'meeting_link']);
            return response()->json(['message' => 'Calender data fetched successfully.', 'data' => $meetings], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Meetings not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load meeting calender data.'], 201);
        }
    }


}
