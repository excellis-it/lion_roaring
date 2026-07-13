<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MembershipBenefit;
use App\Models\MembershipMeasurement;
use App\Models\MembershipPromoCode;
use App\Models\MembershipPromoUsage;
use App\Models\MembershipTier;
use App\Models\SubscriptionPayment;
use App\Models\UserSubscription;
use App\Services\CheckoutPaymentService;
use App\Services\MembershipPricing;
use App\Services\MembershipPrivilegeService;
use App\Services\NotificationService;
use App\Services\PromoCodeValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @group Membership
 */
class MembershipApiController extends Controller
{
    protected int $successStatus = 200;

    /**
     * GET /user/membership/tiers
     * List all membership tiers with benefits.
     */
    public function tiers(): JsonResponse
    {
        $tiers = MembershipTier::with(['benefits' => function ($q) {
            $q->orderBy('sort_order');
        }])->get();

        return response()->json([
            'status' => true,
            'message' => 'Membership tiers.',
            'data' => $tiers,
        ], $this->successStatus);
    }

    /**
     * GET /user/membership/tiers/{id}
     */
    public function tierDetail(int $id): JsonResponse
    {
        $tier = MembershipTier::with(['benefits' => function ($q) {
            $q->orderBy('sort_order');
        }])->find($id);

        if (!$tier) {
            return response()->json(['status' => false, 'message' => 'Tier not found.'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Tier details.',
            'data' => $tier,
        ], $this->successStatus);
    }

    /**
     * GET /user/membership/current
     * Return the authenticated user's current (latest) subscription + tier.
     */
    public function current(): JsonResponse
    {
        $user = Auth::user();
        $sub = $user->userLastSubscription;

        if (!$sub) {
            return response()->json([
                'status' => true,
                'message' => 'No active subscription.',
                'in_app_membership' => (bool) config('lion_roaring.in_app_membership'),
                'data' => null,
            ], $this->successStatus);
        }

        $tier = MembershipTier::find($sub->plan_id);
        $now = now();
        $expireDate = $sub->subscription_expire_date
            ? \Carbon\Carbon::parse($sub->subscription_expire_date)
            : null;
        $expired = $expireDate && $expireDate->isPast();
        $daysUntilExpiry = $expireDate
            ? (int) $now->diffInDays($expireDate, false)
            : null;
        // Web PMA sets $canRenew = true whenever the user has a subscription.
        $canRenew = true;

        return response()->json([
            'status' => true,
            'message' => 'Current subscription.',
            'in_app_membership' => (bool) config('lion_roaring.in_app_membership'),
            'data' => [
                'subscription' => $sub,
                'tier' => $tier,
                'is_expired' => $expired,
                'days_until_expiry' => $daysUntilExpiry,
                'can_renew' => $canRenew,
            ],
        ], $this->successStatus);
    }

    /**
     * GET /user/membership/payment-history
     */
    public function paymentHistory(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 20);
        $payments = SubscriptionPayment::where('user_id', Auth::id())
            ->with('userSubscription')
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Payment history.',
            'data' => $payments,
        ], $this->successStatus);
    }

    /**
     * POST /user/membership/promo-code/validate
     * Body: code (string), tier_id (int)
     */
    public function validatePromoCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'tier_id' => 'required|integer|exists:membership_tiers,id',
            'billing_period' => 'nullable|in:monthly,yearly',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $result = PromoCodeValidator::validateMembership(
            $request->code,
            Auth::id(),
            (int) $request->tier_id,
            $request->input('billing_period', 'yearly')
        );

        if (!$result['valid']) {
            return response()->json(['status' => false, 'message' => $result['message']], 422);
        }

        return response()->json([
            'status' => true,
            'message' => $result['message'],
            'data' => [
                'code' => $result['code'],
                'discount_amount' => $result['discount_amount'],
                'is_percentage' => $result['is_percentage'],
                'original_price' => $result['original_price'],
                'final_price' => $result['final_price'],
                'billing_period' => $result['billing_period'] ?? MembershipPricing::PERIOD_YEARLY,
            ],
        ], $this->successStatus);
    }

    /**
     * GET /user/membership/card
     * Returns payload for the membership card screen (QR source = subscription id, tier name, expiry).
     */
    public function card(): JsonResponse
    {
        $user = Auth::user();
        $sub = $user->userLastSubscription;

        if (!$sub) {
            return response()->json(['status' => false, 'message' => 'No active subscription.'], 404);
        }

        $tier = MembershipTier::find($sub->plan_id);
        $measurement = MembershipMeasurement::first();

        $expireDate = $sub->subscription_expire_date;
        $reminderDays = (int) ($measurement->renewal_reminder_days ?? 7);
        $withinReminderWindow = $expireDate
            ? \Carbon\Carbon::parse($expireDate)->diffInDays(now(), false) >= -$reminderDays
            : false;

        return response()->json([
            'status' => true,
            'message' => 'Membership card.',
            'data' => [
                'card_title' => $measurement->membership_card_title ?? 'Membership Card',
                'holder_name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                'tier_name' => $tier->name ?? $sub->subscription_name,
                'subscription_id' => $sub->id,
                'qr_payload' => 'LRM-' . $sub->id . '-' . $user->id,
                'start_date' => $sub->subscription_start_date,
                'expire_date' => $expireDate,
                'is_within_reminder_window' => $withinReminderWindow,
            ],
        ], $this->successStatus);
    }

    /**
     * GET /user/membership/renewal-reminder
     */
    public function getRenewalReminder(): JsonResponse
    {
        $m = MembershipMeasurement::first();

        return response()->json([
            'status' => true,
            'message' => 'Renewal reminder settings.',
            'data' => [
                'renewal_reminder_days' => $m->renewal_reminder_days ?? 7,
                'renewal_reminder_subject' => $m->renewal_reminder_subject ?? null,
                'renewal_reminder_body' => $m->renewal_reminder_body ?? null,
            ],
        ], $this->successStatus);
    }

    /**
     * PUT /user/membership/renewal-reminder
     * Admin-only. Guarded by permission check inline — adjust once A9/admin middleware exists.
     */
    public function updateRenewalReminder(Request $request): JsonResponse
    {
        if (!Auth::user()->hasRole('SUPER ADMIN')) {
            return response()->json(['status' => false, 'message' => 'Unauthorized.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'renewal_reminder_days' => 'required|integer|min:1|max:365',
            'renewal_reminder_subject' => 'nullable|string|max:255',
            'renewal_reminder_body' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $m = MembershipMeasurement::first() ?? new MembershipMeasurement();
        $m->renewal_reminder_days = $request->renewal_reminder_days;
        $m->renewal_reminder_subject = $request->renewal_reminder_subject;
        $m->renewal_reminder_body = $request->renewal_reminder_body;
        $m->save();

        return response()->json([
            'status' => true,
            'message' => 'Renewal reminder settings updated.',
            'data' => $m,
        ], $this->successStatus);
    }

    /**
     * POST /user/membership/subscribe-token
     * Body: tier_id
     * For token-priced tiers only. Paid subscription + renewal requires Phase A3 payment plumbing.
     */
    public function subscribeToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tier_id' => 'required|integer|exists:membership_tiers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $user = Auth::user();
        $tier = MembershipTier::find($request->tier_id);

        if (($tier->pricing_type ?? 'amount') !== 'token') {
            return response()->json(['status' => false, 'message' => 'Tier is not token-priced.'], 422);
        }

        $previousPlanId = $user->userLastSubscription?->plan_id;

        $sub = new UserSubscription();
        $sub->user_id = $user->id;
        $sub->plan_id = $tier->id;
        $sub->subscription_method = 'token';
        $sub->subscription_name = $tier->name;
        $sub->subscription_price = $tier->life_force_energy_tokens;
        $sub->life_force_energy_tokens = $tier->life_force_energy_tokens;
        $sub->agree_accepted_at = now();
        $sub->agree_description_snapshot = $tier->agree_description;
        $duration = 12;
        $sub->subscription_validity = $duration;
        $sub->subscription_start_date = now();
        $sub->subscription_expire_date = now()->addMonths($duration);
        $sub->save();

        app(MembershipPrivilegeService::class)->applyAfterTierChange($user, $tier, $previousPlanId);

        return response()->json([
            'status' => true,
            'message' => 'Membership subscribed.',
            'data' => $sub,
        ], $this->successStatus);
    }

    /**
     * POST /user/membership/renew
     * Token tiers renew immediately. Amount tiers return `requires_payment: true` so the mobile
     * client can launch PaymentSheet via the checkout endpoint added in Phase A3.
     */
    public function renew(Request $request): JsonResponse
    {
        $user = Auth::user();
        $sub = $user->userLastSubscription;

        if (!$sub) {
            return response()->json(['status' => false, 'message' => 'No subscription to renew.'], 404);
        }

        $previousPlanId = $sub->plan_id;

        $tierId = (int) $request->input('tier_id', $sub->plan_id);
        $tier = MembershipTier::find($tierId);
        if (!$tier) {
            return response()->json(['status' => false, 'message' => 'Invalid membership tier.'], 404);
        }

        $billingPeriod = MembershipPricing::validatePeriod($request->input('billing_period'));
        $basePrice = MembershipPricing::priceFor($tier, $billingPeriod);
        $isToken = ($tier->pricing_type ?? 'amount') === 'token';

        if ($isToken) {
            $base = $sub->subscription_expire_date
                ? now()->max(\Carbon\Carbon::parse($sub->subscription_expire_date))
                : now();
            $sub->subscription_expire_date = $base->addMonths(12);
            $sub->subscription_method = 'token';
            $sub->plan_id = $tier->id;
            $sub->subscription_name = $tier->name;
            $sub->save();
            app(MembershipPrivilegeService::class)->applyAfterTierChange($user, $tier, $previousPlanId);

            return response()->json([
                'status' => true,
                'message' => 'Membership renewed.',
                'data' => $sub,
            ], $this->successStatus);
        }

        if ($basePrice <= 0) {
            $duration = MembershipPricing::durationMonthsFor($billingPeriod);
            if ($sub->plan_id == $tier->id) {
                $base = $sub->subscription_expire_date
                    ? now()->max(\Carbon\Carbon::parse($sub->subscription_expire_date))
                    : now();
                $sub->subscription_expire_date = $base->addMonths($duration);
            } else {
                $sub->plan_id = $tier->id;
                $sub->subscription_name = $tier->name;
                $sub->subscription_start_date = now();
                $sub->subscription_expire_date = now()->addMonths($duration);
            }
            $sub->subscription_method = 'amount';
            $sub->billing_period = $billingPeriod;
            $sub->subscription_validity = $duration;
            $sub->save();
            app(MembershipPrivilegeService::class)->applyAfterTierChange($user, $tier, $previousPlanId);

            return response()->json([
                'status' => true,
                'message' => 'Membership renewed.',
                'data' => $sub,
            ], $this->successStatus);
        }

        return response()->json([
            'status' => true,
            'message' => 'Payment required to complete renewal.',
            'data' => [
                'requires_payment' => true,
                'tier_id' => $tier->id,
                'amount' => $basePrice,
                'billing_period' => $billingPeriod,
                'currency' => 'USD',
            ],
        ], $this->successStatus);
    }

    /**
     * POST /user/membership/cancel
     * Expires subscription, deactivates user, notifies SUPER ADMIN users.
     */
    public function cancel(Request $request): JsonResponse
    {
        $user = Auth::user();
        $sub = $user->userLastSubscription;

        if (!$sub) {
            return response()->json(['status' => false, 'message' => 'No active subscription to cancel.'], 404);
        }

        $sub->subscription_expire_date = now();
        $sub->save();

        $user->status = 0;
        $user->save();

        $admins = \App\Models\User::whereHas('userRole', function ($q) {
            $q->where('name', 'SUPER ADMIN');
        })->where('status', 1)->get();

        $userName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));

        foreach ($admins as $admin) {
            $notification = NotificationService::saveNotification(
                $admin->id,
                '<strong>' . e($userName) . '</strong> has cancelled their <strong>' . e($sub->subscription_name) . '</strong> membership. Their account has been deactivated.',
                'membership_cancellation'
            );
            if ($notification) {
                $notification->chat_id = $user->id;
                $notification->save();
            }
        }

        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Membership cancelled and account deactivated.',
        ], $this->successStatus);
    }

    /**
     * GET /user/membership/checkout/stripe-config
     * Returns the Stripe publishable key so inline CardField can render.
     */
    public function stripeConfig(): JsonResponse
    {
        $key = config('services.stripe.key');
        if (empty($key)) {
            return response()->json(['status' => false, 'message' => 'Stripe is not configured.'], 503);
        }

        return response()->json([
            'status' => true,
            'data' => ['publishable_key' => $key],
        ], $this->successStatus);
    }

    /**
     * POST /user/membership/checkout/payment-intent
     * Creates a Stripe PaymentIntent for a paid membership tier.
     * The client then presents PaymentSheet and posts back to /checkout/confirm.
     *
     * @bodyParam tier_id int required
     * @bodyParam promo_code string optional
     */
    public function createPaymentIntent(Request $request, CheckoutPaymentService $payment): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tier_id' => 'required|integer|exists:membership_tiers,id',
            'promo_code' => 'nullable|string',
            'billing_period' => 'nullable|in:monthly,yearly',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $user = Auth::user();
        $tier = MembershipTier::find($request->tier_id);

        if (($tier->pricing_type ?? 'amount') === 'token') {
            return response()->json([
                'status' => false,
                'message' => 'Token-priced tiers do not require payment. Use /subscribe-token.',
            ], 422);
        }

        $billingPeriod = MembershipPricing::validatePeriod($request->input('billing_period'));
        $basePrice = MembershipPricing::priceFor($tier, $billingPeriod);
        $amount = $basePrice;
        $promoCode = null;
        if ($request->filled('promo_code')) {
            $result = PromoCodeValidator::validateMembership($request->promo_code, $user->id, $tier->id, $billingPeriod);
            if (!$result['valid']) {
                return response()->json(['status' => false, 'message' => $result['message']], 422);
            }
            $amount = (float) $result['final_price'];
            $promoCode = $result['code'];
        }

        // 100%-off promo on a paid tier — collect card via SetupIntent (no charge).
        if ($amount <= 0 && $basePrice > 0) {
            $setup = $payment->createSetupIntent($user, [
                'type' => 'membership',
                'tier_id' => $tier->id,
                'promo_code' => $promoCode,
                'billing_period' => $billingPeriod,
            ]);

            if (!($setup['success'] ?? false)) {
                return response()->json(['status' => false, 'message' => $setup['error'] ?? 'Could not verify card.'], 500);
            }

            return response()->json([
                'status' => true,
                'message' => 'Card verification required.',
                'data' => [
                    'tier_id' => $tier->id,
                    'amount' => 0,
                    'currency' => 'USD',
                    'promo_code' => $promoCode,
                    'billing_period' => $billingPeriod,
                    'card_verification' => true,
                    'setup_intent_id' => $setup['setup_intent_id'],
                    'client_secret' => $setup['client_secret'],
                    'ephemeral_key' => $setup['ephemeral_key'],
                    'customer_id' => $setup['customer_id'],
                    'publishable_key' => $setup['publishable_key'],
                ],
            ], $this->successStatus);
        }

        // Truly free tier — no card required.
        if ($amount <= 0) {
            return response()->json([
                'status' => true,
                'message' => 'No payment required.',
                'data' => [
                    'tier_id' => $tier->id,
                    'amount' => 0,
                    'currency' => 'USD',
                    'promo_code' => $promoCode,
                    'billing_period' => $billingPeriod,
                    'free' => true,
                ],
            ], $this->successStatus);
        }

        $intent = $payment->createIntent(
            $amount,
            'USD',
            $user,
            [
                'type' => 'membership',
                'tier_id' => $tier->id,
                'promo_code' => $promoCode,
                'billing_period' => $billingPeriod,
            ],
            cardOnly: true
        );

        if (!$intent['success']) {
            return response()->json(['status' => false, 'message' => $intent['error'] ?? 'Could not create payment intent.'], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Payment intent created.',
            'data' => [
                'tier_id' => $tier->id,
                'amount' => $amount,
                'currency' => 'USD',
                'promo_code' => $promoCode,
                'billing_period' => $billingPeriod,
                'payment_intent_id' => $intent['payment_intent_id'],
                'client_secret' => $intent['client_secret'],
                'ephemeral_key' => $intent['ephemeral_key'],
                'customer_id' => $intent['customer_id'],
                'publishable_key' => $intent['publishable_key'],
            ],
        ], $this->successStatus);
    }

    /**
     * POST /user/membership/checkout/confirm
     * Called after the mobile client's PaymentSheet succeeds. Verifies the intent,
     * creates/extends a UserSubscription + SubscriptionPayment, syncs tier permissions.
     *
     * @bodyParam payment_intent_id string required
     * @bodyParam tier_id int required
     * @bodyParam is_renew bool optional (default false)
     */
    public function confirmCheckout(Request $request, CheckoutPaymentService $payment): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'payment_intent_id' => 'required_without_all:free,setup_intent_id|nullable|string',
            'setup_intent_id' => 'required_without_all:free,payment_intent_id|nullable|string',
            'tier_id' => 'required|integer|exists:membership_tiers,id',
            'is_renew' => 'nullable|boolean',
            'free' => 'nullable|boolean',
            'promo_code' => 'nullable|string',
            'billing_period' => 'nullable|in:monthly,yearly',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $user = Auth::user();
        $tier = MembershipTier::find($request->tier_id);
        $isRenew = (bool) $request->input('is_renew', false);
        $isFree = (bool) $request->input('free', false);
        $previousPlanId = $user->userLastSubscription?->plan_id;

        $billingPeriod = MembershipPricing::validatePeriod($request->input('billing_period'));
        $basePrice = MembershipPricing::priceFor($tier, $billingPeriod);
        $duration = MembershipPricing::durationMonthsFor($billingPeriod);

        if ($isFree) {
            if ($basePrice > 0) {
                return response()->json(['status' => false, 'message' => 'Payment card details are required.'], 422);
            }
            $amount = $basePrice;
            $promoCode = null;
            if ($request->filled('promo_code')) {
                $result = PromoCodeValidator::validateMembership($request->promo_code, $user->id, $tier->id, $billingPeriod);
                if (!$result['valid']) {
                    return response()->json(['status' => false, 'message' => $result['message']], 422);
                }
                $amount = (float) $result['final_price'];
                $promoCode = $result['code'];
            }
            if ($amount > 0) {
                return response()->json(['status' => false, 'message' => 'This membership requires payment.'], 422);
            }
            $transactionId = 'FREE-' . strtoupper(uniqid());
            $amountPaid = 0.0;
            $paymentMethod = 'Free';
        } elseif ($request->filled('setup_intent_id')) {
            $existing = SubscriptionPayment::where('transaction_id', $request->setup_intent_id)->first();
            if ($existing) {
                return response()->json([
                    'status' => true,
                    'message' => 'Payment already processed.',
                    'data' => $existing->load('userSubscription'),
                ], $this->successStatus);
            }

            $verification = $payment->verifySetupIntent($request->setup_intent_id);
            if (!($verification['success'] ?? false)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Card verification not completed. Status: ' . ($verification['status'] ?? 'unknown'),
                ], 402);
            }

            $intentPeriod = $verification['metadata']['billing_period'] ?? null;
            if ($intentPeriod && $intentPeriod !== $billingPeriod) {
                return response()->json(['status' => false, 'message' => 'Billing period mismatch.'], 422);
            }

            $promoCode = $verification['metadata']['promo_code'] ?? $request->promo_code;
            $transactionId = $request->setup_intent_id;
            $amountPaid = 0.0;
            $paymentMethod = 'Stripe';
        } else {
            // Idempotency: same intent → same payment row.
            $existing = SubscriptionPayment::where('transaction_id', $request->payment_intent_id)->first();
            if ($existing) {
                return response()->json([
                    'status' => true,
                    'message' => 'Payment already processed.',
                    'data' => $existing->load('userSubscription'),
                ], $this->successStatus);
            }

            $verification = $payment->verifyIntent($request->payment_intent_id);
            if (!($verification['success'] ?? false)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Payment not completed. Status: ' . ($verification['status'] ?? 'unknown'),
                ], 402);
            }

            $intentPeriod = $verification['metadata']['billing_period'] ?? null;
            if ($intentPeriod && $intentPeriod !== $billingPeriod) {
                return response()->json(['status' => false, 'message' => 'Billing period mismatch.'], 422);
            }

            $transactionId = $request->payment_intent_id;
            $amountPaid = (float) ($verification['amount'] ?? $basePrice);
            $promoCode = $verification['metadata']['promo_code'] ?? null;
            $paymentMethod = 'Stripe';
        }

        $discountAmount = max(0, $basePrice - $amountPaid);

        $subscription = DB::transaction(function () use ($user, $tier, $isRenew, $amountPaid, $promoCode, $transactionId, $paymentMethod, $billingPeriod, $basePrice, $duration, $discountAmount, $previousPlanId) {
            if ($isRenew && $user->userLastSubscription && $user->userLastSubscription->plan_id == $tier->id) {
                $sub = $user->userLastSubscription;
                $base = $sub->subscription_expire_date
                    ? now()->max(\Carbon\Carbon::parse($sub->subscription_expire_date))
                    : now();
                $sub->subscription_expire_date = $base->addMonths($duration);
                $sub->subscription_method = 'amount';
                $sub->billing_period = $billingPeriod;
                $sub->subscription_validity = $duration;
                $sub->final_price = $amountPaid;
                $sub->promo_code = $promoCode;
                $sub->discount_amount = $discountAmount;
                $sub->save();
            } else {
                $sub = new UserSubscription();
                $sub->user_id = $user->id;
                $sub->plan_id = $tier->id;
                $sub->subscription_method = 'amount';
                $sub->subscription_name = $tier->name;
                $sub->subscription_price = $basePrice;
                $sub->billing_period = $billingPeriod;
                $sub->promo_code = $promoCode;
                $sub->discount_amount = $discountAmount;
                $sub->final_price = $amountPaid;
                $sub->subscription_validity = $duration;
                $sub->subscription_start_date = now();
                $sub->subscription_expire_date = now()->addMonths($duration);
                $sub->save();
            }

            SubscriptionPayment::create([
                'user_id' => $user->id,
                'user_subscription_id' => $sub->id,
                'transaction_id' => $transactionId,
                'payment_method' => $paymentMethod,
                'payment_amount' => $amountPaid,
                'billing_period' => $billingPeriod,
                'promo_code' => $promoCode,
                'discount_amount' => $discountAmount,
                'payment_status' => 'Success',
            ]);

            if ($promoCode) {
                $promo = MembershipPromoCode::where('code', $promoCode)->first();
                if ($promo) {
                    MembershipPromoUsage::create([
                        'promo_code_id' => $promo->id,
                        'user_id' => $user->id,
                        'user_subscription_id' => $sub->id,
                        'discount_applied' => $discountAmount,
                        'used_at' => now(),
                    ]);
                    $promo->incrementUsage();
                }
            }

            // Apply privileges inside the same transaction so a crash after payment
            // cannot leave the member without the correct role/permissions.
            app(MembershipPrivilegeService::class)->applyAfterTierChange($user, $tier, $previousPlanId);

            return $sub;
        });

        return response()->json([
            'status' => true,
            'message' => 'Subscription activated.',
            'data' => $subscription->fresh()->load('payments'),
        ], $this->successStatus);
    }
}
