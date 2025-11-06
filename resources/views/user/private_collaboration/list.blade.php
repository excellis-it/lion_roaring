@extends('user.layouts.master')
@section('title')
    Private Collaboration List - {{ env('APP_NAME') }}
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
                                    <div class="col-md-6">
                                        <h3 class="mb-3">Private Collaboration List</h3>
                                    </div>
                                    <div class="col-lg-2"></div>
                                    <div class="col-lg-2 float-right cl-view">
                                        <a href="{{ route('private-collaborations.view-calender') }}"
                                            class="btn btn-primary w-100">
                                            <i class="fa fa-calendar"></i> Calendar View
                                        </a>
                                    </div>
                                    @if (auth()->user()->can('Create Private Collaboration'))
                                        <div class="col-lg-2 float-right">
                                            <a href="{{ route('private-collaborations.create') }}"
                                                class="btn btn-primary w-100">
                                                <i class="fa-solid fa-plus"></i> Create Collaboration
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-lg-4">
                                        <div class="search-field">
                                            <input type="text" name="search" id="search" placeholder="search..."
                                                required="" class="form-control rounded_search">
                                            <button class="submit_search" id="search-button">
                                                <span class=""><i class="fa fa-search"></i></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table align-middle bg-white color_body_text">
                                        <thead class="color_head">
                                            <tr class="header-row">
                                                <th>Title</th>
                                                <th>Start Time</th>
                                                <th>End Time</th>
                                                <th>Status</th>
                                                <th>Created By</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @include('user.private_collaboration.table', [
                                                'collaborations' => $collaborations,
                                            ])
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
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




            // Delete collaboration
            $(document).on('click', '#delete', function(e) {
                e.preventDefault();
                var route = $(this).data('route');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: route,
                            type: 'GET',
                            success: function(response) {
                                if (response.status == true) {
                                    toastr.success(response.message);
                                    $('#single-collaboration-' + response.id).remove();

                                    // Emit WebSocket event
                                    socket.emit('collaboration_deleted', {
                                        id: response.id
                                    });
                                } else {
                                    toastr.error(response.message);
                                }
                            }
                        });
                    }
                });
            });

            // Accept invitation with real-time update
            $(document).on('click', '#accept-invitation', function(e) {
                e.preventDefault();
                var route = $(this).data('route');
                var collaborationId = $(this).data('id');

                $.ajax({
                    url: route,
                    type: 'POST',
                    success: function(response) {
                        if (response.status == true) {
                            toastr.success(response.message);

                            // Reload the specific row without full page reload
                            $.ajax({
                                url: "{{ route('private-collaborations.show-single-collaboration') }}",
                                type: 'GET',
                                data: {
                                    collaboration_id: collaborationId
                                },
                                success: function(res) {
                                    if (res.status) {
                                        $('#single-collaboration-' +
                                            collaborationId).html(res.view);

                                        // Emit WebSocket event to notify creator
                                        socket.emit('collaboration_accepted', {
                                            collaboration_id: collaborationId
                                        });
                                    }
                                }
                            });
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error accepting invitation');
                    }
                });
            });

            // Search functionality
            function fetch_data(page, query = '') {
                $.ajax({
                    url: "{{ route('private-collaborations.fetch-data') }}",
                    data: {
                        page: page,
                        query: query
                    },
                    success: function(data) {
                        $('tbody').html(data);
                    }
                });
            }

            $('#search').on('keyup', function() {
                var query = $('#search').val();
                fetch_data(1, query);
            });

            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                var query = $('#search').val();
                fetch_data(page, query);
            });

            // Listen for WebSocket events
            socket.on('collaboration_deleted', function(data) {
                $('#single-collaboration-' + data.id).fadeOut(300, function() {
                    $(this).remove();
                });
                toastr.info('A collaboration has been deleted');
            });

            socket.on('collaboration_updated', function(data) {
                // Reload the updated collaboration row
                $.ajax({
                    url: "{{ route('private-collaborations.show-single-collaboration') }}",
                    type: 'GET',
                    data: {
                        collaboration_id: data.id
                    },
                    success: function(res) {
                        if (res.status) {
                            $('#single-collaboration-' + data.id).html(res.view);
                        }
                    }
                });
                toastr.info('A collaboration has been updated');
            });

            socket.on('collaboration_created', function(data) {
                // Reload the table to show new invitation
                fetch_data(1, $('#search').val());
                toastr.success('New collaboration invitation received');
            });

            socket.on('collaboration_accepted', function(data) {
                // Reload the specific collaboration row for the creator
                $.ajax({
                    url: "{{ route('private-collaborations.show-single-collaboration') }}",
                    type: 'GET',
                    data: {
                        collaboration_id: data.collaboration_id
                    },
                    success: function(res) {
                        if (res.status) {
                            $('#single-collaboration-' + data.collaboration_id).html(res.view);
                        }
                    }
                });
            });
        });
    </script>
@endpush
