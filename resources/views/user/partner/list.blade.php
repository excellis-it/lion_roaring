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
                                        <h3 class="mb-3">Members List</h3>
                                    </div>
                                    <div class="col-md-2 float-right">
                                        @if (auth()->user()->can('Create Partners'))
                                            <a href="{{ route('partners.create') }}" class="btn btn-primary w-100">+ Add
                                                Members</a>
                                        @endif

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
                                        @if (auth()->user()->user_type == 'Global')
                                            <select name="country_filter" id="country_filter" class="form-control">
                                                <option value="">All Countries</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>

                                    <div class="col-md-5">
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="search-field float-right">
                                            <input type="text" name="search" id="search" placeholder="search..."
                                                required class="form-control">
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
                                    <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
                                    <input type="hidden" name="hidden_column_name" id="hidden_column_name"
                                        value="id" />
                                    <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="desc" />
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

                // if query empty or null then set page no else set page no to null
                if (query == '' || query == null) {
                    page = page;
                } else {
                    page = null;
                }

                if (is_paginate_click == 1) {

                    page = $('#hidden_page').val();
                }

                // var user_type = $('#user_type_filter').val();
                var country_id = $('#country_filter').val();

                $.ajax({
                    url: "{{ route('partners.fetch-data') }}",
                    data: {
                        page: page,
                        sortby: sort_by,
                        sorttype: sort_type,
                        query: query,
                        // user_type: user_type,
                        country_id: country_id
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

            // $(document).on('change', '#user_type_filter', function() {
            //     var query = $('#search').val();
            //     var column_name = $('#hidden_column_name').val();
            //     var sort_type = $('#hidden_sort_type').val();
            //     var page = $('#hidden_page').val();
            //     fetch_data(page, sort_type, column_name, query, 0);
            // });

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
                    $('#' + column_name + '_icon').html(
                        '<i class="fa fa-arrow-down"></i>');
                }
                if (order_type == 'desc') {
                    $(this).data('sorting_type', 'asc');
                    reverse_order = 'asc';
                    clear_icon();
                    $('#' + column_name + '_icon').html(
                        '<i class="fa fa-arrow-up"></i>');
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

        });
    </script>
@endpush
