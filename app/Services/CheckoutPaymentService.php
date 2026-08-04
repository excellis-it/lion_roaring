<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
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
        Stripe::setApiKey(config('services.stripe.secret') ?: config('services.stripe.secret'));
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
    public function createIntent(float $amount, string $currency, User $user, array $metadata = [], bool $cardOnly = false): array
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

            $intentParams = [
                'amount' => (int) round($amount * 100),
                'currency' => strtolower($currency),
                'customer' => $customerId,
                'metadata' => array_merge([
                    'user_id' => $user->id,
                    'email' => $user->email,
                ], $metadata),
            ];

            if ($cardOnly) {
                $intentParams['payment_method_types'] = ['card'];
            } else {
                $intentParams['automatic_payment_methods'] = ['enabled' => true];
            }

            $intent = StripePaymentIntent::create($intentParams);

            return [
                'success' => true,
                'payment_intent_id' => $intent->id,
                'client_secret' => $intent->client_secret,
                'ephemeral_key' => $ephemeralKey->secret,
                'customer_id' => $customerId,
                'publishable_key' => config('services.stripe.key') ?: config('services.stripe.key'),
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
     * Collect card details without charging (100%-off promo on a paid tier).
     */
    public function createSetupIntent(User $user, array $metadata = []): array
    {
        try {
            $customerId = $this->getOrCreateCustomerId($user);

            $ephemeralKey = StripeEphemeralKey::create(
                ['customer' => $customerId],
                ['stripe_version' => '2023-10-16']
            );

            $setupIntent = \Stripe\SetupIntent::create([
                'customer' => $customerId,
                'payment_method_types' => ['card'],
                'metadata' => array_merge([
                    'user_id' => $user->id,
                ], $metadata),
            ]);

            return [
                'success' => true,
                'setup_intent_id' => $setupIntent->id,
                'client_secret' => $setupIntent->client_secret,
                'ephemeral_key' => $ephemeralKey->secret,
                'customer_id' => $customerId,
                'publishable_key' => config('services.stripe.key') ?: config('services.stripe.key'),
                'card_verification' => true,
            ];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function verifySetupIntent(string $setupIntentId): array
    {
        try {
            $intent = \Stripe\SetupIntent::retrieve($setupIntentId);

            return [
                'success' => $intent->status === 'succeeded',
                'status' => $intent->status,
                'metadata' => $intent->metadata ? $intent->metadata->toArray() : [],
                'intent' => $intent,
            ];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Validate a legacy Stripe token (web card element) without charging.
     */
    public function verifyCardToken(string $stripeToken): array
    {
        try {
            if ($stripeToken === '' || $stripeToken === 'free_tier') {
                return ['success' => false, 'error' => 'Payment card details are required.'];
            }

            $token = \Stripe\Token::retrieve($stripeToken);
            if (empty($token->card)) {
                return ['success' => false, 'error' => 'Invalid card details.'];
            }

            return [
                'success' => true,
                'transaction_id' => 'CARD-' . strtoupper(uniqid()),
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
    public function getOrCreateCustomerId(User $user): string
    {
        if (!empty($user->stripe_customer_id)) {
            return $user->stripe_customer_id;
        }

        $customer = StripeCustomer::create([
            'email' => $user->email,
            'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->email,
            'metadata' => ['user_id' => $user->id],
        ]);

        // Column check only — writing the attribute when the migration has not run yet would
        // throw on save() and take the payment down with it.
        if (Schema::hasColumn($user->getTable(), 'stripe_customer_id')) {
            $user->stripe_customer_id = $customer->id;
            $user->save();
        }

        return $customer->id;
    }

    /**
     * Customer for a registration charge, taken before the User row exists.
     * Keeps the Stripe dashboard/export rows attributable (customer id + email).
     * Never throws — a failed lookup must not block the payment.
     */
    public function createGuestCustomerId(string $email, ?string $name = null, array $metadata = []): ?string
    {
        try {
            return StripeCustomer::create([
                'email' => $email,
                'name' => $name ?: $email,
                'metadata' => $metadata,
            ])->id;
        } catch (Exception $e) {
            Log::warning('Stripe guest customer create failed: ' . $e->getMessage(), ['email' => $email]);

            return null;
        }
    }

    /**
     * Sets the customer id on an unsaved User, but only when the column is actually there —
     * keeps registration working if the code is deployed ahead of the migration.
     */
    public function setCustomerIdIfSupported(User $user, ?string $customerId): void
    {
        if ($customerId && Schema::hasColumn($user->getTable(), 'stripe_customer_id')) {
            $user->stripe_customer_id = $customerId;
        }
    }

    /**
     * Same as getOrCreateCustomerId() but returns null instead of throwing, for call sites
     * where the customer is only there to enrich the charge record.
     */
    public function customerIdOrNull(User $user): ?string
    {
        try {
            return $this->getOrCreateCustomerId($user);
        } catch (\Throwable $e) {
            Log::warning('Stripe customer lookup failed: ' . $e->getMessage(), ['user_id' => $user->id]);

            return null;
        }
    }

    /**
     * Legacy token charge with the customer attached. A stale/foreign customer id (e.g. a
     * database copied between Stripe accounts) makes Stripe reject the request — retry once
     * without it rather than lose the payment. Only InvalidRequestException is retried: it is
     * raised during validation, before any money moves, so no double charge is possible.
     */
    public function chargeWithToken(array $params, ?string $customerId): \Stripe\Charge
    {
        if (!$customerId) {
            return \Stripe\Charge::create($params);
        }

        try {
            return \Stripe\Charge::create($params + ['customer' => $customerId]);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            Log::warning('Stripe charge with customer rejected, retrying without: ' . $e->getMessage(), [
                'customer' => $customerId,
            ]);

            return \Stripe\Charge::create($params);
        }
    }
}
