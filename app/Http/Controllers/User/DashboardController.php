<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Middleware\User;
use App\Models\Country;
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
                'new_password' => 'required|min:8|different:old_password',
                'confirm_password' => 'required|min:8|same:new_password',

            ], [
                'old_password.password' => 'Old password is not correct',
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
}
