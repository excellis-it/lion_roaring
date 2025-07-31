<?php

namespace App\Http\Controllers\Estore;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\EstoreCart;
use Illuminate\Http\Request;

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
        $cartCount = EstoreCart::where('user_id', auth()->id())->count();

        // Check if product is already in cart
        $cartItem = EstoreCart::where('user_id', auth()->id())
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
        $cartCount = EstoreCart::where('user_id', auth()->id())->count();
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

            $category = !empty($category_id) ? Category::whereIn('id', $category_id)->get()->toArray() : null;
            $cartCount = EstoreCart::where('user_id', auth()->id())->count();

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

            $product = Product::find($request->product_id);
            if (!$product) {
                return response()->json(['status' => false, 'message' => 'Product not found']);
            }

            // Check if product already exists in cart
            $existingCart = EstoreCart::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->first();

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

            $cartItem = EstoreCart::where('user_id', auth()->id())
                ->where('product_id', $request->product_id)
                ->first();

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

            $cart = EstoreCart::find($request->id);
            if (!$cart || $cart->user_id != auth()->id()) {
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

            $cart = EstoreCart::find($request->id);
            if (!$cart || $cart->user_id != auth()->id()) {
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
            $carts = EstoreCart::where('user_id', auth()->id())->get();
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
            $cartCount = EstoreCart::where('user_id', auth()->id())->count();
            return response()->json(['status' => true, 'cartCount' => $cartCount]);
        }
    }

    // cartList
    public function cartList(Request $request)
    {
        if ($request->ajax()) {
            $carts = EstoreCart::where('user_id', auth()->id())
                ->with('product')
                ->get();

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
        $carts = EstoreCart::where('user_id', auth()->id())
            ->with('product')
            ->get();
        $cartCount = EstoreCart::where('user_id', auth()->id())->count();

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
    public function checkout(){
        $carts = EstoreCart::where('user_id', auth()->id())
            ->with('product')
            ->get();
        $cartCount = EstoreCart::where('user_id', auth()->id())->count();

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
}
