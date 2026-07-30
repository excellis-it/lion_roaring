<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stripe;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\CardException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\RateLimitException;

/**
 * @group Donation
 */
class DonationController extends Controller
{
    protected $successStatus = 200;

    /**
     * Donation Api
     * @bodyParam first_name string required. Example: Robert Hyde
     * @bodyParam last_name string required. Example: Hyde
     * @bodyParam email string required. Example: hyde@yopmail.com
     * @bodyParam address string required. Example: New york
     * @bodyParam city string required. Example: New york
     * @bodyParam state string required. Example: London
     * @bodyParam postcode string required. Example: ZP74857
     * @bodyParam amount integer required. Example: 150
     * @bodyParam country_id integer required. The country_id of the country. Example: 2
     * @bodyParam stripeToken string required.
     */

    public function donation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postcode' => 'required|string|max:50',
            'amount' => 'required|numeric|min:1',
            'country_id' => 'required',
            'stripeToken' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => false], 422);
        }

        try {
            if (empty(config('services.stripe.secret'))) {
                return response()->json([
                    'message' => 'Payment is temporarily unavailable. Please try again later.',
                    'status' => false,
                ], 503);
            }

            Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $amountCents = (int) round(((float) $request->amount) * 100);
            if ($amountCents < 100) {
                return response()->json([
                    'message' => 'Minimum donation amount is US$ 1.00.',
                    'status' => false,
                ], 422);
            }

            $charge = Stripe\Charge::create([
                'amount' => $amountCents,
                'currency' => 'usd',
                'source' => $request->stripeToken,
                'description' => 'Donation',
                'receipt_email' => $request->email,
            ]);

            if ($charge->status == 'succeeded') {
                $donation = new Donation();
                $donation->country_id = $request->country_id;
                $donation->first_name = $request->first_name;
                $donation->last_name = $request->last_name;
                $donation->email = $request->email;
                $donation->address = $request->address;
                $donation->city = $request->city;
                $donation->state = $request->state;
                $donation->postcode = $request->postcode;
                $donation->phone = $request->phone;
                $donation->transaction_id = $charge->id;
                $donation->donation_type = $request->donation_type;
                $donation->donation_amount = $request->amount;
                $donation->currency = 'usd';
                $donation->payment_method = 'Stripe';
                $donation->payment_status = 'Success';
                $donation->save();

                return response()->json(['message' => 'Payment success.', 'status' => true, 'data' => $donation], 200);
            }

            return response()->json([
                'message' => 'Your payment could not be completed. Please check your card details and try again.',
                'status' => false,
            ], 402);
        } catch (CardException $e) {
            return response()->json([
                'message' => $this->friendlyStripeMessage($e),
                'status' => false,
            ], 402);
        } catch (InvalidRequestException $e) {
            return response()->json([
                'message' => 'Invalid payment details. Please check your card information and try again.',
                'status' => false,
            ], 422);
        } catch (AuthenticationException $e) {
            Log::error('API donation Stripe authentication error', ['message' => $e->getMessage()]);

            return response()->json([
                'message' => 'Payment is temporarily unavailable. Please try again later.',
                'status' => false,
            ], 503);
        } catch (ApiConnectionException|RateLimitException $e) {
            return response()->json([
                'message' => 'Unable to reach the payment provider. Please try again in a moment.',
                'status' => false,
            ], 503);
        } catch (\Throwable $th) {
            Log::error('API donation payment error', [
                'message' => $th->getMessage(),
                'type' => get_class($th),
            ]);

            return response()->json([
                'message' => 'Payment could not be processed. Please check your card details and try again.',
                'status' => false,
            ], 500);
        }
    }

    private function friendlyStripeMessage(CardException $e): string
    {
        $message = trim((string) $e->getMessage());
        if ($message !== '') {
            return $message;
        }

        $code = $e->getDeclineCode() ?: $e->getStripeCode();

        return match ($code) {
            'insufficient_funds' => 'Your card has insufficient funds.',
            'lost_card', 'stolen_card' => 'Your card was declined. Please contact your card issuer or try another card.',
            'expired_card' => 'Your card has expired. Please use a different card.',
            'incorrect_cvc', 'invalid_cvc' => 'Your card\'s security code is incorrect.',
            'incorrect_number', 'invalid_number' => 'Your card number is invalid.',
            'card_declined' => 'Your card was declined. Please try another card or contact your bank.',
            default => 'Your card was declined. Please check your card details or try another card.',
        };
    }

    /**
     * Country List
     * @response 200{
     *"message": "Country list.",
     *"status": true,
     *"data": [
     *    {
     *        "id": 1,
     *        "code": "AF",
     *        "name": "Afghanistan",
     *        "created_at": null,
     *        "updated_at": null
     *    },
     *    {
     *        "id": 2,
     *        "code": "AX",
     *        "name": "Åland Islands",
     *        "created_at": null,
     *        "updated_at": null
     *    }
     * ]
     * }
     * @response 201{
     * "message": "Country not found.",
     * "status": false
     * }
     */

    public function countryList()
    {
        $countries = Country::orderBy('name', 'asc')->get();
        if ($countries) {
            return response()->json(['message' => 'Country list.', 'status' => true, 'data' => $countries], $this->successStatus);
        } else {
            return response()->json(['message' => 'Country not found.', 'status' => false], 201);
        }
    }
}
