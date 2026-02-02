<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Services\NotificationService;

/**
 * @group Meetings
 *
 * @authenticated
 */

class MeetingController extends Controller
{
    /**
     * List All Meetings
     * @queryParam search string optional for search. Example: "abc"
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
    public function index(Request $request)
    {
        try {
            // Fetch the search query from the request
            $searchQuery = $request->get('search');

            $user_type = auth()->user()->user_type ?? 'Global';
            $user_country = auth()->user()->country ?? null;

            // Apply the search filter if searchQuery is provided
            $meetingsQuery = Meeting::with('user')
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    $query->where('title', 'like', "%{$searchQuery}%")
                        ->orWhere('description', 'like', "%{$searchQuery}%");
                });

            // Apply country scope for non-Global users
            if ($user_type !== 'Global' && $user_country) {
                $meetingsQuery->where('country_id', $user_country);
            }

            $meetings = $meetingsQuery->orderBy('id', 'desc')->paginate(15);

            return response()->json($meetings, 200);
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
     * @bodyParam create_zoom boolean optional Use Zoom to generate meeting. Example: true
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
        $user_type = auth()->user()->user_type ?? 'Global';
        $user_country = auth()->user()->country ?? null;

        if ($user_type === 'Global') {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'description' => 'nullable|string',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'meeting_link' => 'nullable|url',
                'create_zoom' => 'nullable|boolean',
                'country_id' => 'required|exists:countries,id',
            ]);
            $country_id = $request->get('country_id');
        } else {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'description' => 'nullable|string',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'meeting_link' => 'nullable|url',
                'create_zoom' => 'nullable|boolean',
            ]);
            $country_id = $user_country;
            $request->merge(['country_id' => $country_id]);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 201);
        }

        try {
            $meetingData = [
                'user_id' => auth()->id(),
                'time_zone' => auth()->user()->time_zone,
                'title' => $request->title,
                'description' => $request->description,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'meeting_link' => $request->meeting_link,
                'country_id' => $country_id,
            ];

            // Create Zoom meeting if requested
            $createZoom = (bool)$request->input('create_zoom', false);
            if ($createZoom) {
                try {
                    $zoom = $this->createZoomMeeting(
                        $request->title,
                        $request->start_time,
                        $request->end_time,
                        strip_tags($request->description ?? ''),
                        auth()->user()->time_zone
                    );
                    if (!empty($zoom['join_url'])) {
                        $meetingData['meeting_link'] = $zoom['join_url'];
                    }
                } catch (\Throwable $e) {
                    Log::warning('Zoom meeting creation failed (API): ' . $e->getMessage());
                    // fallback: keep provided link (if any)
                }
            }

            $meeting = Meeting::create($meetingData);

            $userName = Auth::user()->getFullNameAttribute();
            $noti = NotificationService::notifyAllUsers('New Meeting created by ' . $userName, 'meeting');

            return response()->json(['message' => 'Meeting created successfully.', 'data' => $meeting], 200);
        } catch (\Exception $e) {
            Log::error('Failed to create meeting (API): ' . $e->getMessage());
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
            $user_type = auth()->user()->user_type ?? 'Global';
            $user_country = auth()->user()->country ?? null;

            if ($user_type == 'Global') {
                $meeting = Meeting::with('user')->findOrFail($id);
            } else {
                $meeting = Meeting::with('user')->where('country_id', $user_country)->findOrFail($id);
            }

            return response()->json(['data' => $meeting], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Meeting not found.'], 404);
        } catch (\Exception $e) {
            Log::error('Failed to load meeting details (API): ' . $e->getMessage());
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
     * @bodyParam create_zoom boolean optional Use Zoom to generate meeting on update. Example: true
     *
     * @response 200 {
     *   "message": "Meeting updated successfully.",
     *   "status": true
     * }
     */
    public function update(Request $request, $id)
    {
        $user_type = auth()->user()->user_type ?? 'Global';
        $user_country = auth()->user()->country ?? null;

        if ($user_type === 'Global') {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'meeting_link' => 'nullable|url',
                'create_zoom' => 'nullable|boolean',
                'country_id' => 'required|exists:countries,id',
            ]);
            $country_id = $request->get('country_id');
        } else {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'meeting_link' => 'nullable|url',
                'create_zoom' => 'nullable|boolean',
            ]);
            $country_id = $user_country;
            $request->merge(['country_id' => $country_id]);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // Use 422 for validation errors
        }

        try {
            // Allow updates by owner or SUPER ADMIN
            $meeting = Meeting::findOrFail($id);
            if ($meeting->user_id !== auth()->id() && !auth()->user()->hasNewRole('SUPER ADMIN')) {
                return response()->json(['error' => 'Meeting not found.'], 404);
            }

            // Ensure country scope for non-Global
            if ($user_type !== 'Global' && $meeting->country_id != $country_id) {
                return response()->json(['error' => 'Meeting not found.'], 404);
            }

            $data = $request->only(['title', 'description', 'start_time', 'end_time', 'meeting_link']);
            $data['time_zone'] = auth()->user()->time_zone;
            $data['country_id'] = $country_id;

            // Optionally create Zoom meeting on update
            $createZoom = (bool)$request->input('create_zoom', false);
            if ($createZoom) {
                try {
                    $zoom = $this->createZoomMeeting(
                        $request->title,
                        $request->start_time,
                        $request->end_time,
                        strip_tags($request->description ?? ''),
                        auth()->user()->time_zone
                    );
                    if (!empty($zoom['join_url'])) {
                        $data['meeting_link'] = $zoom['join_url'];
                    }
                } catch (\Throwable $e) {
                    Log::warning('Zoom meeting creation failed (API update): ' . $e->getMessage());
                }
            }

            $meeting->update($data);

            return response()->json([
                'message' => 'Meeting updated successfully.',
                'data' => $meeting
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Meeting not found.'], 404);
        } catch (\Exception $e) {
            Log::error('Failed to update meeting (API): ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update meeting.'], 500);
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
            $meeting = Meeting::findOrFail($id);

            // Allow deletion by owner or SUPER ADMIN
            if ($meeting->user_id !== auth()->id() && !auth()->user()->hasNewRole('SUPER ADMIN')) {
                return response()->json(['error' => 'Meeting not found.'], 201);
            }

            $meeting->delete();

            return response()->json(['message' => 'Meeting deleted successfully.'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Meeting not found.'], 201);
        } catch (\Exception $e) {
            Log::error('Failed to delete meeting (API): ' . $e->getMessage());
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
            $user_type = auth()->user()->user_type ?? 'Global';
            $user_country = auth()->user()->country ?? null;

            if ($user_type == 'Global') {
                $meetings = Meeting::orderBy('id', 'desc')->get(['id', 'title', 'description', 'start_time as start', 'end_time as end', 'meeting_link']);
            } else {
                $meetings = Meeting::where('country_id', $user_country)->orderBy('id', 'desc')->get(['id', 'title', 'description', 'start_time as start', 'end_time as end', 'meeting_link']);
            }

            return response()->json(['message' => 'Calender data fetched successfully.', 'data' => $meetings], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Meetings not found.'], 404);
        } catch (\Exception $e) {
            Log::error('Failed to load meeting calender data (API): ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load meeting calender data.'], 201);
        }
    }

    /**
     * Generate Zoom Web SDK signature and meeting details for API clients.
     *
     * @bodyParam meeting_id int required The ID of the meeting. Example: 1
     * @response 200 {
     *     "status": true,
     *     "signature": "....",
     *     "sdkKey": "ABC",
     *     "meetingNumber": "123456789",
     *     "password": null,
     *     "topic": "My Meeting",
     *     "role": 0,
     *     "userName": "John Doe",
     *     "userEmail": "john@example.com"
     * }
     * @response 201 {
     *     "status": false,
     *     "message": "This meeting is not a Zoom meeting."
     * }
     */
    public function zoomSignature(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'meeting_id' => 'required|integer|exists:meetings,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 201);
            }

            $user_type = auth()->user()->user_type ?? 'Global';
            $user_country = auth()->user()->country ?? null;

            if ($user_type == 'Global') {
                $meeting = Meeting::findOrFail($request->meeting_id);
            } else {
                $meeting = Meeting::where('country_id', $user_country)->findOrFail($request->meeting_id);
            }

            if (!auth()->user()->can('View Meeting Schedule')) {
                return response()->json(['status' => false, 'message' => 'You do not have permission to access this meeting.'], 201);
            }

            $meetingLink = $meeting->meeting_link ?? '';
            if (!$this->isZoomLink($meetingLink)) {
                return response()->json(['status' => false, 'message' => 'This meeting is not a Zoom meeting.'], 201);
            }
            $meetingNumber = $this->parseZoomMeetingIdFromUrl($meetingLink);
            if (!$meetingNumber) {
                return response()->json(['status' => false, 'message' => 'Invalid Zoom meeting link.'], 201);
            }

            // Fetch meeting details: password & topic
            $password = null;
            $topic = $meeting->title;
            try {
                $token = $this->zoomAccessToken();
                if ($token) {
                    $resp = Http::withToken($token)->get("https://api.zoom.us/v2/meetings/{$meetingNumber}");
                    if ($resp->successful()) {
                        $password = $resp->json('password');
                        $topic = $resp->json('topic') ?: $topic;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Zoom meeting fetch failed (API): ' . $e->getMessage());
            }

            $sdkKey = env('ZOOM_SDK_KEY');
            $sdkSecret = env('ZOOM_SDK_SECRET');
            if (!$sdkKey || !$sdkSecret) {
                return response()->json(['status' => false, 'message' => 'Zoom SDK credentials missing.'], 201);
            }

            $role = (auth()->id() === $meeting->user_id || auth()->user()->hasNewRole('SUPER ADMIN')) ? 1 : 0;
            $ts = round(microtime(true) * 1000) - 30000;
            $msg = base64_encode($sdkKey . $meetingNumber . $ts . $role);
            $hash = base64_encode(hash_hmac('sha256', $msg, $sdkSecret, true));
            $signature = base64_encode($sdkKey . '.' . $meetingNumber . '.' . $ts . '.' . $role . '.' . $hash);

            return response()->json([
                'status' => true,
                'signature' => $signature,
                'sdkKey' => $sdkKey,
                'meetingNumber' => (string)$meetingNumber,
                'password' => $password,
                'topic' => $topic,
                'role' => $role,
                'userName' => Auth::user()->full_name ?? (Auth::user()->first_name . ' ' . Auth::user()->last_name),
                'userEmail' => Auth::user()->email,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to generate zoom signature (API): ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate zoom signature.'], 201);
        }
    }

    // -------------------- Zoom helpers for API --------------------

    protected function isZoomLink(?string $url): bool
    {
        if (!$url) return false;
        return (bool)preg_match('/zoom\.us\/j\/(\d+)/i', $url);
    }

    protected function parseZoomMeetingIdFromUrl(?string $url): ?string
    {
        if (!$url) return null;
        if (preg_match('/zoom\.us\/j\/(\d+)/i', $url, $m)) {
            return $m[1];
        }
        return null;
    }

    protected function zoomAccessToken(): ?string
    {
        $accountId = env('ZOOM_ACCOUNT_ID');
        $clientId = env('ZOOM_CLIENT_ID');
        $clientSecret = env('ZOOM_CLIENT_SECRET');

        if (!$accountId || !$clientId || !$clientSecret) {
            Log::warning('Zoom OAuth credentials missing.');
            return null;
        }

        $url = 'https://zoom.us/oauth/token?grant_type=account_credentials&account_id=' . urlencode($accountId);
        $resp = Http::withBasicAuth($clientId, $clientSecret)->asForm()->post($url);

        if (!$resp->successful()) {
            Log::error('Zoom OAuth token failed: ' . $resp->body());
            return null;
        }

        return $resp->json('access_token');
    }

    protected function createZoomMeeting(string $title, string $start_time, string $end_time, string $agenda, ?string $timezone = null): array
    {
        $token = $this->zoomAccessToken();
        if (!$token) {
            throw new \RuntimeException('Zoom access token not available.');
        }

        $start = Carbon::parse($start_time);
        $end = Carbon::parse($end_time);
        $duration = max(15, $end->diffInMinutes($start)); // ensure at least 15 min

        $payload = [
            'topic' => $title,
            'type' => 2,
            'start_time' => $start->toIso8601String(),
            'duration' => $duration,
            'timezone' => $timezone ?: 'UTC',
            'agenda' => Str::limit($agenda ?? '', 200),
            'settings' => [
                'host_video' => true,
                'participant_video' => true,
                'join_before_host' => true,
                'mute_upon_entry' => false,
                'waiting_room' => false,
                'approval_type' => 0,
                'audio' => 'voip',
                'auto_recording' => 'none',
            ],
        ];

        $resp = Http::withToken($token)->post('https://api.zoom.us/v2/users/me/meetings', $payload);
        if (!$resp->successful()) {
            throw new \RuntimeException('Zoom meeting create failed: ' . $resp->body());
        }
        $data = $resp->json();

        return [
            'id' => (string)($data['id'] ?? ''),
            'join_url' => $data['join_url'] ?? null,
            'start_url' => $data['start_url'] ?? null,
            'password' => $data['password'] ?? null,
        ];
    }
}
