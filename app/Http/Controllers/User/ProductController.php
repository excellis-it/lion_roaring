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
use App\Models\ProductOtherCharge;
use App\Models\ProductVariation;
use App\Models\ProductVariationImage;
use App\Models\Review;
use App\Models\WarehouseProductVariation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
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
        // // return User::with('roles')->where('id', auth()->id())->first();
        // if (auth()->user()->hasRole('SUPER ADMIN') || auth()->user()->hasRole('ADMINISTRATOR')) {
        //     $products = Product::where('is_deleted', false)->orderBy('id', 'desc')->paginate(10);
        //     return view('user.product.list', compact('products'));
        // } else if (auth()->user()->isWarehouseAdmin()) {
        //     // $products = Product::where('user_id', auth()->id())->orderBy('id', 'desc')->paginate(10);
        //     // products where warehouse those products which warehouse use inn
        //     // Get warehouse IDs that this admin can manage
        //     $warehouseIds = auth()->user()->warehouses->pluck('id')->toArray();

        //     // Get products that exist in any of these warehouses
        //     $products = Product::where('is_deleted', false)->whereHas('warehouseProducts', function ($query) use ($warehouseIds) {
        //         $query->whereIn('warehouse_id', $warehouseIds);
        //     })->orderBy('id', 'desc')->paginate(10);
        //     return view('user.product.list', compact('products'));
        // } else {
        //     abort(403, 'You do not have permission to access this page.');
        // }
        // return User::with('roles')->where('id', auth()->id())->first();
        if (auth()->user()->can('Manage Estore Products') || auth()->user()->isWarehouseAdmin()) {
            $products = Product::where('is_deleted', false)->orderBy('id', 'desc')->paginate(10);
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
            // if (auth()->user()->isWarehouseAdmin()) {
            //     $warehouseIds = auth()->user()->warehouses->pluck('id')->toArray();
            //     $products = $products->whereHas('warehouseProducts', function ($q) use ($warehouseIds) {
            //         $q->whereIn('warehouse_id', $warehouseIds);
            //     });
            // } elseif (!auth()->user()->hasRole('SUPER ADMIN') && !auth()->user()->hasRole('ADMINISTRATOR')) {
            //     abort(403, 'You do not have permission to access this page.');
            // }

            if (!auth()->user()->can('Manage Estore Products') && !auth()->user()->isWarehouseAdmin()) {
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

    public function checkSlug(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
    ]);

    $base = Str::slug($request->name);

    // fallback when slug becomes empty (e.g. name had only special chars)
    if (empty($base)) {
        $base = Str::random(8);
    }

    // Get all slugs that start with base (including base itself)
    $existing = Product::where('slug', 'LIKE', $base . '%')
                ->pluck('slug') // collection of strings
                ->toArray();

    // if base doesn't exist, return it immediately
    if (! in_array($base, $existing)) {
        return response()->json(['slug' => $base]);
    }

    // find max numeric suffix
    $max = 0;
    $pattern = '/^' . preg_quote($base, '/') . '-(\d+)$/';
    foreach ($existing as $s) {
        if (preg_match($pattern, $s, $m)) {
            $num = (int) $m[1];
            if ($num > $max) $max = $num;
        }
    }

    $newSlug = $base . '-' . ($max + 1);

    return response()->json(['slug' => $newSlug]);
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

        if (auth()->user()->can('Create Estore Products') || auth()->user()->isWarehouseAdmin()) {
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
        // try {
        // Base rules
        $rules = [
            'category_id'       => 'required|numeric|exists:categories,id',
            'name'              => 'required|string|max:255',
            'description'       => 'required|string',
            'specification'     => 'required|string',
            'feature_product'   => 'required|in:0,1',
            'slug'              => 'required|string|unique:products,slug|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            'image'             => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'background_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            // gallery: at least 1 image (your form label requires at least 1)
            'images'            => 'nullable|array|min:1',
            'images.*'          => 'image|mimes:jpeg,png,jpg,gif,svg,webp',
            // product type
            'product_type'      => 'required|in:simple,variable',
            // toggles
            'is_free'           => 'nullable|in:0,1',
            'status'            => 'nullable|in:0,1',
            // other charges (if present)
            'other_charges' => 'nullable|array',
            'other_charges.*.charge_name' => 'nullable|string|max:255',
            'other_charges.*.charge_amount' => 'nullable|numeric|min:0', // base rule

        ];



        // Conditional rules for simple product
        if ($request->input('product_type', 'simple') === 'simple') {
            // If product is marked free, price may be nullable (we'll set 0 server-side).
            if ($request->boolean('is_free')) {
                $rules['price'] = 'nullable|numeric|min:0';
            } else {
                $rules['price'] = 'required|numeric|min:0';
            }

            // SKU and quantity always required for simple product
            $rules['sku'] = 'required|string|max:255|unique:products,sku';
            $rules['quantity'] = 'required|integer|min:0';
        }

        // Conditional rules for variable product
        if ($request->input('product_type') === 'variable') {
            // Require at least one size or one color for variations (adjust as your logic)
            $rules['sizes'] = 'required|array|min:1';
            $rules['sizes.*'] = 'integer|exists:sizes,id';

            // if you use colors for variable product uncomment/adjust:
            // $rules['colors'] = 'nullable|array';
            // $rules['colors.*'] = 'integer|exists:colors,id';

            // For variable products we don't require simple fields
            // ensure sku/price/quantity are not required here (they belong to variations)
        }

        // Custom messages
        $messages = [
            'category_id.required' => 'The category field is required.',
            'category_id.exists' => 'The selected category is invalid.',
            'name.required' => 'The product name field is required.',
            'description.required' => 'The description field is required.',
            'specification.required' => 'The specification field is required.',
            'feature_product.required' => 'The feature product field is required.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug has already been taken.',
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, and hyphens, without spaces.',
            'image.required' => 'The featured image field is required.',
            'images.required' => 'Please upload at least one gallery image.',
            'images.min' => 'Please upload at least one gallery image.',
            'price.required' => 'The price field is required for simple products (unless marked free).',
            'sku.required' => 'The SKU field is required for simple products.',
            'sku.unique' => 'The SKU has already been taken.',
            'quantity.required' => 'The stock quantity is required for simple products.',
            'sizes.required' => 'Please select at least one size for variable products.',
            'other_charges.*.charge_name.required_with' => 'Charge name is required when adding other charges.',
            'other_charges.*.charge_amount.required_with' => 'Charge amount is required when adding other charges.',
            'other_charges.*.charge_amount.min' => 'Charge amount must be at least 0.',
            'product_type.required' => 'The product type field is required.',
            'product_type.in' => 'The selected product type is invalid.',
        ];

        // Run validator (returns JSON errors for AJAX)
        $validator = Validator::make($request->all(), $rules, $messages);

        $validator->sometimes('other_charges.*.charge_amount', 'required', function ($input, $itemKey) {
            // $input->other_charges is an array
            foreach ($input->other_charges as $index => $charge) {
                if (!empty($charge['charge_name'])) {
                    // charge_amount is required if charge_name is filled
                    return true;
                }
            }
            return false;
        });

        $validator->validate();

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validation passed - get validated data
        $validatedData = $validator->validated();

        $product = new Product();
        $product->category_id = $request->category_id;
        $product->user_id = auth()->user()->id;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->short_description = $request->short_description ?? '';
        $product->specification = $request->specification;
        $product->product_type = $request->product_type; // 'simple' or 'variable'
        $product->sku = $request->sku ?? '';
        $product->quantity = $request->quantity ?? 0;
        $product->price = $request->price;
        $product->sale_price = $request->sale_price ?? null;
        $product->slug = $request->slug;
        $product->feature_product = $request->feature_product;
        $product->is_new_product = $request->is_new_product;
        $product->is_free = $request->has('is_free');
        if ($product->is_free) {
            $product->price = 0;
            $product->sale_price = null;
        }


        // background_image
        if ($request->hasFile('background_image')) {
            $product->background_image = $this->imageUpload($request->file('background_image'), 'product');
        }

        $product->save();

        if ($request->hasFile('image')) {
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
        // if product_type is simple then direct create product variation without color and sizes
        if ($product->product_type == 'simple') {

            $variation = new ProductVariation();
            $variation->product_id = $product->id;
            $variation->sku = $product->sku;
            $variation->price = $product->price;
            $variation->sale_price = $product->sale_price ? $product->sale_price : null;
            $variation->before_sale_price = $product->sale_price ? $product->price : null;
            $variation->stock_quantity = $product->quantity;
            $variation->additional_info = $product->product_type;
            $variation->save();

            // Copy product images to variation images
            $productImages = $product->images;
            foreach ($productImages as $pImage) {
                $variationImage = new ProductVariationImage();
                $variationImage->product_variation_id = $variation->id;
                $variationImage->image_path = $pImage->image;
                $variationImage->save();
            }

            // create WarehouseProductVariation and warehouse products and warehouse product images for this variation in all warehouses
            $warehouses = [];
            if (auth()->user()->hasRole('SUPER ADMIN') || auth()->user()->hasRole('ADMINISTRATOR')) {
                $warehouses = WareHouse::where('is_active', 1)->get();
            } else {
                $warehouses = auth()->user()->warehouses;
            }

            foreach ($warehouses as $warehouse) {
                $wpv = new WarehouseProductVariation();
                $wpv->product_variation_id = $variation->id;
                $wpv->warehouse_id = $warehouse->id;
                $wpv->product_id = $variation->product_id;
                $wpv->warehouse_quantity = 0;
                $wpv->save();

                // create warehouse product
                $theWarehouseProduct = WarehouseProduct::create([
                    'product_variation_id' => $variation->id,
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'sku' => $variation->sku,
                    'price' => $variation->sale_price ? $variation->sale_price : $variation->price,
                    'before_sale_price' => $variation->before_sale_price ?? null,
                    'color_id' => null,
                    'size_id' => null,
                    'quantity' => 0,
                ]);

                // Copy variation images to warehouse product images
                $variationImages = $variation->images;
                foreach ($variationImages as $vImage) {
                    $wpImage = new WarehouseProductImage();
                    $wpImage->warehouse_product_id = $theWarehouseProduct->id;
                    $wpImage->image_path = $vImage->image_path;
                    $wpImage->save();
                }
            }
        }




        // notify users
        $userName = Auth::user()->getFullNameAttribute();
        $noti = NotificationService::notifyAllUsers('New Product created by ' . $userName, 'product');
        session()->flash('message', 'Product created successfully!');
        return response()->json([
            'message' => 'Product created successfully!',
            'product' => $product
        ], 200);
        // } catch (\Exception $e) {
        //     return redirect()->back()->with('error', 'Failed to create product: ' . $e->getMessage())->withInput();
        // }
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

        if (auth()->user()->can('Edit Estore Products') || auth()->user()->isWarehouseAdmin()) {
            $product = Product::findOrFail($id);

            // Get existing warehouse products
            $warehouseProducts = WarehouseProduct::where('product_id', $product->id)->get();
            $productSizes = $product->sizesWithDetails();

            return view('user.product.edit', compact('product', 'categories', 'sizes', 'colors', 'warehouses', 'warehouseProducts', 'productSizes'));
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
        $product = Product::findOrFail($id);
        // return $request->all();
        if (auth()->user()->can('Edit Estore Products') || auth()->user()->isWarehouseAdmin()) {
            $rules = [
                'category_id'      => 'required|numeric|exists:categories,id',
                'name'             => 'required|string|max:255',
                'description'      => 'required|string',
                'specification'    => 'required|string',
                'feature_product'  => 'required|in:0,1',
                'slug'             => [
                    'required',
                    'string',
                    Rule::unique('products', 'slug')->ignore($product->id),
                    'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                ],
                'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
                'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
                'images'           => 'nullable|array|min:1',
                'images.*'         => 'image|mimes:jpeg,png,jpg,gif,svg,webp',
                'product_type'     => 'required|in:simple,variable',
                'is_free'          => 'nullable|in:0,1',
                'status'           => 'nullable|in:0,1',
                // other charges (if present)
                'other_charges'             => 'nullable|array',
                'other_charges.*.charge_name' => 'nullable|string|max:255',
                'other_charges.*.charge_amount' => 'nullable|numeric|min:0',
            ];

            // Conditional rules for simple product
            // if ($product->product_type === 'simple') {
            //     if ($request->boolean('is_free')) {
            //         $rules['price'] = 'nullable|numeric|min:0';
            //     } else {
            //         $rules['price'] = 'required|numeric|min:0';
            //     }
            //     $rules['sku'] = [
            //         'required',
            //         'string',
            //         'max:255',
            //         Rule::unique('products', 'sku')->ignore($product->id),
            //     ];
            //     $rules['quantity'] = 'required|integer|min:0';
            // }

            // Conditional rules for variable product
            if ($product->product_type === 'variable') {
                $rules['sizes'] = 'required|array|min:1';
                $rules['sizes.*'] = 'integer|exists:sizes,id';
            }

            $messages = [
                'category_id.required' => 'The category field is required.',
                'category_id.exists' => 'The selected category is invalid.',
                'name.required' => 'The product name field is required.',
                'description.required' => 'The description field is required.',
                'specification.required' => 'The specification field is required.',
                'feature_product.required' => 'The feature product field is required.',
                'slug.required' => 'The slug field is required.',
                'slug.unique' => 'The slug has already been taken.',
                'slug.regex' => 'The slug may only contain lowercase letters, numbers, and hyphens, without spaces.',
                'image.required' => 'The featured image field is required.',
                'images.min' => 'Please upload at least one gallery image.',
                'sizes.required' => 'Please select at least one size for variable products.',
                'other_charges.*.charge_amount.min' => 'Charge amount must be at least 0.',
                'other_charges.*.charge_amount.required_with' => 'Charge amount is required when adding other charges.',
                'other_charges.*.charge_name.required_with' => 'Charge name is required when adding other charges.',
                'product_type.required' => 'The product type field is required.',
                'product_type.in' => 'The selected product type is invalid.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            // Make charge_amount required if charge_name is present
            $validator->sometimes('other_charges.*.charge_amount', 'required', function ($input) {
                if (!empty($input->other_charges) && is_array($input->other_charges)) {
                    foreach ($input->other_charges as $charge) {
                        if (!empty($charge['charge_name'])) {
                            return true;
                        }
                    }
                }
                return false;
            });

            $validatedData = $validator->validate(); // throws 422 if fails


            $product->category_id = $request->category_id;
            $product->name = $request->name;
            $product->description = $request->description;
            $product->short_description = $request->short_description ?? '';
            $product->specification = $request->specification;
            //  $product->product_type = $request->product_type;
            // $product->sku = $request->sku ?? '';
            // $product->quantity = $request->quantity ?? 0;
            // $product->price = $request->price;
            $product->slug = $request->slug;
            $product->feature_product = $request->feature_product;
            $product->is_new_product = $request->is_new_product;
            $product->status = $request->status;
            $product->is_free = $request->has('is_free');
            if ($product->is_free) {
                $product->price = 0;
                ProductVariation::where('product_id', $product->id)->update([
                    'price' => 0,
                    'sale_price' => null,
                    'before_sale_price' => null,
                ]);

                WarehouseProduct::where('product_id', $product->id)->update([
                    'price' => 0,
                    'before_sale_price' => null,
                ]);
            }



            // background_image
            if ($request->hasFile('background_image')) {
                // delete old image from storage
                if ($product->background_image && file_exists(storage_path('app/public/' . $product->background_image))) {
                    unlink(storage_path('app/public/' . $product->background_image));
                }
                $product->background_image = $this->imageUpload($request->file('background_image'), 'product');
            }

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

                    // if product type is simple then also add this image to product variation images and warehouse product images

                    if ($product->product_type == 'simple') {
                        $variation = ProductVariation::where('product_id', $product->id)->first();
                        if ($variation) {
                            $variationImage = new ProductVariationImage();
                            $variationImage->product_variation_id = $variation->id;
                            $variationImage->image_path = $image->image;
                            $variationImage->save();

                            // add this image to all warehouse products images of this variation
                            $warehouseProducts = WarehouseProduct::where('product_variation_id', $variation->id)->get();
                            foreach ($warehouseProducts as $wp) {
                                $wpImage = new WarehouseProductImage();
                                $wpImage->warehouse_product_id = $wp->id;
                                $wpImage->image_path = $image->image;
                                $wpImage->save();
                            }
                        }
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

            if ($request->product_type == 'variable') {
                // Save sizes
                $product->sizes()->delete();
                if ($request->filled('sizes')) {
                    foreach ($request->sizes as $size) {
                        if ($size) {
                            $product->sizes()->create(['size_id' => $size]);
                        }
                    }
                }

                // // Save colors
                // $product->colors()->delete();
                // if ($request->filled('colors')) {
                //     foreach ($request->colors as $color) {
                //         if ($color) {
                //             $product->colors()->create(['color_id' => $color]);
                //         }
                //     }
                // }
            }

            // if ($request->product_type == 'simple') {
            //     $productVariations = ProductVariation::where('product_id', $product->id)->get();
            //     // update product variations price and stock_quantity from product price and quantity
            //     foreach ($productVariations as $variation) {
            //         $variation->price = $product->price;
            //         $variation->stock_quantity = $product->quantity;
            //         $variation->save();
            //     }
            // }

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
        if (auth()->user()->can('Delete Estore Products')) {
            $product = Product::findOrFail($id);
            $product->is_deleted = true;
            $product->save();

            EcomWishList::where('product_id', $product->id)->delete();
            EstoreCart::where('product_id', $product->id)->delete();
            Review::where('product_id', $product->id)->delete();
            ProductOtherCharge::where('product_id', $product->id)->delete();
            ProductVariation::where('product_id', $product->id)->delete();
            WarehouseProductVariation::where('product_id', $product->id)->delete();

            return redirect()->route('products.index')->with('message', 'Product deleted successfully!');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function imageDelete(Request $request)
    {
        $image = ProductImage::findOrFail($request->id);
        $image->delete();
        // then also delete from product variation images and warehouse product images if exists and product type is simple

        if ($image->featured_image == 0) {
            $product = Product::find($image->product_id);
            if ($product && $product->product_type == 'simple') {
                $variation = ProductVariation::where('product_id', $product->id)->first();
                if ($variation) {
                    // delete from product variation images
                    $pVarImage = ProductVariationImage::where('product_variation_id', $variation->id)
                        ->where('image_path', $image->image)
                        ->first();
                    if ($pVarImage) {
                        $pVarImage->delete();
                    }

                    // delete from warehouse product images
                    $warehouseProducts = WarehouseProduct::where('product_variation_id', $variation->id)->get();
                    foreach ($warehouseProducts as $wp) {
                        $wpImage = WarehouseProductImage::where('warehouse_product_id', $wp->id)
                            ->where('image_path', $image->image)
                            ->first();
                        if ($wpImage) {
                            $wpImage->delete();
                        }
                    }
                }
            }
        }

        // Delete the file from storage if needed
        if (file_exists(storage_path('app/public/' . $image->image))) {
            unlink(storage_path('app/public/' . $image->image));
        }




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

    // variations
    public function variations($id)
    {
        $product = Product::findOrFail($id);
        $product_variations = ProductVariation::where('product_id', $product->id)->orderBy('id', 'desc')->get();
        $colors = Color::where('status', 1)->get();
        $productSizes = $product->sizesWithDetails();

        // return $productSizes;

        if (auth()->user()->can('Edit Estore Products') || auth()->user()->isWarehouseAdmin()) {




            return view('user.product.variations', compact('product', 'product_variations', 'colors', 'productSizes'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    // generateVariations
    public function generateVariations(Request $request)
    {
        // return $request->all();
        $request->validate([
            'colors' => 'nullable|array',
            'colors.*' => 'exists:colors,id',
            'sizes' => 'nullable|array',
            'sizes.*' => 'exists:sizes,id',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Get selected colors and sizes
        $selectedColors = $request->input('colors', []);
        $selectedSizes = $request->input('sizes', []);

        // Generate all combinations of selected colors and sizes
        $combinations = [];
        if (!empty($selectedColors) && !empty($selectedSizes)) {
            foreach ($selectedColors as $color) {
                foreach ($selectedSizes as $size) {
                    $combinations[] = ['color_id' => $color, 'size_id' => $size];
                }
            }
        } elseif (!empty($selectedColors)) {
            foreach ($selectedColors as $color) {
                $combinations[] = ['color_id' => $color, 'size_id' => null];
            }
        } elseif (!empty($selectedSizes)) {
            foreach ($selectedSizes as $size) {
                $combinations[] = ['color_id' => null, 'size_id' => $size];
            }
        }

        // Create or update product variations based on combinations
        foreach ($combinations as $combination) {
            // Check if variation already exists
            $existingVariation = ProductVariation::where('product_id', $product->id)
                ->where('color_id', $combination['color_id'])
                ->where('size_id', $combination['size_id'])
                ->first();

            if (!$existingVariation) {
                $newVariation = ProductVariation::create([
                    'product_id' => $product->id,
                    'sku' => strtoupper(uniqid('SKU-' . $combination['color_id'] . '-' . $combination['size_id'] . '-')),
                    'price' => 0.00,
                    'stock_quantity' => 0,
                    'color_id' => $combination['color_id'],
                    'size_id' => $combination['size_id'],
                    'additional_info' => null,
                ]);

                $colorSiblings = ProductVariation::where('product_id', $product->id)
                    ->where('id', '!=', $newVariation->id);

                if (is_null($combination['color_id'])) {
                    $colorSiblings->whereNull('color_id');
                } else {
                    $colorSiblings->where('color_id', $combination['color_id']);
                }

                $siblingIds = $colorSiblings->pluck('id')->toArray();

                if (!empty($siblingIds)) {
                    $imagePaths = ProductVariationImage::whereIn('product_variation_id', $siblingIds)
                        ->pluck('image_path')
                        ->unique();

                    foreach ($imagePaths as $path) {
                        ProductVariationImage::create([
                            'product_variation_id' => $newVariation->id,
                            'image_path' => $path,
                        ]);
                    }

                    $newWarehouseProducts = WarehouseProduct::where('product_variation_id', $newVariation->id)->get();
                    foreach ($newWarehouseProducts as $wp) {
                        foreach ($imagePaths as $path) {
                            WarehouseProductImage::create([
                                'warehouse_product_id' => $wp->id,
                                'image_path' => $path,
                            ]);
                        }
                    }
                }
            }
        }

        return redirect()->route('products.variations', $product->id)->with('message', 'Product variations generated successfully!');
    }

    // deleteVariation
    public function deleteVariation(Request $request)
    {
        $id = $request->id;
        if (auth()->user()->hasRole('SUPER ADMIN') || auth()->user()->hasRole('ADMINISTRATOR') || auth()->user()->isWarehouseAdmin()) {
            $variation = ProductVariation::findOrFail($id);
            $productId = $variation->product_id;
            $variation->delete();

            // delete also the variation images and WarehouseProduct and WarehouseProductImage
            $images = ProductVariationImage::where('product_variation_id', $id)->get();
            foreach ($images as $img) {
                if (file_exists(storage_path('app/public/' . $img->image_path))) {
                    @unlink(storage_path('app/public/' . $img->image_path));
                }
                $img->delete();
            }
            $wpIds = WarehouseProduct::where('product_variation_id', $id)->pluck('id')->toArray();
            $wpImages = WarehouseProductImage::whereIn('warehouse_product_id', $wpIds)->get();
            foreach ($wpImages as $img) {
                if (file_exists(storage_path('app/public/' . $img->image_path))) {
                    @unlink(storage_path('app/public/' . $img->image_path));
                }
                $img->delete();
            }
            WarehouseProduct::where('product_variation_id', $id)->delete();

            // return redirect()->route('products.variations', $productId)->with('message', 'Product variation deleted successfully!');
            return response()->json(['message' => 'Product variation deleted successfully!']);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    // update products.variations.update with save images
    public function updateVariations(Request $request)
    {
        // return $request->all();
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variation_products' => 'required|array',
            'variation_products.*.id' => 'required|exists:product_variations,id',
            'variation_products.*.sku' => 'required|string|max:255',
            'variation_products.*.price' => 'required|numeric|min:0',
            'variation_products.*.sale_price' => 'nullable|numeric|min:0',
            'variation_products.*.stock_quantity' => 'required|integer|min:0',
            'variation_products.*.color_id' => 'nullable|exists:colors,id',
            'variation_products.*.size_id' => 'nullable|exists:sizes,id',
            'variation_products.*.additional_info' => 'nullable|string|max:1000',
            'variation_products.*.images' => 'nullable|array',
            'variation_products.*.images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp',
        ]);

        $product = Product::findOrFail($request->product_id);
        $first_price = 0;

        foreach ($request->variation_products as $variationData) {

            $variation = ProductVariation::findOrFail($variationData['id']);
            $variation->sku = $variationData['sku'];
            $variation->price = $variationData['price'];
            $variation->sale_price = $variationData['sale_price'] ? $variationData['sale_price'] : null;
            $variation->before_sale_price = $variationData['sale_price'] ? $variationData['price'] : null;
            $variation->stock_quantity = $variationData['stock_quantity'];
            $variation->color_id = $variationData['color_id'];
            $variation->size_id = $variationData['size_id'];
            $variation->additional_info = '';
            $variation->save();

            // get product variation in WarehouseProductVariation
            $wpVariations = WarehouseProductVariation::where('product_variation_id', $variation->id)->get();


            // update product variation in WarehouseProduct
            $wpProducts = WarehouseProduct::where('product_variation_id', $variation->id)->get();
            foreach ($wpProducts as $wp) {
                $wp->sku = $variation->sku;
                $wp->price = $variation->sale_price ? $variation->sale_price : $variation->price;
                $wp->before_sale_price = $variation->before_sale_price ?? null;
                $wp->save();
            }

            // update images if available to ProductVariationImage and WarehouseProductImage (use same path)
            if (!empty($variationData['images'])) {
                $targetColorId = $variationData['color_id'] ?? $variation->color_id;

                $query = ProductVariation::where('product_id', $product->id);
                if (is_null($targetColorId)) {
                    $query->whereNull('color_id');
                } else {
                    $query->where('color_id', $targetColorId);
                }
                $variationsWithSameColor = $query->get();
                if ($variationsWithSameColor->isEmpty()) {
                    $variationsWithSameColor = collect([$variation]);
                }
                $variationIdsForColor = $variationsWithSameColor->pluck('id')->all();

                $warehouseProductsForColor = WarehouseProduct::whereIn('product_variation_id', $variationIdsForColor)->get();

                foreach ($variationData['images'] as $file) {
                    $path = $this->imageUpload($file, 'product_variation');

                    foreach ($variationsWithSameColor as $var) {
                        $pvImage = new ProductVariationImage();
                        $pvImage->product_variation_id = $var->id;
                        $pvImage->image_path = $path;
                        $pvImage->save();
                    }

                    foreach ($warehouseProductsForColor as $wp) {
                        $wpImage = new WarehouseProductImage();
                        $wpImage->warehouse_product_id = $wp->id;
                        $wpImage->image_path = $path;
                        $wpImage->save();
                    }
                }
            }

            // Set first price for product price update for from first variation only
            // Prioritize any variation that has a sale_price. If found, use that variation's price/sale_price.
            if (isset($variationData['sale_price']) && $variationData['sale_price'] !== null) {
                $product->price = $variationData['price'];
                $product->sale_price = $variationData['sale_price'];
                $product->save();
                // mark that a sale price was captured so later non-sale variations won't overwrite it
                $first_price = -1;
            } else {
                // If no sale price captured yet, use the first variation's price as the product price
                if ($first_price === 0) {
                    $first_price = $variationData['price'];
                    $product->price = $variationData['price'];
                    $product->sale_price = null;
                    $product->save();
                }
            }
        }

        return redirect()->route('products.variations', $product->id)->with('message', 'Product variations updated successfully!');
    }

    //deleteVariationImage
    public function deleteVariationImage(Request $request)
    {
        $image = ProductVariationImage::findOrFail($request->id);
        $targetImagePath = $image->image_path;
        //  return $targetImagePath;

        // Get the variation to know product_id and color_id
        $variation = ProductVariation::findOrFail($image->product_variation_id);
        $productId = $variation->product_id;
        $colorId = $variation->color_id;

        // Get all variation IDs for the same product and same color (null handled)
        $query = ProductVariation::where('product_id', $productId);
        if (is_null($colorId)) {
            $query->whereNull('color_id');
        } else {
            $query->where('color_id', $colorId);
        }
        $variationIds = $query->pluck('id')->toArray();

        // Find and delete all images for those variations that match the same image path
        $images = ProductVariationImage::whereIn('product_variation_id', $variationIds)
            ->where('image_path', $targetImagePath)
            ->get();

        foreach ($images as $img) {
            if (file_exists(storage_path('app/public/' . $img->image_path))) {
                @unlink(storage_path('app/public/' . $img->image_path));
            }
            $img->delete();
        }


        // remove images from WarehouseProductImage by WarehouseProduct have product_variation_id
        $wpIds = WarehouseProduct::whereIn('product_variation_id', $variationIds)->pluck('id')->toArray();

        $wpImages = WarehouseProductImage::whereIn('warehouse_product_id', $wpIds)
            ->where('image_path', $targetImagePath)
            ->get();
        // return $wpImages;

        foreach ($wpImages as $img) {
            if (file_exists(storage_path('app/public/' . $img->image_path))) {
                @unlink(storage_path('app/public/' . $img->image_path));
            }
            $img->delete();
        }

        return response()->json(['message' => 'Product variation image deleted successfully!']);
    }
}
