<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\SubscriptionPayment;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Stripe;

class SubscriptionController extends Controller
{

    public function subscription()
    {
        $plans = Plan::where('plan_status', 1)->orderBy('plan_price', 'asc')->get();
        return view('user.subscription')->with('plans', $plans);
    }


    public function payment($subscription_id)
    {
        $subscription = Plan::find($subscription_id);
        //  check subscription expire or not
        $user_subscription = UserSubscription::where('user_id', auth()->id())
            ->where('subscription_expire_date', '>', now())
            ->orderBy('id', 'desc')
            ->first();

        if ($user_subscription) {
            return redirect()->route('user.subscription')
                ->with('error', 'You already have an active subscription.');
        }

        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        $redirectUrl = route('stripe.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}';
        $response =  $stripe->checkout->sessions->create([
            'success_url' => $redirectUrl,
            'customer_email' => auth()->user()->email,
            'payment_method_types' => ['link', 'card'],
            'line_items' => [
                [
                    'price_data'  => [
                        'product_data' => [
                            'name' => $subscription->plan_name . ' Subscription',
                        ],
                        'unit_amount'  => 100 * $subscription->plan_price,
                        'currency'     => 'USD',
                    ],
                    'quantity'    => 1
                ],
            ],
            'mode' => 'payment',
            'allow_promotion_codes' => true
        ]);
        session()->put('subscription_id', $subscription_id);
        return redirect($response['url']);
    }


    public function stripeCheckoutSuccess(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        $session = $stripe->checkout->sessions->retrieve($request->session_id);
        info($session);

        $subscription = Plan::find(session()->get('subscription_id'));

        $user_subscription = new UserSubscription();
        $user_subscription->user_id = auth()->id();
        $user_subscription->subscription_id = $subscription->id;
        $user_subscription->subscription_name = $subscription->plan_name;
        $user_subscription->subscription_price = $subscription->plan_price;
        $user_subscription->subscription_validity = $subscription->plan_validity;
        // Calculate subscription expire date based on subscription validity and current date time example subscription validity is 1 month
        $user_subscription->subscription_start_date = now();
        $user_subscription->subscription_expire_date = now()->addMonths($subscription->plan_validity);
        $user_subscription->save();

        $payment = new SubscriptionPayment();
        $payment->user_id = auth()->id();
        $payment->user_subscription_id = $user_subscription->id;
        $payment->transaction_id = $session->payment_intent;
        $payment->payment_method = 'Stripe';
        $payment->payment_amount = $subscription->plan_price;
        $payment->payment_status = 'Success';
        $payment->save();

        session()->forget('subscription_id');
        return redirect()->route('user.profile')
            ->with('message', 'Payment successful.');
    }
}
