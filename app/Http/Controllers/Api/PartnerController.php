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
use App\Helpers\Helper;
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
      * @queryParam status string optional Filter by status (1/0/active/inactive). Example: active
      * @queryParam country_id int optional Filter by country id. Example: 101
      * @queryParam has_agreement int optional Filter by registration agreement (1=yes,0=no). Example: 1
      * @queryParam sortby string optional Sort field: id, name, user_name, email, phone, address, user_type, country, created_at. Example: name
      * @queryParam sorttype string optional Sort direction: asc or desc. Example: desc
     *
     * @response 200 {
      *    "status": true,
      *    "message": "Members fetched successfully.",
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
     * @response 500 {
     *    "status": false,
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
            $firstRoleType = (int) ($user->userRole?->type ?? 0);
            $isSuperAdmin = ($user->userRole?->name ?? '') === 'SUPER ADMIN';
            $isEcclesiaUser = (int) ($user->userRole?->is_ecclesia ?? 0) === 1;
            $user_ecclesia_id = $user->ecclesia_id;
            $currentCode = strtoupper(Helper::getVisitorCountryCode());
            $sortBy = $request->get('sortby', 'id');
            $sortType = strtolower((string) $request->get('sorttype', 'desc')) === 'asc' ? 'asc' : 'desc';
            $status = $request->get('status');
            $countryId = $request->get('country_id');
            $hasAgreement = $request->get('has_agreement');

            // Build the query with search filters (name, email, phone)
            $partners = User::with(['ecclesia', 'userRole', 'roles', 'countries', 'states', 'warehouses', 'userRegisterAgreement'])
                ->leftJoin('user_types as ut', 'users.user_type_id', '=', 'ut.id')
                ->where(function ($q) {
                    $q->whereNull('ut.id')
                        ->orWhere(function ($subQ) {
                            $subQ->where('ut.name', '!=', 'SUPER ADMIN');
                        });
                })
                ->select('users.*')
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    $query->where(function ($q) use ($searchQuery) {
                        $q->where('users.id', 'like', "%{$searchQuery}%")
                            ->orWhereRaw('CONCAT(COALESCE(users.first_name, ""), " ", COALESCE(users.middle_name, ""), " ", COALESCE(users.last_name, "")) LIKE ?', ["%{$searchQuery}%"])
                            ->orWhere('users.email', 'like', "%{$searchQuery}%")
                            ->orWhere('users.phone', 'like', "%{$searchQuery}%")
                            ->orWhere('users.user_name', 'like', "%{$searchQuery}%")
                            ->orWhere('users.user_type', 'like', "%{$searchQuery}%")
                            ->orWhereHas('countries', function ($countryQuery) use ($searchQuery) {
                                $countryQuery->where('name', 'like', "%{$searchQuery}%");
                            });
                        //   ->orWhere('address', 'like', "%{$searchQuery}%")
                        //   ->orWhere('city', 'like', "%{$searchQuery}%")
                        //   ->orWhere('state', 'like', "%{$searchQuery}%")
                        //   ->orWhere('country', 'like', "%{$searchQuery}%");
                    });
                });

            if ($status !== null && $status !== '') {
                if ($status === 'active') {
                    $partners->where('status', 1);
                } elseif ($status === 'inactive') {
                    $partners->where('status', 0);
                } elseif (in_array((string) $status, ['0', '1'], true)) {
                    $partners->where('status', (int) $status);
                }
            }

            if (!empty($countryId)) {
                $partners->where('country', $countryId);
            }

            if ($hasAgreement === '1') {
                $partners->whereHas('userRegisterAgreement');
            } elseif ($hasAgreement === '0') {
                $partners->whereDoesntHave('userRegisterAgreement');
            }

            if ($isSuperAdmin) {
                $partners->where(function ($q) {
                    $q->whereNull('ut.id')
                        ->orWhereHas('userRole', function ($subQ) {
                            $subQ->whereIn('type', [2, 3]);
                        });
                })->where('users.id', '!=', $user->id);
            } else {
                $partners->where('users.status', 1);

                if ($user->user_type == 'Global') {
                    $partners->whereIn('users.user_type', ['Global', 'G_R']);
                } elseif ($user->user_type == 'G_R') {
                    if ($currentCode == 'GL') {
                        $partners->whereIn('users.user_type', ['Global', 'G_R']);
                    } else {
                        $manage_ecclesia_ids = is_array($user->manage_ecclesia)
                            ? $user->manage_ecclesia
                            : explode(',', $user->manage_ecclesia);

                        $partners->where('users.country', $user->country)
                            ->whereIn('users.user_type', ['Regional', 'G_R'])
                            ->where(function ($q) {
                                $q->whereNull('ut.id')
                                    ->orWhereHas('userRole', function ($subQ) {
                                        $subQ->whereIn('type', [2, 3]);
                                    });
                            });

                        if ($isEcclesiaUser) {
                            $partners->where(function ($q) use ($manage_ecclesia_ids, $user) {
                                $q->where(function ($sub) use ($manage_ecclesia_ids) {
                                    $sub->whereIn('users.ecclesia_id', $manage_ecclesia_ids)->whereNotNull('users.ecclesia_id');
                                });
                                foreach ($manage_ecclesia_ids as $id) {
                                    $id = trim($id);
                                    if ($id !== '') {
                                        $q->orWhereRaw('FIND_IN_SET(?, users.manage_ecclesia)', [$id]);
                                    }
                                }
                                $q->orWhere('users.created_id', $user->id);
                                $q->orWhere('users.id', auth()->id());
                            });
                        }
                    }
                } elseif ($user->user_type == 'Regional') {
                    $partners->where('users.country', $user->country)
                        ->whereIn('users.user_type', ['Regional', 'G_R']);

                    if ($isEcclesiaUser) {
                        $manage_ecclesia_ids = is_array($user->manage_ecclesia)
                            ? $user->manage_ecclesia
                            : explode(',', $user->manage_ecclesia);

                        $partners->where(function ($q) use ($manage_ecclesia_ids, $user) {
                            $q->where(function ($sub) use ($manage_ecclesia_ids) {
                                $sub->whereIn('users.ecclesia_id', $manage_ecclesia_ids)->whereNotNull('users.ecclesia_id');
                            });
                            foreach ($manage_ecclesia_ids as $id) {
                                $id = trim($id);
                                if ($id !== '') {
                                    $q->orWhereRaw('FIND_IN_SET(?, users.manage_ecclesia)', [$id]);
                                }
                            }
                            $q->orWhere('users.created_id', $user->id);
                            $q->orWhere('users.id', auth()->id());
                        });
                    }
                }
            }

            $allowedSortColumns = ['id', 'user_name', 'email', 'phone', 'address', 'user_type', 'country', 'created_at'];
            if ($sortBy === 'name') {
                $partners->orderByRaw('CONCAT(COALESCE(first_name, ""), " ", COALESCE(middle_name, ""), " ", COALESCE(last_name, "")) ' . $sortType);
            } elseif (in_array($sortBy, $allowedSortColumns, true)) {
                $partners->orderBy($sortBy, $sortType);
            } else {
                $partners->orderBy('id', 'desc');
            }

            // Call paginate after the conditions
            $paginatedPartners = $partners->paginate(15);

            // Return successful response with partner data
            return response()->json([
                'status' => true,
                'message' => 'Members fetched successfully.',
                'data' => $paginatedPartners
            ], 200);
        } catch (\Exception $e) {
            // Return error response in case of failure
            return response()->json([
                'status' => false,
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


            $authUser = Auth::user();
            $authUserRoleType = (int) ($authUser->userRole?->type ?? 0);
            $authUserIsEcclesia = (int) ($authUser->userRole?->is_ecclesia ?? 0) === 1;
            $auth_user_ecclesia_id = $authUser->ecclesia_id;
            if ($authUserRoleType == 1) {
                $roles = Role::with('permissions')->whereIn('type', [2, 3])->get();
                $eclessias = Ecclesia::orderBy('id', 'asc')->get();
            } elseif ($authUserRoleType == 2 || $authUserRoleType == 3) {
                $roles = Role::with('permissions')->whereIn('type', [2, 3])->get();
                if ($authUserIsEcclesia) {
                    $eclessias = $authUser->ecclesia_access;
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
                'status' => $request->status,
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
            $partner = User::with(['ecclesia', 'userRole', 'countries', 'states', 'warehouses', 'userRegisterAgreement'])->findOrFail($id);
            //   $eclessias = Ecclesia::orderBy('id', 'asc')->get();
            $countries = Country::orderBy('name', 'asc')->get();
            //   $roles = Role::with('permissions')->where('name', '!=', 'SUPER ADMIN')->get();

            $authUser = Auth::user();
            $authUserRoleType = (int) ($authUser->userRole?->type ?? 0);
            $authUserIsEcclesia = (int) ($authUser->userRole?->is_ecclesia ?? 0) === 1;
            $auth_user_ecclesia_id = $authUser->ecclesia_id;
            if ($authUserRoleType == 1) {
                $roles = Role::with('permissions')->whereIn('type', [2, 3])->get();
                $eclessias = Ecclesia::orderBy('id', 'asc')->get();
            } elseif ($authUserRoleType == 2 || $authUserRoleType == 3) {
                $roles = Role::with('permissions')->whereIn('type', [2, 3])->get();
                if ($authUserIsEcclesia) {
                    $eclessias = $authUser->ecclesia_access;
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
