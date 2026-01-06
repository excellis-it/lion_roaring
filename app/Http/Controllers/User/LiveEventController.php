<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\EventPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Services\NotificationService;
use App\Mail\EventInvitation;
use Illuminate\Support\Str;

class LiveEventController extends Controller
{
    /**
     * Display list of events
     */
    public function list()
    {
        if (Auth::user()->can('Manage Event')) {
            $user_type = auth()->user()->user_type;
            $user_country = auth()->user()->country;
            if ($user_type == 'Global') {
                $events = Event::with(['rsvps', 'payments'])->orderBy('id', 'desc')->get();
            } else {
                $events = Event::with(['rsvps', 'payments'])
                    ->where('country_id', $user_country)
                    ->orderBy('id', 'desc')
                    ->get();
            }
            $country = Country::orderBy('name', 'asc')->get();
            return view('user.events.list', compact('events', 'country'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Get calendar events
     */
    public function calender(Request $request)
    {
        $user_type = auth()->user()->user_type;
        $user_country = auth()->user()->country;
        if ($user_type == 'Global') {
            $events = Event::orderBy('id', 'desc')->get();
        } else {
            $events = Event::where('country_id', $user_country)
                ->orderBy('id', 'desc')
                ->get();
        }

        // Add decrypted link, RSVP status, and ensure type is included
        $events = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'start' => $event->start,
                'end' => $event->end,
                'type' => $event->type ?? 'free', // Ensure type is always set
                'price' => $event->price,
                'capacity' => $event->capacity,
                'country_id' => $event->country_id,
                'user_id' => $event->user_id,
                'decrypted_link' => $event->getDecryptedLink(),
                'is_host' => Auth::id() === $event->user_id,
                'user_rsvp_status' => EventRsvp::where('event_id', $event->id)
                    ->where('user_id', Auth::id())
                    ->value('status'),
                'formatted_start' => $event->formatted_start,
                'formatted_end' => $event->formatted_end,
                'timezone' => $event->time_zone,
            ];
        });

        return response()->json($events);
    }

    /**
     * Store a new event
     *
     * @group Event Management
     * @authenticated
     * @bodyParam title string required Event title. Example: Annual Conference
     * @bodyParam description string required Event description
     * @bodyParam start datetime required Event start time
     * @bodyParam end datetime required Event end time
     * @bodyParam country_id integer required Country ID
     * @bodyParam type string Event type (free/paid). Example: paid
     * @bodyParam price decimal Event price for paid events. Example: 50.00
     * @bodyParam capacity integer Maximum attendees
     * @bodyParam send_notification boolean Send notification to all users. Example: true
     * @response 200 {
     *   "status": true,
     *   "message": "Event created successfully.",
     *   "event": {}
     * }
     */
    public function store(Request $request)
    {
        // return $request;
        $country_id = auth()->user()->user_type === 'Global'
            ? $request->country_id
            : auth()->user()->country;

        // return $country_id;

        // Convert 'on' from checkbox to boolean true
        $sendNotification = $request->has('send_notification') && ($request->send_notification === 'on' || $request->send_notification === true || $request->send_notification === '1');

        $request->merge([
            'country_id' => $country_id,
            'send_notification' => $sendNotification
        ]);

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

        $request->validate($rules);

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
            // return $userName;
            $message = 'New Live Event created by ' . $userName . ': ' . $event->title;

            // Send notifications based on event scope (regional or global)
            $abcd = $this->sendNotifications($event, $message);
            // return $abcd;
        }

        return response()->json([
            'message' => 'Event created successfully.',
            'event' => $event,
            'status' => true
        ]);
    }

    /**
     * Update an existing event
     */
    public function update(Request $request, $id)
    {
        $country_id = auth()->user()->user_type === 'Global'
            ? $request->country_id
            : auth()->user()->country;

        $request->merge(['country_id' => $country_id]);

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

        $request->validate($rules);

        $event = Event::findOrFail($id);
        $event->title = $request->title;
        $event->time_zone = auth()->user()->time_zone;
        $event->description = $request->description;
        $event->start = $request->start;
        $event->end = $request->end;
        $event->country_id = $request->country_id;
        $event->type = $request->type;
        $event->price = $request->type === 'paid' ? $request->price : null;
        $event->capacity = $request->capacity;

        // Update encrypted event link
        if ($request->has('event_link')) {
            $event->setEncryptedLink($request->event_link);
        }

        $event->update();

        return response()->json([
            'message' => 'Event updated successfully.',
            'event' => $event,
            'status' => true
        ]);
    }

    /**
     * Delete an event
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        Log::info($event->title . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
        $event->delete();

        return response()->json(['success' => 'Event deleted successfully.']);
    }

    /**
     * View event RSVPs and payments
     *
     * @group Event Management
     * @authenticated
     * @urlParam event integer required The event ID. Example: 1
     */
    public function viewRsvps($id)
    {
        $event = Event::with(['rsvps.user', 'rsvps.payment'])->findOrFail($id);

        if (!Auth::user()->can('Manage Event')) {
            abort(403, 'Unauthorized access');
        }

        return view('user.events.rsvps', compact('event'));
    }

    /**
     * View event payments
     *
     * @group Event Management
     * @authenticated
     * @urlParam event integer required The event ID. Example: 1
     */
    public function viewPayments($id)
    {
        $event = Event::with(['payments.user'])->findOrFail($id);

        if (!Auth::user()->can('Manage Event')) {
            abort(403, 'Unauthorized access');
        }

        $totalRevenue = $event->completedPayments()->sum('amount');
        $pendingRevenue = $event->payments()->where('status', 'pending')->sum('amount');

        return view('user.events.payments', compact('event', 'totalRevenue', 'pendingRevenue'));
    }

    /**
     * Export event attendees
     */
    public function exportAttendees($id)
    {
        $event = Event::with(['confirmedRsvps.user'])->findOrFail($id);

        if (!Auth::user()->can('Manage Event')) {
            abort(403, 'Unauthorized access');
        }

        // Implementation for CSV export
        // This can be enhanced with Laravel Excel package

        return response()->json([
            'status' => true,
            'message' => 'Export functionality will be implemented',
        ]);
    }

    /**
     * Send both in-app and email notifications to users
     */
    protected function sendNotifications(Event $event, string $message)
    {
        // Get all active users based on event country
        $query = \App\Models\User::where('status', 1)->where('is_accept', 1);

        // Filter by country if event is country-specific
        if ($event->country_id) {
            $query->where('country', $event->country_id);
        }

        $users = $query->get();

        // return $users;

        // Send in-app notifications and emails to each user
        foreach ($users as $user) {
            // Send in-app notification
            try {
                NotificationService::saveNotification($user->id, $message, 'live_event');
            } catch (\Exception $e) {
                Log::error('Failed to send in-app notification to user ' . $user->id . ': ' . $e->getMessage());
            }

            // Send email invitation
            try {
                Mail::to($user->email)->queue(new EventInvitation($event));
            } catch (\Exception $e) {
                Log::error('Failed to send event invitation email to ' . $user->email . ': ' . $e->getMessage());
            }
        }

        Log::info('Event notifications sent for event: ' . $event->title . ' to ' . $users->count() . ' users');
    }

    /**
     * Send email invitations to all users (deprecated - use sendNotifications instead)
     */
    protected function sendEventInvitations(Event $event)
    {
        // Get all active users based on event country
        $query = \App\Models\User::where('status', 1)->where('is_accept', 1);

        // Filter by country if event is country-specific
        if ($event->country_id) {
            $query->where('country', $event->country_id);
        }

        $users = $query->get();

        // Send email to each user (queued for performance)
        foreach ($users as $user) {
            try {
                Mail::to($user->email)->queue(new EventInvitation($event));
            } catch (\Exception $e) {
                Log::error('Failed to send event invitation email to ' . $user->email . ': ' . $e->getMessage());
            }
        }

        Log::info('Event invitations sent for event: ' . $event->title . ' to ' . $users->count() . ' users');
    }
}
