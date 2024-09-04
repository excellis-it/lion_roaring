<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Middleware\User;
use App\Models\Country;
use App\Models\Notification;
use App\Models\User as ModelsUser;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    use ImageTrait;
    public function dashboard()
    {
        return view('user.dashboard');
    }

    public function profile()
    {
        if (auth()->user()->can('Manage Profile')) {
            $countries = Country::orderBy('name', 'asc')->get();
            return view('user.profile')->with('countries', $countries);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function password()
    {
        if (auth()->user()->can('Manage Password')) {
            return view('user.change_password');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function passwordUpdate(Request $request)
    {
        if (auth()->user()->can('Manage Password')) {
            $request->validate([
                'old_password' => 'required|min:8|password',
                'new_password' => ['required', 'different:old_password', 'regex:/^(?=.*[@$%&])[^\s]{8,}$/'],
                'confirm_password' => 'required|min:8|same:new_password',

            ], [
                'old_password.password' => 'Old password is not correct',
                'new_password.regex' => 'Password must be at least 8 characters and must contain at least one special character.',
            ]);

            $data = ModelsUser::find(Auth::user()->id);
            $data->password = Hash::make($request->new_password);
            $data->update();
            return redirect()->back()->with('message', 'Password updated successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function profileUpdate(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zip' => 'required',
        ]);
        if (auth()->user()->can('Manage Profile')) {
            $phone_number = $request->full_phone_number;
            $phone_number_cleaned = preg_replace('/[\s\-\(\)]+/', '', $phone_number);
            $check = ModelsUser::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') = ?", [$phone_number_cleaned])
                ->where('id', '!=', Auth::user()->id)
                ->count();
            if ($check > 0) {
                return redirect()->back()->with('error', 'Phone number already exists.');
            }
            $data = ModelsUser::find(Auth::user()->id);
            $data->first_name = $request->first_name;
            $data->last_name = $request->last_name;
            $data->middle_name = $request->middle_name;
            $data->address = $request->address;
            $data->address2 = $request->address2;
            $data->country = $request->country;
            $data->state = $request->state;
            $data->city = $request->city;
            $data->zip = $request->zip;

            $data->phone = $request->country_code ? '+' . $request->country_code . ' ' . $request->phone_number : $request->phone_number;
            if ($request->hasFile('profile_picture')) {
                $data->profile_picture = $this->imageUpload($request->file('profile_picture'), 'profile_picture');
            }
            $data->update();
            return redirect()->back()->with('message', 'Profile updated successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function notifications(Request $request)
    {
        if ($request->ajax()) {
            $perPage = 8; // Number of notifications per page
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $perPage;

            $notifications = Notification::where('user_id', Auth::user()->id)
                ->orderBy('created_at', 'desc')
                ->skip($offset)
                ->take($perPage)
                ->get();

            $is_notification = true;

            return response()->json([
                'view' => view('user.includes.notification', compact('notifications', 'is_notification'))->render(),
                'count' => $notifications->count()
            ]);
        }

        return abort(404); // Optional: return a 404 response if not an AJAX request
    }

    public function notificationRead($type, $id)
    {
        $id = $id;
        if ($type == 'Chat') {
            $notification = Notification::find($id);
            $notification->is_read = 1;
            $notification->update();
            return redirect()->route('chats.index');
        } elseif ($type == 'Team') {
            $notification = Notification::find($id);
            $notification->is_read = 1;
            $notification->update();
            return redirect()->route('team-chats.index');
        }

        return abort(404);
    }

    public function notificationClear()
    {
        Notification::where('user_id', Auth::user()->id)->delete();
        return response()->json(['message' => 'Notification deleted successfully.', 'status' => true]);
    }
}
