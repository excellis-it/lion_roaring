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
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class WareHouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Super admin sees all warehouses, warehouse admin sees only assigned warehouses
        if (auth()->user()->hasRole('SUPER ADMIN')) {
            $wareHouses = WareHouse::all();
        } else {
            $wareHouses = auth()->user()->warehouses;
        }

        return view('user.warehouse.list', compact('wareHouses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Only super admin can create warehouses
        if (!auth()->user()->hasRole('SUPER ADMIN')) {
            abort(403, 'Unauthorized action.');
        }

        $all_users = User::where('status', 1)->where('is_accept', 1)->get(); // Fetch all users with any role and status active

        $countries = Country::get();
        return view('user.warehouse.create', compact('countries', 'all_users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only super admin can create warehouses
        if (!auth()->user()->hasRole('SUPER ADMIN')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'location_lat' => 'required|string|max:255',
                'location_lng' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'country_id' => 'required|exists:countries,id',
                'service_range' => 'required|numeric',
                'is_active' => 'required',
                'assign_user' => 'nullable|array',
                'assign_user.*' => 'exists:users,id',
            ]);

            // Create warehouse and capture model
            $wareHouse = WareHouse::create($request->only([
                'name',
                'location_lat',
                'location_lng',
                'address',
                'country_id',
                'service_range',
                'is_active'
            ]));

            // assign warehouse to users and change user role to WAREHOUSE_ADMIN
            if ($request->has('assign_user') && is_array($request->assign_user)) {
                foreach ($request->assign_user as $userId) {
                    $user = User::find($userId);
                    if ($user) {
                        $user->warehouses()->syncWithoutDetaching([$wareHouse->id]); // avoid duplicate attach
                        if (!$user->hasRole('WAREHOUSE_ADMIN')) {
                            $user->syncRoles(['WAREHOUSE_ADMIN']);
                        }
                    }
                }
            }

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
        // Check if user can access this warehouse
        if (!auth()->user()->canManageWarehouse($wareHouse->id)) {
            abort(403, 'Unauthorized action.');
        }

        // Implement show logic if needed
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WareHouse $wareHouse)
    {
        // Only super admin can edit warehouses
        if (!auth()->user()->hasRole('SUPER ADMIN')) {
            abort(403, 'Unauthorized action.');
        }

        $countries = Country::get();
        $all_users = User::where('status', 1)->where('is_accept', 1)->get();
        $assignedUserIds = DB::table('user_warehouses')->where('warehouse_id', $wareHouse->id)->pluck('user_id')->toArray();
        return view('user.warehouse.edit', compact('wareHouse', 'countries', 'all_users', 'assignedUserIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WareHouse $wareHouse)
    {
        // Only super admin can update warehouses
        if (!auth()->user()->hasRole('SUPER ADMIN')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'location_lat' => 'required|string|max:255',
            'location_lng' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'country_id' => 'required|exists:countries,id',
            'service_range' => 'required|numeric',
            'is_active' => 'required|in:0,1',
            'assign_user' => 'nullable|array',
            'assign_user.*' => 'exists:users,id',
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

            // Sync assigned users: attach new ones, detach removed ones
            $assignedUserIds = $request->has('assign_user') ? $request->assign_user : [];
            $existingUserIds = DB::table('user_warehouses')->where('warehouse_id', $wareHouse->id)->pluck('user_id')->toArray();

            $toAttach = array_diff($assignedUserIds, $existingUserIds);
            $toDetach = array_diff($existingUserIds, $assignedUserIds);

            foreach ($toAttach as $userId) {
                $user = User::find($userId);
                if ($user) {
                    $user->warehouses()->syncWithoutDetaching([$wareHouse->id]);
                    if (!$user->hasRole('WAREHOUSE_ADMIN')) {
                        $user->syncRoles(['WAREHOUSE_ADMIN']);
                    }
                }
            }

            foreach ($toDetach as $userId) {
                $user = User::find($userId);
                if ($user) {
                    $user->warehouses()->detach($wareHouse->id);
                    // if user has no more warehouses, remove warehouse admin role
                    if ($user->warehouses()->count() == 0) {
                        if ($user->hasRole('WAREHOUSE_ADMIN')) {
                            $user->removeRole('WAREHOUSE_ADMIN');
                            // assign default role
                            $user->syncRoles(['MEMBER_NON_SOVEREIGN']);
                        }
                    }
                }
            }

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
        // Only super admin can delete warehouses
        if (!auth()->user()->hasRole('SUPER ADMIN')) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Delete helper used by the GET delete route (ware-houses.delete).
     */
    public function delete($id)
    {
        // Only super admin can delete warehouses
        if (!auth()->user()->hasRole('SUPER ADMIN')) {
            abort(403, 'Unauthorized action.');
        }

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

        // Check if user can access this warehouse
        if (!auth()->user()->canManageWarehouse($wareHouse->id)) {
            abort(403, 'Unauthorized action.');
        }

        $warehouseProducts = $wareHouse->warehouseProducts()->with(['product', 'color', 'size'])->get();
        return view('user.warehouse.products', compact('wareHouse', 'warehouseProducts'));
    }

    /**
     * Show form to add product to warehouse.
     */
    public function addProduct($id)
    {
        $wareHouse = WareHouse::findOrFail($id);

        // Check if user can access this warehouse
        if (!auth()->user()->canManageWarehouse($wareHouse->id)) {
            abort(403, 'Unauthorized action.');
        }

        if (Auth::user()->hasRole('SUPER ADMIN')) {
            $products = Product::where('status', 1)->get();
        } else {
            $products = Product::where('status', 1)
                ->where('user_id', Auth::id())
                ->get();
        }

        $colors = Color::where('status', 1)->get();
        $sizes = Size::where('status', 1)->get();
        return view('user.warehouse.add_product', compact('wareHouse', 'products', 'colors', 'sizes'));
    }

    /**
     * Store a product in warehouse.
     */
    public function storeProduct(Request $request, $id)
    {
        $wareHouse = WareHouse::findOrFail($id);

        // Check if user can access this warehouse
        if (!auth()->user()->canManageWarehouse($wareHouse->id)) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'sku' => 'required|string|max:255|unique:warehouse_products,sku',
            'product_id' => 'required|exists:products,id',
            'color_id' => 'nullable|exists:colors,id',
            'size_id' => 'nullable|exists:sizes,id',
            // 'tax_rate' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0.01',
        ]);

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
                'sku' => $request->sku,
                'warehouse_id' => $wareHouse->id,
                'product_id' => $request->product_id,
                'color_id' => $request->color_id,
                'size_id' => $request->size_id,
                // 'tax_rate' => $request->tax_rate,
                'quantity' => $request->quantity,
                'price' => $request->price,
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

        // Check if user can access this warehouse
        if (!auth()->user()->canManageWarehouse($wareHouse->id)) {
            abort(403, 'Unauthorized action.');
        }

        $warehouseProduct = WarehouseProduct::findOrFail($productId);
        if (Auth::user()->hasRole('SUPER ADMIN')) {
            $products = Product::where('status', 1)->get();
        } else {
            $products = Product::where('status', 1)
                ->where('user_id', Auth::id())
                ->get();
        }
        $colors = Color::where('status', 1)->get();
        $sizes = Size::where('status', 1)->get();

        return view('user.warehouse.edit_product', compact('wareHouse', 'warehouseProduct', 'products', 'colors', 'sizes'));
    }

    /**
     * Update warehouse product.
     */
    public function updateProduct(Request $request, $warehouseId, $productId)
    {
        $wareHouse = WareHouse::findOrFail($warehouseId);

        // Check if user can access this warehouse
        if (!auth()->user()->canManageWarehouse($wareHouse->id)) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'sku' => 'required|string|max:255|unique:warehouse_products,sku,' . $productId,
            'product_id' => 'required|exists:products,id',
            // 'color_id' => 'nullable|exists:colors,id',
            // 'size_id' => 'nullable|exists:sizes,id',
            //  'tax_rate' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0.01',
        ]);

        $warehouseProduct = WarehouseProduct::findOrFail($productId);

        $warehouseProduct->update([
            'sku' => $request->sku,
            'product_id' => $request->product_id,
            // 'color_id' => $request->color_id,
            //  'size_id' => $request->size_id,
            // 'tax_rate' => $request->tax_rate,
            'quantity' => $request->quantity,
            'price' => $request->price,
        ]);

        return redirect()->route('ware-houses.products', $warehouseId)
            ->with('message', 'Warehouse product updated successfully.');
    }

    /**
     * Delete warehouse product.
     */
    public function deleteProduct($warehouseId, $productId)
    {
        $wareHouse = WareHouse::findOrFail($warehouseId);

        // Check if user can access this warehouse
        if (!auth()->user()->canManageWarehouse($wareHouse->id)) {
            abort(403, 'Unauthorized action.');
        }

        $warehouseProduct = WarehouseProduct::findOrFail($productId);
        $warehouseProduct->delete();

        return redirect()->route('ware-houses.products', $warehouseId)
            ->with('message', 'Product removed from warehouse successfully.');
    }

    // // // on change product get product's size and colors
    public function getProductDetails(Request $request)
    {
        $productId = $request->input('id');
        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found.']);
        }

        $sizes = $product->sizesWithDetails();
        $colors = $product->colorsWithDetails();

        return response()->json(['status' => true, 'data' => ['sizes' => $sizes, 'colors' => $colors]]);
    }
}
