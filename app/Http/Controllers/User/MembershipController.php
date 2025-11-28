<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MembershipTier;
use App\Models\MembershipMeasurement;
use App\Models\UserSubscription;
use App\Models\SubscriptionPayment;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Stripe\StripeClient;
use Spatie\Permission\Models\Role;

class MembershipController extends Controller
{
    public function index()
    {
        $measurement = MembershipMeasurement::first();
        $tiers = MembershipTier::with('benefits')->get();
        $user_subscription = Auth::user()->userLastSubscription ?? null;
        return view('user.membership.index', compact('measurement', 'tiers', 'user_subscription'));
    }

    public function upgrade(Request $request, MembershipTier $tier)
    {
        $user = Auth::user();
        // create a new subscription — for now, make a simple record (no payment gateway integrated)
        $user_subscription = new UserSubscription();
        $user_subscription->user_id = $user->id;
        $user_subscription->plan_id = $tier->id;
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
        if ($userSub && $tier->cost <= $userSub->subscription_price) {
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
                $sub->save();
                $user_subscription = $sub;
            } else {
                // new subscription purchase
                $user_subscription = new UserSubscription();
                $user_subscription->user_id = $user->id;
                $user_subscription->plan_id = $tier->id;
                $user_subscription->subscription_name = $tier->name;
                $user_subscription->subscription_price = $tier->cost;
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

            // assign role
            if ($tier->role_id) {
                $role = Role::find($tier->role_id);
                if ($role) {
                    // assign the role without removing existing roles
                    $user->assignRole($role->name);
                }
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

        // Use Stripe checkout for renewals to collect payment
        try {
            $tier = MembershipTier::find($sub->plan_id);
            if (!$tier) {
                return redirect()->route('user.membership.index')->with('error', 'Invalid membership tier');
            }
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
}
