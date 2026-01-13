<?php

namespace App\Http\Controllers\Api\Estore;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\EstoreCart;
use App\Models\EstoreSetting;
use App\Models\EstoreOrder;
use App\Models\EstoreOrderItem;
use App\Models\EstorePayment;
use App\Models\WarehouseProduct;
use App\Models\WarehouseProductVariation;
use App\Models\WareHouse;
use App\Models\OrderStatus;
use App\Models\Size;
use App\Models\Color;
use App\Models\User;
use App\Models\Notification;
use App\Models\OrderEmailTemplate;
use App\Models\EstoreRefund;
use App\Services\PromoCodeService;
use App\Mail\OrderNotificationMail;
use App\Mail\OrderStatusUpdatedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\CardException;
use Stripe\Exception\RateLimitException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\ApiErrorException;
use Carbon\Carbon;

/**
 * @group Checkout
 */
class CheckoutController extends Controller
{



    /**
     * Process checkout for Flutter API
     * @bodyParam first_name string required First name
     * @bodyParam last_name string required Last name
     * @bodyParam email string required Email
     * @bodyParam phone string required Phone number
     * @bodyParam address_line_1 string required Address line 1
     * @bodyParam address_line_2 string nullable Address line 2
     * @bodyParam city string required City
     * @bodyParam state string required State
     * @bodyParam pincode string required Pincode
     * @bodyParam country string required Country
     * @bodyParam payment_method_id string required Stripe payment method ID
     * @bodyParam payment_type string required Payment type (credit/debit)
     * @bodyParam order_method int nullable Is pickup (0 or 1)
     * @bodyParam promo_code string nullable Promo code
     * @authenticated
     */
    public function processCheckout(Request $request)
    {
        // Check authentication
        if (!auth()->check()) {
            return response()->json([
                'status' => false,
                'message' => 'Please login to continue'
            ], 401);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
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
            'payment_method_id' => 'required',
            'payment_type' => 'required',
            'order_method' => 'nullable|integer|in:0,1',
            'promo_code' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $is_pickup = $request->order_method ?? 0;

        // Get user's cart
        $carts = EstoreCart::where('user_id', auth()->id())
            ->with(['product.otherCharges', 'warehouseProduct'])
            ->get();

        if ($carts->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Your cart is empty'
            ], 400);
        }

        $estoreSettings = EstoreSetting::first();

        // Validate stock and recalculate prices
        $hasBlockingStockIssue = false;
        $recalculatedSubtotal = 0;
        $cartItems = [];
        $stockIssues = [];

        foreach ($carts as $cart) {
            $warehouseProduct = $cart->warehouseProduct;
            $currentWarehousePrice = $warehouseProduct?->price ?? 0;
            $availableQty = $warehouseProduct?->quantity ?? 0;

            // Update price if changed
            if (($cart->price ?? 0) != $currentWarehousePrice && !($cart->product?->is_free)) {
                $cart->old_price = $cart->price;
                $cart->price = $currentWarehousePrice;
                $cart->save();
            }

            // Check stock availability
            if ($availableQty <= 0) {
                $hasBlockingStockIssue = true;
                $cart->rejected_reason = 'out_of_stock';
                $stockIssues[] = $cart->product->name ?? 'Unknown Product';
                continue;
            } elseif ($cart->quantity > $availableQty) {
                $cart->quantity = $availableQty;
                $cart->save();
            }

            $cart->other_charges = $cart->product?->otherCharges?->sum('charge_amount') ?? 0;
            $unitPrice = $cart->price ?? $currentWarehousePrice;
            $itemSubtotal = ($unitPrice * $cart->quantity) + ($cart->other_charges ?? 0);
            $recalculatedSubtotal += $itemSubtotal;

            $cartItems[] = [
                'product_id' => $cart->product_id,
                'subtotal' => $itemSubtotal,
            ];
        }

        $subtotal = $recalculatedSubtotal;

        if ($subtotal <= 0 && $hasBlockingStockIssue) {
            return response()->json([
                'status' => false,
                'message' => 'Some items are out of stock: ' . implode(', ', $stockIssues),
                'out_of_stock_items' => $stockIssues
            ], 422);
        }

        // Apply promo code discount
        $appliedPromoCode = $request->promo_code;
        $promoDiscount = 0;

        if ($appliedPromoCode && $subtotal > 0) {
            $validation = PromoCodeService::validatePromoCode($appliedPromoCode, auth()->id(), $cartItems);
            if ($validation['valid']) {
                $promoDiscount = PromoCodeService::calculateDiscount($validation['promo_code'], $subtotal, $cartItems);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => $validation['message'] ?? 'Invalid promo code'
                ], 422);
            }
        }

        // Calculate shipping, delivery, and tax
        $shippingCost = $deliveryCost = $taxAmount = 0;

        // calculate total items in cart
        $totalItems = array_sum(array_map(fn($c) => isset($c['quantity']) ? (int)$c['quantity'] : 0, $carts->toArray()));

        if ($estoreSettings) {
            if ($is_pickup == 0 || !$estoreSettings->is_pickup_available) {
                // If shipping rules exist, use them; otherwise fallback to legacy flat rates
                if (is_array($estoreSettings->shipping_rules) && count($estoreSettings->shipping_rules) > 0) {
                    $shippingForQty = $estoreSettings->getShippingForQuantity($totalItems);
                    $shippingCost = $shippingForQty['shipping_cost'];
                    $deliveryCost = $shippingForQty['delivery_cost'];
                } else {
                    $shippingCost = $estoreSettings->shipping_cost ?? 0;
                    $deliveryCost = $estoreSettings->delivery_cost ?? 0;
                }
            }
            $taxAmount = (($subtotal - $promoDiscount) * ($estoreSettings->tax_percentage ?? 0)) / 100;
        }

        $withAmount = $subtotal - $promoDiscount + $shippingCost + $deliveryCost + $taxAmount;
        $creditCardFee = ($request->payment_type === 'credit')
            ? ($withAmount * ($estoreSettings->credit_card_percentage ?? 0)) / 100
            : 0;

        $totalAmount = $withAmount + $creditCardFee;

        // Begin transaction
        DB::beginTransaction();

        try {
            // Process payment with Stripe
            $paymentIntent = null;
            if ($totalAmount > 0) {
                Stripe::setApiKey(env('STRIPE_SECRET'));

                try {
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
                        return response()->json([
                            'status' => false,
                            'message' => 'Payment failed. Please try again.'
                        ], 402);
                    }
                } catch (CardException $e) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message' => 'Card error: ' . $e->getMessage()
                    ], 402);
                }
            }

            // Create order
            $warehouseId = $carts->first()->warehouse_id ?? null;
            $wareHouse = WareHouse::find($warehouseId);
            $order_status = OrderStatus::where('slug', 'processing')->first();

            $order = EstoreOrder::create([
                'warehouse_id' => $warehouseId,
                'is_pickup' => $is_pickup,
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
                'country' => $request->country,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingCost + $deliveryCost,
                'total_amount' => $totalAmount,
                'credit_card_fee' => $creditCardFee,
                'payment_type' => $request->payment_type,
                'payment_status' => 'paid',
                'status' => $order_status->id ?? null,
                'warehouse_name' => $wareHouse->name ?? null,
                'warehouse_address' => $wareHouse->address ?? null,
                'promo_code' => $appliedPromoCode,
                'promo_discount' => $promoDiscount,
            ]);

            // Create order items and update stock
            foreach ($carts as $cart) {
                $size = Size::find($cart->size_id);
                $color = Color::find($cart->color_id);
                $wareHouseProduct = WareHouse::find($cart->warehouse_id);

                EstoreOrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'warehouse_product_id' => $cart->warehouse_product_id ?? null,
                    'warehouse_id' => $cart->warehouse_id,
                    'product_name' => $cart->product->name ?? 'Unknown Product',
                    'product_image' => $cart->product->getProductFirstImage($cart->color_id) ?? null,
                    'price' => $cart->price ?? ($cart->warehouseProduct->price ?? 0),
                    'quantity' => $cart->quantity,
                    'size_id' => $cart->size_id,
                    'color_id' => $cart->color_id,
                    'size' => $size->size ?? null,
                    'color' => $color->color_name ?? null,
                    'warehouse_name' => $wareHouseProduct->name ?? null,
                    'warehouse_address' => $wareHouseProduct->address ?? null,
                    'other_charges' => json_encode(
                        $cart->product?->otherCharges?->map(fn($charge) => [
                            'charge_name' => $charge->charge_name ?? '',
                            'charge_amount' => $charge->charge_amount ?? 0,
                        ]) ?? []
                    ),
                    'total' => ((($cart->price ?? ($cart->warehouseProduct?->price ?? 0))) * $cart->quantity) + ($cart->other_charges ?? 0),
                ]);

                // Update warehouse product quantity
                $warehouseProduct = WarehouseProduct::where('warehouse_id', $cart->warehouse_id)
                    ->where('id', $cart->warehouse_product_id)
                    ->where('product_id', $cart->product_id)
                    ->first();

                if ($warehouseProduct) {
                    $warehouseProduct->decrement('quantity', $cart->quantity);
                    Log::info('Warehouse product stock updated', [
                        'product_id' => $warehouseProduct->id,
                        'new_quantity' => $warehouseProduct->quantity
                    ]);

                    // Update warehouse product variation
                    $wareHouseProductVariation = WarehouseProductVariation::where('warehouse_id', $cart->warehouse_id)
                        ->where('product_variation_id', $warehouseProduct->product_variation_id)
                        ->where('product_id', $cart->product_id)
                        ->first();

                    if ($wareHouseProductVariation) {
                        $newWarehouseQty = max(0, ($wareHouseProductVariation->warehouse_quantity ?? 0) - $cart->quantity);
                        $wareHouseProductVariation->warehouse_quantity = $newWarehouseQty;
                        $wareHouseProductVariation->updated_at = now();
                        $wareHouseProductVariation->save();
                        Log::info('Warehouse product variation stock updated', [
                            'warehouse_product_variation_id' => $wareHouseProductVariation->id,
                            'new_quantity' => $newWarehouseQty
                        ]);
                    } else {
                        Log::warning('WarehouseProductVariation not found for order item', [
                            'warehouse_id' => $cart->warehouse_id,
                            'warehouse_product_id' => $cart->warehouse_product_id,
                            'product_id' => $cart->product_id,
                            'order_item_id' => $cart->id ?? null
                        ]);
                    }

                    // Notify admin if out of stock
                    if (method_exists($this, 'notifyAdminIfOutOfStock')) {
                        $this->notifyAdminIfOutOfStock($warehouseProduct);
                    }
                }
            }

            // Send notifications to warehouse admins and super admins
            $groupByWareHouse = $carts->groupBy('warehouse_id');
            $allWarehouseAdminIds = [];

            foreach ($groupByWareHouse as $warehouseId => $warehouseCarts) {
                $warehouse = WareHouse::find($warehouseId);

                if ($warehouse && method_exists($warehouse, 'admins') && $warehouse->admins()->exists()) {
                    $warehouseAdmins = $warehouse->admins()->pluck('users.id')->toArray();
                    $allWarehouseAdminIds = array_merge($allWarehouseAdminIds, $warehouseAdmins);
                }
            }

            $allWarehouseAdminIds = array_unique($allWarehouseAdminIds);
            $superAdminIds = User::where('user_type_id', 1)->pluck('id')->toArray();
            $recipientIds = array_unique(array_merge($allWarehouseAdminIds, $superAdminIds));

            if (!empty($recipientIds)) {
                $notifications = [];
                $recipientUsers = User::whereIn('id', $recipientIds)->get();

                foreach ($recipientUsers as $user) {
                    $notifications[] = [
                        'user_id' => $user->id,
                        'chat_id' => $order->id,
                        'message' => 'New order #' . $order->order_number . ' placed with ' . $carts->count() . ' item(s).',
                        'type' => 'Order',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Send email to admin
                    if ($user->email) {
                        try {
                            Mail::to($user->email)->queue(new OrderNotificationMail($order, $user, $warehouseCarts ?? []));
                        } catch (\Throwable $th) {
                            Log::error('Failed to send order notification email', [
                                'error' => $th->getMessage()
                            ]);
                        }
                    }
                }

                // Insert notifications in bulk
                Notification::insert($notifications);
            }

            // Create payment record
            if ($paymentIntent) {
                EstorePayment::create([
                    'order_id' => $order->id,
                    'stripe_payment_intent_id' => $paymentIntent->id,
                    'payment_method' => 'stripe',
                    'payment_type' => $request->payment_type,
                    'amount' => $totalAmount,
                    'currency' => 'USD',
                    'status' => 'succeeded',
                    'payment_details' => [
                        'payment_intent_id' => $paymentIntent->id,
                        'amount_received' => $paymentIntent->amount_received,
                        'currency' => $paymentIntent->currency,
                    ],
                    'paid_at' => now()
                ]);
            }

            // Send order confirmation email to customer
            $isPickup = (bool)($order->is_pickup ?? false);
            $template = OrderEmailTemplate::where('order_status_id', $order_status->id)
                ->where('is_active', 1)
                ->where('is_pickup', $isPickup)
                ->first();

            if (!$template) {
                $template = OrderEmailTemplate::where('order_status_id', $order_status->id)
                    ->where('is_active', 1)
                    ->first();
            }

            if ($template) {
                try {
                    // For API, we don't use blade views, send simplified email
                    $orderList = view('user.emails.order_list_table', ['order' => $order])->render();

                    $body = str_replace(
                        ['{customer_name}', '{customer_email}', '{order_list}', '{order_id}', '{arriving_date}', '{total_order_value}'],
                        [
                            $order->first_name . ' ' . $order->last_name,
                            $order->email ?? '',
                            $orderList,
                            $order->order_number ?? '',
                            $order->expected_delivery_date ? Carbon::parse($order->expected_delivery_date)->format('M d, Y') : '',
                            number_format($order->total_amount ?? 0, 2),
                        ],
                        $template->body
                    );

                    Mail::to($order->email)->send(new OrderStatusUpdatedMail($order, $body));
                } catch (\Throwable $th) {
                    Log::error('Failed to send order status email: ' . $th->getMessage());
                }
            }

            // Clear cart
            EstoreCart::where('user_id', auth()->id())->delete();

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
                    'subtotal' => $order->subtotal,
                    'tax_amount' => $order->tax_amount,
                    'shipping_amount' => $order->shipping_amount,
                    'promo_discount' => $order->promo_discount,
                    'payment_status' => $order->payment_status,
                    'status' => $order->status,
                    'created_at' => $order->created_at,
                ]
            ], 200);
        } catch (RateLimitException $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Too many requests: ' . $e->getMessage()
            ], 429);
        } catch (InvalidRequestException | AuthenticationException | ApiConnectionException | ApiErrorException $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Stripe error: ' . $e->getMessage()
            ], 402);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Checkout failed: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Order success
     * @bodyParam order_id int required
     * @authenticated
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderSuccess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:estore_orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $order = EstoreOrder::find($request->order_id);

        return response()->json([
            'status' => true,
            'message' => 'Order placed successfully',
            'order' => $order
        ], 200);
    }

    /**
     * My orders
     * @authenticated
     * @return \Illuminate\Http\JsonResponse
     */
    public function myOrders(Request $request)
    {
        $orders = EstoreOrder::where('user_id', auth()->id())->where('payment_status', 'paid')->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Orders fetched successfully',
            'orders' => $orders
        ], 200);
    }



    /**
     * Order details
     * @bodyParam order_id int required Order ID
     * @authenticated
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderDetails(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:estore_orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Check authentication
        if (!auth()->check()) {
            return response()->json([
                'status' => false,
                'message' => 'Please login to view your orders'
            ], 401);
        }

        // Get order with relationships
        $order = EstoreOrder::with(['orderItems', 'payments'])
            ->where('id', $request->order_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        // Get refund status
        $order_refund = EstoreRefund::where('order_id', $order->id)->first();
        if ($order_refund) {
            $order->refund_status = $order_refund->is_approved;
            $order->refund_details = $order_refund;
        } else {
            $order->refund_status = null;
            $order->refund_details = null;
        }

        // Get estore settings
        $estoreSettings = EstoreSetting::first();
        $max_refundable_days = $estoreSettings->max_refundable_days ?? 10;

        // Get all active order statuses
        $allStatuses = OrderStatus::where('is_active', 1)
            ->orderBy('sort_order', 'asc')
            ->get();

        // Find the current status id on the order
        $currentStatusId = $order->status; // integer id

        // Handle cancelled status specially
        $cancelSlug = 'cancelled';
        $cancelStatus = $allStatuses->firstWhere('slug', $cancelSlug);

        if ($currentStatusId && $cancelStatus && $currentStatusId == $cancelStatus->id) {
            // Timeline = first (ordered) -> cancelled
            $first = $allStatuses->first();
            $timelineStatuses = collect();
            if ($first) $timelineStatuses->push($first);
            $timelineStatuses->push($cancelStatus);
        } else {
            // Normal timeline: full progression
            $timelineStatuses = $allStatuses;
        }

        // Calculate index of current status in timeline
        $statusIndex = $timelineStatuses->search(function ($s) use ($currentStatusId) {
            return $s->id == $currentStatusId;
        });

        // If not found (custom status etc.), append it to timeline for display
        if ($statusIndex === false && $currentStatusId) {
            $currentStatusModel = OrderStatus::find($currentStatusId);
            if ($currentStatusModel) {
                $timelineStatuses = $timelineStatuses->push($currentStatusModel);
                $statusIndex = $timelineStatuses->count() - 1;
            } else {
                $statusIndex = -1;
            }
        }

        // Get cart count for user
        $cartCount = EstoreCart::where('user_id', auth()->id())->count();

        // Get current status details
        $currentStatus = OrderStatus::find($currentStatusId);

        return response()->json([
            'status' => true,
            'message' => 'Order details fetched successfully',
            'data' => [
                'order' => $order,
                'cart_count' => $cartCount,
                'max_refundable_days' => $max_refundable_days,
                'estore_settings' => [
                    'max_refundable_days' => $max_refundable_days,
                    'shipping_cost' => $estoreSettings->shipping_cost ?? 0,
                    'delivery_cost' => $estoreSettings->delivery_cost ?? 0,
                    'tax_percentage' => $estoreSettings->tax_percentage ?? 0,
                    'shipping_rules' => $estoreSettings->shipping_rules ?? [],
                ],
                'status_timeline' => [
                    'all_statuses' => $timelineStatuses,
                    'current_status_index' => $statusIndex,
                    'current_status' => $currentStatus,
                ],
            ]
        ], 200);
    }

    /**
     * Cancel Order
     * @bodyParam order_id int required Order ID
     * @bodyParam cancellation_reason string nullable Reason for cancellation
     * @authenticated
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelOrder(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer',
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Get order with proper validation
        $order = EstoreOrder::where('id', $request->order_id)
            ->where('user_id', auth()->id())
            ->where('payment_status', 'paid')
            ->whereIn('status', ['1', '2']) // Only allow cancellation for processing/pending orders
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found or cannot be cancelled'
            ], 404);
        }

        DB::beginTransaction();

        try {
            // Get cancelled status ID
            $cancelledStatus = OrderStatus::where('slug', 'cancelled')->first();
            $cancelledStatusId = $cancelledStatus ? $cancelledStatus->id : '5';

            // Update order status
            $order->update([
                'status' => $cancelledStatusId,
                'notes' => $request->cancellation_reason ?? null
            ]);

            // Create refund record if payment exists
            $payment = EstorePayment::where('order_id', $order->id)
                ->where('status', 'succeeded')
                ->first();

            if ($payment) {
                Stripe::setApiKey(env('STRIPE_SECRET'));

                $refund = EstoreRefund::create([
                    'payment_intent' => $payment->stripe_payment_intent_id,
                    'amount' => $payment->amount,
                    'order_id' => $order->id,
                    'user_id' => auth()->id(),
                    'reason' => $request->cancellation_reason ?? 'Customer requested cancellation',
                    'is_approved' => 0, // Pending approval
                ]);

                Log::info('Refund record created', [
                    'order_id' => $order->id,
                    'refund_id' => $refund->id,
                    'amount' => $payment->amount
                ]);
            }

            // Restock products in warehouse
            $orderItems = EstoreOrderItem::where('order_id', $order->id)->get();

            foreach ($orderItems as $item) {
                $warehouseProduct = WarehouseProduct::where('warehouse_id', $item->warehouse_id)
                    ->where('id', $item->warehouse_product_id)
                    ->where('product_id', $item->product_id)
                    ->first();

                if ($warehouseProduct) {
                    // Increment warehouse product quantity
                    $warehouseProduct->increment('quantity', $item->quantity);

                    Log::info('Warehouse product restocked', [
                        'product_id' => $warehouseProduct->id,
                        'quantity_added' => $item->quantity,
                        'new_quantity' => $warehouseProduct->quantity
                    ]);

                    // Increment warehouse product variation quantity
                    $wareHouseProductVariation = WarehouseProductVariation::where('warehouse_id', $item->warehouse_id)
                        ->where('product_variation_id', $warehouseProduct->product_variation_id)
                        ->where('product_id', $item->product_id)
                        ->first();

                    if ($wareHouseProductVariation) {
                        $wareHouseProductVariation->increment('warehouse_quantity', $item->quantity);
                        $wareHouseProductVariation->updated_at = now();
                        $wareHouseProductVariation->save();

                        Log::info('Warehouse product variation restocked', [
                            'warehouse_product_variation_id' => $wareHouseProductVariation->id,
                            'quantity_added' => $item->quantity,
                            'new_quantity' => $wareHouseProductVariation->warehouse_quantity
                        ]);
                    }
                }
            }

            DB::commit();

            // Reload order to get updated data
            $order->refresh();

            return response()->json([
                'status' => true,
                'message' => 'Order cancelled successfully. Refund request has been initiated and will be processed shortly.',
                'order' => $order,
                'refund_initiated' => $payment ? true : false
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Failed to cancel order', [
                'order_id' => $request->order_id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to cancel order: ' . $e->getMessage()
            ], 500);
        }
    }
}
