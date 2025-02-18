<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Ecclesia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class EcclesiaContorller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->can('Manage Role Permission')) {
            $ecclesias = Ecclesia::orderBy('id', 'asc')->paginate(15);
            return view('user.ecclesias.list')->with('ecclesias', $ecclesias);
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
        if (Auth::user()->can('Manage Role Permission')) {
            $countries = Country::orderBy('name', 'asc')->get();
            return view('user.ecclesias.create')->with('countries', $countries);
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
            'name' => 'required|string|max:255|unique:ecclesias',
            'country' => 'required|string|max:255',
        ]);

        $ecclesia = new Ecclesia();
        $ecclesia->name = $request->name;
        $ecclesia->country = $request->country;
        $ecclesia->save();

        return redirect()->route('ecclesias.index')->with('message', 'Ecclesia created successfully.');
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
        if (Auth::user()->can('Manage Role Permission')) {
            $ecclesia = Ecclesia::findOrFail(Crypt::decrypt($id));
            $countries = Country::orderBy('name', 'asc')->get();
            return view('user.ecclesias.edit')->with(compact('ecclesia', 'countries'));
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
        if (Auth::user()->can('Manage Role Permission')) {
            $request->validate([
                'name' => 'required|string|max:255|unique:ecclesias,name,' . Crypt::decrypt($id),
                'country' => 'required|string|max:255',
            ]);

            $ecclesia = Ecclesia::findOrFail(Crypt::decrypt($id));
            $ecclesia->name = $request->name;
            $ecclesia->country = $request->country;
            $ecclesia->save();

            return redirect()->route('ecclesias.index')->with('message', 'Ecclesia updated successfully.');
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
        if (Auth::user()->can('Manage Role Permission')) {
            $ecclesia = Ecclesia::findOrFail(Crypt::decrypt($id));
            Log::info($ecclesia->name . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
            $ecclesia->delete();
            return redirect()->route('ecclesias.index')->with('message', 'Ecclesia deleted successfully.');
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

            $ecclesias = Ecclesia::query()
                ->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhere('name', 'like', '%' . $query . '%');
                })
                ->orWhereHas('countryName', function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%');
                })
                ->orderBy($sort_by, $sort_type)
                ->paginate(15);

            return response()->json(['data' => view('user.ecclesias.table', compact('ecclesias'))->render()]);
        }
    }
}
