<?php

namespace App\Http\Controllers\Api\Estore;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\EstoreCart;
use App\Models\Product;
use App\Models\WarehouseProduct;
use App\Models\ProductVariation;
use App\Models\Size;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Storage;

/**
 * @group E-Store Product APIs
 *
 */
class EstoreProductController extends Controller
{
    /**
     * Get all products (public)
     *
     * @queryParam page integer optional Page number. Example: 1
     * @queryParam category_id array optional Category ids to filter by. Example: [1,2]
     * @queryParam country_code string optional Country code. Example: US
     */
    /**
     * Public products list (All products)
     *
     * @queryParam page integer optional Page number. Example: 1
     * @queryParam limit integer optional Items per page. Example: 12
     * @queryParam category_id array optional Category ids to filter by. Example: [1,2]
     * @queryParam search string optional Search term. Example: book
     *
     * @response 200 {
     *  "status": true,
     *  "data": {"products": [], "products_count": 123}
     * }
     */
    public function products(Request $request, $category_id = null)
    {
        try {
            $page = (int)$request->get('page', 1);
            $limit = (int)$request->get('limit', 12);
            $offset = ($page - 1) * $limit;

            $nearbyWareHouseId = \App\Models\WareHouse::first()->id;
            $originLat = null;
            $originLng = null;
            $isUser = auth()->user();
            if ($isUser) {
                $originLat = $isUser->location_lat;
                $originLng = $isUser->location_lng;
            } else {
                $originLat = session('location_lat');
                $originLng = session('location_lng');
            }
            $nearest = Helper::getNearestWarehouse($originLat, $originLng);
            if (!empty($nearest['warehouse']->id)) {
                $nearbyWareHouseId = $nearest['warehouse']->id;
            }

            $wareHouseProducts = Product::whereHas('warehouseProducts', function ($q) use ($nearbyWareHouseId) {
                $q->where('warehouse_id', $nearbyWareHouseId)
                    ->where('quantity', '>', 0);
            })->pluck('id')->toArray();

            $products = Product::where('is_deleted', false)->where('status', 1);

            if (!empty($category_id)) {
                // if category id is a number or array
                $category_ids = is_array($category_id) ? $category_id : [$category_id];
                $products = $products->whereIn('category_id', $category_ids);
            } elseif ($request->filled('category_id')) {
                $category_ids = is_array($request->category_id) ? $request->category_id : [$request->category_id];
                $products = $products->whereIn('category_id', $category_ids);
            }

            if ($request->filled('search')) {
                $products->where('name', 'LIKE', "%{$request->search}%");
            }

            // Sorting
            if ($request->filled('sort')) {
                $sort = $request->sort;
                if ($sort == 'A to Z') $products->orderBy('name', 'asc');
                elseif ($sort == 'Z to A') $products->orderBy('name', 'desc');
                else $products->orderBy('id', 'desc');
            } else {
                $products->orderBy('id', 'desc');
            }

            $products_count = (clone $products)->count();

            $products = $products->skip($offset)->take($limit)->get()->map(function ($p) {
                $p->image_url = $p->image ? Storage::url($p->image) : null;
                $p->is_in_wishlist = $p->isInWishlist($p->id);
                return $p;
            });

            $categories = Category::whereNull('parent_id')
                ->where('status', 1)
                ->with(['children'])
                ->orderBy('name')
                ->get();

            $isAuth = auth()->check();
            $userSessionId = session()->getId();
            $cartCount = $isAuth ? EstoreCart::where('user_id', auth()->id())->count() : EstoreCart::where('session_id', $userSessionId)->count();

            return response()->json(['data' => [
                'products' => $products,
                'products_count' => $products_count,
                'categories' => $categories,
                'cart_count' => $cartCount,
                'page' => $page,
                'limit' => $limit
            ], 'status' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong. ' . $e->getMessage(), 'status' => false], 201);
        }
    }

    /**
     * Get product details
     *
     * @queryParam slug string required Product slug to fetch details. Example: product-slug
     * @response 200 {
     *  "status": true,
     *  "data": {"product": {"id": 1, "name": "..."}}
     * }
     */
    public function productDetails(Request $request, $slug)
    {
        try {
            $product = Product::where('slug', $slug)->with('image', 'color', 'size', 'reviews')->first();
            if (!$product) {
                return response()->json(['message' => 'Product not found', 'status' => false], 201);
            }

            $isAuth = auth()->check();
            $userSessionId = session()->getId();
            $cartCount = $isAuth ? EstoreCart::where('user_id', auth()->id())->count() : EstoreCart::where('session_id', $userSessionId)->count();

            $cartItem = $isAuth ? EstoreCart::where('user_id', auth()->id())->where('product_id', $product->id)->first() : EstoreCart::where('session_id', $userSessionId)->where('product_id', $product->id)->first();

            // Related products (limited)
            $nearbyWareHouseId = \App\Models\WareHouse::first()->id;
            $nearest = Helper::getNearestWarehouse(session('location_lat'), session('location_lng'));
            if (!empty($nearest['warehouse']->id)) $nearbyWareHouseId = $nearest['warehouse']->id;
            $wareHouseProducts = Product::whereHas('warehouseProducts', function ($q) use ($nearbyWareHouseId) {
                $q->where('warehouse_id', $nearbyWareHouseId)
                    ->where('quantity', '>', 0);
            })->pluck('id')->toArray();

            $related_products = Product::whereIn('id', $wareHouseProducts)->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where('status', 1)
                ->limit(8)
                ->get();

            return response()->json(['data' => ['product' => $product, 'related' => $related_products, 'cart_count' => $cartCount, 'cart_item' => $cartItem], 'status' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong. ' . $e->getMessage(), 'status' => false], 201);
        }
    }

    /**
     * Products filter (for AJAX/filters)
     */
    /**
     * Filter products endpoint
     *
     * @queryParam page integer optional Page number. Example: 1
     * @queryParam limit integer optional Items per page. Example: 12
     * @queryParam category_id array optional Category ids to filter by. Example: [1,2]
     * @queryParam latestFilter string optional Sorting filter: 'A to Z'|'Z to A'.
     * @queryParam search string optional Search term. Example: book
     */
    public function productsFilter(Request $request)
    {
        try {
            $page = (int)$request->get('page', 1);
            $limit = (int)$request->get('limit', 12);
            $offset = ($page - 1) * $limit;
            $category_id = $request->category_id ?? [];
            $prices = $request->prices ?? [];
            $latest_filter = $request->latestFilter ?? '';
            $search = $request->search ?? '';

            $nearbyWareHouseId = \App\Models\WareHouse::first()->id;
            $originLat = null;
            $originLng = null;
            $isUser = auth()->user();
            if ($isUser) {
                $originLat = $isUser->location_lat;
                $originLng = $isUser->location_lng;
            } else {
                $originLat = session('location_lat');
                $originLng = session('location_lng');
            }
            $nearest = Helper::getNearestWarehouse($originLat, $originLng);
            if (!empty($nearest['warehouse']->id)) {
                $nearbyWareHouseId = $nearest['warehouse']->id;
            }

            $products = Product::where('is_deleted', false)->where('status', 1)->with('image');

            if (!empty($category_id)) {
                $products->whereIn('category_id', is_array($category_id) ? $category_id : [$category_id]);
            }

            if (!empty($latest_filter)) {
                if ($latest_filter == 'A to Z') {
                    $products->orderBy('name', 'asc');
                } elseif ($latest_filter == 'Z to A') {
                    $products->orderBy('name', 'desc');
                } else {
                    $products->orderBy('id', 'desc');
                }
            } else {
                $products->orderBy('id', 'desc');
            }

            if (!empty($search)) {
                $products->where('name', 'LIKE', "%$search%");
            }

            $products_count = (clone $products)->count();

            $items = $products->skip($offset)->take($limit)->get();
            foreach ($items as &$product) {
                $product->is_in_wishlist = (new Product())->isInWishlist($product->id);
            }

            $isAuth = auth()->check();
            $userSessionId = session()->getId();
            $cartCount = $isAuth ? EstoreCart::where('user_id', auth()->id())->count() : EstoreCart::where('session_id', $userSessionId)->count();

            return response()->json(['data' => ['products' => $items, 'products_count' => $products_count, 'cart_count' => $cartCount], 'status' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong. ' . $e->getMessage(), 'status' => false], 201);
        }
    }

    /**
     * Live search (returns basic suggestions)
     * @queryParam search string required The search term. Example: book
     */
    /**
     * Live search suggestions (public)
     *
     * @queryParam search string required The search term. Example: bible
     */
    public function liveSearch(Request $request)
    {
        try {
            $q = $request->get('search', '');
            if (empty($q)) {
                return response()->json(['data' => [], 'status' => true], 200);
            }
            $items = Product::where('is_deleted', false)->where('status', 1)->where('name', 'LIKE', "%{$q}%")
                ->limit(10)->get(['id', 'name', 'slug', 'price'])->map(function ($p) {
                    $p->image = $p->image ? Storage::url($p->image) : null;
                    return $p;
                });
            return response()->json(['data' => $items, 'status' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong. ' . $e->getMessage(), 'status' => false], 201);
        }
    }

    /**
     * Add to cart
     *
     * @bodyParam product_id integer required Product id. Example: 1
     * @bodyParam quantity integer required Quantity. Example: 1
     * @authenticated (optional, supports guest by session)
     */
    /**
     * Add product to cart (public or authenticated) — creates/updates a cart item
     *
     * @bodyParam product_id integer required Product id. Example: 1
     * @bodyParam quantity integer required Quantity. Example: 1
     * @bodyParam size_id integer optional Size id. Example: 2
     * @bodyParam color_id integer optional Color id. Example: 3
     */
    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1'
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors(), 'status' => false], 201);
        }
        try {
            $product = Product::find($request->product_id);
            if (!$product) {
                return response()->json(['message' => 'Product not found', 'status' => false], 201);
            }

            $isAuth = auth()->check();
            $userId = $isAuth ? auth()->id() : null;
            $sessionId = session()->getId();

            // find existing cart item
            $cartQuery = EstoreCart::where('product_id', $product->id);
            if ($userId) $cartQuery->where('user_id', $userId);
            else $cartQuery->where('session_id', $sessionId);
            $cartItem = $cartQuery->first();

            if ($cartItem) {
                $cartItem->quantity += $request->quantity;
                $cartItem->save();
            } else {
                $item = new EstoreCart();
                $item->product_id = $product->id;
                $item->quantity = $request->quantity;
                if ($userId) {
                    $item->user_id = $userId;
                } else {
                    $item->session_id = $sessionId;
                }
                $item->price = $product->price;
                $item->warehouse_id = $request->warehouse_id ?? null;
                $item->size_id = $request->size_id ?? null;
                $item->color_id = $request->color_id ?? null;
                $item->product_variation_id = $request->product_variation_id ?? null;
                $item->save();
            }

            $cartCount = $isAuth ? EstoreCart::where('user_id', $userId)->count() : EstoreCart::where('session_id', $sessionId)->count();

            return response()->json(['message' => 'Added to cart', 'status' => true, 'data' => ['cart_count' => $cartCount]], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong. ' . $e->getMessage(), 'status' => false], 201);
        }
    }

    /**
     * Cart list
     */
    public function cartList(Request $request)
    {
        try {
            $isAuth = auth()->check();
            $userId = $isAuth ? auth()->id() : null;
            $sessionId = session()->getId();

            $cartQuery = EstoreCart::with('product');
            if ($userId) $cartQuery->where('user_id', $userId);
            else $cartQuery->where('session_id', $sessionId);
            $items = $cartQuery->get();

            $total = $items->sum(function ($i) {
                return $i->price * $i->quantity;
            });

            return response()->json(['data' => ['items' => $items, 'total' => $total], 'status' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong. ' . $e->getMessage(), 'status' => false], 201);
        }
    }

    /**
     * Remove from cart
     */
    public function removeFromCart(Request $request)
    {
        try {
            $request->validate(['cart_id' => 'required|integer']);
            $cartItem = EstoreCart::find($request->cart_id);
            if (!$cartItem) return response()->json(['message' => 'Cart item not found', 'status' => false], 201);
            $cartItem->delete();

            $isAuth = auth()->check();
            $userId = $isAuth ? auth()->id() : null;
            $sessionId = session()->getId();
            $cartCount = $isAuth ? EstoreCart::where('user_id', $userId)->count() : EstoreCart::where('session_id', $sessionId)->count();

            return response()->json(['message' => 'Removed from cart', 'status' => true, 'data' => ['cart_count' => $cartCount]], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong. ' . $e->getMessage(), 'status' => false], 201);
        }
    }

    /**
     * Update cart quantity
     */
    public function updateCart(Request $request)
    {
        try {
            $request->validate(['cart_id' => 'required|integer', 'quantity' => 'required|integer|min:1']);
            $cartItem = EstoreCart::find($request->cart_id);
            if (!$cartItem) return response()->json(['message' => 'Cart item not found', 'status' => false], 201);
            $cartItem->quantity = $request->quantity;
            $cartItem->save();

            return response()->json(['message' => 'Cart updated', 'status' => true, 'data' => ['cart_id' => $cartItem->id, 'quantity' => $cartItem->quantity]], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong. ' . $e->getMessage(), 'status' => false], 201);
        }
    }

    /**
     * Cart count
     */
    public function cartCount(Request $request)
    {
        try {
            $isAuth = auth()->check();
            $userId = $isAuth ? auth()->id() : null;
            $sessionId = session()->getId();
            $cartCount = $isAuth ? EstoreCart::where('user_id', $userId)->count() : EstoreCart::where('session_id', $sessionId)->count();
            return response()->json(['data' => ['cart_count' => $cartCount], 'status' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong. ' . $e->getMessage(), 'status' => false], 201);
        }
    }

    /**
     * Clear cart
     */
    public function clearCart(Request $request)
    {
        try {
            $isAuth = auth()->check();
            $userId = $isAuth ? auth()->id() : null;
            $sessionId = session()->getId();
            if ($userId) EstoreCart::where('user_id', $userId)->delete();
            else EstoreCart::where('session_id', $sessionId)->delete();

            return response()->json(['message' => 'Cart cleared', 'status' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong. ' . $e->getMessage(), 'status' => false], 201);
        }
    }

    /**
     * Check product in cart
     */
    public function checkProductInCart(Request $request)
    {
        try {
            $request->validate(['product_id' => 'required|integer']);
            $isAuth = auth()->check();
            $userId = $isAuth ? auth()->id() : null;
            $sessionId = session()->getId();

            $cartQuery = EstoreCart::where('product_id', $request->product_id);
            if ($userId) $cartQuery->where('user_id', $userId);
            else $cartQuery->where('session_id', $sessionId);
            $cartItem = $cartQuery->first();

            return response()->json(['data' => ['exists' => (bool)$cartItem, 'cart_item' => $cartItem], 'status' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong. ' . $e->getMessage(), 'status' => false], 201);
        }
    }
}
