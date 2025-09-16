@extends('user.layouts.master')
@section('title')
    Meeting List - {{ env('APP_NAME') }}
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
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="row mb-3 justify-content-end">
                                <div class="col-lg-2">
                                    <a href="{{ route('meetings.index') }}" class="btn btn-primary w-100"><i
                                            class="fa fa-table" aria-hidden="true"></i> Table List</a>
                                </div>

                            </div>
                            <div class="row ">
                                <div class="col-md-8">
                                    <h3 class="mb-3 float-left">Meeting List</h3>
                                </div>
                            </div>
                            <div id='calendar'></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- E0vent view model -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Meeting Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Title:</strong> <span id="modalTitle"></span></p>
                    <p><strong>Start:</strong> <span id="modalStart"></span></p>
                    <p><strong>End:</strong> <span id="modalEnd"></span></p>
                    <p><strong>Description:</strong> <span id="modalDescription"></span></p>
                    <p><strong>Meeting Link:</strong> <span id="modalLink"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="print_btn" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/main.min.js'></script>
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

            var calendar;
            loadCalender();

            function loadCalender() {
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    // change to dayGridWeek or timeGridWeek for week view
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    // change color for each meeting
                    eventColor: '#7851a9',

                    events: function(fetchInfo, successCallback, failureCallback) {
                        $.ajax({
                            url: '{{ route('meetings.calender-fetch-data') }}',
                            method: 'GET',
                            success: function(data) {
                                var meetings = data.map(meeting => ({
                                    id: meeting.id,
                                    title: meeting.title,
                                    start: meeting.start,
                                    end: meeting.end,
                                    description: meeting
                                        .description,
                                    meeting_link: meeting
                                        .meeting_link
                                }));
                                successCallback(meetings);
                            },
                            error: function() {
                                failureCallback();
                            }
                        });
                    },
                    eventClick: function(info) {
                        // Format the start and end times to match 'MM-DD-YYYY HH:mm A'
                        $('#modalTitle').text(info.event.title);
                        $('#modalStart').text(info.event.start ? moment(info.event.start)
                            .format(
                                'DD MMM YYYY h:mm A') : 'N/A');
                        $('#modalEnd').text(info.event.end ? moment(info.event.end).format(
                            'DD MMM YYYY h:mm A') : 'N/A');
                        $('#modalDescription').text(info.event.extendedProps.description);
                        $('#modalLink').text(info.event.extendedProps.meeting_link ? info
                            .event
                            .extendedProps.meeting_link : 'N/A');
                        $('#eventModal').modal('show');
                    },
                    eventTimeFormat: { // format for times in the calendar view
                        hour: '2-digit',
                        minute: '2-digit',
                        meridiem: true // Show AM/PM
                    }
                });
                calendar.render();
            }

            // Listen for the 'meeting' event and update the calendar
            socket.on('delete_meeting', function(data) {
                var calendar;
                loadCalender();
            });

            // meeting_updated
            socket.on('meeting_updated', function(data) {
                var calendar;
                loadCalender();
            });

            // meeting_created
            socket.on('meeting_created', function(data) {
                var calendar;
                loadCalender();
            });
        });
    </script>
@endpush
