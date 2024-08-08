<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

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
        if (auth()->user()->hasRole('ADMIN')) {
            $products = Product::orderBy('id', 'desc')->paginate(10);
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

            $products = Product::query()
                ->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhere('name', 'like', '%' . $query . '%')
                        // ->orWhere('sku', 'like', '%' . $query . '%')
                        // ->orWhere('price', 'like', '%' . $query . '%')
                        // ->orWhere('quantity', 'like', '%' . $query . '%');
                        ->orWhere('slug', 'like', '%' . $query . '%');
                })->orWhereHas('category', function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%');
                });
            if ($sort_by && $sort_type) {
                $products = $products->orderBy($sort_by, $sort_type);
            }

            $products = $products->paginate(10);

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
        if (auth()->user()->hasRole('ADMIN')) {
            $categories = Category::orderBy('id', 'desc')->get();
            return view('user.product.create')->with('categories', $categories);
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
        $request->validate([
            'category_id' => 'required|numeric|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'required|string',
            // 'sku' => 'required|string|unique:products',
            'specification' => 'required|string',
            // 'price' => 'required|numeric',
            // 'quantity' => 'required|numeric',
            'feature_product' => 'required',
            'slug' => 'required|string|unique:products',
            'affiliate_link' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg'
        ]);


        $product = new Product();
        $product->category_id = $request->category_id;
        $product->user_id = auth()->user()->id;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->short_description = $request->short_description;
        // $product->sku = $request->sku;
        $product->specification = $request->specification;
        // $product->price = $request->price;
        // $product->quantity = $request->quantity;
        $product->slug = $request->slug;
        $product->affiliate_link = $request->affiliate_link;
        $product->feature_product = $request->feature_product;
        $product->meta_title = $request->meta_title;
        $product->meta_description = $request->meta_description;
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


        return redirect()->route('products.index')->with('message', 'Product created successfully!');
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
        if (auth()->user()->hasRole('ADMIN')) {
            $product = Product::findOrFail($id);
            $categories = Category::orderBy('id', 'desc')->get();
            return view('user.product.edit', compact('product', 'categories'));
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
        if (auth()->user()->hasRole('ADMIN')) {
            $request->validate([
                'category_id' => 'required|numeric|exists:categories,id',
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'short_description' => 'required|string',
                // 'sku' => 'required|string|unique:products,sku,' . $id,
                'specification' => 'required|string',
                // 'price' => 'required|numeric',
                // 'quantity' => 'required|numeric',
                'slug' => 'required|string|unique:products,slug,' . $id,
                'affiliate_link' => 'required|string',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
                'feature_product' => 'required',
                'status' => 'required'
            ]);

            $product = Product::findOrFail($id);
            $product->category_id = $request->category_id;
            $product->name = $request->name;
            $product->description = $request->description;
            $product->short_description = $request->short_description;
            // $product->sku = $request->sku;
            $product->specification = $request->specification;
            // $product->price = $request->price;
            // $product->quantity = $request->quantity;
            $product->slug = $request->slug;
            $product->affiliate_link = $request->affiliate_link;
            $product->feature_product = $request->feature_product;
            $product->meta_title = $request->meta_title;
            $product->meta_description = $request->meta_description;
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
        if (auth()->user()->hasRole('ADMIN')) {
            $product = Product::findOrFail($id);
            $product->delete();
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
}
