@extends('user.layouts.master')
@section('title')
    Manage Support Reports - {{ env('APP_NAME') }}
@endsection
@section('content')
<div class="container-fluid">
    <div class="bg_white_border">
        <div class="row mb-3 align-items-center">
            <div class="col-md-8">
                <h3 class="mb-0">Manage Support Reports</h3>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('support-reports.index') }}" class="btn btn-primary me-2">
                    My Reports
                </a>
                <a href="{{ route('support-reports.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Submit New Report
                </a>
            </div>
        </div>

        @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        <form method="GET" class="mb-3 row g-2 align-items-center">
            <div class="col-auto">
                <select name="status" class="form-control" style="min-width: 180px;">
                    <option value="">All Statuses</option>
                    @foreach(['open','in_progress','resolved','closed'] as $status)
                        <option value="{{ $status }}" @selected($statusFilter === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
            @if($statusFilter)
            <div class="col-auto">
                <a href="{{ route('support-reports.manage.index') }}" class="btn btn-primary">Clear</a>
            </div>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table align-middle bg-white color_body_text">
                <thead class="color_head">
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr>
                        <td>{{ $report->id }}</td>
                        <td>{{ $report->user->name ?? '-' }}</td>
                        <td>{{ $report->subject }}</td>
                        <td><span class="{{ $report->statusBadgeClass() }}">{{ ucfirst(str_replace('_', ' ', $report->status)) }}</span></td>
                        <td>{{ $report->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="d-flex">
                                <a href="{{ route('support-reports.manage.show', $report) }}" class="view_icon me-2" title="View & Respond">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No support reports found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $reports->links() }}
    </div>
</div>
@endsection
