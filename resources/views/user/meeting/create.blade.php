@extends('user.layouts.master')
@section('title')
    Meeting - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet">
    <style>
        .ck-placeholder {
            color: #a1a1a1;
            height: 250px !important;
        }
    </style>
@endpush
@section('content')
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('meetings.store') }}" method="POST" enctype="multipart/form-data"
                        id="createMeeting">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Meeting Details</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                             @if (auth()->user()->user_type == 'Global')
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label for="country_id">Country*</label>

                                        <select name="country_id" id="country_id" class="form-control">
                                            <option value="">Select Country</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}"
                                                    {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('country_id'))
                                            <span class="error">{{ $errors->first('country_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="name"> Meeting Title* </label>
                                    <input type="text" name="title" id="title" class="form-control"
                                        value="{{ old('title') }}" placeholder="Enter Meeting Title">
                                    <span class="text-danger" style="color:red !important;" id="title_error"></span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label> Meeting Link Source </label>
                                    <!-- Replaced radios with select -->
                                    <select class="form-control" name="link_source" id="link_source">
                                        <option value="external"
                                            {{ old('link_source', 'external') == 'external' ? 'selected' : '' }}>External
                                            link</option>
                                        <option value="zoom" {{ old('link_source') == 'zoom' ? 'selected' : '' }}>Create
                                            Zoom meeting</option>
                                    </select>
                                    <input type="hidden" name="create_zoom" id="create_zoom" value="0">
                                </div>
                            </div>

                            {{-- meeting_link --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="meeting_link"> Meeting Link </label>
                                    <input type="text" name="meeting_link" id="meeting_link" class="form-control"
                                        value="{{ old('meeting_link') }}" placeholder="Enter Meeting Link">
                                    <span class="text-danger" style="color:red !important;" id="meeting_link_error"></span>
                                </div>
                            </div>
                            {{-- start_time --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="start_time"> Meeting Start Time* </label>
                                    <input type="datetime-local" name="start_time" id="start_time" class="form-control"
                                        value="{{ old('start_time') }}" placeholder="Enter Meeting Start Time">
                                    <span class="text-danger" style="color:red !important;" id="start_time_error"></span>
                                </div>
                            </div>
                            {{-- end_time --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="end_time"> Meeting End Time* </label>
                                    <input type="datetime-local" name="end_time" id="end_time" class="form-control"
                                        value="{{ old('end_time') }}" placeholder="Enter Meeting End Time">
                                    <span class="text-danger" style="color:red !important;" id="end_time_error"></span>
                                </div>
                            </div>

                            {{-- description --}}
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="description"> Description* </label>
                                    <textarea name="description" id="description" class="form-control" placeholder="Enter Description">{{ old('description') }}</textarea>
                                    <span class="text-danger" style="color:red !important;" id="description_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Add</button>
                                <a href="{{ route('meetings.index') }}" class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                    </form>
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

                // Toggle Zoom creation vs external link
                function syncZoomToggle() {
                    const useZoom = $('#link_source').val() === 'zoom';
                    $('#create_zoom').val(useZoom ? 1 : 0);
                    $('#meeting_link').prop('disabled', useZoom)
                        .attr('placeholder', useZoom ? 'Will be generated automatically' : 'Enter Meeting Link');
                    if (useZoom) {
                        $('#meeting_link').val('');
                    }
                }
                // Bind to select instead of radios
                $('#link_source').on('change', syncZoomToggle);
                syncZoomToggle();

                // Define WebSocket connection
                let ip_address = "{{ env('IP_ADDRESS') }}";
                let socket_port = '3000';
                let socket = io(ip_address + ':' + socket_port);


                $('#createMeeting').on('submit', function(e) {
                    e.preventDefault();
                    var form = $(this);
                    var url = form.attr('action');
                    var type = form.attr('method');
                    var data = form.serialize();
                    $('#loading').addClass('loading');
                    $('#loading-content').addClass('loading-content');
                    $.ajax({
                        url: url,
                        type: type,
                        data: data,
                        success: function(response) {
                            if (response.status == true) {
                                toastr.success(response.message);
                                $('#createMeeting')[0].reset();
                                socket.emit('meeting_created', {
                                    meeting: response.meeting
                                });
                                window.location.href = "{{ route('meetings.index') }}";
                                $('#loading').removeClass('loading');
                                $('#loading-content').removeClass('loading-content');
                            } else {
                                $('#loading').removeClass('loading');
                                $('#loading-content').removeClass('loading-content');
                                toastr.error(response.message);
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
