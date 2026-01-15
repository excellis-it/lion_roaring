<?php

namespace App\Http\Controllers\Api\Estore;

use App\Http\Controllers\Controller;
use App\Models\EstoreOrder;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * @group Estore Profile
 */
class ProfileController extends Controller
{
    private $successStatus = 200;
    /**
     * Profile Details
     * @authenticated
     *
     * @response 200 {
     * "status": true,
     * "message": "Profile details",
     * "data": {
     *    "id": 2,
     *    "user_name": "john_doe",
     *    "first_name": "John",
     *    "middle_name": null,
     *    "last_name": "Doe",
     *    "email": "john@yopmail.com",
     *    "phone": "7415236986",
     *    "email_verified_at": null,
     *    "profile_picture": "profile_picture/1h5ihHDrrOf3Fp4O0Fg1EnLLkhuXn7vW4C1CAUZY.jpg",
     *    "address": "51 DN Block Merlin Infinite Building, 9th Floor, Unit, 907, Sector V, Bidhannagar, Kolkata, West Bengal 700091",
     *    "status": 1,
     *    "created_at": "2024-03-05T10:58:13.000000Z",
     *    "updated_at": "2024-04-18T12:27:38.000000Z"
     *    }
     * }
     */
    public function profile(Request $request)
    {
        // dd(auth()->user(), $request->user());
        $user = auth()->user();
        return response()->json([
            'status' => true,
            'message' => 'Profile details',
            'data' => $user
        ], $this->successStatus);
    }

    /**
     * Update Profile
     * @authenticated
     * @bodyParam first_name string optional First name
     * @bodyParam last_name string optional Last name
     * @bodyParam phone string optional Phone number
     * @bodyParam address string optional Address
     * @bodyParam profile_picture file optional Profile picture (jpg,jpeg,png,webp,avif,gif - max 200MB)
     *
     * @response 200 {
     * "status": true,
     * "message": "Profile updated successfully",
     * "data": {
     *    "id": 2,
     *    "first_name": "John",
     *    "last_name": "Doe",
     *    "phone": "7415236986",
     *    "profile_picture": "profile_pictures/xyz.jpg",
     *    "address": "123 Main St"
     *    }
     * }
     */
    public function updateProfile(Request $request)
    {
        // Check authentication
        if (!auth()->check()) {
            return response()->json([
                'status' => false,
                'message' => 'Please login to continue'
            ], 401);
        }

        $user = auth()->user();

        // Build validation rules conditionally
        $rules = [];
        if ($request->has('first_name') || $request->has('last_name') || $request->has('phone') || $request->has('address')) {
            $rules = array_merge($rules, [
                'first_name' => 'nullable|string|max:255',
                'last_name'  => 'nullable|string|max:255',
                'phone'      => 'nullable|string|max:255',
                'address'    => 'nullable|string|max:500',
            ]);
        }
        if ($request->hasFile('profile_picture')) {
            $rules['profile_picture'] = 'image|mimes:jpg,jpeg,png,webp,avif,gif|max:204800'; // 200MB in KB
        }

        // Validate if there are rules
        if (!empty($rules)) {
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }
        }

        try {
            // Update text fields if provided
            if ($request->filled('first_name')) $user->first_name = $request->first_name;
            if ($request->filled('last_name'))  $user->last_name  = $request->last_name;
            if ($request->filled('phone'))      $user->phone      = $request->phone;
            if ($request->has('address'))       $user->address    = $request->address;

            // Handle profile image upload
            if ($request->hasFile('profile_picture')) {
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');

                // Delete old image if exists
                if (!empty($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }

                $user->profile_picture = $path;
            }

            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Password
     * @authenticated
     *
     * @bodyParam current_password string required Current password
     * @bodyParam new_password string required New password
     * @bodyParam confirm_password string required Confirm new password
     *
     * @response 200 {
     * "status": true,
     * "message": "Password updated successfully"
     * }
     */


    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        // Validate password
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string|min:8|current_password',
            'new_password' => 'required|string|min:8|different:current_password',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        // Check current password
        if (!password_verify($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Current password is incorrect'
            ], 400);
        }

        // Update password
        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully'
        ], 200);
    }



    //get the below code as per above code for mobile app api

    /**
     * Order Tracking
     * @bodyParam order_number string required Order number
     * @authenticated
     */

    public function orderTracking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_number' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        $order = EstoreOrder::with(['payments'])
            ->where('order_number', $request->order_number)
            ->first();
        if (! $order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $order_status = OrderStatus::where('is_pickup', $order->is_pickup ? 1 : 0)
            ->orderBy('sort_order', 'asc')
            ->get();

        // find the current status id on the order
        $currentStatusId = $order->status; // integer id (assumption)
        $currentStatusModel = $currentStatusId ? OrderStatus::find($currentStatusId) : null;

        // Normalize legacy/mismatched statuses for pickup vs delivery
        if ($currentStatusModel && (bool)$currentStatusModel->is_pickup !== (bool)$order->is_pickup) {
            $deliveryToPickup = [
                'pending' => 'pickup_pending',
                'processing' => 'pickup_processing',
                'shipped' => 'pickup_ready_for_pickup',
                'out_for_delivery' => 'pickup_picked_up',
                'delivered' => 'pickup_picked_up',
                'cancelled' => 'pickup_cancelled',
            ];
            $pickupToDelivery = [
                'pickup_pending' => 'pending',
                'pickup_processing' => 'processing',
                'pickup_ready_for_pickup' => 'shipped',
                'pickup_picked_up' => 'delivered',
                'pickup_cancelled' => 'cancelled',
            ];

            $mappedSlug = $order->is_pickup
                ? ($deliveryToPickup[$currentStatusModel->slug] ?? null)
                : ($pickupToDelivery[$currentStatusModel->slug] ?? null);

            if ($mappedSlug) {
                $mappedStatus = $order_status->firstWhere('slug', $mappedSlug);
                if ($mappedStatus) {
                    $currentStatusId = $mappedStatus->id;
                    $currentStatusModel = $mappedStatus;
                }
            }
        }

        // Optional: handle cancelled specially — if you want timeline to be [first, cancelled]
        $cancelSlug = $order->is_pickup ? 'pickup_cancelled' : 'cancelled';
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
            if ($currentStatusModel && (bool)$currentStatusModel->is_pickup === (bool)$order->is_pickup) {
                $timelineStatuses = $timelineStatuses->push($currentStatusModel);
                $statusIndex = $timelineStatuses->count() - 1;
            } else {
                $statusIndex = -1;
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Order tracking',
            'data' => [
                'order' => $order,
                'timelineStatuses' => $timelineStatuses,
                'statusIndex' => $statusIndex
            ]
        ], 200);
    }
}
