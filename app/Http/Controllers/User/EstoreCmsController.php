<?php

namespace App\Http\Controllers\User;

use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Models\EcomCmsPage;
use App\Models\EcomFooterCms;
use App\Models\EcomHomeCms;
use App\Models\EcomNewsletter;
use App\Models\MemberPrivacyPolicy;
use App\Models\EstoreOrder;
use App\Models\EstoreOrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrdersReportExport;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\EstorePayment;
use App\Models\EstoreRefund;
use App\Models\OrderEmailTemplate;
use App\Models\OrderStatus;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Climate\Order;
use Stripe\Refund;
use Stripe\Stripe;


class EstoreCmsController extends Controller
{
    use ImageTrait;
    public function memberPrivacyPolicy()
    {
        $policy = MemberPrivacyPolicy::orderBy('id', 'desc')->first();
        return view('user.store-cms.member_privacy_policy')->with('policy', $policy);
    }

    public function page($name, $permission)
    {
        $permission = $permission;
        if (auth()->user()->can($permission)) {
            $name = $name;
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
        return view('user.cms')->with(compact('name', 'permission'));
    }

    public function dashboard()
    {
        if (auth()->user()->can('Manage Estore CMS')) {
            $count['pages'] = EcomCmsPage::count() + 2;
            $count['newsletter'] = EcomNewsletter::count();
            return view('user.store-cms.dashboard')->with('count', $count);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function list()
    {
        if (auth()->user()->can('Manage Estore CMS')) {
            $pages = EcomCmsPage::get();
            return view('user.store-cms.list')->with('pages', $pages);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function cms($page, Request $request)
    {

        if (auth()->user()->can('Manage Estore CMS')) {
            if ($page == 'home') {
                $cms = EcomHomeCms::orderBy('id', 'desc')->first();
                return view('user.store-cms.home_cms')->with('cms', $cms);
            } elseif ($page == 'footer') {
                $cms = EcomFooterCms::orderBy('id', 'desc')->first();

                // return $cms;
                return view('user.store-cms.footer_cms')->with('cms', $cms);
            } else {
                $cms = EcomCmsPage::where('slug', $page)->first();
                return view('user.store-cms.cms')->with('cms', $cms);
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function homeCmsUpdate(Request $request)
    {
        if (auth()->user()->can('Edit Estore CMS')) {
            // return $request->all();
            // $request->validate([
            //     'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            //     'banner_image_small' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            //     'product_category_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            //     'featured_product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            //     'new_product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            //     'new_arrival_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            //     'banner_title' => 'nullable|string',
            //     'banner_subtitle' => 'nullable|string',
            //     'product_category_title' => 'required|string',
            //     'product_category_subtitle' => 'required|string',
            //     'featured_product_title' => 'required|string',
            //     'featured_product_subtitle' => 'required|string',
            //     'new_arrival_title' => 'required|string',
            //     'new_arrival_subtitle' => 'required|string',
            //     'new_product_title' => 'required|string',
            //     'new_product_subtitle' => 'required|string',
            //     'slider_titles.*' => 'nullable|string|max:255',
            //     'slider_subtitles.*' => 'nullable|string|max:500',
            //     'slider_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // ]);

            if ($request->id) {
                $cms = EcomHomeCms::find($request->id);
                $message = 'Home CMS updated successfully';
            } else {
                $cms = new EcomHomeCms();
                $message = 'Home CMS added successfully';
            }

            /// Header Logo

            if ($request->hasFile('header_logo')) {
                $cms->header_logo = $this->imageUpload($request->file('header_logo'), 'header_logos');
            }

            ///////// section 1: Top Banner Slider Management //////////

            if ($request->has('slider_titles') && $request->has('slider_subtitles')) {
                $sliderData = [];
                $titles = $request->slider_titles;
                $subtitles = $request->slider_subtitles;
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
                        // Handle image upload
                        if (isset($images[$index])) {
                            $slideData['image'] = $this->imageUpload($images[$index], 'slider_images', true);
                        } elseif (isset($existingSliders[$index]['image'])) {
                            // Keep existing image if no new image uploaded
                            $slideData['image'] = $existingSliders[$index]['image'];
                        }
                        $sliderData[] = $slideData;
                    }
                }
                $cms->slider_data = json_encode($sliderData);
            }

            ///////// section 2: Featured Product //////////
            $cms->featured_product_title = $request->featured_product_title ?? '';
            $cms->featured_product_subtitle = $request->featured_product_subtitle ?? '';



            ///////// section 3: Second Banner Slider Management //////////
            $cms->slider_data_second_title = $request->slider_data_second_title ?? '';

            if ($request->has('slider_titles_second') && $request->has('slider_subtitles_second')) {
                $sliderDataSecond = [];
                $titles = $request->slider_titles_second;
                $subtitles = $request->slider_subtitles_second;
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
                        // Handle image upload
                        if (isset($images[$index])) {
                            $slideDataSecond['image'] = $this->imageUpload($images[$index], 'slider_images', true);
                        } elseif (isset($existingSliders[$index]['image'])) {
                            // Keep existing image if no new image uploaded
                            $slideDataSecond['image'] = $existingSliders[$index]['image'];
                        }
                        $sliderDataSecond[] = $slideDataSecond;
                    }
                }
                $cms->slider_data_second = json_encode($sliderDataSecond);
            }

            ///////// section 4: New Product //////////
            $cms->new_product_title = $request->new_product_title;
            $cms->new_product_subtitle = $request->new_product_subtitle;

            ///////// section 5: Shop Now Section Management //////////
            $cms->shop_now_title = $request->shop_now_title;
            $cms->shop_now_description = $request->shop_now_description;
            $cms->shop_now_button_text = $request->shop_now_button_text;
            $cms->shop_now_button_link = $request->shop_now_button_link;
            $cms->shop_now_image = $request->hasFile('shop_now_image') ? $this->imageUpload($request->file('shop_now_image'), 'ecom_cms', true) : $cms->shop_now_image;

            ///////// section 6: About Section Management //////////
            $cms->about_section_title = $request->about_section_title;
            $cms->about_section_image = $request->hasFile('about_section_image') ? $this->imageUpload($request->file('about_section_image'), 'ecom_cms', true) : $cms->about_section_image;
            $cms->about_section_text_one_title = $request->about_section_text_one_title;
            $cms->about_section_text_one_content = $request->about_section_text_one_content;
            $cms->about_section_text_two_title = $request->about_section_text_two_title;
            $cms->about_section_text_two_content = $request->about_section_text_two_content;
            $cms->about_section_text_three_title = $request->about_section_text_three_title;
            $cms->about_section_text_three_content = $request->about_section_text_three_content;

            ///////// others

            $cms->banner_title = $request->banner_title ?? '';
            $cms->banner_subtitle = $request->banner_subtitle ?? '';
            $cms->product_category_title = $request->product_category_title;
            $cms->product_category_subtitle = $request->product_category_subtitle;
            $cms->new_arrival_title = $request->new_arrival_title;
            $cms->new_arrival_subtitle = $request->new_arrival_subtitle;


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

            $cms->save();
            return redirect()->back()->with('message', $message);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }




    public function footerUpdate(Request $request)
    {
        if (auth()->user()->can('Edit Estore CMS')) {
            $request->validate([
                'footer_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
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
                $cms->footer_logo = $this->imageUpload($request->file('footer_logo'), 'ecom_cms');
            }

            $cms->save();


            return redirect()->back()->with('message', $message);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function create()
    {
        if (auth()->user()->can('Create Estore CMS')) {
            return view('user.store-cms.create');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }


    public function store(Request $request)
    {
        if (auth()->user()->can('Create Estore CMS')) {

            $request->validate([
                'page_name' => 'required|string',
                'page_title' => 'required|string',
                'page_content' => 'required|string',
                'page_banner_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
                'slug' => 'required|string|unique:ecom_cms_pages,slug',
            ]);

            $cms = new EcomCmsPage();
            $cms->page_name = $request->page_name;
            $cms->page_title = $request->page_title;
            $cms->page_content = $request->page_content;
            $cms->slug = $request->slug;
            if ($request->hasFile('page_banner_image')) {
                $cms->page_banner_image = $this->imageUpload($request->file('page_banner_image'), 'ecom_cms', true);
            }

            $cms->save();
            return redirect()->route('user.store-cms.list')->with('message', 'CMS page added successfully');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }



    public function update(Request $request, $id)
    {
        // dd($id);
        if (auth()->user()->can('Edit Estore CMS')) {

            $request->validate([
                'page_name' => 'required|string',
                'page_title' => 'required|string',
                'page_content' => 'required|string',
                'slug' => 'required|string|unique:ecom_cms_pages,slug,' . $id,
            ]);

            $cms = EcomCmsPage::find($id);
            $cms->page_name = $request->page_name;
            $cms->page_title = $request->page_title;
            $cms->page_content = $request->page_content;
            $cms->slug = $request->slug;
            if ($request->hasFile('page_banner_image')) {
                $cms->page_banner_image = $this->imageUpload($request->file('page_banner_image'), 'ecom_cms', true);
            }

            $cms->save();
            return redirect()->route('user.store-cms.list')->with('message', 'CMS page updated successfully');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }


    public function delete($id)
    {
        if (auth()->user()->can('Delete Estore CMS')) {
            $cms = EcomCmsPage::find($id);
            $cms->delete();
            return redirect()->back()->with('message', 'CMS page deleted successfully');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function cmsPage($page_id = null)
    {
        $page_id = $page_id ?? 1;
        $cms = EcomCmsPage::findOrfail($page_id);
        return view('ecom.cms')->with(compact('cms'));
    }

    // Orders List
    public function ordersList()
    {
        if (!auth()->user()->can('Manage Estore Orders') && !auth()->user()->isWarehouseAdmin()) {
            abort(403, 'You do not have permission to access this page.');
        }
        $order_status = OrderStatus::orderBy('sort_order', 'asc')->get();
        return view('user.estore-orders.list')->with(compact('order_status'));
    }

    // Fetch Orders Data for DataTable
    public function fetchOrdersData(Request $request)
    {
        if ($request->ajax()) {
            if (Auth::user()->hasRole('SUPER ADMIN')) {
                $orders = EstoreOrder::with(['user', 'orderItems'])
                    ->orderBy('created_at', 'desc');
            } else {
                $wareHouseIds = Auth::user()->warehouses->pluck('id')->toArray();
                $orders = EstoreOrder::with(['user', 'orderItems'])
                    ->whereIn('warehouse_id', $wareHouseIds)
                    ->orderBy('created_at', 'desc');
            }

            // Apply filters
            if ($request->has('status') && $request->status != '') {
                $orders->where('status', $request->status);
            }

            if ($request->has('payment_status') && $request->payment_status != '') {
                $orders->where('payment_status', $request->payment_status);
            }

            if ($request->has('date_from') && $request->date_from != '') {
                $orders->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to != '') {
                $orders->whereDate('created_at', '<=', $request->date_to);
            }

            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $orders->where(function ($query) use ($search) {
                    $query->where('order_number', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                });
            }

            $orders = $orders->get();

            return view('user.estore-orders.table', compact('orders'))->render();
        }
    }

    // Order Details
    public function orderDetails($orderId)
    {
        if (!auth()->user()->can('View Estore Orders') && !auth()->user()->isWarehouseAdmin()) {
            abort(403, 'You do not have permission to access this page.');
        }
        $order = EstoreOrder::with(['user', 'orderItems.product', 'payments'])
            ->findOrFail($orderId);

        $order_status = OrderStatus::orderBy('sort_order', 'asc')->get();

        // find the current status id on the order
        $currentStatusId = $order->status; // integer id (assumption)

        // Optional: handle cancelled specially — if you want timeline to be [first, cancelled]
        $cancelSlug = 'cancelled';
        $cancelStatus = $order_status->firstWhere('slug', $cancelSlug);

        if ($currentStatusId && $cancelStatus && $currentStatusId == $cancelStatus->id) {
            // timeline = first (ordered) -> cancelled
            $first = $order_status->first();
            $timelineStatuses = collect();
            if ($first) $timelineStatuses->push($first);
            $timelineStatuses->push($cancelStatus);
        } else {
            // Normal timeline: full progression
            $timelineStatuses = $order_status;
        }

        // Calculate index of current status in timeline
        $statusIndex = $timelineStatuses->search(function ($s) use ($currentStatusId) {
            return $s->id == $currentStatusId;
        });

        // If not found (custom status etc.), append it to timeline for display
        if ($statusIndex === false && $currentStatusId) {
            $currentStatusModel = OrderStatus::find($currentStatusId);
            if ($currentStatusModel) {
                $timelineStatuses = $timelineStatuses->push($currentStatusModel);
                $statusIndex = $timelineStatuses->count() - 1;
            } else {
                $statusIndex = -1;
            }
        }

        return view('user.estore-orders.details', compact('order', 'order_status', 'timelineStatuses', 'statusIndex'));
    }

    // Update Order Status
    public function updateOrderStatus(Request $request)
    {
        // Check permissions
        if (!auth()->user()->can('Edit Estore Orders') && !auth()->user()->isWarehouseAdmin()) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to access this page.'
                ]);
            }
            abort(403, 'You do not have permission to access this page.');
        }

        // Validation
        $request->validate(
            [
                'order_id' => 'required|exists:estore_orders,id',
                'status' => 'required|exists:order_statuses,id',
                'payment_status' => 'nullable|in:pending,paid,failed,refunded',
                'notes' => 'nullable|string|max:1000',
                // Only required if status is NOT 5 (e.g., cancelled)
                'expected_delivery_date' => 'nullable|date|after_or_equal:today|required_unless:status,5',
            ],
            [
                'expected_delivery_date.after_or_equal' => 'The expected delivery date must be today or a future date.',
                'expected_delivery_date.required_unless' => 'Please provide an expected delivery date unless the order is cancelled.',
            ]
        );

        try {
            // Find order
            $order = EstoreOrder::findOrFail($request->order_id);

            // Update order fields
            $order->status = $request->status;
            $order->expected_delivery_date = $request->expected_delivery_date;

            if ($request->has('payment_status') && $request->payment_status) {
                $order->payment_status = $request->payment_status;
            }

            if ($request->has('notes') && $request->notes) {
                $order->notes = $request->notes;
            }

            $order->save();

            // Send email if template exists for this status
            $template = OrderEmailTemplate::where('order_status_id', $request->status)
                ->where('is_active', 1)
                ->first();

            if ($template) {
                // Build order list table HTML
                $orderList = view('user.emails.order_list_table', ['order' => $order])->render();
                $orderDetailsUrl = route('e-store.order-details', $order->id);
                $orderDetailsUrlButton = '<a href="' . $orderDetailsUrl . '" style="
                    display: inline-block;
                    padding: 10px 20px;
                    font-size: 16px;
                    color: #ffffff;
                    background-color: #643271;
                    text-decoration: none;
                    border-radius: 5px;
                ">View Order Details</a>';

                $body = str_replace(
                    ['{customer_name}', '{customer_email}', '{order_list}', '{order_id}', '{arriving_date}', '{total_order_value}', '{order_details_url_button}'],
                    [
                        $order->first_name ?? '' . ' ' . $order->last_name ?? '',
                        $order->email ?? '',
                        $orderList,
                        $order->order_number ?? '',
                        $order->expected_delivery_date ? Carbon::parse($order->expected_delivery_date)->format('M d, Y') : '',
                        number_format($order->total_amount ?? 0, 2),
                        $orderDetailsUrlButton
                    ],
                    $template->body
                );

                try {
                    // Send email
                    Mail::to($order->email)
                        ->send(new OrderStatusUpdatedMail($order, $body));
                } catch (\Throwable $th) {
                    Log::error('Failed to send order status email: ' . $th->getMessage());
                }
            }

            // Return response
            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Order status updated successfully'
                ]);
            }

            return redirect()->back()->with('success', 'Order status updated successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to update order status: ' . $e->getMessage()
                ]);
            }

            return redirect()->back()->with('error', 'Failed to update order status');
        }
    }

    // Delete Order
    public function deleteOrder($orderId)
    {
        if (!auth()->user()->can('Delete Estore Orders')) {
            abort(403, 'You do not have permission to access this page.');
        }
        try {
            $order = EstoreOrder::findOrFail($orderId);

            // Delete order items first
            $order->orderItems()->delete();

            // Delete payments
            $order->payments()->delete();

            // Delete order
            $order->delete();

            return response()->json([
                'status' => true,
                'message' => 'Order deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete order: ' . $e->getMessage()
            ]);
        }
    }

    // Export Orders
    // public function exportOrders(Request $request)
    // {
    //     $orders = EstoreOrder::with(['user', 'orderItems'])
    //         ->orderBy('created_at', 'desc');

    //     // Apply same filters as in fetchOrdersData
    //     if ($request->has('status') && $request->status != '') {
    //         $orders->where('status', $request->status);
    //     }

    //     if ($request->has('payment_status') && $request->payment_status != '') {
    //         $orders->where('payment_status', $request->payment_status);
    //     }

    //     if ($request->has('date_from') && $request->date_from != '') {
    //         $orders->whereDate('created_at', '>=', $request->date_from);
    //     }

    //     if ($request->has('date_to') && $request->date_to != '') {
    //         $orders->whereDate('created_at', '<=', $request->date_to);
    //     }

    //     $orders = $orders->get();

    //     $filename = 'orders_' . date('Y-m-d_H-i-s') . '.csv';

    //     $headers = [
    //         'Content-Type' => 'text/csv',
    //         'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    //     ];

    //     $callback = function () use ($orders) {
    //         $file = fopen('php://output', 'w');

    //         // CSV headers
    //         fputcsv($file, [
    //             'Order Number',
    //             'Customer Name',
    //             'Email',
    //             'Phone',
    //             'Total Amount',
    //             'Status',
    //             'Payment Status',
    //             'Order Date',
    //             'Items Count'
    //         ]);

    //         // CSV data
    //         foreach ($orders as $order) {
    //             fputcsv($file, [
    //                 $order->order_number,
    //                 $order->full_name,
    //                 $order->email,
    //                 $order->phone,
    //                 '$' . number_format($order->total_amount, 2),
    //                 ucfirst($order->status),
    //                 ucfirst($order->payment_status),
    //                 $order->created_at->format('Y-m-d H:i:s'),
    //                 $order->orderItems->count()
    //             ]);
    //         }

    //         fclose($file);
    //     };

    //     return response()->stream($callback, 200, $headers);
    // }

    public function exportOrders(Request $request)
    {
        $orderIds = $request->order_ids ?? [];
        $status = $request->status;
        $payment_status = $request->payment_status;
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $search = $request->search;

        $query = EstoreOrder::with(['user', 'orderItems']);

        if ($orderIds) {
            $query->whereIn('id', $orderIds);
        }

        if ($status) $query->where('status', $status);
        if ($payment_status) $query->where('payment_status', $payment_status);
        if ($date_from) $query->whereDate('created_at', '>=', $date_from);
        if ($date_to) $query->whereDate('created_at', '<=', $date_to);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%")
                    ->orWhereRaw("CONCAT(first_name,' ',last_name) like ?", ["%$search%"]);
            });
        }

        $orders = $query->get();

        return Excel::download(new OrdersExport($orders), 'orders_export.xlsx');
    }


    // -----------------------------------------------------------------
    // Product Reviews Management
    // -----------------------------------------------------------------

    public function productReviews(Product $product, Request $request)
    {
        if (!auth()->user()->can('Edit Estore Products')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $statusFilter = $request->get('status', '');
        $searchTerm = trim($request->get('search', ''));

        $reviewsQuery = Review::with(['user'])
            ->where('product_id', $product->id)
            ->orderByDesc('created_at');

        if ($statusFilter !== '') {
            $reviewsQuery->where('status', (int) $statusFilter);
        }

        if (!empty($searchTerm)) {
            $reviewsQuery->where(function ($query) use ($searchTerm) {
                $query->where('review', 'like', "%{$searchTerm}%")
                    ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                        $userQuery->where('full_name', 'like', "%{$searchTerm}%")
                            ->orWhere('email', 'like', "%{$searchTerm}%");
                    });
            });
        }

        $reviews = $reviewsQuery->paginate(15)->withQueryString();

        return view('user.product.reviews', [
            'product' => $product,
            'reviews' => $reviews,
            'statusFilter' => $statusFilter,
            'searchTerm' => $searchTerm,
            'statusOptions' => Review::statusOptions(),
            'statusBadgeClasses' => [
                Review::STATUS_PENDING => 'bg-warning text-dark',
                Review::STATUS_APPROVED => 'bg-success',
            ],
        ]);
    }

    public function approveProductReview(Product $product, Review $review, Request $request)
    {
        if (!auth()->user()->can('Edit Estore Products')) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to perform this action.'
                ], 403);
            }
            abort(403, 'You do not have permission to access this page.');
        }

        if ($review->product_id !== $product->id) {
            abort(404);
        }

        $review->status = Review::STATUS_APPROVED;
        $review->save();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Review approved successfully.',
                'review' => $review->fresh(['user'])
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Review approved successfully.');
    }

    public function deleteProductReview(Product $product, Review $review, Request $request)
    {
        if (!auth()->user()->can('Delete Estore Products')) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to perform this action.'
                ], 403);
            }
            abort(403, 'You do not have permission to access this page.');
        }

        if ($review->product_id !== $product->id) {
            abort(404);
        }

        $review->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Review deleted successfully.'
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Review deleted successfully.');
    }

    // Reports Dashboard
    public function reportsIndex()
    {
        return view('user.estore-orders.reports');
    }

    // Fetch Report Data
    public function fetchReportData(Request $request)
    {
        try {
            $reportType = $request->report_type;
            $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
            $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

            $query = EstoreOrder::query()
                ->with(['orderItems', 'user'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', '!=', 'cancelled');

            // Apply warehouse filter for non-admin users
            if (!Auth::user()->hasRole('SUPER ADMIN')) {
                $wareHouseIds = Auth::user()->warehouses->pluck('id')->toArray();
                $query->whereIn('warehouse_id', $wareHouseIds);
            }

            $data = [];

            switch ($reportType) {
                case 'product':
                    $data = $this->generateProductReport($query);
                    break;
                case 'location':
                    $data = $this->generateLocationReport($query);
                    break;
                case 'monthly':
                    $data = $this->generateMonthlyReport($startDate, $endDate);
                    break;
                case 'yearly':
                    $data = $this->generateYearlyReport($startDate, $endDate);
                    break;
                default:
                    return response()->json(['error' => 'Invalid report type'], 400);
            }

            return response()->json([
                'data' => $data,
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    // Product Report - Group by product with quantities and revenue
    private function generateProductReport($query)
    {
        $orders = $query->get();
        $productData = [];

        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                $productId = $item->product_id;
                $productName = $item->product_name;

                if (!isset($productData[$productId])) {
                    $productData[$productId] = [
                        'id' => $productId,
                        'name' => $productName,
                        'quantity' => 0,
                        'revenue' => 0,
                        'orders_count' => 0
                    ];
                }

                $productData[$productId]['quantity'] += $item->quantity;
                $productData[$productId]['revenue'] += $item->total;
                $productData[$productId]['orders_count'] += 1;
            }
        }

        // Sort by revenue (highest first)
        usort($productData, function ($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });

        return [
            'items' => array_values($productData),
            'total_revenue' => array_sum(array_column($productData, 'revenue')),
            'total_quantity' => array_sum(array_column($productData, 'quantity')),
            'total_orders' => count($orders)
        ];
    }

    // Location Report - Group by state/country with orders and revenue
    private function generateLocationReport($query)
    {
        $orders = $query->get();
        $locationData = [];

        foreach ($orders as $order) {
            $state = $order->state;
            $country = $order->country;
            $key = "$state, $country";

            if (!isset($locationData[$key])) {
                $locationData[$key] = [
                    'location' => $key,
                    'orders_count' => 0,
                    'revenue' => 0,
                    'customers' => 0
                ];
            }

            $locationData[$key]['orders_count'] += 1;
            $locationData[$key]['revenue'] += $order->subtotal;

            // Track unique customers
            $locationData[$key]['customers'] = isset($locationData[$key]['customer_ids'])
                ? count(array_unique($locationData[$key]['customer_ids']))
                : 0;

            if ($order->user_id && !isset($locationData[$key]['customer_ids'][$order->user_id])) {
                $locationData[$key]['customer_ids'][$order->user_id] = true;
                $locationData[$key]['customers'] += 1;
            }
        }

        // Sort by revenue
        usort($locationData, function ($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });

        return [
            'items' => array_values($locationData),
            'total_revenue' => array_sum(array_column($locationData, 'revenue')),
            'total_orders' => count($orders),
            'total_locations' => count($locationData)
        ];
    }

    // Monthly Report - Group by month
    private function generateMonthlyReport($startDate, $endDate)
    {
        $query = EstoreOrder::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if (!Auth::user()->hasRole('SUPER ADMIN')) {
            $wareHouseIds = Auth::user()->warehouses->pluck('id')->toArray();
            $query->whereIn('warehouse_id', $wareHouseIds);
        }

        $monthlyData = $query->select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as orders_count'),
            DB::raw('SUM(subtotal) as revenue'),
            DB::raw('COUNT(DISTINCT user_id) as customers_count')
        )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $formattedData = [];
        $totalRevenue = 0;
        $totalOrders = 0;

        foreach ($monthlyData as $data) {
            $monthName = Carbon::createFromDate($data->year, $data->month, 1)->format('F Y');
            $formattedData[] = [
                'period' => $monthName,
                'orders_count' => $data->orders_count,
                'revenue' => $data->revenue,
                'customers_count' => $data->customers_count
            ];

            $totalRevenue += $data->revenue;
            $totalOrders += $data->orders_count;
        }

        return [
            'items' => $formattedData,
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'periods_count' => count($formattedData)
        ];
    }

    // Yearly Report - Group by year
    private function generateYearlyReport($startDate, $endDate)
    {
        $query = EstoreOrder::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if (!Auth::user()->hasRole('SUPER ADMIN')) {
            $wareHouseIds = Auth::user()->warehouses->pluck('id')->toArray();
            $query->whereIn('warehouse_id', $wareHouseIds);
        }

        $yearlyData = $query->select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as orders_count'),
            DB::raw('SUM(subtotal) as revenue'),
            DB::raw('COUNT(DISTINCT user_id) as customers_count')
        )
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $formattedData = [];
        $totalRevenue = 0;
        $totalOrders = 0;

        foreach ($yearlyData as $data) {
            $formattedData[] = [
                'period' => $data->year,
                'orders_count' => $data->orders_count,
                'revenue' => $data->revenue,
                'customers_count' => $data->customers_count
            ];

            $totalRevenue += $data->revenue;
            $totalOrders += $data->orders_count;
        }

        return [
            'items' => $formattedData,
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'periods_count' => count($formattedData)
        ];
    }

    // Export Reports to Excel
    public function exportReport(Request $request)
    {
        $reportType = $request->report_type;
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $query = EstoreOrder::query()
            ->with(['orderItems', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled');

        if (!Auth::user()->hasRole('SUPER ADMIN')) {
            $wareHouseIds = Auth::user()->warehouses->pluck('id')->toArray();
            $query->whereIn('warehouse_id', $wareHouseIds);
        }

        $data = [];
        $title = 'Orders Report';

        switch ($reportType) {
            case 'product':
                $data = $this->generateProductReport($query);
                $title = 'Product Sales Report';
                break;
            case 'location':
                $data = $this->generateLocationReport($query);
                $title = 'Geographic Sales Report';
                break;
            case 'monthly':
                $data = $this->generateMonthlyReport($startDate, $endDate);
                $title = 'Monthly Sales Report';
                break;
            case 'yearly':
                $data = $this->generateYearlyReport($startDate, $endDate);
                $title = 'Yearly Sales Report';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid report type');
        }

        $filename = str_replace(' ', '_', strtolower($title)) . '_' . date('Y-m-d') . '.xlsx';

        return Excel::download(new OrdersReportExport($data, $reportType, $title), $filename);
    }


    public function downloadInvoice(EstoreOrder $order)
    {
        // Ensure only delivered & paid orders can generate invoice
        if ($order->status != 4 || $order->payment_status !== 'paid') {
            abort(403, 'Invoice not available.');
        }

        // Prepare header logo as a base64 data URI so DomPDF doesn't try to fetch remote URLs (prevents timeouts)
        $cms = EcomHomeCms::first();
        $header_logo_full_url = null;

        try {
            if ($cms && $cms->header_logo && Storage::disk('public')->exists($cms->header_logo)) {
                $content = Storage::disk('public')->get($cms->header_logo);
                $mime = Storage::disk('public')->mimeType($cms->header_logo) ?? 'image/png';
                $header_logo_full_url = 'data:' . $mime . ';base64,' . base64_encode($content);
            } else {
                $defaultPath = public_path('ecom_assets/images/estore_logo.png');
                if (file_exists($defaultPath)) {
                    $content = file_get_contents($defaultPath);
                    $mime = mime_content_type($defaultPath) ?: 'image/png';
                    $header_logo_full_url = 'data:' . $mime . ';base64,' . base64_encode($content);
                }
            }
        } catch (\Exception $e) {
            // fallback to null and let the view handle absence of logo
            $header_logo_full_url = null;
        }

        $pdf = PDF::loadView('user.estore-orders.invoice', compact('order', 'header_logo_full_url'));

        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }

    public function refund(EstoreOrder $order)
    {
        try {
            // Set Stripe secret key
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $estoreRefund =  EstoreRefund::where('order_id', $order->id)->first();
            $payment = EstorePayment::where('order_id', $order->id)->first();
            // Refund by payment intent
            if ($payment->stripe_payment_intent_id) {
                Refund::create([
                    'payment_intent' => $payment->stripe_payment_intent_id, // store this in your orders table
                ]);

                // Update order status
                $order->update([
                    'payment_status' => 'refunded',
                ]);

                // Update refund record
                $payment->update([
                    'status' => 'refunded',
                ]);
                if ($estoreRefund) {
                    $estoreRefund->update([
                        'is_approved' => 1,
                    ]);
                } else {
                    EstoreRefund::create([
                        'payment_intent' => $payment->stripe_payment_intent_id,
                        'amount' => $order->total_amount,
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'is_approved' => 1,
                    ]);
                }



                return response()->json(['success' => true, 'message' => 'Refund processed successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Not able to process refund. Payment intent not found.'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Not able to process refund.'], 500);
        }
    }

    public function contactCms()
    {
        if (auth()->user()->can('Manage Estore CMS')) {
            $cms = \App\Models\EcomContactCms::orderBy('id', 'desc')->first();
            return view('user.store-cms.contact_cms', compact('cms'));
        }
        abort(403, 'You do not have permission to access this page.');
    }

    public function contactCmsUpdate(\Illuminate\Http\Request $request)
    {
        if (!auth()->user()->can('Edit Estore CMS')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $validated = $request->validate([
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

        if ($request->id) {
            $cms = \App\Models\EcomContactCms::find($request->id);
            $message = 'Contact CMS updated successfully';
        } else {
            $cms = new \App\Models\EcomContactCms();
            $message = 'Contact CMS added successfully';
        }

        foreach ($validated as $key => $val) {
            if ($key !== 'banner_image') {
                $cms->$key = $val;
            }
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
        return redirect()->back()->with('message', $message);
    }
}
