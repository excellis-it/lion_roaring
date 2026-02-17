<?php

namespace App\Http\Controllers\Estore;

use App\Http\Controllers\Controller;
use App\Models\OrderStatus;
use App\Models\EstoreOrder;
use App\Models\EstoreOrderItem;
use App\Models\EstoreSetting;
use App\Models\Product;
use App\Models\Review;
use App\Models\EstoreCart;
use App\Models\WareHouse;
use App\Models\EstorePayment;
use App\Services\PromoCodeService;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\OrderEmailTemplate;
use App\Models\User;
use App\Models\Notification;
use App\Mail\OrderNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Carbon\Carbon;

class DigitalCheckoutController extends Controller
{
    public function initiateCheckout(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        session(['digital_checkout_product_id' => $request->product_id]);

        // Clear previous digital promo code if any when starting fresh
        session()->forget(['digital_applied_promo_code', 'digital_promo_discount']);

        return redirect()->route('e-store.digital-checkout');
    }

    public function checkout()
    {
        $id = session('digital_checkout_product_id');

        if (!$id) {
            return redirect()->route('e-store')->with('error', 'No product selected for checkout.');
        }

        $product = Product::findOrFail($id);

        if ($product->product_type !== 'digital') {
            return redirect()->route('e-store.product-details', $product->slug)
                ->with('error', 'This checkout is only for digital products.');
        }

        $estoreSettings = EstoreSetting::first();

        // Check if promo code is already applied in session
        $appliedPromoCode = session('digital_applied_promo_code');
        $promoDiscount = session('digital_promo_discount', 0);

        // Calculate other charges
        $otherChargesTotal = 0;
        $detailedCharges = [];
        $productPrice = ($product->sale_price > 0) ? $product->sale_price : $product->price;

        if ($product->otherCharges) {
            foreach ($product->otherCharges as $charge) {
                $cAmount = 0;
                if ($charge->charge_type == 'percentage') {
                    $cAmount = ($productPrice * ($charge->charge_amount / 100));
                } else {
                    $cAmount = $charge->charge_amount;
                }
                $otherChargesTotal += $cAmount;
                $detailedCharges[] = [
                    'charge_name' => $charge->charge_name ?? '',
                    'charge_type' => $charge->charge_type ?? 'fixed',
                    'charge_amount' => $charge->charge_amount ?? 0,
                    'calculated_amount' => $cAmount
                ];
            }
        }

        // Calculate costs
        $subtotal = $productPrice + $otherChargesTotal;
        $discountAmount = $promoDiscount;
        $taxableAmount = max($subtotal - $discountAmount, 0);
        $taxAmount = ($taxableAmount * ($estoreSettings->tax_percentage ?? 0)) / 100;
        $total = $taxableAmount + $taxAmount;

        return view('ecom.digital-checkout', compact('product', 'estoreSettings', 'subtotal', 'taxAmount', 'total', 'appliedPromoCode', 'promoDiscount', 'otherChargesTotal', 'detailedCharges'));
    }

    public function applyPromoCode(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'promo_code' => 'required|string|max:255',
            ]);

            $id = session('digital_checkout_product_id');
            if (!$id) {
                return response()->json(['status' => false, 'message' => 'No product selected']);
            }

            $product = Product::find($id);
            if (!$product) {
                return response()->json(['status' => false, 'message' => 'Product not found']);
            }

            // Calculate other charges for promo validation
            $otherChargesTotal = 0;
            $productPrice = ($product->sale_price > 0) ? $product->sale_price : $product->price;

            if ($product->otherCharges) {
                foreach ($product->otherCharges as $charge) {
                    $cAmount = 0;
                    if ($charge->charge_type == 'percentage') {
                        $cAmount = ($productPrice * ($charge->charge_amount / 100));
                    } else {
                        $cAmount = $charge->charge_amount;
                    }
                    $otherChargesTotal += $cAmount;
                }
            }

            $subtotal = $productPrice + $otherChargesTotal;
            $isAuth = auth()->check();

            // Build cart items array for validation (just this one digital product)
            $cartItems = [
                [
                    'product_id' => $product->id,
                    'subtotal' => $subtotal,
                ]
            ];

            // Validate promo code
            $validation = PromoCodeService::validatePromoCode(
                $request->promo_code,
                $isAuth ? auth()->id() : null,
                $cartItems
            );

            if (!$validation['valid']) {
                return response()->json(['status' => false, 'message' => $validation['message']]);
            }

            $promoCode = $validation['promo_code'];
            $discountAmount = PromoCodeService::calculateDiscount($promoCode, $subtotal, $cartItems);

            // Store promo code in session specifically for digital checkout
            session(['digital_applied_promo_code' => $request->promo_code]);
            session(['digital_promo_discount' => $discountAmount]);

            return response()->json([
                'status' => true,
                'message' => $validation['message'],
                'discount_amount' => $discountAmount,
                'promo_code' => $request->promo_code,
                'is_percentage' => $promoCode->is_percentage,
                'discount_value' => $promoCode->discount_amount
            ]);
        }
    }

    public function removePromoCode(Request $request)
    {
        if ($request->ajax()) {
            session()->forget(['digital_applied_promo_code', 'digital_promo_discount']);

            return response()->json([
                'status' => true,
                'message' => 'Promo code removed successfully'
            ]);
        }
    }

    public function processCheckout(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['status' => false, 'message' => 'Please login to continue']);
        }

        $product = Product::findOrFail($request->product_id);

        if ($product->product_type !== 'digital') {
            return response()->json(['status' => false, 'message' => 'Invalid product type']);
        }

        $estoreSettings = EstoreSetting::first();

        // Calculate other charges
        $otherChargesTotal = 0;
        $detailedCharges = [];
        $productPrice = ($product->sale_price > 0) ? $product->sale_price : $product->price;

        if ($product->otherCharges) {
            foreach ($product->otherCharges as $charge) {
                $cAmount = 0;
                if ($charge->charge_type == 'percentage') {
                    $cAmount = ($productPrice * ($charge->charge_amount / 100));
                } else {
                    $cAmount = $charge->charge_amount;
                }
                $otherChargesTotal += $cAmount;
                $detailedCharges[] = [
                    'charge_name' => $charge->charge_name ?? '',
                    'charge_type' => $charge->charge_type ?? 'fixed',
                    'charge_amount' => $charge->charge_amount ?? 0,
                    'calculated_amount' => $cAmount
                ];
            }
        }

        // Recalculate totals on server
        $subtotal = $productPrice + $otherChargesTotal;

        // Get promo discount from session
        $promoDiscount = session('digital_promo_discount', 0);

        $taxableAmount = max($subtotal - $promoDiscount, 0);
        $taxAmount = ($taxableAmount * ($estoreSettings->tax_percentage ?? 0)) / 100;

        // No shipping/delivery for digital
        $shippingCost = 0;
        $deliveryCost = 0;

        $withAmount = $taxableAmount + $shippingCost + $deliveryCost + $taxAmount;

        // Initially check total without credit card fee
        $tempTotal = $withAmount;

        // Update validation rules based on tempTotal
        $rules = [
            'product_id' => 'required|exists:products,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:500',
            'address_line_2' => 'nullable|string|max:500',
            'city' => 'required|string|max:255',
            'pincode' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'terms_agreement' => 'required|accepted',
        ];

        if ($tempTotal > 0) {
            $rules['payment_method_id'] = 'required';
            $rules['payment_type'] = 'required';
        }

        $request->validate($rules);

        // Finalize credit card fee and total amount if not free
        $creditCardFee = ($tempTotal > 0 && $request->payment_type === 'credit')
            ? ($tempTotal * ($estoreSettings->credit_card_percentage ?? 0)) / 100
            : 0;

        $totalAmount = $tempTotal + $creditCardFee;

        // Determine initial status
        $statusSlug = 'pending';
        $order_status = OrderStatus::where('slug', $statusSlug)->where('is_pickup', 0)->first();

        DB::beginTransaction();

        try {
            // Payment Processing
            $paymentIntent = null;
            if ($totalAmount > 0) {
                Stripe::setApiKey(env('STRIPE_SECRET'));
                $paymentIntent = PaymentIntent::create([
                    'amount' => round($totalAmount * 100),
                    'currency' => 'usd',
                    'payment_method' => $request->payment_method_id,
                    'payment_method_types' => ['card'],
                    'confirmation_method' => 'manual',
                    'confirm' => true,
                    'receipt_email' => $request->email,
                ]);

                if ($paymentIntent->status !== 'succeeded') {
                    DB::rollback();
                    return response()->json(['status' => false, 'message' => 'Payment failed']);
                }
            }

            // Create Order
            $order = new EstoreOrder();
            $order->order_number = 'ORD-' . strtoupper(uniqid());
            $order->user_id = auth()->id();
            $order->first_name = $request->first_name;
            $order->last_name = $request->last_name;
            $order->email = $request->email;
            $order->phone = $request->phone;
            $order->address_line_1 = $request->address_line_1;
            $order->address_line_2 = $request->address_line_2;
            $order->city = $request->city;
            $order->state = $request->state;
            $order->pincode = $request->pincode;
            $order->country = $request->country ?? 'USA';
            $order->status =  $order_status ? $order_status->id : 1;
            $order->payment_status = 'paid';
            $order->payment_type = $totalAmount > 0 ? $request->payment_type : 'free';
            $order->subtotal = (float) $subtotal;
            $order->tax_amount = (float) $taxAmount;
            $order->shipping_amount = (float) $shippingCost; // Use shipping_amount instead of shipping_cost
            $order->handling_amount = (float) $deliveryCost; // Use handling_amount instead of delivery_cost
            $order->promo_discount = (float) $promoDiscount; // Use promo_discount instead of discount_amount
            $order->credit_card_fee = (float) $creditCardFee;
            $order->total_amount = (float) $totalAmount;
            $order->is_pickup = 0;
            $order->notes = "Digital Product Order" . (session('digital_applied_promo_code') ? " (Promo: " . session('digital_applied_promo_code') . ")" : "");
            $order->save();

            // Create Payment Record
            if ($totalAmount > 0) {
                $payment = new EstorePayment();
                $payment->order_id = $order->id;
                $payment->payment_id = $paymentIntent->id;
                $payment->stripe_payment_intent_id = $paymentIntent->id;
                $payment->payment_method = 'stripe';
                $payment->payment_type = $request->payment_type;
                $payment->amount = (float) $totalAmount;
                $payment->currency = 'usd';
                $payment->status = 'succeeded';
                $payment->paid_at = now();
                $payment->payment_details = $paymentIntent->toArray();
                $payment->save();
            }

            // Create Order Item
            $orderItem = new EstoreOrderItem();
            $orderItem->order_id = $order->id; // Use order_id instead of estore_order_id
            $orderItem->product_id = $product->id;
            $orderItem->product_name = $product->name;
            $orderItem->product_image = $product->getProductFirstImage() ?? null;
            $orderItem->quantity = 1;
            $orderItem->price = $productPrice;
            $orderItem->total = $productPrice + $otherChargesTotal;
            $orderItem->other_charges = json_encode($detailedCharges);
            $orderItem->save();

            // Clear digital checkout session
            session()->forget(['digital_checkout_product_id', 'digital_applied_promo_code', 'digital_promo_discount']);

            // Send notifications to super admins
            $superAdminIds = User::where('user_type_id', 1)->pluck('id')->toArray();

            if (!empty($superAdminIds)) {
                $notifications = [];
                $recipientUsers = User::whereIn('id', $superAdminIds)->get();

                foreach ($recipientUsers as $user) {
                    $notifications[] = [
                        'user_id' => $user->id,
                        'chat_id' => $order->id,
                        'message' => 'New digital order #' . $order->order_number . ' placed for ' . $product->name . '.',
                        'type' => 'Order',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Send email to admin
                    if ($user->email) {
                        try {
                            Mail::to($user->email)->queue(new OrderNotificationMail($order, $user, []));
                        } catch (\Throwable $th) {
                            Log::error('Failed to send digital order notification email to admin', [
                                'error' => $th->getMessage()
                            ]);
                        }
                    }
                }

                // Insert notifications in bulk
                Notification::insert($notifications);
            }

            // Send order confirmation email to customer (Dynamic Mail for Digital)
            try {
                $template = OrderEmailTemplate::where('slug', 'digital')->where('is_active', 1)->first();

                if ($template) {
                    $orderList = view('user.emails.order_list_table', ['order' => $order])->render();
                    $myOrdersUrl = route('e-store.my-orders');

                    $body = str_replace(
                        ['{customer_name}', '{customer_email}', '{order_list}', '{order_id}', '{arriving_date}', '{total_order_value}', '{order_details_url_button}', '{order_note}'],
                        [
                            $order->first_name . ' ' . $order->last_name,
                            $order->email ?? '',
                            $orderList,
                            $order->order_number ?? '',
                            '', // Digital products don't have arriving date usually
                            number_format($order->total_amount ?? 0, 2),
                            "<p><a href='" . $myOrdersUrl . "' style='display:inline-block;padding:10px 20px;background:#000;color:#fff;text-decoration:none;border-radius:5px;'>View Order History</a></p>",
                            $order->notes ?? '',
                        ],
                        $template->body
                    );

                    Mail::to($order->email)->send(new OrderStatusUpdatedMail($order, $body));
                } else {
                    // Fallback to direct mail if template not found
                    $orderList = view('user.emails.order_list_table', ['order' => $order])->render();
                    $myOrdersUrl = route('e-store.my-orders');

                    $body = "<p>Hello " . ($order->first_name . ' ' . $order->last_name) . ",</p>";
                    $body .= "<p>Thank you for your order. Your payment has been successfully received. You can now download the product and view the full details in your order history.</p>";
                    $body .= "<p><strong>Order Number:</strong> " . $order->order_number . "</p>";
                    $body .= $orderList;
                    $body .= "<p><strong>Total Amount:</strong> $" . number_format($order->total_amount, 2) . "</p>";
                    $body .= "<p><a href='" . $myOrdersUrl . "' style='display:inline-block;padding:10px 20px;background:#000;color:#fff;text-decoration:none;border-radius:5px;'>View Order History</a></p>";

                    Mail::to($order->email)->send(new OrderStatusUpdatedMail($order, $body));
                }
            } catch (\Throwable $th) {
                Log::error('Failed to send digital order status email to customer: ' . $th->getMessage());
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order placed successfully',
                'checkout_url' => route('e-store.order-success', $order->id)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
