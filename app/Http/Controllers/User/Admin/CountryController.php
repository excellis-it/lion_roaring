<?php

namespace App\Http\Controllers\User\Admin;

use App\Helpers\Helper;
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

        $sortBy = in_array($request->get('sortby'), ['id', 'name', 'code', 'status']) ? $request->get('sortby') : 'status';
        $sortType = $request->get('sorttype') === 'asc' ? 'asc' : 'desc';
        $countries = $query->orderBy($sortBy, $sortType)->paginate(20)->appends($request->only(['sortby', 'sorttype', 'query']));

        return view('user.admin.countries.list', compact('countries'));
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
        $sortBy = in_array($request->get('sortby'), ['id', 'name', 'code', 'status']) ? $request->get('sortby') : 'status';
        $sortType = $request->get('sorttype') === 'asc' ? 'asc' : 'desc';
        $countries = $query->orderBy($sortBy, $sortType)->paginate(20)->appends($request->only(['sortby', 'sorttype', 'query']));

        $view = view('user.admin.countries.table', compact('countries'))->render();
        return response()->json(['data' => $view]);
    }

    // Show create form
    public function create()
    {
        if (!Gate::allows('Manage Countries')) {
            abort(403, 'You do not have permission to access this page.');
        }
        $languages = TranslateLanguage::orderBy('name', 'asc')->get();
        return view('user.admin.countries.create', compact('languages'));
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
            'domain' => 'nullable|url|max:255',
            'status' => 'nullable|boolean',
            'languages' => 'nullable|array',
            'languages.*' => 'exists:translate_languages,id',
        ]);

        $data = $request->only(['name', 'code', 'domain']);
        $data['status'] = $request->boolean('status');

        if ($request->hasFile('flag_image')) {
            $path = $request->file('flag_image')->store('countries/flags', 'public');
            $data['flag_image'] = $path;
        }

        $country = Country::create($data);

        // Attach selected languages
        if ($request->has('languages')) {
            $country->languages()->sync($request->input('languages'));
        }

        // Refresh the session so language changes are immediately visible across all pages
        Helper::refreshCountryLanguagesSession();

        return redirect()->route('user.admin.admin-countries.index')->with('message', 'Country created successfully.');
    }

    // Update a country
    public function update(Request $request, $id)
    {
        $country = Country::findOrFail($id);
        if (!Gate::allows('Manage Countries')) {
            abort(403, 'You do not have permission to access this page.');
        }

        // Prevent editing the GLOBAL entry
        // if ($country->is_global) {
        //     return redirect()->route('user.admin.admin-countries.index')
        //         ->with('error', 'The Global (Main) country cannot be modified.');
        // }

        $request->validate([
            'name' => 'required|string|max:255|unique:countries,name,' . $country->id,
            'code' => 'nullable|string|max:10|unique:countries,code,' . $country->id,
            'flag_image' => 'nullable|image|mimes:jpeg,png,webp,svg,gif|max:2048',
            'domain' => 'nullable|url|max:255',
            'status' => 'nullable|boolean',
            'languages' => 'nullable|array',
            'languages.*' => 'exists:translate_languages,id',
        ]);

        $country->name = $request->name;
        $country->code = $request->code;
        $country->domain = $request->domain;
        $country->status = $request->boolean('status');

        if ($request->hasFile('flag_image')) {
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
            $country->languages()->sync([]);
        }

        // Refresh the session so language changes are immediately visible across all pages
        Helper::refreshCountryLanguagesSession();

        return redirect()->route('user.admin.admin-countries.index')->with('message', 'Country updated successfully.');
    }

    // Show edit form
    public function edit($id)
    {
        if (!Gate::allows('Manage Countries')) {
            abort(403, 'You do not have permission to access this page.');
        }
        $country = Country::findOrFail($id);

        // Prevent editing the GLOBAL entry
        // if ($country->is_global) {
        //     return redirect()->route('user.admin.admin-countries.index')
        //         ->with('error', 'The Global (Main) country cannot be edited.');
        // }

        $languages = TranslateLanguage::orderBy('name', 'asc')->get();
        return view('user.admin.countries.edit', compact('country', 'languages'));
    }

    // Delete a country
    public function destroy(Country $country)
    {
        if (!Gate::allows('Manage Countries')) {
            abort(403, 'You do not have permission to access this page.');
        }

        // Prevent deleting the GLOBAL entry
        if ($country->is_global) {
            return redirect()->route('user.admin.admin-countries.index')
                ->with('error', 'The Global (Main) country cannot be deleted.');
        }

        if ($country->flag_image && Storage::disk('public')->exists($country->flag_image)) {
            Storage::disk('public')->delete($country->flag_image);
        }
        $country->delete();
        return redirect()->route('user.admin.admin-countries.index')->with('error', 'Country deleted successfully.');
    }

    // Toggle active/inactive
    public function toggleStatus(Country $country)
    {
        if (!Gate::allows('Manage Countries')) {
            abort(403, 'You do not have permission to access this page.');
        }

        // Prevent toggling the GLOBAL entry
        if ($country->is_global) {
            return response()->json(['error' => 'Cannot toggle Global country status.'], 403);
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
