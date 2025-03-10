<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * @group Role Permission
 *
 * @authenticated
 */

class RolePermissionsController extends Controller
{

    /**
     * All roles with permissions
     *
     *
     * @response 200 {
     *    "data": [
     *        {
     *            "id": 2,
     *            "name": "MEMBER_NON_SOVEREIGN",
     *            "guard_name": "web",
     *            "created_at": "2024-03-05T15:36:13.000000Z",
     *            "updated_at": "2024-03-05T15:36:13.000000Z",
     *            "permissions": [
     *                {
     *                    "id": 1,
     *                    "name": "Manage Profile",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 1
     *                    }
     *                },
     *                {
     *                    "id": 2,
     *                    "name": "Manage Password",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 2
     *                    }
     *                },
     *                {
     *                    "id": 3,
     *                    "name": "Manage Chat",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 3
     *                    }
     *                },
     *                {
     *                    "id": 4,
     *                    "name": "Create Team",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 4
     *                    }
     *                },
     *                {
     *                    "id": 6,
     *                    "name": "Delete Team",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 6
     *                    }
     *                },
     *                {
     *                    "id": 7,
     *                    "name": "Manage Team",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 7
     *                    }
     *                },
     *                {
     *                    "id": 9,
     *                    "name": "Manage Email",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 9
     *                    }
     *                },
     *                {
     *                    "id": 10,
     *                    "name": "Manage Becoming Sovereigns",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 10
     *                    }
     *                },
     *                {
     *                    "id": 11,
     *                    "name": "View Becoming Sovereigns",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 11
     *                    }
     *                },
     *                {
     *                    "id": 12,
     *                    "name": "Upload Becoming Sovereigns",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 12
     *                    }
     *                },
     *                {
     *                    "id": 13,
     *                    "name": "Edit Becoming Sovereigns",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 13
     *                    }
     *                },
     *                {
     *                    "id": 14,
     *                    "name": "Delete Becoming Sovereigns",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 14
     *                    }
     *                },
     *                {
     *                    "id": 15,
     *                    "name": "Download Becoming Sovereigns",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 15
     *                    }
     *                },
     *                {
     *                    "id": 16,
     *                    "name": "Manage Becoming Christ Like",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 16
     *                    }
     *                },
     *                {
     *                    "id": 17,
     *                    "name": "View Becoming Christ Like",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 17
     *                    }
     *                },
     *                {
     *                    "id": 22,
     *                    "name": "Manage Becoming a Leader",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 22
     *                    }
     *                },
     *                {
     *                    "id": 23,
     *                    "name": "View Becoming a Leader",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 23
     *                    }
     *                },
     *                {
     *                    "id": 33,
     *                    "name": "Manage Bulletin",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 33
     *                    }
     *                },
     *                {
     *                    "id": 34,
     *                    "name": "Edit Bulletin",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 34
     *                    }
     *                },
     *                {
     *                    "id": 35,
     *                    "name": "Create Bulletin",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 35
     *                    }
     *                },
     *                {
     *                    "id": 36,
     *                    "name": "Delete Bulletin",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 36
     *                    }
     *                },
     *                {
     *                    "id": 37,
     *                    "name": "Manage Job Postings",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 37
     *                    }
     *                },
     *                {
     *                    "id": 38,
     *                    "name": "View Job Postings",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 38
     *                    }
     *                },
     *                {
     *                    "id": 42,
     *                    "name": "Manage Meeting Schedule",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 42
     *                    }
     *                },
     *                {
     *                    "id": 43,
     *                    "name": "View Meeting Schedule",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 43
     *                    }
     *                },
     *                {
     *                    "id": 47,
     *                    "name": "Manage Event",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 47
     *                    }
     *                },
     *                {
     *                    "id": 48,
     *                    "name": "Create Event",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 48
     *                    }
     *                },
     *                {
     *                    "id": 49,
     *                    "name": "Edit Event",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 49
     *                    }
     *                },
     *                {
     *                    "id": 55,
     *                    "name": "Manage Help",
     *                    "guard_name": "web",
     *                    "created_at": "2024-08-05T12:25:46.000000Z",
     *                    "updated_at": "2024-08-05T12:25:46.000000Z",
     *                    "pivot": {
     *                        "role_id": 2,
     *                        "permission_id": 55
     *                    }
     *                }
     *            ]
     *        }
     *    ]
     * }
     *
     * @response 201 {
     *    "message": "You do not have permission to access this page."
     * }
     */
    public function list(Request $request)
    {
        try {
            // Check if the user has the necessary roles
            if (Auth::user()->hasRole('SUPER ADMIN') || Auth::user()->hasRole('LEADER')) {
                // If the user is a LEADER, fetch only the 'MEMBER_NON_SOVEREIGN' roles
                if (Auth::user()->hasRole('LEADER')) {
                    $roles = Role::where('name', 'MEMBER_NON_SOVEREIGN')
                        ->with('permissions') // Load permissions for the 'MEMBER_NON_SOVEREIGN' role
                        ->get();
                } else {
                    // If the user is an SUPER ADMIN, fetch all roles except 'SUPER ADMIN' and load their permissions
                    $roles = Role::where('name', '!=', 'SUPER ADMIN')
                        ->with('permissions') // Load permissions for all roles except 'SUPER ADMIN'
                        ->get();
                }

                // Return roles with their permissions in the response
                return response()->json(['data' => $roles], 200);
            } else {
                // If the user does not have permission, return a 403 message
                return response()->json([
                    'message' => 'You do not have permission to access this page.'
                ], 201);
            }
        } catch (\Exception $e) {
            // In case of an error, return an error response with a 201 status code
            return response()->json([
                'message' => 'An error occurred while fetching the roles and permissions.'
            ], 201);
        }
    }


    /**
     * Role permissions status
     *
     * @authenticated
     *
     * @response 200 {
     *    "modules": [
     *        {
     *            "module": "Profile",
     *            "permissions": {
     *                "manage": 1,
     *                "view": 0,
     *                "create": 0,
     *                "edit": 0,
     *                "delete": 0,
     *                "upload": 0,
     *                "download": 0
     *            }
     *        },
     *        {
     *            "module": "Password",
     *            "permissions": {
     *                "manage": 1,
     *                "view": 0,
     *                "create": 0,
     *                "edit": 0,
     *                "delete": 0,
     *                "upload": 0,
     *                "download": 0
     *            }
     *        },
     *        {
     *            "module": "Chat",
     *            "permissions": {
     *                "manage": 1,
     *                "view": 0,
     *                "create": 0,
     *                "edit": 0,
     *                "delete": 0,
     *                "upload": 0,
     *                "download": 0
     *            }
     *        },
     *        {
     *            "module": "Team",
     *            "permissions": {
     *                "manage": 1,
     *                "view": 0,
     *                "create": 1,
     *                "edit": 0,
     *                "delete": 1,
     *                "upload": 0,
     *                "download": 0
     *            }
     *        },
     *        {
     *            "module": "Email",
     *            "permissions": {
     *                "manage": 1,
     *                "view": 0,
     *                "create": 0,
     *                "edit": 0,
     *                "delete": 0,
     *                "upload": 0,
     *                "download": 0
     *            }
     *        },
     *        {
     *            "module": "Topic",
     *            "permissions": {
     *                "manage": 0,
     *                "view": 0,
     *                "create": 0,
     *                "edit": 0,
     *                "delete": 0,
     *                "upload": 0,
     *                "download": 0
     *            }
     *        },
     *        {
     *            "module": "Becoming Sovereigns",
     *            "permissions": {
     *                "manage": 1,
     *                "view": 1,
     *                "create": 0,
     *                "edit": 1,
     *                "delete": 1,
     *                "upload": 1,
     *                "download": 1
     *            }
     *        },
     *        {
     *            "module": "Becoming Christ Like",
     *            "permissions": {
     *                "manage": 1,
     *                "view": 1,
     *                "create": 0,
     *                "edit": 0,
     *                "delete": 0,
     *                "upload": 0,
     *                "download": 0
     *            }
     *        },
     *        {
     *            "module": "Becoming a Leader",
     *            "permissions": {
     *                "manage": 1,
     *                "view": 1,
     *                "create": 0,
     *                "edit": 0,
     *                "delete": 0,
     *                "upload": 0,
     *                "download": 0
     *            }
     *        },
     *        {
     *            "module": "File",
     *            "permissions": {
     *                "manage": 0,
     *                "view": 0,
     *                "create": 0,
     *                "edit": 0,
     *                "delete": 0,
     *                "upload": 0,
     *                "download": 0
     *            }
     *        },
     *        {
     *            "module": "Bulletin",
     *            "permissions": {
     *                "manage": 1,
     *                "view": 0,
     *                "create": 1,
     *                "edit": 1,
     *                "delete": 1,
     *                "upload": 0,
     *                "download": 0
     *            }
     *        },
     *        {
     *            "module": "Job Postings",
     *            "permissions": {
     *                "manage": 1,
     *                "view": 1,
     *                "create": 0,
     *                "edit": 0,
     *                "delete": 0,
     *                "upload": 0,
     *                "download": 0
     *            }
     *        },
     *        {
     *            "module": "Meeting Schedule",
     *            "permissions": {
     *                "manage": 1,
     *                "view": 1,
     *                "create": 0,
     *                "edit": 0,
     *                "delete": 0,
     *                "upload": 0,
     *                "download": 0
     *            }
     *        },
     *        {
     *            "module": "Event",
     *            "permissions": {
     *                "manage": 1,
     *                "view": 0,
     *                "create": 1,
     *                "edit": 1,
     *                "delete": 0,
     *                "upload": 0,
     *                "download": 0
     *            }
     *        },
     *        {
     *            "module": "Partners",
     *            "permissions": {
     *                "manage": 0,
     *                "view": 0,
     *                "create": 0,
     *                "edit": 0,
     *                "delete": 0,
     *                "upload": 0,
     *                "download": 0
     *            }
     *        },
     *        {
     *            "module": "Strategy",
     *            "permissions": {
     *                "manage": 0,
     *                "view": 0,
     *                "create": 0,
     *                "edit": 0,
     *                "delete": 0,
     *                "upload": 0,
     *                "download": 0
     *            }
     *        },
     *        {
     *            "module": "Help",
     *            "permissions": {
     *                "manage": 1,
     *                "view": 0,
     *                "create": 0,
     *                "edit": 0,
     *                "delete": 0,
     *                "upload": 0,
     *                "download": 0
     *            }
     *        }
     *    ]
     * }
     *
     * @response 201 {
     *   "message": "You do not have permission to access this page."
     * }
     */
    public function edit($id)
    {
        try {
            // Check if the user has the necessary roles
            if (Auth::user()->hasRole('SUPER ADMIN') || Auth::user()->hasRole('LEADER')) {
                // Decrypt the role ID
                $id = $id;
                // Find the role by ID
                $role = Role::findOrFail($id);

                // Define the list of modules
                $modules = [
                    'Profile',
                    'Password',
                    'Chat',
                    'Team',
                    'Email',
                    'Topic',
                    'Becoming Sovereigns',
                    'Becoming Christ Like',
                    'Becoming a Leader',
                    'File',
                    'Bulletin',
                    'Job Postings',
                    'Meeting Schedule',
                    'Event',
                    'Partners',
                    'Strategy',
                    'Help'
                ];

                // Fetch the role's permissions
                $permissions = $role->permissions()->pluck('name', 'id')->toArray();

                // Prepare the modules with their permissions and active status (1 or 0)
                $modulesData = [];
                foreach ($modules as $module) {
                    $permissionsStatus = [];
                    foreach (['Manage', 'View', 'Create', 'Edit', 'Delete', 'Upload', 'Download'] as $action) {
                        $permissionName = "{$action} {$module}";
                        // Check if the permission is active (1) or not (0)
                        $permissionsStatus[strtolower($action)] = in_array($permissionName, $permissions) ? 1 : 0;
                    }
                    $modulesData[] = [
                        'module' => $module,
                        'permissions' => $permissionsStatus
                    ];
                }

                // Return the module and permission data as a JSON response
                return response()->json(['modules' => $modulesData], 200);
            } else {
                // If the user doesn't have the required permissions, return an error
                return response()->json([
                    'message' => 'You do not have permission to access this page.'
                ], 201);
            }
        } catch (\Exception $e) {
            // Handle any errors (e.g., invalid role ID)
            return response()->json([
                'message' => 'An error occurred while fetching the role and permissions.'
            ], 201);
        }
    }



    //
}
