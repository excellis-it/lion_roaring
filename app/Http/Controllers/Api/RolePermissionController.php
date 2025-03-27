<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * @group Role & Permissions Management
 *
 * APIs for managing roles and permissions
 * @authenticated
 */
class RolePermissionController extends Controller
{
    /**
     * List all roles
     *
     * Returns a list of all roles with their associated permissions.
     * Super Admin role is excluded for non-super-admin users.
     *
     * @response 200 {
     *   "roles": [
     *     {
     *       "id": 2,
     *       "name": "ECCLESIA ADMIN",
     *       "guard_name": "web",
     *       "type": 2,
     *       "is_ecclesia": 1,
     *       "created_at": "2023-10-15T14:30:45.000000Z",
     *       "updated_at": "2023-10-15T14:30:45.000000Z",
     *       "permissions": [
     *         {
     *           "id": 1,
     *           "name": "Manage Users",
     *           "guard_name": "web",
     *           "type": 1,
     *           "created_at": "2023-10-15T14:30:45.000000Z",
     *           "updated_at": "2023-10-15T14:30:45.000000Z",
     *           "pivot": {
     *             "role_id": 2,
     *             "permission_id": 1
     *           }
     *         }
     *       ]
     *     }
     *   ]
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function index()
    {
        try {
            if (Auth::user()->getFirstRoleType() == 1) {
                $roles = Role::with('permissions')->whereIn('type', [2, 3])->get();
            } elseif (Auth::user()->getFirstRoleType() == 3) {
                $roles = Role::with('permissions')->whereIn('type', [2])->get();
            } else {
                $roles = Role::with('permissions')->whereIn('type', [2])->get();
            }

            return response()->json([
                'roles' => $roles
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch roles.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all available permissions
     *
     * Returns a list of all available permissions that can be assigned to roles.
     *
     * @response 200 {
     *   "permissions": [
     *     {
     *       "id": 1,
     *       "name": "Manage Users",
     *       "guard_name": "web",
     *       "type": 1,
     *       "created_at": "2023-10-15T14:30:45.000000Z",
     *       "updated_at": "2023-10-15T14:30:45.000000Z"
     *     },
     *     {
     *       "id": 2,
     *       "name": "Manage Roles",
     *       "guard_name": "web",
     *       "type": 1,
     *       "created_at": "2023-10-15T14:30:45.000000Z",
     *       "updated_at": "2023-10-15T14:30:45.000000Z"
     *     }
     *   ]
     * }
     */
    public function getAllPermissions()
    {
        try {
            $permissions = Permission::where('type', 1)->get();
            return response()->json([
                'permissions' => $permissions
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch permissions.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific role
     *
     * Returns details of a specific role including its permissions.
     *
     * @urlParam id integer required The ID of the role. Example: 2
     *
     * @response 200 {
     *   "role": {
     *     "id": 2,
     *     "name": "ECCLESIA ADMIN",
     *     "guard_name": "web",
     *     "type": 2,
     *     "is_ecclesia": 1,
     *     "created_at": "2023-10-15T14:30:45.000000Z",
     *     "updated_at": "2023-10-15T14:30:45.000000Z",
     *     "permissions": [
     *       {
     *         "id": 1,
     *         "name": "Manage Users",
     *         "guard_name": "web",
     *         "type": 1,
     *         "created_at": "2023-10-15T14:30:45.000000Z",
     *         "updated_at": "2023-10-15T14:30:45.000000Z",
     *         "pivot": {
     *           "role_id": 2,
     *           "permission_id": 1
     *         }
     *       }
     *     ]
     *   },
     *   "permissions": [
     *     {
     *       "id": 1,
     *       "name": "Manage Users",
     *       "guard_name": "web",
     *       "type": 1,
     *       "created_at": "2023-10-15T14:30:45.000000Z",
     *       "updated_at": "2023-10-15T14:30:45.000000Z"
     *     }
     *   ]
     * }
     * @response 404 {
     *   "message": "Role not found"
     * }
     */
    public function show($id)
    {
        try {
            $role = Role::with('permissions')->findOrFail($id);
            $permissions = Permission::where('type', 1)->get();

            return response()->json([
                'role' => $role,
                'permissions' => $permissions
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Role not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Create a new role
     *
     * Creates a new role with the specified permissions.
     *
     * @bodyParam role_name string required The name of the role. Example: Manager
     * @bodyParam is_ecclesia integer required Whether this role is for ecclesia management (0=No, 1=Yes). Example: 1
     * @bodyParam permissions array required Array of permission IDs to assign to the role. Example: [1,2,3]
     *
     * @response 201 {
     *   "message": "Role created successfully",
     *   "role": {
     *     "name": "Manager",
     *     "guard_name": "web",
     *     "type": 2,
     *     "is_ecclesia": 1,
     *     "updated_at": "2023-10-28T12:34:56.000000Z",
     *     "created_at": "2023-10-28T12:34:56.000000Z",
     *     "id": 5
     *   }
     * }
     * @response 422 {
     *   "message": "The role name field is required."
     * }
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|unique:roles,name',
            'is_ecclesia' => 'required|in:0,1',
            'permissions' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $role = new Role();
            $role->name = $request->role_name;
            $role->type = 2;
            $role->is_ecclesia = $request->is_ecclesia;
            $role->save();

            foreach ($request->permissions as $permission_id) {
                $permission = Permission::findOrFail($permission_id);
                $role->givePermissionTo($permission);
            }

            return response()->json([
                'message' => 'Role created successfully',
                'role' => $role
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a role
     *
     * Updates an existing role with new name and permissions.
     *
     * @urlParam id integer required The ID of the role. Example: 2
     * @bodyParam role_name string required The name of the role. Example: Senior Manager
     * @bodyParam is_ecclesia integer required Whether this role is for ecclesia management (0=No, 1=Yes). Example: 1
     * @bodyParam permissions array required Array of permission IDs to assign to the role. Example: [1,2,3,4]
     *
     * @response 200 {
     *   "message": "Role updated successfully",
     *   "role": {
     *     "id": 2,
     *     "name": "Senior Manager",
     *     "guard_name": "web",
     *     "type": 2,
     *     "is_ecclesia": 1,
     *     "created_at": "2023-10-15T14:30:45.000000Z",
     *     "updated_at": "2023-10-28T13:45:30.000000Z"
     *   }
     * }
     * @response 404 {
     *   "message": "Role not found"
     * }
     * @response 422 {
     *   "message": "The role name field is required."
     * }
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|unique:roles,name,' . $id,
            'is_ecclesia' => 'required|in:0,1',
            'permissions' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $role = Role::findOrFail($id);
            $role->name = $request->role_name;
            $role->is_ecclesia = $request->is_ecclesia;
            $role->save();

            // Remove all existing permissions
            $permissions = Permission::where('type', 1)->get();
            foreach ($permissions as $permission) {
                $role->revokePermissionTo($permission);
            }

            // Assign new permissions
            foreach ($request->permissions as $permission_id) {
                $permission = Permission::findOrFail($permission_id);
                $role->givePermissionTo($permission);
            }

            return response()->json([
                'message' => 'Role updated successfully',
                'role' => $role
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a role
     *
     * Deletes an existing role. The Super Admin role cannot be deleted.
     *
     * @urlParam id integer required The ID of the role. Example: 3
     *
     * @response 200 {
     *   "message": "Role deleted successfully"
     * }
     * @response 403 {
     *   "message": "You cannot delete the Super Admin role"
     * }
     * @response 404 {
     *   "message": "Role not found"
     * }
     */
    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);

            if ($role->name === 'SUPER ADMIN') {
                return response()->json([
                    'message' => 'You cannot delete the Super Admin role'
                ], 403);
            }

            Log::info($role->name . ' role deleted by ' . Auth::user()->email . ' at ' . now());
            $role->delete();

            return response()->json([
                'message' => 'Role deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete role',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
