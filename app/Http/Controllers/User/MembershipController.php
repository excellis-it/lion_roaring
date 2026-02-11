<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
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
use Spatie\Permission\Models\Permission;

class MembershipController extends Controller
{
    public function index()
    {
        $measurement = MembershipMeasurement::first();
        $tiers = MembershipTier::with('benefits')->get();
        $user_subscription = Auth::user()->userLastSubscription ?? null;

        // If user has subscription and it is expired, show the expired page
        if ($user_subscription && $user_subscription->subscription_expire_date) {
            $expire = \Carbon\Carbon::parse($user_subscription->subscription_expire_date);
            if ($expire->isPast()) {
                return view('user.membership.expired', compact('measurement', 'tiers', 'user_subscription'));
            }
        }

        return view('user.membership.index', compact('measurement', 'tiers', 'user_subscription'));
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
            'cost' => 'nullable|required_if:pricing_type,amount|numeric|min:0',
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
            'cost',
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
            'cost' => 'nullable|required_if:pricing_type,amount|numeric|min:0',
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
            'cost',
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
            $data = $request->only('label', 'description', 'yearly_dues');
            if ($measurement) {
                $measurement->update($data);
            } else {
                MembershipMeasurement::create($data);
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
        $date_after =  '2025-11-01';
        $members = UserSubscription::where('created_at', '>', $date_after)->with(['user', 'payments'])->orderBy('subscription_start_date', 'desc')->paginate(20);
        $measurement = MembershipMeasurement::first();
        return view('user.membership.members', compact('members', 'measurement'));
    }

    public function memberPayments(User $user)
    {
        if (!auth()->user()->can('View Membership Payments')) {
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
            $user_subscription->subscription_validity = 12;
            $user_subscription->subscription_start_date = now();
            $user_subscription->subscription_expire_date = now()->addYear();
            $user_subscription->save();

            $this->syncTierPermissions($user, $tier);

            return redirect()->route('user.membership.index')->with('success', 'Membership upgraded');
        }

        // create a new subscription — for now, make a simple record (no payment gateway integrated)
        $user_subscription = new UserSubscription();
        $user_subscription->user_id = $user->id;
        $user_subscription->plan_id = $tier->id;
        $user_subscription->subscription_method = 'amount';
        $user_subscription->subscription_name = $tier->name;
        $user_subscription->subscription_price = $tier->cost;
        $user_subscription->subscription_validity = 12; // 12 months
        $user_subscription->subscription_start_date = now();
        $user_subscription->subscription_expire_date = now()->addYear();
        $user_subscription->save();

        // record payment placeholder
        $payment = new SubscriptionPayment();
        $payment->user_id = $user->id;
        $payment->user_subscription_id = $user_subscription->id;
        $payment->transaction_id = 'manual-' . rand(1000, 9999);
        $payment->payment_method = 'Manual';
        $payment->payment_amount = $tier->cost;
        $payment->payment_status = 'Success';
        $payment->save();

        $this->syncTierPermissions($user, $tier);

        return redirect()->route('user.membership.index')->with('success', 'Membership upgraded');
    }

    public function checkout(Request $request, MembershipTier $tier)
    {
        $user = Auth::user();
        $userSub = $user->userLastSubscription ?? null;
        if (($tier->pricing_type ?? 'amount') === 'token') {
            return redirect()->route('user.membership.index')->with('error', 'This plan uses Life Force Energy tokens and does not require payment.');
        }
        if ($userSub && ($userSub->subscription_method ?? 'amount') === 'amount' && floatval($tier->cost) <= floatval($userSub->subscription_price)) {
            return redirect()->route('user.membership.index')->with('error', 'You can only upgrade to a higher tier.');
        }

        // Handle promo code if provided
        $promoCode = null;
        $discount = 0;
        $finalPrice = $tier->cost;

        if ($request->has('promo_code') && !empty($request->promo_code)) {
            $promoCode = MembershipPromoCode::where('code', $request->promo_code)->first();

            if ($promoCode && $promoCode->canBeUsedByUser($user->id) && $promoCode->canBeAppliedToTier($tier->id)) {
                $discount = $promoCode->calculateDiscount($tier->cost);
                $finalPrice = max(0, $tier->cost - $discount);
            } else {
                return redirect()->route('user.membership.index')->with('error', 'Invalid or expired promo code.');
            }
        }

        try {
            $stripe = new StripeClient(env('STRIPE_SECRET'));
            $successUrl = route('membership.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}&tier_id=' . $tier->id;

            if ($promoCode) {
                $successUrl .= '&promo_code=' . urlencode($promoCode->code);
            }

            $metadata = ['user_id' => $user->id, 'tier_id' => $tier->id];
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
                        'product_data' => ['name' => $tier->name . ' Membership'],
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
            $stripe = new StripeClient(env('STRIPE_SECRET'));
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

            if ($isRenew && $user->userLastSubscription && $user->userLastSubscription->plan_id == $tier->id) {
                // extend existing subscription
                $sub = $user->userLastSubscription;
                $sub->subscription_expire_date = now()->max($sub->subscription_expire_date)->addYear();
                $sub->subscription_method = 'amount';
                $sub->save();
                $user_subscription = $sub;
            } else {
                // new subscription purchase
                $user_subscription = new UserSubscription();
                $user_subscription->user_id = $user->id;
                $user_subscription->plan_id = $tier->id;
                $user_subscription->subscription_method = 'amount';
                $user_subscription->subscription_name = $tier->name;
                $user_subscription->subscription_price = $tier->cost;
                $user_subscription->promo_code = $promoCode ? $promoCode->code : null;
                $user_subscription->discount_amount = $discount;
                $user_subscription->final_price = isset($session->amount_total) ? ($session->amount_total / 100) : $tier->cost;
                $user_subscription->life_force_energy_tokens = null;
                $user_subscription->agree_accepted_at = null;
                $user_subscription->agree_description_snapshot = null;
                $user_subscription->subscription_validity = 12; // 12 months by default
                $user_subscription->subscription_start_date = now();
                $user_subscription->subscription_expire_date = now()->addYear();
                $user_subscription->save();
            }

            $this->syncTierPermissions($user, $tier);

            $payment = new SubscriptionPayment();
            $payment->user_id = $user->id;
            $payment->user_subscription_id = $user_subscription->id;
            $payment->transaction_id = $session->id;
            $payment->payment_method = 'Stripe';
            $payment->payment_amount = isset($session->amount_total) ? ($session->amount_total / 100) : $tier->cost;
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
        $sub = $user->userLastSubscription;
        if (!$sub) {
            return redirect()->route('user.membership.index')->with('error', 'No subscription to renew');
        }

        $tier = MembershipTier::find($sub->plan_id);
        if (!$tier) {
            return redirect()->route('user.membership.index')->with('error', 'Invalid membership tier');
        }

        if (($tier->pricing_type ?? 'amount') === 'token') {
            $sub->subscription_expire_date = now()->max($sub->subscription_expire_date)->addYear();
            $sub->subscription_method = 'token';
            $sub->save();
            $this->syncTierPermissions($user, $tier);
            return redirect()->route('user.membership.index')->with('success', 'Membership renewed successfully.');
        }

        // Use Stripe checkout for renewals to collect payment
        try {
            $stripe = new StripeClient(env('STRIPE_SECRET'));
            $successUrl = route('membership.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}&tier_id=' . $tier->id . '&renew=1';
            $session = $stripe->checkout->sessions->create([
                'success_url' => $successUrl,
                'cancel_url' => route('user.membership.index'),
                'payment_method_types' => ['card'],
                'client_reference_id' => $user->id,
                'customer_email' => $user->email,
                'line_items' => [[
                    'price_data' => [
                        'product_data' => ['name' => $tier->name . ' Membership Renewal'],
                        'unit_amount' => (int) ($tier->cost * 100),
                        'currency' => 'usd',
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'metadata' => ['user_id' => $user->id, 'tier_id' => $tier->id, 'renew' => '1'],
            ]);
            return redirect($session->url);
        } catch (\Throwable $th) {
            return redirect()->route('user.membership.index')->with('error', $th->getMessage());
        }
    }

    public function tokenSubscribe(Request $request, MembershipTier $tier)
    {
        $user = Auth::user();
        if (($tier->pricing_type ?? 'amount') !== 'token') {
            return redirect()->route('user.membership.index')->with('error', 'Invalid plan type.');
        }

        $user_subscription = new UserSubscription();
        $user_subscription->user_id = $user->id;
        $user_subscription->plan_id = $tier->id;
        $user_subscription->subscription_method = 'token';
        $user_subscription->subscription_name = $tier->name;
        $user_subscription->subscription_price = $tier->life_force_energy_tokens;
        $user_subscription->life_force_energy_tokens = $tier->life_force_energy_tokens;
        $user_subscription->agree_accepted_at = now();
        $user_subscription->agree_description_snapshot = $tier->agree_description;
        $user_subscription->subscription_validity = 12;
        $user_subscription->subscription_start_date = now();
        $user_subscription->subscription_expire_date = now()->addYear();
        $user_subscription->save();

        $this->syncTierPermissions($user, $tier);

        return redirect()->route('user.membership.index')->with('success', 'Membership subscribed successfully.');
    }

    private function syncTierPermissions($user, $tier)
    {
        if (!empty($tier->permissions)) {
            $permissions = explode(',', $tier->permissions);
            // We give permissions directly or we could use roles.
            // Since the user asked to "add permissions", we'll sync them.
            // Note: syncPermissions replaces existing ones. If we want to ADD, we use givePermissionTo.
            // However, usually membership permissions should be consistent for that tier.
            $user->syncPermissions($permissions);
        }
    }
}
