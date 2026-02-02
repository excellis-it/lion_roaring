<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PrivateCollaboration;
use App\Models\CollaborationInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\CollaborationInvitation as CollaborationInvitationMail;
use App\Mail\CollaborationAccepted;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

/**
 * @group Private Collaborations
 *
 * APIs to manage private collaborations (create, list, invite, accept)
 *
 * @authenticated
 */
class PrivateCollaborationController extends Controller
{
    /**
     * List All Private Collaborations
     *
     * @queryParam search string optional for search filter. Example: "project"
     *
     * @response 200 {
     *   "data": [
     *       {
     *           "id": 1,
     *           "user_id": 2,
     *           "title": "Project Sync",
     *           "description": "Weekly sync",
     *           "start_time": "2024-11-10T09:00:00",
     *           "end_time": "2024-11-10T10:00:00",
     *           "meeting_link": "https://zoom.us/j/123456789",
     *           "is_zoom": true,
     *           "user": { "id": 2, "full_name": "John Doe", "email": "john@example.com" }
     *       }
     *   ]
     * }
     * @response 201 {
     *   "error": "Failed to load collaborations."
     * }
     */
    public function index(Request $request)
    {
        try {
            if (!auth()->user()->can('Manage Private Collaboration')) {
                return response()->json(['status' => false, 'message' => 'Permission denied.'], 403);
            }

            $searchQuery = trim((string) $request->get('search', ''));
            $searchQuery = $searchQuery !== '' ? $searchQuery : null;

            $userType = auth()->user()->user_type ?? null;
            $userCountry = auth()->user()->country ?? null;

            $query = PrivateCollaboration::with(['user', 'invitations.user'])
                ->where(function ($query) {
                    $query->where('user_id', auth()->id())
                        ->orWhereHas('invitations', function ($q) {
                            $q->where('user_id', auth()->id());
                        });
                })
                ->when($searchQuery, function ($q) use ($searchQuery) {
                    $q->where(function ($sq) use ($searchQuery) {
                        $sq->where('title', 'like', "%{$searchQuery}%")
                            ->orWhere('description', 'like', "%{$searchQuery}%");
                    });
                });

            if ($userType !== 'Global') {
                $query->where('country_id', $userCountry);
            }

            $collaborations = $query->orderBy('id', 'desc')->paginate(15);

            return response()->json(['status' => true, 'data' => $collaborations], 200);
        } catch (\Exception $e) {
            Log::error('API - Private Collaboration index error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to load collaborations.'], 201);
        }
    }

    /**
     * Create a private collaboration
     * @bodyParam title string required The title of the collaboration. Example: "Team for Project Z"
     * @bodyParam description string optional The description. Example: "Meeting to discuss project Z"
     * @bodyParam start_time datetime required The start time in ISO 8601 format. Example: 2024-11-22T10:00:00
     * @bodyParam end_time datetime required The end time in ISO 8601 format. Example: 2024-11-22T11:00:00
     * @bodyParam meeting_link string optional Meeting link (if not creating Zoom meeting). Example: https://meet.example.com/abc
     * @bodyParam create_zoom boolean optional Create a Zoom meeting automatically. Example: true
     *
     * @response 200 {
     *     "message": "Private collaboration created successfully.",
     *     "data": {"id": 1, "title": "Team for Project Z"}
     * }
     * @response 201 {
     *     "error": "Failed to create collaboration."
     * }
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('Create Private Collaboration')) {
            return response()->json(['status' => false, 'message' => 'Permission denied.'], 403);
        }

        // Determine country based on user type
        $countryId = auth()->user()->user_type === 'Global' ? $request->country_id : auth()->user()->country;
        $request->merge(['country_id' => $countryId]);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'meeting_link' => 'nullable|url',
            'create_zoom' => 'nullable|boolean',
            'country_id' => 'required|exists:countries,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 201);
        }

        try {
            $data = [
                'country_id' => $request->country_id,
                'user_id' => auth()->id(),
                'title' => $request->title,
                'description' => $request->description,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'meeting_link' => $request->meeting_link,
                'create_zoom' => $request->create_zoom ?? 0,
                'is_zoom' => 0,
            ];

            if ($request->create_zoom == 1) {
                try {
                    $zoomMeeting = $this->createZoomMeeting(
                        $request->title,
                        $request->start_time,
                        $request->end_time,
                        $request->description ?? '',
                        auth()->user()->time_zone ?? 'UTC'
                    );
                    if (isset($zoomMeeting['join_url'])) {
                        $data['meeting_link'] = $zoomMeeting['join_url'];
                        $data['is_zoom'] = 1;
                    }
                } catch (\Exception $e) {
                    Log::warning('Zoom create failed (API Private Collaboration): ' . $e->getMessage());
                }
            }

            $collaboration = PrivateCollaboration::create($data);

            // Send invitations to eligible users & notify
            $this->sendInvitationsToEligibleUsers($collaboration);
            $userName = Auth::user()->full_name ?? (Auth::user()->first_name . ' ' . Auth::user()->last_name);
            NotificationService::notifyAllUsers('New collaboration created by ' . $userName, 'collaboration');

            return response()->json(['status' => true, 'message' => 'Private collaboration created successfully and invitations sent.', 'data' => $collaboration], 200);
        } catch (\Exception $e) {
            Log::error('API - Private Collaboration store error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to create collaboration.'], 201);
        }
    }

    /**
     * Get a single collaboration
     * @urlParam id int required The ID of the collaboration. Example: 1
     *
     * @response 200 {
     *    "data": {"id": 1, "title": "Project Sync", "invitations": []},
     *    "is_creator": true,
     *    "has_accepted": true
     * }
     * @response 404 { "error": "Collaboration not found." }
     */
    public function show($id)
    {
        try {
            if (!auth()->user()->can('View Private Collaboration')) {
                return response()->json(['status' => false, 'message' => 'Permission denied.'], 403);
            }

            $userType = auth()->user()->user_type ?? null;
            $userCountry = auth()->user()->country ?? null;

            if ($userType === 'Global') {
                $collaboration = PrivateCollaboration::with(['user', 'invitations.user'])->findOrFail($id);
            } else {
                $collaboration = PrivateCollaboration::with(['user', 'invitations.user'])->where('country_id', $userCountry)->findOrFail($id);
            }

            $isCreator = $collaboration->user_id == auth()->id();
            $invitation = $collaboration->invitations()->where('user_id', auth()->id())->first();
            $hasAccepted = $invitation && $invitation->status == 'accepted';

            if (!$isCreator && !$invitation) {
                return response()->json(['status' => false, 'message' => 'You do not have access to this collaboration.'], 403);
            }

            return response()->json(['status' => true, 'data' => $collaboration, 'is_creator' => $isCreator, 'has_accepted' => $hasAccepted], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => false, 'message' => 'Collaboration not found.'], 404);
        } catch (\Exception $e) {
            Log::error('API - Private Collaboration show error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to load collaboration details.'], 201);
        }
    }

    /**
     * Update a collaboration
     * @urlParam id int required The ID of the collaboration. Example: 1
     * @bodyParam title string required
     * @bodyParam description string
     * @bodyParam start_time datetime required
     * @bodyParam end_time datetime required
     * @bodyParam meeting_link string
     * @bodyParam create_zoom boolean
     *
     * @response 200 {"message": "Private collaboration updated successfully."}
     * @response 404 {"error": "Collaboration not found."}
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('Edit Private Collaboration')) {
            return response()->json(['status' => false, 'message' => 'Permission denied.'], 403);
        }

        // Determine country based on user type
        $countryId = auth()->user()->user_type === 'Global' ? $request->country_id : auth()->user()->country;
        $request->merge(['country_id' => $countryId]);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'meeting_link' => 'nullable|url',
            'country_id' => 'required|exists:countries,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 201);
        }

        try {
            $collaboration = PrivateCollaboration::findOrFail($id);

            if ($collaboration->user_id != auth()->id() && !auth()->user()->hasNewRole('SUPER ADMIN')) {
                return response()->json(['status' => false, 'message' => 'You can only edit your own collaborations.'], 403);
            }

            $data = $request->only(['title', 'description', 'start_time', 'end_time', 'meeting_link', 'country_id']);

            if ($request->create_zoom ?? false) {
                try {
                    $zoomMeeting = $this->createZoomMeeting(
                        $request->title,
                        $request->start_time,
                        $request->end_time,
                        $request->description ?? '',
                        auth()->user()->time_zone ?? 'UTC'
                    );
                    if (isset($zoomMeeting['join_url'])) {
                        $data['meeting_link'] = $zoomMeeting['join_url'];
                        $data['is_zoom'] = 1;
                    }
                } catch (\Exception $e) {
                    Log::warning('Zoom create failed (API update Private Collaboration): ' . $e->getMessage());
                }
            }

            $collaboration->update($data);

            return response()->json(['status' => true, 'message' => 'Private collaboration updated successfully.', 'data' => $collaboration], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => false, 'message' => 'Collaboration not found.'], 404);
        } catch (\Exception $e) {
            Log::error('API - Private Collaboration update error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to update collaboration.'], 500);
        }
    }

    /**
     * Delete a collaboration
     * @urlParam id int required The ID of the collaboration.
     * @response 200 {"message": "Private collaboration deleted successfully.", "status": true}
     * @response 201 {"error": "Failed to delete collaboration."}
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('Delete Private Collaboration')) {
            return response()->json(['status' => false, 'message' => 'Permission denied.'], 403);
        }

        try {
            $collaboration = PrivateCollaboration::findOrFail($id);

            if ($collaboration->user_id == auth()->id() || auth()->user()->hasNewRole('SUPER ADMIN')) {
                $collaboration->delete();
                return response()->json(['status' => true, 'message' => 'Private collaboration deleted successfully.'], 200);
            }

            return response()->json(['status' => false, 'message' => 'You can only delete your own collaborations.'], 403);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => false, 'message' => 'Collaboration not found.'], 404);
        } catch (\Exception $e) {
            Log::error('API - Private Collaboration destroy error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to delete collaboration.'], 201);
        }
    }

    /**
     * Accept invitation for a collaboration
     * @urlParam id int required The ID of the collaboration to accept.
     * @response 200 {"status": true, "message": "Invitation accepted successfully."}
     * @response 403 {"status": false, "message": "You have not been invited to this collaboration."}
     */
    public function acceptInvitation($id)
    {
        try {
            $collaboration = PrivateCollaboration::findOrFail($id);
            $invitation = $collaboration->invitations()->where('user_id', auth()->id())->first();

            if (!$invitation) {
                return response()->json(['status' => false, 'message' => 'You have not been invited to this collaboration.'], 403);
            }

            if ($invitation->status == 'accepted') {
                return response()->json(['status' => false, 'message' => 'You have already accepted this invitation.']);
            }

            $invitation->accept();

            // Notify creator
            try {
                Mail::to($collaboration->user->email)->send(new CollaborationAccepted($collaboration, auth()->user()));
            } catch (\Exception $e) {
                Log::error('Failed to send acceptance email: ' . $e->getMessage());
            }

            try {
                NotificationService::notifyUser(
                    $collaboration->user_id,
                    auth()->user()->full_name . ' has accepted your collaboration invitation: ' . $collaboration->title,
                    'collaboration'
                );
            } catch (\Exception $e) {
                Log::error('Failed to send in-app notification: ' . $e->getMessage());
            }

            return response()->json(['status' => true, 'message' => 'Invitation accepted successfully.'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => false, 'message' => 'Collaboration not found.'], 404);
        } catch (\Exception $e) {
            Log::error('API - Private Collaboration accept invitation error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Something went wrong.'], 201);
        }
    }

    /**
     * Fetch calendar data for private collaborations
     *
     * @response 200 {"message": "Calender data fetched successfully.", "data": []}
     * @response 201 {"error": "Failed to load collaboration calender data."}
     */
    public function fetchCalenderData()
    {
        try {
            if (!auth()->user()->can('Manage Private Collaboration')) {
                return response()->json(['status' => false, 'message' => 'Permission denied.'], 403);
            }

            $userType = auth()->user()->user_type ?? null;
            $userCountry = auth()->user()->country ?? null;

            $query = PrivateCollaboration::with(['user', 'invitations.user'])
                ->where(function ($query) {
                    $query->where('user_id', auth()->id())
                        ->orWhereHas('invitations', function ($q) {
                            $q->where('user_id', auth()->id());
                        });
                });

            if ($userType !== 'Global') {
                $query->where('country_id', $userCountry);
            }

            $collaborations = $query->get()
                ->map(function ($collaboration) {
                    $isCreator = $collaboration->user_id == auth()->id();
                    $invitation = $collaboration->invitations->where('user_id', auth()->id())->first();
                    $hasAccepted = $invitation && $invitation->status == 'accepted';

                    return [
                        'id' => $collaboration->id,
                        'title' => $collaboration->title,
                        'start' => $collaboration->start_time,
                        'end' => $collaboration->end_time,
                        'description' => $collaboration->description,
                        'meeting_link' => ($isCreator || $hasAccepted) ? $collaboration->meeting_link : null,
                        'is_creator' => $isCreator,
                        'has_accepted' => $hasAccepted,
                        'is_zoom' => $collaboration->is_zoom,
                        'created_by' => $collaboration->user->full_name ?? 'N/A',
                    ];
                });

            return response()->json(['status' => true, 'message' => 'Calender data fetched successfully.', 'data' => $collaborations], 200);
        } catch (\Exception $e) {
            Log::error('API - Private Collaboration fetch calender error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to load collaboration calender data.'], 201);
        }
    }

    /**
     * Generate Zoom Web SDK signature for collaboration meeting
     * @bodyParam collaboration_id int required The id of the collaboration. Example: 1
     *
     * @response 200 {
     *     "status": true,
     *     "signature": "...",
     *     "sdkKey": "ABC",
     *     "meetingNumber": "123456789",
     *     "userName": "John Doe",
     *     "userEmail": "john@example.com"
     * }
     * @response 201 {"status": false, "message": "This meeting is not a Zoom meeting."}
     */
    public function zoomSignature(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'collaboration_id' => 'required|integer|exists:private_collaborations,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 201);
        }

        try {
            if (!auth()->user()->can('View Private Collaboration')) {
                return response()->json(['status' => false, 'message' => 'Permission denied.'], 403);
            }

            $collaboration = PrivateCollaboration::findOrFail($request->collaboration_id);
            $meetingLink = $collaboration->meeting_link ?? '';
            if (!$this->isZoomLink($meetingLink)) {
                return response()->json(['status' => false, 'message' => 'This meeting is not a Zoom meeting.'], 201);
            }
            $meetingNumber = $this->parseZoomMeetingIdFromUrl($meetingLink);
            if (!$meetingNumber) {
                return response()->json(['status' => false, 'message' => 'Invalid Zoom meeting URL.'], 201);
            }

            $sdkKey = env('ZOOM_SDK_KEY');
            $sdkSecret = env('ZOOM_SDK_SECRET');
            if (!$sdkKey || !$sdkSecret) {
                return response()->json(['status' => false, 'message' => 'Zoom SDK credentials not configured'], 500);
            }

            $role = ($collaboration->user_id == auth()->id()) ? 1 : 0;
            $iat = time() - 30;
            $exp = $iat + 60 * 60 * 2;
            $payload = [
                'appKey' => $sdkKey,
                'sdkKey' => $sdkKey,
                'mn' => (int)$meetingNumber,
                'role' => $role,
                'iat' => $iat,
                'exp' => $exp,
                'tokenExp' => $exp,
            ];
            $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
            $payloadStr = json_encode($payload);
            $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
            $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payloadStr));
            $signature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, $sdkSecret, true);
            $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
            $jwt = $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;

            return response()->json([
                'status' => true,
                'signature' => $jwt,
                'sdkKey' => $sdkKey,
                'meetingNumber' => $meetingNumber,
                'password' => '',
                'userName' => auth()->user()->full_name ?? 'User',
                'userEmail' => auth()->user()->email ?? '',
            ]);
        } catch (\Exception $e) {
            Log::error('API - Private Collaboration zoom signature error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to generate zoom signature.'], 201);
        }
    }

    protected function sendInvitationsToEligibleUsers($collaboration)
    {
        $users = User::whereHas('roles.permissions', function ($query) {
            $query->where('name', 'Manage Private Collaboration');
        })->where('id', '!=', auth()->id())->get();

        foreach ($users as $user) {
            CollaborationInvitation::create([
                'collaboration_id' => $collaboration->id,
                'user_id' => $user->id,
                'status' => 'pending',
            ]);

            try {
                Mail::to($user->email)->send(new CollaborationInvitationMail($collaboration, $user));
            } catch (\Exception $e) {
                Log::error('Failed to send invitation email to user ' . $user->id . ': ' . $e->getMessage());
            }
        }
    }

    protected function isZoomLink(?string $url): bool
    {
        return $url && (str_contains($url, 'zoom.us/j/') || str_contains($url, 'zoom.us/wc/join/'));
    }

    protected function parseZoomMeetingIdFromUrl(?string $url): ?string
    {
        if (!$url) return null;
        if (preg_match('/\/j\/(\d+)/', $url, $m)) {
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
            return null;
        }

        $response = Http::withBasicAuth($clientId, $clientSecret)
            ->asForm()
            ->post("https://zoom.us/oauth/token", [
                'grant_type' => 'account_credentials',
                'account_id' => $accountId,
            ]);

        if ($response->successful()) {
            return $response->json('access_token');
        }
        return null;
    }

    protected function createZoomMeeting(string $title, string $start_time, string $end_time, string $agenda, ?string $timezone = null): array
    {
        $token = $this->zoomAccessToken();
        if (!$token) {
            throw new \Exception('Unable to obtain Zoom access token');
        }

        $startCarbon = Carbon::parse($start_time);
        $endCarbon = Carbon::parse($end_time);
        $durationMinutes = $startCarbon->diffInMinutes($endCarbon);

        $payload = [
            'topic' => $title,
            'type' => 2,
            'start_time' => $startCarbon->format('Y-m-d\TH:i:s\Z'),
            'duration' => $durationMinutes,
            'timezone' => $timezone ?? 'UTC',
            'agenda' => $agenda,
            'settings' => [
                'host_video' => true,
                'participant_video' => true,
                'join_before_host' => false,
                'mute_upon_entry' => false,
                'watermark' => false,
                'audio' => 'both',
                'auto_recording' => 'none',
            ],
        ];

        $response = Http::withToken($token)->post('https://api.zoom.us/v2/users/me/meetings', $payload);
        if ($response->successful()) {
            return $response->json();
        }
        throw new \Exception('Zoom API error: ' . $response->body());
    }
}
