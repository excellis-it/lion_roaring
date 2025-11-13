@extends('admin.layouts.master')
@section('title')
    All Countries - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <style>
        .dataTables_filter {
            margin-bottom: 10px !important;
        }
    </style>
@endpush
@section('head')
    All Countries
@endsection
@section('create_button')
    @if (auth()->user()->can('Manage Countries'))
        <a href="{{ route('admin-countries.create') }}" class="btn btn-primary">+ Create Country</a>
    @endif
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
                            <div class="col-md-12 pr-0">
                                <div class="search-field prod-search">
                                    <input type="text" name="search" id="search"
                                        placeholder="search by name or code..." required class="form-control">
                                    <a href="javascript:void(0)" class="prod-search-icon"><i
                                            class="ph ph-magnifying-glass"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive" id="countries-data">
                    <table class="table table-bordered" class="display">
                        <thead>
                            <tr>
                                <th class="sorting" data-tippy-content="Sort by Id" data-sorting_type="asc"
                                    data-column_name="id" style="cursor:pointer">#<span id="id_icon"></span></th>
                                <th class="sorting" data-sorting_type="asc" data-column_name="name" style="cursor:pointer"
                                    data-tippy-content="Sort by Name">Name<span id="name_icon"></span></th>
                                <th class="sorting" data-sorting_type="asc" data-column_name="code" style="cursor:pointer"
                                    data-tippy-content="Sort by Code">Code<span id="code_icon"></span></th>
                                <th>Flag</th>
                                <th>Languages</th>
                                <th class="sorting" data-sorting_type="asc" data-column_name="status" style="cursor:pointer"
                                    data-tippy-content="Sort by Status">Status<span id="status_icon"></span></th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @include('admin.countries.table')
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
        $(document).ready(function() {
            function clear_icon() {
                $('#id_icon').html('');
                $('#name_icon').html('');
                $('#code_icon').html('');
                $('#status_icon').html('');
            }

            function fetch_data(page, sort_type, sort_by, query) {
                $.ajax({
                    url: "{{ route('admin-countries.fetch-data') }}",
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
                    $('#' + column_name + '_icon').html('<span class="ph ph-caret-down"></span>');
                }
                if (order_type == 'desc') {
                    $(this).data('sorting_type', 'asc');
                    reverse_order = 'asc';
                    clear_icon();
                    $('#' + column_name + '_icon').html('<span class="ph ph-caret-up"></span>');
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
    <script src="https://unpkg.com/popper.js@1"></script>
    <script src="https://unpkg.com/tippy.js@5"></script>
    <script>
        tippy('[data-tippy-content]', {
            allowHTML: true,
            placement: 'bottom',
            theme: 'light-theme'
        });
    </script>
@endpush
