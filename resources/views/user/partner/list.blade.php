@extends('user.layouts.master')
@section('title')
    Partners List - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <form>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card w-100">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="row mb-3">
                                            <div class="col-md-10">
                                                {{-- <h3 class="mb-3">Partners List</h3> --}}
                                            </div>
                                            <div class="col-md-2 float-right">
                                                <a href="{{ route('partners.create') }}" class="btn btn-primary w-100">+ Add Partnet</a>
                                            </div>
                                        </div>
                                        <div class="row ">
                                            <div class="col-md-8">
                                                <h3 class="mb-3 float-left">Partners List</h3>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="search-field float-right">
                                                    <input type="text" name="search" id="search" placeholder="search..." required
                                                    class="form-control">
                                                    <button class="submit_search" id="search-button"> <span
                                                            class=""><i class="fa fa-search"></i></span></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table align-middle bg-white color_body_text">
                                                <thead class="color_head">
                                                    <tr>
                                                        <th>ID </th>
                                                        <th class="sorting" data-tippy-content="Sort by Name" data-sorting_type="desc"
                                                        data-column_name="name" style="cursor: pointer"> Name<span id="name_icon"><i class="fa fa-arrow-down"></i></span></th>
                                                        <th class="sorting" data-tippy-content="Sort by User Name" data-sorting_type="desc"
                                                        data-column_name="user_name" style="cursor: pointer">User Name<span id="user_name_icon"></span></th>
                                                        <th class="sorting" data-tippy-content="Sort by Email" data-sorting_type="desc"
                                                            data-column_name="email" style="cursor: pointer"> Email <span id="email_icon"></span></th>
                                                        <th class="sorting" data-tippy-content="Sort by Phone" data-sorting_type="desc"
                                                            data-column_name="phone" style="cursor: pointer"> Phone <span id="phone_icon"></span></th>
                                                        <th class="sorting" data-tippy-content="Sort by Address" data-sorting_type="desc"
                                                            data-column_name="address" style="cursor: pointer"> Address <span id="address_icon"></span></th>
                                                            <th>Role</th>
                                                        <th>Status</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @include('user.partner.table', ['partners' => $partners])
                                                </tbody>
                                                <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
                                                <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
                                                <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
                                            </table>
                                        </div>
                                    </div>
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
                text: "To delete this Role.",
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
            url: '{{ route('partners.change-status') }}',
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
                url: "{{ route('partners.fetch-data') }}",
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
