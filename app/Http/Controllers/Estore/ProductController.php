<?php

namespace App\Http\Controllers\Estore;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
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
        return view('ecom.product-details')->with(compact('product', 'related_products', 'reviews'));
    }

    public function products(Request $request, $category_id = null)
    {
        $category_id = $category_id ?? ''; // Default value is ' '
        $products = Product::where('status', 1)->where('quantity', '>', 0);
        if ($category_id) {
            $products = $products->where('category_id', $category_id);
            $category = Category::find($category_id);
        } else {
            $category = null;
        }
        $products = $products->orderBy('id', 'DESC')->limit(12)->get();

        $products_count  = $products->count();
        $categories = Category::where('status', 1)->orderBy('id', 'DESC')->get();
        return view('ecom.products')->with(compact('products', 'categories', 'category_id', 'products_count', 'category'));
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

            if (!empty($prices)) {
                $products->where(function ($query) use ($prices) {
                    foreach ($prices as $price) {
                        if ($price == 'Below 500') {
                            $query->orWhere('price', '<', 500);
                        } elseif ($price == 'Above 5000') {
                            $query->orWhere('price', '>', 5000);
                        } else {
                            $priceRange = explode('-', $price);
                            $query->orWhereBetween('price', [$priceRange[0], $priceRange[1]]);
                        }
                    }
                });
            }

            if (!empty($latest_filter)) {
                if ($latest_filter == 'Low to High') {
                    $products->orderBy('price', 'asc');
                } elseif ($latest_filter == 'High to Low') {
                    $products->orderBy('price', 'desc');
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
            $products = $products->where('quantity', '>', 0)
                ->skip($offset)
                ->take($limit)
                ->get()->toArray();

            $category = !empty($category_id) ? Category::whereIn('id', $category_id)->get()->toArray() : null;

            $view = view('ecom.partials.product-item', compact('products', 'products_count'))->render();
            $view2 = view('ecom.partials.count-product', compact('products', 'products_count', 'category', 'category_id'))->render();

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
        $view = view('ecom.partials.product-review', compact('reviews'))->render();

        return response()->json(['status' => true, 'message' => 'Review submitted successfully', 'view' => $view]);
    }
}
