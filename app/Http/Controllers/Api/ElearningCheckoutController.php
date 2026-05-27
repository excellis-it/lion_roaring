<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ElearningCart;
use App\Models\ElearningOrder;
use App\Models\ElearningOrderItem;
use App\Services\CheckoutPaymentService;
use App\Services\PromoCodeValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

/**
 * @group E-Learning Checkout
 */
class ElearningCheckoutController extends Controller
{
    /**
     * POST /e-learning/checkout/display-prices
     * @bodyParam promo_code string optional
     */
    public function displayPrices(Request $request): JsonResponse
    {
        $items = $this->loadCart(auth()->id());
        if ($items->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Your cart is empty.'], 422);
        }

        $lines = $items->map(function ($c) {
            $unit = (float) $c->price;
            $qty = (int) $c->quantity;
            return [
                'cart_id' => $c->id,
                'product_id' => $c->elearning_product_id,
                'product_name' => $c->product?->name,
                'unit_price' => round($unit, 2),
                'quantity' => $qty,
                'line_total' => round($unit * $qty, 2),
            ];
        });
        $subtotal = (float) $lines->sum('line_total');

        $promoDiscount = 0.0;
        $promoError = null;
        if ($request->filled('promo_code')) {
            $cartForPromo = $items->map(fn ($c) => [
                'product_id' => $c->elearning_product_id,
                'subtotal' => (float) $c->price * (int) $c->quantity,
            ])->toArray();
            $result = PromoCodeValidator::validateEstore($request->promo_code, auth()->id(), $cartForPromo);
            if ($result['valid']) {
                $promoDiscount = $result['discount_amount'];
            } else {
                $promoError = $result['message'];
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Display prices calculated.',
            'data' => [
                'items' => $lines,
                'subtotal' => round($subtotal, 2),
                'promo_discount' => round($promoDiscount, 2),
                'total' => max(0.0, round($subtotal - $promoDiscount, 2)),
                'promo_code_error' => $promoError,
            ],
        ]);
    }

    /**
     * POST /e-learning/promo-code/validate
     * @bodyParam code string required
     */
    public function validatePromoCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $items = $this->loadCart(auth()->id());
        if ($items->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Your cart is empty.'], 422);
        }

        $cartForPromo = $items->map(fn ($c) => [
            'product_id' => $c->elearning_product_id,
            'subtotal' => (float) $c->price * (int) $c->quantity,
        ])->toArray();

        $result = PromoCodeValidator::validateEstore($request->code, auth()->id(), $cartForPromo);

        if (!$result['valid']) {
            return response()->json(['status' => false, 'message' => $result['message']], 422);
        }

        return response()->json([
            'status' => true,
            'message' => $result['message'],
            'data' => [
                'code' => $result['code'],
                'discount_amount' => $result['discount_amount'],
                'is_percentage' => $result['is_percentage'],
                'original_price' => $result['original_price'],
                'final_price' => $result['final_price'],
            ],
        ]);
    }

    /**
     * POST /e-learning/checkout/payment-intent
     * Creates a Stripe PaymentIntent + creates a pending ElearningOrder draft so the
     * mobile client has an order id to tie the payment to.
     * @bodyParam promo_code string optional
     */
    public function createPaymentIntent(Request $request, CheckoutPaymentService $payment): JsonResponse
    {
        $items = $this->loadCart(auth()->id());
        if ($items->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Your cart is empty.'], 422);
        }

        $subtotal = (float) $items->sum(fn ($c) => (float) $c->price * (int) $c->quantity);
        $promoDiscount = 0.0;
        $appliedCode = null;

        if ($request->filled('promo_code')) {
            $cartForPromo = $items->map(fn ($c) => [
                'product_id' => $c->elearning_product_id,
                'subtotal' => (float) $c->price * (int) $c->quantity,
            ])->toArray();
            $result = PromoCodeValidator::validateEstore($request->promo_code, auth()->id(), $cartForPromo);
            if (!$result['valid']) {
                return response()->json(['status' => false, 'message' => $result['message']], 422);
            }
            $promoDiscount = $result['discount_amount'];
            $appliedCode = $result['code'];
        }

        $total = max(0.0, $subtotal - $promoDiscount);
        if ($total <= 0) {
            return response()->json(['status' => false, 'message' => 'Total must be greater than zero.'], 422);
        }

        $order = DB::transaction(function () use ($items, $subtotal, $promoDiscount, $total, $appliedCode) {
            $order = ElearningOrder::create([
                'user_id' => auth()->id(),
                'subtotal' => $subtotal,
                'promo_discount' => $promoDiscount,
                'total_amount' => $total,
                'promo_code' => $appliedCode,
                'payment_status' => 'pending',
            ]);

            foreach ($items as $cart) {
                ElearningOrderItem::create([
                    'elearning_order_id' => $order->id,
                    'elearning_product_id' => $cart->elearning_product_id,
                    'product_name' => $cart->product?->name,
                    'quantity' => (int) $cart->quantity,
                    'unit_price' => (float) $cart->price,
                    'total_price' => (float) $cart->price * (int) $cart->quantity,
                ]);
            }

            return $order;
        });

        $result = $payment->createIntent(
            $total,
            'USD',
            auth()->user(),
            [
                'type' => 'elearning',
                'elearning_order_id' => $order->id,
            ]
        );

        if (!$result['success']) {
            $order->update(['payment_status' => 'failed']);
            return response()->json(['status' => false, 'message' => $result['error']], 500);
        }

        $order->update(['stripe_payment_intent_id' => $result['payment_intent_id']]);

        return response()->json([
            'status' => true,
            'message' => 'Payment intent created.',
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_intent_id' => $result['payment_intent_id'],
                'client_secret' => $result['client_secret'],
                'ephemeral_key' => $result['ephemeral_key'],
                'customer_id' => $result['customer_id'],
                'publishable_key' => $result['publishable_key'],
            ],
        ]);
    }

    /**
     * POST /e-learning/checkout/confirm
     * Called by the mobile app after PaymentSheet succeeds to finalize the order.
     * Verifies the PaymentIntent with Stripe, marks the order paid, clears cart.
     * @bodyParam order_id int required
     */
    public function confirm(Request $request, CheckoutPaymentService $payment): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $order = ElearningOrder::where('user_id', auth()->id())->find($request->order_id);
        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found.'], 404);
        }

        if ($order->payment_status === 'paid') {
            return response()->json(['status' => true, 'message' => 'Order already paid.', 'data' => $order]);
        }

        if (!$order->stripe_payment_intent_id) {
            return response()->json(['status' => false, 'message' => 'No payment intent attached to this order.'], 422);
        }

        $verification = $payment->verifyIntent($order->stripe_payment_intent_id);
        if (!($verification['success'] ?? false)) {
            return response()->json([
                'status' => false,
                'message' => 'Payment not completed. Status: ' . ($verification['status'] ?? 'unknown'),
            ], 402);
        }

        DB::transaction(function () use ($order) {
            $order->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);
            ElearningCart::where('user_id', auth()->id())->delete();
        });

        return response()->json([
            'status' => true,
            'message' => 'Order confirmed.',
            'data' => $order->load('items.product'),
        ]);
    }

    /**
     * GET /e-learning/purchases
     */
    public function purchases(Request $request): JsonResponse
    {
        $perPage = max(1, min(50, (int) $request->input('per_page', 20)));

        $orders = ElearningOrder::with('items.product')
            ->where('user_id', auth()->id())
            ->where('payment_status', 'paid')
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Purchases.',
            'data' => $orders,
        ]);
    }

    /**
     * GET /e-learning/purchases/{orderItem}/download
     * Returns a short-lived signed URL to the ProductFile if the user has paid for it.
     */
    public function download(int $orderItemId): JsonResponse
    {
        $item = ElearningOrderItem::with(['order', 'product'])->find($orderItemId);
        if (!$item || $item->order?->user_id !== auth()->id()) {
            return response()->json(['status' => false, 'message' => 'Item not found.'], 404);
        }

        if ($item->order->payment_status !== 'paid') {
            return response()->json(['status' => false, 'message' => 'Order is not paid.'], 402);
        }

        $fileLocation = $item->product?->file_location;
        if (!$fileLocation) {
            return response()->json([
                'status' => false,
                'message' => 'No downloadable file attached to this product yet.',
            ], 404);
        }

        $url = URL::temporarySignedRoute(
            'elearning.file.signed',
            now()->addMinutes(15),
            ['order_item' => $item->id]
        );

        return response()->json([
            'status' => true,
            'message' => 'Download URL issued.',
            'data' => [
                'download_url' => $url,
                'expires_at' => now()->addMinutes(15)->toIso8601String(),
            ],
        ]);
    }

    /**
     * GET signed route target. Validated via signed middleware in the route definition.
     */
    public function serveSignedFile(int $orderItemId)
    {
        $item = ElearningOrderItem::with(['order', 'product'])->find($orderItemId);
        if (!$item || !$item->product?->file_location || $item->order?->payment_status !== 'paid') {
            return response()->json(['status' => false, 'message' => 'File not available.'], 404);
        }

        $disk = Storage::disk('public');
        if (!$disk->exists($item->product->file_location)) {
            return response()->json(['status' => false, 'message' => 'File missing from storage.'], 404);
        }

        return $disk->download(
            $item->product->file_location,
            $item->product->name . '.' . pathinfo($item->product->file_location, PATHINFO_EXTENSION)
        );
    }

    private function loadCart(int $userId)
    {
        return ElearningCart::with('product.image')
            ->where('user_id', $userId)
            ->get();
    }
}
