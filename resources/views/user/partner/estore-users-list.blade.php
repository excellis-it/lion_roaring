@extends('user.layouts.master')
@section('title')
    E-store Users List - {{ env('APP_NAME') }}
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
                                        {{-- <h3 class="mb-3">Partners List</h3> --}}
                                    </div>

                                </div>
                                <div class="row ">
                                    <div class="col-md-8">
                                        <h3 class="mb-3 float-left">Users List</h3>
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
                                                <th>Phone</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @include('user.partner.estore-users-table')

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

                $.ajax({
                    url: "{{ route('estore-users.fetch-data') }}",
                    data: {
                        page: page,
                        sortby: sort_by,
                        sorttype: sort_type,
                        query: query
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
