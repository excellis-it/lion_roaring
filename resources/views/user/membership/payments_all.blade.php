@extends('user.layouts.master')
@section('title', 'All Membership Payments')
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row mb-3">
                <div class="col-md-12">
                    <h3 class="mb-3">All Membership Payments</h3>
                </div>
            </div>

            {{-- Filters Section --}}
            <div class="card mb-3" style="border: 1px solid #dee2e6; border-radius: 8px;">
                <div class="card-body">
                    <div class="row g-3">
                        {{-- Search --}}
                        <div class="col-md-3">
                            <label class="form-label" style="font-weight: 600; font-size: 14px;">Search</label>
                            <input type="text" id="search" class="form-control"
                                placeholder="User, email, transaction, promo..." style="border-radius: 6px;">
                        </div>

                        {{-- Payment Status Filter --}}
                        <div class="col-md-2">
                            <label class="form-label" style="font-weight: 600; font-size: 14px;">Status</label>
                            <select id="status" class="form-select" style="border-radius: 6px;">
                                <option value="">All Status</option>
                                <option value="Success">Success</option>
                                <option value="Failed">Failed</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>

                        {{-- Payment Method Filter --}}
                        <div class="col-md-2">
                            <label class="form-label" style="font-weight: 600; font-size: 14px;">Method</label>
                            <select id="method" class="form-select" style="border-radius: 6px;">
                                <option value="">All Methods</option>
                                <option value="Stripe">Stripe</option>
                                <option value="Manual">Manual</option>
                            </select>
                        </div>

                        {{-- Promo Code Filter --}}
                        <div class="col-md-2">
                            <label class="form-label" style="font-weight: 600; font-size: 14px;">Promo Code</label>
                            <select id="has_promo" class="form-select" style="border-radius: 6px;">
                                <option value="">All Payments</option>
                                <option value="yes">With Promo</option>
                                <option value="no">Without Promo</option>
                            </select>
                        </div>

                        {{-- Date From --}}
                        <div class="col-md-3">
                            <label class="form-label" style="font-weight: 600; font-size: 14px;">Date Range</label>
                            <div class="d-flex gap-2">
                                <input type="date" id="date_from" class="form-control" placeholder="From"
                                    style="border-radius: 6px;">
                                <input type="date" id="date_to" class="form-control" placeholder="To"
                                    style="border-radius: 6px;">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="button" id="apply-filters" class="btn btn-primary"
                                style="background: #643271; border: none; padding: 8px 20px; border-radius: 6px;">
                                <i class="fa fa-filter"></i> Apply Filters
                            </button>
                            <button type="button" id="reset-filters" class="btn btn-outline-secondary"
                                style="padding: 8px 20px; border-radius: 6px;">
                                <i class="fa fa-redo"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payments Table --}}
            <div id="payments-table">
                @include('user.membership.partials.payments_table', ['payments' => $payments])
            </div>

            {{-- Pagination --}}
            <div id="pagination-container">
                @include('user.membership.partials.pagination', ['payments' => $payments])
            </div>
        </div>
    </div>

    <style>
        .form-label {
            margin-bottom: 6px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #643271;
            box-shadow: 0 0 0 0.2rem rgba(100, 50, 113, 0.25);
        }

        #payments-table {
            position: relative;
        }

        #payments-table.loading {
            opacity: 0.5;
            pointer-events: none;
        }

        #payments-table.loading::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 4px solid #f3f3f3;
            border-top: 4px solid #643271;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }
    </style>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let currentPage = 1;

            // Apply filters
            $('#apply-filters').on('click', function() {
                currentPage = 1;
                loadPayments();
            });

            // Reset filters
            $('#reset-filters').on('click', function() {
                $('#search').val('');
                $('#status').val('');
                $('#method').val('');
                $('#has_promo').val('');
                $('#date_from').val('');
                $('#date_to').val('');
                currentPage = 1;
                loadPayments();
            });

            // Search on enter
            $('#search').on('keypress', function(e) {
                if (e.which === 13) {
                    currentPage = 1;
                    loadPayments();
                }
            });

            // Pagination click handler
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                if (url) {
                    const page = new URL(url).searchParams.get('page');
                    if (page) {
                        currentPage = page;
                        loadPayments();
                    }
                }
            });

            function loadPayments() {
                const filters = {
                    search: $('#search').val(),
                    status: $('#status').val(),
                    method: $('#method').val(),
                    has_promo: $('#has_promo').val(),
                    date_from: $('#date_from').val(),
                    date_to: $('#date_to').val(),
                    page: currentPage
                };

                $('#payments-table').addClass('loading');

                $.ajax({
                    url: '{{ route('user.membership.payments') }}',
                    type: 'GET',
                    data: filters,
                    success: function(response) {
                        $('#payments-table').removeClass('loading').html(response.html);
                        $('#pagination-container').html(response.pagination);
                    },
                    error: function(xhr) {
                        $('#payments-table').removeClass('loading');
                        alert('Error loading payments. Please try again.');
                    }
                });
            }
        });
    </script>
@endpush
