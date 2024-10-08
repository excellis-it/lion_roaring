@extends('user.layouts.master')
@section('title')
    Job List - {{ env('APP_NAME') }}
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
                                        <h3 class="mb-3">Job List</h3>
                                    </div>
                                    <div class="col-md-2 float-right">
                                        @if (auth()->user()->can('Create Job Postings'))
                                            <a href="{{ route('jobs.create') }}" class="btn btn-primary w-100"><i
                                                    class="fa-solid fa-plus"></i> Create Job</a>
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
                                <div class="table-responsive">
                                    <table class="table align-middle bg-white color_body_text">
                                        <thead class="color_head">
                                            <tr class="header-row">
                                                <th>ID (#)</th>
                                                <th class="sorting" data-tippy-content="Sort by Job Name"
                                                    data-sorting_type="desc" data-column_name="job_title"
                                                    style="cursor: pointer">Job Title <span id="job_title_icon"><i
                                                            class="fa fa-arrow-down"></i></span></th>
                                                {{-- job_type --}}
                                                <th class="sorting" data-tippy-content="Sort by Job Type"
                                                    data-sorting_type="desc" data-column_name="job_type"
                                                    style="cursor: pointer">Job Type <span id="job_type_icon"><i
                                                            class="fa fa-arrow-down"></i></span></th>
                                                {{-- job_location --}}
                                                <th class="sorting" data-tippy-content="Sort by Job Location"
                                                    data-sorting_type="desc" data-column_name="job_location"
                                                    style="cursor: pointer">Job Location <span id="job_location_icon"><i
                                                            class="fa fa-arrow-down"></i></span></th>
                                                {{-- job_salary --}}
                                                <th class="sorting" data-tippy-content="Sort by Job Salary"
                                                    data-sorting_type="desc" data-column_name="job_salary"
                                                    style="cursor: pointer">Job Salary <span id="job_salary_icon"><i
                                                            class="fa fa-arrow-down"></i></span></th>
                                                {{-- job_experience --}}
                                                <th class="sorting" data-tippy-content="Sort by Job Experience"
                                                    data-sorting_type="desc" data-column_name="job_experience"
                                                    style="cursor: pointer">Job Experience <span id="job_experience_icon"><i
                                                            class="fa fa-arrow-down"></i></span></th>
                                                            {{-- contact_person --}}
                                                <th class="sorting" data-tippy-content="Sort by Contact Person"
                                                    data-sorting_type="desc" data-column_name="contact_person"
                                                    style="cursor: pointer">Contact Person <span id="contact_person_icon"><i
                                                            class="fa fa-arrow-down"></i></span></th>
                                                            {{-- contact_email --}}
                                                <th class="sorting" data-tippy-content="Sort by Contact Email"
                                                    data-sorting_type="desc" data-column_name="contact_email"
                                                    style="cursor: pointer">Contact Email <span id="contact_email_icon"><i
                                                            class="fa fa-arrow-down"></i></span></th>
                                                            {{-- post date --}}
                                                <th >Post Date </th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @include('user.job.table', ['jobs' => $jobs])

                                        </tbody>
                                    </table>
                                    <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
                                    <input type="hidden" name="hidden_column_name" id="hidden_column_name"
                                        value="id" />
                                    <input type="hidden" name="hidden_sort_type" id="hidden_sort_type"
                                        value="desc" />
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
                    text: "To remove this job from the job board",
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
                $('#job_title_icon').html('');
                $('#job_type_icon').html('');
                $('#job_location_icon').html('');
                $('#job_salary_icon').html('');
                $('#job_experience_icon').html('');
                $('#contact_person_icon').html('');
                $('#contact_email_icon').html('');
            }

            function fetch_data(page, sort_type, sort_by, query) {
                $.ajax({
                    url: "{{ route('jobs.fetch-data') }}",
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
