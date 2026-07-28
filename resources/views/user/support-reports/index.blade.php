@extends('user.layouts.master')
@section('title')
    Support Reports - {{ env('APP_NAME') }}
@endsection
@section('content')
<style>
    .sr-badge {
        display: inline-flex; align-items: center; padding: 0.35rem 0.75rem;
        border-radius: 999px; font-size: 0.75rem; font-weight: 700;
        letter-spacing: 0.02em; line-height: 1.2; color: inherit !important;
    }
    .sr-badge-open { background: #fde8e8; color: #b42318 !important; }
    .sr-badge-in-progress { background: #fff4ce; color: #8a6d00 !important; }
    .sr-badge-resolved { background: #e3f6e8; color: #0f7a35 !important; }
    .sr-badge-closed { background: #eef0f2; color: #4b5563 !important; }

    .sr-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .sr-header h3 { margin: 0; font-weight: 700; letter-spacing: -0.02em; }
    .sr-header p { margin: 0.35rem 0 0; color: #6b7280; font-size: 0.9rem; }
    .sr-actions { display: flex; flex-wrap: wrap; gap: 0.5rem; }
    .sr-tabs {
        display: inline-flex; gap: 0.25rem; padding: 0.25rem;
        background: #f3f4f6; border-radius: 999px; margin-bottom: 1rem;
    }
    .sr-tabs a {
        padding: 0.45rem 1rem; border-radius: 999px; font-size: 0.9rem;
        font-weight: 600; color: #4b5563; text-decoration: none;
    }
    .sr-tabs a.active { background: #6f42c1; color: #fff; box-shadow: 0 1px 3px rgba(111,66,193,.35); }
    .sr-filters {
        display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;
        margin-bottom: 1.15rem; padding: 0.85rem 1rem;
        background: #faf8fd; border: 1px solid #ebe3f4; border-radius: 12px;
    }
    .sr-filters select.form-control {
        width: 180px !important;
        max-width: 180px !important;
        min-width: 180px !important;
        flex: 0 0 180px;
    }
    .sr-card {
        border: 1px solid #ece8f4; border-radius: 14px; padding: 1.05rem 1.2rem;
        margin-bottom: 0.85rem; background: #fff; transition: border-color .15s, box-shadow .15s;
        position: relative;
    }
    .sr-card:hover { border-color: #d5c8eb; box-shadow: 0 6px 18px rgba(91,45,142,.06); }
    .sr-card::before {
        content: ''; position: absolute; left: 0; top: 12px; bottom: 12px; width: 4px;
        border-radius: 0 4px 4px 0; background: #6f42c1;
    }
    .sr-card.status-open::before { background: #dc3545; }
    .sr-card.status-in_progress::before { background: #ffc107; }
    .sr-card.status-resolved::before { background: #198754; }
    .sr-card.status-closed::before { background: #6c757d; }
    .sr-card-top { display: flex; justify-content: space-between; gap: 0.75rem; flex-wrap: wrap; align-items: flex-start; }
    .sr-card-id { color: #9ca3af; font-size: 0.8rem; font-weight: 600; }
    .sr-card-title { margin: 0.15rem 0 0; font-size: 1.05rem; font-weight: 700; color: #1f2937; }
    .sr-card-meta { color: #6b7280; font-size: 0.85rem; margin-top: 0.35rem; }
    .sr-card-actions { display: flex; gap: 0.5rem; align-items: center; flex-shrink: 0; }
    .sr-empty {
        text-align: center; color: #6b7280; padding: 3rem 1rem;
        border: 1px dashed #e5e7eb; border-radius: 12px;
    }
</style>
<div class="container-fluid">
    <div class="bg_white_border">
        <div class="sr-header">
            <div>
                <h3>{{ $canManage && $scope === 'all' ? 'Support Reports' : 'My Support Reports' }}</h3>
                <p>
                    @if ($canManage && $scope === 'all')
                        Review all submitted reports, update status, and reply to members.
                    @else
                        Track the status of reports you have submitted.
                    @endif
                </p>
            </div>
            <div class="sr-actions">
                <a href="{{ route('support-reports.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Submit New Report
                </a>
            </div>
        </div>

        @if ($canManage)
        <div class="sr-tabs">
            <a class="{{ $scope === 'all' ? 'active' : '' }}"
               href="{{ route('support-reports.index', ['scope' => 'all', 'status' => $statusFilter]) }}">All Reports</a>
            <a class="{{ $scope === 'mine' ? 'active' : '' }}"
               href="{{ route('support-reports.index', ['scope' => 'mine', 'status' => $statusFilter]) }}">My Reports</a>
        </div>
        @endif

        @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        <form method="GET" class="sr-filters">
            @if ($canManage)
                <input type="hidden" name="scope" value="{{ $scope }}">
            @endif
            <label class="mb-0 small fw-semibold text-muted">Status</label>
            <select name="status" class="form-control">
                <option value="">All Statuses</option>
                @foreach(['open','in_progress','resolved','closed'] as $status)
                    <option value="{{ $status }}" @selected($statusFilter === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            @if($statusFilter)
                <a href="{{ route('support-reports.index', $canManage ? ['scope' => $scope] : []) }}" class="btn btn-primary">Clear</a>
            @endif
        </form>

        @forelse($reports as $report)
            <article class="sr-card status-{{ $report->status }}">
                <div class="sr-card-top">
                    <div class="min-w-0">
                        <div class="sr-card-id">#{{ $report->id }}</div>
                        <h4 class="sr-card-title">{{ $report->subject }}</h4>
                        <div class="sr-card-meta">
                            @if ($canManage && $scope === 'all')
                                {!! no_translate($report->user->full_name ?? $report->user->email ?? 'Unknown') !!} ·
                            @endif
                            Submitted {{ $report->created_at->format('d M Y') }}
                        </div>
                    </div>
                    <div class="sr-card-actions">
                        <span class="{{ $report->statusBadgeClass() }}">{{ $report->statusLabel() }}</span>
                        <a href="{{ route('support-reports.show', $report) }}"
                           class="btn btn-primary btn-sm">
                            {{ $canManage ? 'Manage' : 'View' }}
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="sr-empty">
                @if ($canManage && $scope === 'all')
                    No support reports found.
                @else
                    You have not submitted any support reports yet.
                @endif
            </div>
        @endforelse

        {{ $reports->links() }}
    </div>
</div>
@endsection

