@extends('user.layouts.master')
@section('title')
    Live Event List - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.2.0/main.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.3.0/main.min.css">
    <style>
        .fc-button-primary {
            background-color: #7851a9 !important;
            border-color: #7851a9 !important;
        }
    </style>
@endpush
@section('content')
    @php
        use App\Helpers\Helper;
    @endphp
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="row mb-3">
                                <div class="col-md-10">
                                    {{-- <h3 class="mb-3">Live Event List</h3> --}}
                                </div>
                                @if (auth()->user()->can('Create Event'))
                                    <div class="col-md-2 float-right">
                                        <a href="javascript:void(0);" class="btn btn-primary w-100" data-bs-toggle="modal"
                                            data-bs-target="#addEventModal" data-bs-whatever="@mdo">+ Add
                                            Event</a>
                                    </div>
                                @endif

                            </div>
                            <div class="row ">
                                <div class="col-md-8">
                                    <h3 class="mb-3 float-left">Live Event List</h3>
                                </div>
                                {{-- <div class="col-lg-4">
                                        <div class="search-field float-right">
                                            <input type="text" name="search" id="search" placeholder="search..."
                                                required class="form-control">
                                            <button class="submit_search" id="search-button"> <span class=""><i
                                                        class="fa fa-search"></i></span></button>
                                        </div>
                                    </div> --}}
                            </div>
                            <div id='calendar'></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Event view model -->
    {{-- @if (auth()->user()->can('Edit Event')) --}}
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Edit Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="javascript:void(0);" id="event-edit">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="modalTitleEdit" class="col-form-label">Title:</label>
                            <input type="text" class="form-control" id="modalTitleEdit" name="title">
                        </div>
                        <div class="mb-3">
                            <label for="modalStartEdit" class="col-form-label">Start:</label>
                            <input type="datetime-local" class="form-control" id="modalStartEdit" name="start">
                        </div>
                        <div class="mb-3">
                            <label for="modalEndEdit" class="col-form-label">End:</label>
                            <input type="datetime-local" class="form-control" id="modalEndEdit" name="end">
                        </div>
                        <div class="mb-3">
                            <label for="modalDescriptionEdit" class="col-form-label">Description:</label>
                            <textarea class="form-control" id="modalDescriptionEdit" name="description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                        <button type="button" class="btn btn-danger" id="deleteEventBtn"
                            style="color: white; background:red"><i class="fas fa-trash"></i> Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- @else --}}
    <div class="modal fade" id="eventModalDetails" tabindex="-1" aria-labelledby="eventModalDetailsLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalDetailsLabel">Event Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Title:</strong> <span id="formmodalTitle"></span></p>
                    <p><strong>Start:</strong> <span id="formmodalStart"></span></p>
                    <p><strong>End:</strong> <span id="formmodalEnd"></span></p>
                    <p><strong>Description:</strong> <span id="formmodalDescription"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="print_btn" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- @endif --}}





    {{-- add event model --}}
    @if (auth()->user()->can('Create Event'))
        <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEventModalLabel">Event Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('events.store') }}" id="event-store">
                        @csrf
                        <div class="modal-body">

                            <div class="mb-3">
                                <label for="modalTitle" class="col-form-label">Title:</label>
                                <input type="text" class="form-control" id="modalTitle" name="title">
                            </div>
                            <div class="mb-3">
                                <label for="modalStart" class="col-form-label">Start:</label>
                                <input type="datetime-local" class="form-control" id="modalStart" name="start"
                                    value="{{ date('Y-m-d\TH:i') }}">
                            </div>
                            <div class="mb-3">
                                <label for="modalEnd" class="col-form-label">End:</label>
                                <input type="datetime-local" class="form-control" id="modalEnd" name="end">
                            </div>
                            <div class="mb-3">
                                <label for="modalDescription" class="col-form-label">Description:</label>
                                <textarea class="form-control" id="modalDescription" name="description"></textarea>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/main.min.js'></script>
    <script>
        $(document).on('click', '#delete', function(e) {
            swal({
                    title: "Are you sure?",
                    text: "To delete this Role.",
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
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            // Define WebSocket connection
            let ip_address = "{{ env('IP_ADDRESS') }}";
            let socket_port = '3000';
            let socket = io(ip_address + ':' + socket_port);

            // Initialize FullCalendar
            var calendar;

            function initializeFullCalendar() {
                var calendarEl = document.getElementById('calendar');
                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    eventColor: '#7851a9',

                    // Load events dynamically from the server
                    events: function(fetchInfo, successCallback, failureCallback) {
                        $.ajax({
                            url: '{{ route('events.calender') }}',
                            method: 'GET',
                            success: function(data) {
                                var events = data.map(event => {
                                    return {
                                        id: event.id,
                                        user_id: event.user_id,
                                        title: event.title,
                                        start: event.start,
                                        end: event.end,
                                        description: event.description
                                    };
                                });
                                successCallback(events);
                            },
                            error: function() {
                                failureCallback();
                            }
                        });
                    },

                    // Handle event click
                    eventClick: function(info) {
                        var event = info.event;
                        var permission = @json(auth()->user()->can('Edit Event'));
                        var admin = @json(auth()->user()->hasRole('SUPER ADMIN'));
                        var currentUser = {{ auth()->user()->id }};

                        // Check if the user has permission or is an admin
                        if (permission && event.extendedProps.user_id == currentUser || admin) {
                            // Helper function to format date for input fields
                            const formatDateForInput = (date) => date ? moment(date).format(
                                'YYYY-MM-DDTHH:mm') : '';

                            // Set modal fields with event data
                            $('#modalTitleEdit').val(event.title);
                            $('#modalStartEdit').val(formatDateForInput(event.start));
                            $('#modalEndEdit').val(formatDateForInput(event.end));
                            $('#modalDescriptionEdit').val(event.extendedProps.description);
                            $('#event-edit').attr('action',
                                '{{ route('events.update', '') }}/' + event.id);

                            // Handle event deletion
                            $('#deleteEventBtn').off('click').on('click', function() {
                                if (confirm('Are you sure you want to delete this event?')) {
                                    $.ajax({
                                        url: '{{ route('events.destroy', '') }}/' +
                                            event.id,
                                        method: 'DELETE',
                                        success: function() {
                                            toastr.success(
                                                'Event deleted successfully.');
                                            event
                                                .remove(); // Remove event from calendar
                                            $('#eventModal').modal('hide');
                                            socket.emit(
                                                'event_store_update_delete', {
                                                    'message': 'Event updated'
                                                });
                                        },
                                        error: function() {
                                            alert('Failed to delete event.');
                                        }
                                    });
                                }
                            });

                            $('#deleteEventBtn').show();
                            $('#eventModal').modal('show'); // Open edit modal
                        } else {
                            // Display event details if user lacks permissions
                            $('#formmodalTitle').text(event.title);
                            $('#formmodalStart').text(moment(event.start).format('MMM D, YYYY h:mm A'));
                            $('#formmodalEnd').text(event.end ? moment(event.end).format(
                                'MMM D, YYYY h:mm A') : 'N/A');
                            $('#formmodalDescription').text(event.extendedProps.description);

                            $('#deleteEventBtn').hide();
                            $('#eventModalDetails').modal('show'); // Open view-only modal
                        }
                    },

                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        meridiem: true // AM/PM format
                    }
                });

                calendar.render(); // Render the calendar
            }

            // Call to initialize the calendar
            initializeFullCalendar();

            $.validator.addMethod("greaterThan", function(value, element, params) {
                var startDate = $(params).val();
                if (!/Invalid|NaN/.test(new Date(value))) {
                    return new Date(value) > new Date(startDate);
                }
                return isNaN(value) && isNaN(startDate) || (Number(value) > Number(startDate));
            }, 'End date must be greater than start date.');

            // Initialize form validation
            $('#event-store').validate({
                rules: {
                    title: {
                        required: true,
                    },
                    start: {
                        required: true,
                    },
                    end: {
                        required: true,
                        greaterThan: "#modalStart"
                    },
                    description: {
                        required: true,
                    },
                },
                messages: {
                    title: {
                        required: "Please enter title",
                    },
                    start: {
                        required: "Please enter start date",
                    },
                    end: {
                        required: "Please enter end date",
                    },
                    description: {
                        required: "Please enter description",
                    },
                },
                submitHandler: function(form) {
                    // Submit form data via AJAX
                    $.ajax({
                        url: $(form).attr('action'),
                        method: 'POST',
                        data: $(form).serialize(),
                        success: function(response) {
                            if (response.status == true) {
                                console.log(response);
                                toastr.success(response.message);
                                $('#addEventModal').modal('hide');
                                var calendar;
                                initializeFullCalendar();
                                socket.emit('event_store_update_delete', {
                                    'message': 'Event updated'
                                });
                            } else {
                                toastr.error('Failed to add event.');
                            }
                        },
                        error: function(xhr) {
                            $('.text-danger').html('');
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        }
                    });
                }
            });

            $('#event-edit').validate({
                rules: {
                    title: {
                        required: true,
                    },
                    start: {
                        required: true,
                    },
                    end: {
                        required: true,
                        greaterThan: "#modalStartEdit"
                    },
                    description: {
                        required: true,
                    },
                },
                messages: {
                    title: {
                        required: "Please enter title",
                    },
                    start: {
                        required: "Please enter start date",
                    },
                    end: {
                        required: "Please enter end date",
                    },
                    description: {
                        required: "Please enter description",
                    },
                },
                submitHandler: function(form) {
                    // Submit form data via AJAX
                    $.ajax({
                        url: $(form).attr('action'),
                        method: 'POST',
                        data: $(form).serialize(),
                        success: function(response) {
                            if (response.status == true) {
                                toastr.success(response.message);
                                $('#eventModal').modal('hide');
                                var calendar;
                                initializeFullCalendar();

                                socket.emit('event_store_update_delete', {
                                    'message': 'Event updated'
                                });

                            } else {
                                toastr.error('Failed to update event.');
                            }
                        },
                        error: function(xhr) {
                            $('.text-danger').html('');
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        }
                    });
                }
            });

            // event_store_update
            socket.on('event_store_update_delete', function(data) {
                var calendar;
                initializeFullCalendar();
            });
        });
    </script>
@endpush
