<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     if (Auth::user()->hasRole('SUPER ADMIN')) {
    //         $roles = Role::where('name', '!=', 'SUPER ADMIN')->whereIn('type', [1, 3])->get();
    //         return view('user.admin.role_permission.list', compact('roles'));
    //     } else {
    //         abort(403, 'You do not have permission to access this page.');
    //     }
    // }
    public function index()
    {
        if (Auth::user()->getFirstRoleType() == 1 || Auth::user()->getFirstRoleType() == 3) {
            if (Auth::user()->getFirstRoleType() == 1) {
                $roles = Role::where('name', '!=', 'SUPER ADMIN')->whereIn('type', [1, 3])->get();
            } else {
                $roles = Role::where('name', '!=', 'SUPER ADMIN')->whereIn('type', [2])->get();
            }
            //   $roles = Role::where('name', '!=', 'SUPER ADMIN')->whereIn('type', [3])->get();
            return view('user.admin.role_permission.list', compact('roles'));
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
        if (Auth::user()->getFirstRoleType() == 1 || Auth::user()->getFirstRoleType() == 3) {
            $permissions = Permission::all()->pluck('name', 'id')->toArray();
            return view('user.admin.role_permission.create', compact('permissions'));
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
        if (Auth::user()->getFirstRoleType() == 1) {
            $roleType = 3;
        } else {
            $roleType = 2;
        }
        $request->validate([
            'role_name' => 'required|unique:roles,name',
            'permissions' => 'required'
        ]);

        $name             = $request['role_name'];
        $role             = new Role();
        $role->name       = $name;
        $role->type = $roleType;
        $permissions      = $request['permissions'];
        $role->save();

        if (!empty($permissions)) {
            foreach ($permissions as $permission) {
                $p = Permission::where('id', '=', $permission)->firstOrFail();
                $role = Role::where('name', '=', $name)->whereIn('type', [3])->first();
                $role->givePermissionTo($p);
            }
        }

        return redirect()->route('admin.roles.index')->with('message', 'Role created successfully.');
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
        if (Auth::user()->getFirstRoleType() == 1 || Auth::user()->getFirstRoleType() == 3) {
            $id = Crypt::decrypt($id);
            $role = Role::findOrFail($id);

            $user = Auth::user();
            $firstRoleType = $user->getFirstRoleType();
            $permissions = new Collection();

            $rolePermissions = $role->permissions()->get();

           

            $permissions1 = $permissions->merge($rolePermissions);


            // Convert the permissions to an associative array (id => name)
            $permissions = $permissions1->pluck('name', 'id')->toArray();
            //  return $permissions;


            return view('user.admin.role_permission.edit', compact('role', 'permissions'));
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
            'permissions' => 'required'
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->role_name;
        $permissions = $request['permissions'];
        $role->save();

        $p_all = Permission::where('type', 2)->get();

        foreach ($p_all as $p) {
            $role->revokePermissionTo($p);
        }

        foreach ($permissions as $permission) {
            $p = Permission::where('id', '=', $permission)->firstOrFail();
            $role->givePermissionTo($p);
        }

        return redirect()->route('admin.roles.index')->with('message', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
        $role = Role::findOrFail($id);
        if ($role->name != 'SUPER ADMIN') {
            $role->delete();
            return redirect()->back()->with('message', 'Role deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'You can not delete this role.');
        }
    }
}
