@extends('user.layouts.master')
@section('title')
    @if ($targetUser)
        Audit Logs — {{ $targetUser->full_name }} - {{ env('APP_NAME') }}
    @else
        Role & Permission Audit Logs - {{ env('APP_NAME') }}
    @endif
@endsection
@section('content')
    @php
        $formAction = $targetUser
            ? route('partners.audit-logs.member', Crypt::encrypt($targetUser->id))
            : route('partners.audit-logs');
        $exportRoute = $targetUser
            ? route('partners.audit-logs.member.export', Crypt::encrypt($targetUser->id))
            : route('partners.audit-logs.export');
        $exportUrl = $exportRoute . '?' . http_build_query(request()->except('page'));
    @endphp
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row mb-3">
                <div class="col-md-8">
                    <h2 class="mb-1">
                        @if ($targetUser)
                            Audit Logs — {!! no_translate($targetUser->full_name) !!}
                        @else
                            Role & Permission Audit Logs
                        @endif
                    </h2>
                    @if ($targetUser)
                        <p class="text-muted mb-0">{{ $targetUser->email }}</p>
                    @endif
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('partners.index') }}" class="btn btn-primary">
                        <i class="ti ti-arrow-left"></i> Members List
                    </a>
                    <a href="{{ $exportUrl }}" class="btn btn-primary">
                        <i class="ti ti-download"></i> Export
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ $formAction }}" class="row g-2 mb-3">
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Date from</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Date to</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Action</label>
                    <select name="action" class="form-control">
                        <option value="">All Actions</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}" @selected(request('action') === $action)>
                                {{ ucwords(str_replace('_', ' ', $action)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Source</label>
                    <select name="source" class="form-control">
                        <option value="">All Sources</option>
                        @foreach ($sources as $source)
                            <option value="{{ $source }}" @selected(request('source') === $source)>
                                {{ ucwords(str_replace('_', ' ', $source)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Actor</label>
                    <input type="text" name="actor" class="form-control" placeholder="Name or email"
                        value="{{ request('actor') }}">
                </div>
                @if (!$targetUser)
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Target member</label>
                        <input type="text" name="target" class="form-control" placeholder="Name or email"
                            value="{{ request('target') }}">
                    </div>
                @endif
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Role</label>
                    <input type="text" name="role" class="form-control" placeholder="Role name"
                        value="{{ request('role') }}">
                </div>
                <div class="col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-filter"></i> Filter
                    </button>
                    <a href="{{ $formAction }}" class="btn btn-primary">Reset</a>
                </div>
            </form>

            <div class="table-responsive card card-body shadow p-0">
                <table class="table align-middle color_body_text table-light table-borderless mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="p-3" style="width: 40px;"></th>
                            <th class="p-3">Date / Time</th>
                            <th class="p-3">Action</th>
                            <th class="p-3">Actor</th>
                            <th class="p-3">Target Member</th>
                            <th class="p-3">Role</th>
                            <th class="p-3">Permissions</th>
                            <th class="p-3">Source</th>
                            <th class="p-3">IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td class="p-3">
                                    <button class="btn btn-sm btn-link p-0 text-primary" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#audit-detail-{{ $log->id }}"
                                        aria-expanded="false" aria-controls="audit-detail-{{ $log->id }}">
                                        <i class="ti ti-chevron-down"></i>
                                    </button>
                                </td>
                                <td class="p-3 text-nowrap">
                                    {{ optional($log->created_at)->format('M d, Y H:i') ?? '—' }}
                                </td>
                                <td class="p-3">
                                    {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                </td>
                                <td class="p-3">
                                    <div>{{ $log->actor_name ?? '—' }}</div>
                                    @if ($log->actor_email)
                                        <small class="text-muted">{{ $log->actor_email }}</small>
                                    @endif
                                </td>
                                <td class="p-3">
                                    @if ($log->target_user_name || $log->target_user_email)
                                        <div>{{ $log->target_user_name ?? '—' }}</div>
                                        @if ($log->target_user_email)
                                            <small class="text-muted">{{ $log->target_user_email }}</small>
                                        @endif
                                    @elseif ($log->role_template_name)
                                        <span class="text-muted">Template: {{ $log->role_template_name }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="p-3">
                                    @if ($log->old_role_name || $log->new_role_name)
                                        {{ $log->old_role_name ?? '—' }} → {{ $log->new_role_name ?? '—' }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="p-3">
                                    @php
                                        $added = count($log->permissions_added ?? []);
                                        $removed = count($log->permissions_removed ?? []);
                                    @endphp
                                    @if ($added || $removed)
                                        <span class="text-success">+{{ $added }}</span>
                                        <span class="text-danger">−{{ $removed }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="p-3">{{ ucwords(str_replace('_', ' ', $log->source ?? '—')) }}</td>
                                <td class="p-3">{{ $log->ip ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td colspan="9" class="p-0 border-0">
                                    <div class="collapse" id="audit-detail-{{ $log->id }}">
                                        <div class="p-3 bg-light border-top">
                                            <div class="row g-3">
                                                @if ($log->old_user_type || $log->new_user_type)
                                                    <div class="col-md-6">
                                                        <strong>User type:</strong>
                                                        {{ $log->old_user_type ?? '—' }} → {{ $log->new_user_type ?? '—' }}
                                                    </div>
                                                @endif
                                                @if ($log->old_membership_tier_name || $log->new_membership_tier_name)
                                                    <div class="col-md-6">
                                                        <strong>Membership tier:</strong>
                                                        {{ $log->old_membership_tier_name ?? '—' }} →
                                                        {{ $log->new_membership_tier_name ?? '—' }}
                                                    </div>
                                                @endif
                                                @if (!empty($log->permissions_added))
                                                    <div class="col-md-6">
                                                        <strong class="text-success">Permissions added</strong>
                                                        <ul class="mb-0 small">
                                                            @foreach ($log->permissions_added as $permission)
                                                                <li>{{ $permission }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                                @if (!empty($log->permissions_removed))
                                                    <div class="col-md-6">
                                                        <strong class="text-danger">Permissions removed</strong>
                                                        <ul class="mb-0 small">
                                                            @foreach ($log->permissions_removed as $permission)
                                                                <li>{{ $permission }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                                @if (!empty($log->old_permissions))
                                                    <div class="col-md-6">
                                                        <strong>Old permissions (full)</strong>
                                                        <ul class="mb-0 small">
                                                            @foreach ($log->old_permissions as $permission)
                                                                <li>{{ $permission }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                                @if (!empty($log->new_permissions))
                                                    <div class="col-md-6">
                                                        <strong>New permissions (full)</strong>
                                                        <ul class="mb-0 small">
                                                            @foreach ($log->new_permissions as $permission)
                                                                <li>{{ $permission }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                                @if ($log->user_agent)
                                                    <div class="col-12">
                                                        <strong>User agent:</strong>
                                                        <span class="small text-muted">{{ $log->user_agent }}</span>
                                                    </div>
                                                @endif
                                                @if (!empty($log->meta))
                                                    <div class="col-12">
                                                        <strong>Meta:</strong>
                                                        <pre class="small mb-0 bg-white p-2 border rounded">{{ json_encode($log->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center p-4 text-muted">No audit logs yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($logs->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {!! $logs->links() !!}
                </div>
            @endif
        </div>
    </div>
@endsection
