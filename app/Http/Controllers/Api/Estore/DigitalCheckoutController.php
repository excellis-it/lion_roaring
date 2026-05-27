<?php

namespace App\Http\Controllers\Api\Estore;

use App\Http\Controllers\Controller;
use App\Mail\OrderNotificationMail;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\EstoreOrder;
use App\Models\EstoreOrderItem;
use App\Models\EstorePayment;
use App\Models\EstoreSetting;
use App\Models\Notification;
use App\Models\OrderEmailTemplate;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ProductFile;
use App\Models\User;
use App\Services\CheckoutPaymentService;
use App\Services\PromoCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class DigitalCheckoutController extends Controller
{
    /**
     * POST /e-store/digital-checkout/display-prices
     *
     * @bodyParam product_id int required
     * @bodyParam promo_code string optional
     * @bodyParam payment_type string optional credit|debit
     * @authenticated
     */
    public function displayPrices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:products,id',
            'promo_code' => 'nullable|string|max:255',
            'payment_type' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $product = Product::with('otherCharges')->find($request->product_id);
        if (!$product || $product->product_type !== 'digital') {
            return response()->json(['status' => false, 'message' => 'This checkout is only for digital products.'], 422);
        }

        $summary = $this->buildPriceSummary(
            $product,
            $request->input('promo_code'),
            auth()->id(),
            $request->input('payment_type', 'debit')
        );

        if (!empty($summary['promo_error'])) {
            $summary['promo_code_error'] = $summary['promo_error'];
            unset($summary['promo_error']);
        }

        return response()->json([
            'status' => true,
            'message' => 'Display prices calculated.',
            'data' => $summary,
        ]);
    }

    /**
     * POST /e-store/digital-checkout/confirm
     *
     * Finalizes a digital product order after PaymentSheet (or free checkout).
     *
     * @authenticated
     */
    public function confirm(Request $request, CheckoutPaymentService $payment)
    {
        if (!auth()->check()) {
            return response()->json(['status' => false, 'message' => 'Please login to continue'], 401);
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:products,id',
            'payment_intent_id' => 'nullable|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:500',
            'address_line_2' => 'nullable|string|max:500',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pincode' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'payment_type' => 'nullable|string|max:50',
            'promo_code' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $product = Product::with(['otherCharges', 'files'])->find($request->product_id);
        if (!$product || $product->product_type !== 'digital') {
            return response()->json(['status' => false, 'message' => 'Invalid product type for digital checkout.'], 422);
        }

        $paymentIntentId = $request->input('payment_intent_id');

        if ($paymentIntentId) {
            $existingPayment = EstorePayment::where('stripe_payment_intent_id', $paymentIntentId)->first();
            if ($existingPayment) {
                $existingOrder = EstoreOrder::find($existingPayment->order_id);
                if ($existingOrder) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Order already recorded for this payment.',
                        'order_id' => $existingOrder->id,
                        'order_number' => $existingOrder->order_number,
                        'total_amount' => $existingOrder->total_amount,
                        'order' => $existingOrder,
                    ], 200);
                }
            }
        }

        $summary = $this->buildPriceSummary(
            $product,
            $request->input('promo_code'),
            auth()->id(),
            $request->input('payment_type', 'debit')
        );

        if (!empty($summary['promo_error'])) {
            return response()->json(['status' => false, 'message' => $summary['promo_error']], 422);
        }

        $totalAmount = (float) ($summary['total'] ?? 0);

        if ($totalAmount > 0) {
            if (!$paymentIntentId) {
                return response()->json(['status' => false, 'message' => 'Payment is required for this order.'], 422);
            }

            $verification = $payment->verifyIntent($paymentIntentId);
            if (!($verification['success'] ?? false)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Payment not completed. Status: ' . ($verification['status'] ?? 'unknown'),
                ], 402);
            }

            $stripeAmount = (float) ($verification['amount'] ?? 0);
            if ($stripeAmount > 0 && abs($stripeAmount - $totalAmount) > 0.02) {
                Log::warning('digitalCheckout confirm: Stripe amount differs from recomputed total', [
                    'stripe' => $stripeAmount,
                    'recomputed' => $totalAmount,
                    'user_id' => auth()->id(),
                ]);
            }
        }

        $estoreSettings = EstoreSetting::first();
        $orderStatus = OrderStatus::where('slug', 'pending')->where('is_pickup', 0)->first();

        DB::beginTransaction();

        try {
            $promoCode = $summary['applied_promo_code'] ?? null;
            $notes = 'Digital Product Order' . ($promoCode ? " (Promo: {$promoCode})" : '');

            $order = EstoreOrder::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'user_id' => auth()->id(),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'country' => $request->country ?? 'USA',
                'subtotal' => (float) ($summary['subtotal'] ?? 0),
                'tax_amount' => (float) ($summary['tax_amount'] ?? 0),
                'shipping_amount' => 0,
                'handling_amount' => 0,
                'promo_discount' => (float) ($summary['promo_discount'] ?? 0),
                'credit_card_fee' => (float) ($summary['credit_card_fee'] ?? 0),
                'total_amount' => $totalAmount,
                'payment_status' => 'paid',
                'payment_type' => $totalAmount > 0
                    ? $request->input('payment_type', 'paymentsheet')
                    : 'free',
                'status' => $orderStatus?->id ?? 1,
                'is_pickup' => 0,
                'promo_code' => $promoCode,
                'notes' => $notes,
            ]);

            $productPrice = (float) ($summary['product_price'] ?? 0);
            $otherChargesTotal = (float) ($summary['other_charges_total'] ?? 0);

            EstoreOrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_image' => $product->getProductFirstImage() ?? null,
                'quantity' => 1,
                'price' => $productPrice,
                'total' => $productPrice + $otherChargesTotal,
                'other_charges' => json_encode($summary['detailed_charges'] ?? []),
            ]);

            if ($totalAmount > 0 && $paymentIntentId) {
                EstorePayment::create([
                    'order_id' => $order->id,
                    'stripe_payment_intent_id' => $paymentIntentId,
                    'payment_method' => 'stripe',
                    'payment_type' => $request->input('payment_type', 'paymentsheet'),
                    'amount' => $totalAmount,
                    'currency' => 'USD',
                    'status' => 'succeeded',
                    'payment_details' => [
                        'payment_intent_id' => $paymentIntentId,
                        'amount' => $totalAmount,
                    ],
                    'paid_at' => now(),
                ]);
            }

            $this->notifyAdminsAndCustomer($order, $product);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order placed successfully',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total_amount' => $totalAmount,
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total_amount' => $order->total_amount,
                    'payment_status' => $order->payment_status,
                    'created_at' => $order->created_at,
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('digitalCheckout confirm failed: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Order creation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildPriceSummary(
        Product $product,
        ?string $promoCode,
        ?int $userId,
        string $paymentType
    ): array {
        $estoreSettings = EstoreSetting::first();
        $productPrice = ($product->sale_price > 0) ? (float) $product->sale_price : (float) $product->price;

        $otherChargesTotal = 0;
        $detailedCharges = [];

        foreach ($product->otherCharges ?? [] as $charge) {
            $cAmount = 0;
            if ($charge->charge_type === 'percentage') {
                $cAmount = $productPrice * ((float) $charge->charge_amount / 100);
            } else {
                $cAmount = (float) $charge->charge_amount;
            }
            $otherChargesTotal += $cAmount;
            $detailedCharges[] = [
                'charge_name' => $charge->charge_name ?? '',
                'charge_type' => $charge->charge_type ?? 'fixed',
                'charge_amount' => $charge->charge_amount ?? 0,
                'calculated_amount' => $cAmount,
            ];
        }

        $subtotal = $productPrice + $otherChargesTotal;
        $promoDiscount = 0;
        $appliedPromoCode = null;
        $promoError = null;

        if ($promoCode && $subtotal > 0) {
            $cartItems = [['product_id' => $product->id, 'subtotal' => $subtotal]];
            $validation = PromoCodeService::validatePromoCode($promoCode, $userId, $cartItems);
            if ($validation['valid']) {
                $appliedPromoCode = $promoCode;
                $promoDiscount = PromoCodeService::calculateDiscount(
                    $validation['promo_code'],
                    $subtotal,
                    $cartItems
                );
            } else {
                $promoError = $validation['message'] ?? 'Invalid promo code';
            }
        }

        $taxableAmount = max($subtotal - $promoDiscount, 0);
        $taxAmount = ($taxableAmount * ((float) ($estoreSettings->tax_percentage ?? 0))) / 100;
        $beforeCardFee = $taxableAmount + $taxAmount;

        $creditCardFee = ($beforeCardFee > 0 && $paymentType === 'credit')
            ? ($beforeCardFee * ((float) ($estoreSettings->credit_card_percentage ?? 0))) / 100
            : 0;

        $total = $beforeCardFee + $creditCardFee;

        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_price' => $productPrice,
            'other_charges_total' => $otherChargesTotal,
            'detailed_charges' => $detailedCharges,
            'subtotal' => $subtotal,
            'promo_discount' => $promoDiscount,
            'applied_promo_code' => $appliedPromoCode,
            'promo_error' => $promoError,
            'tax_amount' => $taxAmount,
            'shipping_amount' => 0,
            'handling_amount' => 0,
            'credit_card_fee' => $creditCardFee,
            'total' => $total,
            'is_free' => $total <= 0,
        ];
    }

    protected function notifyAdminsAndCustomer(EstoreOrder $order, Product $product): void
    {
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

                if ($user->email) {
                    try {
                        Mail::to($user->email)->queue(new OrderNotificationMail($order, $user, []));
                    } catch (\Throwable $th) {
                        Log::error('Failed to send digital order notification email to admin', [
                            'error' => $th->getMessage(),
                        ]);
                    }
                }
            }

            if (!empty($notifications)) {
                Notification::insert($notifications);
            }
        }

        try {
            $template = OrderEmailTemplate::where('slug', 'digital')->first();

            if ($template && $template->is_active) {
                $orderList = view('user.emails.order_list_table', ['order' => $order])->render();
                $myOrdersUrl = route('e-store.my-orders');

                $productForEmail = Product::with('files')->find($product->id);
                $emailExtras = ProductFile::emailExtrasForProducts([$productForEmail ?? $product], $order);
                $orderButtons = "<p><a href='" . $myOrdersUrl . "' style='display:inline-block;padding:10px 20px;background:#000;color:#fff;text-decoration:none;border-radius:5px;'>View Order History</a></p>"
                    . $emailExtras['html'];

                $body = str_replace(
                    ['{customer_name}', '{customer_email}', '{order_list}', '{order_id}', '{arriving_date}', '{total_order_value}', '{order_details_url_button}', '{order_note}'],
                    [
                        $order->first_name . ' ' . $order->last_name,
                        $order->email ?? '',
                        $orderList,
                        $order->order_number ?? '',
                        '',
                        number_format($order->total_amount ?? 0, 2),
                        $orderButtons,
                        $order->notes ?? '',
                    ],
                    $template->body
                );

                Mail::to($order->email)->send(new OrderStatusUpdatedMail($order, $body, $emailExtras['attachments']));
            }
        } catch (\Throwable $th) {
            Log::error('Failed to send digital order status email to customer: ' . $th->getMessage());
        }
    }
}
