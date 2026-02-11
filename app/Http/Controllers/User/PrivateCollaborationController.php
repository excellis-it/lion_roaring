<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PrivateCollaboration;
use App\Models\CollaborationInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\CollaborationInvitation as CollaborationInvitationMail;
use App\Mail\CollaborationAccepted;
use App\Models\Country;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class PrivateCollaborationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->check()) {
            abort(403, 'You do not have permission to access this page.');
        }

        $user_type = auth()->user()->user_type;
        $user_country = auth()->user()->country;
        // return $user_country;

            if ($user_type == 'Global') {

                $collaborations = PrivateCollaboration::with(['user', 'invitations.user'])
                    ->where(function ($query) {
                        $query->where('user_id', auth()->id())
                            ->orWhereHas('invitations', function ($q) {
                                $q->where('user_id', auth()->id());
                            });
                    })
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            } else {
                $collaborations = PrivateCollaboration::with(['user', 'invitations.user'])
                    ->where(function ($query) {
                        $query->where('user_id', auth()->id())
                            ->orWhereHas('invitations', function ($q) {
                                $q->where('user_id', auth()->id());
                            });
                    })
                    ->where('country_id', $user_country)
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            }

            return view('user.private_collaboration.list', compact('collaborations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (auth()->user()->can('Create Private Collaboration')) {
            $countries = Country::orderBy('name', 'asc')->get();
            // Eligible users who can be invited (users with Manage Private Collaboration permission)
            $eligibleUsers = User::whereHas('roles.permissions', function ($query) {
                $query->where('name', 'Manage Private Collaboration');
            })->where('id', '!=', auth()->id())->orderBy('first_name')->orderBy('last_name')->get();
            return view('user.private_collaboration.create', compact('countries', 'eligibleUsers'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (auth()->user()->can('Create Private Collaboration')) {
            $country_id = auth()->user()->user_type === 'Global'
                ? $request->country_id
                : auth()->user()->country;

            $request->merge(['country_id' => $country_id]);

            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'meeting_link' => 'nullable|url',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'create_zoom' => 'nullable|boolean',
                'country_id' => 'required|exists:countries,id',
                'invitees' => 'required|array|min:1',
                'invitees.*' => 'integer|exists:users,id',
            ]);

            // Ensure selected invitees are eligible (have "Manage Private Collaboration" permission)
            $invitees = $request->input('invitees', []);
            $eligibleCount = User::whereIn('id', $invitees)
                ->whereHas('roles.permissions', function ($q) {
                    $q->where('name', 'Manage Private Collaboration');
                })
                ->where('id', '!=', auth()->id())
                ->count();

            if (count($invitees) !== $eligibleCount) {
                return response()->json([
                    'status' => false,
                    'message' => 'One or more selected invitees are not eligible for invitation.'
                ], 422);
            }

            $data = $request->only(['title', 'description', 'meeting_link', 'start_time', 'end_time', 'country_id']);
            $data['user_id'] = auth()->id();
            $data['create_zoom'] = $request->create_zoom ?? 0;
            $data['is_zoom'] = 0;

            // If create_zoom is enabled, create a Zoom meeting
            if ($request->create_zoom == 1) {
                try {
                    $zoomMeeting = $this->createZoomMeeting(
                        $request->title,
                        $request->start_time,
                        $request->end_time,
                        $request->description ?? '',
                        'UTC'
                    );

                    if (isset($zoomMeeting['join_url'])) {
                        $data['meeting_link'] = $zoomMeeting['join_url'];
                        $data['is_zoom'] = 1;
                    }
                } catch (\Exception $e) {
                    Log::error('Zoom meeting creation failed: ' . $e->getMessage());
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to create Zoom meeting: ' . $e->getMessage()
                    ], 500);
                }
            }

            $collaboration = PrivateCollaboration::create($data);

            // Send invitations to selected users (invitees[] from the form)
            $invitees = $request->input('invitees', []);
            $this->sendInvitationsToSelectedUsers($collaboration, $invitees);

            // notify selected users via in-app notification
            $userName = Auth::user()->getFullNameAttribute();
            foreach ($invitees as $uid) {
                try {
                    NotificationService::notifyUser($uid, 'New collaboration created by ' . $userName, 'collaboration');
                } catch (\Exception $e) {
                    Log::error('Failed to send in-app notification to user ' . $uid . ': ' . $e->getMessage());
                }
            }

            session()->flash('message', 'Collaboration scheduled successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Private collaboration created successfully and invitations sent.',
                'collaboration' => $collaboration
            ]);
        } else {
            abort(403, 'You do not have permission to perform this action.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if (!auth()->check()) {
            abort(403, 'You do not have permission to access this page.');
        }

        $user_type = auth()->user()->user_type;
        $user_country = auth()->user()->country;

        if ($user_type == 'Global') {
            $collaboration = PrivateCollaboration::with(['user', 'invitations.user'])->findOrFail($id);
        } else {
            $collaboration = PrivateCollaboration::with(['user', 'invitations.user'])->where('country_id', $user_country)->findOrFail($id);
        }

        // Check if user is creator or has been invited
        $isCreator = $collaboration->user_id == auth()->id();
        $invitation = $collaboration->invitations()->where('user_id', auth()->id())->first();
        $hasAccepted = $invitation && $invitation->status == 'accepted';

        if (!$isCreator && !$invitation && !auth()->user()->can('View Private Collaboration')) {
            abort(403, 'You do not have access to this collaboration.');
        }

        $isZoom = $this->isZoomLink($collaboration->meeting_link);
        $zoomMeetingId = $isZoom ? $this->parseZoomMeetingIdFromUrl($collaboration->meeting_link) : null;

        return view('user.private_collaboration.show', compact('collaboration', 'isCreator', 'hasAccepted', 'isZoom', 'zoomMeetingId'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user_type = auth()->user()->user_type;
        $user_country = auth()->user()->country;

        if ($user_type == 'Global') {
            $collaboration = PrivateCollaboration::findOrFail($id);
        } else {
            $collaboration = PrivateCollaboration::where('country_id', $user_country)->findOrFail($id);
        }

        $countries = Country::orderBy('name', 'asc')->get();

        if (auth()->user()->can('Edit Private Collaboration') && auth()->user()->id == $collaboration->user_id || auth()->user()->hasNewRole('SUPER ADMIN')) {
            return view('user.private_collaboration.edit', compact('collaboration', 'countries'));
        } else {
            abort(403, 'You do not have permission to edit this collaboration.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (auth()->user()->can('Edit Private Collaboration')) {

            $country_id = auth()->user()->user_type === 'Global'
                ? $request->country_id
                : auth()->user()->country;

            $request->merge(['country_id' => $country_id]);

            $collaboration = PrivateCollaboration::findOrFail($id);

            if ($collaboration->user_id != auth()->id() && !auth()->user()->hasNewRole('SUPER ADMIN')) {
                abort(403, 'You can only edit your own collaborations.');
            }

            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'meeting_link' => 'nullable|url',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'country_id' => 'required|exists:countries,id',
            ]);

            $data = $request->only(['title', 'description', 'meeting_link', 'start_time', 'end_time', 'country_id']);

            $collaboration->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Private collaboration updated successfully.',
                'id' => $id
            ]);
        } else {
            abort(403, 'You do not have permission to perform this action.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (Auth::user()->can('Delete Private Collaboration')) {
            $collaboration = PrivateCollaboration::findOrFail($id);

            if ($collaboration->user_id == Auth::user()->id || Auth::user()->hasNewRole('SUPER ADMIN')) {
                $collaboration->delete();
                return response()->json([
                    'message' => 'Private collaboration deleted successfully.',
                    'status' => true,
                    'id' => $id
                ]);
            } else {
                abort(403, 'You can only delete your own collaborations.');
            }
        } else {
            abort(403, 'You do not have permission to delete collaborations.');
        }
    }

    /**
     * Accept invitation to collaboration
     */
    public function acceptInvitation($id)
    {
        $collaboration = PrivateCollaboration::findOrFail($id);
        $invitation = $collaboration->invitations()->where('user_id', auth()->id())->first();

        if (!$invitation) {
            return response()->json([
                'status' => false,
                'message' => 'You have not been invited to this collaboration.'
            ], 403);
        }

        if ($invitation->status == 'accepted') {
            return response()->json([
                'status' => false,
                'message' => 'You have already accepted this invitation.'
            ]);
        }

        $invitation->accept();

        // Send email notification to creator
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

        return response()->json([
            'status' => true,
            'message' => 'Invitation accepted successfully.'
        ]);
    }

    /**
     * Fetch data for DataTable (AJAX)
     */
    public function fetchData(Request $request)
    {
        if ($request->ajax()) {
            $user_type = auth()->user()->user_type;
            $user_country = auth()->user()->country;
            $searchQuery = trim((string) $request->get('query', ''));
            $searchQuery = $searchQuery !== '' ? $searchQuery : null;

            if ($user_type == 'Global') {
                $collaborations = PrivateCollaboration::with(['user', 'invitations.user'])
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
                    })
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            } else {
                $collaborations = PrivateCollaboration::with(['user', 'invitations.user'])
                    ->where(function ($query) use ($user_country) {
                        $query->where('user_id', auth()->id())
                            ->orWhereHas('invitations', function ($q) use ($user_country) {
                                $q->where('user_id', auth()->id());
                            });
                    })
                    ->where('country_id', $user_country)
                    ->when($searchQuery, function ($q) use ($searchQuery) {
                        $q->where(function ($sq) use ($searchQuery) {
                            $sq->where('title', 'like', "%{$searchQuery}%")
                                ->orWhere('description', 'like', "%{$searchQuery}%");
                        });
                    })
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            }

            return view('user.private_collaboration.table', compact('collaborations'))->render();
        }
    }

    /**
     * Show single collaboration row (AJAX)
     */
    public function showSingleCollaboration(Request $request)
    {
        $collaboration = PrivateCollaboration::with(['user', 'invitations.user'])->findOrFail($request->collaboration_id);
        return response()->json([
            'status' => true,
            'view' => view('user.private_collaboration.show-single-collaboration', compact('collaboration'))->render()
        ]);
    }

    /**
     * View calendar
     */
    public function viewCalender()
    {
        if (!auth()->check()) {
            abort(403, 'You do not have permission to access this page.');
        }

        $user_type = auth()->user()->user_type;
        $user_country = auth()->user()->country;

        if ($user_type == 'Global') {
            $collaborations = PrivateCollaboration::with(['user', 'invitations.user'])
                ->where(function ($query) {
                    $query->where('user_id', auth()->id())
                        ->orWhereHas('invitations', function ($q) {
                            $q->where('user_id', auth()->id());
                        });
                })
                ->get();
        } else {
            $collaborations = PrivateCollaboration::with(['user', 'invitations.user'])
                ->where(function ($query) {
                    $query->where('user_id', auth()->id())
                        ->orWhereHas('invitations', function ($q) {
                            $q->where('user_id', auth()->id());
                        });
                })
                ->where('country_id', $user_country)
                ->get();
        }
        return view('user.private_collaboration.calender', compact('collaborations'));
    }

    /**
     * Fetch calendar data (AJAX)
     */
    public function fetchCalenderData()
    {
        if (!auth()->check()) {
            abort(403, 'You do not have permission to access this page.');
        }

        $user_type = auth()->user()->user_type;
        $user_country = auth()->user()->country;

        if ($user_type == 'Global') {
            $collaborations = PrivateCollaboration::with(['user', 'invitations.user'])
                ->where(function ($query) {
                    $query->where('user_id', auth()->id())
                        ->orWhereHas('invitations', function ($q) {
                            $q->where('user_id', auth()->id());
                        });
                })
                ->get()
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
            return response()->json($collaborations);
        } else {
            $collaborations = PrivateCollaboration::with(['user', 'invitations.user'])
                ->where(function ($query) {
                    $query->where('user_id', auth()->id())
                        ->orWhereHas('invitations', function ($q) {
                            $q->where('user_id', auth()->id());
                        });
                })
                ->where('country_id', $user_country)
                ->get()
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
            return response()->json($collaborations);
        }
    }

    /**
     * Send invitations to selected users (invitees)
     */
    protected function sendInvitationsToSelectedUsers($collaboration, array $userIds = [])
    {
        if (empty($userIds)) {
            return;
        }

        $users = User::whereIn('id', $userIds)->where('id', '!=', auth()->id())->get();

        foreach ($users as $user) {
            // Create or update invitation record
            CollaborationInvitation::updateOrCreate(
                ['collaboration_id' => $collaboration->id, 'user_id' => $user->id],
                ['status' => 'pending']
            );

            // Send email invitation
            try {
                Mail::to($user->email)->send(new CollaborationInvitationMail($collaboration, $user));
            } catch (\Exception $e) {
                Log::error('Failed to send invitation email to user ' . $user->id . ': ' . $e->getMessage());
            }

            // Send in-app notification
            try {
                NotificationService::notifyUser($user->id, 'You have been invited to a collaboration: ' . $collaboration->title, 'collaboration');
            } catch (\Exception $e) {
                Log::error('Failed to send in-app notification to user ' . $user->id . ': ' . $e->getMessage());
            }
        }
    }

    /**
     * Generate Zoom Web SDK signature
     */
    public function zoomSignature(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'collaboration_id' => 'required|integer|exists:private_collaborations,id',
        ]);

        $collaboration = PrivateCollaboration::findOrFail($request->collaboration_id);

        if (!auth()->user()->can('View Private Collaboration')) {
            return response()->json(['status' => false, 'message' => 'Permission denied'], 403);
        }

        $meetingLink = $collaboration->meeting_link ?? '';
        if (!$this->isZoomLink($meetingLink)) {
            return response()->json(['status' => false, 'message' => 'Not a Zoom meeting'], 400);
        }

        $meetingNumber = $this->parseZoomMeetingIdFromUrl($meetingLink);
        if (!$meetingNumber) {
            return response()->json(['status' => false, 'message' => 'Invalid Zoom meeting URL'], 400);
        }

        $sdkKey = env('ZOOM_SDK_KEY');
        $sdkSecret = env('ZOOM_SDK_SECRET');

        if (!$sdkKey || !$sdkSecret) {
            return response()->json(['status' => false, 'message' => 'Zoom SDK credentials not configured'], 500);
        }

        $role = ($collaboration->user_id == auth()->id()) ? 1 : 0; // 1 = host, 0 = participant
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
    }

    // -------------------- Zoom helpers --------------------

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
            'type' => 2, // Scheduled meeting
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

        $response = Http::withToken($token)
            ->post('https://api.zoom.us/v2/users/me/meetings', $payload);

        if ($response->successful()) {
            return $response->json();
        } else {
            throw new \Exception('Zoom API error: ' . $response->body());
        }
    }
}
