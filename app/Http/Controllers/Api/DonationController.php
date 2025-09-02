<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe;

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
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'postcode' => 'required',
            'amount' => 'required|integer',
            'country_id' => 'required',
            'stripeToken' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => false], 201);
        }

        try {
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $charge = Stripe\Charge::create([
                'amount' => $request->amount * 100,
                'currency' => 'usd',
                'source' => $request->stripeToken,
                'description' => 'Donation',
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
            } else {
                return response()->json(['message' => 'Payment failed!', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' =>  $th->getMessage(), 'status' => false], 401);
        }
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
