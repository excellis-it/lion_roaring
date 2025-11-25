<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EcomHomeCms;
use App\Models\EcomFooterCms;
use App\Models\EcomCmsPage;
use App\Models\EcomNewsletter;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group E-Store Public APIs
 *
 */
class EstoreController extends Controller
{
    /**
     * Public Store Home
     *
     * @queryParam country_code string optional Two-letter country code to localize content. Default is 'US'. Example: US
     */
    public function storeHome(Request $request)
    {
        try {
            $countryCode = strtoupper($request->input('country_code') ?? 'US');
            $topParentCategories = Category::where('status', 1)->whereNull('parent_id')->orderBy('id', 'DESC')->get();
            $feature_products = Product::where('is_deleted', false)->where('status', 1)->where('feature_product', 1)->orderBy('id', 'DESC')->get();
            $new_products = Product::where('is_deleted', false)->where('is_new_product', 1)->where('status', 1)->orderBy('id', 'DESC')->limit(10)->get();

            $homeCms = EcomHomeCms::where('country_code', $countryCode)->orderBy('id', 'desc')->first();
            $content = $homeCms ? $homeCms : [];

            return response()->json([
                'data' => [
                    'content' => $content,
                    'topParentCategories' => $topParentCategories,
                    'feature_products' => $feature_products,
                    'new_products' => $new_products,
                ],
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status' => false
            ], 201);
        }
    }

    /**
     * Public Header — logo, pages and categories
     *
     * @queryParam country_code string optional Two-letter country code to localize content. Default is 'US'.
     */
    public function header(Request $request)
    {
        try {
            $countryCode = strtoupper($request->input('country_code') ?? 'US');
            $homeCms = EcomHomeCms::where('country_code', $countryCode)->orderBy('id', 'desc')->first();
            $header_logo = $homeCms ? $homeCms->header_logo : null;
            $categories = Category::where('status', 1)->orderBy('id', 'DESC')->get();
            $pages = EcomCmsPage::where('status', 1)->where('country_code', $countryCode)->orderBy('id', 'asc')->get();

            return response()->json([
                'data' => [
                    'header_logo' => $header_logo,
                    'categories' => $categories,
                    'pages' => $pages,
                ],
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong. Please try again later.', 'status' => false], 201);
        }
    }

    /**
     * Public Footer
     *
     * @queryParam country_code string optional Two-letter country code to localize content. Default is 'US'.
     */
    public function footer(Request $request)
    {
        try {
            $countryCode = strtoupper($request->input('country_code') ?? 'US');
            $footer = EcomFooterCms::where('country_code', $countryCode)->orderBy('id', 'desc')->first();
            if (! $footer) {
                return response()->json(['message' => 'Footer not found', 'status' => false], 201);
            }
            return response()->json(['data' => $footer, 'status' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong. Please try again later.', 'status' => false], 201);
        }
    }

    /**
     * Newsletter Subscribe (Public)
     */
    public function newsletterStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'newsletter_name' => 'nullable|string|max:255',
            'newsletter_email' => 'required|email|unique:ecom_newsletters,email',
            'newsletter_message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $validator->errors(), 'status' => false], 201);
        }

        try {
            $newsletter = new EcomNewsletter();
            $newsletter->name = $request->newsletter_name ?? '-';
            $newsletter->email = $request->newsletter_email;
            $newsletter->message = $request->newsletter_message ?? '-';
            $newsletter->save();
            return response()->json(['message' => 'Thank you for subscribing to our newsletter', 'status' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong. Please try again later.', 'status' => false], 201);
        }
    }
}
