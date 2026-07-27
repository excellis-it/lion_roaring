@extends('user.layouts.master')
@section('title')
    Report #{{ $report->id }} — Management - {{ env('APP_NAME') }}
@endsection
@section('content')
<div class="container-fluid">
    <div class="bg_white_border">
        <div class="row mb-3 align-items-center">
            <div class="col-md-8">
                <h3 class="mb-0">Support Report #{{ $report->id }}</h3>
                <small class="text-muted">Submitted by <strong>{{ $report->user->name ?? '-' }}</strong> on {{ $report->created_at->format('d M Y, H:i') }}</small>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('support-reports.manage.index') }}" class="btn btn-primary">&larr; All Reports</a>
            </div>
        </div>

        @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        <div class="mb-4">
            <h5>{{ $report->subject }}</h5>
            <p style="white-space: pre-wrap;">{{ $report->message }}</p>
            @if($report->attachment)
                <a href="{{ Storage::url($report->attachment) }}" target="_blank" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-paperclip"></i> View Attachment
                </a>
            @endif
        </div>

        <hr>

        <h5 class="mb-3">Update Status & Add Notes</h5>
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
            <div class="mb-3">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-control @error('status') is-invalid @enderror" style="max-width: 280px;">
                    @foreach(['open','in_progress','resolved','closed'] as $status)
                        <option value="{{ $status }}" @selected($report->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Admin Notes <span class="text-muted">(visible to submitter)</span></label>
                <textarea name="admin_notes" rows="5" class="form-control @error('admin_notes') is-invalid @enderror"
                    placeholder="Add a reply or notes...">{{ old('admin_notes', $report->admin_notes) }}</textarea>
                @error('admin_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Save & Notify User</button>
        </form>

        @if($report->resolved_at)
        <p class="text-muted mt-3 small">Last updated {{ $report->resolved_at->format('d M Y, H:i') }} by {{ $report->resolver->name ?? '-' }}</p>
        @endif
    </div>
</div>
@endsection
