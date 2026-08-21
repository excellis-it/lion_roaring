<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\SupportReportSubmittedMail;
use App\Models\SupportReport;
use App\Models\User;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SupportReportController extends Controller
{
    use ImageTrait;

    private function canManage(): bool
    {
        $user = auth()->user();

        return $user->hasNewRole('SUPER ADMIN') || $user->can('Manage Support Reports');
    }

    private function canView(): bool
    {
        return $this->canManage() || auth()->user()->can('View Support Reports');
    }

    private function canCreate(): bool
    {
        return $this->canManage() || auth()->user()->can('Create Support Reports');
    }

    private function canDelete(): bool
    {
        return $this->canManage() || auth()->user()->can('Delete Support Reports');
    }

    public function index(Request $request)
    {
        if (!$this->canView()) {
            abort(403, 'You do not have permission to access this page.');
        }

        $canManage = $this->canManage();
        $scope = $canManage && $request->get('scope') === 'mine' ? 'mine' : ($canManage ? 'all' : 'mine');

        $allowed = ['open', 'in_progress', 'resolved', 'closed'];
        $statusFilter = in_array($request->status, $allowed, true) ? $request->status : null;

        if ($canManage && $scope === 'all') {
            $query = SupportReport::with('user')->orderBy('id', 'desc');
            if ($statusFilter) {
                $query->where('status', $statusFilter);
            }
            $reports = $query->paginate(15)->withQueryString();
        } else {
            $query = SupportReport::forUser(auth()->id())->orderBy('id', 'desc');
            if ($statusFilter) {
                $query->where('status', $statusFilter);
            }
            $reports = $query->paginate(15)->withQueryString();
        }

        $canCreate = $this->canCreate();
        $canDelete = $this->canDelete();

        return view('user.support-reports.index', compact(
            'reports',
            'canManage',
            'canCreate',
            'canDelete',
            'scope',
            'statusFilter'
        ));
    }

    public function create()
    {
        if (!$this->canCreate()) {
            abort(403, 'You do not have permission to submit a support report.');
        }

        return view('user.support-reports.create');
    }

    public function store(Request $request)
    {
        if (!$this->canCreate()) {
            abort(403, 'You do not have permission to submit a support report.');
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $ext = strtolower($file->getClientOriginalExtension());
            $allowedImages = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($ext, $allowedImages, true)) {
                $attachmentPath = $this->imageUpload($file, 'support-reports/attachments');
            } else {
                $filename = date('YmdHis') . '_' . uniqid() . '.' . $ext;
                $attachmentPath = $file->storeAs('support-reports/attachments', $filename, 'public');
            }
        }

        $report = SupportReport::create([
            'user_id' => auth()->id(),
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'attachment' => $attachmentPath,
            'status' => 'open',
        ]);

        $report->load('user');

        $managers = User::permission('Manage Support Reports')
            ->where('status', 1)
            ->get();

        $superAdmins = User::where('status', 1)
            ->whereHas('userRole', function ($q) {
                $q->where('name', 'SUPER ADMIN');
            })
            ->get();

        $managers->merge($superAdmins)
            ->unique('id')
            ->each(function (User $manager) use ($report) {
                if (empty($manager->email)) {
                    return;
                }

                try {
                    Mail::to($manager->email)->send(new SupportReportSubmittedMail($report));
                } catch (\Exception $e) {
                    Log::error('SupportReport notify failed: ' . $e->getMessage());
                }
            });

        return redirect()->route('support-reports.index')
            ->with('message', 'Your support report has been submitted successfully.');
    }

    public function show(SupportReport $supportReport)
    {
        $canManage = $this->canManage();

        if ($supportReport->user_id !== auth()->id() && !$canManage) {
            abort(403, 'You do not have permission to view this report.');
        }

        $supportReport->load('user', 'resolver');

        return view('user.support-reports.show', [
            'report' => $supportReport,
            'canManage' => $canManage,
            'canDelete' => $this->canDelete() && ($supportReport->user_id === auth()->id() || $canManage),
        ]);
    }

    public function destroy(SupportReport $supportReport)
    {
        $canManage = $this->canManage();

        if (!$this->canDelete() || ($supportReport->user_id !== auth()->id() && !$canManage)) {
            abort(403, 'You do not have permission to delete this report.');
        }

        $supportReport->delete();

        return redirect()->route('support-reports.index')->with('message', 'Support report deleted.');
    }
}
