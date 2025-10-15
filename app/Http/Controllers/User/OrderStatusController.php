<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderStatusController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('Manage Order Status')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $statuses = OrderStatus::orderBy('sort_order')->get();
        return view('user.order-status.list', compact('statuses'));
    }

    public function create()
    {
        if (!Auth::user()->can('Create Order Status')) {
            abort(403, 'You do not have permission to access this page.');
        }

        return view('user.order-status.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('Create Order Status')) {
            abort(403, 'You do not have permission to perform this action.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            // 'slug' => [
            //     'required',
            //     'string',
            //     'max:100',
            //     'unique:order_statuses,slug',
            //     'regex:/^[a-z0-9-]+$/', // Only lowercase letters, numbers, hyphens
            // ],
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ],
        // [
        //     'slug.required' => 'The status slug is required.',
        //     'slug.unique' => 'This slug is already taken. Please choose a different one.',
        //     'slug.regex' => 'The slug can only contain lowercase letters, numbers, and hyphens (no spaces).',
        //     'slug.max' => 'The slug cannot exceed 100 characters.',
        // ]
    );

    // slug is auto-generated from name and made unique
        $baseSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));
        $slug = $baseSlug;
        $counter = 1;
        while (OrderStatus::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $request->merge(['slug' => $slug]);


        OrderStatus::create($request->only(['name', 'slug', 'sort_order', 'is_active']));

        return redirect()->route('order-status.index')->with('message', 'Order status created successfully.');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('Edit Order Status')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $status = OrderStatus::findOrFail($id);
        return view('user.order-status.edit', compact('status'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('Edit Order Status')) {
            abort(403, 'You do not have permission to perform this action.');
        }

        $status = OrderStatus::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $status->update($request->only(['name', 'sort_order', 'is_active']));

        return redirect()->route('order-status.index')->with('message', 'Order status updated successfully.');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('Delete Order Status')) {
            abort(403, 'You do not have permission to perform this action.');
        }

        $status = OrderStatus::findOrFail($id);

        // Prevent deletion of important statuses
        if (in_array($status->slug, ['pending', 'delivered', 'cancelled'])) {
            return redirect()->route('order-status.index')
                ->with('error', 'This status cannot be deleted.');
        }

        $status->delete();

        return redirect()->route('order-status.index')->with('message', 'Order status deleted successfully.');
    }
}
