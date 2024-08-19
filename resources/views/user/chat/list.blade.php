@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} - User Chat
@endsection
@push('styles')
@endpush
@section('content')
    @php
        use App\Helpers\Helper;
    @endphp
    <div class="container-fluid">
        <div class="bg_white_border">

            <div class="messaging_sec">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="heading_hp">
                        <h2>Messaging</h2>
                    </div>
                </div>
                <div class="SideNavhead">
                    <h2>Chat</h2>
                </div>
                <div class="main">
                    <div>
                        <div class="sideNav2" id="group-manage-{{ Auth::user()->id }}">
                            @if (count($users) > 0)
                                @foreach ($users as $user)
                                    <li class="group user-list" data-id="{{ $user['id'] }}">
                                        <div class="avatar">
                                            @if ($user['profile_picture'])
                                                <img src="{{ Storage::url($user['profile_picture']) }}" alt="">
                                            @else
                                                <img src="{{ asset('user_assets/images/profile_dummy.png') }}"
                                                    alt="">
                                            @endif
                                        </div>
                                        <p class="GroupName">{{ $user['first_name'] }} {{ $user['middle_name'] ?? '' }}
                                            {{ $user['last_name'] ?? '' }}</p>
                                        <p class="GroupDescrp" id="message-app-{{ $user['id'] }}">
                                            @if (isset($user['last_message']['message']))
                                                {{ $user['last_message']['message'] }}
                                            @endif
                                        </p>
                                        <div class="time_online">
                                            @if (isset($user['last_message']['created_at']))
                                                <p>{{ $user['last_message']['created_at']->format('h:i A') }}</p>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            @else
                                <p>No users found</p>
                            @endif
                        </div>
                    </div>
                    <section class="Chat chat-module">
                        @include('user.chat.chat_body')
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
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
            let ip_address = '127.0.0.1';
            let socket_port = '3000';
            let socket = io(ip_address + ':' + socket_port);

            $(document).on("click", ".user-list", function(e) {
                var getUserID = $(this).attr("data-id");
                receiver_id = getUserID;
                loadChats();
                $(this).addClass("active").siblings().removeClass("active");
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

                            if (resp.chat_count > 0) {
                                scrollChatToBottom(receiver_id);
                            }

                            // Initialize EmojiOneArea on MessageInput
                            var emojioneAreaInstance = $("#MessageInput").emojioneArea({
                                pickerPosition: "top",
                                filtersPosition: "top",
                                tonesStyle: "bullet"
                            });

                            // Handle Enter key press within the emoji picker
                            emojioneAreaInstance[0].emojioneArea.on('keydown', function(editor, event) {
                                if (event.which === 13 && !event.shiftKey) {
                                    event.preventDefault();
                                    $("#MessageForm").submit();
                                }
                            });
                        } else {
                            console.log(resp.msg);
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
                var url = "{{ route('chats.send') }}";

                if (message == "") {
                    return false;
                }

                // Perform Ajax request to send the message to the server
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        _token: $("input[name=_token]").val(),
                        message: message,
                        reciver_id: receiver_id,
                        sender_id: sender_id,
                    },
                    success: function(res) {
                        console.log(res);

                        if (res.success) {
                            $("#MessageInput").data("emojioneArea").setText("");
                            let chat = res.chat.message;
                            let created_at = res.chat.created_at_formatted;
                            // use timezones to format the time America/New_York
                            let time_format_12 = moment(created_at, "YYYY-MM-DD HH:mm:ss")
                                .format("hh:mm A");

                            let html = ` <div class="message me">
                                <p class="messageContent">${chat}</p>
                                <div class="messageDetails">
                                    <div class="messageTime">${time_format_12}</div>
                                    <i class="fas fa-check"></i>
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
                                let timezone = 'America/New_York';
                                let time_format_13 = user.last_message && user
                                    .last_message.created_at ?
                                    moment.tz(user.last_message.created_at, timezone)
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
                                    <p class="GroupDescrp">${user.last_message && user.last_message.message ? user.last_message.message : ''}</p>
                                    <div class="time_online">
                                        <p>${time_format_13}</p>
                                    </div>
                                </li>`;
                            });

                            $('#group-manage-' + sender_id).append(new_html);

                            // Emit chat message to the server
                            socket.emit("chat", {
                                message: message,
                                sender_id: sender_id,
                                receiver_id: receiver_id,
                                receiver_users: res.receiver_users,
                            });
                        } else {
                            console.log(res.msg);
                        }
                    },
                });
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
                            let timeZome = 'America/New_York';
                            let time_format_12 = moment.tz(created_at, timeZome).format("hh:mm A");
                            let html = `<div class="message me">`;
                            if (['jpg', 'jpeg', 'png', 'gif'].includes(attachement_extention)) {
                                html +=
                                    `<p class="messageContent"><a href="${fileUrl}" target="_blank"><img src="${fileUrl}" alt="attachment" style="max-width: 200px; max-height: 200px;"></a></p>`;
                            } else if (['mp4', 'webm', 'ogg'].includes(attachement_extention)) {
                                html +=
                                    `<p class="messageContent"><a href="${fileUrl}" target="_blank"><video width="200" height="200" controls><source src="${fileUrl}" type="video/mp4"><source src="${fileUrl}" type="video/webm"><source src="${fileUrl}" type="video/ogg"></video></a></p>`;
                            } else {
                                html +=
                                    `<p class="messageContent"><a href="${fileUrl}" download="${attachment}"><img src="{{ asset('user_assets/images/file.png') }}" alt=""></a></p>`;
                            }

                            html +=
                                `<div class="messageDetails"><div class="messageTime">${time_format_12}</div><i class="fas fa-check"></i></div></div>`;

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
                                let timeZome = 'America/New_York';
                                let time_format_13 = user.last_message && user
                                    .last_message.created_at ? moment.tz(user.last_message
                                        .created_at, timeZome).format(
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
                                    `</div><p class="GroupName">${user.first_name} ${user.middle_name ? user.middle_name : ''} ${user.last_name ? user.last_name : ''}</p><p class="GroupDescrp">${user.last_message && user.last_message.message ? user.last_message.message : ''}</p><div class="time_online"><p>${time_format_13}</p></div></li>`;
                            });

                            $('#group-manage-' + sender_id).append(new_html);

                            socket.emit("chat", {
                                message: file.name,
                                file_url: fileUrl,
                                sender_id: sender_id,
                                receiver_id: receiver_id,
                                receiver_users: res.receiver_users,
                            });
                        } else {
                            console.log(res.msg);
                        }
                    }
                });
            });

            // Listen for incoming chat messages from the server
            socket.on("chat", function(data) {
                let timeZome = 'America/New_York';
                html = `
                        <div class="message you">
                            <p class="messageContent">`
                if (data.file_url) {
                    let attachement_extention = data.file_url.split('.').pop();
                    if (['jpg', 'jpeg', 'png', 'gif'].includes(attachement_extention)) {
                        html +=
                            `<a href="${data.file_url}" target="_blank"><img src="${data.file_url}" alt="attachment" style="max-width: 200px; max-height: 200px;"></a>`;
                    } else if (['mp4', 'webm', 'ogg'].includes(attachement_extention)) {
                        html +=
                            `<a href="${data.file_url}" target="_blank"><video width="200" height="200" controls><source src="${data.file_url}" type="video/mp4"><source src="${data.file_url}" type="video/webm"><source src="${data.file_url}" type="video/ogg"></video></a>`;
                    } else {
                        html +=
                            `<a href="${data.file_url}" download="${data.message}"><img src="{{ asset('user_assets/images/file.png') }}" alt=""></a>`;
                    }
                } else {
                    html += `${data.message}`;
                }

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
                        }
                    }
                    $('#message-app-' + data.sender_id).html(data.message);
                    var users = data.receiver_users;
                    $('#group-manage-' + sender_id).html('');
                    var new_html = '';
                    users.forEach(user => {
                        // Check if last_message exists and has a created_at property
                        let timeZome = 'America/New_York';
                        let time_format_13 = user.last_message && user.last_message.created_at ?
                            moment.tz(user.last_message.created_at, timeZome).format(
                                "hh:mm A") :
                            '';

                        new_html += `
        <li class="group user-list ${user.id == data.sender_id ? 'active' : ''}" data-id="${user.id}">
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
        <p class="GroupDescrp">${user.last_message && user.last_message.message ? user.last_message.message : ''}</p>
        <div class="time_online">
            <p>${time_format_13}</p>
        </div>
    </li>`;
                    });

                    $('#group-manage-' + sender_id).append(new_html);

                }
            });

            // seen message

        });
    </script>
@endpush
