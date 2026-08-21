<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\SupportReportStatusUpdatedMail;
use App\Models\SupportReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SupportReportManageController extends Controller
{
    private function canManage(): bool
    {
        $user = auth()->user();

        return $user->hasNewRole('SUPER ADMIN')
            || $user->can('Manage Support Reports')
            || $user->can('Edit Support Reports');
    }

    public function index(Request $request)
    {
        if (!$this->canManage()) {
            abort(403, 'You do not have permission to access this page.');
        }

        return redirect()->route('support-reports.index', array_filter([
            'scope' => 'all',
            'status' => $request->status,
        ]));
    }

    public function show(SupportReport $supportReport)
    {
        if (!$this->canManage()) {
            abort(403, 'You do not have permission to access this page.');
        }

        return redirect()->route('support-reports.show', $supportReport);
    }

    public function update(Request $request, SupportReport $supportReport)
    {
        if (!$this->canManage()) {
            abort(403, 'You do not have permission to access this page.');
        }

        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'admin_notes' => 'nullable|string',
        ]);

        $updateData = [
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? null,
            'resolved_by' => auth()->id(),
        ];

        if (in_array($validated['status'], ['resolved', 'closed'], true)) {
            $updateData['resolved_at'] = $supportReport->resolved_at ?? now();
        }

        $supportReport->update($updateData);
        $supportReport->load('user');

        if (!empty($supportReport->user?->email)) {
            try {
                Mail::to($supportReport->user->email)->send(new SupportReportStatusUpdatedMail($supportReport));
            } catch (\Exception $e) {
                Log::error('SupportReport status notify failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('support-reports.show', $supportReport)
            ->with('message', 'Report status updated. The submitter has been notified.');
    }
}
