<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Ecclesia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class EcclesiaContorller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->hasRole('ADMIN')) {
            $ecclesias = Ecclesia::orderBy('id', 'desc')->paginate(15);
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
        if (Auth::user()->hasRole('ADMIN')) {
            return view('user.ecclesias.create');
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
            'name' => 'required|string|max:255',
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
        if (Auth::user()->hasRole('ADMIN')) {
            $ecclesia = Ecclesia::findOrFail(Crypt::decrypt($id));
            return view('user.ecclesias.edit')->with('ecclesia', $ecclesia);
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
                'name' => 'required|string|max:255',
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
        if (Auth::user()->hasRole('ADMIN')) {
            $ecclesia = Ecclesia::findOrFail(Crypt::decrypt($id));
            $ecclesia->delete();
            return redirect()->route('ecclesias.index')->with('message', 'Ecclesia deleted successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
