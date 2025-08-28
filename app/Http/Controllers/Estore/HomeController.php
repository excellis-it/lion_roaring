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

class HomeController extends Controller
{
    public function eStore()
    {
        $session_id = session()->getId();
        $this->updateCartUserId();

        $nearbyWareHouseId = 1; // Default warehouse ID
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
        $feature_products = Product::whereIn('id', $wareHouseProducts)->where('status', 1)->where('feature_product', 1)->orderBy('id', 'DESC')->get();
        $new_products = Product::whereIn('id', $wareHouseProducts)->where('status', 1)->orderBy('id', 'DESC')->limit(10)->get();
        $books = Product::whereIn('id', $wareHouseProducts)->where('status', 1)->whereHas('category', function ($q) {
            $q->where('slug', 'books');
        })->orderBy('id', 'DESC')->limit(10)->get();
        $lockets = Product::whereIn('id', $wareHouseProducts)->where('status', 1)->whereHas('category', function ($q) {
            $q->where('slug', 'lockets');
        })->orderBy('id', 'DESC')->limit(10)->get();
        $content = EcomHomeCms::orderBy('id', 'desc')->first();
        $cartCount = EstoreCart::where('user_id', auth()->id())->count();
        return view('ecom.home')->with(compact('categories', 'feature_products', 'new_products', 'books', 'lockets', 'content', 'cartCount'));
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

            // Call Google Geocoding API
            $apiKey = env('GOOGLE_MAPS_API_KEY');
            $client = new Client();
            $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$lng}&key={$apiKey}";

            $response = $client->get($url);
            $data = json_decode($response->getBody(), true);

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

            return response()->json(['success' => true, 'message' => 'Location updated']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
