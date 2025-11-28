@extends('user.layouts.master')
@section('title')
    Activity Dashboard - {{ env('APP_NAME') }}
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
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
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
                    <div class="row mb-2">
                        <div class="col-md-2">
                            <div class="stats-card bg-white">
                                <h4>Logins Today</h4>
                                <div class="number">{{ number_format($stats['logins_today'] ?? 0) }}</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stats-card bg-white">
                                <h4>Logins This Month</h4>
                                <div class="number">{{ number_format($stats['logins_month'] ?? 0) }}</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stats-card bg-white">
                                <h4>Logins This Year</h4>
                                <div class="number">{{ number_format($stats['logins_year'] ?? 0) }}</div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="stats-card bg-white">
                                <h4>Today's Visits</h4>
                                <div class="number">{{ number_format($stats['visits_today'] ?? 0) }}</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stats-card bg-white">
                                <h4>This Month Visits</h4>
                                <div class="number">{{ number_format($stats['visits_month'] ?? 0) }}</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stats-card bg-white">
                                <h4>This Year Visits</h4>
                                <div class="number">{{ number_format($stats['visits_year'] ?? 0) }}</div>
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

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Load statistics on page load
            loadStatistics('country', 1);
            loadStatistics('user', 1);
            loadStatistics('type', 1);

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
        });
    </script>
@endpush
