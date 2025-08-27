<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\WareHouse;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Product;
use App\Models\Color;
use App\Models\Size;
use App\Models\WarehouseProduct;

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

    /**
     * Display products for a specific warehouse.
     */
    public function products($id)
    {
        $wareHouse = WareHouse::findOrFail($id);
        $warehouseProducts = $wareHouse->warehouseProducts()->with(['product', 'color', 'size'])->get();
        return view('user.warehouse.products', compact('wareHouse', 'warehouseProducts'));
    }

    /**
     * Show form to add product to warehouse.
     */
    public function addProduct($id)
    {
        $wareHouse = WareHouse::findOrFail($id);
        $products = Product::where('status', 1)->get();
        $colors = Color::where('status', 1)->get();
        $sizes = Size::where('status', 1)->get();
        return view('user.warehouse.add_product', compact('wareHouse', 'products', 'colors', 'sizes'));
    }

    /**
     * Store a product in warehouse.
     */
    public function storeProduct(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'color_id' => 'nullable|exists:colors,id',
            'size_id' => 'nullable|exists:sizes,id',
            'tax_rate' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ]);

        $wareHouse = WareHouse::findOrFail($id);

        // Check if product with same color and size already exists in this warehouse
        $existingProduct = WarehouseProduct::where('warehouse_id', $wareHouse->id)
            ->where('product_id', $request->product_id)
            ->where('color_id', $request->color_id)
            ->where('size_id', $request->size_id)
            ->first();

        if ($existingProduct) {
            // Update quantity if product already exists
            $existingProduct->quantity += $request->quantity;
            $existingProduct->tax_rate = $request->tax_rate;
            $existingProduct->save();
        } else {
            // Create new warehouse product entry
            WarehouseProduct::create([
                'warehouse_id' => $wareHouse->id,
                'product_id' => $request->product_id,
                'color_id' => $request->color_id,
                'size_id' => $request->size_id,
                'tax_rate' => $request->tax_rate,
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->route('ware-houses.products', $wareHouse->id)
            ->with('message', 'Product added to warehouse successfully.');
    }

    /**
     * Show form to edit warehouse product.
     */
    public function editProduct($warehouseId, $productId)
    {
        $wareHouse = WareHouse::findOrFail($warehouseId);
        $warehouseProduct = WarehouseProduct::findOrFail($productId);
        $products = Product::where('status', 1)->get();
        $colors = Color::where('status', 1)->get();
        $sizes = Size::where('status', 1)->get();

        return view('user.warehouse.edit_product', compact('wareHouse', 'warehouseProduct', 'products', 'colors', 'sizes'));
    }

    /**
     * Update warehouse product.
     */
    public function updateProduct(Request $request, $warehouseId, $productId)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'color_id' => 'nullable|exists:colors,id',
            'size_id' => 'nullable|exists:sizes,id',
            'tax_rate' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ]);

        $warehouseProduct = WarehouseProduct::findOrFail($productId);

        $warehouseProduct->update([
            'product_id' => $request->product_id,
            'color_id' => $request->color_id,
            'size_id' => $request->size_id,
            'tax_rate' => $request->tax_rate,
            'quantity' => $request->quantity,
        ]);

        return redirect()->route('ware-houses.products', $warehouseId)
            ->with('message', 'Warehouse product updated successfully.');
    }

    /**
     * Delete warehouse product.
     */
    public function deleteProduct($warehouseId, $productId)
    {
        $warehouseProduct = WarehouseProduct::findOrFail($productId);
        $warehouseProduct->delete();

        return redirect()->route('ware-houses.products', $warehouseId)
            ->with('message', 'Product removed from warehouse successfully.');
    }
}
