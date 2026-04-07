<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;


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
            $user = auth()->user();
            $user_type = $user->user_type;
            $user_country = $user->country;
            $currentCountry = Country::findByCurrentRequest();
            $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

            if (!$user->hasNewRole('SUPER ADMIN')) {
                if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
                    $jobs = Job::orderBy('id', 'desc')->whereHas('country', function ($query) {
                        $query->where('code', 'GL');
                    })->whereHas('user', function ($query) {
                        $query->whereIn('user_type', ['Global', 'G_R']);
                    })->paginate(15);
                } else {
                    $jobsQuery = Job::where('country_id', $user_country)->orderBy('id', 'desc')->whereHas('user', function ($query) {
                        $query->whereIn('user_type', ['Regional', 'G_R']);
                    });

                    if ($user->is_ecclesia_admin == 1) {
                        $manage_ecclesia_ids = is_array($user->manage_ecclesia)
                            ? $user->manage_ecclesia
                            : explode(',', $user->manage_ecclesia ?? '');
                        $jobsQuery->where(function ($q) use ($manage_ecclesia_ids, $user) {
                            $q->whereHas('user', function ($uq) use ($manage_ecclesia_ids) {
                                $uq->whereIn('ecclesia_id', $manage_ecclesia_ids);
                            })->orWhere('created_by', $user->id);
                        });
                    }
                    $jobs = $jobsQuery->paginate(15);
                }
            } else {
                $jobs = Job::orderBy('id', 'desc')->paginate(15);
            }
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
            $user_type = auth()->user()->user_type;
            $user_country = auth()->user()->country;

            $countries = Country::orderBy('name', 'asc')->get();
            return view('user.job.create')->with(compact('countries'));
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
            $user = auth()->user();
            $user_type = $user->user_type;
            $user_country = $user->country;
            $country_id_ex = null;
            $currentCountry = Country::findByCurrentRequest();
            $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

            if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
                $country = Country::where('code', 'GL')->first();
                $country_id_ex = $country->id;
            } else {
                $country_id_ex = $user_country;
            }

            $country_id = auth()->user()->hasNewRole('SUPER ADMIN') ? $request->country_id : $country_id_ex;

            $request->merge(['country_id' => $country_id]);

            $request->validate([
                'job_title' => 'required',
                'job_description' => 'required',
                'job_type' => 'required',
                'job_location' => 'required',
                'job_salary' => 'nullable|numeric',
                'currency' => 'nullable',
                'job_experience' => 'nullable|numeric',
                'contact_person' => 'nullable',
                'contact_email' => 'nullable|email',
                'list_of_values' => 'nullable|required_with:job_salary',
                'country_id' => 'required',
            ]);

            $job = new Job();
            $job->created_by = auth()->id();
            $job->job_title = $request->job_title;
            $job->job_description = $request->job_description;
            $job->job_type = $request->job_type;
            $job->job_location = $request->job_location;
            $job->job_salary = $request->job_salary;
            $job->currency = $request->currency;
            $job->job_experience = $request->job_experience;
            $job->contact_person = $request->contact_person;
            $job->contact_email = $request->contact_email;
            $job->list_of_values = $request->list_of_values;
            $job->country_id = $request->country_id;
            $job->save();

            $userName = Auth::user()->getFullNameAttribute();
            $noti = NotificationService::notifyAllUsers('New Job created by ' . $userName, 'job');

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
            $user = auth()->user();
            $user_type = $user->user_type;
            $user_country = $user->country;

            if (!$user->hasNewRole('SUPER ADMIN')) {
                $currentCountry = Country::findByCurrentRequest();
                $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

                if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
                    $job = Job::whereHas('country', function ($query) {
                        $query->where('code', 'GL');
                    })->findOrFail($id);
                } else {
                    $job = Job::where('country_id', $user_country)->findOrFail($id);
                }
            } else {
                $job = Job::findOrFail($id);
            }
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
        $user = auth()->user();
        $user_type = $user->user_type;
        $user_country = $user->country;

        $countries = Country::orderBy('name', 'asc')->get();
        if (!$user->hasNewRole('SUPER ADMIN')) {
            $currentCountry = Country::findByCurrentRequest();
            $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

            if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
                $job = Job::whereHas('country', function ($query) {
                    $query->where('code', 'GL');
                })->findOrFail($id);
            } else {
                $job = Job::where('country_id', $user_country)->findOrFail($id);
            }
        } else {
            $job = Job::findOrFail($id);
        }

        if ((auth()->user()->can('Edit Job Postings')  && $job->created_by == auth()->user()->id) || auth()->user()->hasNewRole('SUPER ADMIN')) {
            return view('user.job.edit')->with(compact('job', 'countries'));
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
            $job = Job::findOrFail($id);

            // Only the creator or SUPER ADMIN can update
            if (!auth()->user()->hasNewRole('SUPER ADMIN') && $job->created_by != auth()->id()) {
                abort(403, 'You do not have permission to edit this job posting.');
            }

            $user = auth()->user();
            $user_type = $user->user_type;
            $user_country = $user->country;
            $country_id_ex = null;
            $currentCountry = Country::findByCurrentRequest();
            $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

            if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
                $country = Country::where('code', 'GL')->first();
                $country_id_ex = $country->id;
            } else {
                $country_id_ex = $user_country;
            }

            $country_id = auth()->user()->hasNewRole('SUPER ADMIN') ? $request->country_id : $country_id_ex;

            $request->merge(['country_id' => $country_id]);

            $request->validate([
                'job_title' => 'required',
                'job_description' => 'required',
                'job_type' => 'required',
                'job_location' => 'required',
                'job_salary' => 'nullable|numeric',
                'job_experience' => 'nullable|numeric',
                'contact_person' => 'nullable',
                'contact_email' => 'nullable|email',
                'currency' => 'nullable',
                'list_of_values' => 'nullable|required_with:job_salary',
                'country_id' => 'required',
            ]);

            $job->job_title = $request->job_title;
            $job->job_description = $request->job_description;
            $job->job_type = $request->job_type;
            $job->job_location = $request->job_location;
            $job->job_salary = $request->job_salary;
            $job->currency = $request->currency;
            $job->job_experience = $request->job_experience;
            $job->contact_person = $request->contact_person;
            $job->contact_email = $request->contact_email;
            $job->list_of_values = $request->list_of_values;
            $job->country_id = $request->country_id;
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
                        ->orWhereHas('country', function ($q) use ($query) {
                            $q->where('name', 'like', '%' . $query . '%');
                        })
                        ->orWhere('contact_email', 'like', '%' . $query . '%');
                });

            $user = auth()->user();
            $user_type = $user->user_type;
            $user_country = $user->country;

            if (!$user->hasNewRole('SUPER ADMIN')) {
                $currentCountry = Country::findByCurrentRequest();
                $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

                if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
                    $jobs = $jobs->whereHas('country', function ($query) {
                        $query->where('code', 'GL');
                    })->whereHas('user', function ($query) {
                        $query->whereIn('user_type', ['Global', 'G_R']);
                    })->orderBy($sort_by, $sort_type)->paginate(10);
                } else {
                    $jobs = $jobs->where('country_id', $user_country)->whereHas('user', function ($query) {
                        $query->whereIn('user_type', ['Regional', 'G_R']);
                    });

                    if ($user->is_ecclesia_admin == 1) {
                        $manage_ecclesia_ids = is_array($user->manage_ecclesia)
                            ? $user->manage_ecclesia
                            : explode(',', $user->manage_ecclesia ?? '');
                        $jobs->where(function ($q) use ($manage_ecclesia_ids, $user) {
                            $q->whereHas('user', function ($uq) use ($manage_ecclesia_ids) {
                                $uq->whereIn('ecclesia_id', $manage_ecclesia_ids);
                            })->orWhere('created_by', $user->id);
                        });
                    }

                    $jobs = $jobs->orderBy($sort_by, $sort_type)->paginate(10);
                }
            } else {
                $jobs = $jobs->orderBy($sort_by, $sort_type)->paginate(10);
            }


            return response()->json(['data' => view('user.job.table', compact('jobs'))->render()]);
        }
    }
    public function delete($id)
    {
        $job = Job::findOrFail($id);
        if ((Auth::user()->can('Delete Job Postings')  && $job->created_by == auth()->user()->id) || auth()->user()->hasNewRole('SUPER ADMIN')) {
            Log::info($job->job_title . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
            $job->delete();
            return redirect()->route('jobs.index')->with('error', 'Job has been deleted successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
