<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ElearningCategory;
use App\Models\ElearningSubCategory;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ElearningSubCategoryController extends Controller
{
    use ImageTrait;

    public function index()
    {
        if (auth()->user()->can('Manage Elearning Sub Category')) {
            $subcategories = ElearningSubCategory::with('category')->orderBy('id', 'desc')->paginate(10);
            return view('user.elearning-sub-category.list', compact('subcategories'));
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

            $subcategories = ElearningSubCategory::query()->with('category')
                ->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhere('name', 'like', '%' . $query . '%')
                        ->orWhere('slug', 'like', '%' . $query . '%')
                        ->orWhereHas('category', function ($q2) use ($query) {
                            $q2->where('name', 'like', '%' . $query . '%');
                        });
                });

            if ($sort_by && $sort_type) {
                $subcategories = $subcategories->orderBy($sort_by, $sort_type);
            }

            $subcategories = $subcategories->paginate(10);

            return response()->json(['data' => view('user.elearning-sub-category.table', compact('subcategories'))->render()]);
        }
    }

    public function show($id)
    {
        $subcategory = ElearningSubCategory::findOrFail($id);
        return view('user.elearning-sub-category.show', compact('subcategory'));
    }

    public function create()
    {
        if (auth()->user()->can('Create Elearning Sub Category')) {
            $categories = ElearningCategory::where('status', 1)->get();
            return view('user.elearning-sub-category.create', compact('categories'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('Create Elearning Sub Category')) {
            abort(403, 'You do not have permission to access this page.');
        }
        $request->validate([
            'elearning_category_id' => 'required|exists:elearning_categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:elearning_sub_categories,slug',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        $subcategory = new ElearningSubCategory();
        $subcategory->elearning_category_id = $request->elearning_category_id;
        $subcategory->name = $request->name;
        $subcategory->slug = $request->slug;
        $subcategory->meta_title = $request->meta_title;
        $subcategory->meta_description = $request->meta_description;
        $subcategory->status = $request->status;

        if ($request->hasFile('image')) {
            $subcategory->image = $this->imageUpload($request->file('image'), 'elearning-subcategory');
        }

        $subcategory->save();

        return redirect()->route('elearning-sub-categories.index')->with('message', 'Sub Category created successfully.');
    }

    public function edit($id)
    {
        if (auth()->user()->can('Edit Elearning Sub Category')) {
            $subcategory = ElearningSubCategory::findOrFail($id);
            $categories = ElearningCategory::where('status', 1)->get();
            return view('user.elearning-sub-category.edit', compact('subcategory', 'categories'));
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->can('Edit Elearning Sub Category')) {
            $subcategory = ElearningSubCategory::findOrFail($id);

            $request->validate([
                'elearning_category_id' => 'required|exists:elearning_categories,id',
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:elearning_sub_categories,slug,' . $subcategory->id,
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:255',
                'status' => 'required|boolean',
            ]);

            $subcategory->elearning_category_id = $request->elearning_category_id;
            $subcategory->name = $request->name;
            $subcategory->slug = $request->slug;
            $subcategory->meta_title = $request->meta_title;
            $subcategory->meta_description = $request->meta_description;
            $subcategory->status = $request->status;

            if ($request->hasFile('image')) {
                $subcategory->image = $this->imageUpload($request->file('image'), 'elearning-subcategory');
            }

            $subcategory->save();

            return redirect()->route('elearning-sub-categories.index')->with('message', 'Sub Category updated successfully.');
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function delete(Request $request)
    {
        if (!auth()->user()->can('Delete Elearning Sub Category')) {
            abort(403, 'You do not have permission to access this page.');
        }
        $subcategory = ElearningSubCategory::findOrFail($request->id);
        Log::info($subcategory->name . ' deleted by ' . auth()->user()->email . ' deleted at ' . now());
        $subcategory->delete();

        return redirect()->route('elearning-sub-categories.index')->with('message', 'Sub Category deleted successfully.');
    }

    public function getSubcategories(Request $request)
    {
        $subcategories = ElearningSubCategory::where('elearning_category_id', $request->category_id)->where('status', 1)->get();
        return response()->json(['data' => $subcategories]);
    }
}
