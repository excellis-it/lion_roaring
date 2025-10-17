<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EcomCmsPage;
use App\Models\EcomContactCms;
use App\Models\EcomFooterCms;
use App\Models\EcomHomeCms;
use App\Models\EcomNewsletter;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @authenticated
 *
 * @group Estore CMS
 *
 */
class EstoreCmsController extends Controller
{
    use ImageTrait;
    /**
     * Dashboard
     *
     * Get counts for the dashboard (CMS pages + newsletter subscribers).
     *
     * @response 200 {
     *   "data": {
     *     "pages": 12,
     *     "newsletter": 34
     *   },
     *   "status": true
     * }
     *
     * @response 201 {
     *   "message": "Something went wrong. Please try again later.",
     *   "status": false
     * }
     */

    public function dashboard()
    {
        try {
            $count['pages'] = EcomCmsPage::count() + 2; // +2 as per your logic
            $count['newsletter'] = EcomNewsletter::count();

            return response()->json([
                'data' => $count,
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            // Log::error('Dashboard fetch failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status' => false
            ], 201);
        }
    }


    /**
     * CMS LIST
     */

    public function list(Request $request)
    {
        try {
            $pages = EcomCmsPage::get();
            return response()->json([
                'data' => $pages,
                'status' => true
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status' => false
            ], 201);
        }
    }

    /**
     * Store CMS Page
     *
     * Create a new CMS page.
     *
     * @bodyParam page_name string required The internal page name. Example: "about_us"
     * @bodyParam page_title string required The page title. Example: "About Us"
     * @bodyParam page_content string required The page content. Example: "This is the about us content..."
     * @bodyParam page_banner_image file required Banner image file (jpeg, png, jpg, gif, svg).
     * @bodyParam slug string required Unique slug for the page. Example: "about-us"
     *
     * @response 201 {
     *   "data": {
     *     "id": 12,
     *     "page_name": "about_us",
     *     "page_title": "About Us",
     *     "page_content": "This is the about us content...",
     *     "page_banner_image": "https://example.com/storage/ecom_cms/banner123.jpg",
     *     "slug": "about-us",
     *     "created_at": "2025-10-17T12:00:00Z",
     *     "updated_at": "2025-10-17T12:00:00Z"
     *   },
     *   "message": "CMS page added successfully",
     *   "status": true
     * }
     *
     * @response 201 {
     *   "message": "Validation failed.",
     *   "errors": {
     *     "page_name": ["The page name field is required."],
     *     "slug": ["The slug has already been taken."]
     *   },
     *   "status": false
     * }
     *
     * @response 201 {
     *   "message": "Something went wrong. Please try again later.",
     *   "status": false
     * }
     */
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'page_name' => 'required|string',
            'page_title' => 'required|string',
            'page_content' => 'required|string',
            'page_banner_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'slug' => 'required|string|unique:ecom_cms_pages,slug',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
                'status' => false
            ], 201);
        }

        try {
            $cms = new EcomCmsPage();
            $cms->page_name = $request->page_name;
            $cms->page_title = $request->page_title;
            $cms->page_content = $request->page_content;
            $cms->slug = $request->slug;

            if ($request->hasFile('page_banner_image')) {
                $cms->page_banner_image = $this->imageUpload($request->file('page_banner_image'), 'ecom_cms', true);
            }

            $cms->save();

            return response()->json([
                'data' => $cms,
                'message' => 'CMS page added successfully',
                'status' => true
            ], 201);
        } catch (\Exception $e) {
            // Log::error('CMS page store failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status' => false
            ], 201);
        }
    }


    /**
     * Update CMS Page
     *
     * Update an existing CMS page by ID.
     *
     * @urlParam id int required The ID of the CMS page. Example: 5
     * @bodyParam page_name string required The internal page name. Example: "About Us"
     * @bodyParam page_title string required The page title. Example: "About Our Company"
     * @bodyParam page_content string required The content of the page. Example: "<p>Welcome...</p>"
     * @bodyParam slug string required Unique slug for the page. Example: "about-us"
     * @bodyParam page_banner_image file optional The banner image for the page.
     *
     * @response 200 {
     *   "data": {
     *     "id": 5,
     *     "page_name": "About Us",
     *     "page_title": "About Our Company",
     *     "page_content": "<p>Welcome...</p>",
     *     "slug": "about-us",
     *     "page_banner_image": "https://example.com/storage/ecom_cms/banner.jpg",
     *     "updated_at": "2025-10-17T12:00:00Z"
     *   },
     *   "message": "CMS page updated successfully.",
     *   "status": true
     * }
     *
     * @response 201 {
     *   "message": "Validation failed.",
     *   "errors": {
     *     "slug": ["The slug has already been taken."],
     *     "page_name": ["The page name field is required."]
     *   },
     *   "status": false
     * }
     *
     * @response 201 {
     *   "message": "CMS page not found.",
     *   "status": false
     * }
     *
     * @response 201 {
     *   "message": "Something went wrong. Please try again later.",
     *   "status": false
     * }
     */
    public function update(Request $request, $id)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'page_name' => 'required|string',
            'page_title' => 'required|string',
            'page_content' => 'required|string',
            'slug' => 'required|string|unique:ecom_cms_pages,slug,' . $id,
            'page_banner_image' => 'sometimes|file|image|mimes:jpeg,png,jpg,gif,webp', // optional, max 5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
                'status' => false
            ], 201);
        }

        try {
            $cms = EcomCmsPage::find($id);

            if (!$cms) {
                return response()->json([
                    'message' => 'CMS page not found.',
                    'status' => false
                ], 201);
            }

            $cms->page_name = $request->page_name;
            $cms->page_title = $request->page_title;
            $cms->page_content = $request->page_content;
            $cms->slug = $request->slug;

            if ($request->hasFile('page_banner_image')) {
                $cms->page_banner_image = $this->imageUpload(
                    $request->file('page_banner_image'),
                    'ecom_cms',
                    true
                );
            }

            $cms->save();

            return response()->json([
                'data' => $cms,
                'message' => 'CMS page updated successfully.',
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            // Log::error('CMS page update failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status' => false
            ], 201);
        }
    }


    /**
     * Delete CMS Page
     *
     * Delete an existing CMS page by ID.
     *
     * @bodyParam id int required The ID of the CMS page to delete. Example: 5
     *
     * @response 200 {
     *   "message": "CMS page deleted successfully.",
     *   "status": true
     * }
     *
     * @response 201 {
     *   "message": "CMS page not found.",
     *   "status": false
     * }
     *
     * @response 201 {
     *   "message": "Something went wrong. Please try again later.",
     *   "status": false
     * }
     */
    public function delete(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'id' => [
                'required',
                'exists:ecom_cms_pages,id' // ensures the ID exists in the colors table
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
                'status'  => false
            ], 201);
        }
        try {


            $cms = EcomCmsPage::find($request->id);
            if (!$cms) {
                return response()->json([
                    'message' => 'CMS page not found.',
                    'status' => false
                ], 201);
            }

            $cms->delete();

            return response()->json([
                'message' => 'CMS page deleted successfully',
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            // Log::error('CMS delete failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status' => false
            ], 201);
        }
    }


    /**
     * Get CMS Page
     *
     * Retrieve CMS content by page slug.
     *
     * @bodyParam page string required The CMS page slug. Example: "home"
     *
     * @response 200 {
     *   "data": {
     *     "id": 5,
     *     "title": "Homepage CMS",
     *     "content": "<p>Welcome to our store!</p>",
     *     "slug": "home",
     *     "created_at": "2025-10-17T10:00:00Z",
     *     "updated_at": "2025-10-17T12:00:00Z"
     *   },
     *   "status": true
     * }
     *
     * @response 201 {
     *   "message": "You do not have permission to access this page.",
     *   "status": false
     * }
     *
     * @response 201 {
     *   "message": "CMS page not found.",
     *   "status": false
     * }
     *
     * @response 201 {
     *   "message": "Something went wrong. Please try again later.",
     *   "status": false
     * }
     */
    public function cms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
                'status'  => false
            ], 201);
        }

        try {
            $page = $request->input('page');

            if ($page === 'home') {
                $cms = EcomHomeCms::orderBy('id', 'desc')->first();
            } elseif ($page === 'footer') {
                $cms = EcomFooterCms::orderBy('id', 'desc')->first();
            } else {
                $cms = EcomCmsPage::where('slug', $page)->first();
            }

            if (!$cms) {
                return response()->json([
                    'message' => 'CMS page not found.',
                    'status'  => false
                ], 201);
            }

            return response()->json([
                'data' => $cms,
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            // Log::error('CMS fetch failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status' => false
            ], 201);
        }
    }






    /**
     * Home CMS Update
     *
     * Update or create the home CMS content.
     *
     * @bodyParam id int optional ID of the CMS record to update. Example: 1
     * @bodyParam header_logo file nullable Header logo image (multipart/form-data)
     * @bodyParam banner_image file nullable Banner image
     * @bodyParam banner_image_small file nullable Banner small image
     * @bodyParam product_category_image file nullable Product category image
     * @bodyParam featured_product_image file nullable Featured product image
     * @bodyParam new_product_image file nullable New product image
     * @bodyParam new_arrival_image file nullable New arrival image
     * @bodyParam shop_now_image file nullable Shop Now section image
     * @bodyParam about_section_image file nullable About section image
     *
     * @bodyParam banner_title string nullable Banner title
     * @bodyParam banner_subtitle string nullable Banner subtitle
     * @bodyParam product_category_title string required Product category title
     * @bodyParam product_category_subtitle string required Product category subtitle
     * @bodyParam featured_product_title string required Featured product title
     * @bodyParam featured_product_subtitle string required Featured product subtitle
     * @bodyParam new_arrival_title string required New arrival title
     * @bodyParam new_arrival_subtitle string required New arrival subtitle
     * @bodyParam new_product_title string required New product title
     * @bodyParam new_product_subtitle string required New product subtitle
     * @bodyParam shop_now_title string nullable Shop Now title
     * @bodyParam shop_now_description string nullable Shop Now description
     * @bodyParam shop_now_button_text string nullable Shop Now button text
     * @bodyParam shop_now_button_link string nullable Shop Now button link
     * @bodyParam about_section_title string nullable About section main title
     * @bodyParam about_section_text_one_title string nullable About section text one title
     * @bodyParam about_section_text_one_content string nullable About section text one content
     * @bodyParam about_section_text_two_title string nullable About section text two title
     * @bodyParam about_section_text_two_content string nullable About section text two content
     * @bodyParam about_section_text_three_title string nullable About section text three title
     * @bodyParam about_section_text_three_content string nullable About section text three content
     * @bodyParam slider_data_second_title string nullable Second slider section title
     *
     * @bodyParam slider_titles.* string nullable Array of titles for first slider
     * @bodyParam slider_subtitles.* string nullable Array of subtitles for first slider
     * @bodyParam slider_links.* string nullable Array of links for first slider
     * @bodyParam slider_buttons.* string nullable Array of button texts for first slider
     * @bodyParam slider_images.* file nullable Array of images for first slider (multipart/form-data)
     *
     * @bodyParam slider_titles_second.* string nullable Array of titles for second slider
     * @bodyParam slider_subtitles_second.* string nullable Array of subtitles for second slider
     * @bodyParam slider_links_second.* string nullable Array of links for second slider
     * @bodyParam slider_buttons_second.* string nullable Array of button texts for second slider
     * @bodyParam slider_images_second.* file nullable Array of images for second slider (multipart/form-data)
     *
     * @response 200 {
     *   "data": { ,
     *   "message": "Home CMS updated successfully",
     *   "status": true
     * }
     *
     * @response 201 {
     *   "data": { ,
     *   "message": "Home CMS added successfully",
     *   "status": true
     * }
     *
     * @response 201 {
     *   "message": "Forbidden",
     *   "status": false
     * }
     *
     * @response 201 {
     *   "message": "Validation failed.",
     *   "errors": { "field": ["error message"] },
     *   "status": false
     * }
     *
     * @response 201 {
     *   "message": "Something went wrong. Please try again later.",
     *   "status": false
     * }
     */

    public function homeCmsUpdate(Request $request)
    {
        // validation rules — adjust as needed
        $rules = [
            'id' => 'nullable|exists:ecom_home_cms,id',
            'header_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'banner_image_small' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'product_category_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'featured_product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'new_product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'new_arrival_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'shop_now_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'about_section_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',

            // textual fields (nullable so API clients aren't forced to send everything)
            'banner_title' => 'nullable|string|max:255',
            'banner_subtitle' => 'nullable|string|max:201',
            'product_category_title' => 'nullable|string|max:255',
            'product_category_subtitle' => 'nullable|string|max:201',
            'featured_product_title' => 'nullable|string|max:255',
            'featured_product_subtitle' => 'nullable|string|max:201',
            'new_arrival_title' => 'nullable|string|max:255',
            'new_arrival_subtitle' => 'nullable|string|max:201',
            'new_product_title' => 'nullable|string|max:255',
            'new_product_subtitle' => 'nullable|string|max:201',
            'shop_now_title' => 'nullable|string|max:255',
            'shop_now_description' => 'nullable|string',
            'shop_now_button_text' => 'nullable|string|max:120',
            'shop_now_button_link' => 'nullable|string|max:400',
            'about_section_title' => 'nullable|string|max:255',
            'about_section_text_one_title' => 'nullable|string|max:255',
            'about_section_text_one_content' => 'nullable|string',
            'about_section_text_two_title' => 'nullable|string|max:255',
            'about_section_text_two_content' => 'nullable|string',
            'about_section_text_three_title' => 'nullable|string|max:255',
            'about_section_text_three_content' => 'nullable|string',
            'slider_data_second_title' => 'nullable|string|max:255',
            // slider array rules are permissive — validate elements if you need stronger checks
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
                'status'  => false
            ], 201);
        }

        try {
            if ($request->id) {
                $cms = EcomHomeCms::find($request->id);
                $message = 'Home CMS updated successfully';
                $statusCode = 200;
            } else {
                $cms = new EcomHomeCms();
                $message = 'Home CMS added successfully';
                $statusCode = 201;
            }

            // Header Logo
            if ($request->hasFile('header_logo')) {
                $cms->header_logo = $this->imageUpload($request->file('header_logo'), 'header_logos', true);
            }

            // Slider 1
            if ($request->has('slider_titles') && $request->has('slider_subtitles')) {
                $sliderData = [];
                $titles = is_array($request->slider_titles) ? $request->slider_titles : json_decode($request->slider_titles, true) ?? [];
                $subtitles = is_array($request->slider_subtitles) ? $request->slider_subtitles : json_decode($request->slider_subtitles, true) ?? [];
                $links = $request->slider_links ?? [];
                $buttons = $request->slider_buttons ?? [];
                $images = $request->file('slider_images') ?? [];
                $existingSliders = $cms->slider_data ? json_decode($cms->slider_data, true) : [];

                foreach ($titles as $index => $title) {
                    if (!empty($title)) {
                        $slideData = [
                            'title' => $title,
                            'subtitle' => $subtitles[$index] ?? '',
                            'link' => $links[$index] ?? '',
                            'button' => $buttons[$index] ?? '',
                            'image' => null
                        ];
                        if (isset($images[$index]) && $images[$index]) {
                            $slideData['image'] = $this->imageUpload($images[$index], 'slider_images', true);
                        } elseif (isset($existingSliders[$index]['image'])) {
                            $slideData['image'] = $existingSliders[$index]['image'];
                        }
                        $sliderData[] = $slideData;
                    }
                }
                $cms->slider_data = json_encode($sliderData);
            }

            // Featured product
            $cms->featured_product_title = $request->featured_product_title ?? $cms->featured_product_title;
            $cms->featured_product_subtitle = $request->featured_product_subtitle ?? $cms->featured_product_subtitle;

            // Slider 2
            $cms->slider_data_second_title = $request->slider_data_second_title ?? $cms->slider_data_second_title;
            if ($request->has('slider_titles_second') && $request->has('slider_subtitles_second')) {
                $sliderDataSecond = [];
                $titles = is_array($request->slider_titles_second) ? $request->slider_titles_second : json_decode($request->slider_titles_second, true) ?? [];
                $subtitles = is_array($request->slider_subtitles_second) ? $request->slider_subtitles_second : json_decode($request->slider_subtitles_second, true) ?? [];
                $links = $request->slider_links_second ?? [];
                $buttons = $request->slider_buttons_second ?? [];
                $images = $request->file('slider_images_second') ?? [];
                $existingSliders = $cms->slider_data_second ? json_decode($cms->slider_data_second, true) : [];

                foreach ($titles as $index => $title) {
                    if (!empty($title)) {
                        $slideDataSecond = [
                            'title' => $title,
                            'subtitle' => $subtitles[$index] ?? '',
                            'link' => $links[$index] ?? '',
                            'button' => $buttons[$index] ?? '',
                            'image' => null
                        ];
                        if (isset($images[$index]) && $images[$index]) {
                            $slideDataSecond['image'] = $this->imageUpload($images[$index], 'slider_images_second', true);
                        } elseif (isset($existingSliders[$index]['image'])) {
                            $slideDataSecond['image'] = $existingSliders[$index]['image'];
                        }
                        $sliderDataSecond[] = $slideDataSecond;
                    }
                }
                $cms->slider_data_second = json_encode($sliderDataSecond);
            }

            // New Product
            $cms->new_product_title = $request->new_product_title ?? $cms->new_product_title;
            $cms->new_product_subtitle = $request->new_product_subtitle ?? $cms->new_product_subtitle;

            // Shop Now
            $cms->shop_now_title = $request->shop_now_title ?? $cms->shop_now_title;
            $cms->shop_now_description = $request->shop_now_description ?? $cms->shop_now_description;
            $cms->shop_now_button_text = $request->shop_now_button_text ?? $cms->shop_now_button_text;
            $cms->shop_now_button_link = $request->shop_now_button_link ?? $cms->shop_now_button_link;
            if ($request->hasFile('shop_now_image')) {
                $cms->shop_now_image = $this->imageUpload($request->file('shop_now_image'), 'ecom_cms', true);
            }

            // About Section
            $cms->about_section_title = $request->about_section_title ?? $cms->about_section_title;
            if ($request->hasFile('about_section_image')) {
                $cms->about_section_image = $this->imageUpload($request->file('about_section_image'), 'ecom_cms', true);
            }
            $cms->about_section_text_one_title = $request->about_section_text_one_title ?? $cms->about_section_text_one_title;
            $cms->about_section_text_one_content = $request->about_section_text_one_content ?? $cms->about_section_text_one_content;
            $cms->about_section_text_two_title = $request->about_section_text_two_title ?? $cms->about_section_text_two_title;
            $cms->about_section_text_two_content = $request->about_section_text_two_content ?? $cms->about_section_text_two_content;
            $cms->about_section_text_three_title = $request->about_section_text_three_title ?? $cms->about_section_text_three_title;
            $cms->about_section_text_three_content = $request->about_section_text_three_content ?? $cms->about_section_text_three_content;

            // Other textual fields
            $cms->banner_title = $request->banner_title ?? $cms->banner_title;
            $cms->banner_subtitle = $request->banner_subtitle ?? $cms->banner_subtitle;
            $cms->product_category_title = $request->product_category_title ?? $cms->product_category_title;
            $cms->product_category_subtitle = $request->product_category_subtitle ?? $cms->product_category_subtitle;
            $cms->new_arrival_title = $request->new_arrival_title ?? $cms->new_arrival_title;
            $cms->new_arrival_subtitle = $request->new_arrival_subtitle ?? $cms->new_arrival_subtitle;

            // Image uploads for the rest
            if ($request->hasFile('banner_image')) {
                $cms->banner_image = $this->imageUpload($request->file('banner_image'), 'ecom_cms', true);
            }
            if ($request->hasFile('banner_image_small')) {
                $cms->banner_image_small = $this->imageUpload($request->file('banner_image_small'), 'ecom_cms', true);
            }
            if ($request->hasFile('product_category_image')) {
                $cms->product_category_image = $this->imageUpload($request->file('product_category_image'), 'ecom_cms', true);
            }
            if ($request->hasFile('featured_product_image')) {
                $cms->featured_product_image = $this->imageUpload($request->file('featured_product_image'), 'ecom_cms', true);
            }
            if ($request->hasFile('new_product_image')) {
                $cms->new_product_image = $this->imageUpload($request->file('new_product_image'), 'ecom_cms', true);
            }
            if ($request->hasFile('new_arrival_image')) {
                $cms->new_arrival_image = $this->imageUpload($request->file('new_arrival_image'), 'ecom_cms', true);
            }

            // Save model
            $cms->save();

            return response()->json([
                'data' => $cms,
                'message' => $message,
                'status' => true
            ], $statusCode);
        } catch (\Exception $e) {
            // Log::error('Home CMS update failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status'  => false
            ], 201);
        }
    }


    /**
     * Footer CMS Update
     *
     * Create or update the E-commerce footer CMS details.
     *
     * @bodyParam id int optional The ID of the footer CMS to update. Leave empty to create a new one. Example: 1
     * @bodyParam footer_logo file nullable The footer logo image (jpeg, png, jpg, gif, svg).
     * @bodyParam footer_title string required Footer title. Example: "My Store"
     * @bodyParam footer_newsletter_title string required Newsletter title. Example: "Subscribe"
     * @bodyParam footer_address_title string required Address title. Example: "Our Address"
     * @bodyParam footer_address string required Address. Example: "123 Street, City"
     * @bodyParam footer_phone_number string required Phone number. Example: "+1 123-456-7890"
     * @bodyParam footer_email string required Email. Example: "info@example.com"
     * @bodyParam footer_copywrite_text string required Copywrite text. Example: "© 2025 My Store"
     * @bodyParam footer_facebook_link string nullable Facebook URL. Example: "https://facebook.com/mystore"
     * @bodyParam footer_twitter_link string nullable Twitter URL. Example: "https://twitter.com/mystore"
     * @bodyParam footer_instagram_link string nullable Instagram URL. Example: "https://instagram.com/mystore"
     * @bodyParam footer_youtube_link string nullable YouTube URL. Example: "https://youtube.com/mystore"
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "footer_title": "My Store",
     *     "footer_logo": "https://example.com/storage/ecom_cms/logo.png"
     *   },
     *   "message": "Footer CMS updated successfully.",
     *   "status": true
     * }
     *
     * @response 201 {
     *   "message": "Validation failed.",
     *   "errors": {
     *     "footer_title": ["The footer title field is required."]
     *   },
     *   "status": false
     * }
     *
     * @response 201 {
     *   "message": "You do not have permission to access this resource.",
     *   "status": false
     * }
     *
     * @response 201 {
     *   "message": "Something went wrong. Please try again later.",
     *   "status": false
     * }
     */
    public function footerUpdate(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'footer_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'footer_title' => 'required|string',
            'footer_newsletter_title' => 'required|string',
            'footer_address_title' => 'required|string',
            'footer_address' => 'required|string',
            'footer_phone_number' => 'required|string',
            'footer_email' => 'required|string',
            'footer_copywrite_text' => 'required|string',
            'footer_facebook_link' => 'nullable|string',
            'footer_twitter_link' => 'nullable|string',
            'footer_instagram_link' => 'nullable|string',
            'footer_youtube_link' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
                'status' => false
            ], 201);
        }

        try {
            if ($request->id) {
                $cms = EcomFooterCms::find($request->id);
                $message = 'Footer CMS updated successfully';
            } else {
                $cms = new EcomFooterCms();
                $message = 'Footer CMS added successfully';
            }

            $cms->footer_title = $request->footer_title;
            $cms->footer_newsletter_title = $request->footer_newsletter_title;
            $cms->footer_address_title = $request->footer_address_title;
            $cms->footer_address = $request->footer_address;
            $cms->footer_phone_number = $request->footer_phone_number;
            $cms->footer_email = $request->footer_email;
            $cms->footer_copywrite_text = $request->footer_copywrite_text;
            $cms->footer_facebook_link = $request->footer_facebook_link;
            $cms->footer_twitter_link = $request->footer_twitter_link;
            $cms->footer_instagram_link = $request->footer_instagram_link;
            $cms->footer_youtube_link = $request->footer_youtube_link;

            if ($request->hasFile('footer_logo')) {
                $cms->footer_logo = $this->imageUpload($request->file('footer_logo'), 'ecom_cms', true);
            }

            $cms->save();

            return response()->json([
                'data' => $cms,
                'message' => $message,
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            // Log::error('Footer CMS update failed: '.$e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status' => false
            ], 201);
        }
    }


    /**
     * Get Contact CMS
     *
     * Fetch the latest Contact CMS record.
     *
     * @response 200 {
     *   "data": {
     *     "id": 5,
     *     "banner_title": "Contact Us",
     *     "card_one_title": "Support",
     *     "card_one_content": "...",
     *     "card_two_title": "Sales",
     *     "card_two_content": "...",
     *     "card_three_title": "Marketing",
     *     "card_three_content": "...",
     *     "form_title": "Get in Touch",
     *     "form_subtitle": "We are here to help",
     *     "call_section_title": "Call Us",
     *     "call_section_content": "...",
     *     "follow_us_title": "Follow Us",
     *     "map_iframe_src": "<iframe ...></iframe>",
     *     "banner_image": "ecom_cms/banner_image.jpg",
     *     "created_at": "2025-10-17T10:00:00Z",
     *     "updated_at": "2025-10-17T12:00:00Z"
     *   },
     *   "status": true
     * }
     *
     * @response 201 {
     *   "message": "No Contact CMS record found.",
     *   "status": false
     * }
     *
     * @response 201 {
     *   "message": "Something went wrong. Please try again later.",
     *   "status": false
     * }
     */
    public function contactCms()
    {
        try {
            $cms = EcomContactCms::orderBy('id', 'desc')->first();

            if (!$cms) {
                return response()->json([
                    'message' => 'No Contact CMS record found.',
                    'status' => false
                ], 201);
            }

            return response()->json([
                'data' => $cms,
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            // Log::error('Contact CMS fetch failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status' => false
            ], 201);
        }
    }



    /**
     * Contact CMS Update
     *
     * Create or update the Contact CMS section.
     *
     * @bodyParam id int optional The ID of the CMS record to update. Example: 1
     * @bodyParam banner_image file optional Banner image (jpeg, png, jpg, gif, svg)
     * @bodyParam banner_title string optional Banner title. Example: "Contact Us"
     * @bodyParam card_one_title string optional Card 1 title. Example: "Support"
     * @bodyParam card_one_content string optional Card 1 content.
     * @bodyParam card_two_title string optional Card 2 title.
     * @bodyParam card_two_content string optional Card 2 content.
     * @bodyParam card_three_title string optional Card 3 title.
     * @bodyParam card_three_content string optional Card 3 content.
     * @bodyParam form_title string optional Form title.
     * @bodyParam form_subtitle string optional Form subtitle.
     * @bodyParam call_section_title string optional Call section title.
     * @bodyParam call_section_content string optional Call section content.
     * @bodyParam follow_us_title string optional Follow us title.
     * @bodyParam map_iframe_src string optional Map iframe src.
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "banner_title": "Contact Us",
     *     "card_one_title": "Support",
     *     "card_one_content": "...",
     *     "banner_image": "ecom_cms/banner_image.jpg",
     *     "...": "..."
     *   },
     *   "message": "Contact CMS updated successfully",
     *   "status": true
     * }
     *
     * @response 201 {
     *   "message": "Validation failed.",
     *   "errors": {
     *     "banner_image": ["The banner image must be an image file."],
     *     "banner_title": ["The banner title may not be greater than 255 characters."]
     *   },
     *   "status": false
     * }
     *
     * @response 201 {
     *   "message": "Something went wrong. Please try again later.",
     *   "status": false
     * }
     */
    public function contactCmsUpdate(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'banner_title' => 'nullable|string|max:255',
            'card_one_title' => 'nullable|string|max:255',
            'card_one_content' => 'nullable|string',
            'card_two_title' => 'nullable|string|max:255',
            'card_two_content' => 'nullable|string',
            'card_three_title' => 'nullable|string|max:255',
            'card_three_content' => 'nullable|string',
            'form_title' => 'nullable|string|max:255',
            'form_subtitle' => 'nullable|string',
            'call_section_title' => 'nullable|string|max:255',
            'call_section_content' => 'nullable|string',
            'follow_us_title' => 'nullable|string|max:255',
            'map_iframe_src' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
                'status'  => false
            ], 201);
        }

        try {
            if ($request->id) {
                $cms = EcomContactCms::find($request->id);
                if (!$cms) {
                    return response()->json([
                        'message' => 'CMS record not found.',
                        'status' => false
                    ], 201);
                }
                $message = 'Contact CMS updated successfully';
            } else {
                $cms = new EcomContactCms();
                $message = 'Contact CMS added successfully';
            }

            foreach ($request->except('banner_image') as $key => $val) {
                $cms->$key = $val;
            }

            if ($request->hasFile('banner_image')) {
                if (method_exists($this, 'imageUpload')) {
                    $cms->banner_image = $this->imageUpload($request->file('banner_image'), 'ecom_cms', true);
                } else {
                    $path = $request->file('banner_image')->store('ecom_cms', 'public');
                    $cms->banner_image = $path;
                }
            }

            $cms->save();

            return response()->json([
                'data' => $cms,
                'message' => $message,
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            // Log::error('Contact CMS update failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'status' => false
            ], 201);
        }
    }
}
