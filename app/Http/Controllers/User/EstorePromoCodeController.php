<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EstorePromoCode;
use App\Models\User;
use App\Models\Product;

class EstorePromoCodeController extends Controller
{
    // This controller will handle the promo code functionalities for the eStore.
    public function index()
    {
        // Code to list all promo codes
        $promoCodes = EstorePromoCode::orderBy('created_at', 'desc')->get();

        // Add scope summary to each promo code
        foreach ($promoCodes as $promoCode) {
            $promoCode->scope_summary = $promoCode->scopeSummary();

            // Determine scope label for display
            $scopeLabel = '';
            switch ($promoCode->scope_type) {
                case 'all':
                    $scopeLabel = 'All Orders';
                    break;
                case 'all_users':
                    $scopeLabel = 'All Users';
                    break;
                case 'selected_users':
                    $scopeLabel = 'Selected Users';
                    break;
                case 'all_products':
                    $scopeLabel = 'All Products';
                    break;
                case 'selected_products':
                    $scopeLabel = 'Selected Products';
                    break;
            }
            $promoCode->scope_label = $scopeLabel;
        }
        return view('user.estore-promocode.list', compact('promoCodes'));
    }

    public function create()
    {
        $users = User::orderBy('first_name')->orderBy('last_name')->get();
        $products = Product::where('status', 1)->where('is_deleted', 0)->orderBy('name')->get();

        return view('user.estore-promocode.create', compact('users', 'products'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string|max:255|unique:estore_promo_codes',
                'is_percentage' => 'required|boolean',
                'discount_amount' => 'required|numeric|min:0',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'status' => 'required|boolean',
                'scope_type' => 'required|in:all,all_users,selected_users,all_products,selected_products',
                'user_ids' => 'nullable|array|required_if:scope_type,selected_users',
                'user_ids.*' => 'integer|exists:users,id',
                'product_ids' => 'nullable|array|required_if:scope_type,selected_products',
                'product_ids.*' => 'integer|exists:products,id',
            ]);

            $promoCode = new EstorePromoCode();
            $promoCode->code = $request->input('code');
            $promoCode->is_percentage = $request->boolean('is_percentage');
            $promoCode->discount_amount = $request->input('discount_amount');
            $promoCode->start_date = $request->input('start_date');
            $promoCode->end_date = $request->input('end_date');
            $promoCode->status = $request->boolean('status');
            $promoCode->scope_type = $request->input('scope_type');
            $promoCode->user_ids = $request->input('scope_type') === 'selected_users'
                ? collect($request->input('user_ids', []))->unique()->values()->all()
                : null;
            $promoCode->product_ids = $request->input('scope_type') === 'selected_products'
                ? collect($request->input('product_ids', []))->unique()->values()->all()
                : null;
            $promoCode->save();

            return redirect()->route('store-promo-codes.index')->with('message', 'Promo code created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create promo code: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $promoCode = EstorePromoCode::findOrFail($id);
        $users = User::orderBy('first_name')->orderBy('last_name')->get();
        $products = Product::orderBy('name')->get();

        return view('user.estore-promocode.edit', compact('promoCode', 'users', 'products'));
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'code' => 'required|string|max:255|unique:estore_promo_codes,code,' . $id,
                'is_percentage' => 'required|boolean',
                'discount_amount' => 'required|numeric|min:0',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'status' => 'required|boolean',
                'scope_type' => 'required|in:all,all_users,selected_users,all_products,selected_products',
                'user_ids' => 'nullable|array|required_if:scope_type,selected_users',
                'user_ids.*' => 'integer|exists:users,id',
                'product_ids' => 'nullable|array|required_if:scope_type,selected_products',
                'product_ids.*' => 'integer|exists:products,id',
            ]);

            $promoCode = EstorePromoCode::findOrFail($id);
            $promoCode->code = $request->input('code');
            $promoCode->is_percentage = $request->boolean('is_percentage');
            $promoCode->discount_amount = $request->input('discount_amount');
            $promoCode->start_date = $request->input('start_date');
            $promoCode->end_date = $request->input('end_date');
            $promoCode->status = $request->boolean('status');
            $promoCode->scope_type = $request->input('scope_type');
            $promoCode->user_ids = $request->input('scope_type') === 'selected_users'
                ? collect($request->input('user_ids', []))->unique()->values()->all()
                : null;
            $promoCode->product_ids = $request->input('scope_type') === 'selected_products'
                ? collect($request->input('product_ids', []))->unique()->values()->all()
                : null;
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
