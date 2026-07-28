<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SupportReportSubmittedMail;
use App\Models\SupportReport;
use App\Models\User;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SupportReportController extends Controller
{
    use ImageTrait;

    private function serialize(SupportReport $report): array
    {
        $path = $report->attachment;

        return [
            'id' => $report->id,
            'subject' => $report->subject,
            'message' => $report->message,
            'status' => $report->status,
            'attachment_path' => $path,
            'attachment' => $path ? url(Storage::url($path)) : null,
            'admin_notes' => $report->admin_notes,
            'resolved_at' => optional($report->resolved_at)?->toIso8601String(),
            'created_at' => optional($report->created_at)?->toIso8601String(),
        ];
    }

    public function index(Request $request)
    {
        $allowed = ['open', 'in_progress', 'resolved', 'closed'];
        $statusFilter = in_array($request->status, $allowed, true) ? $request->status : null;

        $query = SupportReport::forUser(auth()->id())->orderBy('id', 'desc');
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $paginator = $query->paginate(15);
        $paginator->getCollection()->transform(fn (SupportReport $r) => $this->serialize($r));

        return response()->json([
            'status' => true,
            'message' => 'OK',
            'data' => $paginator,
        ]);
    }

    public function store(Request $request)
    {
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
        $this->notifyManagers($report);

        return response()->json([
            'status' => true,
            'message' => 'Your support report has been submitted successfully.',
            'data' => $this->serialize($report),
        ], 201);
    }

    public function show(SupportReport $supportReport)
    {
        if ((int) $supportReport->user_id !== (int) auth()->id()) {
            return response()->json(['status' => false, 'message' => 'Forbidden'], 403);
        }

        return response()->json([
            'status' => true,
            'message' => 'OK',
            'data' => $this->serialize($supportReport),
        ]);
    }

    private function notifyManagers(SupportReport $report): void
    {
        $managers = User::permission('Manage Support Reports')->where('status', 1)->get();
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
                    Log::error('SupportReport API notify failed: ' . $e->getMessage());
                }
            });
    }
}
