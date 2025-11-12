@extends('user.layouts.master')
@section('title')
    User Activity - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <style>
        .stats-card {
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 1px 1px 10px 0px rgba(0.1, 0.1, 0.1, 0.2);
        }

        .stats-card h4 {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .stats-card .number {
            font-size: 32px;
            font-weight: bold;
            color: #7851A9;
        }

        .stat-item {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .stat-item:last-child {
            border-bottom: none;
        }

        .stat-item .label {
            font-weight: 500;
            color: #333;
        }

        .stat-item .count {
            color: #7851A9;
            font-weight: 600;
        }

        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <form>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Statistics Cards -->
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="stats-card bg-white">
                                            <h4>Total Activities</h4>
                                            <div class="number">{{ number_format($stats['total_activities']) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stats-card bg-white">
                                            <h4>Active Countries</h4>
                                            <div class="number">{{ $stats['activities_by_country']->count() }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stats-card bg-white">
                                            <h4>Active Users</h4>
                                            <div class="number">{{ $stats['activities_by_user']->count() }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stats-card bg-white">
                                            <h4>Activity Types</h4>
                                            <div class="number">{{ $stats['activities_by_type']->count() }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Detailed Statistics -->
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="stats-card bg-white">
                                            <h4>Top Countries by Activity</h4>
                                            <div style="max-height: 250px; overflow-y: auto;">
                                                @foreach ($stats['activities_by_country']->take(5) as $item)
                                                    <div class="stat-item d-flex justify-content-between">
                                                        <span class="label">{{ $item->country_name ?: 'Unknown' }}</span>
                                                        <span class="count">{{ number_format($item->count) }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="stats-card bg-white">
                                            <h4>Top Users by Activity</h4>
                                            <div style="max-height: 250px; overflow-y: auto;">
                                                @foreach ($stats['activities_by_user']->take(5) as $item)
                                                    <div class="stat-item d-flex justify-content-between">
                                                        <span class="label">{{ Str::limit($item->user_name, 20) }}</span>
                                                        <span class="count">{{ number_format($item->count) }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="stats-card bg-white">
                                            <h4>Top Activity Types</h4>
                                            <div style="max-height: 250px; overflow-y: auto;">
                                                @foreach ($stats['activities_by_type']->take(5) as $item)
                                                    <div class="stat-item d-flex justify-content-between">
                                                        <span
                                                            class="label">{{ Str::limit($item->activity_type, 20) }}</span>
                                                        <span class="count">{{ number_format($item->count) }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Filter Section -->
                                <div class="filter-section">
                                    <h4 class="mb-3">Filter Activities</h4>
                                    <form method="GET" action="{{ route('user-activity.index') }}">
                                        <div class="row">
                                            <div class="col-md-3 mb-2">
                                                <label class="form-label">User Name</label>
                                                <input type="text" name="user_name" class="form-control"
                                                    value="{{ request('user_name') }}" placeholder="Search by name">
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control"
                                                    value="{{ request('email') }}" placeholder="Search by email">
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="form-label">Role</label>
                                                <select name="user_roles" class="form-control">
                                                    <option value="">All Roles</option>
                                                    @foreach ($filters['roles'] as $role)
                                                        <option value="{{ $role }}"
                                                            {{ request('user_roles') == $role ? 'selected' : '' }}>
                                                            {{ $role }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="form-label">Country</label>
                                                <select name="country_name" class="form-control">
                                                    <option value="">All Countries</option>
                                                    @foreach ($filters['countries'] as $country)
                                                        <option value="{{ $country }}"
                                                            {{ request('country_name') == $country ? 'selected' : '' }}>
                                                            {{ $country }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="form-label">Activity Type</label>
                                                <select name="activity_type" class="form-control">
                                                    <option value="">All Types</option>
                                                    @foreach ($filters['activity_types'] as $type)
                                                        <option value="{{ $type }}"
                                                            {{ request('activity_type') == $type ? 'selected' : '' }}>
                                                            {{ $type }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="form-label">Date From</label>
                                                <input type="date" name="date_from" class="form-control"
                                                    value="{{ request('date_from') }}">
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="form-label">Date To</label>
                                                <input type="date" name="date_to" class="form-control"
                                                    value="{{ request('date_to') }}">
                                            </div>
                                            <div class="col-md-3 mb-2 d-flex align-items-end">
                                                <button type="submit" class="btn btn-primary me-2">
                                                    <i class="ti ti-filter"></i> Filter
                                                </button>
                                                <a href="{{ route('user-activity.index') }}" class="btn btn-secondary">
                                                    <i class="ti ti-refresh"></i> Reset
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-10">
                                    </div>
                                    <div class="col-md-2 float-right">
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-md-8">
                                        <h3 class="mb-3 float-left">Activity List</h3>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table align-middle bg-white color_body_text">
                                        <thead class="color_head">
                                            <tr>
                                                <th></th>
                                                <th>User Name</th>
                                                <th>Email</th>
                                                <th>User Role</th>
                                                <th>Ecclesia Name</th>
                                                <th>IP</th>
                                                <th>Country Code</th>
                                                <th>Country Name</th>
                                                {{-- <th>Device MAC</th> --}}
                                                <th>Device Type</th>
                                                <th>Browser</th>
                                                <th>URL</th>
                                                {{-- <th>Permission Access</th> --}}
                                                <th>Activity Type</th>
                                                {{-- <th>Activity Description</th> --}}
                                                <th>Activity Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($activities)
                                                @foreach ($activities as $key => $act)
                                                    <tr>
                                                        {{-- <td>{{ $act->user_id }}</td> --}}
                                                        <td>{{ $activities->firstItem() + $key }}</td>
                                                        <td>{{ $act->user_name }}</td>
                                                        <td>{{ $act->email }}</td>
                                                        <td>{{ $act->user_roles }}</td>
                                                        <td>{{ $act->ecclesia_name }}</td>
                                                        <td>{{ $act->ip }}</td>
                                                        <td>{{ $act->country_code }}</td>
                                                        <td>{{ $act->country_name }}</td>
                                                        {{-- <td>{{ $act->device_mac }}</td> --}}
                                                        <td>{{ $act->device_type }}</td>
                                                        <td>{{ $act->browser }}</td>
                                                        <td>{{ $act->url }}</td>
                                                        {{-- <td>{{ $act->permission_access }}</td> --}}
                                                        <td>{{ $act->activity_type }}</td>
                                                        {{-- <td>{{ $act->activity_description }}</td> --}}
                                                        <td>{{ $act->activity_date }}</td>
                                                    </tr>
                                                @endforeach
                                                {{-- pagination --}}
                                                <tr class="toxic">
                                                    <td colspan="16">
                                                        <div class="d-flex justify-content-center">
                                                            {!! $activities->links() !!}
                                                        </div>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr class="toxic">
                                                    <td colspan="16" class="text-center">No Data Found</td>
                                                </tr>
                                            @endif

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '#delete', function(e) {
            swal({
                    title: "Are you sure?",
                    text: "To delete this activity.",
                    type: "warning",
                    confirmButtonText: "Yes",
                    showCancelButton: true
                })
                .then((result) => {
                    if (result.value) {
                        window.location = $(this).data('route');
                    } else if (result.dismiss === 'cancel') {
                        swal(
                            'Cancelled',
                            'Your stay here :)',
                            'error'
                        )
                    }
                })
        });
    </script>
@endpush
