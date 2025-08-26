<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\WareHouse;
use Illuminate\Http\Request;
use App\Models\Country;

class WareHouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $wareHouses = WareHouse::all();
        return view('user.warehouse.list', compact('wareHouses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $countries = Country::get();
        return view('user.warehouse.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'location_lat' => 'required|string|max:255',
                'location_lng' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'country_id' => 'required|exists:countries,id',
                'service_range' => 'required|numeric',
                'is_active' => 'required',
            ]);
            WareHouse::create($request->all());

            return redirect()->route('ware-houses.index')->with('message', 'Warehouse created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create warehouse. ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(WareHouse $wareHouse)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WareHouse $wareHouse)
    {
        // return edit form with countries
        $countries = Country::get();
        return view('user.warehouse.edit', compact('wareHouse', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WareHouse $wareHouse)
    {
        //
        $request->validate([
            'name' => 'required|string|max:255',
            'location_lat' => 'required|string|max:255',
            'location_lng' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'country_id' => 'required|exists:countries,id',
            'service_range' => 'required|numeric',
            'is_active' => 'required|in:0,1',
        ]);

        try {
            $wareHouse->update([
                'name' => $request->name,
                'location_lat' => $request->location_lat,
                'location_lng' => $request->location_lng,
                'address' => $request->address,
                'country_id' => $request->country_id,
                'service_range' => $request->service_range,
                'is_active' => $request->is_active,
            ]);

            return redirect()->route('ware-houses.index')->with('message', 'Warehouse updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update warehouse. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WareHouse $wareHouse)
    {
        //
    }

    /**
     * Delete helper used by the GET delete route (ware-houses.delete).
     */
    public function delete($id)
    {
        $wareHouse = WareHouse::find($id);
        if ($wareHouse) {
            $wareHouse->delete();
            return redirect()->route('ware-houses.index')->with('message', 'Warehouse deleted successfully.');
        }
        return redirect()->route('ware-houses.index')->with('error', 'Warehouse not found.');
    }
}
