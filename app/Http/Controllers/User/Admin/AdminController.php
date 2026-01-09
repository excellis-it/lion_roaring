<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    //
    public function index()
    {
        if (auth()->user()->can('Manage Admin List')) {
            $admins = User::where('user_type_id', 1)->where('id', '!=', auth()->user()->id)->get();
            return view('user.admin.admin.list')->with(compact('admins'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
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
        ], [
            'password.regex' => 'The password must be at least 8 characters long and include at least one special character from @$%&.',
        ]);

        if (auth()->user()->can('Create Admin List')) {

            $count = User::where('email', $request->email)->count();
            if ($count > 0) {
                return redirect()->back()->with('error', 'Email already exists');
            } else {
                $uniqueNumber = rand(1000, 9999);
                $lr_email = strtolower(trim($request->first_name)) . strtolower(trim($request->middle_name)) . strtolower(trim($request->last_name)) . $uniqueNumber . '@lionroaring.us';

                $admin = new User();
                $admin->first_name = $request->first_name;
                $admin->last_name = $request->last_name;
                $admin->middle_name = $request->middle_name ?? null;
                $admin->personal_email = $lr_email ? str_replace(' ', '', $lr_email) : null;
                $admin->email = $request->email;
                $admin->user_name = $request->user_name;
                $admin->password = bcrypt($request->password);
                $admin->phone = $request->country_code ? '+' . $request->country_code . ' ' . $request->phone : $request->phone;
                $admin->status = true;
                $admin->is_accept = 1;
                $admin->save();
                $admin->assignRole('SUPER ADMIN');
                session()->flash('message', 'Admin account has been successfully created.');
                return response()->json(['message' => 'Admin account has been successfully created.', 'status' => 'success']);
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function edit($id)
    {
        if (auth()->user()->can('Edit Admin List')) {
            $admin = User::where('id', $id)->first();
            $edit = true;
            return response()->json(['data' => view('user.admin.admin.edit', compact('admin', 'edit'))->render()]);
            // return response()->json(['admin' => $admin, 'message' => 'Admin details found successfully.']);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
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
        ], [
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
        $admin->phone = $request->edit_country_code ? '+' . $request->edit_country_code . ' ' . $request->edit_phone : $request->edit_phone;
        $admin->is_accept = 1;
        $admin->save();
        session()->flash('message', 'Admin account has been successfully updated.');
        return response()->json(['message' => 'Admin account has been successfully updated.', 'status' => 'success']);
    }


    public function delete($id)
    {
        if (auth()->user()->can('Delete Admin List')) {
            $user = User::findOrFail($id);

            Log::info($user->email . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());

            // Delete the user
            $user->delete();
            //check if user teamMember
            $teamMember = TeamMember::where('user_id', $id)->get();
            if ($teamMember) {
                $teamMember->each->delete();
            }
            return redirect()->back()->with('error', 'Admin has been deleted!');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
