<?php

namespace App\Http\Controllers\Estore;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\EcomHomeCms;
use App\Models\EcomNewsletter;
use App\Models\Product;
use App\Models\EstoreCart;
use App\Models\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Helpers\Helper;
use App\Models\WareHouse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    public function eStore()
    {

        // temporary denied access
        // return redirect()->route('home')->with('error', 'E-Store is temporarily unavailable. Please try again later.');

        $session_id = session()->getId();
        $this->updateCartUserId();

        $nearbyWareHouseId = Warehouse::first()->id; // first id from warehouses
        $originLat = null;
        $originLng = null;
        $isUser = auth()->user();
        if ($isUser) { // Assuming user location is stored in user model
            $originLat = $isUser->location_lat;
            $originLng = $isUser->location_lng;
        } else {
            $originLat = session('location_lat');
            $originLng = session('location_lng');
        }
        // reuse helper to get nearest warehouse
        $nearest = Helper::getNearestWarehouse($originLat, $originLng);
        if (!empty($nearest['warehouse']->id)) {
            $nearbyWareHouseId = $nearest['warehouse']->id;
        }
        // return $getNearbywareHouse;

        $wareHouseProducts = Product::whereHas('warehouseProducts', function ($q) use ($nearbyWareHouseId) {
            $q->where('warehouse_id', $nearbyWareHouseId)
                ->where('quantity', '>', 0);
        })->pluck('id')->toArray();


        // return $wareHouseProducts;

        $categories = Category::where('status', 1)->orderBy('id', 'DESC')->get();

        $topParentCategories = Category::where('status', 1)->whereNull('parent_id')->orderBy('id', 'DESC')->get();

        // $feature_products = Product::whereIn('id', $wareHouseProducts)->where('status', 1)->where('feature_product', 1)->orderBy('id', 'DESC')->get();
        // $new_products = Product::whereIn('id', $wareHouseProducts)->where('status', 1)->orderBy('id', 'DESC')->limit(10)->get();
        $feature_products = Product::where('is_deleted', false)->where('status', 1)->where('feature_product', 1)->orderBy('id', 'DESC')->get();
        $new_products = Product::where('is_deleted', false)->where('status', 1)->orderBy('id', 'DESC')->limit(10)->get();
        $books = Product::where('is_deleted', false)->whereIn('id', $wareHouseProducts)->where('status', 1)->whereHas('category', function ($q) {
            $q->where('slug', 'books');
        })->orderBy('id', 'DESC')->limit(10)->get();
        $lockets = Product::where('is_deleted', false)->whereIn('id', $wareHouseProducts)->where('status', 1)->whereHas('category', function ($q) {
            $q->where('slug', 'lockets');
        })->orderBy('id', 'DESC')->limit(10)->get();
        $content = EcomHomeCms::orderBy('id', 'desc')->first();
        $cartCount = EstoreCart::where('user_id', auth()->id())->count();

        $homeCms = EcomHomeCms::orderBy('id', 'desc')->first();
        $content = [];
        $sliderData = [];
        $sliderDataSecond = [];


        if ($homeCms) {
            $content = [
                'banner_title' => $homeCms->banner_title,
                'banner_subtitle' => $homeCms->banner_subtitle,
                'banner_image' => $homeCms->banner_image,
                'banner_image_small' => $homeCms->banner_image_small,
                'product_category_title' => $homeCms->product_category_title,
                'product_category_subtitle' => $homeCms->product_category_subtitle,
                'featured_product_title' => $homeCms->featured_product_title,
                'featured_product_subtitle' => $homeCms->featured_product_subtitle,
                'new_arrival_title' => $homeCms->new_arrival_title,
                'new_arrival_subtitle' => $homeCms->new_arrival_subtitle,
                'new_arrival_image' => $homeCms->new_arrival_image,
                'new_product_title' => $homeCms->new_product_title,
                'new_product_subtitle' => $homeCms->new_product_subtitle,
                'slider_data_second_title' => $homeCms->slider_data_second_title,
                'slider_data_second' => $homeCms->slider_data_second,
                'about_section_title' => $homeCms->about_section_title,
                'about_section_image' => $homeCms->about_section_image,
                'about_section_text_one_title' => $homeCms->about_section_text_one_title,
                'about_section_text_one_content' => $homeCms->about_section_text_one_content,
                'about_section_text_two_title' => $homeCms->about_section_text_two_title,
                'about_section_text_two_content' => $homeCms->about_section_text_two_content,
                'about_section_text_three_title' => $homeCms->about_section_text_three_title,
                'about_section_text_three_content' => $homeCms->about_section_text_three_content,
            ];

            // Decode slider data
            if ($homeCms->slider_data) {
                $sliderData = json_decode($homeCms->slider_data, true);
            }

            // Decode second slider data
            if ($homeCms->slider_data_second) {
                $sliderDataSecond = json_decode($homeCms->slider_data_second, true);
            }
        }

        return view('ecom.home')->with(compact('categories', 'topParentCategories', 'feature_products', 'new_products', 'books', 'lockets', 'content', 'cartCount', 'sliderData', 'sliderDataSecond'));
    }

    public function newsletter(Request $request)
    {
        $request->validate([
            'newsletter_name' => 'nullable|string|max:255',
            'newsletter_email' => 'required|email|unique:ecom_newsletters,email',
            'newsletter_message' => 'nullable|string|max:1000',
        ]);

        if ($request->ajax()) {
            $newsletter = new EcomNewsletter();
            $newsletter->name = $request->newsletter_name ?? '-';
            $newsletter->email = $request->newsletter_email;
            $newsletter->message = $request->newsletter_message ?? '-';
            $newsletter->save();
            return response()->json(['message' => 'Thank you for subscribing to our newsletter', 'status' => true]);
        }
    }

    // Function to update user_id in carts if user is logged in
    public function updateCartUserId()
    {
        if (auth()->check()) {
            $userId = auth()->id();
            EstoreCart::where('session_id', session()->getId())
                ->update(['user_id' => $userId]);
        }
    }

    // updateLocation if user auth then in user table else in session
    public function updateLocation(Request $request)
    {
        try {


            $lat = $request->latitude;
            $lng = $request->longitude;

            try {
                // Call Google Geocoding API
                $apiKey = env('GOOGLE_MAPS_API_KEY') ?? 'AIzaSyAL6T_r8Jr6opHuz__8c8iUvmTU30Kdomo';
                $client = new Client();
                $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$lng}&key={$apiKey}";

                $response = $client->get($url);
                //  return $response;
                $data = json_decode($response->getBody(), true);

                // if got any error from api then return
                if ($data['status'] !== 'OK') {
                    return response()->json(['success' => false, 'message' => $response], 500);
                }
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error calling Geocoding API: ' . $e->getMessage()], 500);
            }

            $address = null;
            $zip = null;
            $country = null;
            $state = null;

            if (!empty($data['results'][0])) {
                $address = $data['results'][0]['formatted_address'];

                foreach ($data['results'][0]['address_components'] as $component) {
                    if (in_array("postal_code", $component['types'])) {
                        $zip = $component['long_name'];
                    }
                    if (in_array("country", $component['types'])) {
                        $country = $component['long_name'];
                    }
                    if (in_array("administrative_area_level_1", $component['types'])) {
                        $state = $component['long_name'];
                    }
                }
            }

            $user = auth()->user();

            if ($user) {

                // Save to DB
                $user->location_lat = $lat;
                $user->location_lng = $lng;
                $user->location_address = $address;
                $user->location_zip = $zip;
                $user->location_country = $country;
                $user->location_state = $state;
                $user->save();
            } else {
                // If user is not authenticated, save location in session
                session()->put('location_lat', $lat);
                session()->put('location_lng', $lng);
                session()->put('location_address', $address);
                session()->put('location_zip', $zip);
                session()->put('location_country', $country);
                session()->put('location_state', $state);
            }

            return response()->json(['success' => true, 'message' => 'Location updated', 'location' => [
                'latitude' => $lat,
                'longitude' => $lng,
                'address' => $address,
                'zip' => $zip,
                'country' => $country,
                'state' => $state,
            ]]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Check if user already exists
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return response()->json(['success' => false, 'message' => 'User already exists']);
        }

        // generated user_name with names
        $userName = strtolower($request->first_name . '.' . $request->last_name) . uniqid();
        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = bcrypt($request->password);
        $user->user_name = $userName;
        $user->email_verified_at = now();
        $user->status = 1;
        $user->is_accept = 1;
        $user->save();

        $user->assignRole('ESTORE_USER');

        return response()->json(['success' => true, 'message' => 'Registration successful', 'email' => $user->email]);
    }

    // contact us page
    public function contactUs()
    {
        $contactCms = \App\Models\EcomContactCms::orderBy('id', 'desc')->first();
        return view('ecom.contact', compact('contactCms'));
    }

    // // profile and change password page
    public function profile()
    {
        $user = auth()->user();
        return view('ecom.profile', compact('user'));
    }
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully');
    }
    public function changePassword()
    {
        return view('ecom.change-password');
    }
}
