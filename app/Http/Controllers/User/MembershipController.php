<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\CheckoutPaymentService;
use App\Services\MembershipPrivilegeService;
use App\Services\MembershipPricing;
use Illuminate\Http\Request;
use App\Models\MembershipTier;
use App\Models\MembershipMeasurement;
use App\Models\MembershipBenefit;
use App\Models\MembershipPromoCode;
use App\Models\MembershipPromoUsage;
use App\Models\UserSubscription;
use App\Models\SubscriptionPayment;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Stripe\StripeClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MembershipController extends Controller
{
    public function index()
    {
        $measurement = MembershipMeasurement::first();
        $tiers = MembershipTier::with('benefits')->get();
        $user_subscription = Auth::user()->userLastSubscription ?? null;

        $isExpired = false;
        if ($user_subscription && $user_subscription->subscription_expire_date) {
            $expire = \Carbon\Carbon::parse($user_subscription->subscription_expire_date);
            if ($expire->isPast()) {
                $isExpired = true;
            }
        }

        return view('user.membership.index', compact('measurement', 'tiers', 'user_subscription', 'isExpired'));
    }

    // Management functions
    public function manage()
    {
        if (!auth()->user()->can('Manage Membership')) {
            abort(403, 'Unauthorized');
        }
        $tiers = MembershipTier::with('benefits')->get();
        $measurement = MembershipMeasurement::first();
        return view('user.membership.manage', compact('tiers', 'measurement'));
    }



    public function create()
    {
        if (!auth()->user()->can('Create Membership')) {
            abort(403, 'Unauthorized');
        }

        $allPermissions = Permission::all();
        $partnerController = new PartnerController();
        $data = $partnerController->permissionsArray($allPermissions);
        $allPermsArray = $data['allPermsArray'];
        $categorizedPermissions = $data['categorizedPermissions'];

        return view('user.membership.create', compact('allPermissions', 'allPermsArray', 'categorizedPermissions'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('Create Membership')) {
            abort(403, 'Unauthorized');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:membership_tiers,slug',
            'pricing_type' => 'required|in:amount,token',
            'monthly_cost' => 'nullable|required_if:pricing_type,amount|numeric|min:0',
            'yearly_cost' => 'nullable|required_if:pricing_type,amount|numeric|min:0',
            'life_force_energy_tokens' => 'nullable|required_if:pricing_type,token|numeric|min:0',
            'agree_description' => 'nullable|required_if:pricing_type,token|string',
            'benefits' => 'array',
            'benefits.*' => 'nullable|string|max:250',
        ], [
            'benefits.*.max' => 'Each benefit text must not exceed 250 characters.',
        ]);

        $data = $request->only([
            'name',
            'slug',
            'description',
            'monthly_cost',
            'yearly_cost',
            'pricing_type',
            'life_force_energy_tokens',
            'agree_description',
        ]);
        $data['permissions'] = $request->has('permissions') ? implode(',', $request->permissions) : null;

        $tier = MembershipTier::create($data);
        $benefits = $request->input('benefits', []);
        foreach ($benefits as $i => $b) {
            if (!empty($b)) {
                MembershipBenefit::create(['tier_id' => $tier->id, 'benefit' => $b, 'sort_order' => $i]);
            }
        }
        return redirect()->route('user.membership.manage')->with('success', 'Tier created');
    }

    public function edit(MembershipTier $membership)
    {
        if (!auth()->user()->can('Edit Membership')) {
            abort(403, 'Unauthorized');
        }
        $tier = $membership->load('benefits');
        $allPermissions = Permission::all();
        $currentPermissions = !empty($tier->permissions) ? explode(',', $tier->permissions) : [];

        $partnerController = new PartnerController();
        $data = $partnerController->permissionsArray($allPermissions);
        $allPermsArray = $data['allPermsArray'];
        $categorizedPermissions = $data['categorizedPermissions'];

        return view('user.membership.edit', compact('tier', 'allPermissions', 'currentPermissions', 'allPermsArray', 'categorizedPermissions'));
    }

    public function updateTier(Request $request, MembershipTier $membership)
    {
        if (!auth()->user()->can('Edit Membership')) {
            abort(403, 'Unauthorized');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:membership_tiers,slug,' . $membership->id,
            'pricing_type' => 'required|in:amount,token',
            'monthly_cost' => 'nullable|required_if:pricing_type,amount|numeric|min:0',
            'yearly_cost' => 'nullable|required_if:pricing_type,amount|numeric|min:0',
            'life_force_energy_tokens' => 'nullable|required_if:pricing_type,token|numeric|min:0',
            'agree_description' => 'nullable|required_if:pricing_type,token|string',
            'benefits' => 'array',
            'benefits.*' => 'nullable|string|max:250',
        ], [
            'benefits.*.max' => 'Each benefit text must not exceed 250 characters.',
        ]);

        $data = $request->only([
            'name',
            'slug',
            'description',
            'monthly_cost',
            'yearly_cost',
            'pricing_type',
            'life_force_energy_tokens',
            'agree_description',
        ]);
        $data['permissions'] = $request->has('permissions') ? implode(',', $request->permissions) : null;

        $membership->update($data);
        // update benefits
        MembershipBenefit::where('tier_id', $membership->id)->delete();
        $benefits = $request->input('benefits', []);
        foreach ($benefits as $i => $b) {
            if (!empty($b)) {
                MembershipBenefit::create(['tier_id' => $membership->id, 'benefit' => $b, 'sort_order' => $i]);
            }
        }
        return redirect()->route('user.membership.manage')->with('success', 'Tier updated');
    }

    public function delete(MembershipTier $membership)
    {
        if (!auth()->user()->can('Delete Membership')) {
            abort(403, 'Unauthorized');
        }

        // Check if any users have subscribed to this tier
        $activeSubscriptions = UserSubscription::where('plan_id', $membership->id)->count();

        if ($activeSubscriptions > 0) {
            return redirect()->route('user.membership.manage')
                ->with('error', 'Cannot delete this membership tier. one or more users have subscribed to this plan.');
        }

        $membership->delete();
        return redirect()->route('user.membership.manage')->with('success', 'Tier removed');
    }

    public function settings(Request $request)
    {
        if (!auth()->user()->can('View Membership Settings')) {
            abort(403, 'Unauthorized');
        }
        $measurement = MembershipMeasurement::first();
        if ($request->isMethod('post')) {
            if (!auth()->user()->can('Edit Membership Settings')) {
                abort(403, 'Unauthorized');
            }
            $validated = $request->validate([
                'label' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'yearly_dues' => 'nullable|numeric',
                'membership_card_title' => 'nullable|string|max:255',
                'renewal_reminder_days' => 'nullable|integer|min:1|max:365',
                'renewal_reminder_subject' => 'nullable|string|max:255',
                'renewal_reminder_body' => 'nullable|string',
                'post_expiry_reminder_subject' => 'nullable|string|max:255',
                'post_expiry_reminder_body' => 'nullable|string',
                'post_expiry_interval_1_days' => 'nullable|integer|min:1|max:365',
                'post_expiry_interval_2_days' => 'nullable|integer|min:1|max:365',
                'post_expiry_interval_3_days' => 'nullable|integer|min:1|max:365',
            ]);

            $validated['renewal_reminder_days'] = $request->filled('renewal_reminder_days')
                ? (int) $request->input('renewal_reminder_days')
                : 7;

            if ($measurement) {
                $measurement->update($validated);
            } else {
                MembershipMeasurement::create($validated);
            }
            return redirect()->route('user.membership.manage')->with('success', 'Measurement updated');
        }
        return view('user.membership.settings', compact('measurement'));
    }

    public function members(Request $request)
    {
        if (!auth()->user()->can('View Membership Members')) {
            abort(403, 'Unauthorized');
        }

        $date_after = '2025-11-01';
        $query = UserSubscription::where('created_at', '>', $date_after)
            ->with(['user', 'payments']);

        if (!auth()->user()->hasNewRole('SUPER ADMIN')) {
            $query->where('user_id', auth()->id());
        }

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subscription_name', 'like', "%{$search}%")
                    ->orWhere('promo_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Method Filter (Amount/Token)
        if ($request->filled('method')) {
            $query->where('subscription_method', $request->method);
        }

        // Date From Filter
        if ($request->filled('date_from')) {
            $query->whereDate('subscription_start_date', '>=', $request->date_from);
        }

        // Date To Filter
        if ($request->filled('date_to')) {
            $query->whereDate('subscription_start_date', '<=', $request->date_to);
        }

        $members = $query->orderBy('subscription_start_date', 'desc')->paginate(15);
        $measurement = MembershipMeasurement::first();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('user.membership.partials.members_table', compact('members', 'measurement'))->render(),
            ]);
        }

        return view('user.membership.members', compact('members', 'measurement'));
    }

    public function bulkUpdateExpireDate(Request $request)
    {
        if (!auth()->user()->hasNewRole('SUPER ADMIN')) {
            abort(403, 'Unauthorized');
        }

        if (!auth()->user()->can('Manage Membership')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'expire_date' => 'required|date',
            // Optional: apply to current filters
            'search' => 'nullable|string|max:255',
            'method' => 'nullable|in:amount,token',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $expireDate = \Carbon\Carbon::parse($validated['expire_date'])->startOfDay();

        $date_after = '2025-11-01';
        $query = UserSubscription::where('created_at', '>', $date_after);

        if (!empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('subscription_name', 'like', "%{$search}%")
                    ->orWhere('promo_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($validated['method'])) {
            $query->where('subscription_method', $validated['method']);
        }

        if (!empty($validated['date_from'])) {
            $query->whereDate('subscription_start_date', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->whereDate('subscription_start_date', '<=', $validated['date_to']);
        }

        $updated = 0;
        DB::transaction(function () use ($query, $expireDate, &$updated) {
            $updated = $query->update([
                'subscription_expire_date' => $expireDate,
                'reminder_for_expire_date' => $expireDate,
            ]);
        });

        return redirect()
            ->route('user.membership.members', $request->only(['search', 'method', 'date_from', 'date_to']))
            ->with('success', "Expiration date updated for {$updated} member(s).");
    }

    public function updateMemberExpireDate(Request $request, UserSubscription $subscription)
    {
        if (!auth()->user()->can('Edit Membership Expire Date')) {
            abort(403, 'Unauthorized');
        }

        // Non-super admins can only edit their own subscription
        if (!auth()->user()->hasNewRole('SUPER ADMIN') && (int) $subscription->user_id !== (int) auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'expire_date' => 'required|date',
        ]);

        $expireDate = \Carbon\Carbon::parse($validated['expire_date'])->startOfDay();
        $subscription->subscription_expire_date = $expireDate;
        $subscription->reminder_for_expire_date = $expireDate;
        $subscription->save();

        return response()->json([
            'status' => true,
            'subscription_id' => $subscription->id,
            'expire_date' => $expireDate->format('M d, Y'),
        ]);
    }

    public function memberPayments(User $user)
    {
        if (!auth()->user()->can('View Membership Payments')) {
            abort(403, 'Unauthorized');
        }

        if (!auth()->user()->hasNewRole('SUPER ADMIN') && $user->id != auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $date_after =  '2025-11-01';
        $payments = SubscriptionPayment::where('user_id', $user->id)->where('created_at', '>', $date_after)->with('userSubscription')->orderBy('id', 'desc')->get();
        return view('user.membership.payments', compact('payments', 'user'));
    }

    public function payments(Request $request)
    {
        if (!auth()->user()->can('View Membership Payments')) {
            abort(403, 'Unauthorized');
        }

        $date_after = '2025-11-01';
        $query = SubscriptionPayment::where('created_at', '>', $date_after)
            ->with(['user', 'userSubscription']);

        if (!auth()->user()->hasNewRole('SUPER ADMIN')) {
            $query->where('user_id', auth()->id());
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('promo_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        // Payment method filter
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        // Promo code filter
        if ($request->filled('has_promo')) {
            if ($request->has_promo == 'yes') {
                $query->whereNotNull('promo_code');
            } elseif ($request->has_promo == 'no') {
                $query->whereNull('promo_code');
            }
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $payments = $query->paginate(15);

        // AJAX response
        if ($request->ajax()) {
            return response()->json([
                'html' => view('user.membership.partials.payments_table', compact('payments'))->render(),
                'pagination' => view('user.membership.partials.pagination', compact('payments'))->render(),
            ]);
        }

        return view('user.membership.payments_all', compact('payments'));
    }

    public function upgrade(Request $request, MembershipTier $tier)
    {
        $user = Auth::user();
        $billingPeriod = MembershipPricing::validatePeriod($request->input('billing_period'));
        $basePrice = MembershipPricing::priceFor($tier, $billingPeriod);
        $durationMonths = ($tier->pricing_type ?? 'amount') === 'token'
            ? 12
            : MembershipPricing::durationMonthsFor($billingPeriod);

        // Handle promo code if provided
        $promoCode = null;
        $discount = 0;
        if ($request->has('promo_code') && !empty($request->promo_code)) {
            $promoCode = MembershipPromoCode::where('code', $request->promo_code)->first();
            if ($promoCode && $promoCode->canBeUsedByUser($user->id) && $promoCode->canBeAppliedToTier($tier->id)) {
                $discount = $promoCode->calculateDiscount($basePrice);
            } else {
                return redirect()->route('user.membership.index')->with('error', 'Invalid or expired promo code.');
            }
        }

        $previousPlanId = $user->userLastSubscription?->plan_id;

        if (($tier->pricing_type ?? 'amount') === 'token') {
            $user_subscription = new UserSubscription();
            $user_subscription->user_id = $user->id;
            $user_subscription->plan_id = $tier->id;
            $user_subscription->subscription_method = 'token';
            $user_subscription->subscription_name = $tier->name;
            $user_subscription->subscription_price = $tier->life_force_energy_tokens;
            $user_subscription->life_force_energy_tokens = $tier->life_force_energy_tokens;
            $user_subscription->agree_accepted_at = now();
            $user_subscription->agree_description_snapshot = $tier->agree_description;
            $user_subscription->promo_code = $promoCode ? $promoCode->code : null;
            $user_subscription->discount_amount = $discount;
            $user_subscription->subscription_validity = $durationMonths;
            $user_subscription->subscription_start_date = now();
            $user_subscription->subscription_expire_date = now()->addMonths($durationMonths);
            $user_subscription->save();

            app(MembershipPrivilegeService::class)->applyAfterTierChange($user, $tier, $previousPlanId);

            // Record promo code usage
            if ($promoCode) {
                MembershipPromoUsage::create([
                    'promo_code_id' => $promoCode->id,
                    'user_id' => $user->id,
                    'user_subscription_id' => $user_subscription->id,
                    'discount_applied' => $discount,
                    'used_at' => now()
                ]);
                $promoCode->incrementUsage();
            }

            return redirect()->route('user.membership.index')->with('success', 'Membership upgraded');
        }

        // create a new subscription — for now, make a simple record (no payment gateway integrated)
        $finalPrice = max(0, $basePrice - $discount);

        $user_subscription = new UserSubscription();
        $user_subscription->user_id = $user->id;
        $user_subscription->plan_id = $tier->id;
        $user_subscription->subscription_method = 'amount';
        $user_subscription->subscription_name = $tier->name;
        $user_subscription->subscription_price = $basePrice;
        $user_subscription->billing_period = $billingPeriod;
        $user_subscription->promo_code = $promoCode ? $promoCode->code : null;
        $user_subscription->discount_amount = $discount;
        $user_subscription->final_price = $finalPrice;
        $user_subscription->subscription_validity = $durationMonths;
        $user_subscription->subscription_start_date = now();
        $user_subscription->subscription_expire_date = now()->addMonths($durationMonths);
        $user_subscription->save();

        // record payment placeholder
        $payment = new SubscriptionPayment();
        $payment->user_id = $user->id;
        $payment->user_subscription_id = $user_subscription->id;
        $payment->transaction_id = 'manual-' . rand(1000, 9999);
        $payment->payment_method = 'Manual';
        $payment->payment_amount = $finalPrice;
        $payment->billing_period = $billingPeriod;
        $payment->promo_code = $promoCode ? $promoCode->code : null;
        $payment->discount_amount = $discount;
        $payment->payment_status = 'Success';
        $payment->save();

        app(MembershipPrivilegeService::class)->applyAfterTierChange($user, $tier, $previousPlanId);

        // Record promo code usage
        if ($promoCode) {
            MembershipPromoUsage::create([
                'promo_code_id' => $promoCode->id,
                'user_id' => $user->id,
                'user_subscription_id' => $user_subscription->id,
                'discount_applied' => $discount,
                'used_at' => now()
            ]);
            $promoCode->incrementUsage();
        }

        return redirect()->route('user.membership.index')->with('success', 'Membership upgraded');
    }

    public function checkout(Request $request, MembershipTier $tier)
    {
        $user = Auth::user();
        $userSub = $user->userLastSubscription ?? null;
        if (($tier->pricing_type ?? 'amount') === 'token') {
            return redirect()->route('user.membership.index')->with('error', 'This plan uses Life Force Energy tokens and does not require payment.');
        }
        $billingPeriod = MembershipPricing::validatePeriod($request->input('billing_period'));
        $basePrice = MembershipPricing::priceFor($tier, $billingPeriod);

        // Handle promo code if provided
        $promoCode = null;
        $discount = 0;
        $finalPrice = $basePrice;

        if ($request->has('promo_code') && !empty($request->promo_code)) {
            $promoCode = MembershipPromoCode::where('code', $request->promo_code)->first();

            if ($promoCode && $promoCode->canBeUsedByUser($user->id) && $promoCode->canBeAppliedToTier($tier->id)) {
                $discount = $promoCode->calculateDiscount($basePrice);
                $finalPrice = max(0, $basePrice - $discount);
            } else {
                return redirect()->route('user.membership.index')->with('error', 'Invalid or expired promo code.');
            }
        }

        try {
            $stripe = new StripeClient(config('services.stripe.secret'));
            $isRenew = $request->boolean('renew');
            $successUrl = route('membership.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}&tier_id=' . $tier->id;
            if ($isRenew) {
                $successUrl .= '&renew=1';
            }

            if ($promoCode) {
                $successUrl .= '&promo_code=' . urlencode($promoCode->code);
            }

            $metadata = ['user_id' => $user->id, 'tier_id' => $tier->id, 'billing_period' => $billingPeriod];
            if ($isRenew) {
                $metadata['renew'] = '1';
            }
            if ($promoCode) {
                $metadata['promo_code'] = $promoCode->code;
                $metadata['discount'] = $discount;
            }

            $session = $stripe->checkout->sessions->create([
                'success_url' => $successUrl,
                'cancel_url' => route('user.membership.index'),
                'payment_method_types' => ['card'],
                'client_reference_id' => $user->id,
                'customer_email' => $user->email,
                'line_items' => [[
                    'price_data' => [
                        'product_data' => ['name' => $tier->name . ($isRenew ? ' Renewal' : ' Membership')],
                        'unit_amount' => (int) ($finalPrice * 100),
                        'currency' => 'usd',
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'metadata' => $metadata,
            ]);
            return redirect($session->url);
        } catch (\Throwable $th) {
            return redirect()->route('user.membership.index')->with('error', $th->getMessage());
        }
    }

    public function checkoutSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');
        $tierId = $request->query('tier_id');
        if (!$sessionId || !$tierId) {
            return redirect()->route('user.membership.index')->with('error', 'Invalid session.');
        }
        try {
            $stripe = new StripeClient(config('services.stripe.secret'));
            $session = $stripe->checkout->sessions->retrieve($sessionId);
            $userId = $session->metadata->user_id ?? $session->client_reference_id ?? null;
            $tierId = $session->metadata->tier_id ?? $tierId;
            $user = User::find($userId);
            $tier = MembershipTier::find($tierId);
            if (!$user || !$tier) {
                return redirect()->route('user.membership.index')->with('error', 'Invalid session data');
            }
            $isRenew = ($session->metadata->renew ?? null) == '1' || $request->query('renew') == '1';

            // Get promo code from metadata
            $promoCodeStr = $session->metadata->promo_code ?? $request->query('promo_code') ?? null;
            $discount = $session->metadata->discount ?? 0;
            $promoCode = null;

            if ($promoCodeStr) {
                $promoCode = MembershipPromoCode::where('code', $promoCodeStr)->first();
            }

            // check duplicates
            $exists = SubscriptionPayment::where('transaction_id', $session->id)->first();
            if ($exists) {
                return redirect()->route('user.membership.index')->with('success', 'Payment processed already.');
            }

            $billingPeriod = MembershipPricing::validatePeriod($session->metadata->billing_period ?? 'yearly');
            $basePrice = MembershipPricing::priceFor($tier, $billingPeriod);
            $durationMonths = MembershipPricing::durationMonthsFor($billingPeriod);
            $previousPlanId = $user->userLastSubscription?->plan_id;

            if ($isRenew && $user->userLastSubscription && $user->userLastSubscription->plan_id == $tier->id) {
                // extend existing subscription
                $sub = $user->userLastSubscription;
                $sub->subscription_expire_date = now()->max($sub->subscription_expire_date)->addMonths($durationMonths);
                $sub->subscription_method = 'amount';
                $sub->billing_period = $billingPeriod;
                $sub->subscription_validity = $durationMonths;
                $sub->save();
                $user_subscription = $sub;
            } else {
                // new subscription purchase
                $user_subscription = new UserSubscription();
                $user_subscription->user_id = $user->id;
                $user_subscription->plan_id = $tier->id;
                $user_subscription->subscription_method = 'amount';
                $user_subscription->subscription_name = $tier->name;
                $user_subscription->subscription_price = $basePrice;
                $user_subscription->billing_period = $billingPeriod;
                $user_subscription->promo_code = $promoCode ? $promoCode->code : null;
                $user_subscription->discount_amount = $discount;
                $user_subscription->final_price = isset($session->amount_total) ? ($session->amount_total / 100) : $basePrice;
                $user_subscription->life_force_energy_tokens = null;
                $user_subscription->agree_accepted_at = null;
                $user_subscription->agree_description_snapshot = null;
                $user_subscription->subscription_validity = $durationMonths;
                $user_subscription->subscription_start_date = now();
                $user_subscription->subscription_expire_date = now()->addMonths($durationMonths);
                $user_subscription->save();
            }

            app(MembershipPrivilegeService::class)->applyAfterTierChange($user, $tier, $previousPlanId);

            $payment = new SubscriptionPayment();
            $payment->user_id = $user->id;
            $payment->user_subscription_id = $user_subscription->id;
            $payment->transaction_id = $session->id;
            $payment->payment_method = 'Stripe';
            $payment->payment_amount = isset($session->amount_total) ? ($session->amount_total / 100) : $basePrice;
            $payment->billing_period = $billingPeriod;
            $payment->promo_code = $promoCode ? $promoCode->code : null;
            $payment->discount_amount = $discount;
            $payment->payment_status = 'Success';
            $payment->save();

            // Record promo code usage and increment count
            if ($promoCode) {
                \App\Models\MembershipPromoUsage::create([
                    'promo_code_id' => $promoCode->id,
                    'user_id' => $user->id,
                    'user_subscription_id' => $user_subscription->id,
                    'discount_applied' => $discount,
                ]);
                $promoCode->incrementUsage();
            }

            return redirect()->route('user.membership.index')->with('success', 'Membership upgraded successfully via Stripe');
        } catch (\Throwable $th) {
            return redirect()->route('user.membership.index')->with('error', $th->getMessage());
        }
    }

    public function renew(Request $request)
    {
        $user = Auth::user();
        $sub  = $user->userLastSubscription;
        if (!$sub) {
            return redirect()->route('user.membership.index')->with('error', 'No subscription to renew');
        }

        $previousPlanId = $sub->plan_id;

        $planId = $request->input('plan_id', $sub->plan_id);
        $tier = MembershipTier::find($planId);
        if (!$tier) {
            return redirect()->route('user.membership.index')->with('error', 'Invalid tier');
        }

        $billingPeriod = MembershipPricing::validatePeriod($request->input('billing_period'));
        $basePrice = MembershipPricing::priceFor($tier, $billingPeriod);
        $durationMonths = MembershipPricing::durationMonthsFor($billingPeriod);

        // Resolve promo code
        $promoCode      = null;
        $discount       = 0;
        $finalPrice     = $basePrice;

        if ($request->filled('promo_code')) {
            $promoCode = MembershipPromoCode::where('code', $request->promo_code)->first();
            if ($promoCode && $promoCode->isValid()
                && $promoCode->canBeUsedByUser($user->id)
                && $promoCode->canBeAppliedToTier($tier->id)) {
                $discount   = $promoCode->calculateDiscount($basePrice);
                $finalPrice = max(0, $basePrice - $discount);
            } else {
                $promoCode = null;
            }
        }

        // Token / genuinely free tier — no card required
        if (($tier->pricing_type ?? 'amount') === 'token' || $basePrice <= 0) {
            if ($sub->plan_id == $tier->id) {
                $sub->subscription_expire_date = now()->max($sub->subscription_expire_date)->addMonths(
                    ($tier->pricing_type ?? 'amount') === 'token' ? 12 : $durationMonths
                );
            } else {
                $sub->plan_id = $tier->id;
                $sub->subscription_name = $tier->name;
                $sub->subscription_start_date = now();
                $sub->subscription_expire_date = now()->addMonths(
                    ($tier->pricing_type ?? 'amount') === 'token' ? 12 : $durationMonths
                );
            }
            $sub->subscription_method = ($tier->pricing_type ?? 'amount') === 'token' ? 'token' : 'amount';
            $sub->billing_period = $billingPeriod;
            $sub->subscription_validity = ($tier->pricing_type ?? 'amount') === 'token' ? 12 : $durationMonths;
            $sub->save();
            app(MembershipPrivilegeService::class)->applyAfterTierChange($user, $tier, $previousPlanId);
            return redirect()->route('user.membership.index')->with('success', 'Renewed successfully.');
        }

        if ($finalPrice <= 0) {
            return redirect()->route('user.membership.index')
                ->with('error', 'Please complete payment using Secure Payment to verify your card.');
        }

        // Stripe checkout
        try {
            $stripe     = new StripeClient(config('services.stripe.secret'));
            $successUrl = route('membership.checkout.success')
                . '?session_id={CHECKOUT_SESSION_ID}&tier_id=' . $tier->id . '&renew=1';

            $metadata = ['user_id' => $user->id, 'tier_id' => $tier->id, 'renew' => '1', 'billing_period' => $billingPeriod];
            if ($promoCode) {
                $successUrl .= '&promo_code=' . urlencode($promoCode->code);
                $metadata['promo_code'] = $promoCode->code;
                $metadata['discount']   = $discount;
            }

            $session = $stripe->checkout->sessions->create([
                'success_url'          => $successUrl,
                'cancel_url'           => route('user.membership.index'),
                'payment_method_types' => ['card'],
                'client_reference_id'  => $user->id,
                'customer_email'       => $user->email,
                'line_items'           => [[
                    'price_data' => [
                        'product_data' => ['name' => $tier->name . ' Renewal'],
                        'unit_amount'  => (int) ($finalPrice * 100),
                        'currency'     => 'usd',
                    ],
                    'quantity' => 1,
                ]],
                'mode'     => 'payment',
                'metadata' => $metadata,
            ]);
            return redirect($session->url);
        } catch (\Throwable $th) {
            return redirect()->route('user.membership.index')->with('error', $th->getMessage());
        }
    }

    public function applyPromo(Request $request)
    {
        $user      = Auth::user();
        $code      = $request->input('promo_code');
        $tierId    = $request->input('tier_id');
        $billingPeriod = MembershipPricing::validatePeriod($request->input('billing_period'));

        $promoCode = MembershipPromoCode::where('code', $code)->first();

        if (!$promoCode || !$promoCode->isValid()) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired promo code.']);
        }
        if (!$promoCode->canBeUsedByUser($user->id)) {
            return response()->json(['success' => false, 'message' => 'You have already used this promo code.']);
        }
        if ($tierId && !$promoCode->canBeAppliedToTier((int) $tierId)) {
            return response()->json(['success' => false, 'message' => 'This promo code does not apply to your plan.']);
        }

        $tier     = $tierId ? MembershipTier::find($tierId) : null;
        $basePrice = $tier ? MembershipPricing::priceFor($tier, $billingPeriod) : 0;
        $discount = $tier ? $promoCode->calculateDiscount($basePrice) : 0;
        $final    = $tier ? max(0, $basePrice - $discount) : null;

        $msg = 'Promo code applied!';
        if ($tier) {
            $msg .= ' Discount: $' . number_format($discount, 2) . '. Final: $' . number_format($final, 2) . '.';
        }

        return response()->json(['success' => true, 'message' => $msg, 'discount' => $discount, 'final_price' => $final]);
    }

    public function processInlinePayment(Request $request)
    {
        $user = Auth::user();

        $tier = MembershipTier::find($request->input('tier_id'));
        if (!$tier) {
            return response()->json(['success' => false, 'message' => 'Invalid membership tier.']);
        }

        $isRenew    = (bool) $request->input('renew', false);
        $billingPeriod = MembershipPricing::validatePeriod($request->input('billing_period'));
        $basePrice = MembershipPricing::priceFor($tier, $billingPeriod);
        $durationMonths = MembershipPricing::durationMonthsFor($billingPeriod);
        $previousPlanId = $user->userLastSubscription?->plan_id;
        $promoCode  = null;
        $discount   = 0;
        $finalPrice = $basePrice;

        if ($request->filled('promo_code')) {
            $promo = MembershipPromoCode::where('code', $request->promo_code)->first();
            if ($promo && $promo->isValid() && $promo->canBeUsedByUser($user->id) && $promo->canBeAppliedToTier($tier->id)) {
                $discount   = $promo->calculateDiscount($basePrice);
                $finalPrice = max(0, $basePrice - $discount);
                $promoCode  = $promo;
            }
        }

        $transactionId = null;
        $paymentStatus = 'Pending';

        if (($tier->pricing_type ?? 'amount') === 'amount' && $finalPrice > 0) {
            $stripeToken = $request->input('stripeToken');
            if (!$stripeToken || $stripeToken === 'free_tier') {
                return response()->json(['success' => false, 'message' => 'Payment card details are required.']);
            }
            try {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                $charge = \Stripe\Charge::create([
                    'amount'      => (int) ($finalPrice * 100),
                    'currency'    => 'usd',
                    'source'      => $stripeToken,
                    'description' => 'Membership ' . ($isRenew ? 'Renewal' : '') . ' - ' . $tier->name
                        . ($promoCode ? ' (Promo: ' . $promoCode->code . ')' : ''),
                ]);
                if ($charge->status !== 'succeeded') {
                    return response()->json(['success' => false, 'message' => 'Payment failed. Please try again.']);
                }
                $transactionId = $charge->id;
                $paymentStatus = 'Success';
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Payment error: ' . $e->getMessage()]);
            }
        } elseif (($tier->pricing_type ?? 'amount') === 'amount' && $finalPrice <= 0 && $basePrice > 0) {
            $stripeToken = $request->input('stripeToken');
            $cardCheck = app(CheckoutPaymentService::class)->verifyCardToken((string) $stripeToken);
            if (!($cardCheck['success'] ?? false)) {
                return response()->json(['success' => false, 'message' => $cardCheck['error'] ?? 'Payment card details are required.']);
            }
            $transactionId = $cardCheck['transaction_id'];
            $paymentStatus = 'Success';
        }

        if (($tier->pricing_type ?? 'amount') === 'amount' && $basePrice > 0 && !$transactionId) {
            return response()->json(['success' => false, 'message' => 'Payment card details are required.']);
        }

        // Create or extend subscription
        if ($isRenew && $user->userLastSubscription && $user->userLastSubscription->plan_id == $tier->id) {
            $sub = $user->userLastSubscription;
            $sub->subscription_expire_date = now()->max($sub->subscription_expire_date)->addMonths($durationMonths);
            $sub->subscription_method = 'amount';
            $sub->billing_period = $billingPeriod;
            $sub->subscription_validity = $durationMonths;
            $sub->final_price = $finalPrice;
            $sub->promo_code = $promoCode ? $promoCode->code : null;
            $sub->discount_amount = $discount;
            $sub->save();
            $userSubscription = $sub;
        } else {
            $userSubscription = new UserSubscription();
            $userSubscription->user_id        = $user->id;
            $userSubscription->plan_id        = $tier->id;
            $userSubscription->subscription_method = 'amount';
            $userSubscription->subscription_name   = $tier->name;
            $userSubscription->subscription_price  = $basePrice;
            $userSubscription->billing_period      = $billingPeriod;
            $userSubscription->promo_code          = $promoCode ? $promoCode->code : null;
            $userSubscription->discount_amount     = $discount;
            $userSubscription->final_price         = $finalPrice;
            $userSubscription->subscription_validity    = $durationMonths;
            $userSubscription->subscription_start_date  = now();
            $userSubscription->subscription_expire_date = now()->addMonths($durationMonths);
            $userSubscription->save();
        }

        app(MembershipPrivilegeService::class)->applyAfterTierChange($user, $tier, $previousPlanId);

        if ($transactionId) {
            $payment = new SubscriptionPayment();
            $payment->user_id              = $user->id;
            $payment->user_subscription_id = $userSubscription->id;
            $payment->transaction_id       = $transactionId;
            $payment->payment_method       = 'Stripe';
            $payment->payment_amount       = $finalPrice;
            $payment->billing_period       = $billingPeriod;
            $payment->promo_code           = $promoCode ? $promoCode->code : null;
            $payment->discount_amount      = $discount;
            $payment->payment_status       = $paymentStatus;
            $payment->save();

            if ($promoCode) {
                MembershipPromoUsage::create([
                    'promo_code_id'       => $promoCode->id,
                    'user_id'             => $user->id,
                    'user_subscription_id'=> $userSubscription->id,
                    'discount_applied'    => $discount,
                ]);
                $promoCode->incrementUsage();
            }
        }

        return response()->json(['success' => true, 'message' => 'Membership updated successfully.']);
    }

    public function cancel(Request $request)
    {
        $user = Auth::user();
        $sub = $user->userLastSubscription;

        if (!$sub) {
            return redirect()->route('user.membership.index')->with('error', 'No active subscription to cancel.');
        }

        // Expire the subscription immediately
        $sub->subscription_expire_date = now();
        $sub->save();

        // Deactivate the user account
        $user->status = 0;
       // $user->is_accept = 0; // Also set is_accept to 0 to prevent login
        $user->save();

        // Notify all SUPER ADMIN users
        $admins = User::whereHas('userRole', function ($q) {
            $q->where('name', 'SUPER ADMIN');
        })->where('status', 1)->get();

        $userName = $user->first_name . ' ' . $user->last_name;
        $encryptedUserId = encrypt($user->id);

        foreach ($admins as $admin) {
            $notification = \App\Services\NotificationService::saveNotification(
                $admin->id,
                '<strong>' . e($userName) . '</strong> has cancelled their <strong>' . e($sub->subscription_name) . '</strong> membership. Their account has been deactivated.',
                'membership_cancellation'
            );
            // Store the cancelled user's encrypted ID for linking to partner details
            if ($notification) {
                $notification->chat_id = $user->id;
                $notification->save();
            }
        }

        // Log the user out
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Your membership has been cancelled and your account has been deactivated.');
    }

    public function tokenSubscribe(Request $request, MembershipTier $tier)
    {
        $user = Auth::user();
        if (($tier->pricing_type ?? 'amount') !== 'token') {
            return redirect()->route('user.membership.index')->with('error', 'Invalid plan type.');
        }

        $previousPlanId = $user->userLastSubscription?->plan_id;

        $user_subscription = new UserSubscription();
        $user_subscription->user_id = $user->id;
        $user_subscription->plan_id = $tier->id;
        $user_subscription->subscription_method = 'token';
        $user_subscription->subscription_name = $tier->name;
        $user_subscription->subscription_price = $tier->life_force_energy_tokens;
        $user_subscription->life_force_energy_tokens = $tier->life_force_energy_tokens;
        $user_subscription->agree_accepted_at = now();
        $user_subscription->agree_description_snapshot = $tier->agree_description;
        $durationMonths = 12;
        $user_subscription->subscription_validity = $durationMonths;
        $user_subscription->subscription_start_date = now();
        $user_subscription->subscription_expire_date = now()->addMonths($durationMonths);
        $user_subscription->save();

        app(MembershipPrivilegeService::class)->applyAfterTierChange($user, $tier, $previousPlanId);

        return redirect()->route('user.membership.index')->with('success', 'Membership subscribed successfully.');
    }
}
