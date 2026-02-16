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
use App\Models\EcomWishList;
use App\Models\EstoreCart;
use App\Models\ProductOtherCharge;
use App\Models\ProductVariation;
use App\Models\ProductColorImage;
use App\Models\Review;
use App\Models\WarehouseProductVariation;
use App\Models\MarketMaterial;
use App\Services\MarketRateService;
use App\Models\ProductFile;
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
        // if (auth()->user()->hasNewRole('SUPER ADMIN') || auth()->user()->hasNewRole('ADMINISTRATOR')) {
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
            $categories = Category::orderBy('name')->get();
            return view('user.product.list', compact('products', 'categories'));
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
            $categoryId = $request->get('category_id');
            $query = str_replace(" ", "%", $query);

            $products = Product::query();

            // Apply role-based filtering first
            // if (auth()->user()->isWarehouseAdmin()) {
            //     $warehouseIds = auth()->user()->warehouses->pluck('id')->toArray();
            //     $products = $products->whereHas('warehouseProducts', function ($q) use ($warehouseIds) {
            //         $q->whereIn('warehouse_id', $warehouseIds);
            //     });
            // } elseif (!auth()->user()->hasNewRole('SUPER ADMIN') && !auth()->user()->hasNewRole('ADMINISTRATOR')) {
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

            if (!empty($categoryId)) {
                $products = $products->where('category_id', $categoryId);
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
        $marketMaterials = MarketMaterial::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        // Get warehouses for assignment
        if (auth()->user()->hasNewRole('SUPER ADMIN') || auth()->user()->hasNewRole('ADMINISTRATOR')) {
            $warehouses = WareHouse::where('is_active', 1)->get();
        } else {
            $warehouses = auth()->user()->warehouses;
        }

        if (auth()->user()->can('Create Estore Products') || auth()->user()->isWarehouseAdmin()) {
            return view('user.product.create')
                ->with(compact('categories', 'sizes', 'colors', 'warehouses', 'marketMaterials'));
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
            'product_type'      => 'required|in:simple,variable,digital',
            // toggles
            'is_free'           => 'nullable|in:0,1',
            'use_market_price'  => 'nullable|in:0,1',
            'market_material_id' => 'nullable|integer|exists:market_materials,id',
            'market_grams'       => 'nullable|numeric|min:0.01',
            // unit for market quantity: 'g' grams (default) or 'oz' ounces
            'market_unit'        => 'nullable|in:g,oz',
            'status'            => 'nullable|in:0,1',
            // other charges (if present)
            'other_charges' => 'nullable|array',
            'other_charges.*.charge_name' => 'nullable|string|max:255',
            'other_charges.*.charge_amount' => 'nullable|numeric|min:0', // base rule

        ];



        // Conditional rules for simple product
        if ($request->input('product_type', 'simple') === 'simple') {
            $useMarketPrice = $request->boolean('use_market_price');

            if ($useMarketPrice) {
                $rules['market_material_id'] = 'required|integer|exists:market_materials,id';
                $rules['market_grams'] = 'required|numeric|min:0.01';
                $rules['market_unit'] = 'required|in:g,oz';
                $rules['market_grams'] = 'required|numeric|min:0.01';
            }

            // If product is marked free, price may be nullable (we'll set 0 server-side).
            if (!$useMarketPrice && $request->boolean('is_free')) {
                $rules['price'] = 'nullable|numeric|min:0';
            } elseif (!$useMarketPrice) {
                $rules['price'] = 'required|numeric|min:0';
            }

            // SKU and quantity always required for simple product
            //  $rules['sku'] = 'required|string|max:255|unique:products,sku';
            $rules['quantity'] = 'required|integer|min:0';
        }

        // Conditional rules for variable product
        if ($request->input('product_type') === 'variable') {
            // Require at least one size or one color for variations (adjust as your logic)
            $rules['sizes'] = 'required|array|min:1';
            $rules['sizes.*'] = 'integer|exists:sizes,id';
        }

        // Conditional rules for digital product
        if ($request->input('product_type') === 'digital') {
            if ($request->boolean('is_free')) {
                $rules['digital_price'] = 'nullable|numeric|min:0';
            } else {
                $rules['digital_price'] = 'required|numeric|min:0';
            }
            $rules['digital_sale_price'] = 'nullable|numeric|min:0';
            $rules['digital_files'] = 'required|array|min:1';
            $rules['digital_files.*'] = 'string'; // File paths from AJAX upload
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
            'market_material_id.required' => 'Please select a market material.',
            'market_grams.required' => 'Please enter quantity for market pricing.',
            'market_grams.min' => 'Quantity must be greater than 0.',
            'market_unit.required' => 'Please select unit (g or oz) for market pricing.',
            'market_unit.in' => 'Selected unit is invalid. Choose g or oz.',
            // 'sku.required' => 'The SKU field is required for simple products.',
            // 'sku.unique' => 'The SKU has already been taken.',
            'quantity.required' => 'The stock quantity is required for simple products.',
            'sizes.required' => 'Please select at least one size for variable products.',
            'other_charges.*.charge_name.required_with' => 'Charge name is required when adding other charges.',
            'other_charges.*.charge_amount.required_with' => 'Charge amount is required when adding other charges.',
            'other_charges.*.charge_amount.min' => 'Charge amount must be at least 0.',
            'product_type.required' => 'The product type field is required.',
            'product_type.in' => 'The selected product type is invalid.',
            'digital_price.required' => 'The price field is required for digital products.',
            'digital_sale_price.numeric' => 'The sale price must be a valid number.',
            'digital_files.required' => 'Please upload at least one digital file.',
            'digital_files.min' => 'Please upload at least one digital file.',
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

        $useMarketPrice = $request->boolean('use_market_price');
        if ($useMarketPrice && $request->boolean('is_free')) {
            return response()->json([
                'errors' => [
                    'use_market_price' => ['Market pricing cannot be used with free products.'],
                ],
            ], 422);
        }

        $computedMarketPrice = null;
        $marketRate = null;
        if ($useMarketPrice && $request->input('product_type') === 'simple') {
            $marketRate = MarketRateService::getLatestRateForMaterial((int) $request->market_material_id);
            if (!$marketRate || !$marketRate->rate_per_gram) {
                return response()->json([
                    'errors' => [
                        'market_material_id' => ['Market price is not available right now. Please try again.'],
                    ],
                ], 422);
            }

            // If unit is ounces, convert to grams before multiplying by rate_per_gram
            $unit = strtolower($request->market_unit ?? 'g');
            $gramsQty = (float) $request->market_grams;
            if ($unit === 'oz') {
                $gramsQty = $gramsQty * 31.1034768; // troy ounce -> grams
            }
            $computedMarketPrice = (float) $marketRate->rate_per_gram * $gramsQty;
        }

        // generate SKU with easy coded in the first 3-5 characters indicate the category. for example:  Agriculture - AGR+number, Science & Innovation - SCI+number, etc.
        $categoryName = Category::find($request->category_id)->name ?? 'GEN';
        $categoryPrefix = substr($categoryName, 0, 3) ?: 'GEN';
        $lastProduct = Product::where('category_id', $request->category_id)->orderBy('id', 'desc')->first();
        $number = $lastProduct ? $lastProduct->id + 1 : 1;
        $generatedSKU = strtoupper(uniqid($categoryPrefix . '-' . $number . '-'));

        $product = new Product();
        $product->category_id = $request->category_id;
        $product->user_id = auth()->user()->id;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->short_description = $request->short_description ?? '';
        $product->specification = $request->specification;
        $product->product_type = $request->product_type; // 'simple' or 'variable'
        $product->sku = $generatedSKU;
        $product->quantity = $request->quantity ?? 0;
        $product->price = $useMarketPrice ? $computedMarketPrice : $request->price;
        $product->sale_price = $useMarketPrice ? null : ($request->sale_price ?? null);
        $product->slug = $request->slug;
        $product->feature_product = $request->feature_product;
        $product->is_new_product = $request->is_new_product;
        $product->is_free = $useMarketPrice ? false : $request->has('is_free');
        $product->is_market_priced = $useMarketPrice;
        $product->market_material_id = $useMarketPrice ? $request->market_material_id : null;
        $product->market_grams = $useMarketPrice ? $request->market_grams : null;
        $product->market_unit = $useMarketPrice ? ($request->market_unit ?? 'g') : null;
        $product->market_rate_per_gram = $useMarketPrice ? $marketRate?->rate_per_gram : null;
        $product->market_rate_at = $useMarketPrice ? $marketRate?->fetched_at : null;
        if ($product->is_free) {
            $product->price = 0;
            $product->sale_price = null;
        }


        // background_image
        if ($request->hasFile('background_image')) {
            $product->background_image = $this->imageUpload($request->file('background_image'), 'product', true);
        }


        $product->save();

        if ($request->hasFile('image')) {
            $image = new ProductImage();
            $image->product_id = $product->id;
            $image->image = $this->imageUpload($request->file('image'), 'product', true);
            $image->featured_image = 1;
            $image->save();
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $image = new ProductImage();
                $image->product_id = $product->id;
                $image->image = $this->imageUpload($file, 'product', true);
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
            $variation->sku = $generatedSKU;
            $variation->price = $product->price;
            $variation->sale_price = $product->sale_price ? $product->sale_price : null;
            $variation->before_sale_price = $product->sale_price ? $product->price : null;
            $variation->stock_quantity = $product->quantity;
            $variation->additional_info = $product->product_type;
            $variation->save();

            // create WarehouseProductVariation and warehouse products and warehouse product images for this variation in all warehouses
            $warehouses = [];
            if (auth()->user()->hasNewRole('SUPER ADMIN') || auth()->user()->hasNewRole('ADMINISTRATOR')) {
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

                // // Copy variation images to warehouse product images
                // $variationImages = $variation->images;
                // foreach ($variationImages as $vImage) {
                //     $wpImage = new WarehouseProductImage();
                //     $wpImage->warehouse_product_id = $theWarehouseProduct->id;
                //     $wpImage->image_path = $vImage->image_path;
                //     $wpImage->save();
                // }
            }
        }

        // Handle digital product
        if ($product->product_type == 'digital') {
            // Set digital product pricing
            $product->price = $request->digital_price;
            $product->sale_price = $request->digital_sale_price ?? null;
            $product->quantity = 0; // Digital products have no stock quantity
            $product->save();

            // Save digital files
            if ($request->filled('digital_files')) {
                foreach ($request->digital_files as $filePath) {
                    ProductFile::create([
                        'product_id' => $product->id,
                        'file_location' => $filePath
                    ]);
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
        $marketMaterials = MarketMaterial::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        // Get warehouses for assignment
        if (auth()->user()->hasNewRole('SUPER ADMIN') || auth()->user()->hasNewRole('ADMINISTRATOR')) {
            $warehouses = WareHouse::where('is_active', 1)->get();
        } else {
            $warehouses = auth()->user()->warehouses;
        }

        if (auth()->user()->can('Edit Estore Products') || auth()->user()->isWarehouseAdmin()) {
            $product = Product::findOrFail($id);

            // Get existing warehouse products
            $warehouseProducts = WarehouseProduct::where('product_id', $product->id)->get();
            $productSizes = $product->sizesWithDetails();

            return view('user.product.edit', compact('product', 'categories', 'sizes', 'colors', 'warehouses', 'warehouseProducts', 'productSizes', 'marketMaterials'));
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
                'product_type'     => 'required|in:simple,variable,digital',
                'is_free'          => 'nullable|in:0,1',
                'use_market_price' => 'nullable|in:0,1',
                'market_material_id' => 'nullable|integer|exists:market_materials,id',
                'market_grams'      => 'nullable|numeric|min:0.01',
                // unit for market quantity: 'g' grams (default) or 'oz' ounces
                'market_unit'       => 'nullable|in:g,oz',
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

            if ($product->product_type === 'simple') {
                $useMarketPrice = $request->boolean('use_market_price');

                if ($useMarketPrice) {
                    $rules['market_material_id'] = 'required|integer|exists:market_materials,id';
                    $rules['market_grams'] = 'required|numeric|min:0.01';
                    $rules['market_unit'] = 'required|in:g,oz';
                }

                if (!$useMarketPrice && !$request->boolean('is_free')) {
                    $rules['price'] = 'required|numeric|min:0';
                }
            }

            // Conditional rules for variable product
            if ($product->product_type === 'variable') {
                $rules['sizes'] = 'required|array|min:1';
                $rules['sizes.*'] = 'integer|exists:sizes,id';
            }

            // Conditional rules for digital product
            if ($product->product_type === 'digital') {
                if ($request->boolean('is_free')) {
                    $rules['digital_price'] = 'nullable|numeric|min:0';
                } else {
                    $rules['digital_price'] = 'required|numeric|min:0';
                }
                $rules['digital_sale_price'] = 'nullable|numeric|min:0';
                $rules['digital_files'] = 'nullable|array';
                $rules['digital_files.*'] = 'string';
                $rules['delete_digital_files'] = 'nullable|array';
                $rules['delete_digital_files.*'] = 'integer|exists:product_files,id';
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
                'price.required' => 'The price field is required for simple products (unless marked free).',
                'market_material_id.required' => 'Please select a market material.',
                'market_grams.required' => 'Please enter quantity for market pricing.',
                'market_grams.min' => 'Quantity must be greater than 0.',
                'market_unit.required' => 'Please select unit (g or oz) for market pricing.',
                'market_unit.in' => 'Selected unit is invalid. Choose g or oz.',
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

            $useMarketPrice = $request->boolean('use_market_price');
            if ($useMarketPrice && $request->boolean('is_free')) {
                return response()->json([
                    'errors' => [
                        'use_market_price' => ['Market pricing cannot be used with free products.'],
                    ],
                ], 422);
            }

            $computedMarketPrice = null;
            $marketRate = null;
            if ($useMarketPrice && $product->product_type === 'simple') {
                $marketRate = MarketRateService::getLatestRateForMaterial((int) $request->market_material_id);
                if (!$marketRate || !$marketRate->rate_per_gram) {
                    return response()->json([
                        'errors' => [
                            'market_material_id' => ['Market price is not available right now. Please try again.'],
                        ],
                    ], 422);
                }

                // If unit is ounces, convert to grams before multiplying by rate_per_gram
                $unit = strtolower($request->market_unit ?? 'g');
                $gramsQty = (float) $request->market_grams;
                if ($unit === 'oz') {
                    $gramsQty = $gramsQty * 31.1034768; // troy ounce -> grams
                }
                $computedMarketPrice = (float) $marketRate->rate_per_gram * $gramsQty;
            }


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
            $product->is_market_priced = $useMarketPrice;
            $product->market_material_id = $useMarketPrice ? $request->market_material_id : null;
            $product->market_grams = $useMarketPrice ? $request->market_grams : null;
            $product->market_unit = $useMarketPrice ? ($request->market_unit ?? 'g') : null;
            $product->market_rate_per_gram = $useMarketPrice ? $marketRate?->rate_per_gram : null;
            $product->market_rate_at = $useMarketPrice ? $marketRate?->fetched_at : null;

            if ($useMarketPrice && $product->product_type === 'simple') {
                $product->price = $computedMarketPrice;
                $product->sale_price = null;
                $product->is_free = false;
            } else {
                $product->is_free = $request->has('is_free');
                if ($product->is_free) {
                    $product->price = 0;
                    $product->sale_price = null;
                } elseif ($product->product_type === 'simple') {
                    $product->price = $request->price;
                    $product->sale_price = $request->sale_price ?? null;
                } elseif ($product->product_type === 'digital') {
                    $product->price = $request->digital_price;
                    $product->sale_price = $request->digital_sale_price ?? null;
                    $product->quantity = 0;
                }
            }



            // background_image
            if ($request->hasFile('background_image')) {
                // delete old image from storage
                if ($product->background_image && file_exists(storage_path('app/public/' . $product->background_image))) {
                    unlink(storage_path('app/public/' . $product->background_image));
                }
                $product->background_image = $this->imageUpload($request->file('background_image'), 'product', true);
            }


            $product->save();

            // Handle digital files
            if ($product->product_type === 'digital') {
                // Delete removed files
                if ($request->filled('delete_digital_files')) {
                    $filesToDelete = ProductFile::whereIn('id', $request->delete_digital_files)
                        ->where('product_id', $product->id)
                        ->get();

                    foreach ($filesToDelete as $file) {
                        if (\Storage::disk('public')->exists($file->file_location)) {
                            \Storage::disk('public')->delete($file->file_location);
                        }
                        $file->delete();
                    }
                }

                // Add new files
                if ($request->filled('digital_files')) {
                    foreach ($request->digital_files as $filePath) {
                        ProductFile::create([
                            'product_id' => $product->id,
                            'file_location' => $filePath
                        ]);
                    }
                }
            }

            if ($product->product_type === 'simple') {
                $variation = ProductVariation::where('product_id', $product->id)->first();
                if ($variation) {
                    $variation->price = $product->price;
                    $variation->sale_price = $product->sale_price;
                    $variation->before_sale_price = $product->sale_price ? $product->price : null;
                    $variation->save();
                }

                WarehouseProduct::where('product_id', $product->id)->update([
                    'price' => $product->sale_price ? $product->sale_price : $product->price,
                    'before_sale_price' => $product->sale_price ? $product->price : null,
                ]);
            }

            if ($request->hasFile('image')) {
                $image = ProductImage::where('product_id', $product->id)->where('featured_image', 1)->first();
                if ($image) {
                    $image->delete();
                }
                $image = new ProductImage();
                $image->product_id = $product->id;
                $image->image = $this->imageUpload($request->file('image'), 'product', true);
                $image->featured_image = 1;
                $image->save();
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $image = new ProductImage();
                    $image->product_id = $product->id;
                    $image->image = $this->imageUpload($file, 'product', true);
                    $image->featured_image = 0;
                    $image->save();
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

            // Soft delete the product
            $product->delete();

            // Soft delete all related records (if they support SoftDeletes)
            // These will be restored automatically when the product is restored
            ProductImage::where('product_id', $product->id)->delete();
            ProductOtherCharge::where('product_id', $product->id)->delete();
            ProductVariation::where('product_id', $product->id)->delete();
            WarehouseProductVariation::where('product_id', $product->id)->delete();

            // Hard delete cart and wishlist items (not needed after deletion)
            EcomWishList::where('product_id', $product->id)->forceDelete();
            EstoreCart::where('product_id', $product->id)->forceDelete();
            Review::where('product_id', $product->id)->delete();

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
        // $image = WarehouseProductImage::findOrFail($request->id);
        // // Delete the file from storage if needed
        // if (file_exists(storage_path('app/public/' . $image->image_path))) {
        //     unlink(storage_path('app/public/' . $image->image_path));
        // }
        // $image->delete();
        // return response()->json(['message' => 'Warehouse product image deleted successfully!']);
    }

    // variations
    public function variations($id)
    {
        $product = Product::findOrFail($id);
        $product_variations = ProductVariation::where('product_id', $product->id)->orderBy('id', 'desc')->get();
        $colors = Color::where('status', 1)->get();
        $productSizes = $product->sizesWithDetails();

        if (auth()->user()->can('Edit Estore Products') || auth()->user()->isWarehouseAdmin()) {
            return view('user.product.variations', compact('product', 'product_variations', 'colors', 'productSizes'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    // generateVariations
    public function generateVariations(Request $request)
    {
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
                }
            }
        }

        return redirect()->route('products.variations', $product->id)->with('message', 'Product variations generated successfully!');
    }

    // deleteVariation
    public function deleteVariation(Request $request)
    {
        $id = $request->id;
        if (auth()->user()->hasNewRole('SUPER ADMIN') || auth()->user()->hasNewRole('ADMINISTRATOR') || auth()->user()->isWarehouseAdmin()) {
            $variation = ProductVariation::findOrFail($id);
            $productId = $variation->product_id;
            $variation->delete();

            // delete from ProductColorImage if no other variation exists with same color for this product
            $otherVariationsWithSameColor = ProductVariation::where('product_id', $productId)
                ->where('color_id', $variation->color_id)
                ->count();
            if ($otherVariationsWithSameColor == 0 && $variation->color_id != null) {
                ProductColorImage::where('product_id', $productId)
                    ->where('color_id', $variation->color_id)
                    ->delete();
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

        $productId = $request->product_id;
        $product = Product::findOrFail($productId);
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
                    $path = $this->imageUpload($file, 'product_variation', true);

                    $productColorImage = new ProductColorImage();
                    $productColorImage->product_id = $productId;
                    $productColorImage->color_id = $targetColorId;
                    $productColorImage->image_path = $path;
                    $productColorImage->save();
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

        $image = ProductColorImage::findOrFail($request->id);
        // Delete the file from storage if needed
        if (file_exists(storage_path('app/public/' . $image->image_path))) {
            unlink(storage_path('app/public/' . $image->image_path));
        }
        $image->delete();

        return response()->json(['message' => 'Product variation image deleted successfully!']);
    }

    // NEW: Bulk delete variations (by selected IDs or by color group)
    public function bulkDeleteVariations(Request $request)
    {
        $this->authorizeBulkVariations();

        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variation_ids' => 'nullable|array',
            'variation_ids.*' => 'integer|exists:product_variations,id',
            'color_id' => 'nullable|integer|exists:colors,id',
        ]);

        $productId = (int) $data['product_id'];

        // Determine target IDs
        if (!empty($data['color_id'])) {
            $colorId = (int) $data['color_id'];
            $targetIds = ProductVariation::where('product_id', $productId)
                ->where('color_id', $colorId)
                ->pluck('id');
            $affectedColors = collect([$colorId]);
        } else {
            $ids = collect($data['variation_ids'] ?? []);
            if ($ids->isEmpty()) {
                return response()->json(['message' => 'No variations selected.'], 200);
            }
            // Filter to ensure IDs belong to the product
            $targetIds = ProductVariation::where('product_id', $productId)
                ->whereIn('id', $ids)
                ->pluck('id');

            // collect colors before deletion
            $affectedColors = ProductVariation::whereIn('id', $targetIds)
                ->pluck('color_id')
                ->unique()
                ->filter(function ($c) {
                    return !is_null($c);
                });
        }

        if ($targetIds->isEmpty()) {
            return response()->json(['message' => 'Nothing to delete.'], 200);
        }

        // Delete dependent WarehouseProduct rows
        WarehouseProduct::whereIn('product_variation_id', $targetIds)->delete();

        // Delete variations
        ProductVariation::whereIn('id', $targetIds)->delete();

        // Clean ProductColorImage if a color has no more variations
        foreach ($affectedColors as $colorId) {
            $remaining = ProductVariation::where('product_id', $productId)
                ->where('color_id', $colorId)
                ->count();
            if ($remaining === 0) {
                ProductColorImage::where('product_id', $productId)
                    ->where('color_id', $colorId)
                    ->delete();
            }
        }

        return response()->json(['message' => 'Variations deleted successfully.']);
    }

    // NEW: Bulk update variations (apply provided fields to selected IDs or all in color group)
    public function bulkUpdateVariations(Request $request)
    {
        $this->authorizeBulkVariations();

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variation_ids' => 'nullable|array',
            'variation_ids.*' => 'integer|exists:product_variations,id',
            'color_id' => 'nullable|integer|exists:colors,id',
            'apply_to' => 'required|in:checked,group',

            'sku' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Resolve targets
        if ($validated['apply_to'] === 'group') {
            if (empty($validated['color_id'])) {
                return response()->json(['message' => 'color_id is required for group apply.'], 422);
            }
            $targets = ProductVariation::where('product_id', $product->id)
                ->where('color_id', $validated['color_id'])
                ->get();
        } else {
            $ids = collect($validated['variation_ids'] ?? []);
            if ($ids->isEmpty()) {
                return response()->json(['message' => 'Select at least one variation.'], 422);
            }
            $targets = ProductVariation::where('product_id', $product->id)
                ->whereIn('id', $ids)
                ->get();
        }

        if ($targets->isEmpty()) {
            return response()->json(['message' => 'No matching variations found.'], 404);
        }

        // Validate sale_price <= price where applicable
        $providedPrice = array_key_exists('price', $validated) ? $validated['price'] : null;
        $providedSale = array_key_exists('sale_price', $validated) ? $validated['sale_price'] : null;
        if ($providedSale !== null) {
            foreach ($targets as $v) {
                $effectivePrice = $providedPrice !== null ? (float)$providedPrice : (float)$v->price;
                if ($validated['sale_price'] > $effectivePrice) {
                    return response()->json(['message' => 'Sale price cannot exceed price.'], 422);
                }
            }
        }

        foreach ($targets as $variation) {
            // Apply fields only if provided (non-null)
            if (array_key_exists('sku', $validated) && $validated['sku'] !== null) {
                $variation->sku = $validated['sku'];
            }

            if ($product->is_free) {
                $variation->price = 0;
                $variation->sale_price = null;
                $variation->before_sale_price = null;
            } else {
                if ($providedPrice !== null) {
                    $variation->price = $validated['price'];
                    // maintain before_sale if sale provided later
                    if ($variation->sale_price !== null) {
                        $variation->before_sale_price = $variation->price;
                    }
                }
                if ($providedSale !== null) {
                    $variation->sale_price = $validated['sale_price'];
                    $variation->before_sale_price = $validated['sale_price'] !== null && $validated['sale_price'] !== '' ? ($providedPrice !== null ? $validated['price'] : $variation->price) : null;
                    if ($variation->sale_price === null || $variation->sale_price === '') {
                        $variation->sale_price = null;
                        $variation->before_sale_price = null;
                    }
                }
            }

            if (array_key_exists('stock_quantity', $validated) && $validated['stock_quantity'] !== null) {
                $variation->stock_quantity = (int) $validated['stock_quantity'];
            }

            $variation->save();

            // Update WarehouseProduct mirrors
            $wpProducts = WarehouseProduct::where('product_variation_id', $variation->id)->get();
            foreach ($wpProducts as $wp) {
                if (array_key_exists('sku', $validated) && $validated['sku'] !== null) {
                    $wp->sku = $variation->sku;
                }

                if ($product->is_free) {
                    $wp->price = 0;
                    $wp->before_sale_price = null;
                } else {
                    if ($providedPrice !== null || $providedSale !== null) {
                        $wp->price = $variation->sale_price !== null ? $variation->sale_price : $variation->price;
                        $wp->before_sale_price = $variation->before_sale_price ?? null;
                    }
                }

                $wp->save();
            }
        }

        return response()->json(['message' => 'Variations updated successfully.']);
    }

    private function authorizeBulkVariations()
    {
        if (!(auth()->user()->hasNewRole('SUPER ADMIN')
            || auth()->user()->hasNewRole('ADMINISTRATOR')
            || auth()->user()->can('Edit Estore Products')
            || auth()->user()->isWarehouseAdmin())) {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    /**
     * Upload digital product file via AJAX
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadDigitalFile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|max:102400|mimes:pdf,zip,mp4,mp3,docx,xlsx,doc,xls,avi,mov,rar,7z',
            ], [
                'file.required' => 'Please select a file to upload.',
                'file.max' => 'File size must not exceed 100MB.',
                'file.mimes' => 'Invalid file type. Allowed types: PDF, ZIP, MP4, MP3, DOCX, XLSX, DOC, XLS, AVI, MOV, RAR, 7Z.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first('file')
                ], 422);
            }

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '_' . uniqid() . '.' . $extension;

                // Store in storage/app/public/digital_products/
                $path = $file->storeAs('digital_products', $filename, 'public');

                return response()->json([
                    'success' => true,
                    'path' => $path,
                    'filename' => $originalName,
                    'message' => 'File uploaded successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file was uploaded'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'File upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
