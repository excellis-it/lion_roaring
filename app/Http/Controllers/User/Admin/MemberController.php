<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ActiveUserMail;
use App\Mail\RejectionEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MemberController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('Manage Members Access')) {
            if (Auth::user()->hasNewRole('ECCLESIA')) {
                // Exclude SUPER ADMIN and ECCLESIA roles
                $partners = User::whereHas('roles', function ($q) {
                    $q->whereNotIn('name', ['SUPER ADMIN', 'ECCLESIA']);
                })->where('ecclesia_id', auth()->id())->orderBy('id', 'desc')->paginate(15);
            } else {
                // Exclude SUPER ADMIN and ECCLESIA roles, and filter by ecclesia_id
                $partners = User::whereHas('roles', function ($q) {
                    $q->whereNotIn('name', ['SUPER ADMIN', 'ECCLESIA']);
                })

                    ->orderBy('id', 'desc')
                    ->paginate(15);
            }
            return view('user.admin.members.list', compact('partners'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }


    public function fetchData(Request $request)
    {
        if ($request->ajax()) {
            if (!Auth::user()->can('Manage Members Access')) {
                abort(403, 'You do not have permission to access this page.');
            }

            $sort_by = $request->get('sortby') ?: 'id';
            $sort_type = $request->get('sorttype') === 'asc' ? 'asc' : 'desc';
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);

            $partners = User::with(['ecclesia', 'roles'])
                ->whereHas('roles', function ($q) {
                    $q->whereNotIn('name', ['SUPER ADMIN', 'ECCLESIA']);
                })
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

            // ECCLESIA users only see their own members
            if (Auth::user()->hasNewRole('ECCLESIA')) {
                $partners->where('ecclesia_id', auth()->id());
            }

            // Sorting logic
            if ($sort_by == 'name') {
                $partners->orderBy(DB::raw('CONCAT(COALESCE(first_name, ""), " ", COALESCE(middle_name, ""), " ", COALESCE(last_name, ""))'), $sort_type);
            } else {
                $partners->orderBy($sort_by, $sort_type);
            }

            $partners = $partners->paginate(15);

            return response()->json(['data' => view('user.admin.members.table', compact('partners'))->render()]);
        }
    }

    public function accept(Request $request, $id)
    {
        if (!Auth::user()->can('Manage Members Access')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $user = $this->accessibleMember($id);
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

    public function rejected(Request $request, $id)
    {
        if (!Auth::user()->can('Manage Members Access')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'reason' => 'required',
        ]);
        $partner = $this->accessibleMember($id);
        $partner->is_accept = 2; // mark as rejected
        $partner->save();

        // Send email to the partner
        Mail::to($partner->email)->send(new RejectionEmail($partner, $request->reason));

        return redirect()->route('members.index')->with('message', 'Partner has been rejected and notified.');
    }

    public function rejectedView($id)
    {
        if (!Auth::user()->can('Manage Members Access')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $partner = $this->accessibleMember($id);
        return view('user.admin.members.rejected', compact('partner'));
    }

    /**
     * Resolve a member within the same scope the listing uses.
     */
    private function accessibleMember($id): User
    {
        $query = User::whereHas('roles', function ($q) {
            $q->whereNotIn('name', ['SUPER ADMIN', 'ECCLESIA']);
        })->where('id', $id);

        if (Auth::user()->hasNewRole('ECCLESIA')) {
            $query->where('ecclesia_id', auth()->id());
        }

        return $query->firstOrFail();
    }
}
