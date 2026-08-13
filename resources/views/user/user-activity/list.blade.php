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
                                                <label class="form-label">Name</label>
                                                <input type="text" name="user_name" class="form-control"
                                                    value="{{ request('user_name') }}" placeholder="Search by name">
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control"
                                                    value="{{ request('email') }}" placeholder="Search by email">
                                            </div>
                                            {{-- <div class="col-md-3 mb-2">
                                                <label class="form-label">Username</label>
                                                <select name="user_roles" class="form-control">
                                                    <option value=""
                                                        {{ request('user_roles') == '' ? 'selected' : '' }}>All Username
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
                                            </div> --}}
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
                                            <div class="col-md-3 mb-2 d-flex align-items-end flex-wrap gap-2">
                                                <button type="button" id="apply-filter" class="btn btn-primary">
                                                    <i class="ti ti-filter"></i> Filter
                                                </button>
                                                <button type="button" id="reset-filter" class="btn btn-secondary">
                                                    <i class="ti ti-refresh"></i> Reset
                                                </button>
                                                <button type="button" id="export-activities" class="btn btn-primary">
                                                    <i class="ti ti-download"></i> Export
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
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Username</th>
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

    <div class="modal fade" id="exportProgressModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Exporting activity list</h5>
                </div>
                <div class="modal-body">
                    <p id="export-progress-status" class="mb-2">Preparing export...</p>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small">Progress</span>
                        <span id="export-progress-percent" class="fw-bold">0%</span>
                    </div>
                    <div class="progress" style="height: 22px;">
                        <div id="export-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated"
                            role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                            aria-valuemax="100">0%</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="export-cancel-btn" class="btn btn-outline-secondary">Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let currentPage = 1;
            let currentFilters = {};
            let exportInProgress = false;
            let exportCancelled = false;
            let currentExportId = null;
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            const exportStartUrl = '{{ route('user-activity-export-start') }}';
            const exportChunkUrl = '{{ route('user-activity-export-chunk') }}';
            const exportCancelUrl = '{{ route('user-activity-export-cancel') }}';
            const exportDownloadUrlTemplate = '{{ route('user-activity-export-download', ['exportId' => 'EXPORT_ID']) }}';

            function exportModal() {
                return bootstrap.Modal.getOrCreateInstance(document.getElementById('exportProgressModal'));
            }

            function formatCount(value) {
                return Number(value).toLocaleString();
            }

            function setExportProgress(percent, status) {
                percent = Math.max(0, Math.min(100, parseInt(percent, 10) || 0));
                $('#export-progress-bar')
                    .css('width', percent + '%')
                    .attr('aria-valuenow', percent)
                    .text(percent + '%');
                $('#export-progress-percent').text(percent + '%');
                $('#export-progress-status').text(status);
            }

            function finishExport() {
                exportInProgress = false;
                currentExportId = null;
                $('#export-activities').prop('disabled', false);
                $('#export-cancel-btn').prop('disabled', false);
                exportModal().hide();
            }

            function cancelCurrentExport() {
                if (!exportInProgress) {
                    exportModal().hide();
                    return;
                }

                exportCancelled = true;
                $('#export-cancel-btn').prop('disabled', true);
                setExportProgress($('#export-progress-bar').attr('aria-valuenow') || 0, 'Cancelling...');

                if (currentExportId) {
                    $.post(exportCancelUrl, {
                        _token: csrfToken,
                        export_id: currentExportId
                    }).always(function() {
                        finishExport();
                    });
                    return;
                }

                finishExport();
            }

            function processExportChunk() {
                if (exportCancelled) {
                    finishExport();
                    return;
                }

                $.post(exportChunkUrl, {
                    _token: csrfToken,
                    export_id: currentExportId
                }).done(function(chunk) {
                    if (exportCancelled || chunk.status === 'cancelled') {
                        finishExport();
                        return;
                    }

                    setExportProgress(
                        chunk.percent,
                        formatCount(chunk.processed) + ' of ' + formatCount(chunk.total) + ' records'
                    );

                    if (chunk.done) {
                        setExportProgress(100, 'Download starting...');
                        const downloadUrl = exportDownloadUrlTemplate.replace('EXPORT_ID', currentExportId);
                        const iframe = $('<iframe>', { src: downloadUrl }).hide();
                        $('body').append(iframe);
                        setTimeout(function() {
                            iframe.remove();
                            finishExport();
                        }, 800);
                        return;
                    }

                    processExportChunk();
                }).fail(function(xhr) {
                    if (xhr.status === 409 || exportCancelled) {
                        finishExport();
                        return;
                    }
                    setExportProgress(0, 'Export failed. Please try again.');
                    setTimeout(finishExport, 1500);
                });
            }

            function startChunkedExport() {
                exportCancelled = false;
                exportInProgress = true;
                currentExportId = null;
                $('#export-activities').prop('disabled', true);
                $('#export-cancel-btn').prop('disabled', false);
                setExportProgress(0, 'Preparing export...');
                exportModal().show();

                const payload = Object.assign({ _token: csrfToken }, currentFilters);

                $.post(exportStartUrl, payload).done(function(start) {
                    if (exportCancelled) {
                        if (start.export_id) {
                            $.post(exportCancelUrl, {
                                _token: csrfToken,
                                export_id: start.export_id
                            });
                        }
                        finishExport();
                        return;
                    }

                    if (!start.total) {
                        setExportProgress(100, 'No records to export.');
                        setTimeout(finishExport, 1200);
                        return;
                    }

                    currentExportId = start.export_id;
                    setExportProgress(0, '0 of ' + formatCount(start.total) + ' records');
                    processExportChunk();
                }).fail(function() {
                    setExportProgress(0, 'Could not start export.');
                    setTimeout(finishExport, 1500);
                });
            }

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
                                <td class="notranslate" translate="no">${activity.user_name || '-'}</td>
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
                html += `<span class="mx-2">Page ${response.current_page} of ${response.last_page} (${response.total.toLocaleString()} total records)</span>`;
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

            $('#export-activities').on('click', function(e) {
                e.preventDefault();
                if (exportInProgress) {
                    return;
                }
                startChunkedExport();
            });

            $('#export-cancel-btn').on('click', function() {
                cancelCurrentExport();
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
