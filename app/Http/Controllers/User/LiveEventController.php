<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $events = Event::orderBy('id', 'desc')->get(['id', 'user_id' ,'title', 'description', 'start', 'end']);
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

        return redirect()->route('events.index')->with('message', 'Event created successfully.');
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

        return redirect()->route('events.index')->with('message', 'Event updated successfully.');
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json(['success' => 'Event deleted successfully.']);
    }
}
