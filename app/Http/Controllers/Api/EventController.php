<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group Events
 * 
 * @authenticated
 */

class EventController extends Controller
{
    /**
     * List All events
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "user_id": 2,
     *             "title": "Project Sync",
     *             "description": "Weekly project sync event.",
     *             "start": "2024-11-10T09:00:00.000000Z",
     *             "end": "2024-11-10T10:00:00.000000Z",
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
     *     "error": "Failed to load events."
     * }
     */
    public function index()
    {
        try {
            $events = Event::with('user')->orderBy('id', 'desc')->get();

            return response()->json(['data' => $events], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load events.'], 201);
        }
    }

    /**
     * Create event
     *
     * @bodyParam title string required The title of the event. Example: Project Sync
     * @bodyParam description string The description of the event. Example: Weekly project sync event.
     * @bodyParam start datetime required The start time of the event in ISO 8601 format. Example: 2024-11-22T01:25
     * @bodyParam end datetime required The end time of the event in ISO 8601 format. Example: 2024-11-23T01:25
     *
     * @response 200 {
     *     "message": "event created successfully.",
     *     "data": {
     *         "id": 1,
     *         "user_id": 2,
     *         "title": "Project Sync",
     *         "description": "Weekly project sync event.",
     *         "start": "2024-11-22T01:25",
     *         "end": "2024-11-23T01:25",
     *         "created_at": "2024-11-22T01:25",
     *         "updated_at": "2024-11-22T01:25"
     *     }
     * }
     * @response 201 {
     *     "error": "Failed to create event."
     * }
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'start' => 'required|date',
            'end' => 'required|date|after:start'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 201);
        }

        try {
            $event = Event::create([
                'user_id' => auth()->id(),
                'title' => $request->title,
                'description' => $request->description,
                'start' => $request->start,
                'end' => $request->end,
            ]);

            return response()->json(['message' => 'event created successfully.', 'data' => $event], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create event.' . $e], 201);
        }
    }

    /**
     * Single event Details
     *
     * @urlParam id int required The ID of the event. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "user_id": 2,
     *         "title": "Project Sync",
     *         "description": "Weekly project sync event.",
     *         "start": "2024-11-10T09:00:00.000000Z",
     *         "end": "2024-11-10T10:00:00.000000Z",
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
     *     "error": "event not found."
     * }
     */
    public function show($id)
    {
        try {
            $event = Event::with('user')->findOrFail($id);

            return response()->json(['data' => $event], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'event not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load event details.'], 201);
        }
    }


     /**
     * Update event
     * @urlParam id int required The ID of the event. Example: 1
     * @bodyParam title string required The title of the event. Example: Project Sync Update
     * @bodyParam description string The description of the event. Example: Updated project sync event.
     * @bodyParam start datetime required The updated start time in ISO 8601 format. Example: 2024-11-22T01:25
     * @bodyParam end datetime required The updated end time in ISO 8601 format. Example: 2024-11-23T01:25
     * 
     * @response 200 {
     *   "message": "event updated successfully.",
     *   "status": true
     * }
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'start' => 'required|date',
            'end' => 'required|date|after:start'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 201);
        }

        try {
            $event = Event::where('user_id', auth()->id())->findOrFail($id);

            $event->update($request->only(['title', 'description', 'start', 'end']));

            return response()->json(['message' => 'event updated successfully.', 'data' => $event], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update event.'], 201);
        }
    }

    /**
     * Delete event
     * @urlParam id int required The ID of the event. Example: 1
     * 
     * @response 200 {
     *   "message": "event deleted successfully",
     *   "status": true
     * }
     */
    public function destroy($id)
    {
        try {
            $event = Event::where('user_id', auth()->id())->findOrFail($id);
            $event->delete();

            return response()->json(['message' => 'event deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete event.'], 201);
        }
    }


    /**
     * Events Calender Data
     *
     *
     * @response 200 {
     *    "message": "Calender data fetched successfully.",
     *    "data": [
     *        {
     *            "id": 15,
     *            "title": "Project Sync",
     *            "description": "Weekly project sync event.",
     *            "start": "2024-11-10T09:00:00.000000Z",
     *            "end": "2024-11-10T10:00:00.000000Z",
     *        },
     *        {
     *            "id": 13,
     *            "title": "fourth event",
     *            "description": "afd",
     *            "start": "2024-10-01T18:57",
     *            "end": "2024-10-01T18:58",
     *        },
     *        {
     *            "id": 12,
     *            "title": "Third event",
     *            "description": "fd",
     *            "start": "2024-10-01T18:57",
     *            "end": "2024-10-01T18:58",
     *        },
     *        {
     *            "id": 11,
     *            "title": "Second event",
     *            "description": "adsf",
     *            "start": "2024-10-01T18:56",
     *            "end": "2024-10-01T18:57",
     *        }
     *    ]
     * }
     * @response 404 {
     *     "error": "events not found."
     * }
     */
    public function fetchCalenderData()
    {
        try {
            $events = Event::orderBy('id', 'desc')->get(['id', 'title', 'description', 'start as start', 'end as end']);
            return response()->json(['message' => 'Calender data fetched successfully.', 'data' => $events], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'events not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load event calender data.'], 201);
        }
    }


}
