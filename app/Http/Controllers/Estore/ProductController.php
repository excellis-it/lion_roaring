<?php

namespace App\Http\Controllers\Estore;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\EstoreCart;
use App\Models\EstoreOrder;
use App\Models\EstoreOrderItem;
use App\Models\EstorePayment;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Facades\DB;
use App\Models\EcomWishList;

class ProductController extends Controller
{
    public function productDetails($slug)
    {
        $product = Product::where('slug', $slug)->first();
        $related_products = Product::where('category_id', $product->category_id)
            ->where(function ($query) use ($product) {
                $query->where('id', '!=', $product->id)
                    ->where('status', 1)
                    ->where('quantity', '>', 0);
            })
            ->orderBy('id', 'DESC')
            ->limit(8)
            ->get();
        $reviews = $product->reviews()->where('status', 1)->orderBy('id', 'DESC')->get();
        $isAuth = auth()->check();
        $userSessionId = session()->getId();
        $cartCount = $isAuth ? EstoreCart::where('user_id', auth()->id())->count() : EstoreCart::where('session_id', $userSessionId)->count();

        // Check if product is already in cart
        $cartItem = $isAuth ? EstoreCart::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first() : EstoreCart::where('session_id', $userSessionId)
            ->where('product_id', $product->id)
            ->first();


        return view('ecom.product-details')->with(compact('product', 'related_products', 'reviews', 'cartCount', 'cartItem'));
    }

    public function products(Request $request, $category_id = null)
    {
        $category_id = $category_id ?? ''; // Default value is ' '
        $products = Product::where('status', 1);
        if ($category_id) {
            $products = $products->where('category_id', $category_id);
            $category = Category::find($category_id);
        } else {
            $category = null;
        }
        $products = $products->orderBy('id', 'DESC')->limit(12)->get();
        // dd($products);

        $products_count  = $products->count();
        $categories = Category::where('status', 1)->orderBy('id', 'DESC')->get();
        //  return $categories;
        $isAuth = auth()->check();
        $userSessionId = session()->getId();
        $cartCount = $isAuth ? EstoreCart::where('user_id', auth()->id())->count() : EstoreCart::where('session_id', $userSessionId)->count();

        return view('ecom.products')->with(compact('products', 'categories', 'category_id', 'products_count', 'category', 'cartCount'));
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

            $products = Product::where('status', 1)->with('image');

            if (!empty($category_id)) {
                $products->whereIn('category_id', $category_id);
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
            $products_count = $products->count();

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
        $review->status = 1;
        $review->save();

        // Render the review view
        $reviews = $product->reviews()->where('status', 1)->orderBy('id', 'DESC')->get();
        $cartCount = EstoreCart::where('user_id', auth()->id())->count();
        $view = view('ecom.partials.product-review', compact('reviews', 'cartCount'))->render();

        return response()->json(['status' => true, 'message' => 'Review submitted successfully', 'view' => $view]);
    }

    // addToCart
    public function addToCart(Request $request)
    {
        if ($request->ajax()) {
            $request->validate([
                'product_id' => 'required|integer',
                'quantity' => 'required|integer|min:1',
            ]);

            $isAuth = auth()->check();

            $userSessionId = session()->getId();


            $product = Product::find($request->product_id);
            if (!$product) {
                return response()->json(['status' => false, 'message' => 'Product not found']);
            }

            // Check if product already exists in cart
            if ($isAuth) {
                $existingCart = EstoreCart::where('user_id', auth()->id())
                    ->where('product_id', $product->id)
                    ->first();
            } else {
                $existingCart = EstoreCart::where('session_id', $userSessionId)
                    ->where('product_id', $product->id)
                    ->first();
            }

            if ($existingCart) {
                // Update quantity instead of creating new entry
                $existingCart->quantity += $request->quantity;
                $existingCart->save();
                return response()->json([
                    'status' => true,
                    'message' => 'Cart updated successfully',
                    'action' => 'updated',
                    'cart_item_id' => $existingCart->id,
                    'quantity' => $existingCart->quantity
                ]);
            }

            $cart = new EstoreCart();
            $cart->user_id = auth()->id();
            $cart->product_id = $product->id;
            $cart->price = $product->price;
            $cart->quantity = $request->quantity;
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

            return response()->json([
                'status' => true,
                'inCart' => $cartItem ? true : false,
                'cartItem' => $cartItem ? [
                    'id' => $cartItem->id,
                    'quantity' => $cartItem->quantity
                ] : null
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
            $cartCount = $isAuth ? EstoreCart::where('user_id', auth()->id())->count() : EstoreCart::where('session_id', $userSessionId)->count();
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

        $carts = $isAuth ? EstoreCart::where('user_id', auth()->id())->with('product')->get() : EstoreCart::where('session_id', $userSessionId)->with('product')->get();
        $cartCount = $isAuth ? EstoreCart::where('user_id', auth()->id())->count() : EstoreCart::where('session_id', $userSessionId)->count();

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

        return view('ecom.cart')->with(compact('cartItems', 'total', 'cartCount'));
    }

    // checkout page
    public function checkout()
    {
        if (!auth()->check()) {
            return redirect()->route('home')->with('error', 'Please login to continue');
        }

        $carts = EstoreCart::where('user_id', auth()->id())
            ->with('product')
            ->get();
        $cartCount = EstoreCart::where('user_id', auth()->id())->count();

        if ($carts->isEmpty()) {
            return redirect()->route('e-store.cart')->with('error', 'Your cart is empty');
        }

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

        return view('ecom.checkout')->with(compact('cartItems', 'total', 'cartCount'));
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
        ]);



        $carts = EstoreCart::where('user_id', auth()->id())->with('product')->get();

        if ($carts->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Your cart is empty']);
        }

        $subtotal = $carts->sum(function ($cart) {
            return $cart->price * $cart->quantity;
        });

        try {
            DB::beginTransaction();

            // Create order
            $order = EstoreOrder::create([
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
                'tax_amount' => 0,
                'shipping_amount' => 0,
                'total_amount' => $subtotal,
                'status' => 'pending',
                'payment_status' => 'pending'
            ]);

            // Create order items
            foreach ($carts as $cart) {
                EstoreOrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'product_name' => $cart->product->name,
                    'product_image' => $cart->product->main_image,
                    'price' => $cart->price,
                    'quantity' => $cart->quantity,
                    'total' => $cart->price * $cart->quantity
                ]);
            }

            // Create Stripe Checkout Session
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $lineItems = [];
            foreach ($carts as $cart) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $cart->product->name,
                            'description' => $cart->product->short_description ?? '',
                        ],
                        'unit_amount' => $cart->price * 100, // Amount in cents
                    ],
                    'quantity' => $cart->quantity,
                ];
            }

            $checkoutSession = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('e-store.payment-success') . '?session_id={CHECKOUT_SESSION_ID}&order_id=' . $order->id,
                'cancel_url' => route('e-store.checkout') . '?cancelled=1',
                'metadata' => [
                    'order_id' => $order->id,
                    'user_id' => auth()->id()
                ]
            ]);

            // Create payment record with session ID
            EstorePayment::create([
                'order_id' => $order->id,
                'stripe_payment_intent_id' => $checkoutSession->id,
                'payment_method' => 'stripe',
                'amount' => $subtotal,
                'currency' => 'USD',
                'status' => 'pending'
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'checkout_url' => $checkoutSession->url,
                'order_id' => $order->id
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    // Payment success
    public function paymentSuccess(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('home')->with('error', 'Please login to continue');
        }
        $request->validate([
            'session_id' => 'required|string',
            'order_id' => 'required|integer'
        ]);

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Retrieve the session from Stripe
            $session = StripeSession::retrieve($request->session_id);

            if ($session->payment_status === 'paid') {
                DB::beginTransaction();

                $order = EstoreOrder::find($request->order_id);

                // Check if this order belongs to the current user
                if (!$order || $order->user_id != auth()->id()) {
                    return redirect()->route('e-store.cart')->with('error', 'Order not found');
                }

                // Check if payment is already processed to avoid duplicate updates
                if ($order->payment_status === 'paid') {
                    return redirect()->route('e-store.order-success', $order->id)
                        ->with('info', 'Order already processed');
                }

                $payment = EstorePayment::where('order_id', $order->id)
                    ->where('stripe_payment_intent_id', $request->session_id)
                    ->first();

                if ($order && $payment) {
                    // Update order status
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'processing'
                    ]);

                    // Update payment status
                    $payment->update([
                        'status' => 'succeeded',
                        'payment_details' => [
                            'session_id' => $session->id,
                            'payment_intent' => $session->payment_intent,
                            'payment_status' => $session->payment_status,
                            'amount_total' => $session->amount_total
                        ],
                        'paid_at' => now()
                    ]);

                    // Clear cart
                    EstoreCart::where('user_id', auth()->id())->delete();

                    DB::commit();

                    return redirect()->route('e-store.order-success', $order->id)
                        ->with('success', 'Payment successful! Your order has been placed.');
                }
            }

            return redirect()->route('e-store.checkout')
                ->with('error', 'Payment verification failed');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('e-store.checkout')
                ->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    // Handle cancelled payments
    public function paymentCancelled()
    {
        return redirect()->route('e-store.checkout')
            ->with('warning', 'Payment was cancelled. You can try again.');
    }

    // My Orders page
    public function myOrders()
    {
        if (!auth()->check()) {
            return redirect()->route('home')->with('error', 'Please login to view your orders');
        }
        $orders = EstoreOrder::with('orderItems')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);


        $cartCount = EstoreCart::where('user_id', auth()->id())->count();

        return view('ecom.my-orders', compact('orders', 'cartCount'));
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

        return view('ecom.order-details', compact('order', 'cartCount'));
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

    // Add to wishlist with toggle if have then remove either insert
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
            return redirect()->route('home')->with('error', 'Please login to view your wishlist');
        }

        $wishlistItems = EcomWishList::where('user_id', auth()->id())
            ->with('product')
            ->get();

        $cartCount = EstoreCart::where('user_id', auth()->id())->count();

        return view('ecom.wishlist')->with(compact('wishlistItems', 'cartCount'));
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
}
