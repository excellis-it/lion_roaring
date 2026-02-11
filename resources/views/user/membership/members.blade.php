@extends('user.layouts.master')
@section('title', 'Membership Members')
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0">Membership Members</h3>
            </div>

            <!-- Filters -->
            <div class="card border-0 shadow-sm mb-4"
                style="border-radius: 15px; background: #ffffff; border: 1px solid #eee !important;">
                <div class="card-body p-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-2">
                                <i class="fa fa-search me-1"></i> Search
                            </label>
                            <div class="input-group  rounded-3 overflow-hidden" style="background: #fcfcfc;">
                                <input type="text" id="search" class="form-control  bg-transparent py-2"
                                    placeholder="Name, Email, Promo..." style="box-shadow: none;">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-2">
                                <i class="fa fa-credit-card me-1"></i> Method
                            </label>
                            <select id="method" class="form-select  rounded-3 bg-transparent py-2"
                                style="background: #fcfcfc; box-shadow: none;">
                                <option value="">All Methods</option>
                                <option value="amount">Amount</option>
                                <option value="token">Token</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-2">
                                <i class="fa fa-calendar me-1"></i> From
                            </label>
                            <input type="date" id="date_from" class="form-control  rounded-3 bg-transparent py-2"
                                style="background: #fcfcfc; box-shadow: none;">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-2">
                                <i class="fa fa-calendar me-1"></i> To
                            </label>
                            <input type="date" id="date_to" class="form-control  rounded-3 bg-transparent py-2"
                                style="background: #fcfcfc; box-shadow: none;">
                        </div>
                        <div class="col-lg-3 col-md-12 d-flex gap-2 justify-content-end">
                            <button id="reset-filters" class="btn px-4 rounded-3 fw-bold transition-all"
                                style="background: #f8f9fa; color: #444; border: 1px solid #ddd;">
                                <i class="fa fa-undo me-1"></i> Reset
                            </button>
                            <button id="apply-filters" class="btn px-4 rounded-3 fw-bold transition-all shadow-sm"
                                style="background: #6f42c1; color: white;">
                                <i class="fa fa-filter me-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="members-container">
                @include('user.membership.partials.members_table')
            </div>
        </div>
    </div>

    <style>
        #members-container.loading {
            opacity: 0.5;
            pointer-events: none;
            position: relative;
        }

        #members-container.loading::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 40px;
            height: 40px;
            margin: -20px 0 0 -20px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #6f42c1;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .transition-all:hover {
            transform: translateY(-1px);
            filter: brightness(0.95);
        }

        .transition-all:active {
            transform: translateY(0);
        }
    </style>

    @push('scripts')
        <script>
            $(document).ready(function() {
                let currentPage = 1;

                function loadMembers() {
                    const data = {
                        search: $('#search').val(),
                        method: $('#method').val(),
                        date_from: $('#date_from').val(),
                        date_to: $('#date_to').val(),
                        page: currentPage
                    };

                    $('#members-container').addClass('loading');

                    $.ajax({
                        url: '{{ route('user.membership.members') }}',
                        type: 'GET',
                        data: data,
                        success: function(response) {
                            $('#members-container').removeClass('loading').html(response.html);
                        },
                        error: function() {
                            $('#members-container').removeClass('loading');
                            alert('Error loading members.');
                        }
                    });
                }

                $('#apply-filters').on('click', function() {
                    currentPage = 1;
                    loadMembers();
                });

                $('#reset-filters').on('click', function() {
                    $('#search').val('');
                    $('#method').val('');
                    $('#date_from').val('');
                    $('#date_to').val('');
                    currentPage = 1;
                    loadMembers();
                });

                // Search on typing with debounce
                let searchTimer;
                $('#search').on('input', function() {
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(() => {
                        currentPage = 1;
                        loadMembers();
                    }, 500);
                });

                // Filter on change
                $('#method, #date_from, #date_to').on('change', function() {
                    currentPage = 1;
                    loadMembers();
                });

                // Pagination
                $(document).on('click', '.pagination a', function(e) {
                    e.preventDefault();
                    const url = $(this).attr('href');
                    if (url) {
                        const urlParams = new URLSearchParams(url.split('?')[1]);
                        currentPage = urlParams.get('page') || 1;
                        loadMembers();
                        $('html, body').animate({
                            scrollTop: 0
                        }, 'slow');
                    }
                });
            });
        </script>
    @endpush
@endsection
