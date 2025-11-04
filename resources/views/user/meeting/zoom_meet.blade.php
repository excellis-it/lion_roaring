@extends('user.layouts.master')
@section('title')
    Meeting - {{ env('APP_NAME') }}
@endsection
@push('styles')
    {{-- Zoom Web SDK styles (only loaded if Zoom meeting) --}}
    @if (!empty($isZoom))
        @php $zoomVer = env('ZOOM_SDK_VERSION', '3.1.0'); @endphp
        <link rel="stylesheet" href="https://source.zoom.us/{{ $zoomVer }}/css/bootstrap.css" />
        <style>
            /* Container for in-browser meeting */
            #zmmtg-root {
                position: relative;
                z-index: 9999;
            }

            #zoom-join-container {
                margin-top: 15px;
            }
        </style>
    @endif
@endpush
@section('content')
    <div class="container-fluid">

        {{-- join button  --}}

        <button id="joinInBrowserBtn" class="btn btn-primary">Join in browser (Zoom)</button>
        @if (!empty($isZoom))
            <div id="zmmtg-root"></div>
        @endif
    </div>
@endsection

@push('scripts')
    @if (!empty($isZoom))
        @php $zoomVer = env('ZOOM_SDK_VERSION', '3.1.0'); @endphp
        {{-- Zoom Web SDK scripts --}}
        <script src="https://source.zoom.us/{{ $zoomVer }}/lib/vendor/react.min.js"></script>
        <script src="https://source.zoom.us/{{ $zoomVer }}/lib/vendor/react-dom.min.js"></script>
        <script src="https://source.zoom.us/{{ $zoomVer }}/lib/vendor/redux.min.js"></script>
        <script src="https://source.zoom.us/{{ $zoomVer }}/lib/vendor/redux-thunk.min.js"></script>
        <script src="https://source.zoom.us/{{ $zoomVer }}/lib/vendor/lodash.min.js"></script>
        <script src="https://source.zoom.us/zoom-meeting-{{ $zoomVer }}.min.js"></script>
        <script>
            $(document).ready(function() {


                const joinBtn = document.getElementById('joinInBrowserBtn');
                // if (!joinBtn) return;

                // Prepare SDK
                ZoomMtg.setZoomJSLib('https://source.zoom.us/{{ $zoomVer }}/lib', '/av'); // wasm libs
                ZoomMtg.preLoadWasm();
                ZoomMtg.prepareWebSDK();
                try {
                    ZoomMtg.i18n.load('en-US');
                    ZoomMtg.i18n.reload('en-US');
                } catch (e) {}


                $("#joinInBrowserBtn").on('click', function() {
                    joinBtn.disabled = true;
                    joinBtn.innerText = 'Preparing meeting...';
                    $.ajax({
                        method: 'POST',
                        url: "{{ route('meetings.zoom-signature') }}",
                        data: {
                            meeting_id: {{ $meeting->id }}
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            if (!res.status) {
                                toastr.error(res.message || 'Unable to init Zoom meeting.');
                                joinBtn.disabled = false;
                                joinBtn.innerText = 'Join in browser (Zoom)';
                                return;
                            }

                            const signature = res.signature;
                            const sdkKey = res.sdkKey;
                            const meetingNumber = res.meetingNumber;
                            const password = res.password || ''; // may be empty if not retrievable
                            const userName = res.userName || 'Guest';
                            const userEmail = res.userEmail || '';

                            ZoomMtg.init({
                                leaveUrl: "{{ route('meetings.show', $meeting->id) }}",
                                success: function() {
                                    ZoomMtg.join({
                                        signature: signature,
                                        sdkKey: sdkKey,
                                        meetingNumber: meetingNumber,
                                        password: password,
                                        passWord: password,
                                        userName: userName,
                                        userEmail: userEmail,
                                        success: function() {
                                            // joined
                                        },
                                        error: function(err) {
                                            console.error(err);
                                            toastr.error(
                                                'Zoom join failed.');
                                            joinBtn.disabled = false;
                                            joinBtn.innerText =
                                                'Join in browser (Zoom)';
                                        }
                                    });
                                },
                                error: function(err) {
                                    console.error(err);
                                    toastr.error('Zoom init failed.');
                                    joinBtn.disabled = false;
                                    joinBtn.innerText = 'Join in browser (Zoom)';
                                }
                            });
                        },
                        error: function() {
                            toastr.error('Unable to generate Zoom signature.');
                            joinBtn.disabled = false;
                            joinBtn.innerText = 'Join in browser (Zoom)';
                        }
                    });
                });

                // Auto-start the meeting on page load
                $("#joinInBrowserBtn").trigger('click');
            });
        </script>

        <script>
            setTimeout(() => {

                $(document).on('click', '.leave-meeting-options__btn', function() {

                    alert('Button clicked!');

                });

            }, 1500);

            // Use event delegation to listen for clicks on dynamically added elements
        </script>
        <!-- Dependencies for client view and component view -->
        {{-- <script src="https://source.zoom.us/3.1.0/lib/vendor/react.min.js"></script>
        <script src="https://source.zoom.us/3.1.0/lib/vendor/react-dom.min.js"></script>
        <script src="https://source.zoom.us/3.1.0/lib/vendor/redux.min.js"></script>
        <script src="https://source.zoom.us/3.1.0/lib/vendor/redux-thunk.min.js"></script>
        <script src="https://source.zoom.us/3.1.0/lib/vendor/lodash.min.js"></script>

        <!-- Choose between the client view or component view: -->
        <script src="https://source.zoom.us/zoom-meeting-3.1.0.min.js"></script>

        <script>
            ZoomMtg.preLoadWasm()

            ZoomMtg.prepareWebSDK()

            var sdkKey = "{{ $sdkKey }}";

            var signature = "{{ $signature }}";

            var meetingNumber = "{{ $meetingNumber }}";

            var passWord = "{{ $password }}";

            var userName = "{{ $userName }}";

            var zakToken = "{{ $zakToken ?? '' }}";

            ZoomMtg.init({

                leaveUrl: "https://example.com/thanks-for-joining",

                success: (success) => {

                    ZoomMtg.join({

                        sdkKey: sdkKey,

                        signature: signature,

                        meetingNumber: meetingNumber,

                        passWord: passWord,

                        userName: userName,

                        // zak: zakToken,

                        success: (success) => {

                            console.log(success);

                        },

                        error: (error) => {

                            console.log(error);

                        }

                    })

                },

                error: (error) => {

                    console.log(error);

                }

            })
        </script> --}}
    @endif
@endpush
