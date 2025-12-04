@extends('user.layouts.master')
@section('title')
    All Our Governance Details - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <style>
        .dataTables_filter {
            margin-bottom: 10px !important;
        }
    </style>
@endpush

@section('create_button')
    @if (auth()->user()->can('Create Our Governance'))
        <a href="{{ route('our-governances.create') }}" class="btn btn-primary">+ Create New Our Governance</a>
    @endif
@endsection
@section('content')
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-0">Our Governance List</h3>
                    <p class="text-muted small mb-0">Manage governance structures</p>
                </div>
                <div>
                    @if (auth()->user()->can('Create Our Governance'))
                        <a href="{{ route('our-governances.create') }}" class="print_btn">+ Create Our Governance</a>
                    @endif
                </div>
            </div>

            <div class="row justify-content-end">




                <div class="col-md-6">
                    <div class="row g-1 justify-content-end">
                        <div class="col-md-4">

                            <select onchange="window.location.href='?content_country_code='+$(this).val()"
                                name="content_country_code" id="content_country_code" class="form-control">
                                @foreach (\App\Models\Country::all() as $country)
                                    <option value="{{ $country->code }}"
                                        {{ request()->get('content_country_code', 'US') == $country->code ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="country_code">Content Country</label>
                        </div>
                        <div class="col-md-8 pr-0">
                            <div class="search-field">
                                <input type="text" name="search" id="search" placeholder="search..." required=""
                                    class="form-control rounded_search">
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
            <div class="table-responsive" id="our-governances-data">
                <table class="table align-middle bg-white color_body_text" class="display">
                    <thead class="color_head">
                        <tr class="header-row">
                            <th class="sorting" data-tippy-content="Sort by Id" data-sorting_type="asc"
                                data-column_name="id" style="cursor: pointer">Id<span id="id_icon"></span>
                            </th>
                            <th class="sorting" data-sorting_type="asc" data-column_name="name" style="cursor: pointer"
                                data-tippy-content="Sort by Governance Name">
                                Governance Name<span id="name_icon"></span></th>
                            <th class="sorting" data-sorting_type="asc" data-column_name="slug" style="cursor: pointer"
                                data-tippy-content="Sort by Slug">Slug<span id="slug_icon"></span></th>

                        </tr>
                    </thead>
                    <tbody>
                        @include('user.admin.our-governances.table')

                    </tbody>
                </table>
                <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
                <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
                <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="desc" />
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '#delete', function(e) {
            swal({
                    title: "Are you sure?",
                    text: "To delete this Our Governance.",
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
                $('#name_icon').html('');
                $('#slug_icon').html('');
            }

            function fetch_data(page, sort_type, sort_by, query) {
                $.ajax({
                    url: "{{ route('our-governances.fetch-data') }}",
                    data: {
                        page: page,
                        sortby: sort_by,
                        sorttype: sort_type,
                        query: query,
                        content_country_code: $('#content_country_code').val(),
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
                        '<span class="fa fa-arrow-down"></span>');
                }
                if (order_type == 'desc') {
                    $(this).data('sorting_type', 'asc');
                    reverse_order = 'asc';
                    clear_icon();
                    $('#' + column_name + '_icon').html(
                        '<span class="fa fa-arrow-up"></span>');
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
