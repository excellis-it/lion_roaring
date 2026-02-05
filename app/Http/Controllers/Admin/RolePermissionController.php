<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserType;
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
    //     if (Auth::user()->hasNewRole('SUPER ADMIN')) {
    //         $roles = Role::where('name', '!=', 'SUPER ADMIN')->whereIn('type', [1, 3])->get();
    //         return view('admin.role_permission.list', compact('roles'));
    //     } else {
    //         abort(403, 'You do not have permission to access this page.');
    //     }
    // }
    public function index()
    {
        if (Auth::user()->getFirstUserRoleType() == 1 || Auth::user()->getFirstUserRoleType() == 3) {
            if (Auth::user()->getFirstUserRoleType() == 1) {
                $roles = UserType::where('name', '!=', 'SUPER ADMIN')->whereIn('type', [1, 3])->get();
            } else {
                $roles = UserType::where('name', '!=', 'SUPER ADMIN')->whereIn('type', [2])->get();
            }
            //   $roles = UserType::where('name', '!=', 'SUPER ADMIN')->whereIn('type', [3])->get();
            return view('admin.role_permission.list', compact('roles'));
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
        if (Auth::user()->getFirstUserRoleType() == 1 || Auth::user()->getFirstUserRoleType() == 3) {
            $permissions = Permission::all()->pluck('name', 'id')->toArray();
            return view('admin.role_permission.create', compact('permissions'));
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
        if (Auth::user()->getFirstUserRoleType() == 1) {
            $roleType = 3;
        } else {
            $roleType = 2;
        }
        $request->validate([
            'role_name' => 'required|unique:roles,name',
            'permissions' => 'required'
        ]);

        $name             = $request['role_name'];
        $role             = new UserType();
        $role->name       = $name;
        $role->type = $roleType;
        $permissions      = $request['permissions'];
        $role->save();

        if (!empty($permissions)) {
            foreach ($permissions as $permission) {
                $p = Permission::where('id', '=', $permission)->firstOrFail();
                $role = UserType::where('name', '=', $name)->whereIn('type', [3])->first();
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
        if (Auth::user()->getFirstUserRoleType() == 1 || Auth::user()->getFirstUserRoleType() == 3) {
            $id = Crypt::decrypt($id);
            $role = UserType::findOrFail($id);

            $user = Auth::user();
            $firstRoleType = $user->getFirstUserRoleType();
            $permissions = new Collection();

            $rolePermissions = $role->permissions()->get();

            $permissions1 = $permissions->merge($rolePermissions);


            // Convert the permissions to an associative array (id => name)
            $permissions = $permissions1->pluck('name', 'id')->toArray();
            //  return $permissions;


            return view('admin.role_permission.edit', compact('role', 'permissions'));
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

        $role = UserType::findOrFail($id);
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

    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
        $role = UserType::findOrFail($id);

        if ($role->name == 'SUPER ADMIN') {
            return redirect()->back()->with('error', 'You cannot delete the SUPER ADMIN role.');
        }

        // Check if any users are assigned to this user_type
        $usersCount = \App\Models\User::where('user_type_id', $role->id)->count();

        if ($usersCount > 0) {
            $userWord = $usersCount == 1 ? 'user' : 'users';
            return redirect()->back()->with('error', "Cannot delete this role. {$usersCount} {$userWord} currently assigned to this role. Please reassign or remove these users first before deleting the role.");
        }

        // If no users assigned, proceed with deletion
        \Illuminate\Support\Facades\Log::info($role->id . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());

        // Delete associated permissions
        \App\Models\UserTypePermission::where('user_type_id', $role->id)->delete();

        // Delete the role
        $role->delete();

        return redirect()->back()->with('message', 'Role deleted successfully.');
    }
}
