<?php

namespace App\Http\Controllers\Estore;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\EcomHomeCms;
use App\Models\EcomNewsletter;
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
         $content = EcomHomeCms::orderBy('id', 'desc')->first();
        return view('ecom.home')->with(compact('categories', 'feature_products', 'new_products', 'books', 'lockets', 'content'));
    }

    public function newsletter(Request $request)
    {
        $request->validate([
            'newsletter_name' => 'required',
            'newsletter_email' => 'required|email|unique:ecom_newsletters,email',
            'newsletter_message' => 'required',
        ]);

        if ($request->ajax()) {
            $newsletter = new EcomNewsletter();
            $newsletter->name = $request->newsletter_name;
            $newsletter->email = $request->newsletter_email;
            $newsletter->message = $request->newsletter_message;
            $newsletter->save();
            return response()->json(['message' => 'Thank you for subscribing to our newsletter', 'status' => true]);
        }
    }
}
