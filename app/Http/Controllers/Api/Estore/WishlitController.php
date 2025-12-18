<?php

namespace App\Http\Controllers\Api\Estore;

use App\Http\Controllers\Controller;
use App\Models\EcomWishList;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group Wishlist
 */
class WishlitController extends Controller
{
    /**
     * Add to wishlist
     * @bodyParam product_id int required Product ID
     * @authenticated
     */
    public function addToWishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        $user = auth()->user();
        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
            ]);
        }
        $wishlist = EcomWishList::where('user_id', $user->id)->where('product_id', $product->id)->first();
        if ($wishlist) {
            $wishlist->delete();
            return response()->json([
                'status' => true,
                'message' => 'Product removed from wishlist',
            ]);
        }
        $wishlist = new EcomWishList();
        $wishlist->user_id = $user->id;
        $wishlist->product_id = $product->id;
        $wishlist->save();
        return response()->json([
            'status' => true,
            'message' => 'Product added to wishlist',
        ]);
    }

    /**
     * Wishlist list
     * @return \Illuminate\Http\JsonResponse
     * @authenticated
     */
    public function wishlist(Request $request)
    {
        $user = auth()->user();
        $wishlist = EcomWishList::where('user_id', $user->id)->with('product')->get();
        return response()->json([
            'status' => true,
            'data' => $wishlist,
        ]);
    }

    /**
     * Remove from wishlist
     * @bodyParam product_id int required Product ID
     * @authenticated
     */
    public function removeFromWishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        $user = auth()->user();
        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
            ]);
        }
        $wishlist = EcomWishList::where('user_id', $user->id)->where('product_id', $product->id)->first();
        if (!$wishlist) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found in wishlist',
            ]);
        }
        $wishlist->delete();
        return response()->json([
            'status' => true,
            'message' => 'Product removed from wishlist',
        ]);
    }
}
