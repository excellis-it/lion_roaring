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
                                    <div class="col-md-8">
                                        <h3 class="mb-3">Private Collaboration List</h3>
                                    </div>
                                    @if (auth()->user()->can('Create Private Collaboration'))
                                        <div class="col-lg-4 float-right">
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
                                } else {
                                    toastr.error(response.message);
                                }
                            }
                        });
                    }
                });
            });

            // Accept invitation
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
                            location.reload();
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
        });
    </script>
@endpush
