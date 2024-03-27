<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;
use Stripe;

class DonationController extends Controller
{
    public function donation(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'postcode' => 'required',
            'amount' => 'required',
            'country_id' => 'required',
            'stripeToken' => 'required',
        ]);

        try {
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $charge = Stripe\Charge::create([
                'amount' => $request->amount * 100,
                'currency' => 'usd',
                'source' => $request->stripeToken,
                'description' => 'Donation',
            ]);


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
            session()->put('donation_amount', $request->amount);
            session()->put('transaction_id', $charge->id);
            return redirect()->route('thankyou')->with('success', 'Donation successful');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function thankyou()
    {
        return view('frontend.thankyou');
    }
}
