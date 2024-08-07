<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class JobpostingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->can('Manage Job Postings')) {
            $jobs = Job::orderBy('id', 'desc')->paginate(15);
            return view('user.job.list')->with(compact('jobs'));
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
        if (auth()->user()->can('Create Job Postings')) {
            return view('user.job.create');
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
        if (auth()->user()->can('Create Job Postings')) {
            $request->validate([
                'job_title' => 'required',
                'job_description' => 'required',
                'job_type' => 'required',
                'job_location' => 'required',
                'job_salary' => 'nullable|numeric',
                'job_experience' => 'nullable|numeric',
                'contact_person' => 'nullable',
                'contact_email' => 'nullable|email',
            ]);

            $job = new Job();
            $job->created_by = auth()->id();
            $job->job_title = $request->job_title;
            $job->job_description = $request->job_description;
            $job->job_type = $request->job_type;
            $job->job_location = $request->job_location;
            $job->job_salary = $request->job_salary;
            $job->job_experience = $request->job_experience;
            $job->contact_person = $request->contact_person;
            $job->contact_email = $request->contact_email;
            $job->save();

            return redirect()->route('jobs.index')->with('message', 'Job has been created successfully.');
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
        if (auth()->user()->can('View Job Postings')) {
            $job = Job::findOrFail($id);
            return view('user.job.show')->with(compact('job'));
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
        if (auth()->user()->can('Edit Job Postings')) {
            $job = Job::findOrFail($id);
            return view('user.job.edit')->with(compact('job'));
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
        if (auth()->user()->can('Edit Job Postings')) {
            $request->validate([
                'job_title' => 'required',
                'job_description' => 'required',
                'job_type' => 'required',
                'job_location' => 'required',
                'job_salary' => 'nullable|numeric',
                'job_experience' => 'nullable|numeric',
                'contact_person' => 'nullable',
                'contact_email' => 'nullable|email',
            ]);

            $job = Job::findOrFail($id);
            $job->job_title = $request->job_title;
            $job->job_description = $request->job_description;
            $job->job_type = $request->job_type;
            $job->job_location = $request->job_location;
            $job->job_salary = $request->job_salary;
            $job->job_experience = $request->job_experience;
            $job->contact_person = $request->contact_person;
            $job->contact_email = $request->contact_email;
            $job->save();

            return redirect()->route('jobs.index')->with('message', 'Job has been updated successfully.');
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

            $jobs = Job::query()
                ->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhere('job_title', 'like', '%' . $query . '%')
                        ->orWhere('job_description', 'like', '%' . $query . '%')
                        ->orWhere('job_type', 'like', '%' . $query . '%')
                        ->orWhere('job_location', 'like', '%' . $query . '%')
                        ->orWhere('job_salary', 'like', '%' . $query . '%')
                        ->orWhere('job_experience', 'like', '%' . $query . '%')
                        ->orWhere('contact_person', 'like', '%' . $query . '%')
                        ->orWhere('contact_email', 'like', '%' . $query . '%');
                });


            $jobs->orderBy($sort_by, $sort_type);
            $jobs = $jobs->paginate(10);

            return response()->json(['data' => view('user.job.table', compact('jobs'))->render()]);
        }
    }
    public function delete($id)
    {
        if (Auth::user()->can('Delete Job Postings')) {
            $job = Job::findOrFail($id);
            $job->delete();
            return redirect()->route('jobs.index')->with('error', 'Job has been deleted successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
