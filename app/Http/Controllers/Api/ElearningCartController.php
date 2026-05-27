<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ElearningCart;
use App\Models\ElearningProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group E-Learning Cart
 */
class ElearningCartController extends Controller
{
    /**
     * GET /e-learning/cart
     */
    public function index(): JsonResponse
    {
        $items = ElearningCart::with('product.image')
            ->where('user_id', auth()->id())
            ->orderByDesc('id')
            ->get();

        $subtotal = (float) $items->sum(fn ($c) => (float) $c->price * (int) $c->quantity);

        return response()->json([
            'status' => true,
            'message' => 'Cart.',
            'data' => [
                'items' => $items,
                'item_count' => $items->count(),
                'subtotal' => round($subtotal, 2),
            ],
        ]);
    }

    /**
     * POST /e-learning/cart/add
     * @bodyParam elearning_product_id int required
     * @bodyParam quantity int optional Defaults to 1.
     */
    public function add(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'elearning_product_id' => 'required|integer|exists:elearning_products,id',
            'quantity' => 'nullable|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $product = ElearningProduct::find($request->elearning_product_id);
        if (!$product || (int) $product->status !== 1) {
            return response()->json(['status' => false, 'message' => 'Product unavailable.'], 404);
        }

        $qty = (int) $request->input('quantity', 1);

        $cart = ElearningCart::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'elearning_product_id' => $product->id,
            ],
            [
                'quantity' => $qty,
                'price' => (float) $product->price,
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Added to cart.',
            'data' => $cart->load('product.image'),
        ]);
    }

    /**
     * POST /e-learning/cart/update
     * @bodyParam cart_id int required
     * @bodyParam quantity int required
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $cart = ElearningCart::where('user_id', auth()->id())->find($request->cart_id);
        if (!$cart) {
            return response()->json(['status' => false, 'message' => 'Cart item not found.'], 404);
        }

        $cart->quantity = (int) $request->quantity;
        $cart->save();

        return response()->json([
            'status' => true,
            'message' => 'Cart updated.',
            'data' => $cart->load('product.image'),
        ]);
    }

    /**
     * POST /e-learning/cart/remove
     * @bodyParam cart_id int required
     */
    public function remove(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $deleted = ElearningCart::where('user_id', auth()->id())
            ->where('id', $request->cart_id)
            ->delete();

        if (!$deleted) {
            return response()->json(['status' => false, 'message' => 'Cart item not found.'], 404);
        }

        return response()->json(['status' => true, 'message' => 'Removed from cart.']);
    }

    /**
     * POST /e-learning/cart/clear
     */
    public function clear(): JsonResponse
    {
        ElearningCart::where('user_id', auth()->id())->delete();

        return response()->json(['status' => true, 'message' => 'Cart cleared.']);
    }

    /**
     * GET /e-learning/cart/count
     */
    public function count(): JsonResponse
    {
        $count = ElearningCart::where('user_id', auth()->id())->sum('quantity');

        return response()->json([
            'status' => true,
            'message' => 'Cart count.',
            'data' => ['count' => (int) $count],
        ]);
    }
}
