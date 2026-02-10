@extends('user.layouts.master')
@section('title')
    Members List - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div class="container-fluid">
        <div class="bg_white_border">
            <form>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="row mb-3">
                                    <div class="col-md-10">
                                        <h2 class="mb-3">Members List</h2>
                                    </div>
                                    <div class="col-md-5 float-right text-end">
                                        @if (auth()->user()->can('Create Partners'))
                                            <a href="{{ route('partners.create') }}" class="btn btn-primary">+ Add
                                                Members</a>
                                        @endif
                                        <a href="javascript:void(0);" id="export-report" class="btn btn-primary">
                                            <i class="ti ti-download"></i> Export Report
                                        </a>
                                    </div>
                                </div>
                                <div class="row ">
                                    {{-- <div class="col-md-3">
                                        <select name="user_type_filter" id="user_type_filter" class="form-control">
                                            <option value="">All Types</option>
                                            <option value="Global">Global</option>
                                            <option value="Regional">Regional</option>
                                        </select>
                                    </div> --}}

                                    <div class="col-md-3">
                                        @if (auth()->user()->user_type == 'Regional')
                                            {{-- Regional users can only see their own country --}}
                                            <select name="country_filter" id="country_filter" class="form-control" disabled
                                                style="background-color: #e9ecef; cursor: not-allowed;">
                                                @foreach ($countries as $country)
                                                    @if ($country->id == auth()->user()->country)
                                                        <option value="{{ $country->id }}" selected>
                                                            {{ $country->name }} (Your Region)
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        @else
                                            {{-- Global and other users can filter by any country --}}
                                            <select name="country_filter" id="country_filter" class="form-control">
                                                <option value="">All Countries</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}"
                                                        {{ $country_id == $country->id ? 'selected' : '' }}>
                                                        {{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>

                                    <div class="col-md-3">
                                        <select name="has_agreement" id="has_agreement" class="form-control">
                                            <option value="">Registration Agreement (All)</option>
                                            <option value="1" {{ $has_agreement == '1' ? 'selected' : '' }}>Yes
                                            </option>
                                            <option value="0" {{ $has_agreement == '0' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <a href="{{ route('partners.reset-filters') }}" class="btn btn-primary">Reset
                                            Filters</a>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="search-field float-right">
                                            <input type="text" name="search" id="search" placeholder="search..."
                                                required class="form-control" value="{{ $query }}">
                                            <button class="submit_search" id="search-button"> <span class=""><i
                                                        class="fa fa-search"></i></span></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive card card-body shadow p-0 mt-2">
                                    <table
                                        class="table align-middle color_body_text table-light table-borderless member-table">
                                        <thead class="bg-light ">
                                            <tr>
                                                <th class="p-3"></th>
                                                <th class="sorting p-3" data-tippy-content="Sort by Email"
                                                    data-sorting_type="desc" data-column_name="email"
                                                    style="cursor: pointer"> Email ID <span id="email_icon"></span></th>
                                                <th class="sorting p-3" data-tippy-content="Sort by Name"
                                                    data-sorting_type="desc" data-column_name="name"
                                                    style="cursor: pointer"> Name
                                                    {{-- <span id="name_icon"><i
                                                            class="fa fa-arrow-down"></i></span> --}}
                                                </th>
                                                <th class="sorting p-3" data-tippy-content="Sort by User Type"
                                                    data-sorting_type="desc" data-column_name="user_type"
                                                    style="cursor: pointer"> User Type
                                                    {{-- <span id="name_icon"><i
                                                            class="fa fa-arrow-down"></i></span> --}}
                                                </th>
                                                <th class="sorting p-3" data-tippy-content="Sort by Country"
                                                    data-sorting_type="desc" data-column_name="country"
                                                    style="cursor: pointer"> Country
                                                </th>
                                                <th class="p-3">Role</th>
                                                <th class="p-3">House Of Ecclesia</th>
                                                <th class="p-3 text-center">Registration Agreement</th>
                                                <th class="p-3">Manage Warehouses</th>

                                                {{-- <th>
                                                    Ecclesia
                                                </th>
                                                <th class="sorting" data-tippy-content="Sort by User Name"
                                                    data-sorting_type="desc" data-column_name="user_name"
                                                    style="cursor: pointer">User Name<span id="user_name_icon"></span></th>

                                                <th class="sorting" data-tippy-content="Sort by Phone"
                                                    data-sorting_type="desc" data-column_name="phone"
                                                    style="cursor: pointer"> Phone <span id="phone_icon"></span></th>
                                                <th class="sorting" data-tippy-content="Sort by Address"
                                                    data-sorting_type="desc" data-column_name="address"
                                                    style="cursor: pointer"> Address <span id="address_icon"></span></th> --}}


                                                @if (auth()->user()->can('Edit Partners'))
                                                    <th class="p-3">Status/Approve</th>
                                                @endif
                                                @if (auth()->user()->can('Edit Partners') ||
                                                        auth()->user()->can('Delete Partners') ||
                                                        auth()->user()->can('View Partners'))
                                                    <th class="p-3"></th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @include('user.partner.table', ['partners' => $partners])
                                        </tbody>

                                    </table>
                                    <input type="hidden" name="hidden_page" id="hidden_page"
                                        value="{{ $partners->currentPage() }}" />
                                    <input type="hidden" name="hidden_column_name" id="hidden_column_name"
                                        value="{{ $sort_by }}" />
                                    <input type="hidden" name="hidden_sort_type" id="hidden_sort_type"
                                        value="{{ $sort_type }}" />
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Registration Agreement Modal -->
    <div class="modal fade" id="agreementModal" tabindex="-1" aria-labelledby="agreementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header  text-white py-3" style="background: #643271; color:#fff !important ">
                    <h5 class="modal-title d-flex align-items-center fw-bold" id="agreementModalLabel">
                        <i class="ti ti-file-text fs-6 me-2 text-white"></i>
                        Registration Agreement Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="agreement-content">
                        <!-- Info Grid -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border-start border-4 border-primary shadow-sm h-100">
                                    <label class="text-muted small text-uppercase fw-bold mb-2 d-block">
                                        <i class="ti ti-user me-1 text-primary"></i> Signer Name
                                    </label>
                                    <p id="modal-signer-name" class="h6 mb-0 text-dark fw-bold">-</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border-start border-4 border-primary shadow-sm h-100">
                                    <label class="text-muted small text-uppercase fw-bold mb-2 d-block">
                                        <i class="ti ti-calendar me-1 text-primary"></i> Signed Date
                                    </label>
                                    <p id="modal-signed-at" class="h6 mb-0 text-dark fw-bold">-</p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="p-3 bg-light rounded-3 border-start border-4 border-primary shadow-sm h-100">
                                    <label class="text-muted small text-uppercase fw-bold mb-2 d-block">
                                        <i class="ti ti-world me-1 text-primary"></i> Country
                                    </label>
                                    <p id="modal-country-code" class="h6 mb-0 text-dark fw-bold">-</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Section -->
                        <div class="text-center py-4 bg-light rounded-3 border shadow-sm mt-3"
                            style="border-style: dashed !important;">
                            <h6 class="text-muted mb-3 fw-bold"><i class="ti ti-file-type-pdf me-1 text-danger"></i>
                                Agreement Document</h6>
                            <div class="d-flex justify-content-center gap-3">
                                <a id="modal-view-pdf" href="#" target="_blank"
                                    class="btn btn-primary d-flex align-items-center px-4 fw-bold d-none">
                                    <i class="ti ti-eye fs-5 me-2"></i> View PDF
                                </a>
                                <a id="modal-download-pdf" href="#" download
                                    class="btn btn-primary d-flex align-items-center px-4 fw-bold d-none">
                                    <i class="ti ti-download fs-5 me-2"></i> Download PDF
                                </a>
                            </div>

                            <!-- No PDF found message -->
                            <div id="modal-no-pdf" class="d-none px-3">
                                <div class="alert alert-warning mb-0 d-inline-block border-2">
                                    <i class="ti ti-alert-triangle text-warning fs-5 me-1"></i>
                                    Registration Agreement PDF file not found on the server.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div id="modal-loading" class="text-center d-none py-5">
                        <div class="spinner-grow text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted fw-bold fs-5">Fetching Agreement Details...</p>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-primary px-4 fw-bold" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '#delete', function(e) {
            swal({
                    title: "Are you sure?",
                    text: "To delete this Member.",
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
    <script>
        $(document).ready(function() {
            $(document).on('change', '.toggle-class', function() {
                var status = $(this).prop('checked') == true ? 1 : 0;
                var user_id = $(this).data('id');
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: '{{ route('partners.change-status') }}',
                    data: {
                        'status': status,
                        'user_id': user_id
                    },
                    success: function(resp) {
                        $('#loading').removeClass('loading');
                        $('#loading-content').removeClass('loading-content');
                        toastr.success(resp.success);
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {

            function clear_icon() {
                $('#name_icon').html('');
                $('#email_icon').html('');
                $('#phone_icon').html('');
                $('#address_icon').html('');
                $('#user_name_icon').html('');
            }

            function fetch_data(page, sort_type, sort_by, query, is_paginate_click = 0) {
                if (query == '' || query == null) {
                    page = page;
                } else {
                    page = null;
                }

                if (is_paginate_click == 1) {
                    page = $('#hidden_page').val();
                }

                var country_id = $('#country_filter').val();
                var has_agreement = $('#has_agreement').val();

                $.ajax({
                    url: "{{ route('partners.fetch-data') }}",
                    data: {
                        page: page,
                        sortby: sort_by,
                        sorttype: sort_type,
                        query: query,
                        country_id: country_id,
                        has_agreement: has_agreement
                    },
                    success: function(data) {
                        $('tbody').html(data.data);
                    }
                });
            }

            $(document).on('keyup', '#search', function() {
                var query = $('#search').val();
                var column_name = $('#hidden_column_name').val();
                var sort_type = $('#hidden_sort_type').val();
                var page = $('#hidden_page').val();
                fetch_data(page, sort_type, column_name, query, 0);
            });

            $(document).on('change', '#has_agreement', function() {
                var query = $('#search').val();
                var column_name = $('#hidden_column_name').val();
                var sort_type = $('#hidden_sort_type').val();
                var page = $('#hidden_page').val();
                fetch_data(page, sort_type, column_name, query, 0);
            });

            $(document).on('change', '#country_filter', function() {
                var query = $('#search').val();
                var column_name = $('#hidden_column_name').val();
                var sort_type = $('#hidden_sort_type').val();
                var page = $('#hidden_page').val();
                fetch_data(page, sort_type, column_name, query, 0);
            });

            $(document).on('click', '.sorting', function() {
                var column_name = $(this).data('column_name');
                var order_type = $(this).data('sorting_type');
                var reverse_order = '';
                if (order_type == 'asc') {
                    $(this).data('sorting_type', 'desc');
                    reverse_order = 'desc';
                    clear_icon();
                    $('#' + column_name + '_icon').html('<i class="fa fa-arrow-down"></i>');
                }
                if (order_type == 'desc') {
                    $(this).data('sorting_type', 'asc');
                    reverse_order = 'asc';
                    clear_icon();
                    $('#' + column_name + '_icon').html('<i class="fa fa-arrow-up"></i>');
                }
                $('#hidden_column_name').val(column_name);
                $('#hidden_sort_type').val(reverse_order);
                var page = $('#hidden_page').val();
                var query = $('#search').val();
                fetch_data(page, reverse_order, column_name, query, 0);
            });

            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                $('#hidden_page').val(page);
                var column_name = $('#hidden_column_name').val();
                var sort_type = $('#hidden_sort_type').val();
                var query = $('#search').val();

                $('li').removeClass('active');
                $(this).parent().addClass('active');
                fetch_data(page, sort_type, column_name, query, 1);
            });

            $(document).on('click', '#export-report', function() {
                var query = $('#search').val();
                var country_id = $('#country_filter').val();
                var has_agreement = $('#has_agreement').val();
                var sort_by = $('#hidden_column_name').val();
                var sort_type = $('#hidden_sort_type').val();

                var url = "{{ route('partners.export-report') }}";
                url += "?query=" + encodeURIComponent(query || '') +
                    "&country_id=" + encodeURIComponent(country_id || '') +
                    "&has_agreement=" + encodeURIComponent(has_agreement || '') +
                    "&sortby=" + encodeURIComponent(sort_by || '') +
                    "&sorttype=" + encodeURIComponent(sort_type || '');

                window.open(url, '_blank');
            });

            $(document).on('click', '.view-agreement', function() {
                var userId = $(this).data('user_id');
                var modal = new bootstrap.Modal(document.getElementById('agreementModal'));

                // Reset modal content
                $('#modal-signer-name').text('-');
                $('#modal-signed-at').text('-');
                $('#modal-country-code').text('-');
                $('#modal-view-pdf').addClass('d-none');
                $('#modal-download-pdf').addClass('d-none');
                $('#modal-no-pdf').addClass('d-none');
                $('#agreement-content').addClass('d-none');
                $('#modal-loading').removeClass('d-none');

                modal.show();

                $.ajax({
                    url: "{{ route('partners.agreement-details') }}",
                    type: "GET",
                    data: {
                        user_id: userId
                    },
                    success: function(response) {
                        $('#modal-loading').addClass('d-none');
                        $('#agreement-content').removeClass('d-none');

                        if (response.success) {
                            $('#modal-signer-name').text(response.data.signer_name || '-');
                            $('#modal-signed-at').text(response.data.signed_at || '-');
                            $('#modal-country-code').text(response.data.country_code || '-');

                            if (response.data.pdf_exists) {
                                $('#modal-view-pdf').attr('href', response.data.pdf_url)
                                    .removeClass('d-none');
                                $('#modal-download-pdf').attr('href', response.data.pdf_url)
                                    .removeClass('d-none');
                            } else {
                                $('#modal-no-pdf').removeClass('d-none');
                            }
                        } else {
                            alert(response.message);
                            modal.hide();
                        }
                    },
                    error: function() {
                        $('#modal-loading').addClass('d-none');
                        alert('Something went wrong. Please try again.');
                        modal.hide();
                    }
                });
            });

        });
    </script>
@endpush
