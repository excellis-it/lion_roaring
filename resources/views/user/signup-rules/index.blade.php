@extends('user.layouts.master')
@section('title', 'Signup Field Rules')
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0">Signup Field Rules Management</h3>
                    <p class="text-muted small mb-0">Define validation rules for signup form fields</p>
                </div>
                <div>
                    <a href="{{ route('user.signup-rules.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus me-2"></i>Add New Rule
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Info Card -->
            <div class="card mb-4 border-0 shadow-sm bg-light">
                <div class="card-body">
                    <h5 class="mb-2"><i class="fa fa-info-circle text-primary me-2"></i>How It Works</h5>
                    <ul class="mb-0">
                        <li><strong>Critical Rules:</strong> If a user fails a critical rule, they are registered as <span
                                class="badge bg-danger">Inactive</span></li>
                        <li><strong>Non-Critical Rules:</strong> These are warnings only and don't affect user status</li>
                        <li><strong>User Status:</strong> Users who pass all critical rules get <span
                                class="badge bg-success">Active</span> status</li>
                    </ul>
                </div>
            </div>

            <!-- Rules Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @if ($rules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Priority</th>
                                        <th>Field</th>
                                        <th>Rule Type</th>
                                        <th>Rule Value</th>
                                        <th>Critical</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rules as $rule)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">{{ $rule->priority }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ ucwords(str_replace('_', ' ', $rule->field_name)) }}</strong>
                                                @if ($rule->description)
                                                    <br><small
                                                        class="text-muted">{{ Str::limit($rule->description, 40) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <code>{{ $rule->rule_type }}</code>
                                            </td>
                                            <td>
                                                @if ($rule->rule_value)
                                                    <small
                                                        class="text-muted">{{ Str::limit($rule->rule_value, 30) }}</small>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($rule->is_critical)
                                                    <span class="badge bg-danger">
                                                        <i class="fa fa-exclamation-triangle"></i> Critical
                                                    </span>
                                                @else
                                                    <span class="badge bg-info">
                                                        <i class="fa fa-info-circle"></i> Warning
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('user.signup-rules.toggle-status', $rule->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-sm btn-link p-0 text-decoration-none">
                                                        @if ($rule->is_active)
                                                            <span class="badge bg-success">
                                                                <i class="fa fa-check-circle"></i> Active
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary">
                                                                <i class="fa fa-times-circle"></i> Inactive
                                                            </span>
                                                        @endif
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('user.signup-rules.edit', $rule->id) }}"
                                                        class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('user.signup-rules.destroy', $rule->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to delete this rule?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            title="Delete">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Signup Rules Defined</h5>
                            <p class="text-muted">All users will be registered as active by default.</p>
                            <a href="{{ route('user.signup-rules.create') }}" class="btn btn-primary mt-3">
                                <i class="fa fa-plus me-2"></i>Create Your First Rule
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        code {
            background-color: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.9em;
        }
    </style>
@endpush

@push('scripts')
    <script>
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>
@endpush
