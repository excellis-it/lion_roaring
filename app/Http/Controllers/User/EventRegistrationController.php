<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\EventPayment;
use App\Services\StripePaymentService;
use App\Mail\EventRsvpConfirmation;
use App\Mail\EventPaymentReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EventRegistrationController extends Controller
{
    protected $stripeService;

    public function __construct(StripePaymentService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Display event registration page (public)
     *
     * @group Event Registration
     * @urlParam event integer required The event ID. Example: 1
     * @response 200 {
     *   "event": {
     *     "id": 1,
     *     "title": "Annual Conference",
     *     "description": "Join us for our annual conference",
     *     "type": "paid",
     *     "price": 50.00,
     *     "start": "2026-02-15 10:00:00",
     *     "end": "2026-02-15 16:00:00"
     *   }
     * }
     */
    public function show($id)
    {
        $event = Event::with(['user', 'country'])->findOrFail($id);

        // Check if user is already registered
        $userRsvp = null;
        if (Auth::check()) {
            $userRsvp = EventRsvp::where('event_id', $event->id)
                ->where('user_id', Auth::id())
                ->first();
        }

        return view('frontend.event-register', compact('event', 'userRsvp'));
    }

    /**
     * Process event registration
     *
     * @group Event Registration
     * @authenticated
     * @bodyParam event_id integer required The event ID. Example: 1
     * @bodyParam notes string optional Additional notes. Example: Looking forward to this event!
     * @response 200 {
     *   "status": true,
     *   "message": "Registration successful",
     *   "rsvp": {}
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "Event is full"
     * }
     */
    public function register(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('redirect_to', route('event.register.show', $id));
        }

        $event = Event::findOrFail($id);

        // Check if already registered
        $existingRsvp = EventRsvp::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingRsvp) {
            return response()->json([
                'status' => false,
                'message' => 'You have already registered for this event.',
            ], 422);
        }

        // Check capacity
        if (!$event->hasCapacity()) {
            return response()->json([
                'status' => false,
                'message' => 'This event is full.',
            ], 422);
        }

        // Create RSVP
        $rsvp = EventRsvp::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'status' => $event->isFree() ? 'confirmed' : 'pending',
            'notes' => $request->notes,
        ]);

        // If paid event, initiate payment
        if ($event->isPaid()) {
            $result = $this->stripeService->createCheckoutSession($event, Auth::user(), $rsvp);

            if ($result['success']) {
                return response()->json([
                    'status' => true,
                    'redirect_to_stripe' => true,
                    'session_id' => $result['session_id'],
                ]);
            } else {
                $rsvp->delete();
                return response()->json([
                    'status' => false,
                    'message' => 'Payment processing failed: ' . $result['error'],
                ], 500);
            }
        }

        // For free events, send confirmation
        $this->sendRsvpConfirmation($rsvp);

        return response()->json([
            'status' => true,
            'message' => 'Registration successful! Check your email for event details.',
            'rsvp' => $rsvp,
        ]);
    }

    /**
     * Handle successful payment
     */
    public function paymentSuccess(Request $request, $paymentId)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('events.index')->with('error', 'Invalid payment session');
        }

        $result = $this->stripeService->verifyPayment($sessionId, $paymentId);

        $payment = $result['payment'];

        if ($result['success']) {

            $this->sendRsvpConfirmation($payment->rsvp);
            $this->sendPaymentReceipt($payment);

            return redirect()->route('event.access', $payment->event_id)
                ->with('success', 'Payment successful! You are now registered for the event.');
        }

        return redirect()->route('event.register.show', $payment->event_id)
            ->with('error', 'Payment verification failed');
    }

    /**
     * Display event access page (authenticated users only)
     *
     * @group Event Registration
     * @authenticated
     * @urlParam event integer required The event ID. Example: 1
     */
    public function access($id)
    {
        $event = Event::with(['user', 'country'])->findOrFail($id);

        // Check if user has valid RSVP
        $rsvp = EventRsvp::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->confirmed()
            ->first();

        if (!$rsvp) {
            abort(403, 'You do not have access to this event. Please register first.');
        }

        return view('user.events.access', compact('event', 'rsvp'));
    }

    /**
     * Cancel RSVP
     *
     * @group Event Registration
     * @authenticated
     * @urlParam event integer required The event ID. Example: 1
     */
    public function cancelRsvp($id)
    {
        $rsvp = EventRsvp::where('event_id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $rsvp->update(['status' => 'cancelled']);

        return response()->json([
            'status' => true,
            'message' => 'Your RSVP has been cancelled.',
        ]);
    }

    /**
     * Send RSVP confirmation email
     */
    protected function sendRsvpConfirmation(EventRsvp $rsvp)
    {
        Mail::to($rsvp->user->email)->send(new EventRsvpConfirmation($rsvp));
    }

    /**
     * Send payment receipt email
     */
    protected function sendPaymentReceipt(EventPayment $payment)
    {
        Mail::to($payment->user->email)->send(new EventPaymentReceipt($payment));
    }
}
