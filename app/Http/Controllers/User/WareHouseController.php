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
use App\Models\ProductVariation;
use App\Models\WarehouseProductVariation;


class WareHouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Super admin sees all warehouses, warehouse admin sees only assigned warehouses
        if (auth()->user()->can('Manage Estore Warehouse')) {
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
        if (!auth()->user()->can('Create Estore Warehouse')) {
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
        if (!auth()->user()->can('Create Estore Warehouse')) {
            abort(403, 'Unauthorized action.');
        }


        $request->validate(
            [
                'name' => 'required|string|max:255',
                'location_lat' => 'required|string|max:255',
                'location_lng' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'country_id' => 'required|exists:countries,id',
                'service_range' => 'required|numeric',
                'is_active' => 'required',
                'assign_user' => 'nullable|array',
                'assign_user.*' => 'exists:users,id',
            ],
            [
                'location_lat.required' => 'Latitude is required.',
                'location_lng.required' => 'Longitude is required.',
                'address.required' => 'Address is required.',
                'country_id.required' => 'Country is required.',
                'service_range.required' => 'Service range is required.',
                'is_active.required' => 'Status is required.',
                'assign_user.array' => 'Assigned users must be an array.',
                'assign_user.*.exists' => 'One or more selected users do not exist.'

            ]
        );

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
                    // if (!$user->hasNewRole('WAREHOUSE_ADMIN')) {
                    //     $user->syncRoles(['WAREHOUSE_ADMIN']);
                    // }
                }
            }
        }

        return redirect()->route('ware-houses.index')->with('message', 'Warehouse created successfully.');
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
        if (!auth()->user()->can('Edit Estore Warehouse')) {
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
        if (!auth()->user()->can('Edit Estore Warehouse')) {
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
                    // if (!$user->hasNewRole('WAREHOUSE_ADMIN')) {
                    //     $user->syncRoles(['WAREHOUSE_ADMIN']);
                    // }
                }
            }

            foreach ($toDetach as $userId) {
                $user = User::find($userId);
                if ($user) {
                    $user->warehouses()->detach($wareHouse->id);
                    // if user has no more warehouses, remove warehouse admin role
                    if ($user->warehouses()->count() == 0) {
                        // if ($user->hasNewRole('WAREHOUSE_ADMIN')) {
                        //     $user->removeRole('WAREHOUSE_ADMIN');
                        //     // assign default role
                        //     $user->syncRoles(['MEMBER_NON_SOVEREIGN']);
                        // }
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
        if (!auth()->user()->can('Delete Estore Warehouse')) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Delete helper used by the GET delete route (ware-houses.delete).
     */
    public function delete($id)
    {
        // Only super admin can delete warehouses
        if (!auth()->user()->can('Delete Estore Warehouse')) {
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

        if (Auth::user()->hasNewRole('SUPER ADMIN')) {
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
        if (Auth::user()->hasNewRole('SUPER ADMIN')) {
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

    // select warehouse before warehouse product management
    public function selectWarehouse($productId)
    {


        $product = Product::findOrFail($productId);
        if (auth()->user()->hasNewRole('SUPER ADMIN')) {
            $warehouses = WareHouse::where('is_active', 1)->get();
        } else {
            $user_warehouses = auth()->user()->warehouses->pluck('id')->toArray();
            $warehouses = WareHouse::whereIn('id', $user_warehouses)->where('is_active', 1)->get();
        }

        return view('user.warehouse.select_warehouse', compact('product', 'warehouses'));
    }

    // variationsWarehouse
    public function variationsWarehouse($warehouseId, $productId)
    {
        $product = Product::findOrFail($productId);
        $wareHouse = WareHouse::findOrFail($warehouseId);

        $product_variations_colors = ProductVariation::where('product_id', $productId)->distinct()->pluck('color_id')->toArray();
        $product_have_colors = Color::whereIn('id', $product_variations_colors)->get();
        // return $product_have_colors;


        // Check if user can access this warehouse
        if (!auth()->user()->canManageWarehouse($wareHouse->id)) {
            abort(403, 'Unauthorized action.');
        }

        // Check if user can access this product
        if (auth()->user()->isWarehouseAdmin()) {
            // Ensure warehouse has entries for all existing product variations (created later warehouses)
            $allVariations = $product->variations()->get();
            foreach ($allVariations as $variation) {
                WarehouseProductVariation::firstOrCreate([
                    'warehouse_id' => $wareHouse->id,
                    'product_variation_id' => $variation->id,
                    'product_id' => $product->id,
                ], [
                    'warehouse_quantity' => 0,
                ]);

                // Create corresponding WarehouseProduct if missing
                $warehouseProduct = WarehouseProduct::where('warehouse_id', $wareHouse->id)
                    ->where('product_variation_id', $variation->id)
                    ->where('product_id', $variation->product_id)
                    ->first();

                if (!$warehouseProduct) {
                    WarehouseProduct::create([
                        'product_variation_id' => $variation->id,
                        'sku' => $variation->sku,
                        'warehouse_id' => $wareHouse->id,
                        'product_id' => $variation->product_id,
                        'color_id' => $variation->color_id,
                        'size_id' => $variation->size_id,
                        'quantity' => 0,
                        'price' => $variation->sale_price ? $variation->sale_price : $variation->price,
                        'before_sale_price' => $variation->sale_price ? $variation->price : null,
                        'tax_rate' => 0,
                    ]);
                }
            }

            // Build product_variations list scoped to this warehouse (same ordering as created)
            $warehouse_product_variations_ids = WarehouseProductVariation::where('warehouse_id', $warehouseId)
                ->where('product_id', $productId)
                ->orderBy('id', 'desc')
                ->pluck('product_variation_id');

            if ($warehouse_product_variations_ids->isNotEmpty()) {
                $product_variations = ProductVariation::whereIn('id', $warehouse_product_variations_ids)
                    ->with(['colorDetail', 'sizeDetail', 'images'])
                    ->orderByRaw("FIELD(id, " . implode(',', $warehouse_product_variations_ids->toArray()) . ")")
                    ->get();

                foreach ($product_variations as $variation) {
                    $warehouseProductVariation = WarehouseProductVariation::where('warehouse_id', $warehouseId)
                        ->where('product_variation_id', $variation->id)
                        ->first();
                    $variation->warehouse_quantity = $warehouseProductVariation ? $warehouseProductVariation->warehouse_quantity : 0;
                    $variation->admin_available_quantity = $variation->available_quantity;

                    $warehouseProduct = WarehouseProduct::where('warehouse_id', $warehouseId)
                        ->where('product_variation_id', $variation->id)
                        ->first();
                    if ($warehouseProduct) {
                        $variation->warehouse_price = $warehouseProduct->price;
                        $variation->warehouse_before_sale_price = $warehouseProduct->before_sale_price;
                    } else {
                        $variation->warehouse_price = 0;
                        $variation->warehouse_before_sale_price = 0;
                    }
                }
            } else {
                // Fallback: if no records found for some reason, show empty collection
                $product_variations = collect();
            }

            return view('user.warehouse.warehouse-variations', compact('product', 'product_variations', 'wareHouse', 'product_have_colors'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    // selectWarehouseVariationStock
    public function selectWarehouseVariationStock(Request $request)
    {
        if (!$request->auto_load) {
            $validated = $request->validate([
                'color_id' => 'required|array|min:1',
                'color_id.*' => 'exists:colors,id',
            ]);
        }


        // return $request->all();
        $warehouseId = $request->input('warehouseId');
        $productId = $request->input('productId');
        $colorIds = $request->input('color_id', []); // array of selected color IDs

        $product = Product::findOrFail($productId);
        $wareHouse = WareHouse::findOrFail($warehouseId);
        $available_product_variations = [];

        // Check if user can access this warehouse
        if (!auth()->user()->canManageWarehouse($wareHouse->id)) {
            abort(403, 'Unauthorized action.');
        }

        // Check if user can access this product
        if (auth()->user()->isWarehouseAdmin()) {


            $admin_product_variations = ProductVariation::where('product_id', $productId)
                ->when(!empty($colorIds), function ($query) use ($colorIds) {
                    $query->whereIn('color_id', $colorIds);
                })
                ->with(['colorDetail', 'sizeDetail', 'images'])
                ->get();
            // return $colorIds;

            if ($colorIds && count($colorIds) > 0) {
                foreach ($admin_product_variations as $variation) {
                    // insert if not exists warehouse product variation WarehouseProductVariation
                    WarehouseProductVariation::firstOrCreate([
                        'warehouse_id' => $warehouseId,
                        'product_variation_id' => $variation->id,
                        'product_id' => $productId,
                        'warehouse_quantity' => 0,
                    ]);

                    // save product to WarehouseProduct
                    $warehouseProduct = WarehouseProduct::where('product_variation_id', $variation->id)
                        ->where('warehouse_id', $warehouseId)
                        ->where('product_id', $variation->product_id)
                        ->first();

                    if (!$warehouseProduct) {
                        // create new warehouse product entry
                        $warehouseProduct = WarehouseProduct::create([
                            'product_variation_id' => $variation->id,
                            'sku' => $variation->sku,
                            'warehouse_id' => $warehouseId,
                            'product_id' => $variation->product_id,
                            'color_id' => $variation->color_id,
                            'size_id' => $variation->size_id,
                            'quantity' => 0,
                            'price' => $variation->sale_price ? $variation->sale_price : $variation->price,
                            'before_sale_price' => $variation->sale_price ? $variation->price : null,
                            'tax_rate' => 0,
                        ]);
                    }
                }
            }

            $warehouse_product_variations_ids = WarehouseProductVariation::where('warehouse_id', $warehouseId)
                ->where('product_id', $productId)
                ->orderBy('id', 'desc')
                ->pluck('product_variation_id');
            // return $warehouse_product_variations_ids;

            if ($warehouse_product_variations_ids->isNotEmpty()) {


                $available_product_variations = ProductVariation::whereIn('id', $warehouse_product_variations_ids)
                    ->with(['colorDetail', 'sizeDetail', 'images'])
                    //->orderBy('id', 'desc')
                    // order by the same order as in $warehouse_product_variations_ids
                    ->orderByRaw("FIELD(id, " . implode(',', $warehouse_product_variations_ids->toArray()) . ")")
                    ->get();

                foreach ($available_product_variations as $variation) {
                    $warehouseProductVariation = WarehouseProductVariation::where('warehouse_id', $warehouseId)
                        ->where('product_variation_id', $variation->id)
                        ->first();
                    $variation->warehouse_quantity = $warehouseProductVariation ? $warehouseProductVariation->warehouse_quantity : 0;
                    // get admin_available_quantity
                    $variation->admin_available_quantity = $variation->available_quantity;

                    // warehouse price and warehouse before_sale_price
                    $warehouseProduct = WarehouseProduct::where('warehouse_id', $warehouseId)
                        ->where('product_variation_id', $variation->id)
                        ->first();
                    if ($warehouseProduct) {
                        $variation->warehouse_price = $warehouseProduct->price;
                        $variation->warehouse_before_sale_price = $warehouseProduct->before_sale_price;
                    } else {
                        $variation->warehouse_price = 0;
                        $variation->warehouse_before_sale_price = 0;
                    }

                    // $variation->warehouse_available_quantity = $variation->warehouse_quantity ?? 0;
                }
            }

            // dd($product, $available_product_variations);

            return view('user.warehouse.include.warehouse-variations-data', compact('product', 'available_product_variations'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function updateWarehouseVariationQuantity(Request $request)
    {

        if ($request->has('variations')) {
            $request->validate([
                'warehouse_id' => 'required|integer|exists:ware_houses,id',
                'variations' => 'required|array|min:1',
                'variations.*.variation_id' => 'required|integer|exists:product_variations,id',
                'variations.*.quantity' => 'nullable|integer|min:0',
            ]);

            $warehouseId = (int)$request->warehouse_id;

            if (!auth()->user()->canManageWarehouse($warehouseId) || !auth()->user()->isWarehouseAdmin()) {
                return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
            }

            $results = [];

            foreach ($request->input('variations') as $item) {
                $variationId = (int)$item['variation_id'];
                $newQty = (int)$item['quantity'];

                // if warehouse_quantity is null or not provided, skip this variation
                if (!isset($item['quantity'])) {
                    continue;
                }

                $variation = ProductVariation::with('warehouseProductVariations')->find($variationId);
                if (!$variation) {
                    return response()->json(['status' => false, 'message' => "Variation {$variationId} not found"], 404);
                }

                $warehouseVariation = WarehouseProductVariation::where('warehouse_id', $warehouseId)
                    ->where('product_variation_id', $variationId)
                    ->first();
                if (!$warehouseVariation) {
                    return response()->json(['status' => false, 'message' => "Warehouse variation {$variation->sku} not initialized"], 404);
                }

                $maxAllowedForThisWarehouse = $variation->stock_quantity;
                if ($newQty > $maxAllowedForThisWarehouse) {
                    return response()->json([
                        'status' => false,
                        'message' => "Quantity for variation {$variation->sku} exceeds remaining stock. Max allowed: {$maxAllowedForThisWarehouse}",
                        'max_allowed' => $maxAllowedForThisWarehouse,
                        'variation_id' => $variationId
                    ], 422);
                }

                $warehouseVariation->warehouse_quantity += $newQty;
                $warehouseVariation->save();

                // update corresponding WarehouseProduct quantity
                $warehouseProduct = WarehouseProduct::where('warehouse_id', $warehouseId)
                    ->where('product_variation_id', $variationId)
                    ->first();
                if ($warehouseProduct) {
                    $warehouseProduct->quantity += $newQty;
                    $warehouseProduct->save();
                }

                // adjust product variation stock (same behavior as single update)
                $variationStock = ProductVariation::find($variationId);
                $variationStock->stock_quantity -= $newQty;
                $variationStock->save();

                $available = $variationStock->stock_quantity;

                $warehouse_available_quantity = $warehouseVariation->warehouse_quantity ?? 0;

                $results[] = [
                    'variation_id' => $variationId,
                    'warehouse_quantity' => $newQty,
                    'admin_available_quantity' => $available,
                    'warehouse_available_quantity' => $warehouse_available_quantity,
                ];
            }

            // return array of updated variations
            return response()->json([
                'status' => true,
                'data' => $results,
                'message' => 'Quantity update completed'
            ]);
        }
    }


    // warehouseProductsList
    public function warehouseProductsList($id)
    {
        $wareHouse = WareHouse::findOrFail($id);

        // Check if user can access this warehouse
        if (!auth()->user()->canManageWarehouse($wareHouse->id)) {
            abort(403, 'Unauthorized action.');
        }

        // get only ProductVariation records associated with this warehouse
        $productVariationIds = WarehouseProductVariation::where('warehouse_id', $wareHouse->id)
            ->pluck('product_variation_id')
            ->toArray();

        $warehouseProducts = ProductVariation::whereIn('id', $productVariationIds)
            ->with(['product', 'colorDetail', 'sizeDetail', 'images'])
            ->get();
        // return $warehouseProducts;

        // get warehouse_quantity from WarehouseProductVariation model
        foreach ($warehouseProducts as $variation) {
            $warehouseProductVariation = WarehouseProductVariation::where('warehouse_id', $wareHouse->id)
                ->where('product_variation_id', $variation->id)
                ->first();
            $variation->warehouse_quantity = $warehouseProductVariation ? $warehouseProductVariation->warehouse_quantity : 0;
        }

        return view('user.warehouse.warehouse_products_list', compact('wareHouse', 'warehouseProducts'));
    }
}
