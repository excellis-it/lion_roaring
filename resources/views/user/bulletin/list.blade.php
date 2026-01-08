@extends('user.layouts.master')
@section('title')
    Bulletin List - {{ env('APP_NAME') }}
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
                                        <h3 class="mb-3">Bulletin List</h3>
                                    </div>
                                    <div class="col-md-2 float-right">
                                        @if (auth()->user()->can('Create Bulletin'))
                                            <a href="{{ route('bulletins.create') }}" class="btn btn-primary w-100"><i
                                                    class="fa-solid fa-plus"></i> Create Bulletin</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-lg-4">
                                        <div class="search-field">
                                            <input type="text" name="search" id="search" placeholder="search..."
                                                required="" class="form-control rounded_search">
                                            <button class="submit_search" id="search-button"> <span class=""><i
                                                        class="fa fa-search"></i></span></button>
                                        </div>
                                    </div>
                                </div>
                                <div style="display:none !important;" id="load-bulletin">
                                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center justify-content-between"
                                        role="alert" style="background:antiquewhite; ">
                                        <div>
                                            <strong>Success:</strong> New bulletin has been added.
                                            Do you want to see the new bulletin?
                                        </div>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary btn-sm"
                                                id="reloadPageBtn">Reload</button>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table align-middle bg-white color_body_text">
                                        <thead class="color_head">
                                            <tr class="header-row">
                                                <th>ID (#)</th>
                                                @if (auth()->user()->hasNewRole('SUPER ADMIN'))
                                                    <th>
                                                        Upload By
                                                    </th>
                                                @endif
                                                <th class="sorting" data-tippy-content="Sort by Bulletin Title"
                                                    data-sorting_type="desc" data-column_name="title"
                                                    style="cursor: pointer">Bulletin Title <span id="title_icon"><i
                                                            class="fa fa-arrow-down"></i></span></th>
                                                {{-- Description --}}
                                                <th class="sorting" data-tippy-content="Sort by Bulletin Description"
                                                    data-sorting_type="desc" data-column_name="description"
                                                    style="cursor: pointer">Bulletin Description <span
                                                        id="description_icon"><i class="fa fa-arrow-down"></i></span></th>
                                                        <th>
                                                            Country
                                                        </th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="bulletin-table">
                                            @include('user.bulletin.table', ['bulletins' => $bulletins])
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
    {{-- <script>
        $(document).on('click', '#delete', function(e) {
            swal({
                    title: "Are you sure?",
                    text: "To remove this bulletin from the bulletin board",
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
    </script> --}}

    <script>
        $(document).ready(function() {

            function clear_icon() {
                $('#title_icon').html('');
                $('#description_icon').html('');
            }

            function fetch_data(page, sort_type, sort_by, query) {
                $.ajax({
                    url: "{{ route('bulletins.fetch-data') }}",
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

            // Reload page
            $(document).on('click', '#reloadPageBtn', function() {
                fetch_data(1, 'desc', 'id', '');
                $('#load-bulletin').hide();
            });

        });
    </script>
@endpush
