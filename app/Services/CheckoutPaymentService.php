<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Stripe\Customer as StripeCustomer;
use Stripe\EphemeralKey as StripeEphemeralKey;
use Stripe\PaymentIntent as StripePaymentIntent;
use Stripe\Stripe;

/**
 * Creates Stripe PaymentIntents for flutter_stripe PaymentSheet.
 *
 * Distinct from the pre-existing StripePaymentService (which creates Stripe Checkout
 * Sessions for web redirects). Mobile PaymentSheet requires a PaymentIntent + client
 * secret + a short-lived ephemeral key bound to a Stripe Customer.
 *
 * Used by membership, estore, event, and elearning checkout endpoints.
 */
class CheckoutPaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret') ?: env('STRIPE_SECRET'));
    }

    /**
     * Create a PaymentIntent and supporting objects for PaymentSheet.
     *
     * @param float  $amount    Amount in major units (e.g. 49.99 USD).
     * @param string $currency  ISO 4217 code, lower-case (e.g. 'usd').
     * @param array  $metadata  Free-form metadata merged into the PaymentIntent.
     *                          Callers SHOULD set at least `type` (membership|estore|event|elearning)
     *                          and the relevant foreign key (tier_id, order_id, event_id, etc).
     * @return array{success:bool,payment_intent_id?:string,client_secret?:string,ephemeral_key?:string,customer_id?:string,publishable_key?:string,error?:string}
     */
    public function createIntent(float $amount, string $currency, User $user, array $metadata = []): array
    {
        try {
            if ($amount <= 0) {
                return ['success' => false, 'error' => 'Amount must be greater than zero.'];
            }

            $customerId = $this->getOrCreateCustomerId($user);

            $ephemeralKey = StripeEphemeralKey::create(
                ['customer' => $customerId],
                ['stripe_version' => '2023-10-16']
            );

            $intent = StripePaymentIntent::create([
                'amount' => (int) round($amount * 100),
                'currency' => strtolower($currency),
                'customer' => $customerId,
                'automatic_payment_methods' => ['enabled' => true],
                'metadata' => array_merge([
                    'user_id' => $user->id,
                ], $metadata),
            ]);

            return [
                'success' => true,
                'payment_intent_id' => $intent->id,
                'client_secret' => $intent->client_secret,
                'ephemeral_key' => $ephemeralKey->secret,
                'customer_id' => $customerId,
                'publishable_key' => config('services.stripe.key') ?: env('STRIPE_KEY'),
            ];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Confirm a PaymentIntent is actually paid — call from the mobile client's post-success
     * callback before recording the order/subscription server-side.
     */
    public function verifyIntent(string $paymentIntentId): array
    {
        try {
            $intent = StripePaymentIntent::retrieve($paymentIntentId);

            return [
                'success' => $intent->status === 'succeeded',
                'status' => $intent->status,
                'amount' => $intent->amount ? $intent->amount / 100 : 0,
                'currency' => $intent->currency,
                'metadata' => $intent->metadata ? $intent->metadata->toArray() : [],
                'intent' => $intent,
            ];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Returns the Stripe customer id for this user, creating + persisting one if needed.
     * Relies on `users.stripe_customer_id`; stores silently if the column exists, otherwise
     * the customer is re-created per checkout (still functional, just less efficient).
     */
    private function getOrCreateCustomerId(User $user): string
    {
        if (!empty($user->stripe_customer_id)) {
            return $user->stripe_customer_id;
        }

        $customer = StripeCustomer::create([
            'email' => $user->email,
            'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->email,
            'metadata' => ['user_id' => $user->id],
        ]);

        if (in_array('stripe_customer_id', $user->getFillable(), true)
            || \Schema::hasColumn($user->getTable(), 'stripe_customer_id')) {
            $user->stripe_customer_id = $customer->id;
            $user->save();
        }

        return $customer->id;
    }
}
