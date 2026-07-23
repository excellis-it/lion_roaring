@extends('user.layouts.master')
@section('title')
    Create Private Collaboration - {{ env('APP_NAME') }}
@endsection
@push('styles')
    @include('user.private_collaboration.partials.invitee_select_styles')
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

                            @if (auth()->user()->hasNewRole('SUPER ADMIN'))
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
                                    <label>Timezone</label>
                                    <input type="text" class="form-control"
                                        value="{{ auth()->user()->time_zone ?? 'UTC' }}" readonly disabled>
                                    <small class="text-muted">Start and end times use your profile timezone.</small>
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
            let socket_port = '{{ env('SOCKET_PORT') }}';
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

            function serializeCollaborationForm(form) {
                var params = new URLSearchParams();
                form.serializeArray().forEach(function(item) {
                    if (item.name !== 'invitees[]') {
                        params.append(item.name, item.value);
                    }
                });

                var invitees = $('#invitees').val() || [];
                if (!Array.isArray(invitees)) {
                    invitees = [invitees];
                }
                invitees.forEach(function(id) {
                    if (id) {
                        params.append('invitees[]', id);
                    }
                });

                return params.toString();
            }

            function showCollaborationFormErrors(xhr) {
                $('.text-danger').text('');
                var response = xhr.responseJSON || {};
                var errors = response.errors || {};
                var messages = [];

                $.each(errors, function(key, items) {
                    var msg = items[0];
                    messages.push(msg);
                    var fieldKey = key.split('.')[0];
                    $('#' + fieldKey + '_error').text(msg);
                });

                if (messages.length) {
                    toastr.error(messages.join('<br>'));
                } else if (response.message) {
                    toastr.error(response.message);
                } else {
                    toastr.error('Unable to save collaboration. Please check the form and try again.');
                }
            }

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
                var data = serializeCollaborationForm(form);
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
                    error: function(xhr) {
                        $('#loading').removeClass('loading');
                        $('#loading-content').removeClass('loading-content');
                        showCollaborationFormErrors(xhr);
                    }
                });
            });

            @include('user.private_collaboration.partials.invitee_select_scripts', [
                'preloadFromDom' => true,
                'enableCountryReload' => auth()->user()->hasNewRole('SUPER ADMIN'),
            ])
        });
    </script>
@endpush
