<?php

namespace App\Http\Controllers\Elearning;

use App\Http\Controllers\Controller;
use App\Models\ElearningCategory;
use App\Models\ElearningEcomHomeCms;
use App\Models\ElearningEcomNewsletter;
use App\Models\ElearningProduct;
use Illuminate\Http\Request;
use App\Helpers\Helper;

class ElearningHomeController extends Controller
{
    public function eStore()
    {
        $categories = ElearningCategory::where('status', 1)->orderBy('id', 'DESC')->get();

        $feature_products = ElearningProduct::with('topic')->where('status', 1)->where('feature_product', 1)->orderBy('id', 'DESC')->get();

        $new_products = ElearningProduct::with('topic')->where('status', 1)->orderBy('id', 'DESC')->limit(10)->get();
        $books = ElearningProduct::where('status', 1)->whereHas('category', function ($q) {
            $q->where('slug', 'books');
        })->orderBy('id', 'DESC')->limit(10)->get();

        $lockets = ElearningProduct::where('status', 1)->whereHas('category', function ($q) {
            $q->where('slug', 'lockets');
        })->orderBy('id', 'DESC')->limit(10)->get();
        // $content = ElearningEcomHomeCms::orderBy('id', 'desc')->first();
        $content = Helper::getVisitorCmsContent('ElearningEcomHomeCms', true, false, 'id', 'desc', null);
        // return $content;
        return view('elearning.home')->with(compact('categories', 'feature_products', 'new_products', 'books', 'lockets', 'content'));
    }

    public function newsletter(Request $request)
    {
        $request->validate([
            'newsletter_name' => 'nullable|string',
            'newsletter_email' => 'required|email|unique:ecom_newsletters,email',
            'newsletter_message' => 'nullable|string',
        ]);

        if ($request->ajax()) {
            $newsletter = new ElearningEcomNewsletter();
            $newsletter->name = $request->newsletter_name ?? '';
            $newsletter->email = $request->newsletter_email;
            $newsletter->message = $request->newsletter_message ?? '';
            $newsletter->save();
            return response()->json(['message' => 'Thank you for subscribing to our newsletter', 'status' => true]);
        }
    }
}
