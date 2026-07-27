@extends('user.layouts.master')
@section('title')
    Support Report #{{ $report->id }} - {{ env('APP_NAME') }}
@endsection
@section('content')
<style>
    .sr-badge {
        display: inline-flex; align-items: center; padding: 0.4rem 0.85rem;
        border-radius: 999px; font-size: 0.8rem; font-weight: 700;
        letter-spacing: 0.02em; line-height: 1.2; color: inherit !important;
    }
    .sr-badge-open { background: #fde8e8; color: #b42318 !important; }
    .sr-badge-in-progress { background: #fff4ce; color: #8a6d00 !important; }
    .sr-badge-resolved { background: #e3f6e8; color: #0f7a35 !important; }
    .sr-badge-closed { background: #eef0f2; color: #4b5563 !important; }

    .sr-detail-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .sr-detail-header h3 { margin: 0; font-weight: 700; letter-spacing: -0.02em; }
    .sr-detail-sub { margin: 0.4rem 0 0; color: #6b7280; font-size: 0.9rem; }

    .sr-layout { display: grid; gap: 1.15rem; }
    @media (min-width: 992px) {
        .sr-layout.has-manage { grid-template-columns: 1.4fr 1fr; align-items: start; }
    }

    .sr-panel {
        border: 1px solid #ece8f4; border-radius: 14px; padding: 1.25rem 1.35rem;
        background: #fff;
    }
    .sr-panel-title {
        margin: 0 0 1rem; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.06em; color: #6b7280;
    }
    .sr-subject { margin: 0 0 0.85rem; font-size: 1.25rem; font-weight: 700; color: #1f2937; line-height: 1.35; }
    .sr-message {
        color: #374151; line-height: 1.7; white-space: pre-wrap;
        padding: 1rem 1.1rem; background: #faf8fd; border: 1px solid #ebe3f4; border-radius: 12px;
    }

    .sr-meta-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 0.85rem; margin-bottom: 1.15rem;
    }
    .sr-meta-item {
        background: #faf8fd; border: 1px solid #ebe3f4; border-radius: 10px; padding: 0.75rem 0.9rem;
    }
    .sr-meta-item .label { display: block; font-size: 0.75rem; font-weight: 600; color: #9ca3af; margin-bottom: 0.25rem; }
    .sr-meta-item .value { font-size: 0.95rem; font-weight: 600; color: #1f2937; word-break: break-word; }

    .sr-response {
        border: 1px solid #b7e4c7; background: #f4fbf7; border-radius: 12px;
        padding: 1rem 1.15rem; margin-top: 1.15rem;
    }
    .sr-response h6 { font-weight: 700; margin-bottom: 0.5rem; color: #0f7a35; }

    .sr-manage {
        border: 1px solid #d5c8eb; background: linear-gradient(160deg, #faf8fd 0%, #f3eefb 100%);
        border-radius: 14px; padding: 1.25rem 1.35rem; position: sticky; top: 1rem;
    }
    .sr-manage h5 { font-weight: 700; color: #3b2a5a; margin: 0 0 0.35rem; }
    .sr-manage .hint { color: #6b7280; font-size: 0.85rem; margin-bottom: 1rem; }
    .sr-status-pills { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem; }
    .sr-status-pills label { margin: 0; cursor: pointer; }
    .sr-status-pills input { position: absolute; opacity: 0; pointer-events: none; }
    .sr-status-pills span {
        display: inline-block; border: 1px solid #d1d5db; border-radius: 999px;
        padding: 0.4rem 0.9rem; font-size: 0.85rem; font-weight: 600; color: #4b5563; background: #fff;
    }
    .sr-status-pills input:checked + span {
        background: #6f42c1; color: #fff; border-color: #6f42c1;
    }
</style>
<div class="container-fluid">
    <div class="bg_white_border">
        <div class="sr-detail-header">
            <div>
                <h3>Support Report #{{ $report->id }}</h3>
                <p class="sr-detail-sub">
                    Full report details
                    @if ($canManage)
                        · manage status and reply below
                    @endif
                </p>
            </div>
            <div>
                <a href="{{ route('support-reports.index', $canManage ? ['scope' => 'all'] : []) }}" class="btn btn-primary">
                    &larr; Back to Reports
                </a>
            </div>
        </div>

        @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        <div class="sr-layout {{ $canManage ? 'has-manage' : '' }}">
            <div>
                <div class="sr-panel">
                    <div class="sr-panel-title">Report Details</div>

                    <div class="sr-meta-grid">
                        <div class="sr-meta-item">
                            <span class="label">Status</span>
                            <span class="{{ $report->statusBadgeClass() }}">{{ $report->statusLabel() }}</span>
                        </div>
                        <div class="sr-meta-item">
                            <span class="label">Report ID</span>
                            <span class="value">#{{ $report->id }}</span>
                        </div>
                        <div class="sr-meta-item">
                            <span class="label">Submitted</span>
                            <span class="value">{{ $report->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        @if ($canManage)
                        <div class="sr-meta-item">
                            <span class="label">Submitted By</span>
                            <span class="value">{{ $report->user->full_name ?? '-' }}</span>
                        </div>
                        <div class="sr-meta-item">
                            <span class="label">Email</span>
                            <span class="value">{{ $report->user->email ?? '-' }}</span>
                        </div>
                        @endif
                        <div class="sr-meta-item">
                            <span class="label">Attachment</span>
                            <span class="value">
                                @if($report->attachment)
                                    Yes
                                @else
                                    None
                                @endif
                            </span>
                        </div>
                        @if($report->resolved_at)
                        <div class="sr-meta-item">
                            <span class="label">Resolved / Closed</span>
                            <span class="value">{{ $report->resolved_at->format('d M Y, H:i') }}</span>
                        </div>
                        @endif
                        @if($canManage && $report->resolver)
                        <div class="sr-meta-item">
                            <span class="label">Last Handled By</span>
                            <span class="value">{{ $report->resolver->full_name ?? $report->resolver->email }}</span>
                        </div>
                        @endif
                    </div>

                    <h4 class="sr-subject">{{ $report->subject }}</h4>
                    <div class="sr-message">{{ $report->message }}</div>

                    @if($report->attachment)
                        <a href="{{ Storage::url($report->attachment) }}" target="_blank" class="btn btn-primary btn-sm mt-3">
                            <i class="fa-solid fa-paperclip"></i> View Attachment
                        </a>
                    @endif

                    @if($report->admin_notes && !$canManage)
                    <div class="sr-response">
                        <h6>Response from Support Team</h6>
                        <p class="mb-1" style="white-space: pre-wrap;">{{ $report->admin_notes }}</p>
                        @if($report->resolved_at)
                            <small class="text-muted">Updated {{ $report->resolved_at->format('d M Y, H:i') }}</small>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            @if ($canManage)
            <div>
                <div class="sr-manage">
                    <h5>Manage Report</h5>
                    <p class="hint">Update status and add a reply. The member is notified by email when you save.</p>

                    @if($report->admin_notes)
                    <div class="sr-response mb-3">
                        <h6>Current Admin Notes</h6>
                        <p class="mb-0" style="white-space: pre-wrap;">{{ $report->admin_notes }}</p>
                    </div>
                    @endif

                    <form action="{{ route('support-reports.manage.update', $report) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <label class="form-label fw-semibold">Status</label>
                        <div class="sr-status-pills">
                            @foreach(['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $value => $label)
                                <label>
                                    <input type="radio" name="status" value="{{ $value }}"
                                        @checked(old('status', $report->status) === $value) required>
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('status')<div class="text-danger small mb-2">{{ $message }}</div>@enderror

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Admin Notes <span class="text-muted fw-normal">(visible to submitter)</span></label>
                            <textarea name="admin_notes" rows="6" class="form-control @error('admin_notes') is-invalid @enderror"
                                placeholder="Add a reply or guidance for the member...">{{ old('admin_notes', $report->admin_notes) }}</textarea>
                            @error('admin_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Save Status &amp; Notify User</button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
