<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EstorePromoCode;

class EstorePromoCodeController extends Controller
{
    // This controller will handle the promo code functionalities for the eStore.
    public function index()
    {
        // Code to list all promo codes
        $promoCodes = EstorePromoCode::all();
        return view('user.estore-promocode.list', compact('promoCodes'));
    }

    public function create()
    {
        // Code to show the form for creating a new promo code
        return view('user.estore-promocode.create');
    }

    public function store(Request $request)
    {
        try {
            //validate
            $request->validate([
                'code' => 'required|string|max:255|unique:estore_promo_codes',
                'is_percentage' => 'required|boolean',
                'discount_amount' => 'required|numeric|min:0',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'status' => 'required|boolean',
            ]);



            // Code to store a new promo code
            $promoCode = new EstorePromoCode();
            $promoCode->code = $request->input('code');
            $promoCode->is_percentage = $request->input('is_percentage', false);
            $promoCode->discount_amount = $request->input('discount_amount');
            $promoCode->start_date = $request->input('start_date');
            $promoCode->end_date = $request->input('end_date');
            $promoCode->save();

            return redirect()->route('store-promo-codes.index')->with('message', 'Promo code created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create promo code: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        // Code to show the form for editing a promo code
        $promoCode = EstorePromoCode::findOrFail($id);
        return view('user.estore-promocode.edit', compact('promoCode'));
    }

    public function update(Request $request, $id)
    {
        try {
            //validate
            $request->validate([
                'code' => 'required|string|max:255|unique:estore_promo_codes,code,' . $id,
                'is_percentage' => 'required|boolean',
                'discount_amount' => 'required|numeric|min:0',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'status' => 'required|boolean',
            ]);

            // Code to update a promo code
            $promoCode = EstorePromoCode::findOrFail($id);
            $promoCode->code = $request->input('code');
            $promoCode->is_percentage = $request->input('is_percentage', false);
            $promoCode->discount_amount = $request->input('discount_amount');
            $promoCode->start_date = $request->input('start_date');
            $promoCode->end_date = $request->input('end_date');
            $promoCode->save();

            return redirect()->route('store-promo-codes.index')->with('message', 'Promo code updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update promo code: ' . $e->getMessage())->withInput();
        }
    }



    public function destroy($id)
    {
        // Code to delete a promo code
        $promoCode = EstorePromoCode::findOrFail($id);
        $promoCode->delete();

        return redirect()->route('store-promo-codes.index')->with('message', 'Promo code deleted successfully.');
    }

    // delete
    public function delete($id)
    {
        // Code to delete a promo code
        $promoCode = EstorePromoCode::findOrFail($id);
        $promoCode->delete();

        return redirect()->route('store-promo-codes.index')->with('message', 'Promo code deleted successfully.');
    }
}
