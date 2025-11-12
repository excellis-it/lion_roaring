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

        .pagination-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 15px;
            gap: 10px;
        }

        .pagination-wrapper .btn {
            padding: 5px 15px;
        }

        .stat-table-container {
            max-height: 450px;
            overflow-y: auto;
        }

        .loading-spinner {
            text-align: center;
            padding: 20px;
            display: none;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.3em;
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
                                            <div class="number">{{ $stats['activities_by_country_count'] }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stats-card bg-white">
                                            <h4>Active Users</h4>
                                            <div class="number">{{ $stats['activities_by_user_count'] }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stats-card bg-white">
                                            <h4>Activity Types</h4>
                                            <div class="number">{{ $stats['activities_by_type_count'] }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Detailed Statistics with AJAX Pagination -->
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="stats-card bg-white">
                                            <h4>Activities by Country</h4>
                                            <div class="loading-spinner" id="country-loading">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                            <div class="stat-table-container" id="country-stats"></div>
                                            <div class="pagination-wrapper" id="country-pagination"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="stats-card bg-white">
                                            <h4>Activities by User</h4>
                                            <div class="loading-spinner" id="user-loading">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                            <div class="stat-table-container" id="user-stats"></div>
                                            <div class="pagination-wrapper" id="user-pagination"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="stats-card bg-white">
                                            <h4>Activities by Type</h4>
                                            <div class="loading-spinner" id="type-loading">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                            <div class="stat-table-container" id="type-stats"></div>
                                            <div class="pagination-wrapper" id="type-pagination"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Filter Section -->
                                <div class="filter-section">
                                    <h4 class="mb-3">Filter Activities</h4>
                                    <form id="filter-form">
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
                                                <button type="button" id="apply-filter" class="btn btn-primary me-2">
                                                    <i class="ti ti-filter"></i> Filter
                                                </button>
                                                <button type="button" id="reset-filter" class="btn btn-secondary">
                                                    <i class="ti ti-refresh"></i> Reset
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="row ">
                                    <div class="col-md-8">
                                        <h3 class="mb-3 float-left">Activity List</h3>
                                    </div>
                                </div>

                                <div class="loading-spinner" id="activity-loading">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>

                                <div class="table-responsive" id="activity-table-container">
                                    <table class="table align-middle bg-white color_body_text">
                                        <thead class="color_head">
                                            <tr>
                                                <th>#</th>
                                                <th>User Name</th>
                                                <th>Email</th>
                                                <th>User Role</th>
                                                <th>Ecclesia Name</th>
                                                <th>IP</th>
                                                <th>Country Code</th>
                                                <th>Country Name</th>
                                                <th>Device Type</th>
                                                <th>Browser</th>
                                                <th>URL</th>
                                                <th>Activity Type</th>
                                                <th>Activity Date</th>
                                            </tr>
                                        </thead>
                                        <tbody id="activity-tbody"></tbody>
                                    </table>
                                </div>
                                <div class="pagination-wrapper" id="activity-pagination"></div>
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
        $(document).ready(function() {
            let currentPage = 1;
            let currentFilters = {};

            // Load statistics on page load
            loadStatistics('country', 1);
            loadStatistics('user', 1);
            loadStatistics('type', 1);
            loadActivities(1);

            // Load statistics with AJAX pagination
            function loadStatistics(type, page) {
                const endpoints = {
                    'country': '{{ route('user-activity-by-country') }}',
                    'user': '{{ route('user-activity-by-user') }}',
                    'type': '{{ route('user-activity-by-type') }}'
                };

                $(`#${type}-loading`).show();
                $(`#${type}-stats`).hide();

                $.ajax({
                    url: endpoints[type],
                    type: 'GET',
                    data: {
                        page: page,
                        per_page: 10
                    },
                    success: function(response) {
                        $(`#${type}-loading`).hide();
                        $(`#${type}-stats`).show();
                        renderStatistics(type, response);
                        renderPagination(type, response);
                    },
                    error: function(xhr) {
                        $(`#${type}-loading`).hide();
                        $(`#${type}-stats`).show();
                        console.error('Error loading statistics:', xhr);
                    }
                });
            }

            // Render statistics data
            function renderStatistics(type, response) {
                let html = '';
                response.data.forEach(item => {
                    let label = type === 'country' ? (item.country_name || 'Unknown') :
                        type === 'user' ? item.user_name :
                        item.activity_type;

                    html += `
                        <div class="stat-item d-flex justify-content-between">
                            <span class="label">${label.substring(0, 30)}</span>
                            <span class="count">${parseInt(item.count).toLocaleString()}</span>
                        </div>
                    `;
                });
                $(`#${type}-stats`).html(html);
            }

            // Render pagination for statistics
            function renderPagination(type, response) {
                let html = '';
                if (response.prev_page_url) {
                    html +=
                        `<button class="btn btn-sm btn-outline-primary stat-pagination-btn" data-type="${type}" data-page="${response.current_page - 1}">Previous</button>`;
                }
                html += `<span class="mx-2">Page ${response.current_page} of ${response.last_page}</span>`;
                if (response.next_page_url) {
                    html +=
                        `<button class="btn btn-sm btn-outline-primary stat-pagination-btn" data-type="${type}" data-page="${response.current_page + 1}">Next</button>`;
                }
                $(`#${type}-pagination`).html(html);
            }

            // Handle statistics pagination button clicks
            $(document).on('click', '.stat-pagination-btn', function(e) {
                e.preventDefault();
                const type = $(this).data('type');
                const page = $(this).data('page');
                loadStatistics(type, page);
            });

            // Load activities with AJAX pagination
            function loadActivities(page) {
                $('#activity-loading').show();
                $('#activity-table-container').hide();

                $.ajax({
                    url: '{{ route('user-activity-get-activities') }}',
                    type: 'GET',
                    data: {
                        ...currentFilters,
                        page: page,
                        per_page: 10
                    },
                    success: function(response) {
                        $('#activity-loading').hide();
                        $('#activity-table-container').show();
                        renderActivities(response);
                        renderActivityPagination(response);
                        currentPage = page;
                    },
                    error: function(xhr) {
                        $('#activity-loading').hide();
                        $('#activity-table-container').show();
                        console.error('Error loading activities:', xhr);
                        alert('Error loading activities. Please try again.');
                    }
                });
            }

            // Render activities table
            function renderActivities(response) {
                let html = '';
                if (response.data.length === 0) {
                    html = '<tr><td colspan="13" class="text-center">No Data Found</td></tr>';
                } else {
                    response.data.forEach((activity, index) => {
                        const rowNumber = (response.current_page - 1) * response.per_page + index + 1;
                        html += `
                            <tr>
                                <td>${rowNumber}</td>
                                <td>${activity.user_name || '-'}</td>
                                <td>${activity.email || '-'}</td>
                                <td>${activity.user_roles || '-'}</td>
                                <td>${activity.ecclesia_name || '-'}</td>
                                <td>${activity.ip || '-'}</td>
                                <td>${activity.country_code || '-'}</td>
                                <td>${activity.country_name || '-'}</td>
                                <td>${activity.device_type || '-'}</td>
                                <td>${activity.browser || '-'}</td>
                                <td>${activity.url || '-'}</td>
                                <td>${activity.activity_type || '-'}</td>
                                <td>${activity.activity_date || '-'}</td>
                            </tr>
                        `;
                    });
                }
                $('#activity-tbody').html(html);
            }

            // Render activity pagination
            function renderActivityPagination(response) {
                let html = '';
                if (response.prev_page_url) {
                    html +=
                        `<button class="btn btn-sm btn-outline-primary activity-pagination-btn" data-page="${response.current_page - 1}">Previous</button>`;
                }
                html += `<span class="mx-2">Page ${response.current_page} of ${response.last_page}</span>`;
                if (response.next_page_url) {
                    html +=
                        `<button class="btn btn-sm btn-outline-primary activity-pagination-btn" data-page="${response.current_page + 1}">Next</button>`;
                }
                $('#activity-pagination').html(html);
            }

            // Handle activity pagination button clicks
            $(document).on('click', '.activity-pagination-btn', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                loadActivities(page);
            });

            // Apply filter
            $('#apply-filter').on('click', function(e) {
                e.preventDefault();
                currentFilters = {};
                $('#filter-form').serializeArray().forEach(function(item) {
                    if (item.value) {
                        currentFilters[item.name] = item.value;
                    }
                });
                loadActivities(1);
            });

            // Reset filter
            $('#reset-filter').on('click', function(e) {
                e.preventDefault();
                $('#filter-form')[0].reset();
                currentFilters = {};
                loadActivities(1);
            });

            // Prevent form submission on enter key
            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                $('#apply-filter').click();
            });
        });
    </script>
@endpush
