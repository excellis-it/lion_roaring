@extends('user.layouts.master')
@section('title')
    Edit Private Collaboration - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('private-collaborations.update', $collaboration->id) }}" method="POST"
                        enctype="multipart/form-data" id="updateCollaboration">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Edit Private Collaboration</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label>Title <span style="color:red">*</span></label>
                                    <input type="text" class="form-control" value="{{ $collaboration->title }}"
                                        name="title" placeholder="Enter title" required>
                                    <span class="text-danger" id="title_error"></span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label>Meeting Link</label>
                                    <input type="url" class="form-control" value="{{ $collaboration->meeting_link }}"
                                        name="meeting_link" id="meeting_link" placeholder="Enter meeting link">
                                    <span class="text-danger" id="meeting_link_error"></span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label>Start Time <span style="color:red">*</span></label>
                                    <input type="datetime-local" class="form-control"
                                        value="{{ date('Y-m-d\TH:i', strtotime($collaboration->start_time)) }}"
                                        name="start_time" required>
                                    <span class="text-danger" id="start_time_error"></span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label>End Time <span style="color:red">*</span></label>
                                    <input type="datetime-local" class="form-control"
                                        value="{{ date('Y-m-d\TH:i', strtotime($collaboration->end_time)) }}"
                                        name="end_time" required>
                                    <span class="text-danger" id="end_time_error"></span>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label>Description</label>
                                    <textarea class="form-control" name="description" id="description" rows="5" placeholder="Enter description">{{ $collaboration->description }}</textarea>
                                    <span class="text-danger" id="description_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Update</button>
                                <a href="{{ route('private-collaborations.index') }}"
                                    class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                    </form>
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

            $('#updateCollaboration').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let url = form.attr('action');
                let type = form.attr('method');
                let data = form.serialize();
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
                $.ajax({
                    url: url,
                    type: type,
                    data: data,
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            $('#loading').removeClass('loading');
                            $('#loading-content').removeClass('loading-content');

                            // Emit WebSocket event
                            socket.emit('collaboration_updated', {
                                id: response.id
                            });

                            window.location.href =
                            "{{ route('private-collaborations.index') }}";
                        } else {
                            toastr.error(response.message);
                            $('#loading').removeClass('loading');
                            $('#loading-content').removeClass('loading-content');
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#loading').removeClass('loading');
                        $('#loading-content').removeClass('loading-content');
                        $('.text-danger').text('');
                        $.each(xhr.responseJSON.errors || {}, function(key, item) {
                            $('#' + key + '_error').text(item[0]);
                        });
                    }
                });
            });
        });
    </script>
@endpush
