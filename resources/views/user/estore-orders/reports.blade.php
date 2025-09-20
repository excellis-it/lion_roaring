@extends('user.layouts.master')

@section('title')
    E-Store Sales Reports
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row mb-1">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">Sales Reports</h3>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('user.store-orders.list') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to Orders
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="report-filter-form" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Report Type</label>
                                    <select class="form-select" id="report-type">
                                        <option value="product">Product Sales</option>
                                        <option value="location">Sales by Location</option>
                                        <option value="monthly">Monthly Sales</option>
                                        <option value="yearly">Yearly Sales</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="start-date" value="{{ date('Y-m-01') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="end-date" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-sync"></i> Generate Report
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="mb-0" id="report-title">Report</h4>
                                {{-- <button type="button" class="btn btn-success" id="export-excel">
                                    <i class="fas fa-file-excel"></i> Export to Excel
                                </button> --}}
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Loading indicator -->
                            <div id="loading-indicator" class="text-center py-5 d-none">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading report data...</p>
                            </div>

                            <!-- Summary cards -->
                            <div class="row mb-4" id="summary-cards"></div>

                            <!-- Chart container -->
                            <div class="mb-4">
                                <canvas id="report-chart" height="300"></canvas>
                            </div>

                            <!-- Report data table -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="report-table">
                                    <thead id="report-table-head"></thead>
                                    <tbody id="report-table-body"></tbody>
                                    <tfoot id="report-table-foot"></tfoot>
                                </table>
                            </div>

                            <!-- Empty state -->
                            <div id="empty-state" class="text-center py-5 d-none">
                                <i class="fas fa-chart-bar fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No data available</h5>
                                <p class="text-muted">Try changing your filters or date range.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .summary-card {
            border-radius: 8px;
            box-shadow: 0 4px 6px rgb(52 48 48 / 28%);
            transition: transform 0.2s;
        }

        .summary-card:hover {
            transform: translateY(-5px);
        }

        .summary-card .card-body {
            padding: 1.5rem;
        }

        .summary-value {
            font-size: 1.8rem;
            font-weight: 700;
        }

        .summary-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .summary-icon {
            font-size: 2rem;
            opacity: 0.2;
            position: absolute;
            right: 15px;
            bottom: 10px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let reportChart = null;

        $(document).ready(function() {
            // Generate report on form submit
            $('#report-filter-form').on('submit', function(e) {
                e.preventDefault();
                generateReport();
            });

            // Generate initial report
            generateReport();

            // Export to Excel
            $('#export-excel').on('click', function() {
                exportReport();
            });
        });

        function generateReport() {
            const reportType = $('#report-type').val();
            const startDate = $('#start-date').val();
            const endDate = $('#end-date').val();

            // Update report title
            updateReportTitle(reportType);

            // Show loading indicator
            $('#loading-indicator').removeClass('d-none');
            $('#empty-state').addClass('d-none');
            $('#summary-cards').html('');

            // Clear previous chart
            if (reportChart) {
                reportChart.destroy();
            }

            // Fetch report data
            $.ajax({
                url: '{{ route('user.store-orders.fetch-report') }}',
                type: 'GET',
                data: {
                    report_type: reportType,
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    $('#loading-indicator').addClass('d-none');

                    if (response.data.items.length === 0) {
                        $('#empty-state').removeClass('d-none');
                        // hide report table
                        $('#report-table').addClass('d-none');
                        return;
                    } else {
                        $('#report-table').removeClass('d-none');
                    }

                    // Display summary cards
                    displaySummaryCards(response.data, reportType);

                    // Display chart
                    displayChart(response.data, reportType);

                    // Display table
                    displayTable(response.data, reportType);
                },
                error: function(xhr) {
                    $('#loading-indicator').addClass('d-none');
                    $('#empty-state').removeClass('d-none');
                    toastr.error('Failed to generate report: ' + (xhr.responseJSON?.error || 'Unknown error'));
                }
            });
        }

        function updateReportTitle(reportType) {
            let title = '';
            switch (reportType) {
                case 'product':
                    title = 'Product Sales Report';
                    break;
                case 'location':
                    title = 'Sales by Geographic Location';
                    break;
                case 'monthly':
                    title = 'Monthly Sales Report';
                    break;
                case 'yearly':
                    title = 'Yearly Sales Report';
                    break;
            }
            $('#report-title').text(title);
        }

        function displaySummaryCards(data, reportType) {
            const summaryContainer = $('#summary-cards');
            summaryContainer.html('');

            // Common summary cards
            const cards = [{
                    title: 'Total Revenue',
                    value: '$' + numberFormat(data.total_revenue),
                    icon: 'fa-dollar-sign',
                    color: 'success'
                },
                {
                    title: 'Total Orders',
                    value: numberFormat(data.total_orders),
                    icon: 'fa-shopping-cart',
                    color: 'primary'
                }
            ];

            // Report-specific cards
            switch (reportType) {
                case 'product':
                    cards.push({
                        title: 'Total Products',
                        value: data.items.length,
                        icon: 'fa-box',
                        color: 'info'
                    });
                    cards.push({
                        title: 'Total Quantity',
                        value: numberFormat(data.total_quantity),
                        icon: 'fa-boxes',
                        color: 'warning'
                    });
                    break;
                case 'location':
                    cards.push({
                        title: 'Total Locations',
                        value: data.total_locations,
                        icon: 'fa-map-marker-alt',
                        color: 'info'
                    });
                    break;
                case 'monthly':
                case 'yearly':
                    cards.push({
                        title: 'Total Periods',
                        value: data.periods_count,
                        icon: 'fa-calendar',
                        color: 'info'
                    });
                    break;
            }

            // Create and append cards
            cards.forEach(card => {
                const cardHtml = `
                    <div class="col-md-3 mb-4">
                        <div class="card summary-card bg-${card.color} bg-opacity-10 border-${card.color}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="summary-value text-${card.color}">${card.value}</div>
                                        <div class="summary-label">${card.title}</div>
                                    </div>
                                </div>
                                <i class="fas ${card.icon} summary-icon text-${card.color}"></i>
                            </div>
                        </div>
                    </div>
                `;
                summaryContainer.append(cardHtml);
            });
        }

        function displayChart(data, reportType) {
            const ctx = document.getElementById('report-chart').getContext('2d');

            let labels = [];
            let datasets = [];

            switch (reportType) {
                case 'product':
                    // Get top 10 products
                    const topProducts = data.items.slice(0, 10);
                    labels = topProducts.map(item => item.name);

                    datasets = [{
                            label: 'Revenue ($)',
                            data: topProducts.map(item => item.revenue),
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Quantity',
                            data: topProducts.map(item => item.quantity),
                            backgroundColor: 'rgba(255, 206, 86, 0.5)',
                            borderColor: 'rgba(255, 206, 86, 1)',
                            borderWidth: 1,
                            yAxisID: 'y1'
                        }
                    ];
                    break;

                case 'location':
                    // Get top 10 locations
                    const topLocations = data.items.slice(0, 10);
                    labels = topLocations.map(item => item.location);

                    datasets = [{
                            label: 'Revenue ($)',
                            data: topLocations.map(item => item.revenue),
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Orders',
                            data: topLocations.map(item => item.orders_count),
                            backgroundColor: 'rgba(255, 99, 132, 0.5)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1,
                            yAxisID: 'y1'
                        }
                    ];
                    break;

                case 'monthly':
                case 'yearly':
                    labels = data.items.map(item => item.period);

                    datasets = [{
                            label: 'Revenue ($)',
                            data: data.items.map(item => item.revenue),
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            type: 'bar'
                        },
                        {
                            label: 'Orders',
                            data: data.items.map(item => item.orders_count),
                            backgroundColor: 'rgba(255, 99, 132, 0.5)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 2,
                            type: 'line',
                            yAxisID: 'y1'
                        }
                    ];
                    break;
            }

            // Create chart
            reportChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Revenue ($)'
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false
                            },
                            title: {
                                display: true,
                                text: reportType === 'product' ? 'Quantity' : 'Orders'
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: $('#report-title').text()
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                }
            });
        }

        function displayTable(data, reportType) {
            const tableHead = $('#report-table-head');
            const tableBody = $('#report-table-body');
            const tableFoot = $('#report-table-foot');

            tableHead.html('');
            tableBody.html('');
            tableFoot.html('');

            let headers = [];
            let footerColumns = [];

            switch (reportType) {
                case 'product':
                    headers = ['#', 'Product', 'Quantity', 'Revenue', 'Orders'];

                    // Table header
                    tableHead.html(`
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th class="text-end">Quantity</th>
                            <th class="text-end">Revenue</th>
                            <th class="text-end">Orders</th>

                        </tr>
                    `);

                    // Table body
                    data.items.forEach((item, index) => {
                        const avgPrice = item.quantity > 0 ? item.revenue / item.quantity : 0;
                        tableBody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.name}</td>
                                <td class="text-end">${numberFormat(item.quantity)}</td>
                                <td class="text-end">$${numberFormat(item.revenue)}</td>
                                <td class="text-end">${numberFormat(item.orders_count)}</td>

                            </tr>
                        `);
                    });

                    // Table footer
                    tableFoot.html(`
                        <tr class="table-primary">
                            <th colspan="2">Total</th>
                            <th class="text-end">${numberFormat(data.total_quantity)}</th>
                            <th class="text-end">$${numberFormat(data.total_revenue)}</th>
                            <th class="text-end">${numberFormat(data.total_orders)}</th>

                        </tr>
                    `);
                    break;

                case 'location':
                    // Table header
                    tableHead.html(`
                        <tr>
                            <th>#</th>
                            <th>Location</th>
                            <th class="text-end">Orders</th>
                            <th class="text-end">Revenue</th>
                            <th class="text-end">Customers</th>

                        </tr>
                    `);

                    // Table body
                    data.items.forEach((item, index) => {
                        const avgOrderValue = item.orders_count > 0 ? item.revenue / item.orders_count : 0;
                        tableBody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.location}</td>
                                <td class="text-end">${numberFormat(item.orders_count)}</td>
                                <td class="text-end">$${numberFormat(item.revenue)}</td>
                                <td class="text-end">${numberFormat(item.customers)}</td>

                            </tr>
                        `);
                    });

                    // Table footer
                    const totalOrders = data.total_orders;
                    const totalAvgOrderValue = totalOrders > 0 ? data.total_revenue / totalOrders : 0;

                    tableFoot.html(`
                        <tr class="table-primary">
                            <th colspan="2">Total</th>
                            <th class="text-end">${numberFormat(totalOrders)}</th>
                            <th class="text-end">$${numberFormat(data.total_revenue)}</th>


                        </tr>
                    `);
                    break;

                case 'monthly':
                case 'yearly':
                    const periodLabel = reportType === 'monthly' ? 'Month' : 'Year';

                    // Table header
                    tableHead.html(`
                        <tr>
                            <th>#</th>
                            <th>${periodLabel}</th>
                            <th class="text-end">Orders</th>
                            <th class="text-end">Revenue</th>
                            <th class="text-end">Customers</th>

                        </tr>
                    `);

                    // Table body
                    data.items.forEach((item, index) => {
                        const avgOrderValue = item.orders_count > 0 ? item.revenue / item.orders_count : 0;
                        tableBody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.period}</td>
                                <td class="text-end">${numberFormat(item.orders_count)}</td>
                                <td class="text-end">$${numberFormat(item.revenue)}</td>
                                <td class="text-end">${numberFormat(item.customers_count)}</td>

                            </tr>
                        `);
                    });

                    // Table footer
                    const totalOrdersCount = data.total_orders;
                    const totalAvgOrder = totalOrdersCount > 0 ? data.total_revenue / totalOrdersCount : 0;

                    tableFoot.html(`
                        <tr class="table-primary">
                            <th colspan="2">Total</th>
                            <th class="text-end">${numberFormat(totalOrdersCount)}</th>
                            <th class="text-end">$${numberFormat(data.total_revenue)}</th>


                        </tr>
                    `);
                    break;
            }
        }

        function exportReport() {
            const reportType = $('#report-type').val();
            const startDate = $('#start-date').val();
            const endDate = $('#end-date').val();

            const url =
                `{{ route('user.store-orders.export-report') }}?report_type=${reportType}&start_date=${startDate}&end_date=${endDate}`;
            window.open(url, '_blank');
        }

        function numberFormat(number) {
            return new Intl.NumberFormat().format(parseFloat(number).toFixed(2));
        }
    </script>
@endpush
