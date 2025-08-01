<?php

namespace App\Http\Controllers\User;

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
use Spatie\Permission\Models\Role;


class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->can('Manage Partners')) {


            $user = Auth::user();
            $user_ecclesia_id = $user->ecclesia_id;
            $is_user_ecclesia_admin = $user->is_ecclesia_admin;

            $partners = User::with(['ecclesia', 'roles'])
                ->whereHas('roles', function ($q) {
                    $q->where('name', '!=', 'SUPER ADMIN');
                });

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
            } elseif ($user->hasRole('SUPER ADMIN')) {
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

            // Order and paginate results
            $partners = $partners->orderBy('id', 'desc')->paginate(15);



            return view('user.partner.list', compact('partners'));
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
        if (Auth::user()->can('Create Partners')) {
            // $roles = Role::whereNotIn('type', [1, 3])->get();
            $auth_user_ecclesia_id = Auth::user()->ecclesia_id;
            if (Auth::user()->getFirstRoleType() == 1) {
                $roles = Role::whereIn('type', [2, 3])->get();
                $eclessias = Ecclesia::orderBy('id', 'asc')->get();
            } elseif (Auth::user()->getFirstRoleType() == 2 || Auth::user()->getFirstRoleType() == 3) {
                $roles = Role::whereIn('type', [2, 3])->get();
                if (Auth::user()->isEcclesiaUser()) {
                    $eclessias = Auth::user()->getEcclesiaAccessAttribute();
                } else {
                    $eclessias = Ecclesia::where('id', $auth_user_ecclesia_id)->orderBy('id', 'asc')->get();
                }
            } else {
                $roles = Role::whereIn('type', [2, 3])->get();
                $eclessias = Ecclesia::orderBy('id', 'asc')->get();
            }
            // $eclessias = User::role('ECCLESIA')->orderBy('id', 'desc')->get();
            //   $eclessias = Ecclesia::orderBy('id', 'asc')->get();
            $countries = Country::orderBy('name', 'asc')->get();
            return view('user.partner.create')->with(compact('roles', 'eclessias', 'countries'));
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
            // 'manage_ecclesia' => 'nullable|array'
        ], [
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
        $the_role = Role::where('name', $request->role)->first();
        if ($the_role->is_ecclesia == 1) {
            $is_ecclesia_admin = 1;
            // another validation
            //return $request->manage_ecclesia;
            if ($request->manage_ecclesia == [] || $request->manage_ecclesia == null) {
                //  return 'mn is empty';
                return redirect()->back()->withErrors(['manage_ecclesia' => 'Required - House Of ECCLESIA if Role is an ECCLESIA'])->withInput();
            }
        }

        // return $request;

        $data = new User();
        $data->created_id = Auth::user()->id;
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
        $data->phone = $request->country_code ? '+' . $request->country_code . ' ' . $request->phone : $request->phone;
        $data->phone_country_code_name = $request->phone_country_code_name;
        $data->status = 1;
        $data->is_accept = 1;


        $data->manage_ecclesia = $request->has('manage_ecclesia') ? implode(',', $request->manage_ecclesia) : null;



        $data->save();
        $data->assignRole($request->role);
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
            return view('user.partner.show', compact('partner'));
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
            // // $roles = Role::whereNotIn('type', [1, 3])->get();
            // if (Auth::user()->getFirstRoleType() == 1) {
            //     $roles = Role::whereIn('type', [2, 3])->get();
            // } elseif (Auth::user()->getFirstRoleType() == 3) {
            //     $roles = Role::whereIn('type', [2, 3])->get();
            // } else {
            //     $roles = Role::whereIn('type', [2, 3])->get();
            // }
            // // $ecclessias = User::role('ECCLESIA')->orderBy('id', 'desc')->get();
            // $eclessias = Ecclesia::orderBy('id', 'asc')->get();
            $auth_user_ecclesia_id = Auth::user()->ecclesia_id;
            if (Auth::user()->getFirstRoleType() == 1) {
                $roles = Role::whereIn('type', [2, 3])->get();
                $eclessias = Ecclesia::orderBy('id', 'asc')->get();
            } elseif (Auth::user()->getFirstRoleType() == 2 || Auth::user()->getFirstRoleType() == 3) {
                $roles = Role::whereIn('type', [2, 3])->get();
                if (Auth::user()->isEcclesiaUser()) {
                    $eclessias = Auth::user()->getEcclesiaAccessAttribute();
                } else {
                    $eclessias = Ecclesia::where('id', $auth_user_ecclesia_id)->orderBy('id', 'asc')->get();
                }
            } else {
                $roles = Role::whereIn('type', [2, 3])->get();
                $eclessias = Ecclesia::orderBy('id', 'asc')->get();
            }
            $countries = Country::orderBy('name', 'asc')->get();
            return view('user.partner.edit', compact('partner', 'roles', 'eclessias', 'countries'));
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
            $request->validate([
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

            $phone_number = $request->full_phone_number;
            $phone_number_cleaned = preg_replace('/[\s\-\(\)]+/', '', $phone_number);
            $check = User::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') = ?", [$phone_number_cleaned])->where('id', '!=', $id)->count();
            if ($check > 0) {
                return redirect()->back()->withErrors(['phone' => 'Phone number already exists'])->withInput();
            }

            $is_ecclesia_admin = 0;
            $the_role = Role::where('name', $request->role)->first();
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
            $data->email = $request->email;
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
            $data->syncRoles([$request->role]);
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
            $query = str_replace(" ", "%", $query);

            $user = Auth::user();
            $is_user_ecclesia_admin = $user->is_ecclesia_admin;
            $user_ecclesia_id = $user->ecclesia_id;

            // Base query with roles filter
            $partners = User::with(['ecclesia', 'roles'])
                ->whereHas('roles', function ($q) {
                    $q->where('name', '!=', 'SUPER ADMIN');
                })
                ->when($query, function ($query_builder) use ($query) {
                    $query_builder->where(function ($q) use ($query) {
                        $q->where('id', 'like', "%{$query}%")
                            ->orWhereRaw('CONCAT(COALESCE(first_name, ""), " ", COALESCE(middle_name, ""), " ", COALESCE(last_name, "")) LIKE ?', ["%{$query}%"])
                            ->orWhere('email', 'like', "%{$query}%")
                            ->orWhere('phone', 'like', "%{$query}%")
                          //  ->orWhere('address', 'like', "%{$query}%")
                            ->orWhere('user_name', 'like', "%{$query}%");
                         //   ->orWhere('city', 'like', "%{$query}%")
                         //   ->orWhere('state', 'like', "%{$query}%")
                         //   ->orWhere('country', 'like', "%{$query}%");
                    });
                });

            // Apply role and ecclesia filters
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
            } elseif ($user->hasRole('SUPER ADMIN')) {
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
}
