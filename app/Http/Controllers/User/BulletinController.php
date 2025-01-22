<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bulletin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

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
            if (Auth::user()->hasRole('SUPER ADMIN')) {
                $bulletins = Bulletin::orderBy('id', 'desc')->paginate(15);
            } else {
                $bulletins = Bulletin::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->paginate(15);
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
            return view('user.bulletin.create');
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
            $request->validate([
                'title' => 'required',
                'description' => 'required',
            ], [
                'title.required' => 'The title field is required.',
                'description.required' => 'The message field is required.',

            ]);

            $bulletin = new Bulletin();
            $bulletin->user_id = Auth::user()->id;
            $bulletin->title = $request->title;
            $bulletin->description = $request->description;
            $bulletin->save();
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
            if (auth()->user()->hasRole('SUPER ADMIN')) {
                $bulletin = Bulletin::find($id);
            } else {
                $bulletin = Bulletin::where('user_id', Auth::user()->id)->find($id);
            }
            if ($bulletin) {
                return view('user.bulletin.edit')->with('bulletin', $bulletin);
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
            if (auth()->user()->hasRole('SUPER ADMIN')) {
                $bulletin = Bulletin::find($id);
            } else {
                $bulletin = Bulletin::where('user_id', Auth::user()->id)->find($id);
            }
            if ($bulletin) {
                $request->validate([
                    'title' => 'required',
                    'description' => 'required',
                ], [
                    'title.required' => 'The title field is required.',
                    'description.required' => 'The message field is required.',
                ]);

                $bulletin->title = $request->title;
                $bulletin->description = $request->description;
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
            if (auth()->user()->hasRole('SUPER ADMIN')) {
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

            if ($request->ajax()) {
                $sort_by = $request->get('sortby', 'id'); // Default sort by 'id'
                $sort_type = $request->get('sorttype', 'asc'); // Default sort type 'asc'
                $query = $request->get('query', '');
                $query = str_replace(" ", "%", $query);

                if (Auth::user()->hasRole('SUPER ADMIN')) {
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

                $bulletins = $bulletins->orderBy($sort_by, $sort_type)->paginate(15);
            }


            return response()->json(['data' => view('user.bulletin.table', compact('bulletins'))->render()]);
        }
    }

    public function loadTable(Request $request)
    {
        if (Auth::user()->hasRole('SUPER ADMIN')) {
            $bulletins = Bulletin::orderBy('id', 'desc')->paginate(15);
        } else {
            $bulletins = Bulletin::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->paginate(15);
        }

        return response()->json(['view' => view('user.bulletin.table', compact('bulletins'))->render()]);
    }

    public function single(Request $request)
    {
        if (Auth::user()->hasRole('SUPER ADMIN')) {
            $bulletins = Bulletin::orderBy('id', 'desc')->paginate(15);
        } else {
            $bulletins = Bulletin::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->paginate(15);
        }
        $bulletin = Bulletin::find($request->bulletin_id);
        return response()->json(['view' => view('user.bulletin.show-single-bulletin', compact('bulletin', 'bulletins'))->render()]);
    }
}
