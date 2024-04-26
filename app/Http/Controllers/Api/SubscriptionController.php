<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe;

/**
 * @group Subscription
 */
class SubscriptionController extends Controller
{
    protected $successStatus = 200;

    /**
     * Subscription Details
     * @authenticated
     *
     * @response 200{
     * "message": "Subscription details.",
     *    "status": true,
     *    "data": [
     *        {
     *            "id": 3,
     *            "plan_name": "Gold",
     *            "plan_price": "299",
     *            "plan_validity": "3",
     *            "plan_description": "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.",
     *            "plan_status": 1,
     *            "created_at": "2024-04-25T10:49:46.000000Z",
     *            "updated_at": "2024-04-25T10:49:46.000000Z"
     *        },
     *        {
     *            "id": 2,
     *            "plan_name": "Platinum",
     *            "plan_price": "399",
     *            "plan_validity": "6",
     *            "plan_description": "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.",
     *            "plan_status": 1,
     *            "created_at": "2024-04-25T10:49:11.000000Z",
     *            "updated_at": "2024-04-25T10:49:11.000000Z"
     *        },
     *        {
     *            "id": 1,
     *            "plan_name": "Diamond",
     *            "plan_price": "499",
     *            "plan_validity": "12",
     *            "plan_description": "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.",
     *            "plan_status": 1,
     *            "created_at": "2024-04-24T09:01:31.000000Z",
     *            "updated_at": "2024-04-25T10:49:28.000000Z"
     *        }
     *    ]
     *}
     */

    public function details(Request $request)
    {
        try {
            $plans = Plan::where('plan_status', 1)->orderBy('plan_price', 'asc')->get();

            if ($plans->count() > 0) {
                return response()->json(['message' => 'Subscription details.', 'status' => true, 'data' => $plans], $this->successStatus);
            } else {
                return response()->json(['message' => 'No subscription plan found.', 'status' => false], 404);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Subscribe to a Plan
     * @authenticated
     *
     *@bodyParam plan_id integer required. The id of the plan. Example: 1
     *@response 200{
     *    "message": "Subscription payment initiated.",
     *    "status": true,
     *    "url": "https://checkout.stripe.com/c/pay/cs_test_b17uTN8wYWPUnh2q54aOSdBYOqCYaIFAFxCpTT5kf8jkyvQLo5He9ScAkM#fidkdWxOYHwnPyd1blpxYHZxWjA0SnxdZzdMXHU8MU5vZm8wPVZqSEpwQDRUXGNcXW5dT2kwREhjRlQ1RGZyTXxEVVU0UF01XFd0YUpmNE9hVjMyblxSSH9tcFR3aDx3XHY0VjBff3JIVXUyNTVdfTc3PGhSbicpJ2N3amhWYHdzYHcnP3F3cGApJ2lkfGpwcVF8dWAnPydocGlxbFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl"
     * }
     *@response 201{
     *  "message": "The plan id field is required.",
     *  "status": false
     * }
     */

    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer|exists:plans,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => false], 201);
        }

        try {
            $subscription_id = $request->plan_id;
            $subscription = Plan::find($subscription_id);
            //  check subscription expire or not
            $user_subscription = UserSubscription::where('user_id', auth()->id())
                ->where('subscription_expire_date', '>', now())
                ->orderBy('id', 'desc')
                ->first();

            if ($user_subscription) {
                return response()->json(['message' => 'You already have an active subscription.', 'status' => false], 201);
            }

            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

            $redirectUrl = route('api.stripe.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}';
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
            return response()->json(['message' => 'Subscription payment initiated.', 'status' => true, 'url' => $response['url']], $this->successStatus);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

   /**
    *Plan Subscription Success
    *@authenticated
    *@bodyParam plan_id integer required. The id of the plan. Example: 1
    *@bodyParam session_id string required. The session_id of the session. Example: cs_test_b17uTN8wYWPUnh2q54aOSdBYOqCYaIFAFxCpTT5kf8jkyvQLo5He9ScAkM
    *@response 200{
    *    "message": "Subscription success.",
    *    "status": true
    */

    public function stripeCheckoutSuccess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer|exists:plans,id',
            'session_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => false], 201);
        }

        try {
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

            $session = $stripe->checkout->sessions->retrieve($request->session_id);
            info($session);

            $subscription = Plan::find($request->plan_id);

            $user_subscription = new UserSubscription();
            $user_subscription->user_id = auth()->id();
            $user_subscription->plan_id = $request->plan_id;
            $user_subscription->subscription_start_date = now();
            $user_subscription->subscription_expire_date = now()->addMonths($subscription->plan_validity);
            $user_subscription->save();

            return response()->json(['message' => 'Subscription success.', 'status' => true], $this->successStatus);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }
}
