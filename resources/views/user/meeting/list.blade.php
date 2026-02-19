@extends('user.layouts.master')
@section('title')
    Meeting List - {{ env('APP_NAME') }}
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
                                    <div class="col-md-8">
                                        <h3 class="mb-3">Meeting List</h3>
                                    </div>
                                    <div class="col-lg-2 float-right cl-view">
                                        <a href="{{ route('meetings.view-calender') }}" class="btn btn-primary w-100"><i
                                                class="fa fa-calendar"></i> Calender View</a>
                                    </div>
                                    @if (auth()->user()->can('Create Meeting Schedule'))
                                        <div class="col-lg-2 float-right">
                                            <a href="{{ route('meetings.create') }}" class="btn btn-primary w-100"><i
                                                    class="fa-solid fa-plus"></i> Create Meeting</a>
                                        </div>
                                    @endif
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
                                {{-- show a message design to reload the page design and cancel button --}}
                                {{-- @if (session('success')) --}}
                                <div style="display:none !important;" id="load-meeting">
                                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center justify-content-between"
                                        role="alert" style="background:antiquewhite; ">
                                        <div>
                                            <strong>Success:</strong> New meeting has been added.
                                            Do you want to see the new meeting?
                                        </div>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary btn-sm"
                                                id="reloadPageBtn">Reload</button>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                </div>

                                {{-- @endif --}}
                                <div class="table-responsive">
                                    <table class="table align-middle bg-white color_body_text">
                                        <thead class="color_head">
                                            <tr class="header-row">
                                                {{-- <th>ID (#)</th> --}}
                                                <th class="sorting" data-tippy-content="Sort by Meeting Name"
                                                    data-sorting_type="desc" data-column_name="title"
                                                    style="cursor: pointer">Meeting Title <span id="title_icon"><i
                                                            class="fa fa-arrow-down"></i></span></th>
                                                {{-- start_time --}}
                                                <th class="sorting" data-tippy-content="Sort by Meeting Start Time"
                                                    data-sorting_type="desc" data-column_name="start_time"
                                                    style="cursor: pointer">Meeting Start Time <span id="start_time_icon"><i
                                                            class="fa fa-arrow-down"></i></span></th>
                                                {{-- end_time --}}
                                                <th class="sorting" data-tippy-content="Sort by Meeting End Time"
                                                    data-sorting_type="desc" data-column_name="end_time"
                                                    style="cursor: pointer">Meeting End Time <span id="end_time_icon"><i
                                                            class="fa fa-arrow-down"></i></span></th>
                                                {{-- meeting_link --}}
                                                <th class="sorting" data-tippy-content="Sort by Meeting Link"
                                                    data-sorting_type="desc" data-column_name="meeting_link"
                                                    style="cursor: pointer">Meeting Link <span id="meeting_link_icon"><i
                                                            class="fa fa-arrow-down"></i></span></th>
                                                <th>
                                                    Country
                                                </th>
                                                <th>
                                                    Created Time
                                                </th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @include('user.meeting.table', ['meetings' => $meetings])

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

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            // Define WebSocket connection
            let ip_address = "{{ env('IP_ADDRESS') }}";
            let socket_port = '{{ env('SOCKET_PORT') }}';
            let socket = io(ip_address + ':' + socket_port);
            var sender_id = "{{ auth()->user()->id }}";

            $(document).on('click', '#delete', function(e) {
                swal({
                        title: "Are you sure?",
                        text: "To remove this meeting from the meeting board",
                        type: "warning",
                        confirmButtonText: "Yes",
                        showCancelButton: true
                    })
                    .then((result) => {
                        if (result.value) {
                            var route = $(this).data('route');
                            $.ajax({
                                url: route,
                                type: 'GET',
                                success: function(response) {
                                    if (response.status == true) {
                                        $('#single-meeting-' + response.id).remove();

                                        socket.emit('delete_meeting', {
                                            id: response.id
                                        });

                                        swal(
                                            'Deleted!',
                                            'Your meeting has been deleted.',
                                            'success'
                                        )
                                    } else {
                                        swal(
                                            'Error!',
                                            'Your meeting has not been deleted.',
                                            'error'
                                        )
                                    }
                                }
                            });
                        } else if (result.dismiss === 'cancel') {
                            swal(
                                'Cancelled',
                                'Your stay here :)',
                                'error'
                            )
                        }
                    });
            });

            function loadSingleMeeting(meeting_id) {
                $.ajax({
                    method: 'GET',
                    url: "{{ route('meetings.show-single-meeting') }}",
                    data: {
                        meeting_id: meeting_id
                    },
                    success: function(data) {
                        $('#single-meeting-' + meeting_id).html(data.view);
                    }
                });
            }

            socket.on('delete_meeting', function(data) {
                $('#single-meeting-' + data.id).remove();
            });

            socket.on('meeting_updated', function(data) {
                loadSingleMeeting(data.meeting_id);
            });

            // meeting_created
            socket.on('meeting_created', function(data) {
                if (sender_id != data.meeting.id) {
                    $('#load-meeting').css('display', 'block');
                }
            });

            function clear_icon() {
                $('#title_icon').html('');
                $('#start_time_icon').html('');
                $('#end_time_icon').html('');
                $('#meeting_link_icon').html('');
            }

            function fetch_data(page, sort_type, sort_by, query) {
                $.ajax({
                    url: "{{ route('meetings.fetch-data') }}",
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

            $(document).on('click', '#reloadPageBtn', function() {
                fetch_data(1, 'desc', 'id', '');
                $('#load-meeting').css('display', 'none');
            });

        });
    </script>
@endpush
