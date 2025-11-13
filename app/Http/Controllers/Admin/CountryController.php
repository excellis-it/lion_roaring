<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\TranslateLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class CountryController extends Controller
{
    // Display list of countries (paginated) with optional search/sort
    public function index(Request $request)
    {
        // Authorization via permission
        if (!Gate::allows('Manage Countries')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $query = Country::with('languages');
        // simple search by name or code
        if ($search = $request->get('query')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $sortBy = in_array($request->get('sortby'), ['id', 'name', 'code', 'status']) ? $request->get('sortby') : 'id';
        $sortType = $request->get('sorttype') === 'asc' ? 'asc' : 'desc';
        $countries = $query->orderBy($sortBy, $sortType)->paginate(10);

        return view('admin.countries.list', compact('countries'));
    }

    // Countries table partial for AJAX fetch
    public function fetchData(Request $request)
    {
        if (!Gate::allows('Manage Countries')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $query = Country::with('languages');
        if ($search = $request->get('query')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }
        $sortBy = in_array($request->get('sortby'), ['id', 'name', 'code', 'status']) ? $request->get('sortby') : 'id';
        $sortType = $request->get('sorttype') === 'asc' ? 'asc' : 'desc';
        $countries = $query->orderBy($sortBy, $sortType)->paginate(10);

        $view = view('admin.countries.table', compact('countries'))->render();
        return response()->json(['data' => $view]);
    }

    // Show create form
    public function create()
    {
        if (!Gate::allows('Manage Countries')) {
            abort(403, 'You do not have permission to access this page.');
        }
        $languages = TranslateLanguage::orderBy('name', 'asc')->get();
        return view('admin.countries.create', compact('languages'));
    }

    // Store a new country
    public function store(Request $request)
    {
        if (!Gate::allows('Manage Countries')) {
            abort(403, 'You do not have permission to access this page.');
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:countries,name',
            'code' => 'nullable|string|max:10|unique:countries,code',
            'flag_image' => 'nullable|image|mimes:jpeg,png,webp,svg,gif|max:2048',
            'status' => 'nullable|boolean',
            'languages' => 'nullable|array',
            'languages.*' => 'exists:translate_languages,id',
        ]);

        $data = $request->only(['name', 'code']);
        $data['status'] = $request->boolean('status');

        if ($request->hasFile('flag_image')) {
            $path = $request->file('flag_image')->store('countries/flags', 'public');
            $data['flag_image'] = $path; // stored relative to storage/app/public
        }

        $country = Country::create($data);

        // Attach selected languages
        if ($request->has('languages')) {
            $country->languages()->sync($request->input('languages'));
        }

        return redirect()->route('admin-countries.index')->with('message', 'Country created successfully.');
    }

    // Update a country
    public function update(Request $request, $id)
    {
        // return $request->all();
        $country = Country::findOrFail($id);
        if (!Gate::allows('Manage Countries')) {
            abort(403, 'You do not have permission to access this page.');
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:countries,name,' . $country->id,
            'code' => 'nullable|string|max:10|unique:countries,code,' . $country->id,
            'flag_image' => 'nullable|image|mimes:jpeg,png,webp,svg,gif|max:2048',
            'status' => 'nullable|boolean',
            'languages' => 'nullable|array',
            'languages.*' => 'exists:translate_languages,id',
        ]);

        $country->name = $request->name;
        $country->code = $request->code;
        $country->status = $request->boolean('status');

        if ($request->hasFile('flag_image')) {
            // delete old
            if ($country->flag_image && Storage::disk('public')->exists($country->flag_image)) {
                Storage::disk('public')->delete($country->flag_image);
            }
            $path = $request->file('flag_image')->store('countries/flags', 'public');
            $country->flag_image = $path;
        }

        $country->save();

        // Sync selected languages
        if ($request->has('languages')) {
            $country->languages()->sync($request->input('languages'));
        } else {
            // If no languages selected, detach all
            $country->languages()->sync([]);
        }

        return redirect()->route('admin-countries.index')->with('message', 'Country updated successfully.');
    }

    // Show edit form
    public function edit($id)
    {
        if (!Gate::allows('Manage Countries')) {
            abort(403, 'You do not have permission to access this page.');
        }
        $country = Country::findOrFail($id);
        $languages = TranslateLanguage::orderBy('name', 'asc')->get();
        return view('admin.countries.edit', compact('country', 'languages'));
    }

    // Delete a country
    public function destroy(Country $country)
    {
        if (!Gate::allows('Manage Countries')) {
            abort(403, 'You do not have permission to access this page.');
        }
        if ($country->flag_image && Storage::disk('public')->exists($country->flag_image)) {
            Storage::disk('public')->delete($country->flag_image);
        }
        $country->delete();
        return redirect()->route('admin-countries.index')->with('error', 'Country deleted successfully.');
    }

    // Toggle active/inactive
    public function toggleStatus(Country $country)
    {
        if (!Gate::allows('Manage Countries')) {
            abort(403, 'You do not have permission to access this page.');
        }
        $country->status = $country->status ? 0 : 1;
        $country->save();
        return response()->json(['status' => $country->status, 'id' => $country->id]);
    }

    // Optional GET delete to match other modules style
    public function delete($id)
    {
        if (!Gate::allows('Manage Countries')) {
            abort(403, 'You do not have permission to access this page.');
        }
        $country = Country::findOrFail($id);
        return $this->destroy($country);
    }
}
