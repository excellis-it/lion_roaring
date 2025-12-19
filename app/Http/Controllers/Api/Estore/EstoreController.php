<?php

namespace App\Http\Controllers\Api\Estore;

use App\Http\Controllers\Controller;
use App\Models\EcomHomeCms;
use App\Models\EcomFooterCms;
use App\Models\EcomCmsPage;
use App\Models\EcomNewsletter;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

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
            $pages = EcomCmsPage::where('country_code', $countryCode)->orderBy('id', 'asc')->get();

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
     * Category Menu (nested)
     *
     * Returns categories in nested tree format for the e-store menu.
     *
     * @response 200 {
     *  "data": [
     *    {
     *      "id": 1,
     *      "name": "Books",
     *      "slug": "books",
     *      "children": [
     *         { "id": 2, "name": "Bibles", "slug": "bibles", children: [] }
     *      ]
     *    }
     *  ],
     *  "status": true
     * }
     */
    public function menuCategories(Request $request)
    {
        try {
            // Load parent categories with nested children up to 3 levels to prevent N+1
            $parents = Category::whereNull('parent_id')->where('status', 1)
                ->with(['children.children.children'])
                ->orderBy('name', 'asc')
                ->get();

            $buildNode = function ($cat) use (&$buildNode) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                    'image' => $cat->image ? Storage::url($cat->image) : null,
                    'children' => $cat->children->map(fn($c) => $buildNode($c))->values()->all(),
                ];
            };

            $tree = $parents->map(fn($c) => $buildNode($c))->values()->all();

            return response()->json(['data' => $tree, 'status' => true], 200);
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

    /**
     * Update user location by latitude & longitude (authenticated)
     *
     * @authenticated
     * @bodyParam latitude numeric required The latitude coordinate. Example: 38.8951
     * @bodyParam longitude numeric required The longitude coordinate. Example: -77.0364
     * @response 200 {
     *  "success": true,
     *  "message": "Location updated",
     *  "location": {"latitude": "38.8951","longitude": "-77.0364","address":"..."}
     * }
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $lat = $request->latitude;
        $lng = $request->longitude;

        try {
            // Call Google Geocoding API
            $apiKey = env('GOOGLE_MAPS_API_KEY') ?? 'AIzaSyAL6T_r8Jr6opHuz__8c8iUvmTU30Kdomo';
            $client = new Client();
            $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$lng}&key={$apiKey}";
            $response = $client->get($url);
            $data = json_decode($response->getBody(), true);

            if (!empty($data['results'][0]) && ($data['status'] === 'OK')) {
                $address = $data['results'][0]['formatted_address'] ?? null;
                $zip = null;
                $country = null;
                $state = null;
                foreach ($data['results'][0]['address_components'] as $component) {
                    if (in_array('postal_code', $component['types'])) {
                        $zip = $component['long_name'];
                    }
                    if (in_array('country', $component['types'])) {
                        $country = $component['long_name'];
                    }
                    if (in_array('administrative_area_level_1', $component['types'])) {
                        $state = $component['long_name'];
                    }
                }
            } else {
                $address = null;
                $zip = null;
                $country = null;
                $state = null;
            }

            $user = auth()->user();
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Unauthenticated'], 201);
            }

            $user->location_lat = $lat;
            $user->location_lng = $lng;
            $user->location_address = $address;
            $user->location_zip = $zip;
            $user->location_country = $country;
            $user->location_state = $state;
            $user->save();

            return response()->json(['status' => true, 'message' => 'Location updated', 'location' => [
                'latitude' => $lat,
                'longitude' => $lng,
                'address' => $address,
                'zip' => $zip,
                'country' => $country,
                'state' => $state,
            ]], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 201);
        }
    }
}
