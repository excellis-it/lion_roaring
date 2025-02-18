<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class EcclessiaController extends Controller
{
    public function index()
    {
        if (auth()->user()->can('Manage All Users')) {
            $roles = Role::where('name', '!=', 'SUPER ADMIN')->whereIn('type', [1, 3])->get();
            $ecclessias = User::whereHas('roles', function ($query) {
                $query->whereIn('type', [1, 3])->where('name', '!=', 'SUPER ADMIN');
            })->where('id', '!=', auth()->user()->id)->orderBy('id', 'desc')->get();
            $countries = Country::orderBy('name', 'asc')->get();
            return view('admin.ecclessia.list')->with(compact('ecclessias', 'countries', 'roles'));
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
            'country' => 'required',
            'role' => 'required',
            'confirm_password' => 'required|same:password',
        ], [
            'password.regex' => 'The password must be at least 8 characters long and include at least one special character from @$%&.',
        ]);

        if (auth()->user()->can('Create All Users')) {

            $count = User::where('email', $request->email)->count();
            if ($count > 0) {
                return redirect()->back()->with('error', 'Email already exists');
            } else {
                $uniqueNumber = rand(1000, 9999);
                $lr_email = strtolower(trim($request->first_name)) . strtolower(trim($request->middle_name)) . strtolower(trim($request->last_name)) . $uniqueNumber . '@lionroaring.us';

                $ecclessias = new User();
                $ecclessias->first_name = $request->first_name;
                $ecclessias->last_name = $request->last_name;
                $ecclessias->middle_name = $request->middle_name ?? null;
                $ecclessias->personal_email = $lr_email ? str_replace(' ', '', $lr_email) : null;
                $ecclessias->email = $request->email;
                $ecclessias->user_name = $request->user_name;
                $ecclessias->password = bcrypt($request->password);
                $ecclessias->phone = $request->country_code ? '+' . $request->country_code . ' ' . $request->phone : $request->phone;
                $ecclessias->status = true;
                $ecclessias->country = $request->country;
                $ecclessias->save();
                $ecclessias->assignRole($request->role);
                session()->flash('message', 'Ecclessia account has been successfully created.');
                return response()->json(['message' => 'Ecclessia account has been successfully created.', 'status' => 'success']);
            }
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function edit($id)
    {
        if (auth()->user()->can('Edit All Users')) {
            $eclessia = User::where('id', $id)->first();
            $countries = Country::orderBy('name', 'asc')->get();
            $roles = Role::where('name', '!=', 'SUPER ADMIN')->whereIn('type', [1, 3])->get();
            $edit = true;
            return response()->json(['data' => view('admin.ecclessia.edit', compact('eclessia', 'countries', 'edit', 'roles'))->render()]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function update(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'role' => 'required',
            'edit_first_name' => 'required',
            'edit_last_name' => 'required',
            'edit_middle_name' => 'nullable',
            'edit_email' => 'required|email|unique:users,email,' . $request->id,
            'edit_user_name' => 'required|unique:users,user_name,' . $request->id,
            'edit_phone' => 'required',
            'country' => 'required',
            'password' => ['nullable', 'string', 'regex:/^(?=.*[@$%&])[^\s]{8,}$/'],
            'confirm_password' => 'nullable|same:password',
        ], [
            'edit_email.unique' => 'Email already exists',
            'edit_user_name.unique' => 'Username already exists',
            'edit_email.required' => 'Email is required',
            'edit_user_name.required' => 'Username is required',
            'edit_phone.required' => 'Phone number is required',
            'edit_first_name.required' => 'First name is required',
            'edit_last_name.required' => 'Last name is required',

        ]);

        $ecclessias = User::findOrFail($request->id);
        $ecclessias->first_name = $request->edit_first_name;
        $ecclessias->last_name = $request->edit_last_name;
        $ecclessias->middle_name = $request->edit_middle_name ?? null;
        $ecclessias->email = $request->edit_email;
        $ecclessias->user_name = $request->edit_user_name;
        $ecclessias->country = $request->country;
        $ecclessias->phone = $request->edit_country_code ? '+' . $request->edit_country_code . ' ' . $request->edit_phone : $request->edit_phone;
        if ($request->password) {
            $ecclessias->password = bcrypt($request->password);
        }
        $ecclessias->save();
        $ecclessias->syncRoles([$request->role]);
        session()->flash('message', 'Ecclessia account has been successfully updated.');
        return response()->json(['message' => 'Ecclessia account has been successfully updated.', 'status' => 'success']);
    }


    public function delete($id)
    {
        if (auth()->user()->can('Delete All Users')) {
            $user = User::findOrFail($id);

            Log::info($user->email . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());

            // Delete the user
            $user->delete();
            //check if user teamMember
            $teamMember = TeamMember::where('user_id', $id)->get();
            if ($teamMember) {
                $teamMember->each->delete();
            }
            return redirect()->back()->with('error', 'Ecclessia has been deleted!');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
