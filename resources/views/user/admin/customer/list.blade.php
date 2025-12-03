@extends('user.layouts.master')
@section('title')
    All Customer Details - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <style>
        .dataTables_filter {
            margin-bottom: 10px !important;
        }
    </style>
@endpush

@section('content')
    <section id="loading">
        <div id="loading-content"></div>
    </section>
     <div class="container-fluid">
         <div class="bg_white_border">

            <div class="card table_sec stuff-list-table">
                <div class="row justify-content-end">
                    <div class="col-md-6">
                        <div class="row g-1 justify-content-end">
                            <div class="col-md-8 pr-0">
                                <div class="search-field">
                                     <input type="text" name="search" id="search" placeholder="search..."
                                                required="" class="form-control rounded_search">
                                     <button class="submit_search" id="search-button"> <span class=""><i
                                                        class="fa fa-search"></i></span></button>
                                </div>
                            </div>
                            {{-- <div class="col-md-3 pl-0 ml-2">
                                <button class="btn btn-primary button-search" id="search-button"> <span class=""><i
                                            class="fa fa-search"></i></span> Search</button>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle bg-white color_body_text" id="myTable" class="display">
                       <thead class="color_head">
                                            <tr class="header-row">
                                <th class="sorting" data-tippy-content="Sort by Name" data-sorting_type="desc"
                                data-column_name="name" style="cursor: pointer"> Name<span id="name_icon"><i class="ph ph-caret-down"></i></span></th>
                                <th class="sorting" data-tippy-content="Sort by User Name" data-sorting_type="desc"
                                data-column_name="user_name" style="cursor: pointer">User Name<span id="user_name_icon"></span></th>
                                <th class="sorting" data-tippy-content="Sort by Email" data-sorting_type="desc"
                                    data-column_name="email" style="cursor: pointer"> Email <span id="email_icon"></span></th>
                                <th class="sorting" data-tippy-content="Sort by Phone" data-sorting_type="desc"
                                    data-column_name="phone" style="cursor: pointer"> Phone <span id="phone_icon"></span></th>
                                <th class="sorting" data-tippy-content="Sort by Address" data-sorting_type="desc"
                                    data-column_name="address" style="cursor: pointer"> Address <span id="address_icon"></span></th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @include('user.admin.customer.table')

                        </tbody>
                    </table>
                    <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
                    <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
                    <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
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
                    text: "To delete this customer.",
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
        $('.toggle-class').change(function() {
            var status = $(this).prop('checked') == true ? 1 : 0;
            var user_id = $(this).data('id');

            $.ajax({
                type: "GET",
                dataType: "json",
                url: '{{ route('customers.change-status') }}',
                data: {
                    'status': status,
                    'user_id': user_id
                },
                success: function(resp) {
                    console.log(resp.success)
                }
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

            function fetch_data(page, sort_type, sort_by, query) {
                $.ajax({
                    url: "{{ route('customers.fetch-data') }}",
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
                fetch_data(page, sort_type, column_name, query);
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
                        '<i class="ph ph-caret-down"></i>');
                }
                if (order_type == 'desc') {
                    $(this).data('sorting_type', 'asc');
                    reverse_order = 'asc';
                    clear_icon();
                    $('#' + column_name + '_icon').html(
                        '<i class="ph ph-caret-up"></i>');
                }
                $('#hidden_column_name').val(column_name);
                $('#hidden_sort_type').val(reverse_order);
                var page = $('#hidden_page').val();
                var query = $('#search').val();
                fetch_data(page, reverse_order, column_name, query);
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
                fetch_data(page, sort_type, column_name, query);
            });

        });
    </script>
@endpush
