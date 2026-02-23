<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="generator" content="">

    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title')</title>
    <link rel="icon" href="{{ asset('frontend_assets/uploads/2023/04/cropped-logo-1-32x32.png') }}"
        sizes="32x32" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('user_assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.3/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.css"
        integrity="sha512-Woz+DqWYJ51bpVk5Fv0yES/edIMXjj3Ynda+KWTIkGoynAMHrqTcDUQltbipuiaD5ymEo9520lyoVOo9jCQOCA=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="{{ asset('user_assets/css/menu.css') }}" rel="stylesheet">
    <link id="themeColors" rel="stylesheet" href="{{ asset('user_assets/css/style.min.css') }}">
    <link href="{{ asset('user_assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('user_assets/css/responsive.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('user_assets/css/emojionearea.min.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .skiptranslate {
            display: none !important;
        }

        body {
            top: 0px !important;
        }

        .goog-logo-link {
            display: none !important;
        }

        .trans-section {
            margin: 100px;
        }
    </style>
      <style>
            /* ===== Coupon Slider / Ticker Bar ===== */
            .coupon_slider {
                background: linear-gradient(135deg, var(--main-color, #643271) 0%, #8b47a5 50%, var(--sec-color, #d98b1c) 100%);
                position: sticky;
                overflow: hidden;
                padding: 8px 40px 8px 10px;
                z-index: 1001;
                box-shadow: 0 2px 10px rgba(100, 50, 113, 0.3);
                top: 0;
            }

            .coupon-slider-close {
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                color: rgba(255, 255, 255, 0.7);
                cursor: pointer;
                font-size: 14px;
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.15);
                transition: all 0.3s ease;
                z-index: 2;
            }

            .coupon-slider-close:hover {
                background: rgba(255, 255, 255, 0.3);
                color: #fff;
                transform: translateY(-50%) scale(1.1);
            }

            .coupon-ticker-wrapper {
                overflow: hidden;
                width: 100%;
            }

            .coupon-ticker-track {
                display: flex;
                align-items: center;
                white-space: nowrap;
                animation: couponTickerScroll var(--ticker-duration, 30s) linear infinite;
                will-change: transform;
            }

            .coupon-ticker-track:hover {
                animation-play-state: paused;
            }

            @keyframes couponTickerScroll {
                0% {
                    transform: translateX(0);
                }

                100% {
                    transform: translateX(-50%);
                }
            }

            .coupon-ticker-item {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 0 40px;
                color: #fff;
                font-size: 13px;
                font-family: var(--main-font, 'Roboto', sans-serif);
                flex-shrink: 0;
            }

            .coupon-ticker-icon {
                color: var(--sec-color, #d98b1c);
                font-size: 15px;
                animation: couponIconPulse 2s ease-in-out infinite;
                filter: drop-shadow(0 0 4px rgba(217, 139, 28, 0.5));
            }

            @keyframes couponIconPulse {

                0%,
                100% {
                    transform: scale(1);
                }

                50% {
                    transform: scale(1.2) rotate(-10deg);
                }
            }

            .coupon-ticker-badge {
                background: rgba(255, 255, 255, 0.2);
                padding: 2px 8px;
                border-radius: 10px;
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                backdrop-filter: blur(4px);
                border: 1px solid rgba(255, 255, 255, 0.15);
            }

            .coupon-ticker-code {
                background: rgba(255, 255, 255, 0.2);
                padding: 2px 10px;
                border-radius: 4px;
                letter-spacing: 1.5px;
                font-size: 13px;
                font-weight: 700;
                border: 1px dashed rgba(255, 255, 255, 0.5);
                color: #ffe082;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .coupon-ticker-code:hover {
                background: rgba(255, 255, 255, 0.35);
                transform: scale(1.05);
                box-shadow: 0 0 8px rgba(255, 224, 130, 0.4);
            }

            .coupon-ticker-discount {
                color: #ffe082;
                font-weight: 700;
                font-size: 14px;
                text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            }

            .coupon-ticker-expiry {
                font-size: 11px;
                opacity: 0.8;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }

            .coupon-ticker-item::after {
                content: '';
                width: 4px;
                height: 4px;
                background: rgba(255, 255, 255, 0.5);
                border-radius: 50%;
                margin-left: 32px;
                flex-shrink: 0;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .coupon_slider {
                    padding: 6px 32px 6px 8px;
                }

                .coupon-ticker-item {
                    font-size: 11px;
                    padding: 0 25px;
                    gap: 6px;
                }

                .coupon-ticker-code {
                    font-size: 11px;
                    padding: 1px 6px;
                }

                .coupon-ticker-badge {
                    font-size: 9px;
                    padding: 1px 6px;
                }

                .coupon-ticker-expiry {
                    font-size: 10px;
                }
            }
        </style>
    @stack('styles')

</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-theme="blue_theme" data-layout="vertical" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        @include('user.includes.sidebar')

        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
            @include('user.includes.header')

            <!--  Header End -->


            @yield('content')


        </div>
        <div class="dark-transparent sidebartoggler"></div>
        @include('frontend.includes.google_translate')
        @include('frontend.includes.chatbot')
    </div>
    <script src="{{ asset('user_assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('user_assets/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('user_assets/js/bootstrap.bundle.min.js') }}"></script>
    <!--  core files -->
    <script src="{{ asset('user_assets/js/app.min.js') }}"></script>
    <script src="{{ asset('user_assets/js/app.init.js') }}"></script>
    <script src="{{ asset('user_assets/js/app-style-switcher.js') }}"></script>
    <script src="{{ asset('user_assets/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('user_assets/js/custom.js') }}"></script>
    <!--  current page js files -->
    <script src="{{ asset('user_assets/js/owl.carousel.min.js') }}"></script>
    <!-- <script src="js/apexcharts.min.js"></script> -->
    <!-- <script src="js/dashboard.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
    <script src="{{ asset('user_assets/js/emojionearea.min.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
    {{-- trippy cdn link --}}
    <script src="https://unpkg.com/popper.js@1"></script>
    <script src="https://unpkg.com/tippy.js@5"></script>
    {{-- trippy --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.34/moment-timezone-with-data.min.js"></script>

    <script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>

    <script src="{{ asset('user_assets/js/inapp-notification.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.js"></script>
    <script>
        $(function() {
            $('label:contains("*")').each(function() {
                const html = $(this).html().replace('*', '<span class="required-star">*</span>');
                $(this).html(html);
            });
        });
    </script>
    @php
        use App\Helpers\Helper;

        $panel_watermark_logo = Helper::getSettings()->PANEL_WATERMARK_LOGO ?? '';

        // dd( $panel_watermark_logo);

    @endphp

    <script>
        $('.bg_white_border').css({
            "background": "#fff url('" +
                "{{ $panel_watermark_logo ? asset($panel_watermark_logo) : asset('user_assets/images/banner_lion.png') }}' ) no-repeat center / 400px"
        });
    </script>

    <script>
        tippy("[data-tippy-content]", {
            allowHTML: true,
            placement: "bottom",
            theme: "light-theme",
        });

        toastr.options = {
            positionClass: "toast-bottom-right", // Position the toaster at the bottom right
            timeOut: "5000", // Duration for the message to stay (5 seconds)
            closeButton: true, // Option to show the close button
            progressBar: true, // Show a progress bar
        };
    </script>

    <script>
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}',
            authUserId: {{ auth()->user()->id }},
            authUserRole: "{{ auth()->user()->hasNewRole('SUPER ADMIN') ? 'admin' : 'user' }}",
            authTimeZone: "{{ auth()->user()->time_zone ?? 'UTC' }}",
            ipAddress: "{{ env('IP_ADDRESS') }}",
            socketPort: "{{ env('SOCKET_PORT') }}",
            storageUrl: "{{ Storage::url('') }}",
            assetUrls: {
                profileDummy: '{{ asset('user_assets/images/profile_dummy.png') }}',
                fileIcon: '{{ asset('user_assets/images/file.png') }}',
                groupDefaultImage: '{{ asset('user_assets/images/group.jpg') }}',
            },
            userInfo: {
                firstName: "{{ auth()->user()->first_name }}",
                middleName: "{{ auth()->user()->middle_name }}",
                lastName: "{{ auth()->user()->last_name }}",
                profilePicture: "{{ auth()->user()->profile_picture }}"
            },
            routes: {
                chatbotMessage: "{{ route('chatbot.message') }}",

                notificationList: "{{ route('notification.list') }}",
                notificationClear: "{{ route('notification.clear') }}",

                // chat routes
                chatLoad: "{{ route('chats.load') }}",
                chatSend: "{{ route('chats.send') }}",
                chatList: "{{ route('chats.chat-list') }}",
                chatClear: "{{ route('chats.clear') }}",
                chatRemove: "{{ route('chats.remove') }}",
                chatSeen: "{{ route('chats.seen') }}",
                chatNotification: "{{ route('chats.notification') }}",
                notificationRead: "{{ route('notification.read', ['type' => '__TYPE__', 'id' => '__ID__']) }}",

                // team chat routes
                teamChatLoad: "{{ route('team-chats.load') }}",
                teamChatSend: "{{ route('team-chats.send') }}",
                teamChatGroupList: "{{ route('team-chats.group-list') }}",
                teamChatGroupInfo: "{{ route('team-chats.group-info') }}",
                teamChatUpdateGroupImage: "{{ route('team-chats.update-group-image') }}",
                teamChatEditNameDes: "{{ route('team-chats.edit-name-des') }}",
                teamChatRemoveMember: "{{ route('team-chats.remove-member') }}",
                teamChatMakeAdmin: "{{ route('team-chats.make-admin') }}",
                teamChatExitFromGroup: "{{ route('team-chats.exit-from-group') }}",
                teamChatDeleteGroup: "{{ route('team-chats.delete-group') }}",
                teamChatRemoveChat: "{{ route('team-chats.remove-chat') }}",
                teamChatClearAllConversation: "{{ route('team-chats.clear-all-conversation') }}",
                teamChatSeen: "{{ route('team-chats.seen') }}",
                teamChatNotification: "{{ route('team-chats.notification') }}",
            }
        };
    </script>







    <script>
        @if (Session::has('message'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-bottom-right", // Change position to bottom right
                "timeOut": "3000", // Duration before it auto-closes
            }
            toastr.success("{{ session('message') }}");
        @endif

        @if (Session::has('error'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-bottom-right", // Change position to bottom right
                "timeOut": "3000",
            }
            toastr.error("{{ session('error') }}");
        @endif

        @if (Session::has('info'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-bottom-right", // Change position to bottom right
                "timeOut": "3000",
            }
            toastr.info("{{ session('info') }}");
        @endif

        @if (Session::has('warning'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-bottom-right", // Change position to bottom right
                "timeOut": "3000",
            }
            toastr.warning("{{ session('warning') }}");
        @endif
    </script>


    <script>
        $(document).ready(function() {
            $(".btn_all_open").on("click", function() {
                var target = $(this).data("target");
                $(target).toggleClass("active");
            });
        });

        function dltFun() {
            $(".box_slae").removeClass("active");
        }
    </script>


    <script>
        $(document).ready(function() {
            // Fetch the initial notification count
            getSidebarNotiCounts();

            // Set an interval to fetch the notification count every 5 seconds
            // setInterval(getSidebarNotiCounts, 5000);
        });

        function getSidebarNotiCounts() {
            $.ajax({
                url: "{{ route('unread.messages.count') }}",
                method: "GET",
                success: function(response) {
                    console.log('Notification count fetched successfully:', response);
                    if (response.status) {
                        if (response.data.total > 0) {
                            $('.count_chat_sidebar_count_all').show();
                            $('.count_chat_sidebar_count_all').text(response.data.total);
                        } else {
                            $('.count_chat_sidebar_count_all').hide();
                        }
                        if (response.data.chat > 0) {
                            $('.count_chat_sidebar_count_chat').show();
                            $('.count_chat_sidebar_count_chat').text(response.data.chat);
                        } else {
                            $('.count_chat_sidebar_count_chat').hide();
                        }
                        if (response.data.team_chat > 0) {
                            $('.count_chat_sidebar_count_team').show();
                            $('.count_chat_sidebar_count_team').text(response.data.team_chat);
                        } else {
                            $('.count_chat_sidebar_count_team').hide();
                        }
                        if (response.data.mail > 0) {
                            $('.count_chat_sidebar_count_mail').show();
                            $('.count_chat_sidebar_count_mail').text(response.data.mail);
                        } else {
                            $('.count_chat_sidebar_count_mail').hide();
                        }

                    } else {
                        console.error('Failed to fetch notification count');
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching notification count:', xhr);
                }
            });
        }
    </script>

    <script src="{{ asset('user_assets/js/file-upload-modal.js') }}"></script>
    <script src="{{ asset('user_assets/js/web-chat.js') }}"></script>
    <script src="{{ asset('user_assets/js/web-team-chat.js') }}"></script>


    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });
            let ip_address = "{{ env('IP_ADDRESS') }}";
            let socket_port = '{{ env('SOCKET_PORT') }}';
            let socket = io(ip_address + ':' + socket_port);
            var sender_id = {{ auth()->user()->id }};
            @if (auth()->user()->hasNewRole('SUPER ADMIN'))
                var role = 'admin';
            @else
                var role = 'user';
            @endif




            // function fetchLatestEmails() {
            //     $.ajax({
            //         url: '{{ route('mail.inbox-email-list') }}',
            //         method: 'GET',
            //         success: function(response) {

            //             $('#inbox-email-list-{{ auth()->id() }}').html(response.data);
            //         },
            //         error: function(xhr) {
            //             toastr.error('Failed to fetch latest emails.');
            //         }
            //     });
            // }

            // sendAdminNotification
            socket.on('sendAdminNotification', function(data) {
                getSidebarNotiCounts();
                if (data.user_id == sender_id) {
                    // get count notification
                    var count = $('#show-notification-count-' + sender_id).text();
                    count = parseInt(count);
                    count += 1;
                    $('#show-notification-count-' + sender_id).text(count);
                    var route =
                        `{{ route('notification.read', ['type' => 'Team', 'id' => '__ID__']) }}`
                        .replace('__ID__', data.notification.id);
                    var html = `<li>
                                    <a href="${route}" class="top-text-block">
                                        <div class="top-text-heading">${data.notification.message}</div>
                                        <div class="top-text-light">${moment(data.notification.created_at).fromNow()}</div>
                                    </a>
                                </li>`;
                    $('#show-notification-' + sender_id).prepend(html);
                }
            });



            // create bulletin
            $(document).on('submit', '#create-bulletin', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(resp) {
                        $('#create-bulletin')[0].reset();
                        socket.emit('showBulletin', {
                            'bulletin': resp.bulletin,
                        });
                        $('#loading').removeClass('loading');
                        $('#loading-content').removeClass('loading-content');
                        window.location.href = "{{ route('bulletins.index') }}";
                    },
                    error: function(xhr) {
                        $('#loading').removeClass('loading');
                        $('#loading-content').removeClass('loading-content');
                        $('.text-danger').html('');
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            if (key.includes('.')) {
                                var fieldName = key.split('.')[0];
                                // Display errors for array fields
                                var num = key.match(/\d+/)[0];
                                toastr.error(value[0]);
                            } else {
                                // after text danger span
                                toastr.error(value[0]);
                            }
                        });
                    }
                });
            });

            // update bulletin
            $(document).on('submit', '#update-bulletin', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(resp) {
                        $('#update-bulletin')[0].reset();
                        socket.emit('updateBulletin', {
                            'bulletin': resp.bulletin,
                        });
                        $('#loading').removeClass('loading');
                        $('#loading-content').removeClass('loading-content');
                        window.location.href = "{{ route('bulletins.index') }}";
                    },
                    error: function(xhr) {
                        $('#loading').removeClass('loading');
                        $('#loading-content').removeClass('loading-content');
                        $('.text-danger').html('');
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            if (key.includes('.')) {
                                var fieldName = key.split('.')[0];
                                // Display errors for array fields
                                var num = key.match(/\d+/)[0];
                                toastr.error(value[0]);
                            } else {
                                // after text danger span
                                toastr.error(value[0]);
                            }
                        });
                    }
                });
            });

            $(document).on('click', '#bulletin-delete', function(e) {
                swal({
                        title: "Are you sure?",
                        text: "To remove this bulletin from the bulletin board",
                        type: "warning",
                        confirmButtonText: "Yes",
                        showCancelButton: true
                    })
                    .then((result) => {
                        if (result.value) {
                            var bulletin_id = $(this).data('bulletin-id');
                            var url = $(this).data('route');
                            $.ajax({
                                type: "GET",
                                url: url,
                                data: {
                                    bulletin_id: bulletin_id,
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function(resp) {
                                    if (resp.status == true) {
                                        $('#single-bulletin-' + bulletin_id).remove();

                                        // socket emit
                                        socket.emit('deleteBulletin', {
                                            'bulletin': resp.bulletin,
                                        });

                                        swal(
                                            'Deleted!',
                                            'Bulletin has been deleted.',
                                            'success'
                                        )
                                    } else {
                                        swal(
                                            'Error!',
                                            'Something went wrong',
                                            'error'
                                        )
                                    }
                                },
                                error: function(xhr) {
                                    swal(
                                        'Error!',
                                        'Something went wrong',
                                        'error'
                                    )
                                }
                            });
                        } else if (result.dismiss === 'cancel') {
                            swal(
                                'Cancelled',
                                'Your stay here :)',
                                'error'
                            )
                        }
                    })
            });

            // load bulletin

            function loadBulletin() {
                $.ajax({
                    type: "POST",
                    url: "{{ route('bulletin-board.load') }}",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(resp) {
                        $('#show-bulletin').html(resp.view);
                    },
                    error: function(xhr) {
                        toastr.error('Something went wrong');
                    }
                });
            }

            function loadBulletinTable() {
                $.ajax({
                    type: "POST",
                    url: "{{ route('bulletins.load-table') }}",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(resp) {
                        $('#bulletin-table').html(resp.view);
                    },
                    error: function(xhr) {
                        toastr.error('Something went wrong');
                    }
                });
            }

            function loadSingleBulletin(bulletin_id) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('bulletins.single') }}",
                    data: {
                        bulletin_id: bulletin_id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(resp) {
                        $('#single-bulletin-' + bulletin_id).html(resp.view);
                    },
                    error: function(xhr) {
                        toastr.error('Something went wrong');
                    }
                });
            }

            function refreshMailList() {
                // The URL should point to a route that returns 'user.mail.partials.reply-mails'
                console.log('Refreshing mail list...');
                try {
                    $('#mail-details-reply-mails-list').load(window.location.href +
                        ' #mail-details-reply-mails-list > *');
                } catch (error) {
                    console.error('Error refreshing mail list:', error);

                }
            }

            // showBulletin
            socket.on('showBulletin', function(data) {
                loadBulletin();
                if (role == 'admin' && data.bulletin.user_id != sender_id) {
                    console.log('ads');
                    $('#load-bulletin').css('display', 'block');
                }
            });

            // updateBulletin
            socket.on('updateBulletin', function(data) {
                loadBulletin();
                if (role == 'admin' || data.bulletin.user_id == sender_id) {
                    $('#bulletin-title-' + data.bulletin.id).html(data.bulletin.title);
                    $('#bulletin-description-' + data.bulletin.id).html(data.bulletin.description);
                }
            });

            // deleteBulletin
            socket.on('deleteBulletin', function(data) {
                $('#single-bulletin-' + data.bulletin.id).remove();
                loadBulletin();
            });


            socket.on('send_mail', function(data) {

                getSidebarNotiCounts();
                var send_to_ids = data.send_to_ids;
                var notification_message = data.notification_message;

                // Check if sender_id exists in send_to_ids array
                if (send_to_ids.includes(sender_id)) {
                    // Get the current count of notifications for the sender
                    var countElement = $('#show-notification-count-' + sender_id);
                    var count = parseInt(countElement.text()) ||
                        0;

                    count += 1;
                    countElement.text(count);
                    fetchLatestEmails();

                    var viewMatch = window.location.pathname.match(/\/user\/mail\/view\/([^\/]+)/);
                    if (viewMatch && viewMatch[1]) {
                        console.log('viewMatch', viewMatch);
                        var encodedId = viewMatch[1];
                        // var mailId = atob(encodedId);
                        // Refresh the mail details and reply mails section
                        refreshMailList();
                    }
                }

            });



            // $(document).on('change', '#create-mail-file-input', function() {
            //     const fileNames = Array.from(this.files).map(file => {
            //         return `<span><i class="fa fa-paperclip"></i> ${file.name}</span>`; // Prepend icon to each file name
            //     });
            //     $('#create-mail-selected-file-names').html(fileNames.join(
            //         '<br>')); // Display file names with icons
            // });

            // $(document).on('change', '#reply-mail-file-input', function() {
            //     const fileNames = Array.from(this.files).map(file => {
            //         return `<span><i class="fa fa-paperclip"></i> ${file.name}</span>`; // Prepend icon to each file name
            //     });
            //     console.log(fileNames);
            //     $('#reply-mail-selected-file-names').html(fileNames.join(
            //         '<br>')); // Display file names with icons
            // });

            // $(document).on('change', '#forword-mail-file-input', function() {
            //     const fileNames = Array.from(this.files).map(file => {
            //         return `<span><i class="fa fa-paperclip"></i> ${file.name}</span>`; // Prepend icon to each file name
            //     });
            //     $('#forward-mail-selected-file-names').html(fileNames.join(
            //         '<br>')); // Display file names with icons
            // });










            $(document).on('submit', '#sendUserEMailForm', function(e) {
                e.preventDefault();

                var formData = new FormData(this); // Gather form data

                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false, // Set to false for file upload
                    success: function(response) {
                        $('#loading').removeClass('loading');
                        $('#loading-content').removeClass('loading-content');

                        if (response.status == true) {
                            dltFun(); // Call your custom function (if needed)
                            socket.emit("send_mail", {
                                send_to_ids: response.send_to_ids,
                                notification_message: response.notification_message
                            });

                            fetchLatestEmails();
                            toastr.success(response.message);
                            $('#sendUserEMailForm')[0].reset();
                            $('#selected-file-names').empty();
                            window.location.reload();


                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        $('#loading').removeClass('loading');
                        $('#loading-content').removeClass('loading-content');

                        const errors = xhr.responseJSON.errors;
                        if (errors) {
                            $.each(errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        } else {
                            toastr.error('An error occurred while sending the email.');
                        }
                    }
                });
            });


            $(document).on('submit', '#sendUserEMailReply', function(e) {
                e.preventDefault();

                var formData = new FormData(this); // Gather form data

                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');



                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false, // Set to false for file upload
                    success: function(response) {
                        $('#loading').removeClass('loading');
                        $('#loading-content').removeClass('loading-content');

                        if (response.status == true) {
                            dltFun(); // Call your custom function (if needed)
                            socket.emit("send_mail", {
                                send_to_ids: response.send_to_ids,
                                notification_message: response.notification_message
                            });

                            fetchLatestEmails();
                            toastr.success(response.message);
                            $('#sendUserEMailReply')[0].reset();
                            $('#reply-mail-selected-file-names').empty();

                            window.location.reload();

                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        $('#loading').removeClass('loading');
                        $('#loading-content').removeClass('loading-content');

                        const errors = xhr.responseJSON.errors;
                        if (errors) {
                            $.each(errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        } else {
                            toastr.error('An error occurred while sending the email.');
                        }
                    }
                });
            });


            $(document).on('submit', '#sendUserEMailForward', function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');



                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false, // Set to false for file upload
                    success: function(response) {
                        $('#loading').removeClass('loading');
                        $('#loading-content').removeClass('loading-content');

                        if (response.status == true) {
                            dltFun();
                            socket.emit("send_mail", {
                                send_to_ids: response.send_to_ids,
                                notification_message: response.notification_message
                            });

                            fetchLatestEmails();
                            toastr.success(response.message);
                            $('#sendUserEMailForward')[0].reset();
                            $('#forward-mail-selected-file-names').empty();
                            $('.mail_forward_reply_box').hide();
                            $('.mail_forward_reply_box').find('textarea').val('');
                            window.location.reload();

                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        $('#loading').removeClass('loading');
                        $('#loading-content').removeClass('loading-content');

                        const errors = xhr.responseJSON.errors;
                        if (errors) {
                            $.each(errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        } else {
                            toastr.error('An error occurred while sending the email.');
                        }
                    }
                });
            });







            // fetchLatestEmails();


        });




        let currentMailPage_inbox = 1;
        let currentMailPage_sent = 1;
        let currentMailPage_star = 1;
        let currentMailPage_trash = 1;


        function fetchLatestEmails(page = 1) {
            $.ajax({
                url: '{{ route('mail.inbox-email-list') }}',
                method: 'GET',
                data: {
                    page: page,
                    type: 'inbox'
                },
                success: function(response) {
                    $('#inbox-email-list-{{ auth()->id() }}').html(response.data);
                    // toastr.success("Latest Emails Fetched");
                    currentMailPage_inbox = page;
                    // console.log('the current page: ' + currentMailPage_inbox);

                    $('#paginationInfo').text(
                        `Page ${response.currentPage} of ${response.lastPage}`);
                    if (response.currentPage <= 1) {
                        $('#mailListPrevPage').addClass('hide');
                    } else {
                        $('#mailListPrevPage').removeClass('hide');
                    }
                    if (response.currentPage >= response.lastPage) {
                        $('#mailListNextPage').addClass('hide');
                    } else {
                        $('#mailListNextPage').removeClass('hide');
                    }

                    // Keep sidebar badge/count in sync after list refresh
                    if (typeof getSidebarNotiCounts === 'function') {
                        getSidebarNotiCounts();
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to fetch latest emails.');
                }
            });
        }

        function fetchSentEmails(page = 1) {
            $.ajax({
                url: '{{ route('mail.sent-email-list') }}',
                method: 'GET',
                data: {
                    page: page,
                    type: 'sent'
                },
                success: function(response) {

                    $('#sent-email-list-{{ auth()->id() }}').html(response.data);
                    //  toastr.success("Latest Emails Fetched");
                    currentMailPage_sent = page;
                    $('#paginationInfo').text(
                        `Page ${response.currentPage} of ${response.lastPage}`);
                    if (response.currentPage <= 1) {
                        $('#mailListPrevPage').addClass('hide');
                    } else {
                        $('#mailListPrevPage').removeClass('hide');
                    }
                    if (response.currentPage >= response.lastPage) {
                        $('#mailListNextPage').addClass('hide');
                    } else {
                        $('#mailListNextPage').removeClass('hide');
                    }

                    // Keep sidebar badge/count in sync after list refresh
                    if (typeof getSidebarNotiCounts === 'function') {
                        getSidebarNotiCounts();
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to fetch latest emails.');
                }
            });
        }

        function fetchStarEmails(page = 1) {
            $.ajax({
                url: '{{ route('mail.star-email-list') }}',
                method: 'GET',
                data: {
                    page: page,
                    type: 'star'
                },
                success: function(response) {

                    $('#star-email-list-{{ auth()->id() }}').html(response.data);
                    // toastr.success("Latest Emails Fetched");
                    currentMailPage_star = page;
                    $('#paginationInfo').text(
                        `Page ${response.currentPage} of ${response.lastPage}`);
                    if (response.currentPage <= 1) {
                        $('#mailListPrevPage').addClass('hide');
                    } else {
                        $('#mailListPrevPage').removeClass('hide');
                    }
                    if (response.currentPage >= response.lastPage) {
                        $('#mailListNextPage').addClass('hide');
                    } else {
                        $('#mailListNextPage').removeClass('hide');
                    }

                    // Keep sidebar badge/count in sync after list refresh
                    if (typeof getSidebarNotiCounts === 'function') {
                        getSidebarNotiCounts();
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to fetch latest emails.');
                }
            });
        }

        function fetchTrashEmails(page = 1) {
            $.ajax({
                url: '{{ route('mail.trash-email-list') }}',
                method: 'GET',
                data: {
                    page: page,
                    type: 'trash'
                },
                success: function(response) {

                    $('#trash-email-list-{{ auth()->id() }}').html(response.data);
                    // toastr.success("Latest Emails Fetched");
                    currentMailPage_trash = page;
                    $('#paginationInfo').text(
                        `Page ${response.currentPage} of ${response.lastPage}`);
                    if (response.currentPage <= 1) {
                        $('#mailListPrevPage').addClass('hide');
                    } else {
                        $('#mailListPrevPage').removeClass('hide');
                    }
                    if (response.currentPage >= response.lastPage) {
                        $('#mailListNextPage').addClass('hide');
                    } else {
                        $('#mailListNextPage').removeClass('hide');
                    }

                    // Keep sidebar badge/count in sync after list refresh
                    if (typeof getSidebarNotiCounts === 'function') {
                        getSidebarNotiCounts();
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to fetch latest emails.');
                }
            });
        }




        function setMailStar(element, mailid) {
            var materialIcon = element.querySelector('.material-symbols-outlined');
            var faIcon = element.querySelector('.fa-star');
            var star_val = 0;

            if (materialIcon) {
                // Currently starred (material icon with orange) -> unstar it
                // Replace with FA icon
                element.innerHTML = '<i class="fa-regular fa-star"></i>';
                star_val = 0;
            } else if (faIcon) {
                // Currently unstarred (FA icon) -> star it
                // Replace with material icon
                element.innerHTML =
                    '<span class="material-symbols-outlined" style="color: orange; font-variation-settings: \'FILL\' 1;">grade</span>';
                star_val = 1;
            }

            $.ajax({
                url: "{{ route('mail.star') }}",
                type: 'POST',
                data: {
                    mail_id: mailid,
                    start_value: star_val
                },
                success: function(response) {
                    if (response.status === true) {
                        toastr.success(response.message);
                        fetchLatestEmails();
                        fetchStarEmails();

                        // make sure the sidebar count is refreshed after star change
                        if (typeof getSidebarNotiCounts === 'function') {
                            getSidebarNotiCounts();
                        }
                    } else {
                        swal('Error', response.message, 'error');
                    }
                }
            });
        }


        function deleteSingleMail(mailid) {
            swal({
                    title: "Are you sure?",
                    text: "To remove this mail",
                    type: "warning",
                    confirmButtonText: "Yes",
                    showCancelButton: true
                })
                .then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: "{{ route('mail.deleteSingleMail') }}",
                            type: 'POST',
                            data: {
                                mail_id: mailid
                            },
                            success: function(response) {
                                if (response.status === true) {
                                    toastr.success(response.message);
                                    window.location.href = "{{ route('mail.index') }}";
                                } else {
                                    swal('Error', response.message, 'error');
                                }
                            }
                        });
                    } else if (result.dismiss === 'cancel') {
                        swal(
                            'Cancelled',
                            'Your stay here :)',
                            'error'
                        )
                    }
                })
        }

        function restoreSingleMail(mailid) {
            swal({
                    title: "Are you sure?",
                    text: "To restore this mail",
                    type: "warning",
                    confirmButtonText: "Yes",
                    showCancelButton: true
                })
                .then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: "{{ route('mail.restoreSingleMail') }}",
                            type: 'POST',
                            data: {
                                mail_id: mailid
                            },
                            success: function(response) {
                                if (response.status === true) {
                                    toastr.success(response.message);
                                    window.location.href = "{{ route('mail.index') }}";
                                } else {
                                    swal('Error', response.message, 'error');
                                }
                            }
                        });
                    } else if (result.dismiss === 'cancel') {
                        swal(
                            'Cancelled',
                            'Your stay here :)',
                            'error'
                        )
                    }
                })
        }


        function downloadMailFile(event, element) {
            event.preventDefault();

            const attachment = element.closest('.other_attch').querySelector('.existing-attachment');
            const fileName = attachment.getAttribute('data-name');
            const filePath = attachment.getAttribute('data-path');

            const link = document.createElement('a');
            link.href = filePath;
            link.download = fileName;

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
    <script>
        $(document).ready(function() {
            $('.sidebarOption').click(function() {
                var route = $(this).data('route');
                if (route) {
                    window.location.href = route;
                }

            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Select all textarea elements with the class 'ckeditor'
            const editors = document.querySelectorAll('textarea.ckeditor');

            editors.forEach((textarea) => {
                ClassicEditor
                    .create(textarea, {
                        // You can add configuration options here
                        toolbar: [
                            'heading',
                            '|',
                            'bold',
                            'italic',
                            'blockQuote',
                            'bulletedList',
                            'numberedList',
                            'link',
                            '|',
                            'undo',
                            'redo'
                            // Add any other desired toolbar items here
                        ],
                        // removePlugins: [
                        //     'ImageUpload', // To remove image upload feature
                        //     'Table', // To remove table feature
                        //     'MediaEmbed' // To remove media embed feature
                        // ]
                    })
                    .then(editor => {
                        console.log('CKEditor initialized for:', textarea);
                        editor.ui.view.editable.element.style.height = '250px';
                    })
                    .catch(error => {
                        console.error(error);
                    });
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Object to store selected files for each input, by input ID
            const selectedFilesMap = {};

            // Function to handle file input change and removal
            function handleFileInputChange(inputId, displayId) {
                selectedFilesMap[inputId] = []; // Initialize the selected files array for each file input

                // Handle file input change (file selection)
                document.getElementById(inputId).addEventListener('change', function(event) {
                    const newFiles = Array.from(event.target.files);

                    newFiles.forEach(file => {
                        // Add only unique files (by name) to avoid duplicates
                        if (!selectedFilesMap[inputId].some(f => f.name === file.name)) {
                            selectedFilesMap[inputId].push(file);
                        }
                    });

                    updateFileDisplay(displayId, selectedFilesMap[inputId], inputId);
                    resetFileInput(inputId, selectedFilesMap[inputId]);
                });
            }

            // Update the display with selected files and add remove buttons
            function updateFileDisplay(displayId, files, inputId) {
                const fileDisplay = files.map((file, index) => {
                    return `<span><i class="fa fa-paperclip"></i> ${file.name}
                    <button type="button" class="remove-file-btn btn btn-transparent" data-index="${index}" data-input-id="${inputId}">
                        <i class="fa fa-times"></i>
                    </button></span>`;
                }).join('<br>');

                document.getElementById(displayId).innerHTML = fileDisplay;

                // Attach click event to each remove button after rendering
                document.querySelectorAll(`#${displayId} .remove-file-btn`).forEach(button => {
                    button.addEventListener('click', function() {
                        const index = parseInt(button.getAttribute('data-index'));
                        const inputId = button.getAttribute('data-input-id');

                        // Remove the file from the selected files array
                        selectedFilesMap[inputId].splice(index, 1);

                        // Update display and file input
                        updateFileDisplay(displayId, selectedFilesMap[inputId], inputId);
                        resetFileInput(inputId, selectedFilesMap[inputId]);
                    });
                });
            }

            // Reset file input to match selected files (using DataTransfer)
            function resetFileInput(inputId, files) {
                const dataTransfer = new DataTransfer();
                files.forEach(file => dataTransfer.items.add(file));
                document.getElementById(inputId).files = dataTransfer.files;
            }

            function clearAllEmailFormFiles() {
                const formIds = [{
                        inputId: 'create-mail-file-input',
                        displayId: 'create-mail-selected-file-names'
                    },
                    {
                        inputId: 'reply-mail-file-input',
                        displayId: 'reply-mail-selected-file-names'
                    },
                    {
                        inputId: 'forward-mail-file-input',
                        displayId: 'forward-mail-selected-file-names'
                    }
                ];

                formIds.forEach(({
                    inputId,
                    displayId
                }) => {
                    // Clear the files from selectedFilesMap
                    selectedFilesMap[inputId] = [];

                    // Update display and reset file input to empty
                    updateFileDisplay(displayId, selectedFilesMap[inputId], inputId);
                    resetFileInput(inputId, selectedFilesMap[inputId]);
                });
            }

            $('.open_mail_reply_box').on('click', function(event) {
                event.preventDefault();
                $('.mail_send_reply_box').show(); // Show the reply box
                $('.mail_forward_reply_box').hide(); // Hide the forward box
                handleFileInputChange('reply-mail-file-input', 'reply-mail-selected-file-names');
                mailBodyGoBottom();
            });

            $('.open_mail_forward_box').on('click', function(event) {
                event.preventDefault();
                $('.mail_forward_reply_box').show(); // Show the forward box
                $('.mail_send_reply_box').hide(); // Hide the reply box
                handleFileInputChange('forword-mail-file-input', 'forward-mail-selected-file-names');
                mailBodyGoBottom();
            });

            function clearMailForm() {
                $('#sendUserEMailForm')[0].reset();
                clearAllEmailFormFiles();
                dltFun();
            }

            $('.close_mail_create_box').on('click', function(event) {
                event.preventDefault();
                $('#sendUserEMailForm')[0].reset();
                clearAllEmailFormFiles();
                dltFun();
            });

            $('.close_mail_reply_box').on('click', function(event) {
                event.preventDefault();
                $('.mail_send_reply_box').hide();
                $('.mail_send_reply_box').find('textarea').val('');
                clearAllEmailFormFiles();
            });

            $('.close_mail_forward_box').on('click', function(event) {
                event.preventDefault();
                $('.mail_forward_reply_box').hide();
                $('.mail_forward_reply_box').find('textarea').val('');
                clearAllEmailFormFiles();
            });


            // Initialize file handlers for each form
            handleFileInputChange('create-mail-file-input', 'create-mail-selected-file-names');
            //  handleFileInputChange('reply-mail-file-input', 'reply-mail-selected-file-names');
            //  handleFileInputChange('forword-mail-file-input', 'forward-mail-selected-file-names');
        });

        function mailBodyGoBottom() {
            var goBottom = document.getElementById("mail_body_div");
            goBottom.scrollTop = goBottom.scrollHeight;
        }
    </script>


    <script>
        // Reusable init function
        function initSummernote(selector = '.summernote') {
            $(selector).each(function() {
                var $el = $(this);

                // Skip if already initialized (prevents duplicate editors)
                if ($el.next('.note-editor').length) return;

                var height = $el.data('height') || 220;
                var placeholder = $el.attr('placeholder') || 'Write something nice...';

                $el.summernote({
                    placeholder: placeholder,
                    tabsize: 2,
                    height: height,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['fontname', ['fontname']],
                        ['fontsize', ['fontsize']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['codeview']]
                    ],
                    fontNames: ['Inter', 'Arial', 'Helvetica', 'Times New Roman'],
                    fontSizes: ['12', '14', '16', '18', '20', '24'],
                    callbacks: {
                        onInit: function() {
                            // make sure editor area inherits site font if needed
                            $(this).siblings('.note-editor').find('.note-editable').css('font-family',
                                '"Inter", Arial, sans-serif');
                        }
                    }
                });
            });
        }

        // Initialize on page load for all .summernote elements
        $(document).ready(function() {
            initSummernote('.summernote');
        });

        // Example: call this after you append new textarea(s) via AJAX/DOM
        // appendHtmlContainingTextareas(); // your code that adds new textarea
        // initSummernote('.summernote'); // re-run to initialize new ones

        // Optional: initialize a specific element only
        // initSummernote('#specific-textarea');

        // Destroy example (if you need to remove editor and revert to textarea)
        // $('#some-textarea').each(function(){
        //   if ($(this).next('.note-editor').length) $(this).summernote('destroy');
        // });
    </script>

     <script>
            $(document).ready(function() {
                // Close coupon slider
                $('#couponSliderClose').on('click', function() {
                    $('#couponSliderBar').slideUp(300);
                    sessionStorage.setItem('couponSliderClosed', '1');
                });

                // Check if slider was closed in this session
                // if (sessionStorage.getItem('couponSliderClosed') === '1') {
                //     $('#couponSliderBar').hide();
                // }

                // Adjust ticker animation speed based on number of items
                var itemCount = $('.coupon-ticker-item').length / 2; // half are duplicates
                var duration = Math.max(15, itemCount * 8); // minimum 15s, 8s per item
                document.documentElement.style.setProperty('--ticker-duration', duration + 's');

                // Copy coupon code to clipboard on click
                $(document).on('click', '.coupon-ticker-code', function() {
                    var code = $(this).text().trim();
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(code).then(function() {
                            toastr.success('Coupon code "' + code + '" copied to clipboard!');
                        });
                    } else {
                        // Fallback for older browsers
                        var $temp = $('<input>');
                        $('body').append($temp);
                        $temp.val(code).select();
                        document.execCommand('copy');
                        $temp.remove();
                        toastr.success('Coupon code "' + code + '" copied to clipboard!');
                    }
                });
            });
        </script>





    @stack('scripts')
    <!-- Download Progress Modal -->
    <div class="modal fade" id="downloadProgressModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Downloading</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="downloadFileName" class="mb-2"></p>
                    <div class="progress" style="height: 22px;">
                        <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0"
                            aria-valuemin="0" aria-valuemax="100">0%</div>
                    </div>
                </div>
                {{-- <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            id="downloadCancelBtn">Cancel</button>
                    </div> --}}
            </div>
        </div>
    </div>
</body>

</html>
