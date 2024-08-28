<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    //
    public function index()
    {
        $admins = User::role('ADMIN')->where('id', '!=', auth()->user()->id)->get();
        return view('admin.admin.list')->with(compact('admins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'middle_name' => 'nullable',
            'email' => 'required|email|unique:users',
            'user_name' => 'required|unique:users',
            'password' => ['required', 'string', 'regex:/^(?=.*[@$%&])[^\s]{8,}$/'],
            'phone' => 'required',
            'confirm_password' => 'required|same:password',
        ],[
            'password.regex' => 'The password must be at least 8 characters long and include at least one special character from @$%&.',
        ]);

        $count = User::where('email', $request->email)->count();
        if ($count > 0) {
            return redirect()->back()->with('error', 'Email already exists');
        } else {
            $admin = new User;
            $admin->first_name = $request->first_name;
            $admin->last_name = $request->last_name;
            $admin->middle_name = $request->middle_name ?? null;
            $admin->email = $request->email;
            $admin->user_name = $request->user_name;
            $admin->password = bcrypt($request->password);
            $admin->phone = $request->phone;
            $admin->status = true;
            $admin->save();
            $admin->assignRole('ADMIN');
            session()->flash('message', 'Admin account has been successfully created.');
            return response()->json(['message' => 'Admin account has been successfully created.', 'status' => 'success']);
        }
    }

    public function edit($id)
    {
        $admin = User::where('id', $id)->first();
        return response()->json(['admin' => $admin, 'message' => 'Admin details found successfully.']);
    }

    public function update(Request $request)
    {

        $request->validate([
            'edit_first_name' => 'required',
            'edit_last_name' => 'required',
            'edit_middle_name' => 'nullable',
            'edit_email' => 'required|email|unique:users,email,' . $request->id,
            'edit_user_name' => 'required|unique:users,user_name,' . $request->id,
            'edit_phone' => 'required',
        ],[
            'edit_email.unique' => 'Email already exists',
            'edit_user_name.unique' => 'Username already exists',
            'edit_email.required' => 'Email is required',
            'edit_user_name.required' => 'Username is required',
            'edit_phone.required' => 'Phone number is required',
            'edit_first_name.required' => 'First name is required',
            'edit_last_name.required' => 'Last name is required',

        ]);

        $admin = User::findOrFail($request->id);
        $admin->first_name = $request->edit_first_name;
        $admin->last_name = $request->edit_last_name;
        $admin->middle_name = $request->edit_middle_name ?? null;
        $admin->email = $request->edit_email;
        $admin->user_name = $request->edit_user_name;
        $admin->phone = $request->edit_phone;
        $admin->save();
        session()->flash('message', 'Admin account has been successfully updated.');
        return response()->json(['message' => 'Admin account has been successfully updated.', 'status' => 'success']);

    }


    public function delete($id)
    {

        User::findOrFail($id)->delete();
        return redirect()->back()->with('error', 'Admin has been deleted!');
    }
}
