<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ActiveUserMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MemberController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('Manage All Members')) {
            if (Auth::user()->hasRole('ADMIN')) {
                // Exclude ADMIN and ECCLESIA roles
                $partners = User::whereHas('roles', function ($q) {
                    $q->whereNotIn('name', ['ADMIN', 'ECCLESIA']);
                })->orderBy('id', 'desc')->paginate(15);
            } else {
                // Exclude ADMIN and ECCLESIA roles, and filter by ecclesia_id
                $partners = User::whereHas('roles', function ($q) {
                    $q->whereNotIn('name', ['ADMIN', 'ECCLESIA']);
                })
                ->where('ecclesia_id', auth()->id())
                ->orderBy('id', 'desc')
                ->paginate(15);
            }
            return view('admin.members.list', compact('partners'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }

    }


    public function fetchData(Request $request)
    {
        if ($request->ajax()) {
            $sort_by = $request->get('sortby');
            $sort_type = $request->get('sorttype');
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);

            $partners = User::with(['ecclesia', 'roles'])
                ->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhereRaw('CONCAT(COALESCE(first_name, ""), " ", COALESCE(middle_name, ""), " ", COALESCE(last_name, "")) like ?', ['%' . $query . '%'])
                        ->orWhere('email', 'like', '%' . $query . '%')
                        ->orWhere('phone', 'like', '%' . $query . '%')
                        ->orWhere('address', 'like', '%' . $query . '%')
                        ->orWhere('user_name', 'like', '%' . $query . '%')
                        ->orWhereHas('ecclesia', function ($q) use ($query) {
                            $q->where('first_name', 'like', '%' . $query . '%');
                        })
                        ->orWhereHas('roles', function ($q) use ($query) {
                            $q->where('name', 'like', '%' . $query . '%');
                        });
                });

            // Sorting logic
            if ($sort_by == 'name') {
                $partners->orderBy(DB::raw('CONCAT(COALESCE(first_name, ""), " ", COALESCE(middle_name, ""), " ", COALESCE(last_name, ""))'), $sort_type);
            } else {
                $partners->orderBy($sort_by, $sort_type);
            }

            // Exclude users with the "ADMIN" role
            $partners->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'ADMIN')->orWhere('name', 'ECCLESIA');
            });

            if (Auth::user()->hasRole('ADMIN')) {
                $partners = $partners->paginate(15);
            } else {
                $partners = $partners->where('ecclesia_id', auth()->id())->paginate(15);
            }

            return response()->json(['data' => view('admin.members.table', compact('partners'))->render()]);
        }
    }

 public function accept(Request $request, $id)
 {
     $user = User::find($id);
     $user->is_accept = 1;
     $user->status = 1;
     $user->save();

     $maildata = [
        'name' => $user->full_name,
        'email' => $user->email,
        'type' => 'Activated',
    ];
    Mail::to($user->email)->send(new ActiveUserMail($maildata));

    return redirect()->back()->with('message', 'Account has been accepted successfully');
 }
}
