<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderEmailTemplate;
use App\Models\OrderStatus;
use Illuminate\Support\Facades\Auth;

class OrderEmailTemplateController extends Controller
{
    /**
     * Display a listing of the templates.
     */
    public function index()
    {
        if (!Auth::user()->can('Manage Email Template')) {
            abort(403, 'You do not have permission to access this page.');
        }

        $templates = OrderEmailTemplate::with('orderStatus')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Split templates into general (delivery), pickup variants, and digital
        $digitalTemplate = $templates->where('slug', 'digital')->first();

        $generalTemplates = $templates->where('is_pickup', false)
            ->where('slug', '!=', 'digital')
            ->values();

        $pickupTemplates = $templates->where('is_pickup', true)
            ->where('slug', '!=', 'digital')
            ->values();

        return view('user.order-email-templates.list', compact('generalTemplates', 'pickupTemplates', 'digitalTemplate'));
    }

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        if (!Auth::user()->can('Create Email Template')) {
            abort(403, 'You do not have permission to create a template.');
        }

        // honor ?type=pickup to open create in pickup-mode
        $isPickupParam = request('type') === 'pickup';

        // Get active order statuses scoped by template type
        $statuses = OrderStatus::where('is_active', 1)
            ->where('is_pickup', $isPickupParam ? 1 : 0)
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('user.order-email-templates.create', compact('statuses', 'isPickupParam'));
    }


    /**
     * Store a newly created template in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('Create Email Template')) {
            abort(403, 'You do not have permission to create a template.');
        }

        $request->validate([
            'title' => 'required|string|max:150',
            'order_status_id' => 'nullable|exists:order_statuses,id',
            'subject' => 'required|string|max:200',
            'body'    => 'required|string',
            'is_pickup' => 'nullable|boolean',
        ]);

        // accept either hidden input or ?type=pickup as a fallback
        $isPickup = $request->boolean('is_pickup') || request('type') === 'pickup';

        if ($request->order_status_id) {
            $status = OrderStatus::find($request->order_status_id);
            if (!$status || (bool)$status->is_pickup !== $isPickup) {
                return back()->withInput()->withErrors(['order_status_id' => 'Selected status does not match the template type.']);
            }
        }
        // ensure there isn't already a template for this (status, is_pickup) pair
        if ($request->order_status_id && OrderEmailTemplate::where('order_status_id', $request->order_status_id)->where('is_pickup', $isPickup)->exists()) {
            return back()->withInput()->withErrors(['order_status_id' => 'A template for this order status and template type already exists.']);
        }

        // Generate unique slug from title
        $baseSlug = \Str::slug($request->title);
        $slug = $baseSlug;
        $counter = 1;

        while (\App\Models\OrderEmailTemplate::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        OrderEmailTemplate::create([
            'title' => $request->title,
            'slug' => $slug,
            'order_status_id' => $request->order_status_id,
            'is_pickup' => $isPickup,
            'subject' => $request->subject,
            'body' => $request->body,
        ]);

        return redirect()->route('order-email-templates.index')
            ->with('message', 'Email template created successfully.');
    }



    /**
     * Show the form for editing the specified template.
     */
    public function edit($id)
    {
        if (!Auth::user()->can('Edit Email Template')) {
            abort(403, 'You do not have permission to edit this template.');
        }

        $template = OrderEmailTemplate::findOrFail($id);

        // Get active order statuses scoped by template type
        $allStatuses = OrderStatus::where('is_active', 1)
            ->where('is_pickup', $template->is_pickup ? 1 : 0)
            ->orderBy('sort_order', 'asc')
            ->get();

        // Get order_status_ids already used by other templates of the same template type (pickup/delivery)
        $usedStatusIds = OrderEmailTemplate::where('id', '!=', $id)
            ->where('is_pickup', $template->is_pickup)
            ->pluck('order_status_id')
            ->filter(); // remove nulls

        // Pass to view
        return view('user.order-email-templates.edit', [
            'template' => $template,
            'statuses' => $allStatuses,
            'usedStatusIds' => $usedStatusIds,
        ]);
    }


    /**
     * Update the specified template in storage.
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('Edit Email Template')) {
            abort(403, 'You do not have permission to update this template.');
        }

        $template = OrderEmailTemplate::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:150',
            'order_status_id' => [
                'nullable',
                'exists:order_statuses,id',
            ],
            'subject' => 'required|string|max:200',
            'body'    => 'required|string',
            'is_pickup' => 'nullable|boolean',
        ]);

        // accept either hidden input or ?type=pickup fallback
        $isPickup = $request->boolean('is_pickup') || request('type') === 'pickup';

        if ($request->order_status_id) {
            $status = OrderStatus::find($request->order_status_id);
            if (!$status || (bool)$status->is_pickup !== $isPickup) {
                return back()->withInput()->withErrors(['order_status_id' => 'Selected status does not match the template type.']);
            }
        }

        // ensure uniqueness for (order_status_id, is_pickup) excluding this template
        if (
            $request->order_status_id && OrderEmailTemplate::where('order_status_id', $request->order_status_id)
            ->where('is_pickup', $isPickup)
            ->where('id', '!=', $template->id)
            ->exists()
        ) {
            return back()->withInput()->withErrors(['order_status_id' => 'A template for this order status and template type already exists.']);
        }

        $template->update([
            'title' => $request->title,
            'order_status_id' => $request->order_status_id,
            'is_pickup' => $isPickup,
            'subject' => $request->subject,
            'body' => $request->body,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('order-email-templates.index')
            ->with('message', 'Email template updated successfully.');
    }


    /**
     * Remove the specified template from storage.
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('Delete Email Template')) {
            abort(403, 'You do not have permission to delete this template.');
        }

        $template = OrderEmailTemplate::findOrFail($id);
        $template->delete();

        return redirect()->route('order-email-templates.index')
            ->with('message', 'Email template deleted successfully.');
    }

    /**
     * Update the order of templates via AJAX.
     */
    public function updateOrder(Request $request)
    {
        if (!Auth::user()->can('Edit Email Template')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:order_email_templates,id',
        ]);

        foreach ($request->order as $index => $id) {
            OrderEmailTemplate::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true, 'message' => 'Order updated successfully']);
    }
}
