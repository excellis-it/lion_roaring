<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

/**
 * @group Events
 *
 * @authenticated
 */

class EventController extends Controller
{
    /**
     * List All events
     * @queryParam search string optional for search. Example: "abc"
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
    public function index(Request $request)
    {
        try {
            if (!auth()->user()->can('Manage Event')) {
                return response()->json(['status' => false, 'message' => 'Permission denied.'], 403);
            }

            // Fetch the search query from the request
            $searchQuery = trim((string) $request->get('search', ''));
            $searchQuery = $searchQuery !== '' ? $searchQuery : null;

            $userType = auth()->user()->user_type ?? null;
            $userCountry = auth()->user()->country ?? null;

            $query = Event::with('user')
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    $query->where('title', 'like', "%{$searchQuery}%")
                        ->orWhere('description', 'like', "%{$searchQuery}%");
                });

            if ($userType !== 'Global') {
                $query->where('country_id', $userCountry);
            }

            $events = $query->orderBy('id', 'desc')->paginate(15);

            return response()->json($events, 200);
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
        if (!auth()->user()->can('Create Event')) {
            return response()->json(['status' => false, 'message' => 'Permission denied.'], 403);
        }

        // Determine country based on user type
        $countryId = auth()->user()->user_type === 'Global' ? $request->country_id : auth()->user()->country;

        // Normalize send_notification checkbox variants
        $sendNotification = $request->has('send_notification') && ($request->send_notification === 'on' || $request->send_notification === true || $request->send_notification === '1');

        $request->merge(['country_id' => $countryId, 'send_notification' => $sendNotification]);

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'country_id' => 'required|exists:countries,id',
            'type' => 'required|in:free,paid',
            'capacity' => 'nullable|integer|min:1',
        ];

        if ($request->type === 'paid') {
            $rules['price'] = 'required|numeric|min:0.01';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 201);
        }

        try {
            $event = new Event();
            $event->user_id = Auth::id();
            $event->time_zone = auth()->user()->time_zone;
            $event->title = $request->title;
            $event->description = $request->description;
            $event->start = $request->start;
            $event->end = $request->end;
            $event->country_id = $request->country_id;
            $event->type = $request->type;
            $event->price = $request->type === 'paid' ? $request->price : null;
            $event->capacity = $request->capacity;
            $event->send_notification = $sendNotification;

            // Set encrypted event link if provided
            if ($request->event_link) {
                $event->setEncryptedLink($request->event_link);
            }

            $event->save();

            // Send notifications if enabled
            if ($event->send_notification) {
                $userName = Auth::user()->getFullNameAttribute();
                $message = 'New Live Event created by ' . $userName . ': ' . $event->title;
                $this->sendNotifications($event, $message);
            }

            return response()->json(['message' => 'Event created successfully.', 'event' => $event, 'status' => true], 200);
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
            $userType = auth()->user()->user_type ?? null;
            $userCountry = auth()->user()->country ?? null;

            if ($userType === 'Global') {
                $event = Event::with('user')->findOrFail($id);
            } else {
                $event = Event::with('user')->where('country_id', $userCountry)->findOrFail($id);
            }

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
        if (!auth()->user()->can('Edit Event')) {
            return response()->json(['status' => false, 'message' => 'Permission denied.'], 403);
        }

        // Determine country based on user type
        $countryId = auth()->user()->user_type === 'Global' ? $request->country_id : auth()->user()->country;
        $request->merge(['country_id' => $countryId]);

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'country_id' => 'required|exists:countries,id',
            'type' => 'required|in:free,paid',
            'capacity' => 'nullable|integer|min:1',
        ];

        if ($request->type === 'paid') {
            $rules['price'] = 'required|numeric|min:0.01';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 201);
        }

        try {
            $event = Event::findOrFail($id);

            if ($event->user_id != auth()->id() && !auth()->user()->hasNewRole('SUPER ADMIN')) {
                return response()->json(['status' => false, 'message' => 'You can only edit your own events.'], 403);
            }

            $event->title = $request->title;
            $event->time_zone = auth()->user()->time_zone;
            $event->description = $request->description;
            $event->start = $request->start;
            $event->end = $request->end;
            $event->country_id = $request->country_id;
            $event->type = $request->type;
            $event->price = $request->type === 'paid' ? $request->price : null;
            $event->capacity = $request->capacity;

            if ($request->has('event_link')) {
                $event->setEncryptedLink($request->event_link);
            }

            $event->update();

            return response()->json(['message' => 'Event updated successfully.', 'event' => $event, 'status' => true], 200);
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
        if (!auth()->user()->can('Delete Event')) {
            return response()->json(['status' => false, 'message' => 'Permission denied.'], 403);
        }

        try {
            $event = Event::findOrFail($id);

            if ($event->user_id == auth()->id() || auth()->user()->hasNewRole('SUPER ADMIN')) {
                Log::info($event->title . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
                $event->delete();
                return response()->json(['message' => 'Event deleted successfully.'], 200);
            }

            return response()->json(['status' => false, 'message' => 'You can only delete your own events.'], 403);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'event not found.'], 404);
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
            if (!auth()->user()->can('Manage Event')) {
                return response()->json(['status' => false, 'message' => 'Permission denied.'], 403);
            }

            $userType = auth()->user()->user_type ?? null;
            $userCountry = auth()->user()->country ?? null;

            $query = Event::orderBy('id', 'desc');
            if ($userType !== 'Global') {
                $query->where('country_id', $userCountry);
            }

            $events = $query->get()->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'start' => $event->start,
                    'end' => $event->end,
                    'type' => $event->type ?? 'free',
                    'price' => $event->price,
                    'capacity' => $event->capacity,
                    'country_id' => $event->country_id,
                    'user_id' => $event->user_id,
                    'decrypted_link' => $event->getDecryptedLink(),
                    'is_host' => auth()->id() === $event->user_id,
                    'user_rsvp_status' => \App\Models\EventRsvp::where('event_id', $event->id)->where('user_id', auth()->id())->value('status'),
                    'formatted_start' => $event->formatted_start,
                    'formatted_end' => $event->formatted_end,
                    'timezone' => $event->time_zone,
                ];
            });

            return response()->json(['message' => 'Calender data fetched successfully.', 'data' => $events], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'events not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load event calender data.'], 201);
        }
    }
}
