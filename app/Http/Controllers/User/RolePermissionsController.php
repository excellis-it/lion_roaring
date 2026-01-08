<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->getFirstUserRoleType() == 1 || Auth::user()->getFirstUserRoleType() == 2 || Auth::user()->getFirstUserRoleType() == 3) {
            if (Auth::user()->getFirstUserRoleType() == 1) {
                $roles = UserType::whereIn('type', [2, 3])->orderBy('id', 'DESC')->get();
            } elseif (Auth::user()->getFirstUserRoleType() == 3) {
                $roles = UserType::whereIn('type', [2])->orderBy('id', 'DESC')->get();
            } else {
                $roles = UserType::whereIn('type', [2])->orderBy('id', 'DESC')->get();
            }

            return view('user.role_permission.list', compact('roles'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->getFirstUserRoleType() == 1 || Auth::user()->getFirstUserRoleType() == 2 || Auth::user()->getFirstUserRoleType() == 3) {

            return view('user.role_permission.create');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required|unique:user_types,name',
            'is_ecclesia' => 'required',
        ]);

        $name             = $request['role_name'];
        $role             = new UserType();
        $role->name       = $name;
        $role->type = 2;
        $role->guard_name = 'web';
        $role->is_ecclesia = $request['is_ecclesia'];
        $role->save();



        return redirect()->route('roles.index')->with('message', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::user()->getFirstUserRoleType() == 1 || Auth::user()->getFirstUserRoleType() == 2 || Auth::user()->getFirstUserRoleType() == 3) {
            $id = Crypt::decrypt($id);
            $role = UserType::findOrFail($id);
            $user = Auth::user();


            return view('user.role_permission.edit', compact('role', ));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $request->validate([
            'role_name' => 'required|unique:user_types,name,' . $id,
            'is_ecclesia' => 'required',
        ]);

        $role = UserType::findOrFail($id);
        $role->name = $request->role_name;
        $role->is_ecclesia = $request['is_ecclesia'];
        $role->save();

        return redirect()->route('roles.index')->with('message', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $role = UserType::findOrFail($id);
        if ($role->name != 'SUPER ADMIN') {
            Log::info($role->id . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
            $role->delete();
            return redirect()->back()->with('message', 'Role deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'You can not delete this role.');
        }
    }
}
