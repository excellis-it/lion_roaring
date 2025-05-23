<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
        <meta name="generator" content="Hugo 0.84.0">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>@yield('title')</title>
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
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
        <link rel="stylesheet" href="https://rawgit.com/mervick/emojionearea/master/dist/emojionearea.min.css">
        @stack('styles')
    </head>

    <body>
        <div class="page-wrapper" id="main-wrapper" data-theme="blue_theme" data-layout="vertical"
            data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
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

            <div class="chatbot-container">
                <div class="chatbot-btn" id="chatbotBtn">
                    <img src="{{ asset('user_assets/images/chat-bot.png') }}" class="img-fluid rounded-top"
                        alt="" />

                </div>

                <div class="chatbox" id="chatbox">
                    <div class="chatbox-header">
                        <span>Chat With Us</span>
                        <button class="close-btn" id="closeChatbox">&times;</button>
                    </div>
                    <div class="chatbox-body" id="chatboxBody">
                        <!-- Chat messages go here -->
                        <div class="chatbot-message chatbot-bot-message">
                            <p>Hi! How can I help you today?</p>
                        </div>

                    </div>
                    <div class="chatbox-footer">
                        <input type="text" id="chatbotuserInput" placeholder="Type a message...">
                        <button id="chatbotsendBtn">Send</button>
                    </div>
                </div>
            </div>

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
        <script src="https://rawgit.com/mervick/emojionearea/master/dist/emojionearea.js"></script>
        <script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
        {{-- trippy cdn link --}}
        <script src="https://unpkg.com/popper.js@1"></script>
        <script src="https://unpkg.com/tippy.js@5"></script>
        {{-- trippy --}}
        <script>
            tippy('[data-tippy-content]', {
                allowHTML: true,
                placement: 'bottom',
                theme: 'light-theme',
            });
        </script>
        <script>
            toastr.options = {
                "positionClass": "toast-bottom-right", // Position the toaster at the bottom right
                "timeOut": "5000", // Duration for the message to stay (5 seconds)
                "closeButton": true, // Option to show the close button
                "progressBar": true, // Show a progress bar
            }
        </script>

        <script>
            $(document).ready(function() {
                $('#chatbotBtn').click(function() {
                    $('#chatbox').fadeIn();
                });

                $('#closeChatbox').click(function() {
                    $('#chatbox').fadeOut();
                });

                $('#chatbotsendBtn').click(function() {
                    var message = $('#chatbotuserInput').val().trim();
                    if (message !== '') {
                        var userMessage = $('<div class="chatbot-message chatbot-user-message"><p>' + message +
                            '</p></div>');
                        $('#chatboxBody').append(userMessage);
                        $('#chatbotuserInput').val('');

                        $.ajax({
                            type: "POST",
                            url: "{{ route('chatbot.message') }}",
                            data: {
                                _token: '{{ csrf_token() }}',
                                message: message
                            },
                            dataType: "json",
                            success: function(response) {
                                var dataMessage = response.message;


                                setTimeout(function() {
                                    var botMessage = $(
                                        '<div class="chatbot-message chatbot-bot-message d-block"><p>' +
                                        dataMessage +
                                        '</p></div><span class="chatbot-bot-message-lable"> - Lion Roaring AI</span>'
                                    );
                                    $('#chatboxBody').append(botMessage);
                                    $('#chatboxBody').scrollTop($('#chatboxBody')[0]
                                        .scrollHeight); // Auto scroll to the bottom
                                }, 500);

                            }
                        });




                    }
                });
            });
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

                var notification_page = 1;
                var loading = false; // Prevents multiple simultaneous AJAX requests

                // remove notification dropdown when clicked outside
                $(document).on('click', function(e) {
                    if ($('#show-notification-{{ auth()->user()->id }} .showing').length > 0) {
                        if (!$(e.target).closest('#show-notification-{{ auth()->user()->id }}').length) {
                            $('.notification-dropdown').removeClass('show');
                            $('#show-notification-{{ auth()->user()->id }}').html(
                                ''); // Clear the notifications
                            notification_page = 1;
                        }
                    }
                });

                $(document).on('click', '#drop2', function() {
                    var $dropdown = $('.notification-dropdown');
                    if ($dropdown.hasClass('show')) {
                        // If the dropdown is already shown, hide it
                        $dropdown.removeClass('show');
                        $('#show-notification-{{ auth()->user()->id }}').html(''); // Clear the notifications
                        notification_page = 1;
                    } else {
                        $dropdown.addClass('show');
                        loadMoreNotification(notification_page, true);
                    }
                });

                $('#show-notification-{{ auth()->user()->id }}').on('scroll', function() {
                    loadingNotification();
                });

                function loadingNotification() {
                    if (loading) return; // Exit if a load is already in progress

                    var $container = $('#show-notification-{{ auth()->user()->id }}');
                    var lastItem = $('.message-body').last();
                    var lastItemOffset = lastItem.offset().top + lastItem.outerHeight();
                    var containerOffset = $container.scrollTop() + $container.innerHeight();

                    if (containerOffset >= lastItemOffset) {
                        loading = true;
                        notification_page++;
                        loadMoreNotification(notification_page, false);
                    }
                }

                function loadMoreNotification(page, initialLoad) {
                    loading = true;
                    if (!initialLoad) {
                        $('#show-notification-{{ auth()->user()->id }}').append('<div class="loader-topbar"></div>');
                    }
                    $.ajax({
                        url: "{{ route('notification.list') }}",
                        data: {
                            page: page
                        },
                        success: function(data) {
                            if (page === 1) {
                                $('#show-notification-{{ auth()->user()->id }}').html(data.view);
                            } else {
                                $('#show-notification-{{ auth()->user()->id }}').append(data.view);
                            }

                            if (data.count < 8) {
                                // Stop loading if there are fewer items than the threshold
                                $('#show-notification-{{ auth()->user()->id }}').off('scroll');
                            } else {
                                $('#show-notification-{{ auth()->user()->id }}').on('scroll', function() {
                                    loadingNotification();
                                });
                            }

                            loading = false;
                            $('.loader-topbar').remove();
                        },
                        error: function() {
                            loading = false;
                            $('.loader-topbar').remove();
                        }
                    });
                }
                // clear-all-notification
                $(document).on('click', '.clear-all-notification', function() {
                    var $this = $(this);
                    var $notification = $('#show-notification-{{ auth()->user()->id }}');
                    var $notificationCount = $('#show-notification-count-{{ auth()->user()->id }}');
                    var $notificationDropdown = $('.notification-dropdown');
                    var $notificationDropdownContent = $notificationDropdown.find('.message-body');

                    $.ajax({
                        url: "{{ route('notification.clear') }}",
                        success: function(data) {
                            if (data.status === true) {
                                $notification.html('');
                                $notificationCount.html('0');
                                $notificationDropdownContent.html('');
                                $notificationDropdown.removeClass('show');
                                notification_page = 1;
                                toastr.success(data.message);
                            }
                        }
                    });
                });
            });
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.34/moment-timezone-with-data.min.js"></script>

        <script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>
        <script>
            $(document).ready(function() {
                var sender_id = "{{ Auth::user()->id }}";
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                });
                let ip_address = "{{ env('IP_ADDRESS') }}";
                let socket_port = '3000';
                let socket = io(ip_address + ':' + socket_port);

                // $(document).on("click", ".user-list", function(e) {
                //     var getUserID = $(this).attr("data-id");
                //     receiver_id = getUserID;
                //     loadChats();
                //    // $(this).addClass("active").siblings().removeClass("active");
                //    $("#chat_list_user_"+getUserID).addClass("active").siblings().removeClass("active");
                // });



                $(document).on("click", ".user-list", function(e) {
                    var getUserID = $(this).attr("data-id");
                    receiver_id = getUserID;
                    loadChats();
                    // Remove "active" class from all user-list elements first
                    $(".user-list").removeClass("active");

                    socket.emit("read-chat", {
                        read_chat: 1,
                        receiver_id: receiver_id
                    });

                    // Add "active" class to the clicked element
                    $("#chat_list_user_" + getUserID).addClass("active");

                    $("#last_activate_user").val(getUserID);
                });



                function loadChats() {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('chats.load') }}",
                        data: {
                            _token: $("input[name=_token]").val(),
                            reciver_id: receiver_id,
                            sender_id: sender_id,
                        },
                        success: function(resp) {
                            if (resp.status === true) {
                                $(".chat-module").html(resp.view);

                                // unseen_chat count remove
                                $('#count-unseen-' + receiver_id).remove();

                                socket.emit("multiple_seen", {
                                    unseen_chat: resp.unseen_chat,
                                });



                                if (resp.chat_count > 0) {
                                    scrollChatToBottom(receiver_id);
                                }

                                // Initialize EmojiOneArea on MessageInput
                                var emojioneAreaInstance = $("#MessageInput").emojioneArea({
                                    pickerPosition: "top",
                                    filtersPosition: "top",
                                    tonesStyle: "bullet",
                                });

                                // Handle Enter key press within the emoji picker
                                emojioneAreaInstance[0].emojioneArea.on('keydown', function(editor, event) {


                                    const $messageInput = $('.emojionearea-editor');

                                    console.log('key is : ' + event.key);

                                    var message = $("#MessageInput").emojioneArea()[0].emojioneArea
                                        .getText();
                                    console.log('message is ' + message);

                                    if (event.key === 'Enter') {
                                        //
                                    } else {
                                        if (event.which === 13 && !event.shiftKey) {
                                            event.preventDefault();
                                            $("#MessageForm").submit();
                                        }
                                    }

                                    // var formattedMessage = message.replace(
                                    //     /\b[a-zA-Z0-9._-]+\.[a-zA-Z]{2,}\b/g,
                                    //     function(word) {
                                    //         var url = word.startsWith('http') ? word :
                                    //             'http://' + word; // Add http:// if not present
                                    //         return `<a href="${url}" target="_blank">${word}</a>`;
                                    //     });

                                    // var message = $("#MessageInput").emojioneArea()[0].emojioneArea
                                    //     .setText(formattedMessage);



                                });
                            } else {
                                toastr.error(resp.msg);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("An error occurred: " + status + "\nError: " + error);
                        }
                    });
                }




                function scrollChatToBottom(receiver_id) {
                    var messages = document.getElementById("chat-container-" + receiver_id);
                    messages.scrollTop = messages.scrollHeight;
                }



                $(document).on("submit", "#MessageForm", function(e) {
                    e.preventDefault();

                    // Get the message from the input field emoji area
                    var message = $("#MessageInput").emojioneArea()[0].emojioneArea.getText();
                    var receiver_id = $(".reciver_id").val();
                    //  var sender_id = $("input[name=sender_id]").val(); // Assuming sender_id is available
                    var url = "{{ route('chats.send') }}";

                    // Get the file data
                    var fileInput = $("#file2")[0];
                    var file = fileInput.files[0]; // The selected file

                    // // Format the message and replace words containing a dot with links
                    // var formattedMessage = message.replace(
                    //     /\b[a-zA-Z0-9._-]+\.[a-zA-Z]{2,}\b/g,
                    //     function(word) {
                    //         var link = word.startsWith('http') ? word : 'https://' +
                    //             word; // Add http:// if not present
                    //         return `<a class="text-decoration-underline" href="${link}" target="_blank">${word}</a>`;
                    //     }
                    // );

                    // // Set the formatted message back into the text area
                    // $("#MessageInput").emojioneArea()[0].emojioneArea.setText(formattedMessage);

                    // Create a FormData object to send both message and file
                    var formData = new FormData();
                    formData.append("_token", $("input[name=_token]").val());
                    formData.append("message", message);
                    formData.append("reciver_id", receiver_id);
                    formData.append("sender_id", sender_id);

                    // Append the file if one is selected
                    if (file) {
                        formData.append("file", file);
                    } else {
                        if (message.trim() == '') {
                            return false;
                        }
                    }
                    $('#loading').addClass('loading');
                    $('#loading-content').addClass('loading-content');

                    // Perform Ajax request to send the message to the server
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: formData,
                        processData: false, // Don't process the data
                        contentType: false,
                        success: function(res) {

                            if (res.success) {
                                $("#MessageInput").data("emojioneArea").setText("");
                                $("#file2").val('');
                                $('#file-name-display').hide();

                                let chat = res.chat.message;
                                let created_at = res.chat.created_at_formatted;
                                // use timezones to format the time America/New_York
                                let time_format_12 = moment(created_at, "YYYY-MM-DD HH:mm:ss")
                                    .format("hh:mm A");

                                let html = ` <div class="message me" id="chat-message-${res.chat.id}">
                                  <div class="message-wrap">`;
                                let fileUrl = '';
                                let attachment = res.chat.attachment;
                                if (attachment != '') {
                                    fileUrl = "{{ Storage::url('') }}" + attachment;
                                    let attachement_extention = attachment.split('.').pop();



                                    if (['jpg', 'jpeg', 'png', 'gif'].includes(
                                            attachement_extention)) {
                                        html +=
                                            ` <p class="messageContent"><a href="${fileUrl}" target="_blank"><img src="${fileUrl}" alt="attachment" style="max-width: 200px; max-height: 200px;"></a><br><span class="">${chat.replace(/\n/g, '<br>')}</span></p>`;
                                    } else if (['mp4', 'webm', 'ogg'].includes(
                                            attachement_extention)) {
                                        html +=
                                            ` <p class="messageContent"><a href="${fileUrl}" target="_blank"><video width="200" height="200" controls><source src="${fileUrl}" type="video/mp4"><source src="${fileUrl}" type="video/webm"><source src="${fileUrl}" type="video/ogg"></video></a><br><span class="">${chat.replace(/\n/g, '<br>')}</span></p>`;
                                    } else {
                                        html +=
                                            ` <p class="messageContent"><a href="${fileUrl}" download="${attachment}"><img src="{{ asset('user_assets/images/file.png') }}" alt=""></a><br><span class="">${chat.replace(/\n/g, '<br>')}</span></p>`;
                                    }
                                } else {
                                    html +=
                                        ` <p class="messageContent">${chat.replace(/\n/g, '<br>')}</p>`;
                                }

                                //   html +=` <p class="messageContent">${chat.replace(/\n/g, '<br>')}</p>`;


                                html += `<div class="dropdown">
                         <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1"
                             data-bs-toggle="dropdown" aria-expanded="false">
                             <i class="fa-solid fa-ellipsis-vertical"></i>
                         </button>
                         <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                             <li><a class="dropdown-item remove-chat" data-chat-id="${res.chat.id}" data-del-from="me">Remove For Me</a></li>
                                     <li><a class="dropdown-item remove-chat" data-chat-id="${res.chat.id}" data-del-from="everyone">Remove For Everyone</a></li>
                         </ul>
                     </div>
                     </div>
                                 <div class="messageDetails">
                                     <div class="messageTime">${time_format_12}</div>
                                     <div id="seen_${res.chat.id}">
                                     <i class="fas fa-check"></i>
                                     </div>
                                 </div>
                             </div>
                         `;
                                $('#message-app-' + receiver_id).html(chat);
                                if (res.chat_count > 0) {
                                    $("#chat-container-" + receiver_id).append(html);
                                    scrollChatToBottom(receiver_id);
                                } else {
                                    $("#chat-container-" + receiver_id).html(html);
                                }

                                var users = res.users;
                                $('#group-manage-' + sender_id).html('');
                                var new_html = '';
                                users.forEach(user => {
                                    // let timezone = 'America/New_York';
                                    let time_format_13 = user.last_message && user
                                        .last_message.created_at ?
                                        moment.tz(user.last_message.created_at)
                                        .format("hh:mm A") :
                                        '';

                                    new_html += `
                                 <li class="group user-list ${user.id == receiver_id ? 'active' : ''}" data-id="${user.id}">
                                     <div class="avatar">`;

                                    if (user.profile_picture) {
                                        new_html +=
                                            `<img src="{{ Storage::url('${user.profile_picture}') }}" alt="">`;
                                    } else {
                                        new_html +=
                                            `<img src="{{ asset('user_assets/images/profile_dummy.png') }}" alt="">`;
                                    }

                                    new_html += `</div>
                                     <p class="GroupName">${user.first_name} ${user.middle_name ? user.middle_name : ''} ${user.last_name ? user.last_name : ''}</p>
                                     <p class="GroupDescrp last-chat-${user.last_message ? user.last_message.id : ''}">${user.last_message && user.last_message.message ? user.last_message.message : ''}</p>
                                     <div class="time_online" id="last-chat-time-${user.last_message ? user.last_message.id : ''}">
                                         <p>${time_format_13}</p>
                                     </div>
                                 </li>`;
                                });

                                $('#group-manage-' + sender_id).append(new_html);
                                $('#loading').removeClass('loading');
                                $('#loading-content').removeClass('loading-content');
                                // Emit chat message to the server
                                socket.emit("chat", {
                                    message: message,
                                    sender_id: sender_id,
                                    receiver_id: receiver_id,
                                    receiver_users: res.receiver_users,
                                    chat_id: res.chat.id,
                                    file_url: fileUrl,
                                    created_at: res.chat.new_created_at
                                });
                            } else {
                                $('#loading').removeClass('loading');
                                $('#loading-content').removeClass('loading-content');
                                console.log(res.msg);
                            }
                        },
                    });
                });

                $("#hit-chat-file").click(function(e) {
                    e.preventDefault();
                    $("#file2").click();
                    $("#team-file2").click();
                });


                $(document).on("change", "#file2, #team-file2", function(e) {
                    var file = $(this).prop('files')[0]; // Get the selected file
                    var fileName = file?.name || ''; // Get the file name
                    var $fileNameDisplay = $('#file-name-display');

                    if (file && fileName) {
                        // Check if the file is an image
                        var isImage = file.type.startsWith('image/'); // Check if it's an image
                        var displayContent = '';

                        if (isImage) {
                            // Create an image preview
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                // Display image preview with remove button
                                displayContent = '<img src="' + e.target.result +
                                    '" alt="Image preview" style="max-width: 100px; max-height: 100px; margin-right: 10px;" />' +
                                    fileName +
                                    ' <i class="fas fa-times remove-file" style="cursor: pointer; color: red; margin-left: 10px;"></i>';
                                $fileNameDisplay.html(displayContent).show();
                            };
                            reader.readAsDataURL(file); // Read the file as a data URL
                        } else {
                            // Display default file icon with remove button
                            displayContent = '<i class="fas fa-file"></i> ' + fileName +
                                ' <i class="fas fa-times remove-file" style="cursor: pointer; color: red; margin-left: 10px;"></i>';
                            $fileNameDisplay.html(displayContent).show();
                        }
                    } else {
                        $fileNameDisplay.hide(); // Hide if no file selected
                    }
                });

                // Remove file on click of cross icon
                $(document).on("click", ".remove-file", function() {
                    var $fileNameDisplay = $('#file-name-display');
                    $fileNameDisplay.hide(); // Hide the file display area

                    // Clear the file input
                    $('#file2').val('');
                    $('#team-file2').val('');
                });





                $(document).on("change", "#file", function(e) {
                    var file = e.target.files[0];
                    var receiver_id = $(".reciver_id").val();
                    var formData = new FormData();
                    formData.append('file', file);
                    formData.append('_token', $("meta[name='csrf-token']").attr(
                        'content')); // Retrieve CSRF token from meta tag
                    formData.append('reciver_id', receiver_id);
                    formData.append('sender_id', sender_id);
                    $('#loading').addClass('loading');
                    $('#loading-content').addClass('loading-content');
                    $.ajax({
                        type: "POST",
                        url: "{{ route('chats.send') }}",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            if (res.success) {
                                let attachment = res.chat.attachment;
                                let fileUrl = "{{ Storage::url('') }}" + attachment;
                                let attachement_extention = attachment.split('.').pop();
                                let created_at = res.chat.created_at;
                                // let timeZome = 'America/New_York';
                                let time_format_12 = moment.tz(created_at).format(
                                    "hh:mm A");
                                let html = `<div class="message me">`;
                                if (['jpg', 'jpeg', 'png', 'gif'].includes(attachement_extention)) {
                                    html +=
                                        ` <div class="message-wrap"><p class="messageContent"><a href="${fileUrl}" target="_blank"><img src="${fileUrl}" alt="attachment" style="max-width: 200px; max-height: 200px;"></a></p>`;
                                } else if (['mp4', 'webm', 'ogg'].includes(attachement_extention)) {
                                    html +=
                                        ` <div class="message-wrap"><p class="messageContent"><a href="${fileUrl}" target="_blank"><video width="200" height="200" controls><source src="${fileUrl}" type="video/mp4"><source src="${fileUrl}" type="video/webm"><source src="${fileUrl}" type="video/ogg"></video></a></p>`;
                                } else {
                                    html +=
                                        ` <div class="message-wrap"><p class="messageContent"><a href="${fileUrl}" download="${attachment}"><img src="{{ asset('user_assets/images/file.png') }}" alt=""></a></p>`;
                                }

                                html +=
                                    ` <div class="dropdown">
                         <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1"
                             data-bs-toggle="dropdown" aria-expanded="false">
                             <i class="fa-solid fa-ellipsis-vertical"></i>
                         </button>
                         <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                             <li><a class="dropdown-item remove-chat" data-chat-id="${res.chat.id}" data-del-from="me">Remove For Me</a></li>
                                     <li><a class="dropdown-item remove-chat" data-chat-id="${res.chat.id}" data-del-from="everyone">Remove For Everyone</a></li>
                         </ul>
                     </div></div><div class="messageDetails"><div class="messageTime">${time_format_12}</div>
                                 <div id="seen_${res.chat.id}">
                                 <i class="fas fa-check">
                                     </i>
                                 </div>
                                 </div></div>`;

                                if (res.chat_count > 0) {
                                    $("#chat-container-" + receiver_id).append(html);
                                    scrollChatToBottom(receiver_id);
                                } else {
                                    $("#chat-container-" + receiver_id).html(html);
                                }

                                // Update the user list
                                var users = res.users;
                                $('#group-manage-' + sender_id).html('');
                                var new_html = '';
                                users.forEach(user => {
                                    // let timeZome = 'America/New_York';
                                    let time_format_13 = user.last_message && user
                                        .last_message.created_at ? moment.tz(user
                                            .last_message
                                            .created_at).format(
                                            "hh:mm A") : '';

                                    new_html +=
                                        `<li class="group user-list ${user.id == receiver_id ? 'active' : ''}" data-id="${user.id}"><div class="avatar">`;

                                    if (user.profile_picture) {
                                        new_html +=
                                            `<img src="{{ Storage::url('${user.profile_picture}') }}" alt="">`;
                                    } else {
                                        new_html +=
                                            `<img src="{{ asset('user_assets/images/profile_dummy.png') }}" alt="">`;
                                    }

                                    new_html +=
                                        `</div><p class="GroupName">${user.first_name} ${user.middle_name ? user.middle_name : ''} ${user.last_name ? user.last_name : ''}</p><p class="GroupDescrp last-chat-${user.last_message ? user.last_message.id : ''}">${user.last_message && user.last_message.message ? user.last_message.message : ''}</p><div class="time_online" id="last-chat-time-${user.last_message ? user.last_message.id : ''}"><p>${time_format_13}</p></div></li>`;
                                });

                                $('#group-manage-' + sender_id).append(new_html);
                                $('#loading').removeClass('loading');
                                $('#loading-content').removeClass('loading-content');

                                socket.emit("chat", {
                                    message: file.name,
                                    file_url: fileUrl,
                                    sender_id: sender_id,
                                    receiver_id: receiver_id,
                                    receiver_users: res.receiver_users,
                                    chat_id: res.chat.id,
                                    created_at: res.chat.new_created_at
                                });
                            } else {
                                $('#loading').removeClass('loading');
                                $('#loading-content').removeClass('loading-content');
                                console.log(res.msg);
                            }
                        }
                    });
                });

                function setChatListLastActive(activeid = 0) {
                    if (activeid != 0) {
                        var lastActiveUserId = $("#last_activate_user").val();
                    } else {
                        var lastActiveUserId = $("#last_activate_user").val();
                    }

                    if (lastActiveUserId && lastActiveUserId != '0') {
                        $("#chat_list_user_" + lastActiveUserId).addClass("active");
                        // alert('active set done');
                    }
                }

                // load left chat list

                function load_chat_list() {
                    var lastActiveUserId = $("#last_activate_user").val();
                    $.ajax({
                        type: "GET",
                        url: "{{ route('chats.chat-list') }}",
                        data: {
                            _token: $("input[name=_token]").val(),

                        },
                        success: function(res) {
                            if (res) {
                                $(".main-sidebar-chat-list").html(res);

                                setChatListLastActive(lastActiveUserId);



                            } else {
                                console.log(res.msg);
                            }
                        }
                    });
                }


                // clear-chat

                $(document).on("click", ".clear-chat", function(e) {
                    var receiver_id = $(this).data("reciver-id");
                    r = confirm("Are you sure you want to clear chat?");
                    if (r == false) {
                        return false;
                    } else {
                        $.ajax({
                            type: "POST",
                            url: "{{ route('chats.clear') }}",
                            data: {
                                _token: $("input[name=_token]").val(),
                                reciver_id: receiver_id,
                                sender_id: sender_id,
                            },
                            success: function(res) {
                                if (res.success) {
                                    $("#chat-container-" + receiver_id).html("");
                                    $("#message-app-" + receiver_id).html("");

                                    //socket.emit("clear-chat", {
                                    //   receiver_id: receiver_id,
                                    // sender_id: sender_id,
                                    // });

                                } else {
                                    console.log(res.msg);
                                }
                            }
                        });
                    }

                });

                //remove-chat
                $(document).on("click", ".remove-chat", function(e) {
                    var chat_id = $(this).data("chat-id");
                    var del_from = $(this).data("del-from");
                    r = confirm("Are you sure you want to remove chat?");
                    if (r == false) {
                        return false;
                    } else {
                        $.ajax({
                            type: "POST",
                            url: "{{ route('chats.remove') }}",
                            data: {
                                _token: $("input[name=_token]").val(),
                                chat_id: chat_id,
                                del_from: del_from,
                            },
                            success: function(res) {
                                if (res.status == true) {
                                    if (del_from == 'me') {
                                        $("#chat-message-" + chat_id).remove();
                                        $("#last-chat-time-" + chat_id).remove();
                                        $('.last-chat-' + chat_id).html('');
                                    } else {
                                        $("#chat-message-" + chat_id).remove();
                                        $("#last-chat-time-" + chat_id).remove();
                                        $('.last-chat-' + chat_id).html('');

                                        socket.emit("remove-chat", {
                                            chat: res.chat,
                                        });

                                        //   socket.emit('chat', res.chat.message);
                                    }
                                } else {
                                    console.log(res.msg);
                                }
                            }
                        });
                    }

                });

                //remove-chat
                socket.on('remove-chat', function(data) {
                    // console.log(data);
                    if (data.chat.reciver_id == sender_id) {
                        $("#chat-message-" + data.chat.id).remove();
                        $("#last-chat-time-" + data.chat.id).remove();
                        $('.last-chat-' + data.chat.id).html('');

                        // let unseenCountElement = $("#count-unseen-" + data.chat.sender_id);
                        // let unseenCount = parseInt(unseenCountElement.find('p').text().trim(), 10);

                        // if (unseenCountElement.length > 0) {
                        //     // Get the unseen count, trimming any whitespace around the text
                        //     let unseenCount = parseInt(unseenCountElement.find('p').text().trim(), 10);
                        //     console.log('unseen count is ' + unseenCount);

                        //     // Ensure the count is valid and greater than zero
                        //     if (!isNaN(unseenCount) && unseenCount > 0) {
                        //         unseenCount -= 1;
                        //         unseenCountElement.find('p').text(unseenCount);
                        //     }
                        // }

                        // load_chat_list();

                    }
                    load_chat_list();
                });

                // clear-chat
                socket.on('clear-chat', function(data) {

                    if (data.reciver_id == sender_id) {
                        $("#chat-container-" + data.sender_id).html("");
                        $("#message-app-" + data.sender_id).html("");
                        load_chat_list();
                    }
                });


                socket.on('read-chat', function(data) {
                    load_chat_list();
                });





                // Listen for incoming chat messages from the server
                socket.on("chat", function(data) {
                    let timeZome = '{{auth()->user()->time_zone}}';

                    console.log(timeZome);


                    load_chat_list();
                    setChatListLastActive();
                    html = `
                         <div class="message you" id="chat-message-${data.chat_id}">
                             <p class="messageContent">`;

                    let attachment = data.file_url;
                    if (attachment != '') {
                        let attachement_extention = attachment.split('.').pop();



                        if (['jpg', 'jpeg', 'png', 'gif'].includes(
                                attachement_extention)) {
                            html +=
                                ` <a href="${data.file_url}" target="_blank"><img src="${data.file_url}" alt="attachment" style="max-width: 200px; max-height: 200px;"></a><br><span class="">${data.message.replace(/\n/g, '<br>')}</span>`;
                        } else if (['mp4', 'webm', 'ogg'].includes(
                                attachement_extention)) {
                            html +=
                                ` <a href="${data.file_url}" target="_blank"><video width="200" height="200" controls><source src="${data.file_url}" type="video/mp4"><source src="${data.file_url}" type="video/webm"><source src="${data.file_url}" type="video/ogg"></video></a><br><span class="">${data.message.replace(/\n/g, '<br>')}</span>`;
                        } else {
                            html +=
                                ` <a href="${data.file_url}" download="${data.message}"><img src="{{ asset('user_assets/images/file.png') }}" alt=""></a><br><span class="">${data.message.replace(/\n/g, '<br>')}</span>`;
                        }
                    } else {
                        html +=
                            ` ${data.message.replace(/\n/g, '<br>')}`;
                    }
                    console.log(moment.tz(data.created_at).format("hh:mm A"));
                    html += `</p>
                        <div class="messageDetails">
                                 <div class="messageTime">${ moment.tz(data.created_at, timeZome).format("hh:mm A")}</div>
                             </div>
                         </div>
                     `;
                    if (data.receiver_id == sender_id) {
                        if ($(".chat-module").length > 0) {
                            if ($("#chat-container-" + data.sender_id).length > 0) {
                                $("#chat-container-" + data.sender_id).append(html);
                                scrollChatToBottom(data.sender_id);
                                // remove unseen count
                                $('#count-unseen-' + data.sender_id).remove();
                                // seen message
                                $.ajax({
                                    type: "POST",
                                    url: "{{ route('chats.seen') }}",
                                    data: {
                                        _token: $("input[name=_token]").val(),
                                        reciver_id: data.sender_id,
                                        sender_id: sender_id,
                                        chat_id: data.chat_id,
                                    },
                                    success: function(res) {
                                        if (res.status == true) {
                                            socket.emit("seen", {
                                                last_chat: res.last_chat,
                                            });
                                        } else {
                                            console.log(res.msg);
                                        }
                                    }
                                });

                            }
                        }

                        $('#message-app-' + data.sender_id).html(data.message);
                        var users = data.receiver_users;
                        $('#group-manage-' + sender_id).html('');
                        var new_html = '';
                        //             users.forEach(user => {
                        //                 // Check if last_message exists and has a created_at property
                        //                 let timeZome = 'America/New_York';
                        //                 let time_format_13 = user.last_message && user.last_message.created_at ?
                        //                     moment.tz(user.last_message.created_at, timeZome).format(
                        //                         "hh:mm A") :
                        //                     '';

                        //                 new_html += `
                //  <li class="group user-list ${user.id == data.sender_id ? '' : ''}" id="chat_list_user_${user.id}" data-id="${user.id}">
                //      <div class="avatar">`;

                        //                 if (user.profile_picture) {
                        //                     new_html +=
                        //                         `<img src="{{ Storage::url('${user.profile_picture}') }}" alt="">`;
                        //                 } else {
                        //                     new_html +=
                        //                         `<img src="{{ asset('user_assets/images/profile_dummy.png') }}" alt="">`;
                        //                 }

                        //                 new_html += `</div>
                //  <p class="GroupName">${user.first_name} ${user.middle_name ? user.middle_name : ''} ${user.last_name ? user.last_name : ''}</p>
                //  <p class="GroupDescrp last-chat-${user.last_message ? user.last_message.id : ''}">${user.last_message && user.last_message.message ? user.last_message.message : ''}</p>
                //  <div class="time_online" id="last-chat-time-${user.last_message ? user.last_message.id : ''}">
                //      <p>${time_format_13}</p>
                //  </div>`
                        //                 if (user.id == data.sender_id) {
                        //                     new_html += `<div class="count-unseen" id="count-unseen-${user.id}">
                //      <span><p>${user.unseen_chat}</p></span>
                //  </div>`;
                        //                 }
                        //                 new_html += `</li>`;
                        //             });

                        // $('#group-manage-' + sender_id).append(new_html);
                        // load_chat_list();

                        // if (new_html) {
                        //     setChatListLastActive();
                        // }



                    }

                    if (data.receiver_id == sender_id) {
                        if ($(".chat-module").length > 0) {
                            if ($("#chat-container-" + data.sender_id).length > 0) {
                                $('#count-unseen-' + data.sender_id).remove();
                                $.ajax({
                                    type: "POST",
                                    url: "{{ route('chats.notification') }}",
                                    data: {
                                        _token: $("input[name=_token]").val(),
                                        user_id: sender_id,
                                        sender_id: data.sender_id, // sender_id
                                        chat_id: data.chat_id,
                                        is_delete: true
                                    },
                                    success: function(res) {}
                                });

                            } else {
                                $.ajax({
                                    type: "POST",
                                    url: "{{ route('chats.notification') }}",
                                    data: {
                                        _token: $("input[name=_token]").val(),
                                        user_id: sender_id,
                                        sender_id: data.sender_id, // sender_id
                                        chat_id: data.chat_id
                                    },
                                    success: function(res) {
                                        console.log('go');

                                        if (res.status == true) {
                                            $('#show-notification-count-' + sender_id).html(res
                                                .notification_count);
                                            var route =
                                                `{{ route('notification.read', ['type' => 'Chat', 'id' => '__ID__']) }}`
                                                .replace('__ID__', res.notification.id);
                                            var html = `<li>
                                                 <a href="${route}" class="top-text-block">
                                                     <div class="top-text-heading">${res.notification.message}</div>
                                                     <div class="top-text-light">${moment(res.notification.created_at).fromNow()}</div>
                                                 </a>
                                             </li>`;
                                            $('#show-notification-' + sender_id).prepend(
                                                html
                                            ); // Use prepend to add new notification at the top
                                        } else {
                                            console.log(res.msg);
                                        }


                                    }
                                });
                            }
                        } else {
                            $.ajax({
                                type: "POST",
                                url: "{{ route('chats.notification') }}",
                                data: {
                                    _token: $("input[name=_token]").val(),
                                    user_id: sender_id,
                                    sender_id: data.sender_id, // sender_id
                                    chat_id: data.chat_id,
                                },
                                success: function(res) {
                                    console.log('yes');

                                    if (res.status == true) {
                                        $('#show-notification-count-' + sender_id).html(res
                                            .notification_count);
                                        var route =
                                            `{{ route('notification.read', ['type' => 'Chat', 'id' => '__ID__']) }}`
                                            .replace('__ID__', res.notification.id);
                                        var html = `<li>
                                                 <a href="${route}" class="top-text-block">
                                                     <div class="top-text-heading">${res.notification.message}</div>
                                                     <div class="top-text-light">${moment(res.notification.created_at).fromNow()}</div>
                                                 </a>
                                             </li>`;
                                        $('#show-notification-' + sender_id).prepend(
                                            html
                                        ); // Use prepend to add new notification at the top
                                    } else {
                                        console.log(res.msg);
                                    }


                                }
                            });
                        }
                    } else {
                        $.ajax({
                            type: "POST",
                            url: "{{ route('chats.notification') }}",
                            data: {
                                _token: $("input[name=_token]").val(),
                                user_id: data.receiver_id,
                                sender_id: data.sender_id, // sender_id
                                chat_id: data.chat_id,
                            },
                            success: function(res) {
                                if (res.status == true) {
                                    $('#show-notification-count-' + res.notification.user_id).html(
                                        res
                                        .notification_count);
                                    var route =
                                        `{{ route('notification.read', ['type' => 'Chat', 'id' => '__ID__']) }}`
                                        .replace('__ID__', res.notification.id);
                                    var html = `<li>
                                                 <a href="${route}" class="top-text-block">
                                                     <div class="top-text-heading">${res.notification.message}</div>
                                                     <div class="top-text-light">${moment(res.notification.created_at).fromNow()}</div>
                                                 </a>
                                             </li>`;
                                    $('#show-notification-' + res.notification.user_id).prepend(
                                        html
                                    ); // Use prepend to add new notification at the top
                                }

                            }
                        });
                    }
                    // load_chat_list();
                });

                // seen message
                socket.on("seen", function(data) {
                    if (sender_id == data.last_chat.sender_id) {
                        $("#seen_" + data.last_chat.id).html(
                            '<i class="fas fa-check-double"></i>'
                        );
                        load_chat_list();
                    }
                });

                //multiple_seen
                socket.on("multiple_seen", function(data) {
                    data.unseen_chat.forEach(function(chat) {
                        if (sender_id == chat.sender_id) {
                            $("#seen_" + chat.id).html(
                                '<i class="fas fa-check-double"></i>'
                            );
                        }
                    });
                    load_chat_list();
                });
            });
        </script>
        <script>
            $(document).ready(function() {

                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                });
                let ip_address = "{{ env('IP_ADDRESS') }}";
                let socket_port = '3000';
                let socket = io(ip_address + ':' + socket_port);
                var sender_id = {{ auth()->user()->id }};
                @if (auth()->user()->hasRole('SUPER ADMIN'))
                    var role = 'admin';
                @else
                    var role = 'user';
                @endif

                $('#create-team').submit(function(e) {
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
                            toastr.success(resp.message);
                            var data = resp.team;
                            var group_image = data.group_image;


                            groupList(sender_id);
                            // reset form
                            $('#create-team')[0].reset();

                            $('#previewImage01').attr('src',
                                '{{ asset('user_assets/images/group.jpg') }}');
                            $('#exampleModalToggle').modal('hide');

                            $('#loading').removeClass('loading');
                            $('#loading-content').removeClass('loading-content');
                            // Send message to socket
                            socket.emit('createTeam', {
                                user_id: sender_id,
                                chat_member_id: resp.chat_member_id
                            });
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

                function loadChat(teamId) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('team-chats.load') }}",
                        data: {
                            team_id: teamId,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(resp) {
                            $('.chat-body').html(resp.view);
                            scrollChatToBottom(teamId);
                            // remove unseen count
                            $('#count-team-unseen-' + teamId).html(``);

                            // Initialize EmojiOneArea on MessageInput
                            var emojioneAreaInstance = $("#TeamMessageInput").emojioneArea({
                                pickerPosition: "top",
                                filtersPosition: "top",
                                tonesStyle: "bullet"
                            });

                            // Handle Enter key press within the emoji picker
                            emojioneAreaInstance[0].emojioneArea.on('keydown', function(editor, event) {
                                const $messageInput = $('.emojionearea-editor');

                                console.log('key is : ' + event.key);

                                if (event.key === 'Enter') {
                                    //
                                } else {
                                    if (event.which === 13 && !event.shiftKey) {
                                        event.preventDefault();
                                        $("#TeamMessageForm").submit();
                                    }
                                }
                            });

                            // scrollChatToBottom(teamId);
                        },
                        error: function(xhr) {
                            toastr.error('Something went wrong');
                        }
                    });
                }

                $(document).on('click', '.group-data', function() {
                    var teamId = $(this).data('id');
                    loadChat(teamId);
                    $(this).addClass("active").siblings().removeClass("active");
                });

                function scrollChatToBottom(team_id) {
                    var messages = document.getElementById("team-chat-container-" + team_id);
                    if (messages) {
                        messages.scrollTop = messages.scrollHeight;
                    } else {
                        console.error("Element with ID 'team-chat-container-" + team_id + "' not found.");
                    }
                }

                $(document).on("change", "#team-file", function(e) {
                    var file = e.target.files[0];
                    var team_id = $(this).data('team-id');
                    var formData = new FormData();
                    formData.append('file', file);
                    formData.append('_token', $("meta[name='csrf-token']").attr(
                        'content')); // Retrieve CSRF token from meta tag
                    formData.append('team_id', team_id);

                    $.ajax({
                        type: "POST",
                        url: "{{ route('team-chats.send') }}",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            if (res.status == true) {
                                groupList(sender_id);
                                let attachment = res.chat.attachment;
                                let fileUrl = "{{ Storage::url('') }}" + attachment;
                                let attachement_extention = attachment.split('.').pop();
                                let created_at = res.chat.created_at;
                                // let timeZome = 'America/New_York';
                                let time_format_12 = moment.tz(created_at).format(
                                    "hh:mm A");
                                let html =
                                    `<div class="message me" id="team-chat-message-${res.chat.id}">`;
                                if (['jpg', 'jpeg', 'png', 'gif'].includes(attachement_extention)) {
                                    html +=
                                        `<div class="message-wrap"><p class="messageContent"><a href="${fileUrl}" target="_blank"><img src="${fileUrl}" alt="attachment" style="max-width: 200px; max-height: 200px;"></a></p>`;
                                } else if (['mp4', 'webm', 'ogg'].includes(attachement_extention)) {
                                    html +=
                                        ` <div class="message-wrap"><p class="messageContent"><a href="${fileUrl}" target="_blank"><video width="200" height="200" controls><source src="${fileUrl}" type="video/mp4"><source src="${fileUrl}" type="video/webm"><source src="${fileUrl}" type="video/ogg"></video></a></p>`;
                                } else {
                                    html +=
                                        `<div class="message-wrap"><p class="messageContent"><a href="${fileUrl}" download="${attachment}"><img src="{{ asset('user_assets/images/file.png') }}" alt=""></a></p>`;
                                }

                                html +=
                                    `<div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li><a class="dropdown-item team-remove-chat" data-chat-id="${res.chat.id}" data-del-from="me" data-team-id="${res.chat.team_id}">Remove For Me</a></li>
                                            <li><a class="dropdown-item team-remove-chat" data-chat-id="${res.chat.id}" data-del-from="everyone" data-team-id="${res.chat.team_id}">Remove For Everyone</a></li>
                                </ul>
                            </div></div><div class="messageDetails"><div class="messageTime">${time_format_12}</div></div></div>`;
                                $('#team-chat-container-' + team_id).append(html);
                                scrollChatToBottom(team_id);

                                // Send message to socket
                                socket.emit('sendTeamMessage', {
                                    chat: res.chat,
                                    file_url: fileUrl,
                                    chat_member_id: res.chat_member_id,
                                    created_at: res.chat.new_created_at
                                });
                            } else {
                                console.log(res.msg);
                            }
                        }
                    });
                });

                $(document).on("submit", "#TeamMessageForm", function(e) {
                    e.preventDefault();
                    // var message = $("#TeamMessageInput").emojioneArea()[0].emojioneArea.getText();
                    // var url = "{{ route('team-chats.send') }}";

                    // if (message.trim() == '') {
                    //     return false;
                    // }

                    // Get the message from the input field emoji area
                    var message = $("#TeamMessageInput").emojioneArea()[0].emojioneArea.getText();
                    var team_id = $(".team_id").val();
                    //  var sender_id = $("input[name=sender_id]").val(); // Assuming sender_id is available
                    var url = "{{ route('team-chats.send') }}";

                    // Get the file data
                    var fileInput = $("#team-file2")[0];
                    var file = fileInput.files[0]; // The selected file

                    // Create a FormData object to send both message and file
                    var formData = new FormData();
                    formData.append("_token", $("input[name=_token]").val());
                    formData.append("message", message);
                    formData.append("team_id", team_id);
                    // formData.append("sender_id", sender_id);

                    // Append the file if one is selected
                    if (file) {
                        formData.append("file", file);
                    } else {
                        if (message.trim() == '') {
                            return false;
                        }
                    }

                    $('#loading').addClass('loading');
                    $('#loading-content').addClass('loading-content');

                    $.ajax({
                        type: "POST",
                        url: url,
                        data: formData,
                        processData: false, // Don't process the data
                        contentType: false,
                        success: function(resp) {
                            loadChat($("#team_id").val());

                            $("#TeamMessageInput").emojioneArea()[0].emojioneArea.setText('');
                            $("#team-file2").val('');
                            $('#file-name-display').hide();
                            // let timezone = 'America/New_York';
                            let created_at = resp.chat.created_at;
                            let time = moment.tz(created_at).format('h:mm A');

                            // append new message to the chat
                            var data = resp.chat;
                            groupList(sender_id, data.team_id);
                            let html = `<div class="message me" id="team-chat-message-${data.id}">
                <div class="message-wrap">`;

                            let fileUrl = '';
                            let attachment = data.attachment;

                            if (attachment && attachment !== '') {
                                fileUrl = "{{ Storage::url('') }}" + attachment;
                                let attachement_extention = attachment.split('.').pop();

                                if (['jpg', 'jpeg', 'png', 'gif'].includes(attachement_extention)) {
                                    html += `<p class="messageContent">
                    <a href="${fileUrl}" target="_blank">
                        <img src="${fileUrl}" alt="attachment" style="max-width: 200px; max-height: 200px;">
                    </a><br>
                    <span>${data.message.replace(/\n/g, '<br>')}</span>
                 </p>`;
                                } else if (['mp4', 'webm', 'ogg'].includes(attachement_extention)) {
                                    html += `<p class="messageContent">
                    <a href="${fileUrl}" target="_blank">
                        <video width="200" height="200" controls>
                            <source src="${fileUrl}" type="video/mp4">
                            <source src="${fileUrl}" type="video/webm">
                            <source src="${fileUrl}" type="video/ogg">
                        </video>
                    </a><br>
                    <span>${data.message.replace(/\n/g, '<br>')}</span>
                 </p>`;
                                } else {
                                    html += `<p class="messageContent">
                    <a href="${fileUrl}" download="${attachment}">
                        <img src="{{ asset('user_assets/images/file.png') }}" alt="file">
                    </a><br>
                    <span>${data.message.replace(/\n/g, '<br>')}</span>
                 </p>`;
                                }
                            } else {
                                html +=
                                    `<p class="messageContent">${data.message.replace(/\n/g, '<br>')}</p>`;
                            }

                            html += `<div class="messageDetails">
                                        <div class="messageTime">${time}</div>
                                    </div>
                                </div>
                                <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li><a class="dropdown-item team-remove-chat" data-chat-id="${data.id}" data-del-from="me" data-team-id="${data.team_id}">Remove For Me</a></li>
                                            <li><a class="dropdown-item team-remove-chat" data-chat-id="${data.id}" data-del-from="everyone" data-team-id="${data.team_id}">Remove For Everyone</a></li>
                                </ul>
                            </div></div>
                                    `;
                            $('#team-chat-container-' + data.team_id).append(html);

                            scrollChatToBottom(data.team_id);
                            $('#loading').removeClass('loading');
                            $('#loading-content').removeClass('loading-content');
                            // Send message to socket
                            socket.emit('sendTeamMessage', {
                                chat: data,
                                chat_member_id: resp.chat_member_id,
                                created_at: resp.chat.new_created_at
                            });
                        },
                        error: function(xhr) {
                            toastr.error('Something went wrong');
                        }
                    });
                });

                $(document).on('submit', '#name-des-update', function(e) {
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
                            $('#loading').removeClass('loading');
                            $('#loading-content').removeClass('loading-content');
                            toastr.success(resp.message);
                            $('.group-name-' + resp.team_id).html(resp.name);
                            $('.group-des-' + resp.team_id).html(resp.description);
                            $('#exampleModalToggle3').modal('hide');
                            $('#groupInfo').modal('show');

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


                $(document).on('click', '.group-info', function() {
                    var team_id = $(this).data('team-id');
                    groupDetails(team_id);
                });

                $(document).on('click', '.back-to-group-info', function() {
                    $('#exampleModalToggle3').modal('hide');
                    var team_id = $(this).data('team-id');
                    groupDetails(team_id);
                });
                // back-to-group-info-one
                $(document).on('click', '.back-to-group-info-one', function() {
                    $('#exampleModalToggle2').modal('hide');
                    var team_id = $(this).data('team-id');
                    groupDetails(team_id);
                });

                function groupDetails(team_id) {
                    $('#loading').addClass('loading');
                    $('#loading-content').addClass('loading-content');
                    $.ajax({
                        type: "POST",
                        url: "{{ route('team-chats.group-info') }}",
                        data: {
                            team_id: team_id,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(resp) {
                            // model open

                            $('#group-information').html(resp.view);
                            $('#groupInfo').modal('show');
                            $('#loading').removeClass('loading');
                            $('#loading-content').removeClass('loading-content');
                        },
                        error: function(xhr) {
                            toastr.error('Something went wrong');
                        }
                    });
                }

                $(document).on('change', '.team-profile-picture', function() {
                    var team_id = $(this).data('team-id');
                    var file = $(this).prop('files')[0];
                    var formData = new FormData();
                    formData.append('group_image', file);
                    formData.append('team_id', team_id);
                    formData.append('_token', "{{ csrf_token() }}");
                    $('#loading').addClass('loading');
                    $('#loading-content').addClass('loading-content');
                    $.ajax({
                        type: "POST",
                        url: "{{ route('team-chats.update-group-image') }}",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(resp) {
                            if (resp.status == true) {
                                var group_image = resp.group_image;
                                var group_image_url = "{{ Storage::url('') }}" + group_image;
                                $('.team-image-' + team_id).html(
                                    `<img src="{{ Storage::url('') }}${group_image}" alt="">`);

                                $('#loading').removeClass('loading');
                                $('#loading-content').removeClass('loading-content');
                                toastr.success(resp.message);

                                socket.emit('updateGroupImage', {
                                    team_id: team_id,
                                    group_image: group_image_url
                                });
                            } else {
                                toastr.error(resp.message);
                            }
                        },
                        error: function(xhr) {
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

                $(document).on('click', '.edit-name-des', function() {
                    var team_id = $(this).data('team-id');
                    $('#loading').addClass('loading');
                    $('#loading-content').addClass('loading-content');
                    $.ajax({
                        type: "POST",
                        url: "{{ route('team-chats.edit-name-des') }}",
                        data: {
                            team_id: team_id,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(resp) {
                            $('#change-group-details').html(resp.view);
                            $('#groupInfo').modal('hide');
                            $('#exampleModalToggle3').modal('show');
                            $('#loading').removeClass('loading');
                            $('#loading-content').removeClass('loading-content');
                        },
                        error: function(xhr) {
                            toastr.error('Something went wrong');
                        }
                    });
                });

                $(document).on('click', '.remove-member-from-group', function() {
                    var team_id = $(this).data('team-id');
                    var user_id = $(this).data('user-id');
                    var r = confirm("Are you sure you want to remove this member?");
                    if (r == true) {
                        $.ajax({
                            type: "POST",
                            url: "{{ route('team-chats.remove-member') }}",
                            data: {
                                team_id: team_id,
                                user_id: user_id,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(resp) {
                                if (resp.status == true) {
                                    $('#groupInfo').modal('hide');
                                    loadChat(team_id);
                                    toastr.success(resp.message);
                                    $('#group-member-' + team_id + '-' + user_id).remove();

                                    // socket emit
                                    socket.emit('removeMemberFromGroup', {
                                        team_id: team_id,
                                        user_id: user_id,
                                        sender_id: sender_id,
                                        notification: resp.notification
                                    });

                                    socket.emit('sendTeamMessage', {
                                        chat: resp.chat,
                                        chat_member_id: resp.chat_member_id,
                                        created_at: resp.chat.new_created_at
                                    });

                                } else {
                                    toastr.error(resp.message);
                                }
                            },
                            error: function(xhr) {
                                toastr.error('Something went wrong');
                            }
                        });
                    } else {
                        return false;
                    }
                });

                // make-admin
                $(document).on('click', '.make-admin', function() {
                    var team_id = $(this).data('team-id');
                    var user_id = $(this).data('user-id');
                    var r = confirm("Are you sure you want to make this member admin?");
                    if (r == true) {
                        $.ajax({
                            type: "POST",
                            url: "{{ route('team-chats.make-admin') }}",
                            data: {
                                team_id: team_id,
                                user_id: user_id,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(resp) {
                                if (resp.status == true) {
                                    toastr.success(resp.message);
                                    $('#show-permission-' + team_id + '-' + user_id).html(
                                        ` <span class="admin_name">Admin</span>`);

                                    // socket emit sendAdminNotification
                                    socket.emit('sendAdminNotification', {
                                        team_id: team_id,
                                        user_id: user_id,
                                        sender_id: sender_id,
                                        notification: resp.notification
                                    });
                                } else {
                                    toastr.error(resp.message);
                                }
                            },
                            error: function(xhr) {
                                toastr.error('Something went wrong');
                            }
                        });
                    } else {
                        return false;
                    }
                });

                function groupList(user_id, team_id = null) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('team-chats.group-list') }}",
                        data: {
                            user_id: user_id,
                            team_id: team_id,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(resp) {
                            $('.group-list').html(resp.view);
                        },
                        error: function(xhr) {
                            toastr.error('Something went wrong');
                        }
                    });
                }

                $(document).on('click', '.exit-from-group', function() {
                    var team_id = $(this).data('team-id');
                    var r = confirm("Are you sure you want to exit from this group?");
                    if (r == true) {
                        $.ajax({
                            type: "POST",
                            url: "{{ route('team-chats.exit-from-group') }}",
                            data: {
                                team_id: team_id,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(resp) {
                                if (resp.status == true) {
                                    toastr.success(resp.message);
                                    if (resp.team_delete == true) {
                                        groupList(sender_id);
                                        $('#group-member-' + resp.team_id + '-' + resp.user_id)
                                            .remove();
                                        $('#groupInfo').modal('hide');
                                        html = `<div class="icon_chat">
                                        <span><img src="{{ asset('user_assets/images/icon-chat.png') }}" alt=""></span>
                                        <h4>Seamless Real-Time Chat | Connect Instantly</h4>
                                        <p>Join our dynamic chat platform, where real-time communication is effortless. Engage in private and group
                                            conversations, manage your contacts, and stay connected with instant updates. Experience a secure and
                                            responsive interface, perfect for personal or professional use.</p>
                                    </div>`;
                                        $('.chat-body').html(html);
                                    } else {
                                        loadChat(team_id);
                                        $('#group-member-' + resp.team_id + '-' + resp.user_id)
                                            .remove();
                                        $('#groupInfo').modal('hide');
                                    }


                                    // socket emit
                                    socket.emit('exitFromGroup', {
                                        team_id: team_id,
                                        user_id: resp.user_id,
                                        team_member_name: resp.team_member_name,
                                        team_delete: resp.team_delete,
                                        team_member_id: resp.team_member_id
                                    });
                                } else {
                                    toastr.error(resp.message);
                                }
                            },
                            error: function(xhr) {
                                toastr.error('Something went wrong');
                            }
                        });
                    } else {
                        return false;
                    }
                });

                //add-member-team form submit
                $(document).on('submit', '#add-member-team', function(e) {
                    e.preventDefault();
                    var form = $(this);
                    var url = form.attr('action');
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: new FormData(this),
                        processData: false,
                        contentType: false,
                        success: function(resp) {
                            toastr.success(resp.message);
                            $('#add-member-team')[0].reset();
                            $('#exampleModalToggle2').modal('hide');
                            loadChat(resp.team_id);
                            // socket emit
                            socket.emit('sendTeamMessage', {
                                chat: resp.chat,
                                chat_member_id: resp.chat_member_id,
                                created_at: resp.chat.new_created_at
                            });

                            socket.emit('addMemberToGroup', {
                                team_id: resp.team_id,
                                user_id: resp.user_id,
                                team_member_name: resp.team_member_name,
                                chat_member_id: resp.chat_member_id,
                                already_member_arr: resp.already_member_arr,
                                only_added_members: resp.only_added_members
                            });

                        },
                        error: function(xhr) {
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

                // delete-group
                $(document).on('click', '.delete-group', function() {
                    var team_id = $(this).data('team-id');
                    var r = confirm("Are you sure you want to delete this group?");
                    if (r == true) {
                        $.ajax({
                            type: "POST",
                            url: "{{ route('team-chats.delete-group') }}",
                            data: {
                                team_id: team_id,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(resp) {
                                if (resp.status == true) {
                                    toastr.success(resp.message);
                                    groupList(sender_id);
                                    html = `<div class="icon_chat">
                                        <span><img src="{{ asset('user_assets/images/icon-chat.png') }}" alt=""></span>
                                        <h4>Seamless Real-Time Chat | Connect Instantly</h4>
                                        <p>Join our dynamic chat platform, where real-time communication is effortless. Engage in private and group
                                            conversations, manage your contacts, and stay connected with instant updates. Experience a secure and
                                            responsive interface, perfect for personal or professional use.</p>
                                    </div>`;
                                    $('.chat-body').html(html);

                                    // socket emit
                                    socket.emit('deleteGroup', {
                                        team_id: resp.team_id,
                                        user_id: sender_id,
                                        team_member_id: resp.team_member_id
                                    });

                                } else {
                                    toastr.error(resp.message);
                                }
                            },
                            error: function(xhr) {
                                toastr.error('Something went wrong');
                            }
                        });
                    } else {
                        return false;
                    }
                });

                // team-remove-chat
                $(document).on('click', '.team-remove-chat', function() {
                    var chat_id = $(this).data('chat-id');
                    var del_from = $(this).data('del-from');
                    var team_id = $(this).data('team-id');
                    var r = confirm("Are you sure you want to delete this message?");
                    if (r == true) {
                        $.ajax({
                            type: "POST",
                            url: "{{ route('team-chats.remove-chat') }}",
                            data: {
                                chat_id: chat_id,
                                del_from: del_from,
                                team_id: team_id,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(resp) {
                                if (resp.status == true) {
                                    if (del_from == 'me') {
                                        $("#team-chat-message-" + chat_id).remove();
                                        if (resp.last_message == true) {
                                            $("#team-last-chat-time-" + chat_id).remove();
                                            $('.team-last-chat-' + chat_id).html('');
                                        }
                                    } else {
                                        $("#team-chat-message-" + chat_id).remove();
                                        if (resp.last_message == true) {
                                            $("#team-last-chat-time-" + chat_id).remove();
                                            $('.team-last-chat-' + chat_id).html('');
                                        }

                                        socket.emit("team-remove-chat", {
                                            chat_id: chat_id,
                                            last_message: resp.last_message,
                                        });
                                    }
                                } else {
                                    toastr.error(resp.message);
                                }
                            },
                            error: function(xhr) {
                                toastr.error('Something went wrong');
                            }
                        });
                    } else {
                        return false;
                    }
                });

                $(document).on('click', '.clear-all-conversation', function() {
                    var teamId = $(this).data('team-id');
                    r = confirm("Are you sure you want to clear all conversation?");
                    if (r == true) {
                        $.ajax({
                            url: "{{ route('team-chats.clear-all-conversation') }}",
                            type: 'POST',
                            data: {
                                team_id: teamId
                            },
                            success: function(response) {
                                if (response.status == true) {
                                    $('#team-chat-container-' + teamId).html('');
                                    groupList(sender_id);
                                    toastr.success(response.message);

                                    // socket emit
                                    socket.emit('clearAllConversation', {
                                        team_id: teamId,
                                        user_id: sender_id
                                    });
                                }
                            }
                        });
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

                // clearAllConversation
                socket.on('clearAllConversation', function(data) {
                    if (data.user_id != sender_id) {
                        $('#team-chat-container-' + data.team_id).html('');
                        groupList(sender_id);
                    }
                });

                // team-remove-chat
                socket.on('team-remove-chat', function(data) {
                    $("#team-chat-message-" + data.chat_id).remove();
                    if (data.last_message == true) {
                        $("#team-last-chat-time-" + data.chat_id).remove();
                        $('.team-last-chat-' + data.chat_id).html('');
                    }
                });

                // deleteGroup

                socket.on('deleteGroup', function(data) {
                    if (data.user_id != sender_id && data.team_member_id.includes(sender_id)) {
                        groupList(sender_id);
                        html = `<div class="icon_chat">
                                        <span><img src="{{ asset('user_assets/images/icon-chat.png') }}" alt=""></span>
                                        <h4>Seamless Real-Time Chat | Connect Instantly</h4>
                                        <p>Join our dynamic chat platform, where real-time communication is effortless. Engage in private and group
                                            conversations, manage your contacts, and stay connected with instant updates. Experience a secure and
                                            responsive interface, perfect for personal or professional use.</p>
                                    </div>`;
                        $('.chat-body').html(html);
                    }
                });

                // addMemberToGroup
                socket.on('addMemberToGroup', function(data) {
                    if (data.user_id != sender_id && data.chat_member_id.includes(sender_id)) {
                        $('#all-member-' + data.team_id).html(
                            data.team_member_name.length > 60 ?
                            data.team_member_name.substring(0, 60) + '...' :
                            data.team_member_name
                        );

                        groupList(sender_id);
                    }

                    if (data.only_added_members.includes(sender_id)) {
                        //  get count notification
                        var count = $('#show-notification-count-' + sender_id).text();
                        count = parseInt(count);
                        count += 1;
                        $('#show-notification-count-' + sender_id).text(count);
                    }


                    if (data.already_member_arr.includes(sender_id)) {
                        html = `  <form id="TeamMessageForm">
            <input type="file" id="file" style="display: none" data-team-id="${data.team_id}">
            <div class="file-upload">
                <label for="file">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24">
                        <path fill="currentColor" fill-rule="evenodd"
                            d="M9 7a5 5 0 0 1 10 0v8a7 7 0 1 1-14 0V9a1 1 0 0 1 2 0v6a5 5 0 0 0 10 0V7a3 3 0 1 0-6 0v8a1 1 0 1 0 2 0V9a1 1 0 1 1 2 0v6a3 3 0 1 1-6 0z"
                            clip-rule="evenodd" style="color:black"></path>
                    </svg>
                </label>
            </div>
            <input type="text" id="TeamMessageInput" placeholder="Type a message...">
            <input type="hidden" id="team_id" value="${data.team_id}" class="team_id">
            <div>
                <button class="Send">
                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M0.82186 0.827412C0.565716 0.299519 0.781391 0.0763349 1.32445 0.339839L20.6267 9.70604C21.1614 9.96588 21.1578 10.4246 20.6421 10.7179L1.6422 21.526C1.11646 21.8265 0.873349 21.6115 1.09713 21.0513L4.71389 12.0364L15.467 10.2952L4.77368 8.9726L0.82186 0.827412Z"
                            fill="white" />
                    </svg>
                </button>
            </div>
        </form>`;
                        $('#group-member-form-' + data.team_id + '-' + sender_id).html(html);

                    }
                });


                // exitFromGroup
                socket.on('exitFromGroup', function(data) {
                    if (data.user_id != sender_id) {
                        $('#group-member-' + data.team_id + '-' + data.user_id).remove();
                        $('#all-member-' + data.team_id).html(
                            data.team_member_name.length > 60 ?
                            data.team_member_name.substring(0, 60) + '...' :
                            data.team_member_name
                        );
                    }

                    if (data.team_delete == true && data.team_member_id.includes(sender_id) && data.user_id !=
                        sender_id) {
                        groupList(sender_id);
                        if ($('#team-chat-container-' + data.team_id).length > 0) {
                            html = `<div class="icon_chat">
                                        <span><img src="{{ asset('user_assets/images/icon-chat.png') }}" alt=""></span>
                                        <h4>Seamless Real-Time Chat | Connect Instantly</h4>
                                        <p>Join our dynamic chat platform, where real-time communication is effortless. Engage in private and group
                                            conversations, manage your contacts, and stay connected with instant updates. Experience a secure and
                                            responsive interface, perfect for personal or professional use.</p>
                                    </div>`;
                            $('.chat-body').html(html);
                        }
                    }
                });

                socket.on('createTeam', function(data) {
                    if (sender_id != data.user_id && data.chat_member_id.includes(sender_id)) {
                        // get count notification
                        var count = $('#show-notification-count-' + sender_id).text();
                        count = parseInt(count);
                        count += 1;
                        $('#show-notification-count-' + sender_id).text(count);
                        groupList(sender_id);
                    }
                })

                socket.on('removeMemberFromGroup', function(data) {
                    if (data.user_id == sender_id) {
                        var notification = data.notification;
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
                        loadChat(data.team_id);

                        //             $('#group-member-form-' + data.team_id + '-' + data.user_id).html(`
                //           <div class="justify-content-center">
                //     <div class="text-center">
                //         <h4 style="color:#be2020 !important; front-size:1.25rem;">Sorry! you are not able to send message in this group.</h4>
                //     </div>
                // </div>
                //         `);

                    }

                    if (data.sender_id != sender_id) {
                        $('#group-member-' + data.team_id + '-' + data.user_id).remove();
                    }
                });

                socket.on('updateGroupImage', function(data) {
                    $('.team-image-' + data.team_id).html(
                        `<img src="${data.group_image}" alt="">`);
                });

                // Receive message from socket
                socket.on('sendTeamMessage', function(data) {
                    // console.log(data);

                    let timezone = '{{auth()->user()->time_zone ?? "UTC"}}';
                    let created_at = data.created_at;

                    let time = moment.tz(created_at, timezone).format('h:mm A');

                    let chat_member_id_array = data.chat_member_id;

                    if (data.chat.user_id != sender_id && chat_member_id_array.includes(sender_id)) {
                        let html = `
        <div class="message you" id="team-chat-message-${data.chat.id}">
            <div class="d-flex">
                <div class="member_image">
                    <span>`;
                        if (data.chat.user.profile_picture) {
                            html +=
                                `<img src="{{ Storage::url('${data.chat.user.profile_picture}') }}" alt="">`;
                        } else {
                            html += `<img src="{{ asset('user_assets/images/profile_dummy.png') }}" alt="">`;
                        }

                        html += `   </span>
                </div>
                <div class="message_group">
                    <p class="messageContent">
                        <span class="namemember">
    ${ (data.chat.user.first_name ?? '') + ' ' + (data.chat.user.middle_name ?? '') + ' ' + (data.chat.user.last_name ?? '') }
</span>`;

                        let fileUrl = '';
                        let attachment = data.chat.attachment;
                        if (attachment != '') {
                            fileUrl = "{{ Storage::url('') }}" + attachment;
                            let attachement_extention = attachment.split('.').pop();

                            if (['jpg', 'jpeg', 'png', 'gif'].includes(attachement_extention)) {
                                html += `<a href="${fileUrl}" target="_blank">
                        <img src="${fileUrl}" alt="attachment" style="max-width: 200px; max-height: 200px;">
                     </a><br><span class="">${data.chat.message.replace(/\n/g, '<br>')}</span>`;
                            } else if (['mp4', 'webm', 'ogg'].includes(attachement_extention)) {
                                html += `<a href="${fileUrl}" target="_blank">
                        <video width="200" height="200" controls>
                            <source src="${fileUrl}" type="video/mp4">
                            <source src="${fileUrl}" type="video/webm">
                            <source src="${fileUrl}" type="video/ogg">
                        </video>
                     </a><<br><span class="">${data.chat.message.replace(/\n/g, '<br>')}</span>`;
                            } else {
                                html += `<a href="${fileUrl}" download="${attachment}">
                        <img src="{{ asset('user_assets/images/file.png') }}" alt="">
                     </a><br><span class="">${data.chat.message.replace(/\n/g, '<br>')}</span>`;
                            }
                        } else {
                            html += `${data.chat.message.replace(/\n/g, '<br>')}`;
                        }

                        html += `</p>
                    <div class="messageDetails">
                        <div class="messageTime">${time}</div>
                    </div>
                </div>
            </div>
        </div>`;

                        $('#team-chat-container-' + data.chat.team_id).append(html);
                        scrollChatToBottom(data.chat.team_id);
                    }
                    if (data.chat.user_id != sender_id && chat_member_id_array.includes(sender_id)) {
                        if ($(".chat-body").length > 0) {
                            if ($("#team-chat-container-" + data.chat.team_id).length > 0) {
                                $('#count-team-unseen-' + data.chat.team_id).html('');
                                $.ajax({
                                    type: "POST",
                                    url: "{{ route('team-chats.seen') }}",
                                    data: {
                                        chat_id: data.chat.id,
                                        _token: "{{ csrf_token() }}"
                                    },
                                    success: function(res) {
                                        if (res.status == true) {
                                            // socket.emit('teamSeenChat', {
                                            //     last_chat: data.chat
                                            // });
                                        } else {
                                            console.log(res.msg);
                                        }
                                    }
                                });
                                $.ajax({
                                    type: "POST",
                                    url: "{{ route('team-chats.notification') }}",
                                    data: {
                                        user_id: sender_id,
                                        team_id: data.chat.team_id,
                                        chat_id: data.chat.id,
                                        is_delete: 1,
                                        _token: "{{ csrf_token() }}"
                                    },
                                    success: function(res) {
                                        console.log(res);

                                    }
                                });
                            } else {
                                $.ajax({
                                    type: "POST",
                                    url: "{{ route('team-chats.notification') }}",
                                    data: {
                                        user_id: sender_id,
                                        team_id: data.chat.team_id,
                                        chat_id: data.chat.id,
                                        _token: "{{ csrf_token() }}"
                                    },
                                    success: function(res) {
                                        if (res.status == true) {
                                            $('#show-notification-count-' + sender_id).html(res
                                                .notification_count);
                                            var route =
                                                `{{ route('notification.read', ['type' => 'Team', 'id' => '__ID__']) }}`
                                                .replace('__ID__', res.notification.id);
                                            var html = `<li>
                                                 <a href="${route}" class="top-text-block">
                                                     <div class="top-text-heading">${res.notification.message}</div>
                                                     <div class="top-text-light">${moment(res.notification.created_at).fromNow()}</div>
                                                 </a>
                                             </li>`;
                                            $('#show-notification-' + sender_id).prepend(
                                                html
                                            ); // Use prepend to add new notification at the top
                                        }
                                    }
                                });
                            }
                        } else {
                            $.ajax({
                                type: "POST",
                                url: "{{ route('team-chats.notification') }}",
                                data: {
                                    user_id: sender_id,
                                    team_id: data.chat.team_id,
                                    chat_id: data.chat.id,
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function(res) {
                                    if (res.status == true) {
                                        $('#show-notification-count-' + sender_id).html(res
                                            .notification_count);
                                        var route =
                                            `{{ route('notification.read', ['type' => 'Team', 'id' => '__ID__']) }}`
                                            .replace('__ID__', res.notification.id);
                                        var html = `<li>
                                                 <a href="${route}" class="top-text-block">
                                                     <div class="top-text-heading">${res.notification.message}</div>
                                                     <div class="top-text-light">${moment(res.notification.created_at).fromNow()}</div>
                                                 </a>
                                             </li>`;
                                        $('#show-notification-' + sender_id).prepend(
                                            html
                                        ); // Use prepend to add new notification at the top
                                    } else {
                                        console.log(res.msg);
                                    }
                                }
                            });
                        }
                        groupList(sender_id, data.chat.team_id);
                    }

                });

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


                socket.on('send_mail', function(data) {
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
                    },
                    error: function(xhr) {
                        toastr.error('Failed to fetch latest emails.');
                    }
                });
            }




            function setMailStar(element, mailid) {
                const icon = element.querySelector('.material-symbols-outlined');
                var star_val = 0;

                if (icon.style.color === 'orange') {
                    icon.style.color = '';
                    icon.style.fontVariationSettings = "'FILL' 0";
                    star_val = 0;
                } else {
                    icon.style.color = 'orange';
                    icon.style.fontVariationSettings = "'FILL' 1";
                    star_val = 1;
                }

                // console.log('mail id is ' + mailid);
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




        @stack('scripts')
    </body>

</html>
