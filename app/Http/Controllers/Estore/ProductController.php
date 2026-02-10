<?php

namespace App\Http\Controllers\Estore;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Mail\OrderNotificationMail;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Review;
use App\Models\EstoreCart;
use App\Models\EstoreOrder;
use App\Models\EstoreOrderItem;
use App\Models\EstorePayment;
use App\Models\EstoreSetting;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Facades\DB;
use App\Models\EcomWishList;
use App\Models\Size;
use App\Models\WarehouseProduct;
use App\Models\WareHouse;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Stripe\Charge;
use Stripe\Exception\CardException;
use Stripe\Exception\RateLimitException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use App\Models\EstoreRefund;
use App\Models\ProductVariation;
use App\Models\WarehouseProductVariation;
use Illuminate\Support\Facades\Storage;
use App\Services\PromoCodeService;
use App\Models\EstorePromoCode;
use App\Models\Notification;
use App\Models\OrderEmailTemplate;
use App\Models\OrderStatus;
use App\Models\ProductColorImage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\OutOfStockNotificationMail;
use Stripe\Climate\Order;
use App\Models\ProductImage;
use App\Models\MarketMaterial;
use App\Models\MarketMaterialRate;

class ProductController extends Controller
{
    public function productDetails($slug)
    {
        $isAuth = auth()->check();
        $userSessionId = session()->getId();
        $cartCount = $isAuth ? EstoreCart::where('user_id', auth()->id())->count() : EstoreCart::where('session_id', $userSessionId)->count();

        $nearbyWareHouseId = WareHouse::where('is_active', 1)->first()->id;
        $originLat = null;
        $originLng = null;
        $isUser = auth()->user();
        if ($isUser) { // Assuming user location is stored in user model
            $originLat = $isUser->location_lat;
            $originLng = $isUser->location_lng;
        } else {
            $originLat = session('location_lat');
            $originLng = session('location_lng');
        }
        // reuse helper to get nearest warehouse
        $nearest = Helper::getNearestWarehouse($originLat, $originLng);
        // return $nearest;
        // return $nearest;
        if (!empty($nearest['warehouse']->id)) {
            $nearbyWareHouseId = $nearest['warehouse']->id;
        }
        // return $nearbyWareHouseId;

        $wareHouseProducts = Product::whereHas('warehouseProducts', function ($q) use ($nearbyWareHouseId) {
            $q->where('warehouse_id', $nearbyWareHouseId)
                ->where('quantity', '>', 0);
        })->pluck('id')->toArray();

        $getProduct = Product::where('slug', $slug)->first();

        $wareHouseHaveProductVariables = WarehouseProduct::where('product_id', $getProduct->id)
            ->where('warehouse_id', $nearbyWareHouseId)
            ->first();
        // $wareHouseHaveProductVariables = WarehouseProductVariation::where('product_id', $getProduct->id)
        //     ->where('warehouse_id', $nearbyWareHouseId)
        //     ->where('warehouse_quantity', '>', 0)
        //     ->with('productVariation.colorDetail', 'productVariation.sizeDetail', 'productVariation.images')
        //     ->get();

        // return $wareHouseHaveProductVariables;

        // if (! $wareHouseHaveProductVariables) {
        //     // Handle the case where the product is not found in the warehouse
        //     return view('ecom.product-not-available', compact('cartCount'));
        // }

        // select prodcut is first product in warehouse have with the product id
        if ($wareHouseHaveProductVariables) {
            $product = Product::where('id', $wareHouseHaveProductVariables->product_id)->where('slug', $slug)->first();
        } else {
            $product = $getProduct;
        }

        $related_products = Product::whereIn('id', $wareHouseProducts)->where('category_id', $product->category_id)
            ->where(function ($query) use ($product) {
                $query->where('id', '!=', $product->id)
                    ->where('status', 1)
                    ->where('quantity', '>', 0);
            })
            ->orderBy('id', 'DESC')
            ->limit(8)
            ->get();

        $own_review = $product->reviews()->where('user_id', auth()->id() ?? 0)->where('status', 1)->first();
        $reviews = $product->reviews()
            ->where('status', Review::STATUS_APPROVED)
            ->orderBy('id', 'DESC')
            ->get();

        // Check if product is already in cart
        $cartItem = $isAuth ? EstoreCart::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first() : EstoreCart::where('session_id', $userSessionId)
            ->where('product_id', $product->id)
            ->first();

        $marketMaterials = collect();
        $marketMaterialRates = collect();
        if ($product && ($product->is_market_priced ?? false)) {
            $marketMaterials = MarketMaterial::where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            $marketMaterialRates = MarketMaterialRate::whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('market_material_rates')
                    ->groupBy('market_material_id');
            })
                ->get()
                ->keyBy('market_material_id');
        }


        return view('ecom.product-details')->with(compact('product', 'related_products', 'reviews', 'own_review', 'cartCount', 'cartItem', 'wareHouseHaveProductVariables', 'marketMaterials', 'marketMaterialRates'));
    }

    public function products(Request $request, $category_id = null)
    {
        $category_id = $category_id ?? ''; // Default value is ' '
        $childCategories = [];
        $childCategoriesList = [];
        $category_name = null;

        $nearbyWareHouseId = WareHouse::where('is_active', 1)->first()->id;
        $originLat = null;
        $originLng = null;
        $isUser = auth()->user();
        if ($isUser) { // Assuming user location is stored in user model
            $originLat = $isUser->location_lat;
            $originLng = $isUser->location_lng;
        } else {
            $originLat = session('location_lat');
            $originLng = session('location_lng');
        }
        // reuse helper to get nearest warehouse
        $nearest = Helper::getNearestWarehouse($originLat, $originLng);
        if (!empty($nearest['warehouse']->id)) {
            $nearbyWareHouseId = $nearest['warehouse']->id;
        }
        // return $nearest;

        $wareHouseProducts = Product::whereHas('warehouseProducts', function ($q) use ($nearbyWareHouseId) {
            $q->where('warehouse_id', $nearbyWareHouseId)
                ->where('quantity', '>', 0);
        })->pluck('id')->toArray();


        // if ($wareHouseProducts ) {
        //    $products = Product::whereIn('id', $wareHouseProducts)->where('status', 1);
        // } else {
        $products = Product::where('is_deleted', false)->where('status', 1);
        //  }


        // $products = Product::whereIn('id', $wareHouseProducts)->where('status', 1);
        if ($category_id) {
            // $products = $products->where('category_id', $category_id);
            $category = Category::find($category_id);
            // products also with children
            if ($category) {
                $childCategories = Category::where('parent_id', $category->id)->pluck('id')->toArray();
                $childCategoriesList = Category::where('parent_id', $category->id)->get();
                $products = $products->whereIn('category_id', array_merge([$category->id], $childCategories));
                $category_name = $category->name;
            }
        } else {
            $category = null;
        }
        $products = $products->orderBy('id', 'DESC')->limit(12)->get();
        // dd($products);

        $products_count  = $products->count();
        // Build hierarchical categories (up to 3 levels for UI)
        $categories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->with(['children' => function ($q) {
                $q->with(['children']); // nested second level
            }])
            ->orderBy('name')
            ->get();
        //  return $categories;
        $isAuth = auth()->check();
        $userSessionId = session()->getId();
        $cartCount = $isAuth ? EstoreCart::where('user_id', auth()->id())->count() : EstoreCart::where('session_id', $userSessionId)->count();

        return view('ecom.products')->with(compact('products', 'category_name', 'categories', 'category_id', 'products_count', 'category', 'cartCount', 'childCategories', 'childCategoriesList'));
    }

    public static function productsFilter(Request $request)
    {
        if ($request->ajax()) {
            $page = $request->get('page', 1);
            $limit = 12; // Number of products per page
            $offset = ($page - 1) * $limit;
            $category_id = $request->category_id ?? [];
            $prices = $request->prices ?? [];
            $latest_filter = $request->latestFilter ?? '';
            $search = $request->search ?? '';



            $nearbyWareHouseId = WareHouse::where('is_active', 1)->first()->id;
            $originLat = null;
            $originLng = null;
            $isUser = auth()->user();
            if ($isUser) { // Assuming user location is stored in user model
                $originLat = $isUser->location_lat;
                $originLng = $isUser->location_lng;
            } else {
                $originLat = session('location_lat');
                $originLng = session('location_lng');
            }
            // reuse helper to get nearest warehouse
            $nearest = Helper::getNearestWarehouse($originLat, $originLng);
            if (!empty($nearest['warehouse']->id)) {
                $nearbyWareHouseId = $nearest['warehouse']->id;
            }
            // return $getNearbywareHouse;

            $wareHouseProducts = Product::whereHas('warehouseProducts', function ($q) use ($nearbyWareHouseId) {
                $q->where('warehouse_id', $nearbyWareHouseId)
                    ->where('quantity', '>', 0);
            })->pluck('id')->toArray();

            //  if ($wareHouseProducts) {
            //     $products = Product::whereIn('id', $wareHouseProducts)->where('status', 1)->with('image');
            //  } else {
            $products = Product::where('is_deleted', false)->where('status', 1)->with('image');
            //  }

            if (!empty($category_id)) {
                // Expand selected categories to include all descendants
                $allCategoryIds = [];
                $queue = Category::whereIn('id', $category_id)->get();
                foreach ($queue as $cat) {
                    $allCategoryIds[] = $cat->id;
                }
                // BFS through children to collect all descendants
                $index = 0;
                while ($index < count($queue)) {
                    $current = $queue[$index];
                    $children = $current->children()->pluck('id')->toArray();
                    foreach ($children as $childId) {
                        if (!in_array($childId, $allCategoryIds)) {
                            $allCategoryIds[] = $childId;
                            $queue->push(Category::find($childId));
                        }
                    }
                    $index++;
                }
                $products->whereIn('category_id', $allCategoryIds);
            }

            // if (!empty($prices)) {
            //     $products->where(function ($query) use ($prices) {
            //         foreach ($prices as $price) {
            //             if ($price == 'Below 500') {
            //                 $query->orWhere('price', '<', 500);
            //             } elseif ($price == 'Above 5000') {
            //                 $query->orWhere('price', '>', 5000);
            //             } else {
            //                 $priceRange = explode('-', $price);
            //                 $query->orWhereBetween('price', [$priceRange[0], $priceRange[1]]);
            //             }
            //         }
            //     });
            // }

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

            // Get the total count of filtered products
            // $products_count = $products->count();
            $productsCountQuery = clone $products;
            $products_count = $productsCountQuery->count();

            // Get the paginated products
            $products = $products->skip($offset)
                ->take($limit)
                ->get()->toArray();

            //  return $products;

            // is the product in wishlist
            foreach ($products as &$product) {
                $product['is_in_wishlist'] = (new Product())->isInWishlist($product['id']);
            }

            $category = !empty($category_id) ? Category::whereIn('id', $category_id)->get()->toArray() : null;
            $isAuth = auth()->check();
            $userSessionId = session()->getId();
            $cartCount = $isAuth ? EstoreCart::where('user_id', auth()->id())->count() : EstoreCart::where('session_id', $userSessionId)->count();

            $view = view('ecom.partials.product-item', compact('products', 'products_count', 'cartCount'))->render();
            $view2 = view('ecom.partials.count-product', compact('products', 'products_count', 'category', 'category_id', 'cartCount'))->render();

            return response()->json([
                'status' => true,
                'view' => $view,
                'view2' => $view2,
                'products_count' => $products_count,
                'products' => $products,
            ]);
        }
    }

    public function productAddReview(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'rate' => 'required|integer',
            'review' => 'required|string',
        ]);

        if (!auth()->check()) {
            return response()->json(['status' => false, 'message' => 'Please login to add a review']);
        }

        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found']);
        }

        if ($product->reviews()->where('user_id', auth()->id())->count() > 0) {
            return response()->json(['status' => false, 'message' => 'You have already submitted a review for this product']);
        }

        $review = new Review();
        $review->product_id = $request->product_id;
        $review->user_id = auth()->id();
        $review->rating = $request->rate;
        $review->review = $request->review;
        $review->status = Review::STATUS_PENDING;
        $review->save();

        // Render the review view
        $own_review = $product->reviews()->where('user_id', auth()->id() ?? 0)->where('status', 1)->first();
        $reviews = $product->reviews()
            ->where('status', Review::STATUS_APPROVED)
            ->orderBy('id', 'DESC')
            ->get();
        $cartCount = EstoreCart::where('user_id', auth()->id())->count();
        $view = view('ecom.partials.product-review', compact('reviews', 'own_review', 'cartCount'))->render();

        return response()->json(['status' => true, 'message' => 'Review submitted successfully', 'view' => $view]);
    }

    // addToCart
    public function addToCart(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'product_id' => 'required|integer',
                'quantity' => 'required|integer|min:1',
                'warehouse_product_id' => 'required|integer',
                'product_variation_id' => 'required|integer',
            ]);

            $isAuth = auth()->check();

            $userSessionId = session()->getId();

            // Check max order quantity setting (total cart quantity)
            $estoreSettings = EstoreSetting::first();
            $maxOrderQty = $estoreSettings->max_order_quantity ?? null;

            if ($maxOrderQty && $maxOrderQty > 0) {
                $baseCartQuery = $isAuth
                    ? EstoreCart::where('user_id', auth()->id())
                    : EstoreCart::where('session_id', $userSessionId);

                $totalQty = (int) $baseCartQuery->sum('quantity');

                if ($totalQty + $request->quantity > $maxOrderQty) {
                    return response()->json([
                        'status' => false,
                        'message' => "Maximum order quantity is {$maxOrderQty} for your cart"
                    ]);
                }
            }

            $product = Product::find($request->product_id);
            if (!$product) {
                return response()->json(['status' => false, 'message' => 'Product not found']);
            }

            $warehouseProductId = $request->warehouse_product_id;
            $warehouseProduct = WarehouseProduct::find($warehouseProductId);

            if (!$warehouseProduct || $warehouseProduct->quantity <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product is currently out of stock in this warehouse'
                ]);
            }

            $wareHouse = Warehouse::find($warehouseProduct->warehouse_id);

            $wareHouseProductPrice = $warehouseProduct->price ?? 0;
            $isFree = $product->is_free ?? false;

            // Check if product already exists in cart
            if ($isAuth) {
                $existingCart = EstoreCart::where('user_id', auth()->id())
                    ->where('product_id', $product->id)
                    // ->where('warehouse_product_id', $warehouseProductId)
                    ->where('product_variation_id', $request->product_variation_id)
                    ->first();
            } else {
                $existingCart = EstoreCart::where('session_id', $userSessionId)
                    ->where('product_id', $product->id)
                    ->where('product_variation_id', $request->product_variation_id)
                    ->first();
            }

            if ($existingCart) {
                // Update quantity instead of creating new entry
                $newTotalQty = $existingCart->quantity + $request->quantity;

                // Check against available stock
                if ($newTotalQty > $warehouseProduct->quantity) {
                    return response()->json([
                        'status' => false,
                        'message' => "Only {$warehouseProduct->quantity} items available in stock in this warehouse"
                    ]);
                }

                // Check against max order quantity (total cart quantity)
                if ($maxOrderQty && $maxOrderQty > 0) {
                    $baseCartQuery = $isAuth
                        ? EstoreCart::where('user_id', auth()->id())
                        : EstoreCart::where('session_id', $userSessionId);

                    $totalQty = (int) $baseCartQuery->sum('quantity');
                    $newCartTotal = $totalQty - (int) $existingCart->quantity + (int) $newTotalQty;

                    if ($newCartTotal > $maxOrderQty) {
                        return response()->json([
                            'status' => false,
                            'message' => "Maximum order quantity is {$maxOrderQty} for your cart"
                        ]);
                    }
                }

                $existingCart->quantity = $newTotalQty;
                if ($isFree) {
                    $existingCart->old_price = $existingCart->price; // keep previous stored
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

            // return $product->sizes->count();
            // check if product have size and color and user not selected those then auto select first color and size either set null
            $sizeId = $request->size_id ?? null;
            if (!$request->size_id && ($product->sizes->count() > 0)) {
                $sizeId = $product->sizes->first()->size->id;
            }
            $colorId = $request->color_id ?? null;
            if (!$request->color_id && ($product->colors->count() > 0)) {
                $colorId = $product->colors->first()->color->id;
            }

            // return $sizeId;

            // Check against available stock for new item
            if ($request->quantity > $warehouseProduct->quantity) {
                return response()->json([
                    'status' => false,
                    'message' => "Only {$warehouseProduct->quantity} items available in stock in this warehouse"
                ]);
            }

            $cart = new EstoreCart();
            $cart->user_id = auth()->id();
            $cart->product_id = $product->id;
            $cart->product_variation_id = $warehouseProduct->product_variation_id;
            $cart->warehouse_product_id = $warehouseProduct->id;
            $cart->warehouse_id = $wareHouse->id;
            $cart->size_id = $sizeId;
            $cart->color_id = $colorId;
            $cart->quantity = $request->quantity;
            $cart->old_price = $wareHouseProductPrice;
            $cart->price = $isFree ? 0 : $wareHouseProductPrice;
            $cart->session_id = $userSessionId;
            $cart->save();

            return response()->json([
                'status' => true,
                'message' => 'Product added to cart successfully',
                'action' => 'added',
                'cart_item_id' => $cart->id,
                'quantity' => $cart->quantity
            ]);
        }
    }

    // Check if product is in cart
    public function checkProductInCart(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'product_id' => 'required|integer',
            ]);

            $isAuth = auth()->check();
            $userSessionId = session()->getId();

            $cartItem = $isAuth ? EstoreCart::where('user_id', auth()->id()) : EstoreCart::where('session_id', $userSessionId);
            $cartItem = $cartItem->where('product_id', $request->product_id)->first();

            // return response()->json([
            //     'status' => true,
            //     'inCart' => $cartItem ? true : false,
            //     'cartItem' => $cartItem ? [
            //         'id' => $cartItem->id,
            //         'quantity' => $cartItem->quantity
            //     ] : null
            // ]);
            return response()->json([
                'status' => true,
                'inCart' => false,
                'cartItem' => null
            ]);
        }
    }

    // removeFromCart
    public function removeFromCart(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'id' => 'required|integer',
            ]);

            $isAuth = auth()->check();
            $userSessionId = session()->getId();

            $cart = EstoreCart::find($request->id);
            if (!$cart || ($isAuth && $cart->user_id != auth()->id()) || (!$isAuth && $cart->session_id != $userSessionId)) {
                return response()->json(['status' => false, 'message' => 'Cart item not found']);
            }

            $cart->delete();

            return response()->json(['status' => true, 'message' => 'Product removed from cart successfully']);
        }
    }
    // updateCart
    public function updateCart(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'id' => 'required|integer',
                'quantity' => 'required|integer|min:0',
            ]);

            $isAuth = auth()->check();
            $userSessionId = session()->getId();

            $cart = EstoreCart::find($request->id);
            if (!$cart || ($isAuth && $cart->user_id != auth()->id()) || (!$isAuth && $cart->session_id != $userSessionId)) {
                return response()->json(['status' => false, 'message' => 'Cart item not found']);
            }

            // if 0 quantity, remove the item
            if ($request->quantity <= 0) {
                $cart->delete();
                return response()->json(['status' => true, 'message' => 'Cart item removed successfully']);
            }

            // Check max order quantity setting (total cart quantity)
            $estoreSettings = EstoreSetting::first();
            $maxOrderQty = $estoreSettings->max_order_quantity ?? null;

            if ($maxOrderQty && $maxOrderQty > 0) {
                $baseCartQuery = $isAuth
                    ? EstoreCart::where('user_id', auth()->id())
                    : EstoreCart::where('session_id', $userSessionId);

                $totalQty = (int) $baseCartQuery->sum('quantity');
                $newCartTotal = $totalQty - (int) $cart->quantity + (int) $request->quantity;

                if ($newCartTotal > $maxOrderQty) {
                    return response()->json([
                        'status' => false,
                        'message' => "Maximum order quantity is {$maxOrderQty} for your cart"
                    ]);
                }
            }

            // Check against available stock
            $warehouseProduct = WarehouseProduct::find($cart->warehouse_product_id);
            if ($warehouseProduct && $request->quantity > $warehouseProduct->quantity) {
                return response()->json([
                    'status' => false,
                    'message' => "Only {$warehouseProduct->quantity} items available in stock in this warehouse"
                ]);
            }

            $cart->quantity = $request->quantity;
            $cart->save();

            return response()->json(['status' => true, 'message' => 'Cart updated successfully']);
        }
    }

    // clearCart
    public function clearCart(Request $request)
    {
        if ($request->ajax()) {
            $isAuth = auth()->check();
            $userSessionId = session()->getId();

            $carts = $isAuth ? EstoreCart::where('user_id', auth()->id())->get() : EstoreCart::where('session_id', $userSessionId)->get();

            foreach ($carts as $cart) {
                $cart->delete();
            }
            return response()->json(['status' => true, 'message' => 'Cart cleared successfully']);
        }
    }

    // cartCount
    public function cartCount(Request $request)
    {
        if ($request->ajax()) {
            $isAuth = auth()->check();
            $userSessionId = session()->getId();
            $cartCount = $isAuth
                ? EstoreCart::where('user_id', auth()->id())->sum('quantity')
                : EstoreCart::where('session_id', $userSessionId)->sum('quantity');
            return response()->json(['status' => true, 'cartCount' => $cartCount]);
        }
    }

    // cartList
    public function cartList(Request $request)
    {
        if ($request->ajax()) {
            $isAuth = auth()->check();
            $userSessionId = session()->getId();
            $carts = $isAuth ? EstoreCart::where('user_id', auth()->id())->with('product')->get() : EstoreCart::where('session_id', $userSessionId)->with('product')->get();

            $cartItems = [];
            $total = 0;

            foreach ($carts as $cart) {
                $subtotal = $cart->price * $cart->quantity;
                $total += $subtotal;

                $cartItems[] = [
                    'id' => $cart->id,
                    'product_id' => $cart->product_id,
                    'product_name' => $cart->product->name ?? 'Unknown Product',
                    'product_image' => $cart->product->main_image ?? null,
                    'price' => $cart->price,
                    'quantity' => $cart->quantity,
                    'subtotal' => $subtotal
                ];
            }

            return response()->json([
                'status' => true,
                'cartItems' => $cartItems,
                'total' => $total,
                'cartCount' => $carts->count()
            ]);
        }
    }

    // cart page
    public function cart()
    {
        $isAuth = auth()->check();
        $userSessionId = session()->getId();

        $carts = $isAuth
            ? EstoreCart::where('user_id', auth()->id())->with(['product.otherCharges', 'warehouseProduct'])->get()
            : EstoreCart::where('session_id', $userSessionId)->with(['product.otherCharges', 'warehouseProduct'])->get();

        $cartCount = $isAuth ? EstoreCart::where('user_id', auth()->id())->count() : EstoreCart::where('session_id', $userSessionId)->count();

        $total = 0;
        $hasChanges = false;

        foreach ($carts as $cart) {
            $warehouseProduct = $cart->warehouseProduct;
            $currentWarehousePrice = $warehouseProduct?->price ?? 0;
            $availableQty = $warehouseProduct?->quantity ?? 0;

            $meta = [
                'price_changed' => false,
                'out_of_stock' => false,
                'original_price' => $cart->price,
                'current_price' => $currentWarehousePrice,
            ];

            if (($cart->price ?? 0) != $currentWarehousePrice && !($cart->product?->is_free)) {
                $cart->old_price = $cart->price;
                $cart->price = $currentWarehousePrice;
                $cart->save();
                $meta['price_changed'] = true;
                $hasChanges = true;
            }

            if ($availableQty <= 0) {
                $meta['out_of_stock'] = true;
                $hasChanges = true;
            } elseif ($cart->quantity > $availableQty) {
                $cart->quantity = $availableQty;
                $cart->save();
                $hasChanges = true;
            }

            $otherCharges = $cart->product?->otherCharges?->sum('charge_amount') ?? 0;

            if (!$meta['out_of_stock']) {
                $cart->subtotal = ($cart->price ?? 0) * $cart->quantity + ($otherCharges ?? 0);
                $total += $cart->subtotal;
            } else {
                $cart->subtotal = 0;
            }

            $cart->setAttribute('meta', $meta);
        }

        // Get promo code discount from session
        $appliedPromoCode = session('applied_promo_code');
        $promoDiscount = session('promo_discount', 0);

        // Recalculate promo discount if code is applied
        if ($appliedPromoCode && $total > 0) {
            $cartItems = [];
            foreach ($carts as $cart) {
                if (!($cart->meta['out_of_stock'] ?? false)) {
                    $cartItems[] = [
                        'product_id' => $cart->product_id,
                        'subtotal' => $cart->subtotal,
                    ];
                }
            }

            $validation = PromoCodeService::validatePromoCode($appliedPromoCode, $isAuth ? auth()->id() : null, $cartItems);
            if ($validation['valid']) {
                $promoDiscount = PromoCodeService::calculateDiscount($validation['promo_code'], $total, $cartItems);
                session(['promo_discount' => $promoDiscount]);
            } else {
                session()->forget(['applied_promo_code', 'promo_discount']);
                $appliedPromoCode = null;
                $promoDiscount = 0;
            }
        }

        return view('ecom.cart')->with(compact('carts', 'total', 'cartCount', 'hasChanges', 'appliedPromoCode', 'promoDiscount'));
    }

    // checkout page
    public function checkout()
    {
        if (!auth()->check()) {
            return redirect()->route('home')->with('error', 'Please login to continue');
        }

        $carts = EstoreCart::where('user_id', auth()->id())
            ->with(['product.otherCharges', 'warehouseProduct'])
            ->get();
        $cartCount = EstoreCart::where('user_id', auth()->id())->count();

        if ($carts->isEmpty()) {
            return redirect()->route('e-store.cart')->with('error', 'Your cart is empty');
        }



        // Get estore settings
        $estoreSettings = EstoreSetting::first();

        $subtotal = 0;
        $cartItems = [];
        $hasChanges = false;

        foreach ($carts as $cart) {

            $warehouseProduct = $cart->warehouseProduct;
            $currentWarehousePrice = $warehouseProduct?->price ?? 0;
            $availableQty = $warehouseProduct?->quantity ?? 0;

            $priceChanged = false;
            $outOfStock = false;

            if (($cart->price ?? 0) != $currentWarehousePrice && !($cart->product?->is_free)) {
                $cart->old_price = $cart->price;
                $cart->price = $currentWarehousePrice;
                $cart->save();
                $priceChanged = true;
                $hasChanges = true;
            }

            if ($availableQty <= 0) {
                $outOfStock = true;
                $hasChanges = true;
            } elseif ($cart->quantity > $availableQty) {
                $cart->quantity = $availableQty;
                $cart->save();
                $hasChanges = true;
            }

            $otherCharges = $cart->product?->otherCharges?->sum('charge_amount') ?? 0;
            $itemSubtotal = $outOfStock ? 0 : ((($cart->price ?? 0) * $cart->quantity) + $otherCharges);
            $subtotal += $itemSubtotal;

            $cartItems[] = [
                'id' => $cart->id,
                'product_id' => $cart->product_id,
                'product_name' => $cart->product->name ?? '',
                'product_image' => $cart->product->getProductFirstImage($cart->color_id) ?? '',
                'price' => $cart->price ?? 0,
                'quantity' => $cart->quantity,
                'other_charges' => $otherCharges,
                'subtotal' => $itemSubtotal,
                'price_changed' => $priceChanged,
                'out_of_stock' => $outOfStock,
                'original_price' => $cart->old_price ?? null,
                'current_price' => $cart->price ?? 0,
            ];
        }

        // Get promo code discount
        $appliedPromoCode = session('applied_promo_code');
        $promoDiscount = 0;

        if ($appliedPromoCode && $subtotal > 0) {
            $validation = PromoCodeService::validatePromoCode($appliedPromoCode, auth()->id(), $cartItems);
            if ($validation['valid']) {
                $promoDiscount = PromoCodeService::calculateDiscount($validation['promo_code'], $subtotal, $cartItems);
            } else {
                session()->forget(['applied_promo_code', 'promo_discount']);
                $appliedPromoCode = null;
            }
        }

        $shippingCost = 0;
        $deliveryCost = 0;
        $taxAmount = 0;

        if ($estoreSettings) {
            // Calculate total ordered item count
            $totalQuantity = 0;
            foreach ($carts as $c) {
                $totalQuantity += (int)($c->quantity ?? 0);
            }

            // Use shipping rules if configured
            if (is_array($estoreSettings->shipping_rules) && count($estoreSettings->shipping_rules) > 0) {
                $shippingForQty = $estoreSettings->getShippingForQuantity($totalQuantity);
                $shippingCost = $shippingForQty['shipping_cost'];
                $deliveryCost = $shippingForQty['delivery_cost'];
            } else {
                $shippingCost = $estoreSettings->shipping_cost ?? 0;
                $deliveryCost = $estoreSettings->delivery_cost ?? 0;
            }

            $taxPercentage = $estoreSettings->tax_percentage ?? 0;

            if ($taxPercentage > 0) {
                $taxAmount = (($subtotal - $promoDiscount) * $taxPercentage) / 100;
            }
        }

        $total = $subtotal - $promoDiscount + $shippingCost + $deliveryCost + $taxAmount;

        // Get nearest warehouse based on user location
        $nearestWarehouse = null;
        $warehouseDistance = null;

        // Try to get location from user profile or session
        $userLat = null;
        $userLng = null;

        if (auth()->check() && auth()->user()->location_lat && auth()->user()->location_lng) {
            $userLat = auth()->user()->location_lat;
            $userLng = auth()->user()->location_lng;
        } elseif (session('location_lat') && session('location_lng')) {
            $userLat = session('location_lat');
            $userLng = session('location_lng');
        }

        // Get nearest warehouse if we have user coordinates
        if ($userLat && $userLng) {
            $warehouseData = \App\Helpers\Helper::getNearestWarehouse($userLat, $userLng);
            if ($warehouseData) {
                $nearestWarehouse = $warehouseData['warehouse'];
                $warehouseDistance = $warehouseData['distance_km'];
            }
        }

        // If no location available, get the first active warehouse as fallback
        if (!$nearestWarehouse) {
            $nearestWarehouse = WareHouse::where('is_active', 1)
                ->whereHas('warehouseProducts')
                ->with('country')
                ->first();
        }

        return view('ecom.checkout')->with(compact(
            'cartItems',
            'subtotal',
            'shippingCost',
            'deliveryCost',
            'taxAmount',
            'total',
            'cartCount',
            'estoreSettings',
            'hasChanges',
            'appliedPromoCode',
            'promoDiscount',
            'nearestWarehouse',
            'warehouseDistance'
        ));
    }

    // Process checkout
    public function processCheckout(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['status' => false, 'message' => 'Please login to continue']);
        }

        $request->validate([
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
        ]);

        $is_pickup = $request->order_method ?? 0;
        $carts = EstoreCart::where('user_id', auth()->id())->with(['product.otherCharges', 'warehouseProduct'])->get();

        if ($carts->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Your cart is empty']);
        }

        $estoreSettings = EstoreSetting::first();
        $maxOrderQty = $estoreSettings->max_order_quantity ?? null;

        $hasBlockingStockIssue = false;
        $recalculatedSubtotal = 0;
        $cartItems = [];

        foreach ($carts as $cart) {
            $warehouseProduct = $cart->warehouseProduct;
            $currentWarehousePrice = $warehouseProduct?->price ?? 0;
            $availableQty = $warehouseProduct?->quantity ?? 0;

            // Check max order quantity
            if ($maxOrderQty && $maxOrderQty > 0 && $cart->quantity > $maxOrderQty) {
                return response()->json([
                    'status' => false,
                    'message' => "Product '{$cart->product->name}' exceeds maximum order quantity of {$maxOrderQty}"
                ]);
            }

            if (($cart->price ?? 0) != $currentWarehousePrice && !($cart->product?->is_free)) {
                $cart->old_price = $cart->price;
                $cart->price = $currentWarehousePrice;
                $cart->save();
            }

            if ($availableQty <= 0) {
                $hasBlockingStockIssue = true;
                $cart->rejected_reason = 'out_of_stock';
                continue;
            } elseif ($cart->quantity > $availableQty) {
                $cart->quantity = $availableQty;
                $cart->save();
            }

            $cart->other_charges = $cart->product?->otherCharges?->sum('charge_amount') ?? 0;
            $unitPrice = $cart->price ?? ($currentWarehousePrice);
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
                'message' => 'Some items are out of stock. Please review your cart before proceeding.'
            ], 422);
        }

        // Apply promo code discount
        $appliedPromoCode = session('applied_promo_code');
        $promoDiscount = 0;

        if ($appliedPromoCode && $subtotal > 0) {
            $validation = PromoCodeService::validatePromoCode($appliedPromoCode, auth()->id(), $cartItems);
            if ($validation['valid']) {
                $promoDiscount = PromoCodeService::calculateDiscount($validation['promo_code'], $subtotal, $cartItems);
            }
        }

        $shippingCost = $deliveryCost = $taxAmount = 0;

        if ($estoreSettings) {
            // Calculate total ordered item count
            $totalQuantity = 0;
            foreach ($carts as $c) {
                $totalQuantity += (int)($c->quantity ?? 0);
            }

            if ($is_pickup == 0 || !$estoreSettings->is_pickup_available) {
                // Use shipping rules if configured
                if (is_array($estoreSettings->shipping_rules) && count($estoreSettings->shipping_rules) > 0) {
                    $shippingForQty = $estoreSettings->getShippingForQuantity($totalQuantity);
                    $shippingCost = $shippingForQty['shipping_cost'] ?? 0;
                    $deliveryCost = $shippingForQty['delivery_cost'] ?? 0;
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

        $otherCharges = [];
        foreach ($carts as $cart) {
            if (!empty($cart->product?->otherCharges)) {
                foreach ($cart->product->otherCharges as $charge) {
                    $otherCharges[$charge->charge_name] =
                        ($otherCharges[$charge->charge_name] ?? 0) + ($charge->charge_amount ?? 0);
                }
            }
        }

        DB::beginTransaction();

        try {
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

            $warehouseId = $carts->first()->warehouse_id ?? null;
            $wareHouse = WareHouse::find($warehouseId);

            // UC #1: Determine initial status
            // If pickup, go straight to processing.
            // If delivery, go to processing if cancel window is 0, otherwise pending.
            $cancelHours = $estoreSettings->cancel_within_hours ?? 0;
            if ($is_pickup) {
                $statusSlug = 'pickup_processing';
            } else {
                $statusSlug = ($cancelHours == 0) ? 'processing' : 'pending';
            }

            $order_status = OrderStatus::where('slug', $statusSlug)
                ->where('is_pickup', $is_pickup ? 1 : 0)
                ->first();

            // If the specific status wasn't found, fallback to the old logic
            if (!$order_status) {
                $statusSlug = $is_pickup ? 'pickup_pending' : 'pending';
                $order_status = OrderStatus::where('slug', $statusSlug)
                    ->where('is_pickup', $is_pickup ? 1 : 0)
                    ->first();
            }

            // UC #1: Calculate expected delivery date for non-pickup orders
            $expectedDeliveryDate = null;
            if (!$is_pickup) {
                $daysToAdd = $estoreSettings->expected_delivery_days ?? 7;
                $expectedDeliveryDate = now()->addDays($daysToAdd);
            }

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
                'shipping_amount' => $shippingCost,
                'handling_amount' => $deliveryCost,
                'total_amount' => $totalAmount,
                'credit_card_fee' => $creditCardFee,
                'payment_type' => $request->payment_type,
                'payment_status' => 'paid',
                'status' => $order_status->id ?? null,
                'warehouse_name' => $wareHouse->name ?? null,
                'warehouse_address' => $wareHouse->address ?? null,
                'promo_code' => $appliedPromoCode,
                'promo_discount' => $promoDiscount,
                'expected_delivery_date' => $expectedDeliveryDate,
            ]);
            // dd($order);

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

                $warehouseProduct = WarehouseProduct::where('warehouse_id', $cart->warehouse_id)
                    ->where('id', $cart->warehouse_product_id)
                    ->where('product_id', $cart->product_id)
                    ->first();

                if ($warehouseProduct) {
                    $warehouseProduct->decrement('quantity', $cart->quantity);
                    Log::info('Warehouse product stock updated', ['product_id' => $warehouseProduct->id, 'new_quantity' => $warehouseProduct->quantity]);

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
                    $this->notifyAdminIfOutOfStock($warehouseProduct);
                }
            }

            $groupByWareHouse = $carts->groupBy('warehouse_id');

            $allWarehouseAdminIds = [];

            foreach ($groupByWareHouse as $warehouseId => $warehouseCarts) {
                $warehouse = Warehouse::find($warehouseId);

                if ($warehouse && $warehouse->admins()->exists()) {
                    $warehouseAdmins = $warehouse->admins()->pluck('users.id')->toArray(); // 👈 fixed here
                    $allWarehouseAdminIds = array_merge($allWarehouseAdminIds, $warehouseAdmins);
                }
            }
            // dd($allWarehouseAdminIds);

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

                    // Send email to user
                    if ($user->email) {
                        try {
                            Mail::to($user->email)->queue(new OrderNotificationMail($order, $user, $warehouseCarts ?? []));
                        } catch (\Throwable $th) {
                            Log::error('Failed to send order notification email', ['error' => $th->getMessage()]);
                        }
                    }
                }

                // Insert notifications in bulk
                Notification::insert($notifications);
            }

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

            // For pickup orders use the pickup-specific template if available
            $isPickup = (bool)($order->is_pickup ?? false);
            $template = OrderEmailTemplate::where('order_status_id', $order_status->id)
                ->where('is_active', 1)
                ->where('is_pickup', $isPickup)
                ->first();

            // Fallback: if not found, try opposite type (delivery/pickup) then any
            if (!$template) {
                $template = OrderEmailTemplate::where('order_status_id', $order_status->id)
                    ->where('order_status_id', $order_status->id)
                    ->where('is_active', 1)
                    ->first();
            }

            if ($template) {
                // Build order list table HTML
                $orderList = view('user.emails.order_list_table', ['order' => $order])->render();
                $orderDetailsUrl = route('e-store.order-details', $order->id);
                $orderDetailsUrlButton = '<a href="' . $orderDetailsUrl . '" style="
                    display: inline-block;
                    padding: 10px 20px;
                    font-size: 16px;
                    color: #ffffff;
                    background-color: #643271;
                    text-decoration: none;
                    border-radius: 5px;
                ">View Order Details</a>';
                $body = str_replace(
                    ['{customer_name}', '{customer_email}', '{order_list}', '{order_id}', '{arriving_date}', '{total_order_value}', '{order_details_url_button}'],
                    [
                        $order->first_name ?? '' . ' ' . $order->last_name ?? '',
                        $order->email ?? '',
                        $orderList,
                        $order->order_number ?? '',
                        $order->expected_delivery_date ? Carbon::parse($order->expected_delivery_date)->format('M d, Y') : '',
                        number_format($order->total_amount ?? 0, 2),
                        $orderDetailsUrlButton
                    ],
                    $template->body
                );

                try {
                    // Send email
                    Mail::to($order->email)
                        ->send(new OrderStatusUpdatedMail($order, $body));
                } catch (\Throwable $th) {
                    Log::error('Failed to send order status email: ' . $th->getMessage());
                }
            }

            $checkoutUrl = route('e-store.order-success', $order->id);
            EstoreCart::where('user_id', auth()->id())->delete();

            // Clear promo code from session
            session()->forget(['applied_promo_code', 'promo_discount']);

            DB::commit();

            return response()->json([
                'status' => true,
                'checkout_url' => $checkoutUrl,
                'order_id' => $order->id
            ]);
        } catch (CardException $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => 'Card error: ' . $e->getMessage()]);
        } catch (RateLimitException $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => 'Too many requests: ' . $e->getMessage()]);
        } catch (InvalidRequestException | AuthenticationException | ApiConnectionException | ApiErrorException $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => 'Stripe error: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }




    // My Orders page
    public function myOrders()
    {
        if (!auth()->check()) {
            return redirect()->route('home')->with('error', 'Please login to view your orders');
        }
        $orders = EstoreOrder::with('orderItems')
            ->where('user_id', auth()->id())
            ->whereIn('payment_status', ['paid', 'refunded'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);


        $cartCount = EstoreCart::where('user_id', auth()->id())->count();
        $estoreSettings = EstoreSetting::first();
        $max_refundable_days = $estoreSettings->refund_max_days ?? 10;

        return view('ecom.my-orders', compact('orders', 'cartCount', 'max_refundable_days'));
    }

    // Order details
    public function orderDetails($orderId)
    {
        if (!auth()->check()) {
            return redirect()->route('home')->with('error', 'Please login to view your orders');
        }
        $order = EstoreOrder::with(['orderItems', 'payments'])
            ->where('id', $orderId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$order) {
            return redirect()->route('e-store.my-orders')->with('error', 'Order not found');
        }

        $cartCount = EstoreCart::where('user_id', auth()->id())->count();

        $order_refund = EstoreRefund::where('order_id', $order->id)->first();
        if ($order_refund) {
            $order->refund_status = $order_refund->is_approved;
        } else {
            $order->refund_status = null;
        }

        // get max refundable days from EstoreSetting
        $estoreSettings = EstoreSetting::first();
        $max_refundable_days = $estoreSettings->refund_max_days ?? 10;
        $allStatuses = OrderStatus::where('is_active', 1)
            ->where('is_pickup', $order->is_pickup ? 1 : 0)
            ->orderBy('sort_order', 'asc')
            ->get();

        // find the current status id on the order
        $currentStatusId = $order->status; // integer id (assumption)
        $currentStatusModel = $currentStatusId ? OrderStatus::find($currentStatusId) : null;

        // Normalize legacy/mismatched statuses for pickup vs delivery
        if ($currentStatusModel && (bool)$currentStatusModel->is_pickup !== (bool)$order->is_pickup) {
            $deliveryToPickup = [
                'pending' => 'pickup_pending',
                'processing' => 'pickup_processing',
                'shipped' => 'pickup_ready_for_pickup',
                'out_for_delivery' => 'pickup_picked_up',
                'delivered' => 'pickup_picked_up',
                'cancelled' => 'pickup_cancelled',
            ];
            $pickupToDelivery = [
                'pickup_pending' => 'pending',
                'pickup_processing' => 'processing',
                'pickup_ready_for_pickup' => 'shipped',
                'pickup_picked_up' => 'delivered',
                'pickup_cancelled' => 'cancelled',
            ];

            $mappedSlug = $order->is_pickup
                ? ($deliveryToPickup[$currentStatusModel->slug] ?? null)
                : ($pickupToDelivery[$currentStatusModel->slug] ?? null);

            if ($mappedSlug) {
                $mappedStatus = $allStatuses->firstWhere('slug', $mappedSlug);
                if ($mappedStatus) {
                    $currentStatusId = $mappedStatus->id;
                    $currentStatusModel = $mappedStatus;
                }
            }
        }

        // Optional: handle cancelled specially — if you want timeline to be [first, cancelled]
        $cancelSlug = $order->is_pickup ? 'pickup_cancelled' : 'cancelled';
        $cancelStatus = $allStatuses->firstWhere('slug', $cancelSlug);

        if ($currentStatusId && $cancelStatus && $currentStatusId == $cancelStatus->id) {
            // timeline = first (ordered) -> cancelled
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
            if ($currentStatusModel && (bool)$currentStatusModel->is_pickup === (bool)$order->is_pickup) {
                $timelineStatuses = $timelineStatuses->push($currentStatusModel);
                $statusIndex = $timelineStatuses->count() - 1;
            } else {
                $statusIndex = -1;
            }
        }
        return view('ecom.order-details', compact('order', 'cartCount', 'max_refundable_days', 'estoreSettings', 'timelineStatuses', 'statusIndex'));
    }

    // orderSuccess
    public function orderSuccess($orderId)
    {
        $order = EstoreOrder::with(['orderItems', 'payments'])
            ->where('id', $orderId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$order) {
            return redirect()->route('e-store.my-orders')->with('error', 'Order not found');
        }

        $cartCount = EstoreCart::where('user_id', auth()->id())->count();

        return view('ecom.order-success', compact('order', 'cartCount'));
    }

    // Add to wishlist with toggle if have then remove either set null
    public function addToWishlist(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'product_id' => 'required|integer',
            ]);

            if (!auth()->check()) {
                return response()->json(['status' => false, 'message' => 'Please login to add products to your wishlist']);
            }

            $product = Product::find($request->product_id);
            if (!$product) {
                return response()->json(['status' => false, 'message' => 'Product not found']);
            }

            $wishlistItem = EcomWishList::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->first();

            if ($wishlistItem) {
                // Remove from wishlist
                $wishlistItem->delete();
                return response()->json(['status' => true, 'action' => 'remove', 'message' => 'Product removed from wishlist']);
            } else {
                // Add to wishlist
                EcomWishList::create([
                    'user_id' => auth()->id(),
                    'product_id' => $product->id,
                ]);
                return response()->json(['status' => true, 'action' => 'added', 'message' => 'Product added to wishlist']);
            }
        }
    }

    // list wishlist for user
    public function wishlist()
    {

        // if not auth then redirect to login
        if (!auth()->check()) {
            // return redirect()->route('home')->with('error', 'Please login to view your wishlist');
            $wishlistItems = [];
        }

        $wishlistItems = EcomWishList::where('user_id', auth()->id())
            ->with('product')
            ->get();



        return view('ecom.wishlist')->with(compact('wishlistItems'));
    }

    // Remove from wishlist
    public function removeFromWishlist(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'product_id' => 'required|integer',
            ]);

            $wishlistItem = EcomWishList::where('user_id', auth()->id())
                ->where('product_id', $request->product_id)
                ->first();

            if (!$wishlistItem) {
                return response()->json(['status' => false, 'message' => 'Wishlist item not found']);
            }

            $wishlistItem->delete();

            return response()->json(['status' => true, 'message' => 'Product removed from wishlist']);
        }
    }

    // by ajax get warehouse product details by product id with optional size and color
    public function getWarehouseProductDetails(Request $request)
    {
        // return $request->all();
        if ($request->ajax()) {
            $request->validate([
                'product_id' => 'required|integer',
                'size_id' => 'nullable|integer',
                'color_id' => 'nullable|integer',
            ]);

            $productImages = [];
            $product = Product::where('id', $request->product_id)->where('is_deleted', 0)->first();
            if (!$product) {
                return response()->json(['status' => false, 'message' => 'Product not found']);
            }
            $nearbyWareHouseId = WareHouse::where('is_active', 1)->first()->id; // first id from warehouses
            $originLat = null;
            $originLng = null;
            $isUser = auth()->user();
            if ($isUser) { // Assuming user location is stored in user model
                $originLat = $isUser->location_lat;
                $originLng = $isUser->location_lng;
            } else {
                $originLat = session('location_lat');
                $originLng = session('location_lng');
            }
            // reuse helper to get nearest warehouse
            $nearest = Helper::getNearestWarehouse($originLat, $originLng);
            if (!empty($nearest['warehouse']->id)) {
                $nearbyWareHouseId = $nearest['warehouse']->id;
            }
            if ($product->product_type != 'simple') {
                $warehouseProduct = WarehouseProduct::where('warehouse_id', $nearbyWareHouseId)->where('product_id', $request->product_id)
                    ->when($request->size_id, function ($query) use ($request) {
                        return $query->where('size_id', $request->size_id);
                    })
                    ->when($request->color_id, function ($query) use ($request) {
                        return $query->where('color_id', $request->color_id);
                    })
                    ->first();
            } else {
                $warehouseProduct = WarehouseProduct::where('warehouse_id', $nearbyWareHouseId)->where('product_id', $request->product_id)->first();
            }

            $colorMatchedImages = [];
            $colorMatchedImages = ProductColorImage::where('product_id', $request->product_id)
                ->where('color_id', $request->color_id)
                ->get();
            // if found color images the set $productImages with array of image urls with color id, product id, image url, color name
            if ($colorMatchedImages->isNotEmpty()) {
                $productImages = $colorMatchedImages->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'color_id' => $item->color_id,
                        'image_path' => $item->image_path,
                        'color_name' => $item->color->color_name ?? null,
                    ];
                })->toArray();
            } else {
                // else get from product images
                $productImages = ProductImage::where('product_id', $request->product_id)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'product_id' => $item->product_id,
                            'image_path' => $item->image,
                        ];
                    })->toArray();
            }

            // return $warehouseProduct;

            if (!$warehouseProduct) {
                return response()->json(['status' => false, 'message' => 'Item Out Of Stock']);
            }

            return response()->json(['status' => true, 'data' => $warehouseProduct, 'productImages' => $productImages]);
        }
    }

    // notifyAdminIfOutOfStock
    protected function notifyAdminIfOutOfStock($warehouseProduct)
    {
        if ($warehouseProduct->quantity <= 0) {
            // Notify super admin and the user assigned that warehouse about out of stock product
            Log::info('Product is out of stock', ['product_id' => $warehouseProduct->product_id]);

            // product details
            $productDetails = [
                'product_id' => $warehouseProduct->product_id,
                'product_name' => $warehouseProduct->product->name ?? '',
                'warehouse_id' => $warehouseProduct->warehouse_id,
                'quantity' => $warehouseProduct->quantity,
            ];


            // Get the admin users directly through the relationship
            $warehouse_admin_users = $warehouseProduct->warehouse->admins ?? collect();
            foreach ($warehouse_admin_users as $assigned_user) {
                // In-app notification
                NotificationService::notifyUser($assigned_user->id, 'Product is out of stock: ' . $productDetails['product_name']);

                // Send queued email to assigned warehouse admin
                if (!empty($assigned_user->email)) {
                    try {
                        Mail::to($assigned_user->email)->queue(new OutOfStockNotificationMail($warehouseProduct, $assigned_user));
                    } catch (\Throwable $th) {
                        Log::error('Failed to send out-of-stock email to warehouse admin', ['user_id' => $assigned_user->id, 'error' => $th->getMessage()]);
                    }
                }
            }

            // Also notify and email all SUPER ADMIN users
            $superAdmins = User::where('user_type_id', 1)->get();
            foreach ($superAdmins as $sa) {
                NotificationService::notifyUser($sa->id, 'Product is out of stock: ' . $productDetails['product_name']);
                if (!empty($sa->email)) {
                    try {
                        Mail::to($sa->email)->queue(new OutOfStockNotificationMail($warehouseProduct, $sa));
                    } catch (\Throwable $th) {
                        Log::error('Failed to send out-of-stock email to super admin', ['user_id' => $sa->id, 'error' => $th->getMessage()]);
                    }
                }
            }
        }
    }

    // cancelOrder
    public function cancelOrder(Request $request)
    {

        //  return $request->all();
        if (!auth()->check()) {
            // return response()->json(['status' => false, 'message' => 'Please login to continue']);
            return redirect()->route('home')->with('error', 'Please login to continue');
        }

        $request->validate([
            'order_id' => 'required|integer',
        ]);

        $order = EstoreOrder::where('id', $request->order_id)
            ->where('user_id', auth()->id())
            ->where('payment_status', 'paid')
            ->first();

        if ($order) {
            $allowedSlugs = $order->is_pickup
                ? ['pickup_pending', 'pickup_processing']
                : ['pending', 'processing'];
            $allowedStatusIds = OrderStatus::whereIn('slug', $allowedSlugs)
                ->where('is_pickup', $order->is_pickup ? 1 : 0)
                ->pluck('id')
                ->toArray();

            if (!in_array((int)$order->status, $allowedStatusIds, true)) {
                $order = null;
            }
        }

        // Enforce cancellation window from settings (hours) — use minutes for accurate cutover
        $estoreSettings = EstoreSetting::first();
        $cancelHours = $estoreSettings->cancel_within_hours ?? 24;
        $elapsedMinutes = (int) $order->created_at->diffInMinutes(now());
        if ($order && $elapsedMinutes >= ($cancelHours * 60)) {
            return redirect()->back()->with('warning', "Order cancellation is allowed only within {$cancelHours} hours from the order date.");
        }

        //  return $order;

        if (!$order) {
            // return response()->json(['status' => false, 'message' => 'Order not found or cannot be cancelled']);
            return redirect()->back()->with('warning', 'Order cannot be cancelled');
        }

        DB::beginTransaction();

        try {
            $cancelSlug = $order->is_pickup ? 'pickup_cancelled' : 'cancelled';
            $cancelledStatusId = OrderStatus::where('slug', $cancelSlug)
                ->where('is_pickup', $order->is_pickup ? 1 : 0)
                ->value('id');

            // Update order status
            $order->update([
                'status' => $cancelledStatusId,
                'notes' => $request->cancellation_reason ?? null,
                'expected_delivery_date' => null,
            ]);

            // Refund payment if applicable
            $payment = EstorePayment::where('order_id', $order->id)
                ->where('status', 'succeeded')
                ->first();

            if ($payment) {
                Stripe::setApiKey(env('STRIPE_SECRET'));

                $refund = EstoreRefund::create([
                    'payment_intent' => $payment->stripe_payment_intent_id,
                    'amount' => $payment->amount, // in cents
                    // order id and user id
                    'order_id' => $order->id,
                    'user_id' => auth()->id(),
                ]);

                // // Update payment status
                // $payment->update([
                //     'status' => 'refunded',
                //     'payment_details' => array_merge(
                //         is_array($payment->payment_details) ? $payment->payment_details : [],
                //         [
                //             'refund_id' => $refund->id,
                //             'refund_status' => $refund->status,
                //             'amount_refunded' => $refund->amount / 100,
                //         ]
                //     ),
                // ]);
            }

            // Restock products
            $orderItems = EstoreOrderItem::where('order_id', $order->id)->get();
            foreach ($orderItems as $item) {
                $warehouseProduct = WarehouseProduct::where('warehouse_id', $item->warehouse_id)
                    ->where('id', $item->warehouse_product_id)
                    ->where('product_id', $item->product_id)
                    ->first();

                if ($warehouseProduct) {
                    // Increment warehouse product quantity
                    $warehouseProduct->increment('quantity', $item->quantity);

                    // Attempt to restock the corresponding warehouse product variation (if present)
                    $wareHouseProductVariation = WarehouseProductVariation::where('warehouse_id', $item->warehouse_id)
                        ->where('product_variation_id', $warehouseProduct->product_variation_id)
                        ->where('product_id', $item->product_id)
                        ->first();

                    if ($wareHouseProductVariation) {
                        $wareHouseProductVariation->increment('warehouse_quantity', $item->quantity);
                        $wareHouseProductVariation->updated_at = now();
                        $wareHouseProductVariation->save();
                    }
                }
            }

            // Send email if template exists for this status
            $template = OrderEmailTemplate::where('order_status_id', $cancelledStatusId)
                ->where('is_active', 1)
                ->where('is_pickup', $order->is_pickup ? 1 : 0)
                ->first();

            if (!$template) {
                $template = OrderEmailTemplate::where('order_status_id', $cancelledStatusId)
                    ->where('is_active', 1)
                    ->first();
            }

            if ($template) {
                $orderList = view('user.emails.order_list_table', ['order' => $order])->render();
                $orderDetailsUrl = route('e-store.order-details', $order->id);
                $orderDetailsUrlButton = '<a href="' . $orderDetailsUrl . '" style="
                    display: inline-block;
                    padding: 10px 20px;
                    font-size: 16px;
                    color: #ffffff;
                    background-color: #643271;
                    text-decoration: none;
                    border-radius: 5px;
                ">View Order Details</a>';

                $body = str_replace(
                    ['{customer_name}', '{customer_email}', '{order_list}', '{order_id}', '{arriving_date}', '{total_order_value}', '{order_details_url_button}'],
                    [
                        ($order->first_name ?? '') . ' ' . ($order->last_name ?? ''),
                        $order->email ?? '',
                        $orderList,
                        $order->order_number ?? '',
                        $order->expected_delivery_date ? Carbon::parse($order->expected_delivery_date)->format('M d, Y') : '',
                        number_format($order->total_amount ?? 0, 2),
                        $orderDetailsUrlButton
                    ],
                    $template->body
                );

                try {
                    Mail::to($order->email)->send(new OrderStatusUpdatedMail($order, $body));
                } catch (\Throwable $th) {
                    Log::error('Failed to send order cancellation status email: ' . $th->getMessage());
                }
            }

            DB::commit();

            $refundDays = $estoreSettings->refund_max_days ?? 7;
            $cancelMsg = "Order cancelled successfully. Refund is being processed within {$refundDays} business days.";

            // return response()->json(['status' => true, 'message' => $cancelMsg]);
            return redirect()->back()->with('message', $cancelMsg);
        } catch (\Exception $e) {
            DB::rollback();
            // return response()->json(['status' => false, 'message' => 'Failed to cancel order: ' . $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to cancel order: ' . $e->getMessage());
        }
    }

    public function liveSearch(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $q = trim($request->get('q', ''));
        if ($q === '') {
            return response()->json([]);
        }

        $products = Product::where('is_deleted', false)
            ->where('status', 1)
            ->where(function ($qr) use ($q) {
                $qr->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('short_description', 'LIKE', "%{$q}%");
            })
            ->with('images') // in case main_image accessor relies on relation
            ->orderBy('id', 'desc')
            ->limit(8)
            ->get();

        $results = $products->map(function ($p) {
            $image = $p->main_image ? Storage::url($p->main_image) : asset('ecom_assets/images/placeholder.png');
            return [
                'id'    => $p->id,
                'name'  => $p->name,
                'price' => number_format($p->price, 2),
                'image' => $image,
                'url'   => route('e-store.product-details', $p->slug),
            ];
        });

        return response()->json($results);
    }

    // Apply promo code
    public function applyPromoCode(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'promo_code' => 'required|string|max:255',
            ]);

            $isAuth = auth()->check();
            $userSessionId = session()->getId();

            $carts = $isAuth
                ? EstoreCart::where('user_id', auth()->id())->with(['product.otherCharges', 'warehouseProduct'])->get()
                : EstoreCart::where('session_id', $userSessionId)->with(['product.otherCharges', 'warehouseProduct'])->get();

            if ($carts->isEmpty()) {
                return response()->json(['status' => false, 'message' => 'Your cart is empty']);
            }

            // Build cart items array for validation
            $cartItems = [];
            $subtotal = 0;

            foreach ($carts as $cart) {
                $warehouseProduct = $cart->warehouseProduct;
                $currentWarehousePrice = $warehouseProduct?->price ?? 0;
                $availableQty = $warehouseProduct?->quantity ?? 0;

                if ($availableQty > 0) {
                    $otherCharges = $cart->product?->otherCharges?->sum('charge_amount') ?? 0;
                    $itemSubtotal = (($cart->price ?? 0) * $cart->quantity) + $otherCharges;
                    $subtotal += $itemSubtotal;

                    $cartItems[] = [
                        'product_id' => $cart->product_id,
                        'subtotal' => $itemSubtotal,
                    ];
                }
            }

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

            // Store promo code in session
            session(['applied_promo_code' => $request->promo_code]);
            session(['promo_discount' => $discountAmount]);

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

    // Remove promo code
    public function removePromoCode(Request $request)
    {
        if ($request->ajax()) {
            session()->forget(['applied_promo_code', 'promo_discount']);

            return response()->json([
                'status' => true,
                'message' => 'Promo code removed successfully'
            ]);
        }
    }
}
