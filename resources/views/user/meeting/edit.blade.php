@extends('user.layouts.master')
@section('title')
    Meeting Edit - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('meetings.update', $meeting->id) }}" method="POST" enctype="multipart/form-data"
                        id="updateMeeting">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Meeting Details</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="name"> Meeting Title* </label>
                                    <input type="text" name="title" id="title" class="form-control"
                                        placeholder="Enter Meeting Title" value="{{ $meeting->title }}">

                                    <span class="text-danger" style="color:red !important;" id="title_error"></span>
                                </div>
                            </div>
                            {{-- meeting_link --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="meeting_link"> Meeting Link </label>
                                    <input type="text" name="meeting_link" id="meeting_link" class="form-control"
                                        placeholder="Enter Meeting Link" value="{{ $meeting->meeting_link }}">

                                    <span class="text-danger" style="color:red !important;" id="meeting_link_error"></span>
                                </div>
                            </div>
                            {{-- start_time --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="start_time"> Meeting Start Time* </label>
                                    <input type="datetime-local" name="start_time" id="start_time" class="form-control"
                                        placeholder="Enter Meeting Start Time"
                                        value="{{ date('Y-m-d\TH:i', strtotime($meeting->start_time)) }}">
                                    <span class="text-danger" style="color:red !important;" id="start_time_error"></span>
                                </div>
                            </div>
                            {{-- end_time --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="end_time"> Meeting End Time* </label>
                                    <input type="datetime-local" name="end_time" id="end_time" class="form-control"
                                        placeholder="Enter Meeting End Time"
                                        value="{{ date('Y-m-d\TH:i', strtotime($meeting->end_time)) }}">
                                    <span class="text-danger" style="color:red !important;" id="end_time_error"></span>
                                </div>
                            </div>

                            {{-- description --}}
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="description"> Description* </label>
                                    <textarea name="description" id="description" class="form-control" placeholder="Enter Description"> {{ $meeting->description }}</textarea>
                                    <span class="text-danger" id="description_error" style="color:red !important;"></span>
                                </div>
                            </div>

                        </div>
                        <div class="row">

                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Update</button>
                                <a href="{{ route('meetings.index') }}" class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                </div>
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
                let socket_port = '3000';
                let socket = io(ip_address + ':' + socket_port);

                // Update Meeting

                $('#updateMeeting').on('submit', function(e) {
                    e.preventDefault();
                    let form = $(this);
                    let url = form.attr('action');
                    let type = form.attr('method');
                    let data = form.serialize();
                    $.ajax({
                        url: url,
                        type: type,
                        data: data,
                        success: function(response) {
                            if (response.status) {
                                toastr.success(response.message);
                                $('#updateMeeting')[0].reset();
                                socket.emit('meeting_updated', {
                                    meeting_id: response.id,
                                });
                                window.location.href = "{{ route('meetings.index') }}";


                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            // show error message in span
                            $('.text-danger').text('');
                            $.each(xhr.responseJSON.errors, function(key, item) {
                                $('#' + key + '_error').text(item[0]);
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
