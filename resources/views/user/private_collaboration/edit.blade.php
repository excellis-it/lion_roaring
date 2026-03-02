@extends('user.layouts.master')
@section('title')
    Edit Private Collaboration - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple {
            min-height: 45px;
        }

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
                            @if (auth()->user()->hasNewRole('SUPER ADMIN'))
                                {{-- country --}}
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label>Country *</label>
                                        <select name="country_id" id="countries" class="form-control">
                                            <option value="">Select Country</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}"
                                                    {{ $collaboration->country_id == $country->id ? 'selected' : '' }}>
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

                            @if (auth()->user()->hasNewRole('SUPER ADMIN'))
                                <div class="col-md-12 mb-2">
                                    <div class="box_label">
                                        <label>Invite Users <small>(Select multiple users)</small></label>
                                        <select name="invitees[]" id="invitees" class="form-control select2-multi"
                                            multiple="multiple">
                                            <option></option>
                                        </select>
                                        <span class="text-danger" id="invitees_error"></span>
                                    </div>
                                </div>
                            @endif

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
            let socket_port = '{{ env('SOCKET_PORT') }}';
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

            @if (auth()->user()->hasNewRole('SUPER ADMIN'))
                // Initialize Select2 for invitees
                function initInviteesSelect() {
                    var renderTemplate = function(data) {
                        if (!data.id) return data.text;
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

                function loadUsersForCountry(countryId) {
                    if (!countryId) return;
                    var $invitees = $('#invitees');

                    $.ajax({
                        url: '{{ route('private-collaborations.get-eligible-users') }}',
                        type: 'GET',
                        data: {
                            country_id: countryId
                        },
                        success: function(response) {
                            if (response.status && response.users) {
                                $invitees.empty().append('<option></option>');

                                // Get currently invited user IDs
                                var invitedUserIds = @json($collaboration->invitations->pluck('user_id')->toArray());

                                $.each(response.users, function(index, user) {
                                    var selected = invitedUserIds.includes(user.id) ?
                                        ' selected' : '';
                                    $invitees.append('<option value="' + user.id + '"' +
                                        selected + '>' + user.text + '</option>');
                                });

                                if (typeof $.fn.select2 !== 'undefined') {
                                    $invitees.trigger('change');
                                }
                            }
                        },
                        error: function(xhr) {
                            console.error('Failed to load users:', xhr);
                            toastr.error('Failed to load users for selected country.');
                        }
                    });
                }

                if (typeof $.fn.select2 === 'undefined') {
                    $.getScript('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js')
                        .done(function() {
                            initInviteesSelect();
                            // Load users for the currently selected country
                            var currentCountry = $('#countries').val();
                            if (currentCountry) {
                                loadUsersForCountry(currentCountry);
                            }
                        });
                } else {
                    initInviteesSelect();
                    var currentCountry = $('#countries').val();
                    if (currentCountry) {
                        loadUsersForCountry(currentCountry);
                    }
                }

                // When country changes, reload users
                $('#countries').on('change', function() {
                    var countryId = $(this).val();
                    var $invitees = $('#invitees');

                    // Clear current options
                    $invitees.empty().append('<option></option>');
                    if (typeof $.fn.select2 !== 'undefined') {
                        $invitees.val(null).trigger('change');
                    }

                    if (countryId) {
                        loadUsersForCountry(countryId);
                    }
                });
            @endif
        });
    </script>
@endpush
