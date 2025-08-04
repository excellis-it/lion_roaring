<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use ImageTrait;

    public function index()
    {
        if (auth()->user()->can('Manage My Profile')) {
            return view('admin.profile');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function profileUpdate(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email'    => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email,' . Auth::user()->id,
            'phone_number' => 'required',
            'profile_picture' => 'nullable|mimes:jpg,png,jpeg,gif,svg',
        ]);

        $data = User::find(Auth::user()->id);
        $data->first_name = $request->first_name;
        $data->last_name = $request->last_name;
        $data->email = $request->email;
        $data->phone = $request->phone_number;
        $data->profile_picture = $this->imageUpload($request->file('profile_picture'), 'profile');
        $data->save();
        return redirect()->back()->with('message', 'Profile updated successfully.');
    }

    public function password()
    {
        if (auth()->user()->can('Manage My Password')) {
            return view('admin.password');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function passwordUpdate(Request $request)
    {

        $request->validate([
            'old_password' => 'required|min:8|password',
            'new_password' => 'required|min:8|different:old_password',
            'confirm_password' => 'required|min:8|same:new_password',

        ], [
            'old_password.password' => 'Old password is not correct',
        ]);

        $data = User::find(Auth::user()->id);
        $data->password = Hash::make($request->new_password);
        $data->update();
        return redirect()->back()->with('message', 'Password updated successfully.');
    }
}
