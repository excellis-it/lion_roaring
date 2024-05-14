@extends('admin.layouts.master')
@section('title')
    All Donations Details - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <style>
        .dataTables_filter {
            margin-bottom: 10px !important;
        }
    </style>
@endpush
@section('head')
    All Donations Details
@endsection
@section('create_button')
@endsection
@section('content')
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div class="main-content">
        <div class="inner_page">

            <div class="card table_sec stuff-list-table">
                <div class="row justify-content-end">
                    <div class="col-md-6">
                        <div class="row g-1 justify-content-end">
                            <div class="col-md-8 pr-0">
                                <div class="search-field prod-search">
                                    <input type="text" name="search" id="search" placeholder="search..." required
                                        class="form-control">
                                    <a href="javascript:void(0)" class="prod-search-icon"><i
                                            class="ph ph-magnifying-glass"></i></a>
                                </div>
                            </div>
                            {{-- <div class="col-md-3 pl-0 ml-2">
                                <button class="btn btn-primary button-search" id="search-button"> <span class=""><i
                                            class="ph ph-magnifying-glass"></i></span> Search</button>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <div class="table-responsive" id="contacts-data">
                    <table class="table table-bordered" class="display">
                        <thead>
                            <tr>
                                <th>Id </th>
                                {{-- transaction_id --}}
                                <th class="sorting" data-sorting_type="asc" data-column_name="transaction_id"
                                    style="cursor: pointer" data-tippy-content="Sort by transaction_id">Transaction ID<span
                                        id="transaction_id_icon"></span></th>
                                        {{-- amount --}}
                                <th class="sorting" data-sorting_type="asc" data-column_name="donation_amount"
                                style="cursor: pointer" data-tippy-content="Sort by donation amount">Donation Amount<span
                                    id="amount_icon"></span></th>

                                <th class="sorting" data-sorting_type="asc" data-column_name="first_name"
                                    style="cursor: pointer" data-tippy-content="Sort by Name">
                                    Name<span id="first_name_icon"></span></th>

                                <th class="sorting" data-sorting_type="asc" data-column_name="email" style="cursor: pointer"
                                    data-tippy-content="Sort by email">
                                    Email<span id="email_icon"></span></th>
                                <th class="sorting" data-sorting_type="asc" data-column_name="address"
                                    style="cursor: pointer" data-tippy-content="Sort by address">Address<span
                                        id="address_icon"></span></th>
                                {{-- city --}}
                                <th class="sorting" data-sorting_type="asc" data-column_name="city" style="cursor: pointer"
                                    data-tippy-content="Sort by city">City<span id="city_icon"></span></th>
                                {{-- state --}}

                                <th class="sorting" data-sorting_type="asc" data-column_name="state" style="cursor: pointer"
                                    data-tippy-content="Sort by state">State<span id="state_icon"></span></th>
                                {{-- postcode --}}
                                <th class="sorting" data-sorting_type="asc" data-column_name="postcode"
                                    style="cursor: pointer" data-tippy-content="Sort by postcode">Postcode<span
                                        id="postcode_icon"></span></th>
                                {{-- country --}}
                                <th class="sorting" data-sorting_type="asc" data-column_name="country"
                                    style="cursor: pointer" data-tippy-content="Sort by country">Country<span
                                        id="country_icon"></span></th>


                                {{-- payment_status --}}
                                <th class="sorting" data-sorting_type="asc" data-column_name="payment_status"
                                    style="cursor: pointer" data-tippy-content="Sort by payment_status">Payment Status<span
                                        id="payment_status_icon"></span></th>
                                {{-- created_at --}}
                                <th class="sorting" data-sorting_type="asc" data-column_name="created_at"
                                    style="cursor: pointer" data-tippy-content="Sort by created_at">Created At<span
                                        id="created_at_icon"></span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @include('admin.donations.table')

                        </tbody>
                    </table>
                    <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
                    <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
                    <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="desc" />
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
                    text: "To delete this contact.",
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

            function clear_icon() {
                $('#id_icon').html('');
                $('#first_name_icon').html('');
                $('#phone_icon').html('');
                $('#email_icon').html('');
                $('#address_icon').html('');
                $('#city_icon').html('');
                $('#state_icon').html('');
                $('#postcode_icon').html('');
                $('#country_icon').html('');
                $('#amount_icon').html('');
                $('#transaction_id_icon').html('');
                $('#payment_status_icon').html('');
                $('#created_at_icon').html('');
            }

            function fetch_data(page, sort_type, sort_by, query) {
                $.ajax({
                    url: "{{ route('donations.fetch-data') }}",
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
                        '<span class="ph ph-caret-down"></span>');
                }
                if (order_type == 'desc') {
                    $(this).data('sorting_type', 'asc');
                    reverse_order = 'asc';
                    clear_icon();
                    $('#' + column_name + '_icon').html(
                        '<span class="ph ph-caret-up"></span>');
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
    {{-- trippy cdn link --}}
    <script src="https://unpkg.com/popper.js@1"></script>
    <script src="https://unpkg.com/tippy.js@5"></script>
    {{-- trippy --}}
    <script>
        tippy('[data-tippy-content]', {
            allowHTML: true,
            placement: 'bottom',
            theme: 'light-theme',
        });
    </script>
@endpush
