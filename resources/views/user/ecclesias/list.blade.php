@extends('user.layouts.master')
@section('title')
    Ecclesias List - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <form>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="row mb-3">
                                    <div class="col-md-10">
                                        {{-- <h3 class="mb-3">Ecclesias List</h3> --}}
                                    </div>
                                    <div class="col-md-2 float-right">
                                        <a href="{{ route('ecclesias.create') }}" class="btn btn-primary w-100">+ Add
                                            Ecclesia</a>
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-md-8">
                                        <h3 class="mb-3 float-left">House Of Ecclesia List</h3>
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
                                <div class="table-responsive">
                                    <table class="table align-middle bg-white color_body_text">
                                        <thead class="color_head">
                                            <tr>
                                                <th>ID </th>
                                                <th class="sorting" data-tippy-content="Sort by Name"
                                                    data-sorting_type="desc" data-column_name="name"
                                                    style="cursor: pointer"> Ecclesia Name<span id="name_icon"><i
                                                            class="fa fa-arrow-down"></i></span></th>
                                                <th> Country </th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @include('user.ecclesias.table')

                                        </tbody>
                                        <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
                                        <input type="hidden" name="hidden_column_name" id="hidden_column_name"
                                            value="id" />
                                        <input type="hidden" name="hidden_sort_type" id="hidden_sort_type"
                                            value="desc" />
                                    </table>
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
                    text: "To delete this Ecclesia",
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
                $('#name_icon').html('');
                $('#country_icon').html('');
            }

            function fetch_data(page, sort_type, sort_by, query, topic_id) {
                $.ajax({
                    url: "{{ route('ecclesias.fetch-data') }}",
                    data: {
                        page: page,
                        sortby: sort_by,
                        sorttype: sort_type,
                        query: query,
                        topic_id: topic_id
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
                var topic_id = $('#topics').val();
                fetch_data(page, sort_type, column_name, query, topic_id);
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
                var topic_id = $('#topics').val();
                fetch_data(page, reverse_order, column_name, query, topic_id);
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
                var topic_id = $('#topics').val();
                fetch_data(page, sort_type, column_name, query, topic_id);
            });

            $(document).on('change', '#topics', function() {
                var query = $('#search').val();
                var column_name = $('#hidden_column_name').val();
                var sort_type = $('#hidden_sort_type').val();
                var page = $('#hidden_page').val();
                var topic_id = $(this).val();
                fetch_data(page, sort_type, column_name, query, topic_id);
            });

        });
    </script>
@endpush
