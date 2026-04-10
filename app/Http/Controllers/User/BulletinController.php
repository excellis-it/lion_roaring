<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bulletin;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Services\NotificationService;

class BulletinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->can('Manage Bulletin')) {
            $user = auth()->user();
            $user_type = auth()->user()->user_type;
            $user_country = auth()->user()->country;
            $currentCountry = Country::findByCurrentRequest();
            $isOnGlobalServer = $currentCountry && $currentCountry->is_global;


            if (Auth::user()->hasNewRole('SUPER ADMIN')) {
                $bulletins = Bulletin::orderBy('id', 'desc')->paginate(15);
            } else {
                if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
                    $bulletins = Bulletin::orderBy('id', 'desc')->whereHas('country', function ($query) {
                        $query->where('code', 'GL');
                    })->whereHas('user', function ($query) {
                        $query->whereIn('user_type', ['Global', 'G_R'])->where('status', 1);
                    })->paginate(15);
                } else {
                    $bulletinsQuery = Bulletin::where('country_id', $user_country)->orderBy('id', 'desc')->whereHas('user', function ($query) {
                        $query->whereIn('user_type', ['Regional', 'G_R'])->where('status', 1);
                    });
                    if ($user->is_ecclesia_admin == 1) {
                        $manage_ecclesia_ids = is_array($user->manage_ecclesia)
                            ? $user->manage_ecclesia
                            : explode(',', $user->manage_ecclesia ?? '');
                        $bulletinsQuery->where(function ($q) use ($manage_ecclesia_ids, $user) {
                            $q->whereHas('user', function ($uq) use ($manage_ecclesia_ids) {
                                $uq->where(function ($sub) use ($manage_ecclesia_ids) {
                                    $sub->whereIn('ecclesia_id', $manage_ecclesia_ids)->whereNotNull('ecclesia_id');
                                    foreach ($manage_ecclesia_ids as $id) {
                                        $sub->orWhereRaw('FIND_IN_SET(?, manage_ecclesia)', [trim($id)]);
                                    }
                                });
                            })->orWhere('user_id', $user->id);
                        });
                    }
                    $bulletins = $bulletinsQuery->paginate(15);
                }
            }

            return view('user.bulletin.list')->with(compact('bulletins'));
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
        if (Auth::user()->can('Create Bulletin')) {
            $countries = Country::orderBy('name', 'asc')->get();
            return view('user.bulletin.create')->with(compact('countries'));
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
        if (Auth::user()->can('Create Bulletin')) {
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
                'title' => 'required',
                'description' => 'required',
                'country_id' => 'required',
            ], [
                'title.required' => 'The title field is required.',
                'description.required' => 'The message field is required.',
                'country_id.required' => 'The country field is required.',
            ]);

            $bulletin = new Bulletin();
            $bulletin->user_id = Auth::user()->id;
            $bulletin->title = $request->title;
            $bulletin->description = $request->description;
            $bulletin->country_id = $request->country_id;
            $bulletin->save();


            $userName = Auth::user()->getFullNameAttribute();
            $noti = NotificationService::notifyAllUsers('New Bulletin created by ' . $userName, 'bulletin');

            session()->flash('message', 'Bulletin created successfully');
            return response()->json(['message' => 'Bulletin created successfully', 'status' => true, 'bulletin' => $bulletin]);
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
        if (Auth::user()->can('Edit Bulletin')) {
            $user = auth()->user();
            $user_type = $user->user_type;
            $user_country = $user->country;
            $currentCountry = Country::findByCurrentRequest();
            $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

            if (auth()->user()->hasNewRole('SUPER ADMIN')) {
                $bulletin = Bulletin::find($id);
            } else {
                if ($user_type == 'Global' || ($user_type == 'G_R' && $isOnGlobalServer)) {
                    $bulletin = Bulletin::where('user_id', Auth::user()->id)->find($id);
                } else {
                    $bulletin = Bulletin::where('user_id', Auth::user()->id)->where('country_id', $user_country)->find($id);
                }
            }
            $countries = Country::orderBy('name', 'asc')->get();
            if ($bulletin) {
                return view('user.bulletin.edit')->with('bulletin', $bulletin)->with('countries', $countries);
            }
            return redirect()->back()->with('error', 'Bulletin not found');
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
        if (Auth::user()->can('Edit Bulletin')) {
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

            if (auth()->user()->hasNewRole('SUPER ADMIN')) {
                $bulletin = Bulletin::find($id);
            } else {
                if (auth()->user()->user_type == 'Global') {
                    $bulletin = Bulletin::where('user_id', Auth::user()->id)->find($id);
                } else {
                    $bulletin = Bulletin::where('user_id', Auth::user()->id)->where('country_id', auth()->user()->country)->find($id);
                }
            }
            if ($bulletin) {
                $request->validate([
                    'title' => 'required',
                    'description' => 'required',
                    'country_id' => 'required',
                ], [
                    'title.required' => 'The title field is required.',
                    'description.required' => 'The message field is required.',
                    'country_id.required' => 'The country field is required.',
                ]);

                $bulletin->title = $request->title;
                $bulletin->description = $request->description;
                $bulletin->country_id = $request->country_id;
                $bulletin->save();

                session()->flash('message', 'Bulletin updated successfully');
                return response()->json(['message' => 'Bulletin updated successfully', 'status' => true, 'bulletin' => $bulletin]);
            }
            return redirect()->back()->with('error', 'Bulletin not found');
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
        if (Auth::user()->can('Delete Bulletin')) {
            if (auth()->user()->hasNewRole('SUPER ADMIN')) {
                $bulletin = Bulletin::find($id);
            } else {
                $bulletin = Bulletin::where('user_id', Auth::user()->id)->find($id);
            }
            if ($bulletin) {
                Log::info($bulletin->title . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
                $bulletin->delete();
                return response()->json(['message' => 'Bulletin deleted successfully', 'status' => true, 'bulletin' => $bulletin]);
            }
            return response()->json(['message' => 'Bulletin not found', 'status' => false]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function fetchData(Request $request)
    {
        if ($request->ajax()) {
            $sort_by = $request->get('sortby', 'id'); // Default sort by 'id'
            $sort_type = $request->get('sorttype', 'asc'); // Default sort type 'asc'
            $query = $request->get('query', '');
            $query = str_replace(" ", "%", $query);
            $currentCountry = Country::findByCurrentRequest();
            $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

            if ($request->ajax()) {
                $sort_by = $request->get('sortby', 'id'); // Default sort by 'id'
                $sort_type = $request->get('sorttype', 'asc'); // Default sort type 'asc'
                $query = $request->get('query', '');
                $query = str_replace(" ", "%", $query);

                if (Auth::user()->hasNewRole('SUPER ADMIN')) {
                    $bulletins = Bulletin::query()
                        ->where('title', 'like', '%' . $query . '%')
                        ->orWhere('description', 'like', '%' . $query . '%')
                        ->orWhereHas('user', function ($q) use ($query) {
                            $q->whereRaw("CONCAT(first_name, ' ', IFNULL(middle_name,''), ' ', last_name) LIKE ?", ['%' . $query . '%']);
                        });
                } else {
                    $bulletins = Bulletin::query()
                        ->where('user_id', Auth::user()->id)
                        ->where(function ($q) use ($query) {
                            $q->where('title', 'like', '%' . $query . '%')
                                ->orWhere('description', 'like', '%' . $query . '%')
                                ->orWhereHas('user', function ($subQuery) use ($query) {
                                    $subQuery->whereRaw("CONCAT(first_name, ' ', IFNULL(middle_name,''), ' ', last_name) LIKE ?", ['%' . $query . '%']);
                                });
                        });
                }

                if (!Auth::user()->hasNewRole('SUPER ADMIN')) {
                    if (auth()->user()->user_type == 'Global' || (auth()->user()->user_type == 'G_R' && $isOnGlobalServer)) {
                        $bulletins = $bulletins->orderBy($sort_by, $sort_type)->whereHas('country', function ($query) {
                            $query->where('code', 'GL');
                        })->paginate(15);
                    } else {
                        $bulletins = $bulletins->where('country_id', auth()->user()->country)->whereHas('user', function ($query) {
                            $query->whereIn('user_type', ['Regional', 'G_R']);
                        });
                        // dd($bulletins->get()->toArray());
                        if (auth()->user()->is_ecclesia_admin == 1) {
                            $manage_ecclesia_ids = is_array(auth()->user()->manage_ecclesia)
                                ? auth()->user()->manage_ecclesia
                                : explode(',', auth()->user()->manage_ecclesia ?? '');
                            $bulletins = $bulletins->where(function ($q) use ($manage_ecclesia_ids) {
                                $q->whereHas('user', function ($uq) use ($manage_ecclesia_ids) {
                                    $uq->whereIn('ecclesia_id', $manage_ecclesia_ids);
                                })->orWhere('user_id', auth()->user()->id);
                            });
                        }
                        $bulletins = $bulletins->orderBy($sort_by, $sort_type)->paginate(15);
                    }
                } else {
                    $bulletins = $bulletins->orderBy($sort_by, $sort_type)->paginate(15);
                }
            }


            return response()->json(['data' => view('user.bulletin.table', compact('bulletins'))->render()]);
        }
    }

    public function loadTable(Request $request)
    {
        if (Auth::user()->hasNewRole('SUPER ADMIN')) {
            $bulletins = Bulletin::orderBy('id', 'desc')->paginate(15);
        } else {
            $bulletins = Bulletin::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->paginate(15);
        }

        return response()->json(['view' => view('user.bulletin.table', compact('bulletins'))->render()]);
    }

    public function single(Request $request)
    {
        if (Auth::user()->hasNewRole('SUPER ADMIN')) {
            $bulletins = Bulletin::orderBy('id', 'desc')->paginate(15);
        } else {
            $bulletins = Bulletin::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->paginate(15);
        }
        $bulletin = Bulletin::find($request->bulletin_id);
        return response()->json(['view' => view('user.bulletin.show-single-bulletin', compact('bulletin', 'bulletins'))->render()]);
    }
}
