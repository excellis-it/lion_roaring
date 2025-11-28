<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Middleware\User;
use App\Models\Country;
use App\Models\Notification;
use App\Models\User as ModelsUser;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\MailUser;
use App\Models\Chat;
use App\Models\TeamChat;
use App\Models\ChatMember;

class DashboardController extends Controller
{
    use ImageTrait;
    public function dashboard()
    {
        return view('user.dashboard');
    }

    public function profile()
    {
        if (auth()->user()->can('Manage Profile')) {
            $countries = Country::orderBy('name', 'asc')->get();
            return view('user.profile')->with('countries', $countries);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function userSubscription()
    {
        return redirect()->route('user.membership.index');
    }

    public function password()
    {
        if (auth()->user()->can('Manage Password')) {
            return view('user.change_password');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function passwordUpdate(Request $request)
    {
        if (auth()->user()->can('Manage Password')) {
            $request->validate([
                'old_password' => 'required|min:8|password',
                'new_password' => ['required', 'different:old_password', 'regex:/^(?=.*[@$%&])[^\s]{8,}$/'],
                'confirm_password' => 'required|min:8|same:new_password',

            ], [
                'old_password.password' => 'Old password is not correct',
                'new_password.regex' => 'The password must be at least 8 characters long and include at least one special character from @$%&',
            ]);

            $data = ModelsUser::find(Auth::user()->id);
            $data->password = Hash::make($request->new_password);
            $data->update();
            return redirect()->back()->with('message', 'Password updated successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function profileUpdate(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zip' => 'required',
        ]);
        if (auth()->user()->can('Manage Profile')) {
            $phone_number = $request->full_phone_number;
            $phone_number_cleaned = preg_replace('/[\s\-\(\)]+/', '', $phone_number);
            $check = ModelsUser::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') = ?", [$phone_number_cleaned])
                ->where('id', '!=', Auth::user()->id)
                ->count();
            if ($check > 0) {
                return redirect()->back()->with('error', 'Phone number already exists.');
            }
            $data = ModelsUser::find(Auth::user()->id);
            $data->first_name = $request->first_name;
            $data->last_name = $request->last_name;
            $data->middle_name = $request->middle_name;
            $data->address = $request->address;
            $data->address2 = $request->address2;
            $data->country = $request->country;
            $data->state = $request->state;
            $data->city = $request->city;
            $data->zip = $request->zip;

            $data->phone = $request->country_code ? '+' . $request->country_code . ' ' . $request->phone_number : $request->phone_number;
            $data->phone_country_code_name = $request->phone_country_code_name;
            if ($request->hasFile('profile_picture')) {
                $data->profile_picture = $this->imageUpload($request->file('profile_picture'), 'profile_picture');
            }
            $data->update();
            return redirect()->back()->with('message', 'Profile updated successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function notifications(Request $request)
    {
        if ($request->ajax()) {
            $perPage = 8; // Number of notifications per page
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $perPage;

            $notifications = Notification::where('user_id', Auth::user()->id)->where('is_delete', 0)
                ->orderBy('created_at', 'desc')
                ->skip($offset)
                ->take($perPage)
                ->get();

            $is_notification = true;

            return response()->json([
                'view' => view('user.includes.notification', compact('notifications', 'is_notification'))->render(),
                'count' => $notifications->count()
            ]);
        }

        return abort(404); // Optional: return a 404 response if not an AJAX request
    }

    public function notificationRead($type, $id)
    {
        $id = $id;
        $notification = Notification::find($id);
        $notification->is_read = 1;
        $notification->update();

        if ($type == 'Chat') {
            return redirect()->route('chats.index');
        } elseif ($type == 'Team') {
            return redirect()->route('team-chats.index');
        } elseif ($type == 'Mail') {
            return redirect()->route('mail.index');
        } elseif ($type == 'topic') {
            return redirect()->route('topics.index');
        } elseif ($type == 'becoming_sovereign') {
            return redirect()->route('becoming-sovereign.index');
        } elseif ($type == 'becoming_christ_like') {
            return redirect()->route('becoming-christ-link.index');
        } elseif ($type == 'becoming_a_leader') {
            return redirect()->route('leadership-development.index');
        } elseif ($type == 'file') {
            return redirect()->route('file.index');
        } elseif ($type == 'bulletin') {
            return redirect()->route('bulletins.index');
        } elseif ($type == 'job') {
            return redirect()->route('jobs.index');
        } elseif ($type == 'meeting') {
            return redirect()->route('meetings.index');
        } elseif ($type == 'live_event') {
            return redirect()->route('events.index');
        } elseif ($type == 'product') {
            return redirect()->route('products.index');
        } elseif ($type == 'strategy') {
            return redirect()->route('strategy.index');
        } elseif ($type == 'policy') {
            return redirect()->route('policy-guidence.index');
        } elseif ($type == 'Order') {
            $order_id = $notification->chat_id;
            return redirect()->route('user.store-orders.details', $order_id);
        } elseif ($type == 'collaboration') {
            return redirect()->route('private-collaborations.index');
        }


        return abort(404);
    }

    public function notificationClear()
    {
        Notification::where('user_id', Auth::user()->id)->delete();
        return response()->json(['message' => 'Notification deleted successfully.', 'status' => true]);
    }

    public function unreadMessagesCount(Request $request)
    {
        $user = $request->user();

        // Chat::where('id', '!=', null)->update(['seen' => 1]);
        // MailUser::where('id', '!=', null)->update(['is_read' => 1]);
        // TeamChat::where('id', '!=', null)->update(['is_seen' => 1]);
        // ChatMember::where('id', '!=', null)->update(['is_seen' => 1]);

        $mailCount = MailUser::where('user_id', $user->id)
            ->where('is_delete', 0) // Check not deleted first
            ->where('is_read', 0)   // message can be deleted but not read
            ->where('is_to', 1)   // Only count mails where user is receiver
            ->count();

        // Count unread individual chats where user is receiver
        $chatCount = Chat::where('reciver_id', $user->id)
            ->where('seen', 0)
            ->where('deleted_for_reciver', 0)
            ->where('delete_from_receiver_id', 0)
            ->count();

        $all_team_chats_ids = TeamChat::pluck('id');
        $teamChatCount = ChatMember::whereIn('chat_id', $all_team_chats_ids)
            ->where('user_id', $user->id)
            ->where('is_seen', 0)
            // ->whereHas('chat', function ($query) {
            //     $query->whereNull('deleted_at');
            // })
            ->count();

        $totalCount = $mailCount + $chatCount + $teamChatCount;

        return response()->json([
            'status' => true,
            'data' => [
                'mail' => $mailCount,
                'chat' => $chatCount,
                'team_chat' => $teamChatCount,
                'total' => $totalCount,
                // 'maildata' => $originalMailCount,
            ]
        ], 200);
    }
}
