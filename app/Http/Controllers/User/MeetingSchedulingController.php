<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Str;

class MeetingSchedulingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->can('Manage Meeting Schedule')) {
            $meetings = Meeting::orderBy('id', 'desc')->paginate(15);
            return view('user.meeting.list')->with(compact('meetings'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (auth()->user()->can('Create Meeting Schedule')) {
            return view('user.meeting.create');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (auth()->user()->can('Create Meeting Schedule')) {
            $request->validate([
                'title' => 'required',
                'description' => 'required',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
                'meeting_link' => 'nullable',
                // link_source will be 'external' or 'zoom'; create_zoom hidden flag for JS simplicity
            ]);

            $meeting = new Meeting();
            $meeting->user_id = auth()->id();
            $meeting->time_zone = auth()->user()->time_zone;
            $meeting->title = $request->title;
            $meeting->description = $request->description;
            $meeting->start_time = $request->start_time;
            $meeting->end_time = $request->end_time;

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
                    $meeting->meeting_link = $zoom['join_url'] ?? $request->meeting_link;
                } catch (\Throwable $e) {
                    Log::error('Zoom meeting creation failed: ' . $e->getMessage());
                    // fallback: keep provided link (if any)
                    $meeting->meeting_link = $request->meeting_link;
                }
            } else {
                $meeting->meeting_link = $request->meeting_link;
            }

            $meeting->save();

            // notify users
            $userName = Auth::user()->getFullNameAttribute();
            $noti = NotificationService::notifyAllUsers('New Meeting created by ' . $userName, 'meeting');

            session()->flash('message', 'Meeting scheduled successfully.');

            return response()->json(['status' => true, 'message' => 'Meeting scheduled successfully.', 'meeting' => $meeting]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (auth()->user()->can('View Meeting Schedule')) {
            $meeting = Meeting::find($id);
            $isZoom = $this->isZoomLink($meeting->meeting_link ?? '');
            $zoomMeetingId = $isZoom ? $this->parseZoomMeetingIdFromUrl($meeting->meeting_link) : null;
            return view('user.meeting.show')->with('meeting', $meeting)->with('isZoom', $isZoom)->with('zoomMeetingId', $zoomMeetingId);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    // joinMeeting
    public function joinMeeting($id)
    {
        if (auth()->user()->can('View Meeting Schedule')) {
            $meeting = Meeting::find($id);
            $isZoom = $this->isZoomLink($meeting->meeting_link ?? '');
            $zoomMeetingId = $isZoom ? $this->parseZoomMeetingIdFromUrl($meeting->meeting_link) : null;
            return view('user.meeting.zoom_meet')->with('meeting', $meeting)->with('isZoom', $isZoom)->with('zoomMeetingId', $zoomMeetingId);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (auth()->user()->can('Edit Meeting Schedule') && auth()->user()->id == Meeting::find($id)->user_id || auth()->user()->hasRole('SUPER ADMIN')) {
            $meeting = Meeting::find($id);
            return view('user.meeting.edit')->with('meeting', $meeting);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (auth()->user()->can('Edit Meeting Schedule')) {
            $request->validate([
                'title' => 'required',
                'description' => 'required',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
                'meeting_link' => 'nullable',
            ]);

            $meeting = Meeting::find($id);
            $meeting->time_zone = auth()->user()->time_zone;
            $meeting->title = $request->title;
            $meeting->description = $request->description;
            $meeting->start_time = $request->start_time;
            $meeting->end_time = $request->end_time;

            // Optionally create a Zoom link on update
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
                    $meeting->meeting_link = $zoom['join_url'] ?? $request->meeting_link;
                } catch (\Throwable $e) {
                    Log::error('Zoom meeting creation (update) failed: ' . $e->getMessage());
                    $meeting->meeting_link = $request->meeting_link;
                }
            } else {
                $meeting->meeting_link = $request->meeting_link;
            }

            $meeting->save();

            session()->flash('message', 'Meeting updated successfully.');
            return response()->json(['status' => true, 'message' => 'Meeting updated successfully.', 'id' => $id]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function fetchData(Request $request)
    {
        if ($request->ajax()) {
            $sort_by = $request->get('sortby');
            $sort_type = $request->get('sorttype');
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);

            $meetings = Meeting::query()
                ->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhere('title', 'like', '%' . $query . '%')
                        ->orWhere('description', 'like', '%' . $query . '%')
                        ->orWhere('start_time', 'like', '%' . $query . '%')
                        ->orWhere('end_time', 'like', '%' . $query . '%')
                        ->orWhere('meeting_link', 'like', '%' . $query . '%');
                });


            $meetings->orderBy($sort_by, $sort_type);
            $meetings = $meetings->paginate(15);

            return response()->json(['data' => view('user.meeting.table', compact('meetings'))->render()]);
        }
    }
    public function delete($id)
    {
        if (Auth::user()->can('Delete Meeting Schedule') && Auth::user()->id == Meeting::find($id)->user_id || Auth::user()->hasRole('SUPER ADMIN')) {
            $meeting = Meeting::findOrFail($id);
            $meeting->delete();
            return response()->json(['message' => 'Meeting deleted successfully.', 'status' => true, 'id' => $id]);
        } else {
            return response()->json(['message' => 'You do not have permission to delete this meeting.', 'status' => false]);
        }
    }

    public function viewCalender()
    {
        if (auth()->user()->can('Manage Meeting Schedule')) {
            $meetings = Meeting::orderBy('id', 'desc')->get();
            return view('user.meeting.calender')->with(compact('meetings'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function fetchCalenderData()
    {
        if (auth()->user()->can('Manage Meeting Schedule')) {
            $meetings = Meeting::orderBy('id', 'desc')->get(['id', 'title', 'description', 'start_time as start', 'end_time as end', 'meeting_link']);
            return response()->json($meetings);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function showSingleMeeting(Request $request)
    {
        $meeting = Meeting::find($request->meeting_id);
        return response()->json(['status' => true, 'view' => view('user.meeting.show-single-meeting', compact('meeting'))->render()]);
    }

    /**
     * Generate Zoom Web SDK signature and return meeting details for browser join.
     */
    public function zoomSignature(Request $request)
    {
        if (!auth()->check()) {
            abort(403);
        }

        $request->validate([
            'meeting_id' => 'required|integer|exists:meetings,id',
        ]);

        $meeting = Meeting::findOrFail($request->meeting_id);

        if (!auth()->user()->can('View Meeting Schedule')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $meetingLink = $meeting->meeting_link ?? '';
        if (!$this->isZoomLink($meetingLink)) {
            return response()->json(['status' => false, 'message' => 'This meeting is not a Zoom meeting.'], 422);
        }

        $meetingNumber = $this->parseZoomMeetingIdFromUrl($meetingLink);
        if (!$meetingNumber) {
            return response()->json(['status' => false, 'message' => 'Invalid Zoom meeting link.'], 422);
        }

        // Fetch meeting details from Zoom (to get the passcode)
        $password = null;
        $topic = $meeting->title;
        try {
            $token = $this->zoomAccessToken();
            if ($token) {
                $resp = Http::withToken($token)->get("https://api.zoom.us/v2/meetings/{$meetingNumber}");
                if ($resp->successful()) { // CHANGED from ok() -> successful()
                    $password = $resp->json('password');
                    $topic = $resp->json('topic') ?: $topic;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Zoom meeting fetch failed: ' . $e->getMessage());
        }

        $sdkKey = env('ZOOM_SDK_KEY');
        $sdkSecret = env('ZOOM_SDK_SECRET');
        if (!$sdkKey || !$sdkSecret) {
            return response()->json(['status' => false, 'message' => 'Zoom SDK credentials missing.'], 500);
        }

        // Host if creator or SUPER ADMIN, else attendee
        $role = (auth()->id() === $meeting->user_id || auth()->user()->hasRole('SUPER ADMIN')) ? 1 : 0;

        // Signature v2 (per Zoom docs)
        $ts = round(microtime(true) * 1000) - 30000;
        $msg = base64_encode($sdkKey . $meetingNumber . $ts . $role);
        $hash = base64_encode(hash_hmac('sha256', $msg, $sdkSecret, true));
        $signature = base64_encode($sdkKey . '.' . $meetingNumber . '.' . $ts . '.' . $role . '.' . $hash);

        return response()->json([
            'status' => true,
            'signature' => $signature,
            'sdkKey' => $sdkKey,
            'meetingNumber' => (string)$meetingNumber,
            'password' => $password, // may be null if not retrievable
            'topic' => $topic,
            'role' => $role,
            'userName' => Auth::user()->full_name ?? (Auth::user()->first_name . ' ' . Auth::user()->last_name),
            'userEmail' => Auth::user()->email,
        ]);
    }

    // -------------------- Zoom helpers --------------------

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

        if (!$resp->successful()) { // CHANGED from ok() -> successful()
            Log::error('Zoom OAuth token failed: ' . $resp->body());
            return null;
        }

        return $resp->json('access_token');
    }

    /**
     * Create a Zoom meeting and return ['id', 'join_url', 'start_url', 'password']
     */
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
            'type' => 2, // scheduled
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
        if (!$resp->successful()) { // CHANGED from ok() -> successful()
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
