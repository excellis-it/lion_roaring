<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\MembershipTier;
use App\Models\SubscriptionPayment;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\MembershipPrivilegeService;
use Illuminate\Http\Request;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            if ($endpointSecret) {
                $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            } else {
                // If there's no webhook secret set, attempt to decode the payload
                $event = json_decode($payload);
            }
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        $type = $event->type ?? ($event->event ?? null);

        if ($type == 'checkout.session.completed') {
            $session = $event->data->object;
            $metadata = $session->metadata ?? null;
            $userId = $metadata->user_id ?? null;
            $tierId = $metadata->tier_id ?? null;
            $isRenew = ($metadata->renew ?? null) == '1';

            if (!$userId || !$tierId) {
                return response()->json(['error' => 'Missing metadata'], 400);
            }

            $user = User::find($userId);
            $tier = MembershipTier::find($tierId);

            if (!$user || !$tier) {
                return response()->json(['error' => 'User or Tier not found'], 404);
            }

            // Prevent duplicates
            $exists = SubscriptionPayment::where('transaction_id', $session->id)->first();
            if ($exists) {
                return response()->json(['status' => 'already_processed']);
            }

            $billingPeriod = \App\Services\MembershipPricing::validatePeriod($metadata->billing_period ?? 'yearly');
            $basePrice = \App\Services\MembershipPricing::priceFor($tier, $billingPeriod);
            $durationMonths = \App\Services\MembershipPricing::durationMonthsFor($billingPeriod);
            $previousPlanId = $user->userLastSubscription?->plan_id;

            // Create or renew user subscription
            if ($isRenew && $user->userLastSubscription && $user->userLastSubscription->plan_id == $tier->id) {
                $sub = $user->userLastSubscription;
                $sub->subscription_expire_date = now()->max($sub->subscription_expire_date)->addMonths($durationMonths);
                $sub->billing_period = $billingPeriod;
                $sub->subscription_validity = $durationMonths;
                $sub->save();
                $user_subscription = $sub;
            } else {
                $user_subscription = new UserSubscription();
                $user_subscription->user_id = $user->id;
                $user_subscription->plan_id = $tier->id;
                $user_subscription->subscription_name = $tier->name;
                $user_subscription->subscription_method = 'amount';
                $user_subscription->subscription_price = $basePrice;
                $user_subscription->billing_period = $billingPeriod;
                $user_subscription->subscription_validity = $durationMonths;
                $user_subscription->subscription_start_date = now();
                $user_subscription->subscription_expire_date = now()->addMonths($durationMonths);
                $user_subscription->save();
            }

            app(MembershipPrivilegeService::class)->applyAfterTierChange($user, $tier, $previousPlanId);

            // Record payment
            $payment = new SubscriptionPayment();
            $payment->user_id = $user->id;
            $payment->user_subscription_id = $user_subscription->id;
            $payment->transaction_id = $session->id;
            $payment->payment_method = 'Stripe';
            $payment->payment_amount = isset($session->amount_total) ? ($session->amount_total / 100) : $basePrice;
            $payment->billing_period = $billingPeriod;
            $payment->payment_status = 'Success';
            $payment->save();


            return response()->json(['status' => 'ok'], 200);
        }

        return response()->json(['status' => 'ignored']);
    }
}
