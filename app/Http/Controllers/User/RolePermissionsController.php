<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserType;
use App\Models\UserTypePermission;
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
            // dd($roles);
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
            $partnerController = new PartnerController();
            $allPermissions = Permission::all();
            $data = $partnerController->permissionsArray($allPermissions);
            $allPermsArray = $data['allPermsArray'];
            $categorizedPermissions = $data['categorizedPermissions'];
            return view('user.role_permission.create', compact('allPermsArray', 'categorizedPermissions'));
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
            'is_admin' => 'required',
        ]);

        $name             = $request['role_name'];
        $role             = new UserType();
        $role->name       = $name;
        $role->type = 2;
        $role->guard_name = 'web';
        $role->is_ecclesia = $request['is_ecclesia'];
        $role->is_admin = $request['is_admin'];
        $role->save();

        if ($name != 'MEMBER_SOVEREIGN' && $request->has('permissions')) {
            foreach ($request->permissions as $permName) {
                $permission = Permission::where('name', $permName)->first();
                if ($permission) {
                    UserTypePermission::create([
                        'user_type_id' => $role->id,
                        'permission_id' => $permission->id,
                    ]);
                }
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
        if (Auth::user()->getFirstUserRoleType() == 1 || Auth::user()->getFirstUserRoleType() == 2 || Auth::user()->getFirstUserRoleType() == 3) {
            $id = Crypt::decrypt($id);
            $role = UserType::findOrFail($id);
            $user = Auth::user();

            $partnerController = new PartnerController();
            $allPermissions = Permission::all();
            $data = $partnerController->permissionsArray($allPermissions);
            $allPermsArray = $data['allPermsArray'];
            $categorizedPermissions = $data['categorizedPermissions'];

            $currentPermissions = UserTypePermission::where('user_type_id', $role->id)
                ->join('permissions', 'user_type_permissions.permission_id', '=', 'permissions.id')
                ->pluck('permissions.name')
                ->toArray();

            return view('user.role_permission.edit', compact('role', 'allPermsArray', 'categorizedPermissions', 'currentPermissions'));
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
            'is_admin' => 'required',
        ]);

        $role = UserType::findOrFail($id);
        $wasAdmin = $role->is_admin;
        $role->name = $request->role_name;
        $role->is_ecclesia = $request['is_ecclesia'];
        $role->is_admin = $request['is_admin'];
        $role->save();

        // If is_admin changed from 0 to 1, bulk-update all assigned users' user_type to G_R
        if ($wasAdmin == 0 && $request['is_admin'] == 1) {
            User::where('user_type_id', $role->id)
                ->where('user_type', '!=', 'G_R')
                ->update(['user_type' => 'G_R']);
        }

        if ($role->name != 'MEMBER_SOVEREIGN') {
            UserTypePermission::where('user_type_id', $role->id)->delete();
            if ($request->has('permissions')) {
                foreach ($request->permissions as $permName) {
                    $permission = Permission::where('name', $permName)->first();
                    if ($permission) {
                        UserTypePermission::create([
                            'user_type_id' => $role->id,
                            'permission_id' => $permission->id,
                        ]);
                    }
                }
            }
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
        $role = UserType::findOrFail($id);

        if ($role->name == 'SUPER ADMIN') {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'You cannot delete the SUPER ADMIN role.']);
            }
            return redirect()->back()->with('error', 'You cannot delete the SUPER ADMIN role.');
        }

        // Check if any users are assigned to this user_type
        $usersCount = \App\Models\User::where('user_type_id', $role->id)->count();

        if ($usersCount > 0) {
            $userWord = $usersCount == 1 ? 'user' : 'users';
            $message = "Cannot delete this role. {$usersCount} {$userWord} currently assigned to this role. Please reassign or remove these users first before deleting the role.";

            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $message]);
            }
            return redirect()->back()->with('error', $message);
        }

        // If no users assigned, proceed with deletion
        Log::info($role->id . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());

        // Delete associated permissions
        UserTypePermission::where('user_type_id', $role->id)->delete();

        // Delete the role
        $role->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Role deleted successfully.']);
        }
        return redirect()->back()->with('message', 'Role deleted successfully.');
    }

    /**
     * Get users affected by changing is_admin to Yes for a given role.
     */
    public function affectedUsers($id)
    {
        $id = Crypt::decrypt($id);
        $role = UserType::findOrFail($id);

        $users = User::where('user_type_id', $role->id)
            ->where('user_type', '!=', 'G_R')
            ->select('id', 'first_name', 'last_name', 'middle_name', 'email', 'user_type')
            ->get()
            ->map(function ($user) use ($role) {
                return [
                    'full_name' => trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name),
                    'email' => $user->email,
                    'role_name' => $role->name,
                    'current_user_type' => $user->user_type,
                    'new_user_type' => 'G_R',
                ];
            });

        return response()->json([
            'count' => $users->count(),
            'users' => $users,
        ]);
    }
}
