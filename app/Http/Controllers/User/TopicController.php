<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->hasRole('ADMIN')) {
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
        if (Auth::user()->hasRole('ADMIN')) {
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
            'topic_name' => 'required|string|max:255',
        ]);

        $topic = new Topic();
        $topic->topic_name = $request->topic_name;
        $topic->save();

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
        if (Auth::user()->hasRole('ADMIN')) {
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
        if (Auth::user()->hasRole('ADMIN')) {
            $request->validate([
                'topic_name' => 'required|string|max:255',
            ]);

            $topic = Topic::findOrFail(Crypt::decrypt($id));
            $topic->topic_name = $request->topic_name;
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
        if (Auth::user()->hasRole('ADMIN')) {
            $topic = Topic::findOrFail(Crypt::decrypt($id));
            $topic->delete();
            return redirect()->route('topics.index')->with('message', 'Topic deleted successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
