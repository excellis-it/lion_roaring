@extends('user.layouts.master')
@section('title')
    Private Collaboration Calendar - {{ env('APP_NAME') }}
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
                                    <a href="{{ route('private-collaborations.index') }}" class="btn btn-primary w-100">
                                        <i class="fa fa-table" aria-hidden="true"></i> Table List
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <h3 class="mb-3 float-left">Private Collaboration Calendar</h3>
                                </div>
                            </div>
                            <div id='calendar'></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Event view modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Collaboration Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Title:</strong> <span id="modalTitle"></span></p>
                    <p><strong>Start:</strong> <span id="modalStart"></span></p>
                    <p><strong>End:</strong> <span id="modalEnd"></span></p>
                    <p><strong>Description:</strong> <span id="modalDescription"></span></p>
                    <p><strong>Created By:</strong> <span id="modalCreatedBy"></span></p>
                    <p id="modalStatusContainer"><strong>Status:</strong> <span id="modalStatus"></span></p>
                    <p id="modalLinkContainer"><strong>Meeting Link:</strong> <span id="modalLink"></span></p>
                    <p id="modalAccessMessage" style="color: #dc3545; display: none;">
                        <i class="fa fa-info-circle"></i> Accept the invitation to view the meeting link
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="print_btn" data-bs-dismiss="modal">Close</button>
                    <a id="modalViewDetails" href="#" class="print_btn" style="display: none;">View Details</a>
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
            let socket_port = '{{ env('SOCKET_PORT') }}';
            let socket = io(ip_address + ':' + socket_port);

            var calendar;
            loadCalender();

            function loadCalender() {
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    eventColor: '#7851a9',
                    events: function(fetchInfo, successCallback, failureCallback) {
                        $.ajax({
                            url: '{{ route('private-collaborations.calender-fetch-data') }}',
                            method: 'GET',
                            success: function(data) {
                                var collaborations = data.map(collaboration => ({
                                    id: collaboration.id,
                                    title: collaboration.title,
                                    start: collaboration.start,
                                    end: collaboration.end,
                                    description: collaboration.description,
                                    meeting_link: collaboration.meeting_link,
                                    is_creator: collaboration.is_creator,
                                    has_accepted: collaboration.has_accepted,
                                    is_zoom: collaboration.is_zoom,
                                    created_by: collaboration.created_by
                                }));
                                successCallback(collaborations);
                            },
                            error: function() {
                                failureCallback();
                            }
                        });
                    },
                    eventClick: function(info) {
                        var collaboration = info.event.extendedProps;

                        // Set basic details
                        $('#modalTitle').text(info.event.title);
                        $('#modalStart').text(info.event.start ? moment(info.event.start).format(
                            'DD MMM YYYY h:mm A') : 'N/A');
                        $('#modalEnd').text(info.event.end ? moment(info.event.end).format(
                            'DD MMM YYYY h:mm A') : 'N/A');
                        $('#modalDescription').html(collaboration.description || 'N/A');
                        $('#modalCreatedBy').text(collaboration.created_by || 'N/A');

                        // Set status badge
                        var statusHtml = '';
                        if (collaboration.is_creator) {
                            statusHtml = '<span class="badge bg-success">Creator</span>';
                        } else if (collaboration.has_accepted) {
                            statusHtml = '<span class="badge bg-primary">Accepted</span>';
                        } else {
                            statusHtml = '<span class="badge bg-warning">Pending Invitation</span>';
                        }
                        $('#modalStatus').html(statusHtml);

                        // Handle meeting link visibility
                        if (collaboration.is_creator || collaboration.has_accepted) {
                            $('#modalLinkContainer').show();
                            $('#modalAccessMessage').hide();

                            var link = collaboration.meeting_link ? collaboration.meeting_link.trim() :
                                '';
                            if (link) {
                                var href = link;
                                if (!/^[a-zA-Z][a-zA-Z0-9+\-.]*:/.test(link)) {
                                    if (link.startsWith('/')) {
                                        href = window.location.origin + link;
                                    } else if (/^[^\s\/]+\.[^\s\/]+/.test(link)) {
                                        href = 'https://' + link;
                                    } else {
                                        href = window.location.origin + '/' + link;
                                    }
                                }
                                var a = $('<a/>', {
                                    href: href,
                                    text: link,
                                    target: '_blank',
                                    rel: 'noopener noreferrer'
                                });

                                // Add badge for Zoom
                                if (collaboration.is_zoom) {
                                    var badge = collaboration.is_creator ?
                                        ' <span class="badge bg-success">Zoom - Host</span>' :
                                        ' <span class="badge bg-primary">Zoom</span>';
                                    a.after(badge);
                                }

                                $('#modalLink').empty().append(a);
                            } else {
                                $('#modalLink').text('N/A');
                            }
                        } else {
                            $('#modalLinkContainer').hide();
                            $('#modalAccessMessage').show();
                        }

                        // Set view details link
                        $('#modalViewDetails').attr('href',
                                '{{ route('private-collaborations.show', '') }}/' + info.event.id)
                            .show();

                        $('#eventModal').modal('show');
                    },
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        meridiem: true
                    }
                });
                calendar.render();
            }

            // Listen for WebSocket events and reload calendar
            socket.on('collaboration_deleted', function(data) {
                loadCalender();
                toastr.info('A collaboration has been deleted');
            });

            socket.on('collaboration_updated', function(data) {
                loadCalender();
                toastr.info('A collaboration has been updated');
            });

            socket.on('collaboration_created', function(data) {
                loadCalender();
                toastr.success('New collaboration invitation received');
            });

            socket.on('collaboration_accepted', function(data) {
                loadCalender();
                toastr.success('Someone accepted your collaboration invitation');
            });
        });
    </script>
@endpush
