<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;

class LiveEventController extends Controller
{
    public function list()
    {
        if (Auth::user()->can('Manage Event')) {
            $user_type = auth()->user()->user_type;
            $user_country = auth()->user()->country;
            if ($user_type == 'Global') {
                $events = Event::orderBy('id', 'desc')->get();
            } else {
                $events = Event::where('country_id', $user_country)->orderBy('id', 'desc')->get();
            }
            $country = Country::orderBy('name', 'asc')->get();
            return view('user.events.list', compact('events', 'country'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }


    public function calender(Request $request)
    {
        $user_type = auth()->user()->user_type;
        $user_country = auth()->user()->country;
        if ($user_type == 'Global') {
            $events = Event::orderBy('id', 'desc')->get(['id', 'user_id', 'title', 'description', 'start', 'end', 'country_id']);
        } else {
            $events = Event::where('country_id', $user_country)->orderBy('id', 'desc')->get(['id', 'user_id', 'title', 'description', 'start', 'end', 'country_id']);
        }
        return response()->json($events);
    }

    public function store(Request $request)
    {
        $country_id = auth()->user()->user_type === 'Global'
            ? $request->country_id
            : auth()->user()->country;

        $request->merge(['country_id' => $country_id]);
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'start' => 'required',
            'end' => 'required',
            'country_id' => 'required',
        ]);

        $event = new Event();
        $event->user_id = Auth::id();
        $event->time_zone = auth()->user()->time_zone;
        $event->title = $request->title;
        $event->description = $request->description;
        $event->start = $request->start;
        $event->end = $request->end;
        $event->country_id = $request->country_id;
        $event->save();

        // notify users
        $userName = Auth::user()->getFullNameAttribute();
        $noti = NotificationService::notifyAllUsers('New Live Event created by ' . $userName, 'live_event');

        return response()->json(['message' => 'Event created successfully.', 'event' => $event, 'status' => true]);
    }

    public function update(Request $request, $id)
    {
        $country_id = auth()->user()->user_type === 'Global'
            ? $request->country_id
            : auth()->user()->country;

        $request->merge(['country_id' => $country_id]);
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'start' => 'required',
            'end' => 'required',
            'country_id' => 'required',
        ]);

        $event = Event::find($id);
        $event->title = $request->title;
        $event->time_zone = auth()->user()->time_zone;
        $event->description = $request->description;
        $event->start = $request->start;
        $event->end = $request->end;
        $event->country_id = $request->country_id;
        $event->update();

        return response()->json(['message' => 'Event updated successfully.', 'event' => $event, 'status' => true]);
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        Log::info($event->title . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
        $event->delete();

        return response()->json(['success' => 'Event deleted successfully.']);
    }
}
