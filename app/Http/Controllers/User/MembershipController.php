<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MembershipTier;
use App\Models\MembershipMeasurement;
use App\Models\MembershipBenefit;
use App\Models\UserSubscription;
use App\Models\SubscriptionPayment;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Stripe\StripeClient;

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
        return view('user.membership.create');
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
        ]);

        $tier = MembershipTier::create($request->only([
            'name',
            'slug',
            'description',
            'cost',
            'pricing_type',
            'life_force_energy_tokens',
            'agree_description',
        ]));
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
        return view('user.membership.edit', compact('tier'));
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
        ]);

        $membership->update($request->only([
            'name',
            'slug',
            'description',
            'cost',
            'pricing_type',
            'life_force_energy_tokens',
            'agree_description',
        ]));
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
        $date_after =  '2025-11-01';
        $payments = SubscriptionPayment::where('created_at', '>', $date_after)->with(['user', 'userSubscription'])->orderBy('id', 'desc')->paginate(20);
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
        try {
            $stripe = new StripeClient(env('STRIPE_SECRET'));
            $successUrl = route('membership.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}&tier_id=' . $tier->id;
            $session = $stripe->checkout->sessions->create([
                'success_url' => $successUrl,
                'cancel_url' => route('user.membership.index'),
                'payment_method_types' => ['card'],
                'client_reference_id' => $user->id,
                'customer_email' => $user->email,
                'line_items' => [[
                    'price_data' => [
                        'product_data' => ['name' => $tier->name . ' Membership'],
                        'unit_amount' => (int) ($tier->cost * 100),
                        'currency' => 'usd',
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'metadata' => ['user_id' => $user->id, 'tier_id' => $tier->id],
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
                $user_subscription->life_force_energy_tokens = null;
                $user_subscription->agree_accepted_at = null;
                $user_subscription->agree_description_snapshot = null;
                $user_subscription->subscription_validity = 12; // 12 months by default
                $user_subscription->subscription_start_date = now();
                $user_subscription->subscription_expire_date = now()->addYear();
                $user_subscription->save();
            }

            $payment = new SubscriptionPayment();
            $payment->user_id = $user->id;
            $payment->user_subscription_id = $user_subscription->id;
            $payment->transaction_id = $session->id;
            $payment->payment_method = 'Stripe';
            $payment->payment_amount = isset($session->amount_total) ? ($session->amount_total / 100) : $tier->cost;
            $payment->payment_status = 'Success';
            $payment->save();

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

        return redirect()->route('user.membership.index')->with('success', 'Membership subscribed successfully.');
    }
}
