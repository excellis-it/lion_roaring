@extends('user.layouts.master')
@section('title')
    Activity List - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <style>
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
                                                    <option value=""
                                                        {{ request('user_roles') == '' ? 'selected' : '' }}>All Roles
                                                    </option>
                                                    @foreach ($filters['roles'] as $role)
                                                        @if (trim((string) $role) === '')
                                                            @continue
                                                        @endif
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

            // Load activities on page load
            loadActivities(1);

            // Load activities with AJAX pagination
            function loadActivities(page) {
                $('#activity-loading').show();
                $('#activity-table-container').hide();

                const requestData = {
                    page: page,
                    per_page: 10
                };

                if (Object.keys(currentFilters).length > 0) {
                    Object.assign(requestData, currentFilters);
                }

                $.ajax({
                    url: '{{ route('user-activity-get-activities') }}',
                    type: 'GET',
                    data: requestData,
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
                        alert('Error loading activities. Please check console for details.');
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

                const userName = $('input[name="user_name"]').val();
                const email = $('input[name="email"]').val();
                const userRoles = $('select[name="user_roles"]').val();
                const countryName = $('select[name="country_name"]').val();
                const activityType = $('select[name="activity_type"]').val();
                const dateFrom = $('input[name="date_from"]').val();
                const dateTo = $('input[name="date_to"]').val();

                if (userName && userName.trim() !== '') {
                    currentFilters.user_name = userName.trim();
                }
                if (email && email.trim() !== '') {
                    currentFilters.email = email.trim();
                }
                if (userRoles && userRoles !== '') {
                    currentFilters.user_roles = userRoles;
                }
                if (countryName && countryName !== '') {
                    currentFilters.country_name = countryName;
                }
                if (activityType && activityType !== '') {
                    currentFilters.activity_type = activityType;
                }
                if (dateFrom && dateFrom !== '') {
                    currentFilters.date_from = dateFrom;
                }
                if (dateTo && dateTo !== '') {
                    currentFilters.date_to = dateTo;
                }

                loadActivities(1);
            });

            // Reset filter
            $('#reset-filter').on('click', function(e) {
                e.preventDefault();

                $('input[name="user_name"]').val('');
                $('input[name="email"]').val('');
                $('select[name="user_roles"]').val('');
                $('select[name="country_name"]').val('');
                $('select[name="activity_type"]').val('');
                $('input[name="date_from"]').val('');
                $('input[name="date_to"]').val('');

                currentFilters = {};
                loadActivities(1);
            });

            // Prevent form submission on enter key
            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                $('#apply-filter').click();
            });

            // Allow Enter key to trigger filter
            $('#filter-form input, #filter-form select').on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault();
                    $('#apply-filter').click();
                }
            });
        });
    </script>
@endpush
