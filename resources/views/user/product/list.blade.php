@extends('user.layouts.master')
@section('title')
    Product List - {{ env('APP_NAME') }}
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
                                        <h3 class="mb-3">Product List</h3>
                                    </div>
                                    <div class="col-md-2 float-right">
                                        <a href="{{ route('products.create') }}" class="btn btn-primary w-100"><i
                                                class="fa-solid fa-plus"></i> Create Product</a>
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
                                <div class="table-responsive">
                                    <table class="table align-middle bg-white color_body_text">
                                        <thead class="color_head">
                                            <tr class="header-row">
                                                <th>ID (#)</th>
                                                <th>
                                                    Product Image
                                                </th>
                                                <th class="sorting" data-tippy-content="Sort by Product Name"
                                                    data-sorting_type="desc" data-column_name="name"
                                                    style="cursor: pointer">Product Name <span id="name_icon"><i
                                                            class="fa fa-arrow-down"></i></span></th>
                                                <th>
                                                    Product Category
                                                </th>
                                                <th class="sorting" data-tippy-content="Sort by Product Slug"
                                                    data-sorting_type="desc" data-column_name="slug"
                                                    style="cursor: pointer">Product Slug <span id="slug_icon"><i
                                                            class="fa fa-arrow-down"></i></span></th>
                                                {{-- price --}}
                                                <th class="sorting" data-tippy-content="Sort by Product Price"
                                                    data-sorting_type="desc" data-column_name="price"
                                                    style="cursor: pointer">Product Price <span id="price_icon"><i
                                                            class="fa fa-arrow-down"></i></span></th>
                                                {{-- quantity --}}
                                                <th class="sorting" data-tippy-content="Sort by Product Quantity"
                                                    data-sorting_type="desc" data-column_name="quantity"
                                                    style="cursor: pointer">Product Quantity <span id="quantity_icon"><i
                                                            class="fa fa-arrow-down"></i></span></th>
                                                {{-- sku --}}
                                                <th class="sorting" data-tippy-content="Sort by Product SKU"
                                                    data-sorting_type="desc" data-column_name="sku" style="cursor: pointer">
                                                    Product SKU <span id="sku_icon"><i class="fa fa-arrow-down"></i></span>
                                                </th>
                                                <th>Status</th>
                                                <th>
                                                    Is Featured
                                                </th>
                                                <th>
                                                    Created On
                                                </th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @include('user.product.table', ['products' => $products])
                                            <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
                                            <input type="hidden" name="hidden_column_name" id="hidden_column_name"
                                                value="id" />
                                            <input type="hidden" name="hidden_sort_type" id="hidden_sort_type"
                                                value="asc" />
                                        </tbody>
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
                    text: "To remove this product from the product board",
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
                $('#slug_icon').html('');
                $('#price_icon').html('');
                $('#quantity_icon').html('');
                $('#sku_icon').html('');
            }

            function fetch_data(page, sort_type, sort_by, query) {
                $.ajax({
                    url: "{{ route('products.fetch-data') }}",
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
