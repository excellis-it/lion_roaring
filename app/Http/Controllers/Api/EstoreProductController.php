<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;

/**
 * @group E-store
 * 
 * @authenticated
 */

class EstoreProductController extends Controller
{
    /**
     * Fetch Products
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
            $productsQuery = Product::where('status', 1);

            // Check if category_id is provided and filter by category
            if ($category_id) {
                $productsQuery = $productsQuery->where('category_id', $category_id);
                $category = Category::find($category_id);
            } else {
                $category = null;
            }

            // Get the products with a limit of 12, ordered by id descending
            $products = $productsQuery->orderBy('id', 'DESC')->limit(12)->get();

            // Count the total number of products
            $products_count = $products->count();

            // Get the categories
            $categories = Category::where('status', 1)->orderBy('id', 'DESC')->get();

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
}
