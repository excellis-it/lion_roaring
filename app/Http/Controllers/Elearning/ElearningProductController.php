<?php

namespace App\Http\Controllers\Elearning;

use App\Http\Controllers\Controller;
use App\Models\ElearningCategory;
use App\Models\ElearningProduct;
use App\Models\ElearningTopic;
use App\Models\ElearningReview;
use Illuminate\Http\Request;

class ElearningProductController extends Controller
{
    public function productDetails($slug)
    {
        $product = ElearningProduct::with('elearningTopic')->where('slug', $slug)->first();
        $related_products = ElearningProduct::where('category_id', $product->category_id)
            ->where(function ($query) use ($product) {
                $query->where('id', '!=', $product->id)
                    ->where('status', 1)
                    ->where('quantity', '>', 0);
            })
            ->orderBy('id', 'DESC')
            ->limit(8)
            ->get();
        $reviews = $product->reviews()->where('status', 1)->orderBy('id', 'DESC')->get();
        return view('elearning.product-details')->with(compact('product', 'related_products', 'reviews'));
    }

    public function products(Request $request, $category_id = null)
    {
        $category_id = $category_id ?? ''; // Default value is ' '
        $products = ElearningProduct::where('status', 1)->with('elearningTopic');
        if ($category_id) {
            $products = $products->where('category_id', $category_id);
            $category = ElearningCategory::find($category_id);
        } else {
            $category = null;
        }
        $products = $products->orderBy('id', 'DESC')->limit(12)->get();
        // dd($products);

        $products_count  = $products->count();
        $categories = ElearningCategory::where('status', 1)->orderBy('id', 'DESC')->get();
        $topics = ElearningTopic::orderBy('id', 'desc')->get();
        return view('elearning.products')->with(compact('products', 'categories', 'category_id', 'products_count', 'category', 'topics'));
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
            $topic_id = $request->elearning_topic_id ?? [];
            $topic_search = $request->elearning_topic_search ?? '';

            $products = ElearningProduct::where('status', 1)->with(['image', 'elearningTopic']);

            if (!empty($category_id)) {
                $products->whereIn('category_id', $category_id);
            }

            if (!empty($topic_id)) {
                $products->whereIn('elearning_topic_id', $topic_id);
            }

            if (!empty($topic_search)) {
                $products->whereHas('elearningTopic', function ($q) use ($topic_search) {
                    $q->where('topic_name', 'LIKE', "%$topic_search%");
                });
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

            $category = !empty($category_id) ? ElearningCategory::whereIn('id', $category_id)->get()->toArray() : null;

            $view = view('elearning.partials.product-item', compact('products', 'products_count'))->render();
            $view2 = view('elearning.partials.count-product', compact('products', 'products_count', 'category', 'category_id'))->render();

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

        $product = ElearningProduct::find($request->product_id);
        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found']);
        }

        if ($product->reviews()->where('user_id', auth()->id())->count() > 0) {
            return response()->json(['status' => false, 'message' => 'You have already submitted a review for this product']);
        }

        $review = new ElearningReview();
        $review->product_id = $request->product_id;
        $review->user_id = auth()->id();
        $review->rating = $request->rate;
        $review->review = $request->review;
        $review->status = 1;
        $review->save();

        // Render the review view
        $reviews = $product->reviews()->where('status', 1)->orderBy('id', 'DESC')->get();
        $view = view('elearning.partials.product-review', compact('reviews'))->render();

        return response()->json(['status' => true, 'message' => 'Review submitted successfully', 'view' => $view]);
    }
}
