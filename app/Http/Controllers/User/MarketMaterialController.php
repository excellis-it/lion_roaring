<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\MarketMaterial;
use Illuminate\Http\Request;

class MarketMaterialController extends Controller
{
    public function index()
    {
        $this->authorizeAccess();

        $materials = MarketMaterial::orderBy('sort_order')->orderBy('name')->paginate(20);

        return view('user.market-materials.index', compact('materials'));
    }

    public function create()
    {
        $this->authorizeAccess();

        return view('user.market-materials.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAccess();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:market_materials,code',
            'is_active' => 'nullable|in:0,1',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        MarketMaterial::create([
            'name' => $data['name'],
            'code' => strtoupper($data['code']),
            'is_active' => $request->has('is_active'),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return redirect()->route('market-materials.index')->with('message', 'Material created successfully!');
    }

    public function edit($id)
    {
        $this->authorizeAccess();

        $material = MarketMaterial::findOrFail($id);

        return view('user.market-materials.edit', compact('material'));
    }

    public function update(Request $request, $id)
    {
        $this->authorizeAccess();

        $material = MarketMaterial::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:market_materials,code,' . $material->id,
            'is_active' => 'nullable|in:0,1',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $material->update([
            'name' => $data['name'],
            'code' => strtoupper($data['code']),
            'is_active' => $request->has('is_active'),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return redirect()->route('market-materials.index')->with('message', 'Material updated successfully!');
    }

    public function destroy($id)
    {
        $this->authorizeAccess();

        $material = MarketMaterial::findOrFail($id);
        $material->delete();

        return redirect()->route('market-materials.index')->with('message', 'Material deleted successfully!');
    }

    private function authorizeAccess(): void
    {
        if (!auth()->user()->can('Manage Estore Products')) {
            abort(403, 'You do not have permission to access this page.');
        }
    }
}
