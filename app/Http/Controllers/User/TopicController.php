<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Services\NotificationService;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->can('Manage Topic')) {
            $topics = Topic::orderBy('id', 'desc')->paginate(15);
            return view('user.topics.list')->with('topics', $topics);
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
        if (Auth::user()->can('Create Topic')) {
            return view('user.topics.create');
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
            'topic_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('topics')->where(function ($query) use ($request) {
                    return $query->where('education_type', $request->education_type);
                }),
            ],
            'education_type' => 'required|string|max:255',
        ]);

        $topic = new Topic();
        $topic->topic_name = $request->topic_name;
        $topic->education_type = $request->education_type;
        $topic->save();

        $userName = Auth::user()->getFullNameAttribute();
        $noti = NotificationService::notifyAllUsers('New Topic created by ' . $userName, 'topic');



        return redirect()->route('topics.index')->with('message', 'Topic created successfully.');
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
        if (Auth::user()->can('Edit Topic')) {
            $topic = Topic::findOrFail(Crypt::decrypt($id));
            return view('user.topics.edit')->with('topic', $topic);
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
        if (Auth::user()->can('Edit Topic')) {
            $id = Crypt::decrypt($id);
            $request->validate([
                'topic_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('topics')->ignore($id)->where(function ($query) use ($request) {
                        return $query->where('education_type', $request->education_type);
                    }),
                ],
                'education_type' => 'required|string|max:255',
            ]);

            $topic = Topic::findOrFail($id);
            $topic->topic_name = $request->topic_name;
            $topic->education_type = $request->education_type;
            $topic->save();

            return redirect()->route('topics.index')->with('message', 'Topic updated successfully.');
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

    public function delete($id)
    {
        if (Auth::user()->can('Delete Topic')) {
            $topic = Topic::findOrFail(Crypt::decrypt($id));
            Log::info($topic->topic_name . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
            $topic->delete();
            return redirect()->route('topics.index')->with('message', 'Topic deleted successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
