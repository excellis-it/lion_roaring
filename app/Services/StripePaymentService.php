<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventPayment;
use App\Models\EventRsvp;
use App\Models\User;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Exception;

class StripePaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a Stripe checkout session for event payment
     */
    public function createCheckoutSession(Event $event, User $user, ?EventRsvp $rsvp = null)
    {
        try {
            $transactionId = 'TXN-' . strtoupper(Str::random(12));

            // Create pending payment record
            $payment = EventPayment::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'rsvp_id' => $rsvp?->id,
                'transaction_id' => $transactionId,
                'amount' => $event->price,
                'currency' => 'USD',
                'status' => 'pending',
                'payment_method' => 'stripe',
            ]);

            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $event->title,
                            'description' => 'Event Registration: ' . $event->title,
                        ],
                        'unit_amount' => $event->price * 100, // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('event.payment.success', ['payment' => $payment->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('event.register.show', ['id' => $event->id]) . '?payment=cancelled',
                'client_reference_id' => $payment->id,
                'customer_email' => $user->email,
                'metadata' => [
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                    'payment_id' => $payment->id,
                    'transaction_id' => $transactionId,
                ],
            ]);

            // Update payment with Stripe session info
            $payment->update([
                'stripe_payment_intent_id' => $session->id,
                'payment_details' => json_encode(['session_id' => $session->id]),
            ]);

            return [
                'success' => true,
                'session_id' => $session->id,
                'payment' => $payment,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify and process successful payment
     */
    public function verifyPayment($sessionId, $paymentId)
    {
        try {
            $session = StripeSession::retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                return [
                    'success' => false,
                    'error' => 'Payment not completed',
                ];
            }

            $payment = EventPayment::findOrFail($paymentId);

            // Update payment status
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'payment_details' => json_encode([
                    'session_id' => $sessionId,
                    'payment_intent' => $session->payment_intent,
                ]),
            ]);

            // Confirm RSVP if exists
            if ($payment->rsvp_id) {
                $payment->rsvp->update(['status' => 'confirmed']);
            }

            return [
                'success' => true,
                'payment' => $payment,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process refund for event payment
     */
    public function refundPayment(EventPayment $payment)
    {
        try {
            // Implementation for Stripe refund
            // Note: Requires the payment intent ID

            $payment->update([
                'status' => 'refunded',
            ]);

            return [
                'success' => true,
                'payment' => $payment,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
