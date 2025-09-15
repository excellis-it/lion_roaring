<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WareHouse;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Size;
use App\Models\Color;
use App\Models\Category;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;


class WarehouseAdminController extends Controller
{
    use ImageTrait;

    public function __construct()
    {
        // Example: only admins can access this controller
        if (!auth()->user()->hasRole('SUPER ADMIN') || !auth()->user()->hasRole('ADMINISTRATOR') || !auth()->user()->isWarehouseAdmin()) {
            abort(403, 'Access denied.');
        }
    }

    /**
     * Display a listing of the warehouse admins.
     */
    public function index()
    {

        $warehouseAdmins = User::role('WAREHOUSE_ADMIN')->get();
        return view('user.warehouse-admin.list', compact('warehouseAdmins'));
    }

    /**
     * Show the form for creating a new warehouse admin.
     */
    public function create()
    {


        $warehouses = WareHouse::where('is_active', 1)->get();
        return view('user.warehouse-admin.create', compact('warehouses'));
    }

    /**
     * Store a newly created warehouse admin in storage.
     */
    public function store(Request $request)
    {


        $request->validate([
            'user_name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'warehouses' => 'required|array|min:1',
            'warehouses.*' => 'exists:ware_houses,id',
        ]);

        // Create the user
        $user = new User();
        $user->user_name = $request->user_name;
        $user->email = $request->email;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        $user->status = 1; // Active by default
        $user->is_accept = 1; // Already approved
        $user->save();

        // Assign WAREHOUSE_ADMIN role
        $role = Role::where('name', 'WAREHOUSE_ADMIN')->first();
        $user->assignRole($role);

        // Assign warehouses
        if ($request->warehouses) {
            foreach ($request->warehouses as $warehouseId) {
                $user->warehouses()->attach($warehouseId);
            }
        }

        return redirect()->route('warehouse-admins.index')
            ->with('message', 'Warehouse admin created successfully.');
    }

    /**
     * Display the specified warehouse admin.
     */
    public function show($id)
    {


        $warehouseAdmin = User::role('WAREHOUSE_ADMIN')->findOrFail($id);
        return view('user.warehouse-admin.show', compact('warehouseAdmin'));
    }

    /**
     * Show the form for editing the specified warehouse admin.
     */
    public function edit($id)
    {


        $warehouseAdmin = User::role('WAREHOUSE_ADMIN')->findOrFail($id);
        $warehouses = WareHouse::where('is_active', 1)->get();
        $assignedWarehouseIds = $warehouseAdmin->warehouses->pluck('id')->toArray();

        return view('user.warehouse-admin.edit', compact('warehouseAdmin', 'warehouses', 'assignedWarehouseIds'));
    }

    /**
     * Update the specified warehouse admin in storage.
     */
    public function update(Request $request, $id)
    {


        $warehouseAdmin = User::role('WAREHOUSE_ADMIN')->findOrFail($id);

        $request->validate([
            'user_name' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($warehouseAdmin->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($warehouseAdmin->id)],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'warehouses' => 'required|array|min:1',
            'warehouses.*' => 'exists:ware_houses,id',
            'status' => 'required|boolean',
        ]);

        // Update user details
        $warehouseAdmin->user_name = $request->user_name;
        $warehouseAdmin->email = $request->email;
        $warehouseAdmin->first_name = $request->first_name;
        $warehouseAdmin->last_name = $request->last_name;
        $warehouseAdmin->phone = $request->phone;
        $warehouseAdmin->status = $request->status;

        if ($request->filled('password')) {
            $warehouseAdmin->password = Hash::make($request->password);
        }

        $warehouseAdmin->save();

        // Update warehouse assignments
        $warehouseAdmin->warehouses()->sync($request->warehouses);

        return redirect()->route('warehouse-admins.index')
            ->with('message', 'Warehouse admin updated successfully.');
    }

    /**
     * Remove the specified warehouse admin from storage.
     */
    public function destroy($id)
    {


        $warehouseAdmin = User::role('WAREHOUSE_ADMIN')->findOrFail($id);

        // Remove role
        $warehouseAdmin->removeRole('WAREHOUSE_ADMIN');

        // Detach all warehouses
        $warehouseAdmin->warehouses()->detach();

        // Optionally delete the user completely
        // $warehouseAdmin->delete();

        return redirect()->route('warehouse-admins.index')
            ->with('message', 'Warehouse admin removed successfully.');
    }

    /**
     * Delete helper used by the GET delete route.
     */
    public function delete($id)
    {


        $warehouseAdmin = User::role('WAREHOUSE_ADMIN')->findOrFail($id);

        // Remove role
        $warehouseAdmin->removeRole('WAREHOUSE_ADMIN');

        // Detach all warehouses
        $warehouseAdmin->warehouses()->detach();

        return redirect()->route('warehouse-admins.index')
            ->with('message', 'Warehouse admin removed successfully.');
    }

    /**
     * =================== WAREHOUSE ADMIN PRODUCT MANAGEMENT ==================
     */

    /**
     * Display a listing of products for warehouse admin.
     */
    public function listProducts()
    {


        // Get warehouse IDs that this admin can manage
        $warehouseIds = auth()->user()->warehouses->pluck('id')->toArray();

        // Get all products in these warehouses
        $products = Product::whereHas('warehouses', function ($query) use ($warehouseIds) {
            $query->whereIn('ware_houses.id', $warehouseIds);
        })->paginate(10);

        return view('user.warehouse-admin.products.list', compact('products'));
    }

    /**
     * Show form to create a new product.
     */
    public function createProduct()
    {


        $categories = Category::where('status', 1)->get();
        $sizes = Size::where('status', 1)->get();
        $colors = Color::where('status', 1)->get();
        $warehouses = auth()->user()->warehouses;

        return view('user.warehouse-admin.products.create', compact('categories', 'sizes', 'colors', 'warehouses'));
    }

    /**
     * Store a newly created product and add to warehouse.
     */
    public function storeProduct(Request $request)
    {


        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'required|string',
            'specification' => 'required|string',
            'price' => 'required|numeric|min:0',
            'slug' => 'required|string|unique:products,slug',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'warehouse_id' => 'required|exists:ware_houses,id',
            'tax_rate' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'sizes' => 'nullable|array',
            'sizes.*' => 'exists:sizes,id',
            'colors' => 'nullable|array',
            'colors.*' => 'exists:colors,id',
        ]);

        // Verify that the user can manage this warehouse
        if (!auth()->user()->canManageWarehouse($request->warehouse_id)) {
            abort(403, 'You are not authorized to manage this warehouse.');
        }

        // Create the product
        $product = new Product();
        $product->category_id = $request->category_id;
        $product->user_id = auth()->user()->id;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->short_description = $request->short_description;
        $product->specification = $request->specification;
        $product->price = $request->price;
        $product->slug = $request->slug;
        $product->feature_product = $request->has('feature_product') ? 1 : 0;
        $product->status = 1;
        $product->save();

        // Handle main image
        if ($request->hasFile('image')) {
            $image = new ProductImage();
            $image->product_id = $product->id;
            $image->image = $this->imageUpload($request->file('image'), 'product');
            $image->featured_image = 1;
            $image->save();
        }

        // Handle additional images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $image = new ProductImage();
                $image->product_id = $product->id;
                $image->image = $this->imageUpload($file, 'product');
                $image->featured_image = 0;
                $image->save();
            }
        }

        // Save sizes
        if ($request->filled('sizes')) {
            foreach ($request->sizes as $sizeId) {
                $product->sizes()->create(['size_id' => $sizeId]);
            }
        }

        // Save colors
        if ($request->filled('colors')) {
            foreach ($request->colors as $colorId) {
                $product->colors()->create(['color_id' => $colorId]);
            }
        }

        // Add product to warehouse
        $product->warehouseProducts()->create([
            'warehouse_id' => $request->warehouse_id,
            'tax_rate' => $request->tax_rate,
            'quantity' => $request->quantity,
            'color_id' => $request->color_id ?? null,
            'size_id' => $request->size_id ?? null,
        ]);

        return redirect()->route('warehouse-admin.products')
            ->with('message', 'Product created and added to warehouse successfully.');
    }

    /**
     * Show form to edit a product.
     */
    public function editProduct($id)
    {


        $product = Product::findOrFail($id);

        // Check if product is in any of the warehouses managed by this admin
        $warehouseIds = auth()->user()->warehouses->pluck('id')->toArray();
        $productWarehouses = $product->warehouses->pluck('id')->toArray();

        if (!array_intersect($warehouseIds, $productWarehouses)) {
            abort(403, 'You are not authorized to edit this product.');
        }

        $categories = Category::where('status', 1)->get();
        $sizes = Size::where('status', 1)->get();
        $colors = Color::where('status', 1)->get();
        $warehouses = auth()->user()->warehouses;

        // Get warehouse product entry for this product in warehouses managed by this admin
        $warehouseProduct = $product->warehouseProducts()
            ->whereIn('warehouse_id', $warehouseIds)
            ->first();

        return view('user.warehouse-admin.products.edit', compact('product', 'categories', 'sizes', 'colors', 'warehouses', 'warehouseProduct'));
    }

    /**
     * Update the specified product.
     */
    public function updateProduct(Request $request, $id)
    {


        $product = Product::findOrFail($id);

        // Check if product is in any of the warehouses managed by this admin
        $warehouseIds = auth()->user()->warehouses->pluck('id')->toArray();
        $productWarehouses = $product->warehouses->pluck('id')->toArray();

        if (!array_intersect($warehouseIds, $productWarehouses)) {
            abort(403, 'You are not authorized to update this product.');
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'required|string',
            'specification' => 'required|string',
            'price' => 'required|numeric|min:0',
            'slug' => 'required|string|unique:products,slug,' . $id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'warehouse_id' => 'required|exists:ware_houses,id',
            'tax_rate' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'sizes' => 'nullable|array',
            'sizes.*' => 'exists:sizes,id',
            'colors' => 'nullable|array',
            'colors.*' => 'exists:colors,id',
        ]);

        // Verify that the user can manage this warehouse
        if (!auth()->user()->canManageWarehouse($request->warehouse_id)) {
            abort(403, 'You are not authorized to manage this warehouse.');
        }

        // Update the product
        $product->category_id = $request->category_id;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->short_description = $request->short_description;
        $product->specification = $request->specification;
        $product->price = $request->price;
        $product->slug = $request->slug;
        $product->feature_product = $request->has('feature_product') ? 1 : 0;
        $product->save();

        // Handle main image if provided
        if ($request->hasFile('image')) {
            // Delete old main image if exists
            $oldMainImage = ProductImage::where('product_id', $product->id)
                ->where('featured_image', 1)
                ->first();

            if ($oldMainImage) {
                $oldMainImage->delete();
            }

            // Upload new main image
            $image = new ProductImage();
            $image->product_id = $product->id;
            $image->image = $this->imageUpload($request->file('image'), 'product');
            $image->featured_image = 1;
            $image->save();
        }

        // Handle additional images if provided
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $image = new ProductImage();
                $image->product_id = $product->id;
                $image->image = $this->imageUpload($file, 'product');
                $image->featured_image = 0;
                $image->save();
            }
        }

        // Update sizes
        $product->sizes()->delete();
        if ($request->filled('sizes')) {
            foreach ($request->sizes as $sizeId) {
                $product->sizes()->create(['size_id' => $sizeId]);
            }
        }

        // Update colors
        $product->colors()->delete();
        if ($request->filled('colors')) {
            foreach ($request->colors as $colorId) {
                $product->colors()->create(['color_id' => $colorId]);
            }
        }

        // Update warehouse product entry
        $warehouseProduct = $product->warehouseProducts()
            ->where('warehouse_id', $request->warehouse_id)
            ->first();

        if ($warehouseProduct) {
            $warehouseProduct->update([
                'tax_rate' => $request->tax_rate,
                'quantity' => $request->quantity,
                'color_id' => $request->color_id ?? null,
                'size_id' => $request->size_id ?? null,
            ]);
        } else {
            // If product not in this warehouse yet, add it
            $product->warehouseProducts()->create([
                'warehouse_id' => $request->warehouse_id,
                'tax_rate' => $request->tax_rate,
                'quantity' => $request->quantity,
                'color_id' => $request->color_id ?? null,
                'size_id' => $request->size_id ?? null,
            ]);
        }

        return redirect()->route('warehouse-admin.products')
            ->with('message', 'Product updated successfully.');
    }

    /**
     * Remove the specified product.
     */
    public function deleteProduct($id)
    {


        $product = Product::findOrFail($id);

        // Check if product is in any of the warehouses managed by this admin
        $warehouseIds = auth()->user()->warehouses->pluck('id')->toArray();
        $productWarehouses = $product->warehouses->pluck('id')->toArray();

        if (!array_intersect($warehouseIds, $productWarehouses)) {
            abort(403, 'You are not authorized to delete this product.');
        }

        // For warehouse admins, we only remove the product from their warehouses, not delete it completely
        $product->warehouseProducts()
            ->whereIn('warehouse_id', $warehouseIds)
            ->delete();

        return redirect()->route('warehouse-admin.products')
            ->with('message', 'Product removed from your warehouses successfully.');
    }

    /**
     * =================== WAREHOUSE ADMIN SIZE MANAGEMENT ==================
     */

    /**
     * Display a listing of sizes.
     */
    public function listSizes()
    {


        $sizes = Size::paginate(10);
        return view('user.warehouse-admin.sizes.list', compact('sizes'));
    }

    /**
     * Show form to create a new size.
     */
    public function createSize()
    {


        return view('user.warehouse-admin.sizes.create');
    }

    /**
     * Store a newly created size.
     */
    public function storeSize(Request $request)
    {


        $request->validate([
            'name' => 'required|string|max:255|unique:sizes,size',
        ]);

        $size = new Size();
        $size->size = $request->name;
        $size->status = 1;
        $size->save();

        return redirect()->route('warehouse-admin.sizes')
            ->with('message', 'Size created successfully.');
    }

    /**
     * Show form to edit a size.
     */
    public function editSize($id)
    {


        $size = Size::findOrFail($id);
        return view('user.warehouse-admin.sizes.edit', compact('size'));
    }

    /**
     * Update the specified size.
     */
    public function updateSize(Request $request, $id)
    {


        $size = Size::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:sizes,size,' . $id . ',id',
            'status' => 'required|boolean',
        ]);

        $size->size = $request->name;
        $size->status = $request->status;
        $size->save();

        return redirect()->route('warehouse-admin.sizes')
            ->with('message', 'Size updated successfully.');
    }

    /**
     * Remove the specified size.
     */
    public function deleteSize($id)
    {


        $size = Size::findOrFail($id);

        // Check if size is in use
        $isInUse = $size->warehouseProducts()->exists();
        if ($isInUse) {
            return redirect()->route('warehouse-admin.sizes')
                ->with('error', 'Size cannot be deleted because it is in use.');
        }

        $size->delete();

        return redirect()->route('warehouse-admin.sizes')
            ->with('message', 'Size deleted successfully.');
    }

    /**
     * =================== WAREHOUSE ADMIN COLOR MANAGEMENT ==================
     */

    /**
     * Display a listing of colors.
     */
    public function listColors()
    {


        $colors = Color::paginate(10);
        return view('user.warehouse-admin.colors.list', compact('colors'));
    }

    /**
     * Show form to create a new color.
     */
    public function createColor()
    {


        return view('user.warehouse-admin.colors.create');
    }

    /**
     * Store a newly created color.
     */
    public function storeColor(Request $request)
    {


        $request->validate([
            'color_name' => 'required|string|max:255|unique:colors,color_name',
            'color' => 'required|string|max:7',
        ]);

        $color = new Color();
        $color->color_name = $request->color_name;
        $color->color = $request->color;
        $color->status = 1;
        $color->save();

        return redirect()->route('warehouse-admin.colors')
            ->with('message', 'Color created successfully.');
    }

    /**
     * Show form to edit a color.
     */
    public function editColor($id)
    {


        $color = Color::findOrFail($id);
        return view('user.warehouse-admin.colors.edit', compact('color'));
    }

    /**
     * Update the specified color.
     */
    public function updateColor(Request $request, $id)
    {


        $color = Color::findOrFail($id);

        $request->validate([
            'color_name' => 'required|string|max:255|unique:colors,color_name,' . $id . ',id',
            'color' => 'required|string|max:7',
            'status' => 'required|boolean',
        ]);

        $color->color_name = $request->color_name;
        $color->color = $request->color;
        $color->status = $request->status;
        $color->save();

        return redirect()->route('warehouse-admin.colors')
            ->with('message', 'Color updated successfully.');
    }

    /**
     * Remove the specified color.
     */
    public function deleteColor($id)
    {


        $color = Color::findOrFail($id);

        // Check if color is in use
        $isInUse = $color->warehouseProducts()->exists();
        if ($isInUse) {
            return redirect()->route('warehouse-admin.colors')
                ->with('error', 'Color cannot be deleted because it is in use.');
        }

        $color->delete();

        return redirect()->route('warehouse-admin.colors')
            ->with('message', 'Color deleted successfully.');
    }
}
