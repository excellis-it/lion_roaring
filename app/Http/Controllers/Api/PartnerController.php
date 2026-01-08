<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ActiveUserMail;
use App\Mail\InactiveUserMail;
use App\Mail\RegistrationMail;
use App\Models\ChatMember;
use App\Models\Country;
use App\Models\Ecclesia;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

/**
 * @group Members
 *
 * @authenticated
 */

class PartnerController extends Controller
{
    /**
     * List Of Members
     * @queryParam search string optional for search. Example: "abc"
     *
     * @response 200 {
     *    "data": {
     *        "current_page": 1,
     *        "data": [
     *            {
     *                "id": 39,
     *                "ecclesia_id": 9,
     *                "created_id": null,
     *                "user_name": "johndoe",
     *                "first_name": "John",
     *                "middle_name": "A.",
     *                "last_name": "Doe",
     *                "email": "johndoe@example.com",
     *                "phone": "1234567890",
     *                "email_verified_at": "2024-11-06T10:49:25.000000Z",
     *                "profile_picture": null,
     *                "address": "123 Main St",
     *                "city": "Springfield",
     *                "state": "Illinois",
     *                "address2": "Apt 4B",
     *                "country": "USA",
     *                "zip": "62704",
     *                "status": 0,
     *                "created_at": "2024-11-06T10:49:25.000000Z",
     *                "updated_at": "2024-11-06T10:49:25.000000Z"
     *            },
     *            {
     *                "id": 38,
     *                "ecclesia_id": 4,
     *                "created_id": null,
     *                "user_name": "masum2",
     *                "first_name": "Masum",
     *                "middle_name": null,
     *                "last_name": "2",
     *                "email": "masum2@excellisit.net",
     *                "phone": "+91 11 1111 1111",
     *                "email_verified_at": "2024-11-05T07:17:07.000000Z",
     *                "profile_picture": null,
     *                "address": "Kolkata",
     *                "city": "Kolkata",
     *                "state": "41",
     *                "address2": null,
     *                "country": "101",
     *                "zip": "700001",
     *                "status": 1,
     *                "created_at": "2024-11-05T07:17:07.000000Z",
     *                "updated_at": "2024-11-05T07:17:07.000000Z"
     *            },
     *            {
     *                "id": 37,
     *                "ecclesia_id": 4,
     *                "created_id": null,
     *                "user_name": "masum1",
     *                "first_name": "Test",
     *                "middle_name": null,
     *                "last_name": "User",
     *                "email": "masum@excellisit.net",
     *                "phone": "+91 91234 56789",
     *                "email_verified_at": "2024-10-28T08:35:17.000000Z",
     *                "profile_picture": "profile_picture\/sLWWnksqS6PHYMdZeBQ4OK3SnbVA0oMc9oykPbCn.webp",
     *                "address": "kolkata",
     *                "city": "kolkata",
     *                "state": "41",
     *                "address2": "kolkata",
     *                "country": "101",
     *                "zip": "700001",
     *                "status": 1,
     *                "created_at": "2024-10-28T08:35:17.000000Z",
     *                "updated_at": "2024-11-08T12:59:01.000000Z"
     *            }
     *        ],
     *        "first_page_url": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/partners\/list?page=1",
     *        "from": 1,
     *        "last_page": 2,
     *        "last_page_url": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/partners\/list?page=2",
     *        "links": [
     *            {
     *                "url": null,
     *                "label": "&laquo; Previous",
     *                "active": false
     *            },
     *            {
     *                "url": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/partners\/list?page=1",
     *                "label": "1",
     *                "active": true
     *            },
     *            {
     *                "url": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/partners\/list?page=2",
     *                "label": "2",
     *                "active": false
     *            },
     *            {
     *                "url": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/partners\/list?page=2",
     *                "label": "Next &raquo;",
     *                "active": false
     *            }
     *        ],
     *        "next_page_url": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/partners\/list?page=2",
     *        "path": "http:\/\/127.0.0.1:8000\/api\/v3\/user\/partners\/list",
     *        "per_page": 15,
     *        "prev_page_url": null,
     *        "to": 15,
     *        "total": 2
     *    }
     * }
     *
     * @response 201 {
     *    "message": "Error occurred while fetching the partners."
     * }
     */
    public function list(Request $request)
    {
        try {
            // Get search query if available
            $query = $request->get('search');
            $searchQuery = str_replace(" ", "%", $query);
            $user = Auth::user();
            $is_user_ecclesia_admin = $user->is_ecclesia_admin;
            $user_ecclesia_id = $user->ecclesia_id;

            // Build the query with search filters (name, email, phone)
            $partners = User::with(['ecclesia', 'roles'])
                ->whereHas('roles', function ($q) {
                    $q->where('name', '!=', 'SUPER ADMIN');
                })
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    $query->where(function ($q) use ($searchQuery) {
                        $q->where('id', 'like', "%{$searchQuery}%")
                            ->orWhereRaw('CONCAT(COALESCE(first_name, ""), " ", COALESCE(middle_name, ""), " ", COALESCE(last_name, "")) LIKE ?', ["%{$searchQuery}%"])
                            ->orWhere('email', 'like', "%{$searchQuery}%")
                            ->orWhere('phone', 'like', "%{$searchQuery}%");
                        //   ->orWhere('address', 'like', "%{$searchQuery}%")
                        //   ->orWhere('city', 'like', "%{$searchQuery}%")
                        //   ->orWhere('state', 'like', "%{$searchQuery}%")
                        //   ->orWhere('country', 'like', "%{$searchQuery}%");
                    });
                })
                ->orderBy('id', 'desc');

            if ($is_user_ecclesia_admin == 1) {
                $manage_ecclesia_ids = is_array($user->manage_ecclesia)
                    ? $user->manage_ecclesia
                    : explode(',', $user->manage_ecclesia);

                $partners->whereHas('roles', function ($q) {
                    $q->whereIn('type', [2, 3]);
                })
                    ->where(function ($q) use ($manage_ecclesia_ids, $user) {
                        $q->whereIn('ecclesia_id', $manage_ecclesia_ids)->whereNotNull('ecclesia_id')
                            ->orWhere('created_id', $user->id)->orWhere('id', auth()->id());
                    });
            } elseif ($user->hasNewRole('SUPER ADMIN')) {
                $partners->whereHas('roles', function ($q) {
                    $q->whereIn('type', [2, 3]);
                })
                    ->where('id', '!=', $user->id);
            } else {
                $partners->where(function ($q) use ($user_ecclesia_id, $user) {
                    $q->where('ecclesia_id', $user_ecclesia_id)->whereNotNull('ecclesia_id')
                        ->orWhere('created_id', $user->id)->orWhere('id', auth()->id());
                });
            }

            // Call paginate after the conditions
            $partners = $partners->paginate(15);

            // Ensure accessor is loaded
            $partners->getCollection()->transform(function ($partner) {
                $partner->ecclesia_access = $partner->ecclesia_access; // Ensure it's appended
                return $partner;
            });

            // Return successful response with partner data
            return response()->json([
                'data' => $partners
            ], 200);
        } catch (\Exception $e) {
            // Return error response in case of failure
            return response()->json([
                'message' => 'Error occurred while fetching the partners.',
                'error' => $e->getMessage() // Useful for debugging
            ], 500);
        }
    }




    /**
     * Load data roles, ecclesias, countries.
     *
     * @authenticated
     *
     * @response 200 {
     *   "roles": [
     *     {"id": 1, "name": "Manager"},
     *     {"id": 2, "name": "Supervisor"}
     *   ],
     *   "ecclesias": [
     *     {"id": 10, "name": "Ecclesia One"},
     *     {"id": 9, "name": "Ecclesia Two"}
     *   ],
     *   "countries": [
     *     {"id": 1, "name": "United States"},
     *     {"id": 2, "name": "Canada"}
     *   ]
     * }
     */
    public function loadCreateData()
    {
        try {
            // $roles = Role::with('permissions')->where('name', '!=', 'SUPER ADMIN')->get();
            // $ecclesias = Ecclesia::orderBy('id', 'desc')->get();


            $auth_user_ecclesia_id = Auth::user()->ecclesia_id;
            if (Auth::user()->getFirstUserRoleType() == 1) {
                $roles = Role::with('permissions')->whereIn('type', [2, 3])->get();
                $eclessias = Ecclesia::orderBy('id', 'asc')->get();
            } elseif (Auth::user()->getFirstUserRoleType() == 2 || Auth::user()->getFirstUserRoleType() == 3) {
                $roles = Role::with('permissions')->whereIn('type', [2, 3])->get();
                if (Auth::user()->isEcclesiaUser()) {
                    $eclessias = Auth::user()->getEcclesiaAccessAttribute();
                } else {
                    $eclessias = Ecclesia::where('id', $auth_user_ecclesia_id)->orderBy('id', 'asc')->get();
                }
            } else {
                $roles = Role::with('permissions')->whereIn('type', [2, 3])->get();
                $eclessias = Ecclesia::orderBy('id', 'asc')->get();
            }

            $countries = Country::orderBy('name', 'asc')->get();



            return response()->json([
                'roles' => $roles,
                'ecclesias' => $eclessias,
                'countries' => $countries
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to load data.',
                'error' => $e->getMessage()
            ], 201);
        }
    }

    /**
     * Store new Members
     *
     * @bodyParam user_name string required The unique username. Example: johndoe
     * @bodyParam ecclesia_id integer nullable The ID of the ecclesia. Example: 1
     * @bodyParam role string required The role to assign. Example: MEMBER
     * @bodyParam first_name string required The first name of the user. Example: John
     * @bodyParam last_name string required The last name of the user. Example: Doe
     * @bodyParam middle_name string nullable The middle name of the user. Example: Smith
     * @bodyParam email string required The email address of the user. Example: johndoe@example.com
     * @bodyParam password string required The password. Must contain at least one special character (@$%&). Example: P@ssword1
     * @bodyParam confirm_password string required The confirmed password. Must match the password. Example: P@ssword1
     * @bodyParam address string required The address of the user. Example: 123 Main St
     * @bodyParam country int required The country of the user. Example: 1
     * @bodyParam state int required The state of the user. Example: 1
     * @bodyParam city string required The city of the user. Example: Los Angeles
     * @bodyParam zip string required The zip code. Example: 90001
     * @bodyParam address2 string nullable The secondary address. Example: Apt 4B
     * @bodyParam phone string required The phone number. Example: 1234567890
     *
     * @response 200 {
     *   "message": "Customer created successfully."
     * }
     * @response 201 {
     *   "message": "Failed to create user.",
     *   "error": "Validation error or database error"
     * }
     */
    public function storePartner(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_name' => 'required|unique:users',
            'ecclesia_id' => 'nullable|exists:ecclesias,id',
            'role' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'middle_name' => 'nullable',
            'email' => 'required|unique:users|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
            'password' => ['required', 'string', 'regex:/^(?=.*[@$%&])[^\s]{8,}$/'],
            'confirm_password' => 'required|min:8|same:password',
            'address' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'address2' => 'nullable',
            'phone' => 'required',
        ], [
            'password.regex' => 'The password must be at least 8 characters long and include at least one special character from @$%&.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->first()
            ], 201);
        }

        // try {
        $phone_number = $request->phone;
        $phone_number_cleaned = preg_replace('/[\s\-\(\)]+/', '', $phone_number);

        $check = User::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') = ?", [$phone_number_cleaned])->count();
        if ($check > 0) {
            return response()->json(['message' => 'Phone number already exists.', 'stauts' => false], 201);
        }

        $uniqueNumber = rand(1000, 9999);
        $lr_email = strtolower(trim($request->first_name)) . strtolower(trim($request->middle_name)) . strtolower(trim($request->last_name)) . $uniqueNumber . '@lionroaring.us';


        $is_ecclesia_admin = 0;
        $the_role = Role::where('name', $request->role)->first();
        if ($the_role->is_ecclesia == 1) {
            $is_ecclesia_admin = 1;

            if ($request->manage_ecclesia == [] || $request->manage_ecclesia == null) {
                return response()->json(['message' => 'Required - House Of ECCLESIA if Role is an ECCLESIA.', 'stauts' => false], 201);
            }
        }
        Log::info($request->all());
        $data = new User();
        $data->created_id = auth()->id();
        $data->user_name = $request->user_name;
        $data->first_name = $request->first_name;
        $data->last_name = $request->last_name;
        $data->middle_name = $request->middle_name;
        $data->personal_email = $lr_email ? str_replace(' ', '', $lr_email) : null;
        $data->email = $request->email;
        $data->password = bcrypt($request->password);
        $data->address = $request->address;
        $data->country = $request->country;
        $data->state = $request->state;
        $data->city = $request->city;
        $data->zip = $request->zip;
        $data->address2 = $request->address2;
        $data->ecclesia_id = $request->ecclesia_id;
        $data->is_ecclesia_admin = $is_ecclesia_admin;
        $data->user_name = $request->user_name;
        $data->phone = $request->phone;
        $data->phone_country_code_name = $request->phone_country_code_name;
        $data->status = 1;
        $data->is_accept = 1;


        $data->manage_ecclesia = $request->has('manage_ecclesia') ? implode(',', $request->manage_ecclesia) : null;



        $data->save();
        $data->assignRole($request->role);

        Mail::to($request->email)->send(new RegistrationMail([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => $request->password,
            'type' => ucfirst(strtolower($request->role)),
        ]));

        return response()->json(['message' => 'Customer created successfully.', 'stauts' => true], 200);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'message' => 'Failed to create user.',
        //         'error' => $e->getMessage(),
        //     ], 201);
        // }
    }


    /**
     * Update Members's details
     *
     * @authenticated
     *
     * @bodyParam first_name string required The first name of the user. Example: John
     * @bodyParam last_name string required The last name of the user. Example: Doe
     * @bodyParam middle_name string nullable The middle name of the user. Example: Smith
     * @bodyParam email string required The email address of the user. Example: johndoe@example.com
     * @bodyParam address string required The address of the user. Example: 123 Main St
     * @bodyParam phone string required The phone number. Example: 1234567890
     * @bodyParam ecclesia_id integer nullable The ID of the ecclesia. Example: 1
     * @bodyParam country int required The country of the user. Example: 1
     * @bodyParam state int required The state of the user. Example: 1
     * @bodyParam city string required The city of the user. Example: Los Angeles
     * @bodyParam zip string required The zip code. Example: 90001
     * @bodyParam address2 string nullable The secondary address. Example: Apt 4B
     * @bodyParam password string nullable The new password. Must include at least one special character (@$%&). Example: P@ssword1
     * @bodyParam confirm_password string nullable The confirmed password. Must match the password. Example: P@ssword1
     * @bodyParam status int required The status. Example: 1
     *
     * @response 200 {
     *   "message": "Member updated successfully."
     * }
     * @response 201 {
     *   "message": "Failed to update member.",
     *   "error": "Validation error or database error"
     * }
     */
    public function updatePartner(Request $request, $id)
    {


        $validator = Validator::make($request->all(), [
            // 'role' => 'required',
            'role' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'middle_name' => 'nullable',
            'email' => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email,' . $id,
            'address' => 'required',
            'phone' => 'required',
            'ecclesia_id' => 'nullable|exists:ecclesias,id',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'address2' => 'nullable',
            'password' => ['nullable', 'string', 'regex:/^(?=.*[@$%&])[^\s]{8,}$/'],
            'confirm_password' => 'nullable|min:8|same:password',
        ], [
            'password.regex' => 'The password must be at least 8 characters long and include at least one special character from @$%&.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->first()
            ], 201);
        }


        // try {

        $phone_number_cleaned = preg_replace('/[\s\-\(\)]+/', '', $request->phone);

        if (User::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') = ?", [$phone_number_cleaned])->where('id', '!=', $id)->exists()) {
            return response()->json(['message' => 'Phone number already exists.', 'stauts' => false], 201);
        }

        $is_ecclesia_admin = 0;
        $the_role = Role::where('name', $request->role)->first();
        if ($the_role->is_ecclesia == 1) {
            $is_ecclesia_admin = 1;
            if ($request->manage_ecclesia == [] || $request->manage_ecclesia == null) {
                return response()->json(['message' => 'Required - House Of ECCLESIA if Role is an ECCLESIA.', 'stauts' => false], 201);
            }
        }


        $data = User::findOrFail($id);
        $data->first_name = $request->first_name;
        $data->last_name = $request->last_name;
        $data->middle_name = $request->middle_name;
        $data->email = $request->email;
        $data->address = $request->address;
        $data->country = $request->country;
        $data->state = $request->state;
        $data->city = $request->city;
        $data->zip = $request->zip;
        $data->address2 = $request->address2;
        $data->ecclesia_id = $request->ecclesia_id;
        $data->is_ecclesia_admin = $is_ecclesia_admin;
        $data->phone = $request->phone;
        $data->phone_country_code_name = $request->phone_country_code_name;
        if ($request->password) {
            $data->password = bcrypt($request->password);
        }

        $data->manage_ecclesia = $request->has('manage_ecclesia') ? implode(',', $request->manage_ecclesia) : null;

        $data->save();
        // $data->roles()->detach(); // Remove all roles first
        $data->syncRoles([$the_role->name]); // Assign new role

        return response()->json(['message' => 'Member updated successfully.', 'stauts' => true], 200);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'message' => 'Failed to update member.',
        //         'error' => $e->getMessage(),
        //     ], 201);
        // }
    }


    /**
     * Delete a Member
     *
     * @authenticated
     *
     * @urlParam id int required The ID of the partner to delete.
     *
     * @response 200 {
     *   "message": "Member has been deleted successfully."
     * }
     * @response 201 {
     *   "message": "Failed to delete member.",
     *   "error": "Validation error or database error"
     * }
     */
    public function deletePartner($id)
    {
        try {

            $user = User::findOrFail($id);

            Log::info($user->email . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());

            // Delete the user
            $user->delete();

            // Check and delete team members associated with the user
            $teamMembers = TeamMember::where('user_id', $id)->get();
            if ($teamMembers->isNotEmpty()) {
                $teamMembers->each->delete();
            }

            return response()->json(['message' => 'Member has been deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete member.',
                'error' => $e->getMessage(),
            ], 201);
        }
    }


    /**
     * Change Partner Status
     *
     * This endpoint allows an admin to change the status of a partner.
     * If the status is set to `0`, the user is deactivated and an email notification is sent.
     * If the status is set to `1`, the user is activated and an email notification is sent.
     *
     * @authenticated
     *
     * @bodyParam user_id integer required The ID of the user whose status is being updated. Example: 12
     * @bodyParam status integer required The new status of the user (0 = Deactivated, 1 = Activated). Example: 1
     *
     * @response 200 {
     *    "message": "Status activated successfully."
     * }
     * @response 201 {
     *    "message": "The user_id field is required."
     * }
     * @response 500 {
     *    "message": "An error occurred while changing status."
     * }
     */
    public function changePartnerStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 201);
        }

        try {
            $user = User::find($request->user_id);
            $user->status = $request->status;
            $user->is_accept = ($request->status == 1) ? 1 : 0;
            $user->save();

            // Mail to user
            $maildata = [
                'name' => $user->full_name,
                'email' => $user->email,
                'type' => $request->status == 1 ? 'Activated' : 'Deactivated',
            ];

            if ($request->status == 0) {
                Mail::to($user->email)->send(new InactiveUserMail($maildata));
                $message = 'Status deactivated successfully.';
            } else {
                Mail::to($user->email)->send(new ActiveUserMail($maildata));
                $message = 'Status activated successfully.';
            }

            return response()->json(['message' => $message], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while changing status.'], 500);
        }
    }

    /**
     * Get Partner Details
     *
     * This endpoint retrieves details of a specific partner by ID, including their ecclesia, roles, and associated data.
     *
     * @authenticated
     *
     * @urlParam id int required The ID of the partner. Example: 5
     *
     * @response 200 {
     *   "roles": [
     *     {
     *       "id": 2,
     *       "name": "Admin",
     *       "permissions": [
     *         {
     *           "id": 1,
     *           "name": "Manage Users"
     *         }
     *       ]
     *     }
     *   ],
     *   "eclessias": [
     *     {
     *       "id": 1,
     *       "name": "Saint Peter's Church"
     *     }
     *   ],
     *   "countries": [
     *     {
     *       "id": 1,
     *       "name": "United States"
     *     }
     *   ],
     *   "partner": {
     *     "id": 5,
     *     "first_name": "John",
     *     "last_name": "Doe",
     *     "email": "john@example.com",
     *     "ecclesia": {
     *       "id": 3,
     *       "name": "Grace Church"
     *     },
     *     "roles": [
     *       {
     *         "id": 2,
     *         "name": "Admin"
     *       }
     *     ]
     *   }
     * }
     *
     * @response 201 {
     *   "message": "Failed to fetch partner.",
     *   "error": "Partner not found."
     * }
     */

    public function viewPartner($id)
    {
        try {
            $partner = User::with(['ecclesia', 'roles'])->findOrFail($id);
            //   $eclessias = Ecclesia::orderBy('id', 'asc')->get();
            $countries = Country::orderBy('name', 'asc')->get();
            //   $roles = Role::with('permissions')->where('name', '!=', 'SUPER ADMIN')->get();

            $auth_user_ecclesia_id = Auth::user()->ecclesia_id;
            if (Auth::user()->getFirstUserRoleType() == 1) {
                $roles = Role::with('permissions')->whereIn('type', [2, 3])->get();
                $eclessias = Ecclesia::orderBy('id', 'asc')->get();
            } elseif (Auth::user()->getFirstUserRoleType() == 2 || Auth::user()->getFirstUserRoleType() == 3) {
                $roles = Role::with('permissions')->whereIn('type', [2, 3])->get();
                if (Auth::user()->isEcclesiaUser()) {
                    $eclessias = Auth::user()->getEcclesiaAccessAttribute();
                } else {
                    $eclessias = Ecclesia::where('id', $auth_user_ecclesia_id)->orderBy('id', 'asc')->get();
                }
            } else {
                $roles = Role::with('permissions')->whereIn('type', [2, 3])->get();
                $eclessias = Ecclesia::orderBy('id', 'asc')->get();
            }

            return response()->json([
                'roles' => $roles,
                'eclessias' => $eclessias,
                'countries' => $countries,
                'partner' => $partner
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch partner.',
                'error' => $e->getMessage()
            ], 201);
        }
    }
}
