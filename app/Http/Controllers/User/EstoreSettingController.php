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
        if (!auth()->user()->can('Manage Estore Settings') && !auth()->user()->can('View Estore Settings')) {
            abort(403, 'You do not have permission to access this page.');
        }
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
        if (!auth()->user()->can('Edit Estore Settings')) {
            abort(403, 'You do not have permission to access this page.');
        }
        // ✅ Basic validation
        $validated = $request->validate([
            'shipping_cost' => 'nullable|numeric|min:0',
            'delivery_cost' => 'nullable|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'credit_card_percentage' => 'nullable|numeric|min:0|max:100',
            'is_pickup_available' => 'required|boolean',
            'refund_max_days' => 'nullable|integer|min:0',
            'max_order_quantity' => 'nullable|integer|min:1',
            'shipping_rules' => 'nullable', // accept array or JSON string
        ]);

        // Normalize and validate shipping_rules if provided (accepted as JSON string or array)
        if ($request->filled('shipping_rules')) {
            $raw = $request->input('shipping_rules');
            $decoded = $raw;
            if (is_string($raw)) {
                $decoded = json_decode($raw, true);
            }

            if (!is_array($decoded)) {
                return redirect()->back()->withErrors(['shipping_rules' => 'Shipping rules must be an array or JSON string.'])->withInput();
            }

            // Normalize numeric fields and remove empty/default rules (defensive: users sometimes leave an empty row)
            $normalized = array_map(function ($r) {
                return [
                    'min_qty' => isset($r['min_qty']) ? (int) $r['min_qty'] : 0,
                    'max_qty' => isset($r['max_qty']) ? (int) $r['max_qty'] : 0,
                    'shipping_cost' => isset($r['shipping_cost']) ? (float) $r['shipping_cost'] : 0,
                    'delivery_cost' => isset($r['delivery_cost']) ? (float) $r['delivery_cost'] : 0,
                ];
            }, $decoded);

            // Remove rules where all numeric values are zero (likely an empty row)
            $normalized = array_values(array_filter($normalized, function ($r) {
                return ($r['min_qty'] > 0) || ($r['max_qty'] > 0) || ($r['shipping_cost'] > 0) || ($r['delivery_cost'] > 0);
            }));

            // Validate each remaining rule
            $ruleValidator = \Illuminate\Support\Facades\Validator::make(['shipping_rules' => $normalized], [
                'shipping_rules.*.min_qty' => 'required|integer|min:0',
                'shipping_rules.*.max_qty' => 'required|integer|min:0',
                'shipping_rules.*.shipping_cost' => 'nullable|numeric|min:0',
                'shipping_rules.*.delivery_cost' => 'nullable|numeric|min:0',
            ]);

            if ($ruleValidator->fails()) {
                return redirect()->back()->withErrors($ruleValidator)->withInput();
            }

            // Ensure min <= max when max > 0
            foreach ($normalized as $idx => $r) {
                if ($r['max_qty'] > 0 && $r['min_qty'] > $r['max_qty']) {
                    return redirect()->back()->withErrors(['shipping_rules' => "Rule #" . ($idx + 1) . ": min_qty cannot be greater than max_qty."])->withInput();
                }
            }

            $validated['shipping_rules'] = $normalized;
        } else {
            $validated['shipping_rules'] = null;
        }

        // If quantity-based shipping rules are present, ensure legacy flat rates are reset to 0
        if (!empty($validated['shipping_rules']) && is_array($validated['shipping_rules']) && count($validated['shipping_rules']) > 0) {
            $validated['shipping_cost'] = 0;
            $validated['delivery_cost'] = 0;
        }

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
