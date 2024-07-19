<?php

namespace App\Http\Controllers\Estore;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function productDetails($slug)
    {
        $product = Product::where('slug', $slug)->first();
        return view('ecom.product-details')->with(compact('product'));
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
        $products = $products->orderBy('id', 'DESC')->get();

        $product_count = $products->count();
        $categories = Category::where('status', 1)->orderBy('id', 'DESC')->get();
        return view('ecom.products')->with(compact('products', 'categories', 'category_id', 'product_count', 'category'));
    }
}
