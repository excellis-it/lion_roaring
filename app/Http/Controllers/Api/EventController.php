<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\Concerns\AppliesPmaContentScope;
use App\Http\Controllers\Api\Concerns\AppliesPmaCountryFromRequest;
use App\Http\Controllers\Controller;
use App\Mail\EventInvitation;
use App\Models\Country;
use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\EventPayment;
use App\Models\User;
use App\Services\CheckoutPaymentService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * @group Events
 *
 * @authenticated
 */

class EventController extends Controller
{
    use AppliesPmaContentScope;
    use AppliesPmaCountryFromRequest;

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

            $ctx = $this->pmaScopeContext();

            $query = Event::with(['user', 'country'])
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    $query->where(function ($q) use ($searchQuery) {
                        $q->where('title', 'like', "%{$searchQuery}%")
                            ->orWhere('description', 'like', "%{$searchQuery}%");
                    });
                });

            $this->applyPmaEventScope($query, $ctx);

            $events = $query->orderBy('id', 'desc')->paginate(15);

            $events->getCollection()->transform(function ($event) {
                $event->user_rsvp_status = EventRsvp::where('event_id', $event->id)
                    ->where('user_id', auth()->id())
                    ->value('status');
                $event->is_host = auth()->id() === $event->user_id;

                return $event;
            });

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

        $countryId = $this->resolvePmaCountryId($request);

        // Normalize send_notification checkbox variants
        $sendNotification = $request->has('send_notification') && ($request->send_notification === 'on' || $request->send_notification === true || $request->send_notification === '1');

        $request->merge(['country_id' => $countryId, 'send_notification' => $sendNotification]);

        $rules = array_merge([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'type' => 'required|in:free,paid',
            'capacity' => 'nullable|integer|min:1',
            'event_link' => 'nullable|string',
            'send_notification' => 'nullable|boolean',
        ], $this->pmaCountryValidationRules());

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
            if (! auth()->user()->can('View Event')) {
                return response()->json(['error' => 'Permission denied.'], 403);
            }

            $ctx = $this->pmaScopeContext();
            $query = Event::with('user');
            $this->applyPmaEventScope($query, $ctx);
            $event = $query->findOrFail($id);
            $event->user_rsvp_status = EventRsvp::where('event_id', $event->id)
                ->where('user_id', auth()->id())
                ->value('status');
            $event->is_host = auth()->id() === $event->user_id;

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

        $countryId = $this->resolvePmaCountryId($request);
        $request->merge(['country_id' => $countryId]);

        $rules = array_merge([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'type' => 'required|in:free,paid',
            'capacity' => 'nullable|integer|min:1',
            'event_link' => 'nullable|string',
        ], $this->pmaCountryValidationRules());

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

            $ctx = $this->pmaScopeContext();
            $query = Event::query();
            $this->applyPmaEventScope($query, $ctx);

            $events = $query->orderBy('id', 'desc')->get()->map(function ($event) {
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

    /**
     * POST /events/{id}/rsvp
     * Creates or re-confirms the authenticated user's RSVP for a free event.
     * For paid events, returns `requires_payment: true` so the client invokes
     * `/events/{id}/payment` next.
     * @bodyParam notes string optional
     */
    public function rsvp(Request $request, int $id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['status' => false, 'message' => 'Event not found.'], 404);
        }

        if (!$event->hasCapacity()) {
            return response()->json(['status' => false, 'message' => 'Event is at full capacity.'], 422);
        }

        $existing = EventRsvp::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($event->isPaid()) {
            $rsvp = $existing ?: EventRsvp::create([
                'event_id' => $event->id,
                'user_id' => Auth::id(),
                'status' => 'pending',
                'rsvp_date' => now(),
                'notes' => $request->input('notes'),
            ]);

            if ($existing && $existing->status === 'cancelled') {
                $existing->update(['status' => 'pending', 'rsvp_date' => now()]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Payment required to confirm RSVP.',
                'data' => [
                    'rsvp' => $rsvp->fresh(),
                    'requires_payment' => true,
                    'amount' => (float) $event->price,
                    'currency' => 'USD',
                ],
            ]);
        }

        if ($existing) {
            if ($existing->status === 'cancelled') {
                $existing->update(['status' => 'confirmed', 'rsvp_date' => now(), 'notes' => $request->input('notes')]);
            }
            return response()->json([
                'status' => true,
                'message' => 'RSVP confirmed.',
                'data' => ['rsvp' => $existing->fresh(), 'requires_payment' => false],
            ]);
        }

        $rsvp = EventRsvp::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'status' => 'confirmed',
            'rsvp_date' => now(),
            'notes' => $request->input('notes'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'RSVP confirmed.',
            'data' => ['rsvp' => $rsvp, 'requires_payment' => false],
        ]);
    }

    /**
     * DELETE /events/{id}/rsvp
     */
    public function cancelRsvp(int $id)
    {
        $rsvp = EventRsvp::where('event_id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$rsvp) {
            return response()->json(['status' => false, 'message' => 'RSVP not found.'], 404);
        }

        $rsvp->update(['status' => 'cancelled']);

        return response()->json(['status' => true, 'message' => 'RSVP cancelled.']);
    }

    /**
     * GET /events/my-rsvps
     */
    public function myRsvps(Request $request)
    {
        $perPage = max(1, min(50, (int) $request->input('per_page', 20)));

        $rsvps = EventRsvp::with(['event', 'payment'])
            ->where('user_id', Auth::id())
            ->orderByDesc('rsvp_date')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'My RSVPs.',
            'data' => $rsvps,
        ]);
    }

    /**
     * POST /events/{id}/payment
     * Creates a Stripe PaymentIntent for a paid event and attaches it to the RSVP.
     * Mobile client calls this after RSVP returns `requires_payment: true`.
     */
    public function createPaymentIntent(int $id, CheckoutPaymentService $payment)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['status' => false, 'message' => 'Event not found.'], 404);
        }

        if (!$event->isPaid() || (float) $event->price <= 0) {
            return response()->json(['status' => false, 'message' => 'Event does not require payment.'], 422);
        }

        if (!$event->hasCapacity()) {
            return response()->json(['status' => false, 'message' => 'Event is at full capacity.'], 422);
        }

        $rsvp = EventRsvp::firstOrCreate(
            ['event_id' => $event->id, 'user_id' => Auth::id()],
            ['status' => 'pending', 'rsvp_date' => now()]
        );

        $pending = EventPayment::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'rsvp_id' => $rsvp->id,
            'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
            'amount' => $event->price,
            'currency' => 'USD',
            'status' => 'pending',
            'payment_method' => 'stripe',
        ]);

        $result = $payment->createIntent(
            (float) $event->price,
            'USD',
            Auth::user(),
            [
                'type' => 'event',
                'event_id' => $event->id,
                'rsvp_id' => $rsvp->id,
                'payment_id' => $pending->id,
            ]
        );

        if (!$result['success']) {
            $pending->update(['status' => 'failed']);
            return response()->json(['status' => false, 'message' => $result['error']], 500);
        }

        $pending->update(['stripe_payment_intent_id' => $result['payment_intent_id']]);

        return response()->json([
            'status' => true,
            'message' => 'Payment intent created.',
            'data' => [
                'payment_id' => $pending->id,
                'rsvp_id' => $rsvp->id,
                'payment_intent_id' => $result['payment_intent_id'],
                'client_secret' => $result['client_secret'],
                'ephemeral_key' => $result['ephemeral_key'],
                'customer_id' => $result['customer_id'],
                'publishable_key' => $result['publishable_key'],
            ],
        ]);
    }

    /**
     * POST /events/{id}/payment/confirm
     * Mobile client calls after PaymentSheet succeeds. Verifies with Stripe,
     * flips payment+RSVP to confirmed.
     * @bodyParam payment_id int required
     */
    public function confirmPayment(int $id, Request $request, CheckoutPaymentService $payment)
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $paymentRow = EventPayment::where('id', $request->payment_id)
            ->where('event_id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$paymentRow) {
            return response()->json(['status' => false, 'message' => 'Payment not found.'], 404);
        }

        if ($paymentRow->status === 'completed') {
            return response()->json([
                'status' => true,
                'message' => 'Payment already completed.',
                'data' => $paymentRow->load('rsvp'),
            ]);
        }

        if (!$paymentRow->stripe_payment_intent_id) {
            return response()->json(['status' => false, 'message' => 'No payment intent attached.'], 422);
        }

        $verification = $payment->verifyIntent($paymentRow->stripe_payment_intent_id);
        if (!($verification['success'] ?? false)) {
            return response()->json([
                'status' => false,
                'message' => 'Payment not completed. Status: ' . ($verification['status'] ?? 'unknown'),
            ], 402);
        }

        DB::transaction(function () use ($paymentRow) {
            $paymentRow->update([
                'status' => 'completed',
                'paid_at' => now(),
            ]);
            if ($paymentRow->rsvp_id) {
                EventRsvp::where('id', $paymentRow->rsvp_id)->update(['status' => 'confirmed']);
            }
        });

        return response()->json([
            'status' => true,
            'message' => 'Payment confirmed.',
            'data' => $paymentRow->fresh()->load('rsvp'),
        ]);
    }

    /**
     * Send in-app and email notifications for a new/updated live event (mirrors web LiveEventController).
     */
    protected function sendNotifications(Event $event, string $message): void
    {
        $query = User::where('status', 1)->where('is_accept', 1);

        if (! auth()->user()->hasNewRole('SUPER ADMIN')) {
            $currentCountry = Country::findByCurrentRequest();
            $isOnGlobalServer = $currentCountry && $currentCountry->is_global;
            $userType = auth()->user()->user_type;

            if ($userType == 'Global' || ($userType == 'G_R' && $isOnGlobalServer)) {
                $query->whereIn('user_type', ['Global', 'G_R']);
            } else {
                $query->whereIn('user_type', ['Regional', 'G_R'])
                    ->where('country', auth()->user()->country);

                if (auth()->user()->is_ecclesia_admin == 1) {
                    $manageEcclesiaIds = is_array(auth()->user()->manage_ecclesia)
                        ? auth()->user()->manage_ecclesia
                        : explode(',', (string) (auth()->user()->manage_ecclesia ?? ''));
                    $query->whereIn('ecclesia_id', $manageEcclesiaIds);
                }
            }
        }

        $users = $query->get();

        foreach ($users as $user) {
            try {
                NotificationService::saveNotification($user->id, $message, 'live_event');
            } catch (\Exception $e) {
                Log::error('Failed to send in-app notification to user '.$user->id.': '.$e->getMessage());
            }

            try {
                Mail::to($user->email)->queue(new EventInvitation($event));
            } catch (\Exception $e) {
                Log::error('Failed to send event invitation email to '.$user->email.': '.$e->getMessage());
            }
        }
    }
}
