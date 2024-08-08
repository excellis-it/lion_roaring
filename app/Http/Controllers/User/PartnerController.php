<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\ActiveUserMail;
use App\Mail\InactiveUserMail;
use App\Mail\RegistrationMail;
use App\Models\Country;
use App\Models\Ecclesia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
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
            if (Auth::user()->hasRole('ADMIN')) {
                $partners = User::whereHas('roles', function ($q) {
                    $q->where('name', '!=', 'ADMIN');
                })->orderBy('id', 'desc')->paginate(10);
            } else {
                $partners = User::orderBy('id', 'desc')->paginate(10);
            }
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
            $roles = Role::where('name', '!=', 'ADMIN')->get();
            $eclessias = Ecclesia::orderBy('id', 'desc')->get();
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
            'password' => 'required|min:8',
            'confirm_password' => 'required|min:8|same:password',
            'address' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'address2' => 'nullable',
            'phone' => 'required',
        ]);

        $data = new User();
        $data->created_id = Auth::user()->id;
        $data->user_name = $request->user_name;
        $data->first_name = $request->first_name;
        $data->last_name = $request->last_name;
        $data->middle_name = $request->middle_name;
        $data->email = $request->email;
        $data->password = bcrypt($request->password);
        $data->address = $request->address;
        $data->country = $request->country;
        $data->state = $request->state;
        $data->city = $request->city;
        $data->zip = $request->zip;
        $data->address2 = $request->address2;
        $data->ecclesia_id = $request->ecclesia_id;
        $data->phone = $request->country_code ? '+'.$request->country_code . ' ' . $request->phone : $request->phone;
        $data->status = 1;
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
        if (Auth::user()->can('Edit Partners')) {
            $id = Crypt::decrypt($id);
            $partner = User::findOrFail($id);
            $roles = Role::where('name', '!=', 'ADMIN')->get();
            $ecclessias = Ecclesia::orderBy('id', 'desc')->get();
            $countries = Country::orderBy('name', 'asc')->get();
            return view('user.partner.edit', compact('partner', 'roles', 'ecclessias', 'countries'));
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
                'email' => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
                'address' => 'required',
                'phone' => 'required',
                'ecclesia_id' => 'nullable|exists:ecclesias,id',
                'country' => 'required',
                'state' => 'required',
                'city' => 'required',
                'zip' => 'required',
                'address2' => 'nullable',

            ]);

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
            $data->phone = $request->country_code ? '+'.$request->country_code . ' ' . $request->phone : $request->phone;
            $data->save();
            $data->syncRoles([$request->role]);
            return redirect()->route('partners.index')->with('message', 'Partner updated successfully.');
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
        if ($request->ajax()) {
            $sort_by = $request->get('sortby');
            $sort_type = $request->get('sorttype');
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);

            $partners = User::query()
                ->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhereRaw('CONCAT(first_name, " ", middle_name, " ", last_name) like ?', ['%' . $query . '%'])
                        ->orWhere('email', 'like', '%' . $query . '%')
                        ->orWhere('phone', 'like', '%' . $query . '%')
                        ->orWhere('address', 'like', '%' . $query . '%');
                });

            if ($sort_by == 'name') {
                $partners->orderBy(DB::raw('CONCAT(first_name, " ", middle_name, " ", last_name)'), $sort_type);
            } else {
                $partners->orderBy($sort_by, $sort_type);
            }

            // if (Auth::user()->hasRole('ADMIN')) {
            //     $partners->whereDoesntHave('roles', function ($q) {
            //         $q->where('name', 'ADMIN');
            //     });
            // } else {
            //     $partners->where('created_id', Auth::user()->id);
            // }

            $partners = $partners->paginate(10);

            return response()->json(['data' => view('user.partner.table', compact('partners'))->render()]);
        }


    }


    public function changePartnerStatus(Request $request)
    {
        $user = User::find($request->user_id);
        $user->status = $request->status;
        $user->save();
        // Mail to user
        if ($request->status == 0) {
            $maildata = [
                'name' => $user->full_name,
                'email' => $user->email,
                'type' => 'Deactivated',
            ];
            Mail::to($user->email)->send(new InactiveUserMail($maildata));
        } else {
            $maildata = [
                'name' => $user->full_name,
                'email' => $user->email,
                'type' => 'Activated',
            ];
            Mail::to($user->email)->send(new ActiveUserMail($maildata));
        }
        return response()->json(['success' => 'Status change successfully.']);
    }

    public function delete($id)
    {
       if (Auth::user()->can('Delete Partners')){
        $id = Crypt::decrypt($id);
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('partners.index')->with('error', 'Partner has been deleted successfully.');
       } else {
        abort(403, 'You do not have permission to access this page.');
       }
    }
}
