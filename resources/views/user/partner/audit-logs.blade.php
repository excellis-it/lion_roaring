@extends('user.layouts.master')
@section('title')
    @if ($targetUser)
        Audit Logs — {{ $targetUser->full_name }} - {{ env('APP_NAME') }}
    @else
        Member Audit Logs - {{ env('APP_NAME') }}
    @endif
@endsection
@push('styles')
    <style>
        .audit-page {
            --audit-ink: #1c2434;
            --audit-muted: #6b7280;
            --audit-line: #e5e7eb;
            --audit-soft: #f8fafc;
            --audit-accent: #0f766e;
            --audit-accent-soft: #ccfbf1;
            --audit-danger: #b91c1c;
            --audit-danger-soft: #fee2e2;
            --audit-warn: #a16207;
            --audit-warn-soft: #fef3c7;
        }

        .audit-page .audit-hero {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 1rem;
            align-items: flex-end;
            margin-bottom: 1.25rem;
        }

        .audit-page .audit-hero h2 {
            margin: 0;
            font-size: 1.65rem;
            font-weight: 700;
            color: var(--audit-ink);
            letter-spacing: -0.02em;
        }

        .audit-page .audit-hero p {
            margin: 0.35rem 0 0;
            color: var(--audit-muted);
        }

        .audit-page .audit-filters {
            background: var(--audit-soft);
            border: 1px solid var(--audit-line);
            border-radius: 16px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .audit-page .audit-filters .form-control,
        .audit-page .audit-filters .form-select {
            border-radius: 10px;
            border-color: #d1d5db;
        }

        .audit-page .audit-timeline {
            position: relative;
            padding-left: 1.25rem;
        }

        .audit-page .audit-timeline::before {
            content: '';
            position: absolute;
            left: 0.35rem;
            top: 0.25rem;
            bottom: 0.25rem;
            width: 2px;
            background: linear-gradient(180deg, #99f6e4, #e5e7eb 40%, #e5e7eb);
        }

        .audit-card {
            position: relative;
            background: #fff;
            border: 1px solid var(--audit-line);
            border-radius: 18px;
            padding: 1.1rem 1.2rem;
            margin-bottom: 1rem;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .audit-card:hover {
            border-color: #99f6e4;
            box-shadow: 0 12px 28px rgba(15, 118, 110, 0.08);
        }

        .audit-card::before {
            content: '';
            position: absolute;
            left: -1.08rem;
            top: 1.35rem;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid var(--audit-accent);
            box-shadow: 0 0 0 4px var(--audit-accent-soft);
        }

        .audit-card__head {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem 1rem;
            justify-content: space-between;
            align-items: flex-start;
        }

        .audit-card__meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
            margin-top: 0.65rem;
        }

        .audit-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            border-radius: 999px;
            padding: 0.2rem 0.65rem;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1.4;
            background: #eef2ff;
            color: #3730a3;
        }

        .audit-badge--action {
            background: var(--audit-accent-soft);
            color: #115e59;
        }

        .audit-badge--source {
            background: #ecfeff;
            color: #0e7490;
        }

        .audit-badge--count {
            background: #f3f4f6;
            color: #374151;
        }

        .audit-card__people {
            margin-top: 0.85rem;
            display: grid;
            gap: 0.35rem;
            color: var(--audit-ink);
        }

        .audit-card__people small {
            color: var(--audit-muted);
        }

        .audit-card__summary {
            margin-top: 0.85rem;
            color: var(--audit-muted);
            font-size: 0.92rem;
        }

        .audit-diff-list {
            margin: 0.9rem 0 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 0.55rem;
        }

        .audit-diff-list li {
            display: grid;
            grid-template-columns: minmax(120px, 180px) 1fr;
            gap: 0.75rem;
            padding: 0.7rem 0.85rem;
            border-radius: 12px;
            background: var(--audit-soft);
            border: 1px solid var(--audit-line);
        }

        .audit-diff-list .field-label {
            font-weight: 650;
            color: var(--audit-ink);
            font-size: 0.88rem;
        }

        .audit-diff-list .field-values {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.4rem 0.55rem;
            font-size: 0.88rem;
        }

        .audit-old {
            color: var(--audit-danger);
            background: var(--audit-danger-soft);
            border-radius: 8px;
            padding: 0.15rem 0.45rem;
            text-decoration: line-through;
            text-decoration-thickness: 1px;
        }

        .audit-new {
            color: #065f46;
            background: #d1fae5;
            border-radius: 8px;
            padding: 0.15rem 0.45rem;
            font-weight: 600;
        }

        .audit-arrow {
            color: var(--audit-muted);
        }

        .audit-perm-diff {
            display: grid;
            gap: 0.65rem;
            width: 100%;
        }

        .audit-perm-group-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 0.35rem;
        }

        .audit-perm-group-title.is-added {
            color: #047857;
        }

        .audit-perm-group-title.is-removed {
            color: var(--audit-danger);
        }

        .audit-perm-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
        }

        .audit-perm-chip {
            border-radius: 999px;
            padding: 0.2rem 0.55rem;
            font-size: 0.78rem;
            font-weight: 600;
            line-height: 1.35;
            max-width: 100%;
            word-break: break-word;
        }

        .audit-perm-chip.is-added {
            background: #d1fae5;
            color: #065f46;
        }

        .audit-perm-chip.is-removed {
            background: var(--audit-danger-soft);
            color: var(--audit-danger);
            text-decoration: line-through;
        }

        .audit-perm-empty {
            font-size: 0.82rem;
            color: var(--audit-muted);
        }

        .audit-empty {
            text-align: center;
            padding: 3rem 1rem;
            border: 1px dashed var(--audit-line);
            border-radius: 18px;
            color: var(--audit-muted);
            background: var(--audit-soft);
        }

        @media (max-width: 767.98px) {
            .audit-diff-list li {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush
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
    <div class="container-fluid audit-page">
        <div class="bg_white_border">
            <div class="audit-hero">
                <div>
                    <h2>
                        @if ($targetUser)
                            Audit timeline — {!! no_translate($targetUser->full_name) !!}
                        @else
                            Member audit timeline
                        @endif
                    </h2>
                    <p>
                        @if ($targetUser)
                            {{ $targetUser->email }} · field-level history for this member
                        @else
                            Who changed what — profile fields, roles, and permissions
                        @endif
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('partners.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left"></i> Members List
                    </a>
                    <a href="{{ $exportUrl }}" class="btn btn-primary">
                        <i class="ti ti-download"></i> Export
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ $formAction }}" class="audit-filters row g-2">
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
                    <label class="form-label small text-muted mb-1">By</label>
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
                <div class="col-md-12 d-flex gap-2 mt-1">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-filter"></i> Filter
                    </button>
                    <a href="{{ $formAction }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="audit-timeline">
                @forelse ($logs as $log)
                    @php
                        $changes = $log->field_changes ?? [];
                        $changeCount = count($changes);
                        $added = count($log->permissions_added ?? []);
                        $removed = count($log->permissions_removed ?? []);
                    @endphp
                    <article class="audit-card">
                        <div class="audit-card__head">
                            <div>
                                <div class="fw-semibold" style="color: var(--audit-ink); font-size: 1.02rem;">
                                    {{ optional($log->created_at)->format('M d, Y · H:i') ?? '—' }}
                                </div>
                                <div class="audit-card__meta">
                                    <span class="audit-badge audit-badge--action">
                                        {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                    <span class="audit-badge audit-badge--source">
                                        {{ ucwords(str_replace('_', ' ', $log->source ?? '—')) }}
                                    </span>
                                    @if ($changeCount)
                                        <span class="audit-badge audit-badge--count">
                                            {{ $changeCount }} field{{ $changeCount === 1 ? '' : 's' }}
                                        </span>
                                    @endif
                                    @if ($added || $removed)
                                        <span class="audit-badge audit-badge--count">
                                            Perms +{{ $added }} / −{{ $removed }}
                                        </span>
                                    @endif
                                    @if ($log->ip)
                                        <span class="audit-badge">IP {{ $log->ip }}</span>
                                    @endif
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse"
                                data-bs-target="#audit-detail-{{ $log->id }}" aria-expanded="{{ $changeCount ? 'true' : 'false' }}">
                                Details
                            </button>
                        </div>

                        <div class="audit-card__people">
                            <div>
                                <strong>By</strong> {{ $log->actor_name ?? 'System' }}
                                @if ($log->actor_email)
                                    <small>{{ $log->actor_email }}</small>
                                @endif
                            </div>
                            <div>
                                @if ($log->target_user_name || $log->target_user_email)
                                    <strong>Member</strong> {{ $log->target_user_name ?? '—' }}
                                    @if ($log->target_user_email)
                                        <small>{{ $log->target_user_email }}</small>
                                    @endif
                                @elseif ($log->role_template_name)
                                    <strong>Template</strong> {{ $log->role_template_name }}
                                @endif
                            </div>
                        </div>

                        @if ($changeCount === 0 && ($log->old_role_name || $log->new_role_name || $added || $removed))
                            <div class="audit-card__summary">
                                @if ($log->old_role_name || $log->new_role_name)
                                    Role: {{ $log->old_role_name ?? '—' }} → {{ $log->new_role_name ?? '—' }}
                                @endif
                                @if ($added || $removed)
                                    · Permissions +{{ $added }} / −{{ $removed }}
                                @endif
                            </div>
                        @elseif ($changeCount)
                            <div class="audit-card__summary">
                                @php
                                    $summaryLabels = collect($changes)->take(3)->map(function ($change) {
                                        if (($change['field'] ?? '') === 'permissions') {
                                            $a = count($change['added'] ?? []);
                                            $r = count($change['removed'] ?? []);
                                            if ($a === 0 && $r === 0 && is_array($change['old'] ?? null) && is_array($change['new'] ?? null)) {
                                                $a = count(array_values(array_diff($change['new'], $change['old'])));
                                                $r = count(array_values(array_diff($change['old'], $change['new'])));
                                            }
                                            return 'Permissions +' . $a . '/−' . $r;
                                        }
                                        return $change['label'] ?? ($change['field'] ?? 'Field');
                                    });
                                @endphp
                                {{ $summaryLabels->implode(', ') }}
                                @if ($changeCount > 3)
                                    +{{ $changeCount - 3 }} more
                                @endif
                            </div>
                        @endif

                        <div class="collapse {{ $changeCount ? 'show' : '' }}" id="audit-detail-{{ $log->id }}">
                            @if ($changeCount)
                                <ul class="audit-diff-list">
                                    @foreach ($changes as $change)
                                        <li>
                                            <div class="field-label">{{ $change['label'] ?? ($change['field'] ?? 'Field') }}</div>
                                            <div class="field-values">
                                                @if (($change['field'] ?? '') === 'permissions')
                                                    @php
                                                        $permAdded = $change['added'] ?? null;
                                                        $permRemoved = $change['removed'] ?? null;
                                                        if ($permAdded === null || $permRemoved === null) {
                                                            $oldList = is_array($change['old'] ?? null) ? $change['old'] : [];
                                                            $newList = is_array($change['new'] ?? null) ? $change['new'] : [];
                                                            $permAdded = array_values(array_diff($newList, $oldList));
                                                            $permRemoved = array_values(array_diff($oldList, $newList));
                                                        }
                                                    @endphp
                                                    <div class="audit-perm-diff">
                                                        <div>
                                                            <div class="audit-perm-group-title is-added">
                                                                Added ({{ count($permAdded) }})
                                                            </div>
                                                            @if (count($permAdded))
                                                                <div class="audit-perm-chips">
                                                                    @foreach ($permAdded as $permission)
                                                                        <span class="audit-perm-chip is-added">+ {{ $permission }}</span>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <div class="audit-perm-empty">None</div>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <div class="audit-perm-group-title is-removed">
                                                                Removed ({{ count($permRemoved) }})
                                                            </div>
                                                            @if (count($permRemoved))
                                                                <div class="audit-perm-chips">
                                                                    @foreach ($permRemoved as $permission)
                                                                        <span class="audit-perm-chip is-removed">− {{ $permission }}</span>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <div class="audit-perm-empty">None</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    @php
                                                        $old = $change['old'] ?? null;
                                                        $new = $change['new'] ?? null;
                                                        $oldText = is_array($old) ? implode(', ', $old) : ($old ?? '—');
                                                        $newText = is_array($new) ? implode(', ', $new) : ($new ?? '—');
                                                    @endphp
                                                    <span class="audit-old">{{ $oldText }}</span>
                                                    <span class="audit-arrow">→</span>
                                                    <span class="audit-new">{{ $newText }}</span>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="mt-3">
                                    @if ($log->old_user_type || $log->new_user_type)
                                        <div class="mb-2"><strong>User type:</strong>
                                            {{ $log->old_user_type ?? '—' }} → {{ $log->new_user_type ?? '—' }}</div>
                                    @endif
                                    @if ($log->old_membership_tier_name || $log->new_membership_tier_name)
                                        <div class="mb-2"><strong>Membership tier:</strong>
                                            {{ $log->old_membership_tier_name ?? '—' }} →
                                            {{ $log->new_membership_tier_name ?? '—' }}</div>
                                    @endif
                                    @if (!empty($log->permissions_added))
                                        <div class="mb-2">
                                            <strong class="text-success">Permissions added</strong>
                                            <ul class="mb-0 small">
                                                @foreach ($log->permissions_added as $permission)
                                                    <li>{{ $permission }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    @if (!empty($log->permissions_removed))
                                        <div class="mb-2">
                                            <strong class="text-danger">Permissions removed</strong>
                                            <ul class="mb-0 small">
                                                @foreach ($log->permissions_removed as $permission)
                                                    <li>{{ $permission }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    @if ($log->user_agent)
                                        <div class="small text-muted mt-2">{{ $log->user_agent }}</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="audit-empty">
                        <div class="fw-semibold mb-1">No audit logs yet</div>
                        <div>Changes will appear here after members are created or updated.</div>
                    </div>
                @endforelse
            </div>

            @if ($logs->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {!! $logs->links() !!}
                </div>
            @endif
        </div>
    </div>
@endsection
