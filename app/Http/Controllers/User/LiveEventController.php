<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
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
            $events = Event::orderBy('id', 'desc')->get();
            return view('user.events.list', compact('events'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }


    public function calender(Request $request)
    {
        $events = Event::orderBy('id', 'desc')->get(['id', 'user_id', 'title', 'description', 'start', 'end']);
        return response()->json($events);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'start' => 'required',
            'end' => 'required',
        ]);

        $event = new Event();
        $event->user_id = Auth::id();
        $event->title = $request->title;
        $event->description = $request->description;
        $event->start = $request->start;
        $event->end = $request->end;
        $event->save();

        // notify users
        $userName = Auth::user()->getFullNameAttribute();
        $noti = NotificationService::notifyAllUsers('New Live Event created by ' . $userName, 'live_event');

        return response()->json(['message' => 'Event created successfully.', 'event' => $event, 'status' => true]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'start' => 'required',
            'end' => 'required',
        ]);

        $event = Event::find($id);
        $event->title = $request->title;
        $event->description = $request->description;
        $event->start = $request->start;
        $event->end = $request->end;
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
