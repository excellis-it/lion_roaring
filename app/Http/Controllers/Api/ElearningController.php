<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\ElearningCategory;
use App\Models\ElearningProduct;
use App\Models\ElearningReview;
use App\Models\ElearningEcomHomeCms;
use App\Models\ElearningEcomNewsletter;
use App\Models\ElearningSubCategory;
use App\Models\ElearningTopic;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * @group E-learning
 *
 * @authenticated
 */

class ElearningController extends Controller
{
    /** @var array<int, string> */
    private const PRODUCT_RELATIONS = ['image', 'elearningTopic', 'category', 'subcategory'];

    //
    /**
     * Fetch Products
     *
     *
     * @response 200 {
     *   "products": [
     *     {
     *            "id": 13,
     *            "category_id": 1,
     *            "user_id": 1,
     *            "name": "New Product",
     *            "description": null,
     *            "short_description": "In today’s rapidly evolving world, traditional higher education models are",
     *            "sku": null,
     *            "specification": null,
     *            "affiliate_link": "http://127.0.0.1:8000/",
     *            "price": null,
     *            "quantity": null,
     *            "discount": null,
     *            "slug": "new-product",
     *            "meta_title": null,
     *            "meta_description": null,
     *            "status": 1,
     *            "feature_product": 1,
     *            "today_deals": 0,
     *            "created_at": "2024-08-21T09:12:48.000000Z",
     *            "updated_at": "2024-08-21T09:12:48.000000Z",
     *            "button_name": "new shop"
     *        },
     *        {
     *            "id": 11,
     *            "category_id": 1,
     *            "user_id": 1,
     *            "name": "Jesus Ministry Store",
     *            "description": "<p>Product 10 description</p>",
     *            "short_description": "Incredible! Three students from Medhavi Skills University stole the show at the Yoga - India",
     *            "sku": "BT-555",
     *            "specification": "<p>Incredible! Three students from Medhavi Skills University stole the show at the Yoga - India</p>",
     *            "affiliate_link": "https://webstore.jesusministries.org",
     *            "price": 0,
     *            "quantity": 0,
     *            "discount": null,
     *            "slug": "jesus-ministry-store",
     *            "meta_title": null,
     *            "meta_description": null,
     *            "status": 1,
     *            "feature_product": 0,
     *            "today_deals": 0,
     *            "created_at": "2024-07-19T12:24:14.000000Z",
     *            "updated_at": "2024-08-03T22:26:53.000000Z",
     *            "button_name": null
     *        },
     *     ...
     *   ],
     *   "categories": [
     *     {
     *       "id": 1,
     *       "name": "Category Name",
     *       "status": 1
     *     },
     *     ...
     *   ],
     *   "products_count": 20,
     *   "category": {
     *     "id": 1,
     *     "name": "Category Name",
     *     "status": 1
     *   }
     * }
     *
     * @response 201 {
     *   "message": "No products found for this category."
     * }
     */
    public function products(Request $request, $category_id = null)
    {
        try {
            // If category_id is provided, filter products by category
            $category_id = $category_id ?? ''; // Default value is ''
            $productsQuery = ElearningProduct::with(['image', 'elearningTopic'])->where('status', 1);

            // Check if category_id is provided and filter by category
            if ($category_id) {
                $productsQuery = $productsQuery->where('category_id', $category_id);
                $category = ElearningCategory::find($category_id);
            } else {
                $category = null;
            }

            // Get the products with a limit of 12, ordered by id descending
            $products = $productsQuery->orderBy('id', 'DESC')->limit(12)->get();

            // Count the total number of products
            $products_count = $products->count();

            // Get the categories
            $categories = ElearningCategory::where('status', 1)->orderBy('id', 'DESC')->get();

            // Check if products exist
            if ($products_count === 0) {
                return response()->json([
                    'message' => 'No products found for this category.'
                ], 201);
            }

            // Return the response with products, categories, and count
            return response()->json([
                'products' => $products,
                'categories' => $categories,
                'products_count' => $products_count,
                'category' => $category
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the products.'
            ], 201);
        }
    }

    /**
     * Store Home Products
     *
     * Fetches categories, featured products, new products, and section titles for the home page.
     *
     * @response 200 {
     *   "section_titles": {...},
     *   "categories": [...],
     *   "feature_products": [...],
     *   "new_products": [...]
     * }
     */

    public function storeHome(Request $request)
    {
        try {
            $categories = ElearningCategory::where('status', 1)->orderBy('name')->get();
            $feature_products = ElearningProduct::with(self::PRODUCT_RELATIONS)
                ->where('status', 1)
                ->where('feature_product', 1)
                ->orderByDesc('id')
                ->get();
            $new_products = ElearningProduct::with(self::PRODUCT_RELATIONS)
                ->where('status', 1)
                ->orderByDesc('id')
                ->limit(10)
                ->get();
            $books = ElearningProduct::with(self::PRODUCT_RELATIONS)
                ->where('status', 1)
                ->whereHas('category', fn ($q) => $q->where('slug', 'books'))
                ->orderByDesc('id')
                ->limit(10)
                ->get();
            $lockets = ElearningProduct::with(self::PRODUCT_RELATIONS)
                ->where('status', 1)
                ->whereHas('category', fn ($q) => $q->where('slug', 'lockets'))
                ->orderByDesc('id')
                ->limit(10)
                ->get();

            $cms = Helper::getVisitorCmsContent('ElearningEcomHomeCms', true, false, 'id', 'desc', null);
            $content = $cms ? $cms->toArray() : [];
            $section_titles = $cms ?? ElearningEcomHomeCms::orderBy('id', 'desc')->first();

            $footerCms = Helper::getElearningFooterCms();
            $footer = $footerCms ? $footerCms->toArray() : null;
            if (is_array($footer)) {
                $articlesPdf = Helper::getPDFAttribute();
                $footer['articles_agreement_url'] = $articlesPdf
                    ? (str_starts_with($articlesPdf, 'http')
                        ? $articlesPdf
                        : url($articlesPdf))
                    : null;
            }

            return response()->json([
                'status' => true,
                'message' => 'Store home.',
                'content' => $content,
                'section_titles' => $section_titles,
                'categories' => $categories,
                'feature_products' => $feature_products,
                'new_products' => $new_products,
                'books' => $books,
                'lockets' => $lockets,
                'footer' => $footer,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching the products.',
            ], 500);
        }
    }

    /**
     *
     * Get Products by Category Slug
     *
     * Get 12 latest products by category slug.
     *
     * @urlParam slug string required The category slug. Example: electronics
     *
     * @response 200 {
     *   "products": [...],
     *   "categories": [...],
     *   "products_count": 12,
     *   "category": {
     *     "id": 3,
     *     "name": "Electronics",
     *     ...
     *   }
     * }
     * @response 201 {
     *   "message": "Category not found"
     * }
     */
    public function productsByCategorySlug($slug)
    {
        try {
            $category = ElearningCategory::where('slug', $slug)->where('status', 1)->first();

            if (!$category) {
                return response()->json(['message' => 'Category not found'], 201);
            }

            $products = ElearningProduct::with(['image', 'elearningTopic'])
                ->where('status', 1)
                ->where('category_id', $category->id)
                ->orderBy('id', 'DESC')
                ->limit(12)
                ->get();

            $products_count = $products->count();
            //  $categories = Category::where('status', 1)->orderBy('id', 'DESC')->get();

            return response()->json([
                'products' => $products,
                //   'categories' => $categories,
                'products_count' => $products_count,
                'category' => $category,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the products.'
            ], 201);
        }
    }

    /**
     *
     *
     * Product Details
     *
     * @urlParam slug string required The slug of the product.
     *
     * @response 200 {
     *   "product": {...},
     *   "related_products": [...],
     *   "reviews": [...]
     * }
     */
    public function productDetails($slug)
    {
        try {
            $product = ElearningProduct::with(array_merge(self::PRODUCT_RELATIONS, ['images']))
                ->where('slug', $slug)
                ->where('status', 1)
                ->first();

            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found.',
                ], 404);
            }

            $related_products = ElearningProduct::with(self::PRODUCT_RELATIONS)
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where('status', 1)
                ->orderByDesc('id')
                ->limit(8)
                ->get();

            $reviews = $product->reviews()
                ->where('status', 1)
                ->with(['user:id,first_name,last_name,profile_picture'])
                ->orderByDesc('id')
                ->limit(20)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Product details.',
                'product' => $product,
                'related_products' => $related_products,
                'reviews' => $reviews,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching product details.',
            ], 500);
        }
    }


    /**
     *
     *
     * Filter Products
     *
     * @queryParam category_id[] array Optional. Array of category IDs.
     * @queryParam elearning_topic_id[] array Optional. Array of ElearningTopic IDs.
     * @queryParam elearning_topic_search string Optional. Search by ElearningTopic text.
     * @queryParam latestFilter string Optional. Filter by latest, A-Z, Z-A.
     * @queryParam search string Optional. Search by product name.
     * @queryParam page integer Optional. Page number.
     *
     * @response 200 {
     *   "products": [...],
     *   "products_count": 10
     * }
     */
    public function productsFilter(Request $request)
    {
        try {
            $categoryIds = $this->normalizeIdList($request->input('category_id'));
            $latest_filter = $request->latestFilter ?? '';
            $search = $request->search ?? '';

            $products = ElearningProduct::with(self::PRODUCT_RELATIONS)->where('status', 1);

            if (! empty($categoryIds)) {
                $products->whereIn('category_id', $categoryIds);
            }

            $elearningTopicIds = $this->normalizeIdList($request->input('elearning_topic_id'));
            $elearning_topic_search = $request->elearning_topic_search ?? '';

            if (! empty($elearningTopicIds)) {
                $products->whereIn('elearning_topic_id', $elearningTopicIds);
            }

            if (! empty($elearning_topic_search)) {
                $products->whereHas('elearningTopic', function ($q) use ($elearning_topic_search) {
                    $q->where('topic_name', 'LIKE', "%$elearning_topic_search%");
                });
            }

            $subCategoryIds = $this->normalizeIdList($request->input('elearning_sub_category_id'));
            if (! empty($subCategoryIds)) {
                $products->whereIn('elearning_sub_category_id', $subCategoryIds);
            }

            if (!empty($latest_filter)) {
                if ($latest_filter === 'A to Z') {
                    $products->orderBy('name', 'asc');
                } elseif ($latest_filter === 'Z to A') {
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

            $products_count = $products->count();

            //  $products = $products->skip($offset)->take($limit)->get();
            $perPage = max(1, min(50, (int) $request->input('per_page', 12)));
            $products = $products->paginate($perPage);

            return response()->json([
                'status' => true,
                'message' => 'Filtered products.',
                'products' => $products,
                'products_count' => $products_count,
            ], 200);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Error filtering products.',
            ], 500);
        }
    }

    /**
     * POST /e-learning/newsletter
     */
    public function newsletterStore(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'newsletter_email' => 'required|email|unique:elearning_ecom_newsletters,email',
            'newsletter_name' => 'nullable|string|max:255',
            'newsletter_message' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        ElearningEcomNewsletter::create([
            'name' => $request->input('newsletter_name', ''),
            'email' => $request->newsletter_email,
            'message' => $request->input('newsletter_message', ''),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Thank you for subscribing to our newsletter.',
        ]);
    }

    /**
     * GET /e-learning/get-subcategories?category_id[]=1
     * Used by the collection filter sidebar (same as web).
     */
    public function getSubcategories(Request $request): \Illuminate\Http\JsonResponse
    {
        $categoryIds = $this->normalizeIdList($request->input('category_id'));

        $query = ElearningSubCategory::where('status', 1)->orderBy('name');

        if (! empty($categoryIds)) {
            $query->whereIn('elearning_category_id', $categoryIds);
        }

        return response()->json([
            'status' => true,
            'message' => 'Sub-categories.',
            'data' => $query->get(['id', 'name', 'elearning_category_id']),
        ]);
    }

    /**
     * GET /e-learning/topics-list
     */
    public function topicsList(): \Illuminate\Http\JsonResponse
    {
        $topics = ElearningTopic::orderBy('topic_name')->get(['id', 'topic_name']);

        return response()->json([
            'status' => true,
            'message' => 'Topics.',
            'data' => $topics,
        ]);
    }

    /**
     * GET /e-learning/categories/{id}/sub-categories
     */
    public function subCategoriesByCategory(int $id)
    {
        $subs = ElearningSubCategory::where('elearning_category_id', $id)
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Sub-categories.',
            'data' => $subs,
        ]);
    }

    /**
     * GET /e-learning/sub-categories/{id}/topics
     * Returns the distinct topics used by products within a given sub-category.
     */
    public function topicsBySubCategory(int $id)
    {
        $topicIds = ElearningProduct::where('elearning_sub_category_id', $id)
            ->whereNotNull('elearning_topic_id')
            ->pluck('elearning_topic_id')
            ->unique()
            ->values();

        $topics = ElearningTopic::whereIn('id', $topicIds)->orderBy('topic_name')->get();

        return response()->json([
            'status' => true,
            'message' => 'Topics.',
            'data' => $topics,
        ]);
    }

    /**
     * GET /e-learning/topics/{id}
     */
    public function topicDetail(int $id)
    {
        $topic = ElearningTopic::find($id);
        if (!$topic) {
            return response()->json(['status' => false, 'message' => 'Topic not found.'], 404);
        }

        return response()->json(['status' => true, 'message' => 'Topic.', 'data' => $topic]);
    }

    /**
     * GET /e-learning/topics/{id}/products
     */
    public function productsByTopic(int $id, Request $request)
    {
        $perPage = max(1, min(50, (int) $request->input('per_page', 12)));

        $products = ElearningProduct::where('elearning_topic_id', $id)
            ->where('status', 1)
            ->with(['image', 'category', 'subcategory'])
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Products by topic.',
            'data' => $products,
        ]);
    }

    /**
     * Mobile clients often send filter ids as a single query value (e.g. category_id=18).
     * Laravel's whereIn() requires an array — normalize scalars and arrays alike.
     *
     * @param  mixed  $value
     * @return array<int, int>
     */
    private function normalizeIdList($value): array
    {
        if ($value === null || $value === '' || $value === []) {
            return [];
        }

        if (! is_array($value)) {
            $value = [$value];
        }

        return array_values(array_filter(array_map('intval', $value), static fn (int $id) => $id > 0));
    }
}
