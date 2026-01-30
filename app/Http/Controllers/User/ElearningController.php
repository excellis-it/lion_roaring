<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ElearningCategory;
use App\Models\ElearningProduct;
use App\Models\ElearningTopic;
use App\Models\ElearningProductImage;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class ElearningController extends Controller
{
    use ImageTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->can('Manage Elearning Product')) {
            $products = ElearningProduct::orderBy('id', 'desc')->paginate(10);
            $categories = ElearningCategory::orderBy('name')->get();
            return view('user.elearning-product.list', compact('products', 'categories'));
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

            $products = ElearningProduct::query();

            if (!empty($query)) {
                $products = $products->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhere('name', 'like', '%' . $query . '%')
                        // ->orWhere('sku', 'like', '%' . $query . '%')
                        // ->orWhere('price', 'like', '%' . $query . '%')
                        // ->orWhere('quantity', 'like', '%' . $query . '%');
                        ->orWhere('slug', 'like', '%' . $query . '%');
                })->orWhereHas('category', function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%');
                })->orWhereHas('elearningTopic', function ($q) use ($query) {
                    $q->where('topic_name', 'like', '%' . $query . '%');
                });
            }

            if (!empty($categoryId)) {
                $products = $products->where('category_id', $categoryId);
            }
            if ($sort_by && $sort_type) {
                $products = $products->orderBy($sort_by, $sort_type);
            }

            $products = $products->paginate(10);

            return response()->json(['data' => view('user.elearning-product.table', compact('products'))->render()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (auth()->user()->can('Create Elearning Product')) {
            $categories = ElearningCategory::orderBy('id', 'desc')->get();
            $topics = ElearningTopic::orderBy('id', 'desc')->get();
            return view('user.elearning-product.create')->with(compact('categories', 'topics'));
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
            'category_id' => 'required|numeric|exists:elearning_categories,id',
            'name' => 'required|string|max:255',
            // 'description' => 'required|string',
            'short_description' => 'required|string',
            // 'sku' => 'required|string|unique:products',
            // 'specification' => 'required|string',
            // 'price' => 'required|numeric',
            // 'quantity' => 'required|numeric',
            'feature_product' => 'required',
            'slug' => 'required|string|unique:elearning_products',
            'affiliate_link' => 'required|string',
            'elearning_topic_id' => 'nullable|numeric|exists:elearning_topics,id',
            // 'meta_title' => 'nullable|string|max:255',
            // 'meta_description' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp',
            'button_name' => 'required|string',
        ]);


        $product = new ElearningProduct();
        $product->category_id = $request->category_id;
        $product->elearning_topic_id = $request->elearning_topic_id;
        $product->user_id = auth()->user()->id;
        $product->name = $request->name;
        // $product->description = $request->description;
        $product->short_description = $request->short_description;
        // $product->sku = $request->sku;
        // $product->specification = $request->specification;
        // $product->price = $request->price;
        // $product->quantity = $request->quantity;
        $product->button_name = $request->button_name;
        $product->slug = $request->slug;
        $product->affiliate_link = $request->affiliate_link;
        $product->feature_product = $request->feature_product;
        // $product->meta_title = $request->meta_title;
        // $product->meta_description = $request->meta_description;
        $product->save();

        if ($request->hasFile('image')) {
            $image = new ElearningProductImage();
            $image->product_id = $product->id;
            $image->image = $this->imageUpload($request->file('image'), 'product');
            $image->featured_image = 1;
            $image->save();
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $image = new ElearningProductImage();
                $image->product_id = $product->id;
                $image->image = $this->imageUpload($file, 'product');
                $image->featured_image = 0;
                $image->save();
            }
        }

        // notify users
        $userName = Auth::user()->getFullNameAttribute();
        $noti = NotificationService::notifyAllUsers('New Product created by ' . $userName, 'product');


        return redirect()->route('elearning.index')->with('message', 'Product created successfully!');
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
        if (auth()->user()->can('Edit Elearning Product')) {
            $product = ElearningProduct::findOrFail($id);
            $categories = ElearningCategory::orderBy('id', 'desc')->get();
            $topics = ElearningTopic::orderBy('id', 'desc')->get();
            return view('user.elearning-product.edit', compact('product', 'categories', 'topics'));
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
        if (auth()->user()->can('Edit Elearning Product')) {
            $request->validate([
                'category_id' => 'required|numeric|exists:elearning_categories,id',
                'name' => 'required|string|max:255',
                // 'description' => 'required|string',
                'short_description' => 'required|string',
                // 'sku' => 'required|string|unique:products,sku,' . $id,
                // 'specification' => 'required|string',
                // 'price' => 'required|numeric',
                // 'quantity' => 'required|numeric',
                'slug' => 'required|string|unique:elearning_products,slug,' . $id,
                'affiliate_link' => 'required|string',
                'elearning_topic_id' => 'nullable|numeric|exists:elearning_topics,id',
                // 'meta_title' => 'nullable|string|max:255',
                // 'meta_description' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp',
                'feature_product' => 'required',
                'status' => 'required',
                'button_name' => 'required|string',
            ]);

            $product = ElearningProduct::findOrFail($id);
            $product->category_id = $request->category_id;
            $product->elearning_topic_id = $request->elearning_topic_id;
            $product->name = $request->name;
            // $product->description = $request->description;
            $product->short_description = $request->short_description;
            // $product->sku = $request->sku;
            // $product->specification = $request->specification;
            // $product->price = $request->price;
            // $product->quantity = $request->quantity;
            $product->slug = $request->slug;
            $product->affiliate_link = $request->affiliate_link;
            $product->feature_product = $request->feature_product;
            $product->button_name = $request->button_name;
            // $product->meta_title = $request->meta_title;
            // $product->meta_description = $request->meta_description;
            $product->status = $request->status;
            $product->save();

            if ($request->hasFile('image')) {
                $image = ElearningProductImage::where('product_id', $product->id)->where('featured_image', 1)->first();
                if ($image) {
                    $image->delete();
                }
                $image = new ElearningProductImage();
                $image->product_id = $product->id;
                $image->image = $this->imageUpload($request->file('image'), 'product');
                $image->featured_image = 1;
                $image->save();
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $image = new ElearningProductImage();
                    $image->product_id = $product->id;
                    $image->image = $this->imageUpload($file, 'product');
                    $image->featured_image = 0;
                    $image->save();
                }
            }

            return redirect()->route('elearning.index')->with('message', 'Product updated successfully!');
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
        if (auth()->user()->can('Delete Elearning Product')) {
            $product = ElearningProduct::findOrFail($id);
            $product->delete();
            return redirect()->route('elearning.index')->with('message', 'Product deleted successfully!');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function imageDelete(Request $request)
    {
        $image = ElearningProductImage::findOrFail($request->id);
        $image->delete();
        return response()->json(['message' => 'Image deleted successfully!']);
    }
}
