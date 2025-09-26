<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    use ImageTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->hasRole('SUPER ADMIN') || auth()->user()->hasRole('ADMINISTRATOR')) {
            $categories = Category::orderBy('id', 'desc')->paginate(10);
            return view('user.category.list', compact('categories'));
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

            $categories = Category::query()
                ->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhere('name', 'like', '%' . $query . '%')
                        ->orWhere('slug', 'like', '%' . $query . '%');
                });

            if ($sort_by && $sort_type) {
                $categories = $categories->orderBy($sort_by, $sort_type);
            }

            $categories = $categories->paginate(10);

            return response()->json(['data' => view('user.category.table', compact('categories'))->render()]);
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
        if (auth()->user()->hasRole('SUPER ADMIN') || auth()->user()->hasRole('ADMINISTRATOR')) {
            return view('user.category.create', compact('categories'));
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
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'slug' => 'required|string|max:255|unique:categories',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->parent_id = $request->parent_id ?? null;
        $category->slug = $request->slug;
        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;
        $category->status = $request->status;

        if ($request->hasFile('image')) {
            $category->image = $this->imageUpload($request->file('image'), 'category');
        }

        if ($request->hasFile('background_image')) {
            $category->background_image = $this->imageUpload($request->file('background_image'), 'category');
        }

        $category->save();

        return redirect()->route('categories.index')->with('message', 'Category created successfully.');
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
        if (auth()->user()->hasRole('SUPER ADMIN') || auth()->user()->hasRole('ADMINISTRATOR')) {
            $category = Category::findOrFail($id);
            return view('user.category.edit', compact('category', 'categories'));
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
        if (auth()->user()->hasRole('SUPER ADMIN') || auth()->user()->hasRole('ADMINISTRATOR')) {
            $category = Category::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable|exists:categories,id',
                'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
                'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:255',
                'status' => 'required|boolean',
            ]);

            $category->name = $request->name;
            $category->parent_id = $request->parent_id ?? null;
            if ($category->main == 0) {
                $category->slug = $request->slug;
            }
            $category->meta_title = $request->meta_title;
            $category->meta_description = $request->meta_description;
            $category->status = $request->status;

            if ($request->hasFile('image')) {
                $category->image = $this->imageUpload($request->file('image'), 'category');
            }

            if ($request->hasFile('background_image')) {
                $category->background_image = $this->imageUpload($request->file('background_image'), 'category');
            }

            $category->save();

            return redirect()->route('categories.index')->with('message', 'Category updated successfully.');
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

    public function delete(Request $request)
    {
        $category = Category::findOrFail($request->id);
        Log::info($category->name . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
        $category->delete();

        return redirect()->route('categories.index')->with('message', 'Category deleted successfully.');
    }
}
