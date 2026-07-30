<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\CardException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\RateLimitException;

class DonationController extends Controller
{
    public function donation(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postcode' => 'required|string|max:50',
            'amount' => 'required|numeric|min:1',
            'country_id' => 'required|integer',
            'stripeToken' => 'required|string',
        ]);

        try {
            if (empty(config('services.stripe.secret'))) {
                return $this->donationFailed(
                    'Payment is temporarily unavailable. Please try again later or use bank transfer.'
                );
            }

            Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $amountCents = (int) round(((float) $request->amount) * 100);
            if ($amountCents < 100) {
                return $this->donationFailed('Minimum donation amount is US$ 1.00.');
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
                session()->put('donation_amount', $request->amount);
                session()->put('transaction_id', $charge->id);

                return redirect()->route('thankyou')->with('success', 'Donation successful');
            }

            return $this->donationFailed(
                'Your payment could not be completed. Please check your card details and try again.'
            );
        } catch (CardException $e) {
            return $this->donationFailed($this->friendlyStripeMessage($e));
        } catch (InvalidRequestException $e) {
            return $this->donationFailed(
                'Invalid payment details. Please check your card information and try again.'
            );
        } catch (AuthenticationException $e) {
            Log::error('Donation Stripe authentication error', ['message' => $e->getMessage()]);

            return $this->donationFailed(
                'Payment is temporarily unavailable. Please try again later or use bank transfer.'
            );
        } catch (ApiConnectionException|RateLimitException $e) {
            return $this->donationFailed(
                'Unable to reach the payment provider. Please try again in a moment.'
            );
        } catch (\Throwable $th) {
            Log::error('Donation payment error', [
                'message' => $th->getMessage(),
                'type' => get_class($th),
            ]);

            return $this->donationFailed(
                'Payment could not be processed. Please check your card details and try again.'
            );
        }
    }

    public function thankyou()
    {
        return view('frontend.thankyou');
    }

    private function donationFailed(string $message)
    {
        return back()
            ->withInput()
            ->with('error', $message)
            ->with('open_donation_modal', true);
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
}
