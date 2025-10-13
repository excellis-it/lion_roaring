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
            ->orderBy('id', 'desc')
            ->get();

        return view('user.order-email-templates.list', compact('templates'));
    }

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        if (!Auth::user()->can('Create Email Template')) {
            abort(403, 'You do not have permission to create a template.');
        }

        // Get all active order statuses that don't already have an email template
        $statuses = OrderStatus::where('is_active', 1)
            ->whereDoesntHave('emailTemplate') // relationship we will define
            ->get();

        return view('user.order-email-templates.create', compact('statuses'));
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
            'order_status_id' => 'nullable|exists:order_statuses,id|unique:order_email_templates,order_status_id',
            'subject' => 'required|string|max:200',
            'body'    => 'required|string',
        ]);

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

        // Get all active order statuses
        $allStatuses = OrderStatus::where('is_active', 1)->get();

        // Get order_status_ids already used by other templates
        $usedStatusIds = OrderEmailTemplate::where('id', '!=', $id)
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
                // ensure uniqueness except for this template
                \Illuminate\Validation\Rule::unique('order_email_templates', 'order_status_id')->ignore($template->id),
            ],
            'subject' => 'required|string|max:200',
            'body'    => 'required|string',
        ]);



        $template->update([
            'title' => $request->title,
            'order_status_id' => $request->order_status_id,
            'subject' => $request->subject,
            'body' => $request->body,
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
}
