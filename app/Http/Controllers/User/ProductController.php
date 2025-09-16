<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;
use App\Models\Size;
use App\Models\Color;
use App\Models\WareHouse;
use App\Models\WarehouseProduct;
use App\Models\User;
use App\Models\WarehouseProductImage;
use App\Models\EcomWishList;
use App\Models\EstoreCart;

class ProductController extends Controller
{
    use ImageTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return User::with('roles')->where('id', auth()->id())->first();
        if (auth()->user()->hasRole('SUPER ADMIN') || auth()->user()->hasRole('ADMINISTRATOR')) {
            $products = Product::where('is_deleted', false)->orderBy('id', 'desc')->paginate(10);
            return view('user.product.list', compact('products'));
        } else if (auth()->user()->isWarehouseAdmin()) {
            // $products = Product::where('user_id', auth()->id())->orderBy('id', 'desc')->paginate(10);
            // products where warehouse those products which warehouse use inn
            // Get warehouse IDs that this admin can manage
            $warehouseIds = auth()->user()->warehouses->pluck('id')->toArray();

            // Get products that exist in any of these warehouses
            $products = Product::where('is_deleted', false)->whereHas('warehouseProducts', function ($query) use ($warehouseIds) {
                $query->whereIn('warehouse_id', $warehouseIds);
            })->orderBy('id', 'desc')->paginate(10);
            return view('user.product.list', compact('products'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }


    public function fetchData(Request $request)
    {
        if ($request->ajax()) {
            $sort_by = $request->get('sortby');
            $sort_type = $request->get('sorttype');
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);

            $products = Product::query();

            // Apply role-based filtering first
            if (auth()->user()->isWarehouseAdmin()) {
                $warehouseIds = auth()->user()->warehouses->pluck('id')->toArray();
                $products = $products->whereHas('warehouseProducts', function ($q) use ($warehouseIds) {
                    $q->whereIn('warehouse_id', $warehouseIds);
                });
            } elseif (!auth()->user()->hasRole('SUPER ADMIN') && !auth()->user()->hasRole('ADMINISTRATOR')) {
                abort(403, 'You do not have permission to access this page.');
            }

            // Apply search filters
            if (!empty($query)) {
                $products = $products->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhere('name', 'like', '%' . $query . '%')
                        ->orWhere('slug', 'like', '%' . $query . '%')
                        ->orWhereHas('category', function ($subQ) use ($query) {
                            $subQ->where('name', 'like', '%' . $query . '%');
                        });
                });
            }

            // Apply sorting
            if ($sort_by && $sort_type) {
                $products = $products->orderBy($sort_by, $sort_type);
            } else {
                $products = $products->orderBy('id', 'desc');
            }

            $products = $products->where('is_deleted', false)->paginate(10);

            return response()->json(['data' => view('user.product.table', compact('products'))->render()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::where('status', 1)->get();
        $sizes = Size::where('status', 1)->get();
        $colors = Color::where('status', 1)->get();

        // Get warehouses for assignment
        if (auth()->user()->hasRole('SUPER ADMIN') || auth()->user()->hasRole('ADMINISTRATOR')) {
            $warehouses = WareHouse::where('is_active', 1)->get();
        } else {
            $warehouses = auth()->user()->warehouses;
        }

        if (auth()->user()->hasRole('SUPER ADMIN') || auth()->user()->hasRole('ADMINISTRATOR') || auth()->user()->isWarehouseAdmin()) {
            return view('user.product.create')
                ->with(compact('categories', 'sizes', 'colors', 'warehouses'));
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
        try {
            $request->validate([
                // 'category_id' => 'required|numeric|exists:categories,id',
                // 'name' => 'required|string|max:255',
                // 'description' => 'required|string',
                // 'short_description' => 'required|string',
                // 'specification' => 'required|string',
                // 'price' => 'required|numeric',
                // 'feature_product' => 'required',
                // 'slug' => 'required|string|unique:products',
                // 'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
                // // 'images' => 'nullable|array',
                // // 'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg',

                // // Warehouse product validation
                // 'warehouse_products' => 'nullable|array',
                // 'warehouse_products.*.warehouse_id' => 'required|exists:ware_houses,id',
                // 'warehouse_products.*.sku' => 'required|string|distinct|unique:warehouse_products,sku',
                // 'warehouse_products.*.price' => 'required|numeric|min:0',
                // 'warehouse_products.*.color_id' => 'nullable|exists:colors,id',
                // 'warehouse_products.*.size_id' => 'nullable|exists:sizes,id',
                // 'warehouse_products.*.quantity' => 'required|integer|min:0',
                // 'warehouse_products.*.images' => 'nullable|array',
                // 'warehouse_products.*.images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp',
            ]);

            $product = new Product();
            $product->category_id = $request->category_id;
            $product->user_id = auth()->user()->id;
            $product->name = $request->name;
            $product->description = $request->description;
            $product->short_description = $request->short_description;
            $product->specification = $request->specification;
            $product->price = $request->price;
            $product->slug = $request->slug;
            $product->feature_product = $request->feature_product;
            $product->save();

            if ($request->hasFile('image')) {
                $image = new ProductImage();
                $image->product_id = $product->id;
                $image->image = $this->imageUpload($request->file('image'), 'product');
                $image->featured_image = 1;
                $image->save();
            }

            // if ($request->hasFile('images')) {
            //     foreach ($request->file('images') as $file) {
            //         $image = new ProductImage();
            //         $image->product_id = $product->id;
            //         $image->image = $this->imageUpload($file, 'product');
            //         $image->featured_image = 0;
            //         $image->save();
            //     }
            // }

            // Save sizes
            if ($request->filled('sizes')) {
                foreach ($request->sizes as $size) {
                    if ($size) {
                        $product->sizes()->create(['size_id' => $size]);
                    }
                }
            }

            // Save colors
            if ($request->filled('colors')) {
                foreach ($request->colors as $color) {
                    if ($color) {
                        $product->colors()->create(['color_id' => $color]);
                    }
                }
            }

            // save other charges if available
            if ($request->filled('other_charges')) {
                foreach ($request->other_charges as $charge) {
                    if ($charge['charge_name'] && $charge['charge_amount']) {
                        $product->otherCharges()->create($charge);
                    }
                }
            }

            // Save warehouse products
            if ($request->filled('warehouse_products')) {
                foreach ($request->warehouse_products as $warehouseProduct) {
                    if (!empty($warehouseProduct['warehouse_id'])) {
                        $theWarehouseProduct = WarehouseProduct::create([
                            'product_id' => $product->id,
                            'warehouse_id' => $warehouseProduct['warehouse_id'],
                            'sku' => $warehouseProduct['sku'],
                            'price' => $warehouseProduct['price'],
                            'color_id' => $warehouseProduct['color_id'] ?? null,
                            'size_id' => $warehouseProduct['size_id'] ?? null,
                            'quantity' => $warehouseProduct['quantity'],
                        ]);

                        // Save warehouse product images
                        if (!empty($warehouseProduct['images'])) {
                            foreach ($warehouseProduct['images'] as $file) {
                                $wpImage = new WarehouseProductImage();
                                $wpImage->warehouse_product_id = $theWarehouseProduct->id;
                                $wpImage->image_path = $this->imageUpload($file, 'warehouse_product');
                                $wpImage->save();
                            }
                        }
                    }
                }
            }



            // notify users
            $userName = Auth::user()->getFullNameAttribute();
            $noti = NotificationService::notifyAllUsers('New Product created by ' . $userName, 'product');

            return redirect()->route('products.index')->with('message', 'Product created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create product: ' . $e->getMessage())->withInput();
        }
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
        $categories = Category::where('status', 1)->get();
        $sizes = Size::where('status', 1)->get();
        $colors = Color::where('status', 1)->get();

        // Get warehouses for assignment
        if (auth()->user()->hasRole('SUPER ADMIN') || auth()->user()->hasRole('ADMINISTRATOR')) {
            $warehouses = WareHouse::where('is_active', 1)->get();
        } else {
            $warehouses = auth()->user()->warehouses;
        }

        if (auth()->user()->hasRole('SUPER ADMIN') || auth()->user()->hasRole('ADMINISTRATOR') || auth()->user()->isWarehouseAdmin()) {
            $product = Product::findOrFail($id);

            // Get existing warehouse products
            $warehouseProducts = WarehouseProduct::where('product_id', $product->id)->get();

            return view('user.product.edit', compact('product', 'categories', 'sizes', 'colors', 'warehouses', 'warehouseProducts'));
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
        if (auth()->user()->hasRole('SUPER ADMIN') || auth()->user()->hasRole('ADMINISTRATOR') || auth()->user()->isWarehouseAdmin()) {
            $request->validate([
                // 'category_id' => 'required|numeric|exists:categories,id',
                // 'name' => 'required|string|max:255',
                // 'description' => 'required|string',
                // 'short_description' => 'required|string',
                // 'specification' => 'required|string',
                // 'price' => 'required|numeric',
                // 'slug' => 'required|string|unique:products,slug,' . $id,
                // 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
                // 'images' => 'nullable|array',
                // 'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
                // 'feature_product' => 'required',
                // 'status' => 'required',

                // // Warehouse product validation
                // 'warehouse_products' => 'nullable|array',
                // 'warehouse_products.*.id' => 'nullable|exists:warehouse_products,id',
                // 'warehouse_products.*.warehouse_id' => 'required|exists:ware_houses,id',
                // 'warehouse_products.*.sku' => 'required|string|distinct',
                // 'warehouse_products.*.price' => 'required|numeric|min:0',
                // 'warehouse_products.*.color_id' => 'nullable|exists:colors,id',
                // 'warehouse_products.*.size_id' => 'nullable|exists:sizes,id',
                // 'warehouse_products.*.quantity' => 'required|integer|min:0',
                // 'warehouse_products.*.images' => 'nullable|array',
                // 'warehouse_products.*.images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp',

                // // Other charges validation
                // 'other_charges' => 'nullable|array',
                // 'other_charges.*.charge_name' => 'nullable|string|max:255',
                // 'other_charges.*.charge_amount' => 'nullable|numeric|min:0',
            ]);

            $product = Product::findOrFail($id);
            $product->category_id = $request->category_id;
            $product->name = $request->name;
            $product->description = $request->description;
            $product->short_description = $request->short_description;
            $product->specification = $request->specification;
            $product->price = $request->price;
            $product->slug = $request->slug;
            $product->feature_product = $request->feature_product;
            $product->status = $request->status;
            $product->save();

            if ($request->hasFile('image')) {
                $image = ProductImage::where('product_id', $product->id)->where('featured_image', 1)->first();
                if ($image) {
                    $image->delete();
                }
                $image = new ProductImage();
                $image->product_id = $product->id;
                $image->image = $this->imageUpload($request->file('image'), 'product');
                $image->featured_image = 1;
                $image->save();
            }

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
                foreach ($request->sizes as $size) {
                    if ($size) {
                        $product->sizes()->create(['size_id' => $size]);
                    }
                }
            }

            // Update colors
            $product->colors()->delete();
            if ($request->filled('colors')) {
                foreach ($request->colors as $color) {
                    if ($color) {
                        $product->colors()->create(['color_id' => $color]);
                    }
                }
            }

            // save other charges
            $product->otherCharges()->delete();
            if ($request->filled('other_charges')) {
                foreach ($request->other_charges as $charge) {
                    if ($charge['charge_name'] && $charge['charge_amount']) {
                        $product->otherCharges()->create($charge);
                    }
                }
            }

            // Update warehouse products
            if ($request->filled('warehouse_products')) {
                // Get existing warehouse product IDs
                $existingIds = [];

                foreach ($request->warehouse_products as $wp) {
                    if (!empty($wp['warehouse_id'])) {
                        if (!empty($wp['id'])) {
                            // Update existing warehouse product
                            $warehouseProduct = WarehouseProduct::find($wp['id']);
                            if ($warehouseProduct) {
                                $warehouseProduct->update([
                                    'warehouse_id' => $wp['warehouse_id'],
                                    'sku' => $wp['sku'],
                                    'price' => $wp['price'],
                                    'color_id' => $wp['color_id'] ?? null,
                                    'size_id' => $wp['size_id'] ?? null,
                                    'quantity' => $wp['quantity'],
                                ]);

                                // Handle new images for existing warehouse product
                                if (!empty($wp['images'])) {
                                    foreach ($wp['images'] as $file) {
                                        $wpImage = new WarehouseProductImage();
                                        $wpImage->warehouse_product_id = $warehouseProduct->id;
                                        $wpImage->image_path = $this->imageUpload($file, 'warehouse_product');
                                        $wpImage->save();
                                    }
                                }

                                $existingIds[] = $warehouseProduct->id;
                            }
                        } else {
                            // Create new warehouse product
                            $warehouseProduct = WarehouseProduct::create([
                                'product_id' => $product->id,
                                'warehouse_id' => $wp['warehouse_id'],
                                'sku' => $wp['sku'],
                                'price' => $wp['price'],
                                'color_id' => $wp['color_id'] ?? null,
                                'size_id' => $wp['size_id'] ?? null,
                                'quantity' => $wp['quantity'],
                            ]);

                            // Save warehouse product images
                            if (!empty($wp['images'])) {
                                foreach ($wp['images'] as $file) {
                                    $wpImage = new WarehouseProductImage();
                                    $wpImage->warehouse_product_id = $warehouseProduct->id;
                                    $wpImage->image_path = $this->imageUpload($file, 'warehouse_product');
                                    $wpImage->save();
                                }
                            }

                            $existingIds[] = $warehouseProduct->id;
                        }
                    }
                }

                // Delete warehouse products that were removed
                WarehouseProduct::where('product_id', $product->id)
                    ->whereNotIn('id', $existingIds)
                    ->delete();
            } else {
                // Delete all warehouse products if none were submitted
                WarehouseProduct::where('product_id', $product->id)->delete();
            }

            return redirect()->route('products.index')->with('message', 'Product updated successfully!');
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
        if (auth()->user()->hasRole('SUPER ADMIN') || auth()->user()->hasRole('ADMINISTRATOR')) {
            $product = Product::findOrFail($id);
            $product->is_deleted = true;
            $product->save();

            EcomWishList::where('product_id', $product->id)->delete();
            EstoreCart::where('product_id', $product->id)->delete();

            return redirect()->route('products.index')->with('message', 'Product deleted successfully!');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function imageDelete(Request $request)
    {
        $image = ProductImage::findOrFail($request->id);
        $image->delete();
        return response()->json(['message' => 'Image deleted successfully!']);
    }

    public function warehouseImageDelete(Request $request)
    {
        $image = WarehouseProductImage::findOrFail($request->id);
        // Delete the file from storage if needed
        if (file_exists(storage_path('app/public/' . $image->image_path))) {
            unlink(storage_path('app/public/' . $image->image_path));
        }
        $image->delete();
        return response()->json(['message' => 'Warehouse product image deleted successfully!']);
    }
}
