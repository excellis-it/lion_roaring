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
use App\Models\WareHouse;
use Illuminate\Support\Facades\Storage;

/**
 * @group E-Store Product APIs
 *
 */
class EstoreProductController extends Controller
{
    /**
     * Get all products (public)
     * @authenticated
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

            $nearbyWareHouseId = WareHouse::first()->id;
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
     * @authenticated
     * @queryParam slug string required Product slug to fetch details. Example: product-slug
     * @response 200 {
     *  "status": true,
     *  "data": {"product": {"id": 1, "name": "..."}}
     * }
     */
    /**
     * Get product details (including nearest warehouse product and images for selected color/size)
     *
     * @queryParam slug string required Product slug. Example: product-slug
     * @response 200 {
     *  "status": true,
     *  "data": {"product": {"id": 1, "name": "..."}, "warehouse_product": null, "product_images": [] }
     * }
     */
    public function productDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()->first(), 'status' => false], 201);
        }
        try {
            $product = Product::where('slug', $request->slug)->with('image', 'colors', 'sizes', 'reviews')->first();
            if (!$product) {
                return response()->json(['message' => 'Product not found', 'status' => false], 201);
            }

            // Determine nearest warehouse
            $nearbyWareHouseId = WareHouse::first()->id;
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

            // Find an initial warehouse product (respecting product_type and variations)
            $warehouseProduct = null;
            if ($product->product_type != 'simple') {
                $warehouseProduct = WarehouseProduct::where('warehouse_id', $nearbyWareHouseId)
                    ->where('product_id', $product->id)
                    ->first();
            } else {
                $warehouseProduct = WarehouseProduct::where('warehouse_id', $nearbyWareHouseId)
                    ->where('product_id', $product->id)
                    ->first();
            }

            // Build product images (prefer color specific images if any)
            $productImages = [];
            if ($warehouseProduct) {
                $colorImages = \App\Models\ProductColorImage::where('product_id', $product->id)
                    ->where('color_id', $warehouseProduct->color_id)
                    ->get();
                if ($colorImages->isNotEmpty()) {
                    $productImages = $colorImages->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'product_id' => $item->product_id,
                            'color_id' => $item->color_id,
                            'image_path' => $item->image_path,
                            'color_name' => $item->color?->name ?? null,
                        ];
                    })->toArray();
                }
            }

            if (empty($productImages)) {
                $productImages = \App\Models\ProductImage::where('product_id', $product->id)
                    ->get(['id', 'product_id', 'image'])
                    ->map(function ($img) {
                        return ['id' => $img->id, 'product_id' => $img->product_id, 'image' => $img->image, 'image_path' => $img->image];
                    })->toArray();
            }

            $isAuth = auth()->check();
            $userSessionId = session()->getId();
            $cartCount = $isAuth ? EstoreCart::where('user_id', auth()->id())->count() : EstoreCart::where('session_id', $userSessionId)->count();

            $cartItem = $isAuth ? EstoreCart::where('user_id', auth()->id())->where('product_id', $product->id)->first() : EstoreCart::where('session_id', $userSessionId)->where('product_id', $product->id)->first();

            // Related products (limited)
            $wareHouseProducts = Product::whereHas('warehouseProducts', function ($q) use ($nearbyWareHouseId) {
                $q->where('warehouse_id', $nearbyWareHouseId)
                    ->where('quantity', '>', 0);
            })->pluck('id')->toArray();

            $related_products = Product::whereIn('id', $wareHouseProducts)->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where('status', 1)
                ->limit(8)
                ->get();

            // in the product data array in colors set all the colors from Product variationUniqueColors
            $product->variation_colors_images = $product->variation_unique_color_first_images;

            return response()->json(['data' => ['product' => $product, 'warehouse_product' => $warehouseProduct, 'product_images' => $productImages, 'related' => $related_products, 'cart_count' => $cartCount, 'cart_item' => $cartItem], 'status' => true], 200);
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
     * Get warehouse product details by product + optional size/color (used to update images/price/stock when user changes variant)
     * @authenticated
     *
     * @bodyParam product_id integer required Product id. Example: 1
     * @bodyParam size_id integer optional Size id. Example: 2
     * @bodyParam color_id integer optional Color id. Example: 3
     *
     * @response 200 {
     *  "status": true,
     *  "data": {"id": 139, "product_variation_id": 110, "price": "30.00", "quantity": 10},
     *  "productImages": []
     * }
     */
    public function getWarehouseProductDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|integer',
                'size_id' => 'nullable|integer',
                'color_id' => 'nullable|integer',
            ]);
            if ($validator->fails()) {
                return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()->first(), 'status' => false], 201);
            }

            $product = Product::where('id', $request->product_id)->where('is_deleted', 0)->first();
            if (!$product) {
                return response()->json(['message' => 'Product not found', 'status' => false], 201);
            }

            $nearbyWareHouseId = WareHouse::first()->id;
            $originLat = null;
            $originLng = null;
            $isUser = auth()->user();
            // return $isUser;
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

            if ($product->product_type != 'simple') {
                $warehouseProduct = WarehouseProduct::where('warehouse_id', $nearbyWareHouseId)
                    ->where('product_id', $request->product_id)
                    ->when($request->size_id, function ($query) use ($request) {
                        return $query->where('size_id', $request->size_id);
                    })
                    ->when($request->color_id, function ($query) use ($request) {
                        return $query->where('color_id', $request->color_id);
                    })
                    ->first();
            } else {
                $warehouseProduct = WarehouseProduct::where('warehouse_id', $nearbyWareHouseId)
                    ->where('product_id', $request->product_id)
                    ->first();
            }
            $productImages = [];
            $colorMatchedImages = \App\Models\ProductColorImage::where('product_id', $request->product_id)
                ->where('color_id', $request->color_id)
                ->get();
            if ($colorMatchedImages->isNotEmpty()) {
                $productImages = $colorMatchedImages->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'color_id' => $item->color_id,
                        'image_path' => $item->image_path,
                        'color_name' => $item->color?->name ?? null,
                    ];
                })->toArray();
            } else {
                $productImages = \App\Models\ProductImage::where('product_id', $request->product_id)
                    ->get(['id', 'product_id', 'image'])
                    ->map(function ($img) {
                        return ['id' => $img->id, 'product_id' => $img->product_id, 'image' => $img->image, 'image_path' => $img->image];
                    })->toArray();
            }

            if (!$warehouseProduct) {
                return response()->json(['status' => false, 'message' => 'Item Out Of Stock'], 201);
            }

            return response()->json(['status' => true, 'data' => $warehouseProduct, 'productImages' => $productImages], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong. ' . $e->getMessage(), 'status' => false], 201);
        }
    }

    /**
     * Add to cart
     * @authenticated
     *
     * @bodyParam product_id integer required Product id. Example: 1
     * @bodyParam quantity integer required Quantity. Example: 1
     * @bodyParam warehouse_product_id integer optional Warehouse product id (preferred for variations). Example: 139
     * @bodyParam product_variation_id integer optional Product variation id. Example: 110
     * @bodyParam size_id integer optional Size id. Example: 2
     * @bodyParam color_id integer optional Color id. Example: 3
     */
    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'warehouse_product_id' => 'required|integer',
            'product_variation_id' => 'required|integer',
            'size_id' => 'nullable|integer',
            'color_id' => 'nullable|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()->first(), 'status' => false], 201);
        }
        try {
            $product = Product::find($request->product_id);
            if (!$product) {
                return response()->json(['message' => 'Product not found', 'status' => false], 201);
            }

            // determine nearest warehouse
            $nearbyWareHouseId = WareHouse::first()->id;
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

            // Require explicit warehouse product (same as website) and use it directly
            $warehouseProduct = WarehouseProduct::find($request->warehouse_product_id);
            if (!$warehouseProduct) {
                return response()->json(['message' => 'Item Out Of Stock', 'status' => false], 201);
            }

            // Check stock availability
            if ($warehouseProduct->quantity < $request->quantity) {
                return response()->json(['message' => 'Only ' . $warehouseProduct->quantity . ' items available', 'status' => false], 201);
            }

            $isAuth = auth()->check();
            $sessionId = session()->getId();

            // find existing cart item for this user/session using product_variation_id (same as website)
            if ($isAuth) {
                $existingCart = EstoreCart::where('user_id', auth()->id())
                    ->where('product_id', $product->id)
                    ->where('product_variation_id', $request->product_variation_id)
                    ->first();
            } else {
                $existingCart = EstoreCart::where('session_id', $sessionId)
                    ->where('product_id', $product->id)
                    ->where('product_variation_id', $request->product_variation_id)
                    ->first();
            }

            if ($existingCart) {
                $existingCart->quantity += $request->quantity;
                if ($product->is_free) {
                    $existingCart->old_price = $existingCart->price;
                    $existingCart->price = 0;
                }
                $existingCart->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Cart updated successfully',
                    'action' => 'updated',
                    'cart_item_id' => $existingCart->id,
                    'quantity' => $existingCart->quantity
                ]);
            }

            // set defaults for size/color if not provided (same as website)
            $sizeId = $request->size_id ?? null;
            if (!$request->size_id && ($product->sizes->count() > 0)) {
                $sizeId = $product->sizes->first()->size->id;
            }
            $colorId = $request->color_id ?? null;
            if (!$request->color_id && ($product->colors->count() > 0)) {
                $colorId = $product->colors->first()->color->id;
            }

            $cart = new EstoreCart();
            $cart->user_id = auth()->id();
            $cart->product_id = $product->id;
            $cart->product_variation_id = $warehouseProduct->product_variation_id;
            $cart->warehouse_product_id = $warehouseProduct->id;
            $cart->warehouse_id = $warehouseProduct->warehouse_id;
            $cart->size_id = $sizeId;
            $cart->color_id = $colorId;
            $cart->quantity = $request->quantity;
            $cart->old_price = $warehouseProduct->price;
            $cart->price = $product->is_free ? 0 : $warehouseProduct->price;
            $cart->session_id = $sessionId;
            $cart->save();

            return response()->json([
                'status' => true,
                'message' => 'Product added to cart successfully',
                'action' => 'added',
                'cart_item_id' => $cart->id,
                'quantity' => $cart->quantity
            ]);

            $cartCount = $isAuth ? EstoreCart::where('user_id', auth()->id())->count() : EstoreCart::where('session_id', $sessionId)->count();

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
            $cartQuery = EstoreCart::with('product');
            if (auth()->check()) {
                $cartQuery->where('user_id', auth()->id());
            } else {
                $cartQuery->where('session_id', session()->getId());
            }
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
            $validator = Validator::make($request->all(), [
                'cart_id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()->first(), 'status' => false], 201);
            }
            $cartItem = EstoreCart::find($request->cart_id);
            if (!$cartItem) return response()->json(['message' => 'Cart item not found', 'status' => false], 201);
            $cartItem->delete();

            $cartCount = auth()->check() ? EstoreCart::where('user_id', auth()->id())->count() : EstoreCart::where('session_id', session()->getId())->count();

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
            $validator = Validator::make($request->all(), [
                'cart_id' => 'required|integer',
                'quantity' => 'required|integer|min:1',
            ]);
            if ($validator->fails()) {
                return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()->first(), 'status' => false], 201);
            }
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
            if (auth()->check()) {
                $cartCount = EstoreCart::where('user_id', auth()->id())->count();
            } else {
                $cartCount = EstoreCart::where('session_id', session()->getId())->count();
            }
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
            if (auth()->check()) {
                EstoreCart::where('user_id', auth()->id())->delete();
            } else {
                EstoreCart::where('session_id', session()->getId())->delete();
            }
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
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()->first(), 'status' => false], 201);
            }

            $cartQuery = EstoreCart::where('product_id', $request->product_id);
            if (auth()->check()) {
                $cartQuery->where('user_id', auth()->id());
            } else {
                $cartQuery->where('session_id', session()->getId());
            }
            $cartItem = $cartQuery->first();

            return response()->json(['data' => ['exists' => (bool)$cartItem, 'cart_item' => $cartItem], 'status' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong. ' . $e->getMessage(), 'status' => false], 201);
        }
    }
}
