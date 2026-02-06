<?php

namespace App\Http\Controllers\User;

use App\Helpers\Helper;
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
use App\Models\UserType;
use App\Models\UserRegisterAgreement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use App\Models\MembershipTier;
use App\Models\UserSubscription;
use Spatie\Permission\Models\Permission;
use App\Models\UserTypePermission;

class PartnerController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('Manage Partners')) {
            $user = Auth::user();
            $user_ecclesia_id = $user->ecclesia_id;
            $is_user_ecclesia_admin = $user->is_ecclesia_admin;

            // Retrieve filters from session
            $filters = session('partner_filters', []);
            $query = isset($filters['query']) ? $filters['query'] : null;
            $country_id = isset($filters['country_id']) ? $filters['country_id'] : null;
            $sort_by = isset($filters['sortby']) ? $filters['sortby'] : 'id';
            $sort_type = isset($filters['sorttype']) ? $filters['sorttype'] : 'desc';
            // Set current page for pagination if exists
            if (isset($filters['page'])) {
                \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($filters) {
                    return $filters['page'];
                });
            }


            $partners = User::with(['ecclesia', 'userRole'])
                ->leftJoin('user_types as ut', 'users.user_type_id', '=', 'ut.id')
                ->where(function ($q) {
                    $q->whereNull('ut.id') // Include users with deleted user types
                        ->orWhere(function ($subQ) {
                            $subQ->where('ut.name', '!=', 'SUPER ADMIN')
                                ->where('ut.name', '!=', 'ESTORE_USER');
                        });
                })
                ->select('users.*'); // Only select user columns to avoid conflicts

            // Apply search query filter
            if ($query) {
                $query_search = str_replace(" ", "%", $query);
                $partners->where(function ($q) use ($query_search) {
                    $q->where('users.id', 'like', "%{$query_search}%")
                        ->orWhereRaw('CONCAT(COALESCE(users.first_name, ""), " ", COALESCE(users.middle_name, ""), " ", COALESCE(users.last_name, "")) LIKE ?', ["%{$query_search}%"])
                        ->orWhere('users.email', 'like', "%{$query_search}%")
                        ->orWhere('users.phone', 'like', "%{$query_search}%")
                        ->orWhere('users.user_name', 'like', "%{$query_search}%")
                        ->orWhere('users.user_type', 'like', "%{$query_search}%")
                        ->orWhereHas('countries', function ($q) use ($query_search) {
                            $q->where('name', 'like', "%{$query_search}%");
                        });
                });
            }

            // Apply country filter
            if ($country_id) {
                $partners->where('users.country', $country_id);
            }


            if ($user->hasNewRole('SUPER ADMIN')) {
                $partners->where(function ($q) {
                    // Include users with deleted user types OR users with type 2 or 3
                    $q->whereNull('ut.id')
                        ->orWhereHas('userRole', function ($subQ) {
                            $subQ->whereIn('type', [2, 3]);
                        });
                })
                    ->where('users.id', '!=', $user->id);
            } elseif ($is_user_ecclesia_admin == 1) {
                $manage_ecclesia_ids = is_array($user->manage_ecclesia)
                    ? $user->manage_ecclesia
                    : explode(',', $user->manage_ecclesia);
                // print_r($manage_ecclesia_ids);
                // die;
                $partners->where(function ($q) {
                    // Include users with deleted user types OR users with type 2 or 3
                    $q->whereNull('ut.id')
                        ->orWhereHas('userRole', function ($subQ) {
                            $subQ->whereIn('type', [2, 3]);
                        });
                })
                    ->where(function ($q) use ($manage_ecclesia_ids, $user) {
                        $q->whereIn('users.ecclesia_id', $manage_ecclesia_ids)->whereNotNull('users.ecclesia_id')
                            ->orWhere('users.created_id', $user->id)->orWhere('users.id', auth()->id());
                    });
            }

            // else {
            //     $partners->where(function ($q) use ($user_ecclesia_id, $user) {
            //         $q->where('users.ecclesia_id', $user_ecclesia_id)->whereNotNull('users.ecclesia_id')
            //             ->orWhere('users.created_id', $user->id)->orWhere('users.id', auth()->id());
            //     });
            // }
            if (!$user->hasNewRole('SUPER ADMIN')) {
                if ($user->user_type == 'Regional') {
                    $partners->where('users.country', $user->country)->where('users.user_type', 'Regional');
                } elseif ($user->user_type == 'Global') {
                    $partners->where('users.user_type', '!=', 'Global');
                }
            }

            // Order results
            if ($sort_by == 'name') {
                $partners->orderByRaw('CONCAT(COALESCE(first_name, ""), " ", COALESCE(middle_name, ""), " ", COALESCE(last_name, "")) ' . $sort_type);
            } else {
                $partners->orderBy($sort_by, $sort_type);
            }

            // Paginate results
            $partners = $partners->paginate(15);
            $countries = Country::orderBy('name', 'asc')->get();

            return view('user.partner.list', compact('partners', 'countries', 'query', 'country_id', 'sort_by', 'sort_type'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function permissionsArray($allPermissions)
    {

        $categorizedPermissions = [
            'Profile' => ['Manage Profile', 'Manage My Profile'],
            'Password' => ['Manage Password', 'Manage My Password'],
            'Chat' => ['Manage Chat'],
            'Team' => ['Create Team', 'Delete Team', 'Manage Team'],
            'Email' => ['Manage Email'],
            'Becoming Sovereigns' => [
                'Manage Becoming Sovereigns',
                'View Becoming Sovereigns',
                'Upload Becoming Sovereigns',
                'Edit Becoming Sovereigns',
                'Delete Becoming Sovereigns',
                'Download Becoming Sovereigns',
            ],
            'Becoming Christ Like' => [
                'Manage Becoming Christ Like',
                'View Becoming Christ Like',
                'Upload Becoming Christ Like',
                'Edit Becoming Christ Like',
                'Delete Becoming Christ Like',
                'Download Becoming Christ Like',
            ],
            'Becoming a Leader' => [
                'Manage Becoming a Leader',
                'View Becoming a Leader',
                'Upload Becoming a Leader',
                'Edit Becoming a Leader',
                'Delete Becoming a Leader',
                'Download Becoming a Leader',
            ],
            'Files' => ['Upload File', 'Delete File', 'View File', 'Edit File', 'Manage File'],
            'Bulletin' => ['Manage Bulletin', 'Edit Bulletin', 'Create Bulletin', 'Delete Bulletin'],
            'Job Postings' => [
                'Manage Job Postings',
                'View Job Postings',
                'Create Job Postings',
                'Edit Job Postings',
                'Delete Job Postings',
            ],
            'Meeting Schedule' => [
                'Manage Meeting Schedule',
                'View Meeting Schedule',
                'Create Meeting Schedule',
                'Edit Meeting Schedule',
                'Delete Meeting Schedule',
            ],
            'Event' => ['Manage Event', 'Create Event', 'Edit Event'],
            'Partners' => ['Create Partners', 'Edit Partners', 'Delete Partners', 'Manage Partners', 'View Partners'],
            'Help' => ['Manage Help'],
            'Strategy' => [
                'Manage Strategy',
                'Upload Strategy',
                'Download Strategy',
                'View Strategy',
                'Delete Strategy',
            ],
            'Topic' => ['Manage Topic', 'Edit Topic', 'Create Topic', 'Delete Topic'],
            'Admin List' => ['Create Admin List', 'Delete Admin List', 'Manage Admin List', 'Edit Admin List'],
            'Donations' => ['Manage Donations'],
            'Contact Us Messages' => ['Manage Contact Us Messages', 'Delete Contact Us Messages'],
            'Newsletters' => ['Manage Newsletters', 'Delete Newsletters'],
            'Testimonials' => ['Create Testimonials', 'Delete Testimonials', 'Manage Testimonials', 'Edit Testimonials'],
            'Our Governance' => [
                'Create Our Governance',
                'Delete Our Governance',
                'Manage Our Governance',
                'Edit Our Governance',
            ],
            'Our Organization' => [
                'Create Our Organization',
                'Delete Our Organization',
                'Manage Our Organization',
                'Edit Our Organization',
            ],
            'Organization Center' => [
                'Create Organization Center',
                'Delete Organization Center',
                'Manage Organization Center',
                'Edit Organization Center',
            ],
            'Pages' => [
                'Manage Pages',
                'Manage Home Page',
                'Manage Details Page',
                'Manage Organizations Page',
                'Manage About Us Page',
                'Manage Ecclesia Association Page',
                'Manage Principle and Business Page',
                'Manage Contact Us Page',
                'Manage Article of Association Page',
                'Manage Footer',
                'Manage Register Page Agreement Page',
                'Manage Member Privacy Policy Page',
                'Manage PMA Terms Page',
                'Manage Privacy Policy Page',
                'Manage Terms and Conditions Page',
            ],
            'FAQ' => ['Manage Faq', 'Create Faq', 'Edit Faq', 'Delete Faq'],
            'Gallery' => ['Manage Gallery', 'Create Gallery', 'Edit Gallery', 'Delete Gallery'],
            'Users' => [
                'Manage Members Access',
                'Manage All Users',
                'Create All Users',
                'Edit All Users',
                'Delete All Users',
                'Manage All Members',
            ],
            'Roles & Permissions' => ['Manage Role Permission'],
            'Policy' => ['Manage Policy', 'Upload Policy', 'Download Policy', 'View Policy', 'Delete Policy'],
            'Warehouse' => ['Manage Warehouse Admin', 'Manage Assigned Warehouses', 'Manage Warehouse Manager'],
            'Elearning CMS' => [
                'Manage Elearning CMS',
                'View Elearning CMS',
                'Create Elearning CMS',
                'Edit Elearning CMS',
                'Delete Elearning CMS',
            ],
            'Elearning Category' => [
                'Manage Elearning Category',
                'View Elearning Category',
                'Create Elearning Category',
                'Edit Elearning Category',
                'Delete Elearning Category',
            ],
            'Elearning Topic' => [
                'Manage Elearning Topic',
                'View Elearning Topic',
                'Create Elearning Topic',
                'Edit Elearning Topic',
                'Delete Elearning Topic',
            ],
            'Elearning Product' => [
                'Manage Elearning Product',
                'View Elearning Product',
                'Create Elearning Product',
                'Edit Elearning Product',
                'Delete Elearning Product',
            ],
            'Estore CMS' => [
                'Manage Estore CMS',
                'View Estore CMS',
                'Create Estore CMS',
                'Edit Estore CMS',
                'Delete Estore CMS',
            ],
            'Estore Users' => ['Manage Estore Users', 'View Estore Users'],
            'Estore Category' => [
                'Manage Estore Category',
                'View Estore Category',
                'Create Estore Category',
                'Edit Estore Category',
                'Delete Estore Category',
            ],
            'Estore Sizes' => ['Manage Estore Sizes', 'View Estore Sizes', 'Create Estore Sizes', 'Edit Estore Sizes'],
            'Estore Colors' => [
                'Manage Estore Colors',
                'View Estore Colors',
                'Create Estore Colors',
                'Edit Estore Colors',
            ],
            'Estore Products' => [
                'Manage Estore Products',
                'View Estore Products',
                'Create Estore Products',
                'Edit Estore Products',
                'Delete Estore Products',
            ],
            'Estore Settings' => ['Manage Estore Settings', 'View Estore Settings', 'Edit Estore Settings'],
            'Estore Warehouse' => [
                'Manage Estore Warehouse',
                'View Estore Warehouse',
                'Create Estore Warehouse',
                'Edit Estore Warehouse',
                'Delete Estore Warehouse',
            ],
            'Estore Orders' => ['Manage Estore Orders', 'View Estore Orders', 'Edit Estore Orders'],
            'Order Status' => ['Manage Order Status', 'Create Order Status', 'Edit Order Status', 'Delete Order Status'],
            'Email Template' => [
                'Manage Email Template',
                'Create Email Template',
                'Edit Email Template',
                'Delete Email Template',
            ],
            'Private Collaboration' => [
                'Manage Private Collaboration',
                'View Private Collaboration',
                'Create Private Collaboration',
                'Edit Private Collaboration',
                'Delete Private Collaboration',
            ],
            'User Activity' => [
                'Manage User Activity',
                'View User Activity',
                'Create User Activity',
                'Edit User Activity',
                'Delete User Activity',
            ],
            'Membership' => [
                'Manage Membership',
                'View Membership',
                'Create Membership',
                'Edit Membership',
                'Delete Membership',
            ],
            'Membership Settings' => [
                'Manage Membership Settings',
                'View Membership Settings',
                'Edit Membership Settings',
            ],
            'Membership Members' => ['Manage Membership Members', 'View Membership Members'],
            'Membership Payments' => ['Manage Membership Payments', 'View Membership Payments'],
            'Chatbot' => ['Manage Chatbot', 'View Chatbot History', 'Manage Chatbot Keywords', 'View Chatbot Analytics'],
            'Site Settings' => ['Manage Site Settings', 'Manage Menu Settings'],
            'Signup Rules' => ['Manage Signup Rules', 'Create Signup Rules', 'Edit Signup Rules', 'Delete Signup Rules'],
        ];

        $allPermsArray = $allPermissions->pluck('name')->toArray();
        $assignedPerms = collect($categorizedPermissions)->flatten()->toArray();
        $otherPerms = array_diff($allPermsArray, $assignedPerms);

        if (!empty($otherPerms)) {
            $categorizedPermissions['Others'] = array_values($otherPerms);
        }

        $data['allPermsArray'] = $allPermsArray;
        $data['categorizedPermissions'] = $categorizedPermissions;

        return $data;
    }




    public function create()
    {
        if (Auth::user()->can('Create Partners')) {
            // $roles = UserType::whereNotIn('type', [1, 3])->get();
            $auth_user_ecclesia_id = Auth::user()->ecclesia_id;
            if (Auth::user()->getFirstUserRoleType() == 1) {
                $roles = UserType::whereIn('type', [2, 3])->get();
                $eclessias = Ecclesia::orderBy('id', 'asc')->get();
            } elseif (Auth::user()->getFirstUserRoleType() == 2 || Auth::user()->getFirstUserRoleType() == 3) {
                $roles = UserType::whereIn('type', [2, 3])->get();
                if (Auth::user()->isEcclesiaUser()) {
                    $eclessias = Auth::user()->getEcclesiaAccessAttribute();
                } else {
                    $eclessias = Ecclesia::where('id', $auth_user_ecclesia_id)->orderBy('id', 'asc')->get();
                }
            } else {
                $roles = UserType::whereIn('type', [2, 3])->get();
                $eclessias = Ecclesia::orderBy('id', 'asc')->get();
            }
            // $eclessias = User::role('ECCLESIA')->orderBy('id', 'desc')->get();
            //   $eclessias = Ecclesia::orderBy('id', 'asc')->get();
            $countries = Country::orderBy('name', 'asc')->get();

            // Load all permissions
            $allPermissions = Permission::all();

            // Load permissions for each role from user_type_permissions
            foreach ($roles as $role) {
                $role->permissions = UserTypePermission::where('user_type_id', $role->id)
                    ->join('permissions', 'user_type_permissions.permission_id', '=', 'permissions.id')
                    ->select('permissions.*')
                    ->get();
            }


            $membershipTiers = MembershipTier::all();

            $data = $this->permissionsArray($allPermissions);
            $allPermsArray = $data['allPermsArray'];
            $categorizedPermissions = $data['categorizedPermissions'];

            return view('user.partner.create')->with(compact('roles', 'allPermsArray', 'categorizedPermissions', 'eclessias', 'countries', 'allPermissions', 'membershipTiers'));
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

        $rules = [
            'user_name' => 'required|unique:users',
            'lion_roaring_id' => 'required|string|max:255|unique:users,lion_roaring_id',
            'roar_id' => 'required|string|max:255',
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
            'user_type' => 'required',
        ];

        if ($request->role === 'MEMBER_NON_SOVEREIGN') {
            $rules['membership_tier_id'] = 'required|exists:membership_tiers,id';
        } else {
            $rules['permissions'] = 'required|array';
        }

        $request->validate($rules, [
            'password.regex' => 'The password must be at least 8 characters long and include at least one special character from @$%&.',
        ]);

        $phone_number = $request->full_phone_number;
        $phone_number_cleaned = preg_replace('/[\s\-\(\)]+/', '', $phone_number);

        $check = User::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') = ?", [$phone_number_cleaned])->count();
        if ($check > 0) {
            return redirect()->back()->withErrors(['phone' => 'Phone number already exists'])->withInput();
        }

        $uniqueNumber = rand(1000, 9999);
        $lr_email = strtolower(trim($request->first_name)) . strtolower(trim($request->middle_name)) . strtolower(trim($request->last_name)) . $uniqueNumber . '@lionroaring.us';


        $is_ecclesia_admin = 0;
        $the_role = UserType::where('name', $request->role)->first();
        if ($the_role->is_ecclesia == 1) {
            $is_ecclesia_admin = 1;
            // another validation
            //return $request->manage_ecclesia;
            if ($request->manage_ecclesia == [] || $request->manage_ecclesia == null) {
                //  return 'mn is empty';
                return redirect()->back()->withErrors(['manage_ecclesia' => 'Required - House Of ECCLESIA if Role is an ECCLESIA'])->withInput();
            }
        }

        // Create a unique slug for the role name
        $slug = \Illuminate\Support\Str::slug($request->user_name);

        // Ensure slug is unique in roles table
        $originalSlug = $slug;
        $counter = 1;
        while (Role::where('name', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Create the new role
        $newRole = Role::create([
            'name' => $slug,
            'type' => $the_role->type ?? 2,
            'is_ecclesia' => $the_role->is_ecclesia ?? 0,
            'guard_name' => 'web'
        ]);

        // Sync permissions
        if ($the_role->name == 'MEMBER_NON_SOVEREIGN' && $request->has('membership_tier_id')) {
            $tier = MembershipTier::find($request->membership_tier_id);
            if ($tier && !empty($tier->permissions)) {
                $permissions = explode(',', $tier->permissions);
                $newRole->syncPermissions($permissions);
            }
        } elseif ($request->has('permissions')) {
            $newRole->syncPermissions($request->permissions);
        }

        // return $request;

        $data = new User();
        $data->created_id = Auth::user()->id;
        $data->user_name = $request->user_name;
        $data->lion_roaring_id = $request->lion_roaring_id;
        $data->roar_id = $request->roar_id;
        $data->first_name = $request->first_name;
        $data->last_name = $request->last_name;
        $data->middle_name = $request->middle_name;
        $data->personal_email = $lr_email ? str_replace(' ', '', $lr_email) : null;
        $data->email = $request->email;
        $data->user_type = $request->user_type;
        $data->user_type_id = $the_role->id;
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
        $data->phone = $request->country_code ? '+' . $request->country_code . ' ' . $request->phone : $request->phone;
        $data->phone_country_code_name = $request->phone_country_code_name;
        $data->status = 1;
        $data->is_accept = 1;


        $data->manage_ecclesia = $request->has('manage_ecclesia') ? implode(',', $request->manage_ecclesia) : null;

        $data->save();

        // Assign the newly created role to the user
        $data->assignRole($newRole->name);

        // If MEMBER_NON_SOVEREIGN, create subscription
        if ($the_role->name == 'MEMBER_NON_SOVEREIGN' && $request->has('membership_tier_id')) {
            $tier = MembershipTier::find($request->membership_tier_id);
            if ($tier) {
                UserSubscription::create([
                    'user_id' => $data->id,
                    'plan_id' => $tier->id,
                    'subscription_name' => $tier->name,
                    'subscription_method' => $tier->pricing_type ?? 'amount',
                    'subscription_price' => $tier->cost ?? 0,
                    'subscription_start_date' => now(),
                    'subscription_expire_date' => now()->addYear(),
                    'subscription_validity' => 12,
                ]);
            }
        }

        $maildata = [
            'name' => $request->full_name,
            'email' => $request->email,
            'password' => $request->password,
            'type' => ucfirst(strtolower($request->role)),
        ];

        Mail::to($request->email)->send(new RegistrationMail($maildata));
        return redirect()->route('partners.index')->with('message', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Auth::user()->can('View Partners')) {
            $id = Crypt::decrypt($id);
            $partner = User::findOrFail($id);
            $userAgreement = UserRegisterAgreement::where('user_id', $partner->id)
                ->orderBy('id', 'desc')
                ->first();
            return view('user.partner.show', compact('partner', 'userAgreement'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::user()->can('Edit Partners')) {
            $id = Crypt::decrypt($id);
            $partner = User::findOrFail($id);
            // // $roles = UserType::whereNotIn('type', [1, 3])->get();
            // if (Auth::user()->getFirstUserRoleType() == 1) {
            //     $roles = UserType::whereIn('type', [2, 3])->get();
            // } elseif (Auth::user()->getFirstUserRoleType() == 3) {
            //     $roles = UserType::whereIn('type', [2, 3])->get();
            // } else {
            //     $roles = UserType::whereIn('type', [2, 3])->get();
            // }
            // // $ecclessias = User::role('ECCLESIA')->orderBy('id', 'desc')->get();
            // $eclessias = Ecclesia::orderBy('id', 'asc')->get();
            $auth_user_ecclesia_id = Auth::user()->ecclesia_id;
            if (Auth::user()->getFirstUserRoleType() == 1) {
                $roles = UserType::whereIn('type', [2, 3])->get();
                $eclessias = Ecclesia::orderBy('id', 'asc')->get();
            } elseif (Auth::user()->getFirstUserRoleType() == 2 || Auth::user()->getFirstUserRoleType() == 3) {
                $roles = UserType::whereIn('type', [2, 3])->get();
                if (Auth::user()->isEcclesiaUser()) {
                    $eclessias = Auth::user()->getEcclesiaAccessAttribute();
                } else {
                    $eclessias = Ecclesia::where('id', $auth_user_ecclesia_id)->orderBy('id', 'asc')->get();
                }
            } else {
                $roles = UserType::whereIn('type', [2, 3])->get();
                $eclessias = Ecclesia::orderBy('id', 'asc')->get();
            }
            $countries = Country::orderBy('name', 'asc')->get();

            // Load all permissions
            $allPermissions = Permission::all();

            // Load permissions for each role from user_type_permissions
            foreach ($roles as $role) {
                $role->permissions = UserTypePermission::where('user_type_id', $role->id)
                    ->join('permissions', 'user_type_permissions.permission_id', '=', 'permissions.id')
                    ->select('permissions.*')
                    ->get();
            }

            $currentPermissions = $partner->getAllPermissions()->pluck('name');
            $membershipTiers = MembershipTier::all();
            $currentTierId = $partner->userLastSubscription->plan_id ?? null;


            $data = $this->permissionsArray($allPermissions);
            $allPermsArray = $data['allPermsArray'];
            $categorizedPermissions = $data['categorizedPermissions'];

            return view('user.partner.edit', compact('partner', 'allPermsArray', 'categorizedPermissions', 'roles', 'eclessias', 'countries', 'allPermissions', 'currentPermissions', 'membershipTiers', 'currentTierId'));
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
        // dd($request->all());
        if (Auth::user()->can('Edit Partners')) {
            $id = Crypt::decrypt($id);
            $rules = [
                'role' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'middle_name' => 'nullable',
                'lion_roaring_id' => 'required|string|max:255|unique:users,lion_roaring_id,' . $id,
                'roar_id' => 'required|string|max:255',
                'email' => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email,' . $id,
                'user_type' => 'required',
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
            ];

            if ($request->role === 'MEMBER_NON_SOVEREIGN') {
                $rules['membership_tier_id'] = 'required|exists:membership_tiers,id';
            } else {
                $rules['permissions'] = 'required|array';
            }

            $request->validate($rules, [
                'password.regex' => 'The password must be at least 8 characters long and include at least one special character from @$%&.',
            ]);

            $the_role = UserType::where('name', $request->role)->first();

            $phone_number = $request->full_phone_number;
            $phone_number_cleaned = preg_replace('/[\s\-\(\)]+/', '', $phone_number);
            $check = User::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') = ?", [$phone_number_cleaned])->where('id', '!=', $id)->count();
            if ($check > 0) {
                return redirect()->back()->withErrors(['phone' => 'Phone number already exists'])->withInput();
            }

            $is_ecclesia_admin = 0;
            $the_role = UserType::where('name', $request->role)->first();
            if ($the_role->is_ecclesia == 1) {
                $is_ecclesia_admin = 1;
                // another validation
                //return $request->manage_ecclesia;
                if ($request->manage_ecclesia == [] || $request->manage_ecclesia == null) {
                    //  return 'mn is empty';
                    return redirect()->back()->withErrors(['manage_ecclesia' => 'Required - House Of ECCLESIA if Role is an ECCLESIA'])->withInput();
                }
            }

            $data = User::find($id);
            $data->first_name = $request->first_name;
            $data->last_name = $request->last_name;
            $data->middle_name = $request->middle_name;
            $data->lion_roaring_id = $request->lion_roaring_id;
            $data->roar_id = $request->roar_id;
            $data->email = $request->email;
            $data->user_type = $request->user_type;
            $data->user_type_id = $the_role->id; // SAVE USER_TYPE_ID
            $data->address = $request->address;
            $data->country = $request->country;
            $data->state = $request->state;
            $data->city = $request->city;
            $data->zip = $request->zip;
            $data->address2 = $request->address2;
            $data->ecclesia_id = $request->ecclesia_id;
            $data->is_ecclesia_admin = $is_ecclesia_admin;
            $data->phone = $request->country_code ? '+' . $request->country_code . ' ' . $request->phone : $request->phone;
            $data->phone_country_code_name = $request->phone_country_code_name;
            if ($request->password) {
                $data->password = bcrypt($request->password);
            }

            $data->manage_ecclesia = $request->has('manage_ecclesia') ? implode(',', $request->manage_ecclesia) : null;

            $data->save();

            // Handle unique slug role
            $slug = \Illuminate\Support\Str::slug($data->user_name);
            $userRole = null;

            // Check if user already has a custom role (one that is NOT a base role)
            $baseRoleNames = UserType::pluck('name')->toArray();
            foreach ($data->roles as $role) {
                if (!in_array($role->name, $baseRoleNames)) {
                    $userRole = $role;
                    break;
                }
            }

            if (!$userRole) {
                // If no custom role found, check if a role with this slug exists
                $originalSlug = $slug;
                $counter = 1;
                while (Role::where('name', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }

                $userRole = Role::create([
                    'name' => $slug,
                    'type' => $the_role->type ?? 2,
                    'is_ecclesia' => $the_role->is_ecclesia ?? 0,
                    'guard_name' => 'web'
                ]);
            } else {
                // Update existing custom role metadata
                $userRole->type = $the_role->type ?? 2;
                $userRole->is_ecclesia = $the_role->is_ecclesia ?? 0;
                $userRole->save();
            }

            // Sync permissions to the custom role
            if ($the_role->name == 'MEMBER_NON_SOVEREIGN' && $request->has('membership_tier_id')) {
                $tier = MembershipTier::find($request->membership_tier_id);
                if ($tier && !empty($tier->permissions)) {
                    $permissions = explode(',', $tier->permissions);
                    $userRole->syncPermissions($permissions);
                }
            } elseif ($request->has('permissions')) {
                $userRole->syncPermissions($request->permissions);
            }

            // Sync user to ONLY the custom role
            $data->syncRoles([$userRole->name]);

            // Handle Membership Subscription Update
            if ($the_role->name == 'MEMBER_NON_SOVEREIGN' && $request->has('membership_tier_id')) {
                $tier = MembershipTier::find($request->membership_tier_id);
                if ($tier) {
                    $sub = UserSubscription::where('user_id', $data->id)->orderBy('id', 'desc')->first();
                    if ($sub) {
                        $sub->update([
                            'plan_id' => $tier->id,
                            'subscription_name' => $tier->name,
                            'subscription_method' => $tier->pricing_type ?? 'amount',
                            'subscription_price' => $tier->cost ?? 0,
                        ]);
                    } else {
                        UserSubscription::create([
                            'user_id' => $data->id,
                            'plan_id' => $tier->id,
                            'subscription_name' => $tier->name,
                            'subscription_method' => $tier->pricing_type ?? 'amount',
                            'subscription_price' => $tier->cost ?? 0,
                            'subscription_start_date' => now(),
                            'subscription_expire_date' => now()->addYear(),
                            'subscription_validity' => 12,
                        ]);
                    }
                }
            }

            return redirect()->route('partners.index')->with('message', 'Member updated successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
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

    public function fetchData(Request $request)
    {
        // return $request->all();
        if ($request->ajax()) {
            $sort_by = $request->get('sortby', 'id'); // Default sorting by 'id'
            $sort_type = $request->get('sorttype', 'asc'); // Default sorting type
            $query = $request->get('query');

            // Store filters in session
            session(['partner_filters' => [
                'query' => $query,
                'country_id' => $request->country_id,
                'sortby' => $sort_by,
                'sorttype' => $sort_type,
                'page' => $request->page
            ]]);

            $query = str_replace(" ", "%", $query);

            $user = Auth::user();
            $is_user_ecclesia_admin = $user->is_ecclesia_admin;
            $user_ecclesia_id = $user->ecclesia_id;

            // Base query with roles filter
            $partners = User::with(['ecclesia', 'userRole'])
                ->leftJoin('user_types as ut', 'users.user_type_id', '=', 'ut.id')
                ->where(function ($q) {
                    $q->whereNull('ut.id') // Include users with deleted user types
                        ->orWhere(function ($subQ) {
                            $subQ->where('ut.name', '!=', 'SUPER ADMIN')
                                ->where('ut.name', '!=', 'ESTORE_USER');
                        });
                })
                ->select('users.*') // Only select user columns to avoid conflicts
                ->when($query, function ($query_builder) use ($query) {
                    $query_builder->where(function ($q) use ($query) {
                        $q->where('users.id', 'like', "%{$query}%")
                            ->orWhereRaw('CONCAT(COALESCE(users.first_name, ""), " ", COALESCE(users.middle_name, ""), " ", COALESCE(users.last_name, "")) LIKE ?', ["%{$query}%"])
                            ->orWhere('users.email', 'like', "%{$query}%")
                            ->orWhere('users.phone', 'like', "%{$query}%")
                            //  ->orWhere('address', 'like', "%{$query}%")
                            ->orWhere('users.user_name', 'like', "%{$query}%")
                            ->orWhere('users.user_type', 'like', "%{$query}%")
                            //   ->orWhere('state', 'like', "%{$query}%")
                            ->orWhereHas('countries', function ($q) use ($query) {
                                $q->where('name', 'like', "%{$query}%");
                            });
                    });
                });

            if ($request->country_id) {
                $partners->where('users.country', $request->country_id);
            }


            // Apply role, user_type and ecclesia filters
            if ($user->hasNewRole('SUPER ADMIN')) {
                $partners->where(function ($q) {
                    // Include users with deleted user types OR users with type 2 or 3
                    $q->whereNull('ut.id')
                        ->orWhereHas('userRole', function ($subQ) {
                            $subQ->whereIn('type', [2, 3]);
                        });
                })
                    ->where('users.id', '!=', $user->id);
            } elseif ($is_user_ecclesia_admin == 1) {
                $manage_ecclesia_ids = is_array($user->manage_ecclesia)
                    ? $user->manage_ecclesia
                    : explode(',', $user->manage_ecclesia);

                $partners->where(function ($q) {
                    // Include users with deleted user types OR users with type 2 or 3
                    $q->whereNull('ut.id')
                        ->orWhereHas('userRole', function ($subQ) {
                            $subQ->whereIn('type', [2, 3]);
                        });
                })
                    ->where(function ($q) use ($manage_ecclesia_ids, $user) {
                        $q->whereIn('users.ecclesia_id', $manage_ecclesia_ids)->whereNotNull('users.ecclesia_id')
                            ->orWhere('users.created_id', $user->id)->orWhere('users.id', auth()->id());
                    });
            }

            // else {
            //     $partners->where(function ($q) use ($user_ecclesia_id, $user) {
            //         $q->where('users.ecclesia_id', $user_ecclesia_id)->whereNotNull('users.ecclesia_id')
            //             ->orWhere('users.created_id', $user->id)->orWhere('users.id', auth()->id());
            //     });
            // }

            if (!$user->hasNewRole('SUPER ADMIN')) {
                if ($user->user_type == 'Regional') {
                    $partners->where('users.country', $user->country)->where('users.user_type', 'Regional');
                } elseif ($user->user_type == 'Global') {
                    $partners->where('users.user_type', '!=', 'Global');
                }
            }
            // Sorting logic
            if ($sort_by == 'name') {
                $partners->orderByRaw('CONCAT(COALESCE(first_name, ""), " ", COALESCE(middle_name, ""), " ", COALESCE(last_name, "")) ' . $sort_type);
            } else {
                $partners->orderBy($sort_by, $sort_type);
            }

            // Paginate results
            $partners = $partners->orderBy('id', 'desc')->paginate(15);

            return response()->json(['data' => view('user.partner.table', compact('partners'))->render()]);
        }
    }

    public function resetFilters()
    {
        session()->forget('partner_filters');
        return redirect()->route('partners.index');
    }

    public function changePartnerStatus(Request $request)
    {
        $user = User::find($request->user_id);
        $user->status = $request->status;
        $user->is_accept = ($request->status == 1) ? 1 : 0;
        $user->save();
        // Mail to user
        if ($request->status == 0) {
            $maildata = [
                'name' => $user->full_name,
                'email' => $user->email,
                'type' => 'Deactivated',
            ];
            Mail::to($user->email)->send(new InactiveUserMail($maildata));
            $message = 'Status deactivated successfully.';
        } else {
            $maildata = [
                'name' => $user->full_name,
                'email' => $user->email,
                'type' => 'Activated',
            ];
            Mail::to($user->email)->send(new ActiveUserMail($maildata));
            $message = 'Status activated successfully.';
        }
        return response()->json(['success' => $message]);
    }

    public function delete($id)
    {
        if (Auth::user()->can('Delete Partners')) {
            $id = Crypt::decrypt($id);
            $user = User::findOrFail($id);
            Log::info($user->email . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());

            $user->delete();

            //check if user teamMember
            $teamMember = TeamMember::where('user_id', $id)->get();
            if ($teamMember) {
                $teamMember->each->delete();
            }
            return redirect()->route('partners.index')->with('error', 'Member has been deleted successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    // estore users list
    public function estoreUsers()
    {
        // return 'hello';
        if (Auth::user()->can('Manage Estore Users')) {


            $user = Auth::user();


            $partners = User::with(['ecclesia', 'userRole'])
                ->whereHas('userRole', function ($q) {
                    $q->where('name', 'ESTORE_USER');
                });

            $partners = $partners->orderBy('id', 'desc')->paginate(15);

            // return $partners;

            return view('user.partner.estore-users-list', compact('partners'));
        }
    }

    //estoreFetchData
    public function estoreFetchData(Request $request)
    {
        $sort_by = $request->get('sortby', 'id'); // Default sorting by 'id'
        $sort_type = $request->get('sorttype', 'asc'); // Default sorting type
        $query = $request->get('query');
        $query = str_replace(" ", "%", $query);

        if (Auth::user()->can('Manage Partners')) {
            $partners = User::with(['ecclesia', 'userRole'])
                ->whereHas('userRole', function ($q) {
                    $q->where('name', 'ESTORE_USER');
                });

            if ($query) {
                $partners->where(function ($q) use ($query) {
                    $q->where('id', 'like', "%{$query}%")
                        ->orWhereRaw('CONCAT(COALESCE(first_name, ""), " ", COALESCE(middle_name, ""), " ", COALESCE(last_name, "")) LIKE ?', ["%{$query}%"])
                        ->orWhere('email', 'like', "%{$query}%")
                        ->orWhere('phone', 'like', "%{$query}%")
                        //  ->orWhere('address', 'like', "%{$query}%")
                        ->orWhere('user_name', 'like', "%{$query}%");
                });
            }

            $partners = $partners->orderBy('id', 'desc')->paginate(15);

            return response()->json(['data' => view('user.partner.estore-users-table', compact('partners'))->render()]);
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }
}
