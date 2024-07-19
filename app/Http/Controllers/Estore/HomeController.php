<?php

namespace App\Http\Controllers\Estore;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function eStore()
    {
        $categories = Category::where('status', 1)->orderBy('id', 'DESC')->get();
        $feature_products = Product::where('status', 1)->where('feature_product', 1)->orderBy('id', 'DESC')->get();
        $new_products = Product::where('status', 1)->orderBy('id', 'DESC')->limit(10)->get();
        $books = Product::where('status', 1)->whereHas('category', function ($q) {
            $q->where('slug', 'books');
        })->orderBy('id', 'DESC')->limit(10)->get();
        $lockets = Product::where('status', 1)->whereHas('category', function ($q) {
            $q->where('slug', 'lockets');
        })->orderBy('id', 'DESC')->limit(10)->get();
        return view('ecom.home')->with(compact('categories', 'feature_products', 'new_products', 'books', 'lockets'));
    }
}
