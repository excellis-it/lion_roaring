<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ElearningTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Services\NotificationService;

class ElearningTopicController extends Controller
{
    public function index()
    {
        if (auth()->user()->can('Manage Elearning Topic')) {
            $topics = ElearningTopic::orderBy('id', 'desc')->paginate(15);
            return view('user.elearning-topic.list')->with('topics', $topics);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function create()
    {
        if (auth()->user()->can('Create Elearning Topic')) {
            return view('user.elearning-topic.create');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'topic_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('elearning_topics'),
            ],
        ]);

        $topic = new ElearningTopic();
        $topic->topic_name = $request->topic_name;
        $topic->save();

        $userName = auth()->user()->getFullNameAttribute();
        $noti = NotificationService::notifyAllUsers('New Elearning Topic created by ' . $userName, 'topic');

        return redirect()->route('elearning-topics.index')->with('message', 'Elearning Topic created successfully.');
    }

    public function edit($id)
    {
        if (auth()->user()->can('Edit Elearning Topic')) {
            $topic = ElearningTopic::findOrFail(Crypt::decrypt($id));
            return view('user.elearning-topic.edit')->with('topic', $topic);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->can('Edit Elearning Topic')) {
            $id = Crypt::decrypt($id);
            $request->validate([
                'topic_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('elearning_topics')->ignore($id),
                ],
            ]);

            $topic = ElearningTopic::findOrFail($id);
            $topic->topic_name = $request->topic_name;
            $topic->save();

            return redirect()->route('elearning-topics.index')->with('message', 'Elearning Topic updated successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function delete($id)
    {
        if (auth()->user()->can('Delete Elearning Topic')) {
            $topic = ElearningTopic::findOrFail(Crypt::decrypt($id));
            Log::info($topic->topic_name . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
            $topic->delete();
            return redirect()->route('elearning-topics.index')->with('message', 'Elearning Topic deleted successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
