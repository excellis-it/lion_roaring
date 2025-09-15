<?php


namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\EstoreSetting;
use Illuminate\Http\Request;

class EstoreSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $storeSetting = EstoreSetting::first();
        return view('user.estore-settings.settings', compact('storeSetting'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EstoreSetting  $estoreSetting
     * @return \Illuminate\Http\Response
     */
    public function show(EstoreSetting $estoreSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EstoreSetting  $estoreSetting
     * @return \Illuminate\Http\Response
     */
    public function edit(EstoreSetting $estoreSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EstoreSetting  $estoreSetting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // ✅ Validate request
        $validated = $request->validate([
            'shipping_cost' => 'nullable|numeric|min:0',
            'delivery_cost' => 'nullable|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'credit_card_percentage' => 'nullable|numeric|min:0|max:100',
            'is_pickup_available' => 'required|boolean',
        ]);

        try {
            $estoreSetting = EstoreSetting::findOrFail($id);
            $estoreSetting->update($validated);

            return redirect()
                ->route('store-settings.index')
                ->with('message', 'Settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EstoreSetting  $estoreSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(EstoreSetting $estoreSetting)
    {
        //
    }
}
