@extends('user.layouts.master')
@section('title')
    Change Logs - {{ env('APP_NAME') }}
@endsection
@section('content')
<style>
    .cl-page-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .cl-page-header h3 { margin: 0; font-weight: 700; letter-spacing: -0.02em; }
    .cl-version-chip {
        display: inline-flex; align-items: center; gap: 0.4rem;
        margin-top: 0.5rem; padding: 0.35rem 0.75rem;
        background: #f4f0fa; color: #5b2d8e; border-radius: 999px;
        font-size: 0.85rem; font-weight: 600;
    }
    .cl-version-chip span { color: #6c757d; font-weight: 500; }
    .cl-tabs {
        display: inline-flex; gap: 0.25rem; padding: 0.25rem;
        background: #f3f4f6; border-radius: 999px; margin-bottom: 1.25rem;
    }
    .cl-tabs a {
        padding: 0.45rem 1rem; border-radius: 999px; font-size: 0.9rem;
        font-weight: 600; color: #4b5563; text-decoration: none; transition: all .15s ease;
    }
    .cl-tabs a:hover { color: #1f2937; }
    .cl-tabs a.active { background: #6f42c1; color: #fff; box-shadow: 0 1px 3px rgba(111,66,193,.35); }
    .cl-version-panel {
        border: 1px solid #e9e4f3; background: linear-gradient(135deg, #faf8fd 0%, #f5f1fb 100%);
        border-radius: 12px; padding: 1rem 1.15rem; margin-bottom: 1.25rem;
    }
    .cl-version-panel h6 { margin: 0 0 0.75rem; font-weight: 700; color: #3b2a5a; }
    .cl-entry {
        position: relative; border: 1px solid #ece8f4; border-radius: 14px;
        padding: 1.15rem 1.25rem 1.15rem 1.4rem; margin-bottom: 1rem;
        background: #fff; transition: border-color .15s ease, box-shadow .15s ease;
    }
    .cl-entry:hover { border-color: #d5c8eb; box-shadow: 0 6px 18px rgba(91,45,142,.06); }
    .cl-entry::before {
        content: ''; position: absolute; left: 0; top: 14px; bottom: 14px; width: 4px;
        border-radius: 0 4px 4px 0; background: #6f42c1;
    }
    .cl-entry.is-unpublished::before { background: #9ca3af; }
    .cl-entry-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 0.75rem; flex-wrap: wrap; }
    .cl-entry-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 0.4rem; margin-bottom: 0.35rem; }
    .cl-entry-title { margin: 0; font-size: 1.05rem; font-weight: 700; color: #1f2937; line-height: 1.35; }
    .cl-entry-date { color: #6b7280; font-size: 0.85rem; white-space: nowrap; }
    .cl-entry-body { margin-top: 0.75rem; color: #374151; font-size: 0.95rem; line-height: 1.6; }
    .cl-entry-body p { margin-bottom: 0.5rem; }
    .cl-entry-body p:last-child { margin-bottom: 0; }
    .cl-entry-body ul, .cl-entry-body ol {
        margin: 0.35rem 0 0.5rem 0;
        padding-left: 1.5rem;
        list-style-position: outside;
    }
    .cl-entry-body ul { list-style-type: disc; }
    .cl-entry-body ol { list-style-type: decimal; }
    .cl-entry-body li { margin-bottom: 0.25rem; display: list-item; }
    .cl-entry-actions { display: flex; gap: 0.4rem; margin-top: 0.85rem; }
    .cl-empty {
        text-align: center; color: #6b7280; padding: 3rem 1rem;
        border: 1px dashed #e5e7eb; border-radius: 12px;
    }
</style>
<div class="container-fluid">
    <div class="bg_white_border">
        <div class="cl-page-header">
            <div>
                <h3>Change Logs</h3>
                @php
                    $currentVersion = $platform === 'mobile' ? $mobileVersion : $webVersion;
                    $versionLabel = $platform === 'mobile' ? 'Mobile App' : 'Web';
                @endphp
                @if (!empty($currentVersion))
                    <div class="cl-version-chip">
                        <span>{{ $versionLabel }}</span>
                        {{ $currentVersion }}
                    </div>
                @endif
            </div>
            @if (auth()->user()->hasNewRole('SUPER ADMIN') || auth()->user()->can('Manage Change Logs'))
            <div>
                <a href="{{ route('change-logs.create', ['platform' => $platform]) }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Add Entry
                </a>
            </div>
            @endif
        </div>

        <div class="cl-tabs">
            <a class="{{ $platform === 'web' ? 'active' : '' }}"
               href="{{ route('change-logs.index', ['platform' => 'web']) }}">Web Version</a>
            <a class="{{ $platform === 'mobile' ? 'active' : '' }}"
               href="{{ route('change-logs.index', ['platform' => 'mobile']) }}">Mobile App Version</a>
        </div>

        @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (auth()->user()->hasNewRole('SUPER ADMIN') || auth()->user()->can('Manage Change Logs'))
        <div class="cl-version-panel">
            <h6>Current App Versions</h6>
            <form action="{{ route('change-logs.versions.update') }}" method="POST" class="row g-2 align-items-end">
                @csrf
                @method('PUT')
                <input type="hidden" name="platform" value="{{ $platform }}">
                <div class="col-md-4">
                    <label class="form-label small mb-1 fw-semibold">Web App Version</label>
                    <input type="text" name="WEB_APP_VERSION" class="form-control"
                        value="{{ old('WEB_APP_VERSION', $webVersion) }}" placeholder="e.g. v2.1.1">
                </div>
                <div class="col-md-4">
                    <label class="form-label small mb-1 fw-semibold">Mobile App Version</label>
                    <input type="text" name="MOBILE_APP_VERSION" class="form-control"
                        value="{{ old('MOBILE_APP_VERSION', $mobileVersion) }}" placeholder="e.g. v1.0.37">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Update Versions</button>
                </div>
            </form>
            <p class="text-muted small mb-0 mt-2">
                Shown on this page and in the PMA profile menu. After a release, bump the version here and add a matching entry below.
            </p>
        </div>
        @endif

        @forelse($changeLogs as $log)
            @php
                $isPublished = $log->published_at && $log->published_at->lte(now());
                $desc = $log->description ?? '';
                $hasHtml = preg_match('/<(p|ul|ol|li|br|b|strong|i|em|u)\b/i', $desc);
            @endphp
            <article class="cl-entry {{ $isPublished ? '' : 'is-unpublished' }}">
                <div class="cl-entry-top">
                    <div>
                        <div class="cl-entry-meta">
                            <span class="badge bg-secondary">{{ $log->version }}</span>
                            <span class="{{ $log->typeBadgeClass() }}">{{ ucfirst($log->type) }}</span>
                            @if (!$isPublished)
                                <span class="badge bg-light text-dark border">Unpublished</span>
                            @endif
                        </div>
                        <h4 class="cl-entry-title">{{ $log->title }}</h4>
                    </div>
                    <div class="cl-entry-date">
                        {{ $log->published_at?->timezone(auth()->user()->time_zone ?? config('app.timezone'))->format('d M Y') }}
                    </div>
                </div>
                <div class="cl-entry-body">
                    @if ($hasHtml)
                        {!! $desc !!}
                    @else
                        {!! nl2br(e($desc)) !!}
                    @endif
                </div>
                @if (auth()->user()->hasNewRole('SUPER ADMIN') || auth()->user()->can('Manage Change Logs'))
                <div class="cl-entry-actions">
                    <a href="{{ route('change-logs.edit', $log) }}" class="edit_icon" title="Edit">
                        <i class="ti ti-edit"></i>
                    </a>
                    <form action="{{ route('change-logs.destroy', $log) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this entry?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete_icon border-0 bg-transparent p-0" title="Delete">
                            <i class="ti ti-trash"></i>
                        </button>
                    </form>
                </div>
                @endif
            </article>
        @empty
            <div class="cl-empty">
                No {{ $platform === 'mobile' ? 'Mobile App' : 'Web' }} change log entries yet.
            </div>
        @endforelse

        {{ $changeLogs->links() }}
    </div>
</div>
@endsection
