@extends('user.layouts.master')
@section('title')
    Team - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
@php
    use App\Helpers\Helper;
@endphp
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="messaging_sec">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="heading_hp ">
                        <h2>Messaging</h2>

                    </div>
                    <div>
                        @if (auth()->user()->can('Create Team'))
                            <a class="btn btn-primary" data-bs-toggle="modal" href="#exampleModalToggle"><i
                                    class="fa-solid fa-plus"></i>
                                Create Group</a>
                        @endif

                    </div>
                </div>
                <div class="SideNavhead">
                    <h2>Recent Chat</h2>

                </div>
                <!-- Modal -->

                <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
                    tabindex="-1">
                    <div class="modal-dialog  modal-dialog-centered">
                        <div class="modal-content group_create">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalToggleLabel">Create Group</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form action="{{ route('team-chats.create') }}" method="post" enctype="multipart/form-data"
                                id="create-team">
                                @csrf
                                <div class="modal-body">
                                    <div class="group_crate">
                                        <div class="mb-3">
                                            <div class="image-upload">
                                                <div class="image-wrap"><img id="previewImage01"
                                                        src="{{ asset('user_assets/images/group.jpg') }}" /></div>
                                                <input class="btn-inputfile change-profile" id="file01" type="file"
                                                    name="group_image" />
                                                <label for="file01"><i class="fa-solid fa-camera"></i></label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="" class="form-label">Group Name</label>
                                            <input type="text" class="form-control" id="" placeholder=""
                                                name="name">
                                        </div>
                                        <div class="">
                                            <label for="" class="form-label">Description</label>
                                            <textarea class="form-control" id="" rows="3" name="description"></textarea>
                                        </div>
                                    </div>
                                    <div class="member_add mt-3">
                                        <h5>
                                            <strong>Add Member</strong>
                                        </h5>
                                        <div class="search-field float-right">
                                            <input type="text" name="search" id="search" placeholder="search..."
                                                class="form-control">
                                            <button class="submit_search" id="search-button"> <span class=""><i
                                                        class="fa fa-search"></i></span></button>
                                        </div>

                                        <ul id="member-list">
                                            @if (count($members) > 0)
                                                @foreach ($members as $user)
                                                    <li class="member-item">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="{{ $user->id }}" id="flexCheckDefault"
                                                                name="members[]">
                                                            <label class="form-check-label" for="flexCheckDefault"></label>
                                                        </div>
                                                        <div class="avatar">
                                                            <img src="{{ $user->profile_picture ? Storage::url($user->profile_picture) : asset('user_assets/images/profile_dummy.png') }}"
                                                                alt="">
                                                        </div>
                                                        <p class="GroupName">{{ $user->full_name }}</p>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" type="submit">Create Group</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
                <div id="change-group-details">
                    @include('user.team-chat.group-details')
                </div>

                <div class="modal fade" id="exampleModalToggle2" aria-hidden="true"
                    aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content group_create">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalToggleLabel2">Change Group Name And Description
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="member_add">
                                    <ul>
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefault">
                                                <label class="form-check-label" for="flexCheckDefault"></label>
                                            </div>
                                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                                    alt=""></div>
                                            <p class="GroupName">David Johnson</p>
                                        </li>
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefault">
                                                <label class="form-check-label" for="flexCheckDefault"></label>
                                            </div>
                                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                                    alt=""></div>
                                            <p class="GroupName">David Johnson</p>
                                        </li>
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefault">
                                                <label class="form-check-label" for="flexCheckDefault"></label>
                                            </div>
                                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                                    alt=""></div>
                                            <p class="GroupName">David Johnson</p>
                                        </li>
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefault">
                                                <label class="form-check-label" for="flexCheckDefault"></label>
                                            </div>
                                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                                    alt=""></div>
                                            <p class="GroupName">David Johnson</p>
                                        </li>
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefault">
                                                <label class="form-check-label" for="flexCheckDefault"></label>
                                            </div>
                                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                                    alt=""></div>
                                            <p class="GroupName">David Johnson</p>
                                        </li>
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefault">
                                                <label class="form-check-label" for="flexCheckDefault"></label>
                                            </div>
                                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                                    alt=""></div>
                                            <p class="GroupName">David Johnson</p>
                                        </li>
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefault">
                                                <label class="form-check-label" for="flexCheckDefault"></label>
                                            </div>
                                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                                    alt=""></div>
                                            <p class="GroupName">David Johnson</p>
                                        </li>
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefault">
                                                <label class="form-check-label" for="flexCheckDefault"></label>
                                            </div>
                                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                                    alt=""></div>
                                            <p class="GroupName">David Johnson</p>
                                        </li>
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefault">
                                                <label class="form-check-label" for="flexCheckDefault"></label>
                                            </div>
                                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                                    alt=""></div>
                                            <p class="GroupName">David Johnson</p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" data-bs-target="#exampleModalToggle"
                                    data-bs-toggle="modal" data-bs-dismiss="modal">Back to Create Page</button>
                                <button class="btn btn-primary" data-bs-dismiss="modal">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>



                <div id="group-information">
                    @include('user.team-chat.group-info')
                </div>


                <div class="main">
                    <div class="sideNav2 group-list" id="group-list">
                        @include('user.team-chat.group-list')

                    </div>
                    <section class="Chat chat-body">
                        @include('user.team-chat.chat-body')

                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('change', '.change-profile', function() {
                var previewImageID = $(this).parent().find("img").attr("id");
                // alert(previewImageID);
                previewFile(this, previewImageID);
            });

            function previewFile(input, image) {
                var preview = document.getElementById(image);
                var file = input.files[0];
                var reader = new FileReader();
                reader.addEventListener("load", function() {
                    preview.src = reader.result;
                }, false);
                if (file) {
                    reader.readAsDataURL(file);
                }
            }
        });
    </script>
    {{-- <script>
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('#blah')
                            .attr('src', e.target.result);
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script> --}}
    <script>
        $(document).ready(function() {
            $('#search').on('keyup', function() {
                // Get the search term, trim any leading/trailing spaces, and convert to lowercase
                var searchTerm = $(this).val().toLowerCase().trim();

                $('#member-list .member-item').each(function() {
                    // Get the user's name, normalize spaces, and convert to lowercase
                    var userName = $(this).find('.GroupName').text().toLowerCase().replace(/\s+/g,
                        ' ').trim();

                    // Check if the search term is included in the user's name
                    if (userName.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
    </script>
    <script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.34/moment-timezone-with-data.min.js"></script>

    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });
            let ip_address = '127.0.0.1';
            let socket_port = '3000';
            let socket = io(ip_address + ':' + socket_port);
            var sender_id = {{ auth()->user()->id }};

            $('#create-team').submit(function(e) {
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
                        // append new team to the list
                        var data = resp.team;
                        var group_image = data.group_image;
                        var time = data.last_message ?
                            "{{ date('h:i A', strtotime('" + data.last_message.created_at + "')) }}" :
                            '';
                        html = `<li class="group">
                                    <div class="avatar">`

                        if (group_image) {
                            html +=
                                `<img src="{{ Storage::url('${data.group_image}') }}" alt="">`;
                        } else {
                            html +=
                                `<img src="{{ asset('user_assets/images/group.jpg') }}" alt="">`;

                        }
                        html += `</div><p class="GroupName">${data.name}</p>
                                    <p class="GroupDescrp">${data.last_message ? data.last_message.message : ''}</p>
                                    <div class="time_online">${time ? time : ''}</div>
                                </li>`;
                        $('.group-list').prepend(html);
                        $('#exampleModalToggle').modal('hide');
                        // reset form
                        $('#create-team')[0].reset();

                        // Send message to socket
                        socket.emit('createTeam', {
                            user_id: sender_id,
                            chat_member_id : resp.chat_member_id
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

                        // Initialize EmojiOneArea on MessageInput
                        var emojioneAreaInstance = $("#TeamMessageInput").emojioneArea({
                            pickerPosition: "top",
                            filtersPosition: "top",
                            tonesStyle: "bullet"
                        });

                        // Handle Enter key press within the emoji picker
                        emojioneAreaInstance[0].emojioneArea.on('keydown', function(editor, event) {
                            if (event.which === 13 && !event.shiftKey) {
                                event.preventDefault();
                                $("#TeamMessageForm").submit();
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


            // Send message

            $(document).on("change", "#file", function(e) {
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
                            let attachment = res.chat.attachment;
                            let fileUrl = "{{ Storage::url('') }}" + attachment;
                            let attachement_extention = attachment.split('.').pop();
                            let created_at = res.chat.created_at;
                            let timeZome = 'America/New_York';
                            let time_format_12 = moment.tz(created_at, timeZome).format(
                                "hh:mm A");
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
                                `<div class="messageDetails"><div class="messageTime">${time_format_12}</div></div></div>`;
                            $('#team-chat-container-' + team_id).append(html);
                            scrollChatToBottom(team_id);
                            // Send message to socket
                            socket.emit('sendTeamMessage', {
                                chat: res.chat,
                                file_url: fileUrl,
                                chat_member_id: res.chat_member_id,
                            });
                        } else {
                            console.log(res.msg);
                        }
                    }
                });
            });

            $(document).on("submit", "#TeamMessageForm", function(e) {
                e.preventDefault();
                var message = $("#TeamMessageInput").emojioneArea()[0].emojioneArea.getText();
                var url = "{{ route('team-chats.send') }}";

                if (message.trim() == '') {
                    return false;
                }

                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        message: message,
                        team_id: $(".team_id").val(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(resp) {
                        loadChat($("#team_id").val());
                        $("#TeamMessageInput").emojioneArea()[0].emojioneArea.setText('');
                        let timezone = 'America/New_York';
                        let created_at = resp.chat.created_at;
                        let time = moment.tz(created_at, timezone).format('h:mm A');

                        // append new message to the chat
                        var data = resp.chat;
                        var html = `<div class="message me">
                                        <p class="messageContent">${data.message}</p>
                                        <div class="messageDetails">
                                            <div class="messageTime">${time}</div>
                                        </div>
                                    </div>`;
                        $('#team-chat-container-' + data.team_id).append(html);
                        scrollChatToBottom(data.team_id);

                        // Send message to socket
                        socket.emit('sendTeamMessage', {
                            chat: data,
                            chat_member_id: resp.chat_member_id
                        });
                    },
                    error: function(xhr) {
                        toastr.error('Something went wrong');
                    }
                });
            });

            // name-des-update form submit
            $(document).on('submit', '#name-des-update', function(e) {
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
                        $('.group-name-' + resp.team_id).html(resp.name);
                        $('.group-des-' + resp.team_id).html(resp.description);
                        $('#exampleModalToggle3').modal('hide');
                        $('#groupInfo').modal('show');
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


            // group-info
            $(document).on('click', '.group-info', function() {
                var team_id = $(this).data('team-id');
                groupDetails(team_id);
            });

            $(document).on('click', '.back-to-group-info', function() {
                $('#exampleModalToggle3').modal('hide');
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

            // remove-member-from-group click
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
                                    user_id: user_id
                                });

                                socket.emit('sendTeamMessage', {
                                    chat: resp.chat,
                                    chat_member_id: resp.chat_member_id
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

            function groupList(user_id) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('team-chats.group-list') }}",
                    data: {
                        user_id: user_id,
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

            // createTeam
            socket.on('createTeam', function(data){
                console.log(data);

                if (sender_id != data.user_id && data.chat_member_id.includes(sender_id)) {
                    console.log(data);

                    groupList(sender_id);
                }
            })

            socket.on('removeMemberFromGroup', function(data) {
                $('#group-member-form-' + data.team_id + '-' + data.user_id).html(`
                  <div class="justify-content-center">
            <div class="text-center">
                <h4 style="color:#be2020 !important; front-size:1.3125rem;">Sorry! You are removed from this group.</h4>
            </div>
        </div>
                `);
                if (data.user_id != sender_id) {
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

                let timezone = 'America/New_York';
                let created_at = data.chat.created_at;
                let time = moment.tz(created_at, timezone).format('h:mm A');

                let chat_member_id_array = data.chat_member_id;

                if (data.chat.user_id != sender_id && chat_member_id_array.includes(sender_id)) {
                    let html = `
        <div class="message you">
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
                                `<a href="${data.file_url}" download="${data.file_url}"><img src="{{ asset('user_assets/images/file.png') }}" alt=""></a>`;
                        }
                    } else {
                        html += `${data.chat.message}`;
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

            });

        });
    </script>
@endpush
