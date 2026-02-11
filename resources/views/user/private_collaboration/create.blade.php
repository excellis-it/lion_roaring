@extends('user.layouts.master')
@section('title')
    Create Private Collaboration - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        .ck-placeholder {
            color: #a1a1a1;
            height: 250px !important;
        }

        .select2-container--default .select2-selection--multiple {
            min-height: 45px;
        }

        /* Selected tag styling for invitees (purple theme) */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #7851a9;
            border: 1px solid #5f3b86;
            color: #ffffff;
            padding-left: 8px;
            padding-right: 6px;
            border-radius: 6px;
            font-weight: 500;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: rgba(255, 255, 255, 0.9);
            margin-right: 6px;
            font-weight: 700;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #fff;
        }

        /* Make the selected item text more readable */
        .select2-container--default .select2-selection--multiple .select2-selection__choice span {
            color: #ffffff;
        }
    </style>
@endpush
@section('content')
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('private-collaborations.store') }}" method="POST" enctype="multipart/form-data"
                        id="createCollaboration">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Create Private Collaboration</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            @if (auth()->user()->user_type == 'Global')
                                {{-- country --}}
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label>Country *</label>
                                        <select name="country_id" id="countries" class="form-control">
                                            <option value="">Select Country</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}"
                                                    {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}</option>
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
                                    <label>Title <span style="color:red">*</span></label>
                                    <input type="text" class="form-control" name="title" placeholder="Enter title"
                                        required>
                                    <span class="text-danger" id="title_error"></span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label>Meeting Link Source <span style="color:red">*</span></label>
                                    <select name="link_source" id="link_source" class="form-control" required>
                                        <option value="manual">Enter Manual Link</option>
                                        <option value="zoom">Create Zoom Meeting</option>
                                    </select>
                                    <input type="hidden" name="create_zoom" id="create_zoom" value="0">
                                    <span class="text-danger" id="link_source_error"></span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label>Meeting Link</label>
                                    <input type="url" class="form-control" name="meeting_link" id="meeting_link"
                                        placeholder="Enter meeting link">
                                    <span class="text-danger" id="meeting_link_error"></span>
                                </div>
                            </div>

                            @if (isset($eligibleUsers) && $eligibleUsers->count())
                                <div class="col-md-12 mb-2">
                                    <div class="box_label">
                                        <label>Invite Users <small>(Select multiple users)</small></label>
                                        <select name="invitees[]" id="invitees" class="form-control select2-multi"
                                            multiple="multiple" required aria-required="true">
                                            <option></option>
                                            @foreach ($eligibleUsers as $user)
                                                <option value="{{ $user->id }}">{{ $user->full_name }}
                                                    &lt;{{ $user->email }}&gt;</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger" id="invitees_error"></span>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label>Start Time <span style="color:red">*</span></label>
                                    <input type="datetime-local" class="form-control" name="start_time" required>
                                    <span class="text-danger" id="start_time_error"></span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label>End Time <span style="color:red">*</span></label>
                                    <input type="datetime-local" class="form-control" name="end_time" required>
                                    <span class="text-danger" id="end_time_error"></span>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label>Description</label>
                                    <textarea class="form-control" name="description" id="description" rows="5" placeholder="Enter description"></textarea>
                                    <span class="text-danger" id="description_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Create & Send Invitations</button>
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
            $('#link_source').on('change', syncZoomToggle);
            syncZoomToggle();

            $('#createCollaboration').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);

                // Client-side validation: ensure at least one invitee selected
                if ($('#invitees').length) {
                    var selected = $('#invitees').val();
                    if (!selected || selected.length === 0) {
                        $('#invitees_error').text('Please select at least one user to invite.');
                        return;
                    } else {
                        $('#invitees_error').text('');
                    }
                }

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
                            // clear form and select2 selection
                            $('#createCollaboration')[0].reset();
                            if ($('#invitees').length && typeof $.fn.select2 !== 'undefined') {
                                $('#invitees').val(null).trigger('change');
                            }
                            $('#loading').removeClass('loading');
                            $('#loading-content').removeClass('loading-content');

                            // Emit WebSocket event
                            socket.emit('collaboration_created', {
                                collaboration: response.collaboration
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
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            toastr.error(xhr.responseJSON.message);
                        }
                        $.each(xhr.responseJSON.errors || {}, function(key, item) {
                            $('#' + key + '_error').text(item[0]);
                        });
                    }
                });
            });

            // Initialize Select2 for invitees with better UX
            function initInviteesSelect() {
                var renderTemplate = function(data) {
                    if (!data.id) return data.text;
                    // data.text is like "First Middle Last <email@domain.com>"
                    var m = data.text.match(/^(.*) <(.*)>$/);
                    if (m) {
                        var $container = $("<div class='invite-template'><div><strong>" + m[1] +
                            "</strong></div><div style='font-size:90%;color:#6c757d;'>" + m[2] +
                            "</div></div>");
                        return $container;
                    }
                    return data.text;
                };

                $('#invitees').select2({
                    placeholder: 'Select users to invite',
                    allowClear: true,
                    width: '100%',
                    closeOnSelect: false,
                    templateResult: renderTemplate,
                    templateSelection: function(data) {
                        return data.text;
                    }
                });
            }

            if ($('#invitees').length) {
                // load select2 script dynamically if not present
                if (typeof $.fn.select2 === 'undefined') {
                    $.getScript('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js')
                        .done(function() {
                            initInviteesSelect();
                        });
                } else {
                    initInviteesSelect();
                }
            }
        });
    </script>
@endpush
