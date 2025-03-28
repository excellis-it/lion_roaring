<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
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
        if (Auth::user()->getFirstRoleType() == 1 || Auth::user()->getFirstRoleType() == 2 || Auth::user()->getFirstRoleType() == 3) {
            if (Auth::user()->getFirstRoleType() == 1) {
                $roles = Role::whereIn('type', [2, 3])->orderBy('id', 'DESC')->get();
            } elseif (Auth::user()->getFirstRoleType() == 3) {
                $roles = Role::whereIn('type', [2])->orderBy('id', 'DESC')->get();
            } else {
                //  $roles = Role::where('name', '!=', 'SUPER ADMIN')->whereIn('type', [2])->get();
                $roles = Role::whereIn('type', [2])->orderBy('id', 'DESC')->get();
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
        if (Auth::user()->getFirstRoleType() == 1 || Auth::user()->getFirstRoleType() == 2 || Auth::user()->getFirstRoleType() == 3) {
            $permissions = Permission::all()->pluck('name', 'id')->toArray();
            return view('user.role_permission.create', compact('permissions'));
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
            'role_name' => 'required|unique:roles,name',
            'is_ecclesia' => 'required',
            'permissions' => 'required'
        ]);

        $name             = $request['role_name'];
        $role             = new Role();
        $role->name       = $name;
        $role->type = 2;
        $role->is_ecclesia = $request['is_ecclesia'];
        $permissions      = $request['permissions'];
        $role->save();

        if (!empty($permissions)) {
            foreach ($permissions as $permission) {
                $p = Permission::where('id', '=', $permission)->firstOrFail();
                $role = Role::where('name', '=', $name)->first();
                $role->givePermissionTo($p);
            }
        }

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
        if (Auth::user()->getFirstRoleType() == 1 || Auth::user()->getFirstRoleType() == 2 || Auth::user()->getFirstRoleType() == 3) {
            $id = Crypt::decrypt($id);
            $role = Role::findOrFail($id);
            $user = Auth::user();
            $permissions = new Collection();
            // foreach ($user->roles as $role1) {
            //     $permissions = $permissions->merge($role1->permissions);
            // }
            $rolePermissions = Permission::where('type', 1)->get();
            $permissions = $permissions->merge($rolePermissions);
            $permissions = $permissions->pluck('name', 'id')->toArray();

            //  $allPermissions = Permission::where('type', 1)->get();

            // return $allPermissions;

            return view('user.role_permission.edit', compact('role', 'permissions'));
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
            'role_name' => 'required|unique:roles,name,' . $id,
            'is_ecclesia' => 'required',
            'permissions' => 'required'
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->role_name;
        $role->is_ecclesia = $request['is_ecclesia'];
        $permissions = $request['permissions'];
        $role->save();

        $p_all = Permission::where('type', 1)->get();

        foreach ($p_all as $p) {
            $role->revokePermissionTo($p);
        }

        foreach ($permissions as $permission) {
            $p = Permission::where('id', '=', $permission)->firstOrFail();
            $role->givePermissionTo($p);
        }

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
        $role = Role::findOrFail($id);
        if ($role->name != 'SUPER ADMIN') {
            Log::info($role->id . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
            $role->delete();
            return redirect()->back()->with('message', 'Role deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'You can not delete this role.');
        }
    }
}
