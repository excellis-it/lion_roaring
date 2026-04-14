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
use App\Services\NotificationService;
use App\Services\PromoCodeValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\PermissionRegistrar;

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
                'data' => null,
            ], $this->successStatus);
        }

        $tier = MembershipTier::find($sub->plan_id);
        $now = now();
        $expired = $sub->subscription_expire_date && \Carbon\Carbon::parse($sub->subscription_expire_date)->isPast();

        return response()->json([
            'status' => true,
            'message' => 'Current subscription.',
            'data' => [
                'subscription' => $sub,
                'tier' => $tier,
                'is_expired' => $expired,
                'days_until_expiry' => $sub->subscription_expire_date
                    ? (int) $now->diffInDays(\Carbon\Carbon::parse($sub->subscription_expire_date), false)
                    : null,
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
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $result = PromoCodeValidator::validateMembership(
            $request->code,
            Auth::id(),
            (int) $request->tier_id
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

        $sub = new UserSubscription();
        $sub->user_id = $user->id;
        $sub->plan_id = $tier->id;
        $sub->subscription_method = 'token';
        $sub->subscription_name = $tier->name;
        $sub->subscription_price = $tier->life_force_energy_tokens;
        $sub->life_force_energy_tokens = $tier->life_force_energy_tokens;
        $sub->agree_accepted_at = now();
        $sub->agree_description_snapshot = $tier->agree_description;
        $duration = $tier->duration_months ?? 12;
        $sub->subscription_validity = $duration;
        $sub->subscription_start_date = now();
        $sub->subscription_expire_date = now()->addMonths($duration);
        $sub->save();

        $this->syncTierPermissions($user, $tier);

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

        $tier = MembershipTier::find($sub->plan_id);
        if (!$tier) {
            return response()->json(['status' => false, 'message' => 'Invalid membership tier.'], 404);
        }

        $isToken = ($tier->pricing_type ?? 'amount') === 'token' || (float) $tier->cost <= 0;

        if ($isToken) {
            $duration = $tier->duration_months ?? 12;
            $base = $sub->subscription_expire_date
                ? now()->max(\Carbon\Carbon::parse($sub->subscription_expire_date))
                : now();
            $sub->subscription_expire_date = $base->addMonths($duration);
            $sub->subscription_method = 'token';
            $sub->save();
            $this->syncTierPermissions($user, $tier);

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
                'amount' => (float) $tier->cost,
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
     * Sync Spatie permissions on tier change.
     * Mirrors User\MembershipController::syncTierPermissions. Extract to a shared service in Phase A3.
     */
    private function syncTierPermissions($user, MembershipTier $tier): void
    {
        if (!empty($tier->permissions)) {
            $permissions = array_filter(array_map('trim', explode(',', $tier->permissions)));
            $baseRoleNames = \App\Models\UserType::pluck('name')->toArray();
            $userRole = null;
            foreach ($user->roles as $role) {
                if (!in_array($role->name, $baseRoleNames)) {
                    $userRole = $role;
                    break;
                }
            }
            if ($userRole) {
                $userRole->syncPermissions($permissions);
            } else {
                $user->syncPermissions($permissions);
            }
            $directPerms = $user->getDirectPermissions()->pluck('name')->toArray();
            if (!empty($directPerms) && $userRole) {
                $user->revokePermissionTo($directPerms);
            }
        } else {
            $directPerms = $user->getDirectPermissions()->pluck('name')->toArray();
            if (!empty($directPerms)) {
                $user->revokePermissionTo($directPerms);
            }
        }

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
        $user->forgetCachedPermissions();
    }
}
