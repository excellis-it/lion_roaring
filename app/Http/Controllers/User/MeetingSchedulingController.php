<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingSchedulingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->can('Manage Meeting Schedule')) {
            $meetings = Meeting::orderBy('id', 'desc')->paginate(15);
            return view('user.meeting.list')->with(compact('meetings'));
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
        if (auth()->user()->can('Create Meeting Schedule')) {
            return view('user.meeting.create');
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
        if (auth()->user()->can('Create Meeting Schedule')) {
            $request->validate([
                'title' => 'required',
                'description' => 'required',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
                'meeting_link' => 'nullable',
            ]);

            $meeting = new Meeting();
            $meeting->user_id = auth()->id();
            $meeting->title = $request->title;
            $meeting->description = $request->description;
            $meeting->start_time = $request->start_time;
            $meeting->end_time = $request->end_time;
            $meeting->meeting_link = $request->meeting_link;
            $meeting->save();

            session()->flash('message', 'Meeting scheduled successfully.');

            return response()->json(['status' => true, 'message' => 'Meeting scheduled successfully.', 'meeting' => $meeting]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (auth()->user()->can('View Meeting Schedule')) {
            $meeting = Meeting::find($id);
            return view('user.meeting.show')->with('meeting', $meeting);
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
        if (auth()->user()->can('Edit Meeting Schedule') && auth()->user()->id == Meeting::find($id)->user_id || auth()->user()->hasRole('ADMIN')) {
            $meeting = Meeting::find($id);
            return view('user.meeting.edit')->with('meeting', $meeting);
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
        if (auth()->user()->can('Edit Meeting Schedule')) {
            $request->validate([
                'title' => 'required',
                'description' => 'required',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
                'meeting_link' => 'nullable',
            ]);

            $meeting = Meeting::find($id);
            $meeting->title = $request->title;
            $meeting->description = $request->description;
            $meeting->start_time = $request->start_time;
            $meeting->end_time = $request->end_time;
            $meeting->meeting_link = $request->meeting_link;
            $meeting->save();

            session()->flash('message', 'Meeting updated successfully.');
            return response()->json(['status' => true, 'message' => 'Meeting updated successfully.', 'id' => $id]);
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

            $meetings = Meeting::query()
                ->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhere('title', 'like', '%' . $query . '%')
                        ->orWhere('description', 'like', '%' . $query . '%')
                        ->orWhere('start_time', 'like', '%' . $query . '%')
                        ->orWhere('end_time', 'like', '%' . $query . '%')
                        ->orWhere('meeting_link', 'like', '%' . $query . '%');
                });


            $meetings->orderBy($sort_by, $sort_type);
            $meetings = $meetings->paginate(15);

            return response()->json(['data' => view('user.meeting.table', compact('meetings'))->render()]);
        }
    }
    public function delete($id)
    {
        if (Auth::user()->can('Delete Meeting Schedule') && Auth::user()->id == Meeting::find($id)->user_id || Auth::user()->hasRole('ADMIN')) {
            $meeting = Meeting::findOrFail($id);
            $meeting->delete();
            return response()->json(['message' => 'Meeting deleted successfully.', 'status' => true, 'id' => $id]);
        } else {
            return response()->json(['message' => 'You do not have permission to delete this meeting.', 'status' => false]);
        }
    }

    public function viewCalender()
    {
        if (auth()->user()->can('Manage Meeting Schedule')) {
            $meetings = Meeting::orderBy('id', 'desc')->get();
            return view('user.meeting.calender')->with(compact('meetings'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function fetchCalenderData()
    {
        if (auth()->user()->can('Manage Meeting Schedule')) {
            $meetings = Meeting::orderBy('id', 'desc')->get(['id', 'title', 'description', 'start_time as start', 'end_time as end', 'meeting_link']);
            return response()->json($meetings);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function showSingleMeeting(Request $request)
    {
        $meeting = Meeting::find($request->meeting_id);
        return response()->json(['status' => true, 'view' => view('user.meeting.show-single-meeting', compact('meeting'))->render()]);
    }
}
