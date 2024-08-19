@extends('user.layouts.master')
@section('title')
    Team - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
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
                                                <input class="btn-inputfile" id="file01" type="file"
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
                <div class="modal fade" id="exampleModalToggle2" aria-hidden="true"
                    aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content group_create">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalToggleLabel2">Add Member</h5>
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


                <div class="modal fade" id="groupInfo" aria-hidden="true" aria-labelledby="groupInfo1" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content group_create">
                            <div class="modal-header">
                                <h5 class="modal-title" id="groupInfo1">Group Info</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="group_crate">
                                    <div class="group_text_right">
                                        <div class="dropdown">
                                            <button class="group_text_right" type="button" id="dropdownMenuButton1"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                <li><a class="dropdown-item" data-bs-toggle="modal" href="#">Edit
                                                        Name</a></li>
                                                <li><a class="dropdown-item" data-bs-toggle="modal" href="#">Edit
                                                        Member</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="image-upload">
                                            <div class="image-wrap"><img id="previewImage01"
                                                    src="assets/images/group.jpg" /></div>
                                            <input class="btn-inputfile" id="file01" type="file" name="file" />
                                            <label for="file01"><i class="fa-solid fa-camera"></i></label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <h4>Group Name</h4>
                                    </div>
                                    <div class="mb-3">
                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Officia, vel a. Iste
                                            repudiandae
                                            ipsam illum cum reiciendis et, voluptatibus aliquam aperiam facere maiores odit
                                            quis
                                            asperiores nihil, corrupti commodi consectetur?</p>
                                    </div>
                                </div>
                                <div class="min_height400">
                                    <ul>
                                        <li class="group">
                                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                                    alt=""></div>
                                            <p class="GroupName">David Johnson</p>
                                            <p class="GroupDescrp">Lorem ipsum dolor sit amet</p>
                                            <div class="time_online">
                                                <div class="dropdown">
                                                    <button class="eleplish dropdown-toggle" type="button"
                                                        id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                        <li><a class="dropdown-item" data-bs-toggle="modal"
                                                                href="#">Remove Group</a></li>
                                                        <li><a class="dropdown-item" data-bs-toggle="modal"
                                                                href="#">Add admin</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="group">
                                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                                    alt=""></div>
                                            <p class="GroupName">David Johnson</p>
                                            <p class="GroupDescrp">Lorem ipsum dolor sit amet </p>
                                            <div class="time_online"><i class="fa-solid fa-ellipsis-vertical"></i></div>
                                        </li>
                                        <li class="group">
                                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                                    alt=""></div>
                                            <p class="GroupName">David Johnson</p>
                                            <p class="GroupDescrp">Lorem ipsum dolor sit amet</p>
                                            <div class="time_online"><i class="fa-solid fa-ellipsis-vertical"></i></div>
                                        </li>
                                        <li class="group">
                                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                                    alt=""></div>
                                            <p class="GroupName">David Johnson</p>
                                            <p class="GroupDescrp">Lorem ipsum dolor sit amet.</p>
                                            <div class="time_online"><i class="fa-solid fa-ellipsis-vertical"></i></div>
                                        </li>
                                        <li class="group">
                                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                                    alt=""></div>
                                            <p class="GroupName">David Johnson</p>
                                            <p class="GroupDescrp">Lorem ipsum dolor sit amet.</p>
                                            <div class="time_online"><i class="fa-solid fa-ellipsis-vertical"></i></div>
                                        </li>
                                        <li class="group">
                                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                                    alt=""></div>
                                            <p class="GroupName">David Johnson</p>
                                            <p class="GroupDescrp">Lorem ipsum dolor sit amet.</p>
                                            <div class="time_online"><i class="fa-solid fa-ellipsis-vertical"></i></div>
                                        </li>
                                        <li class="group">
                                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                                    alt=""></div>
                                            <p class="GroupName">David Johnson</p>
                                            <p class="GroupDescrp">Lorem ipsum dolor sit amet.</p>
                                            <div class="time_online"><i class="fa-solid fa-ellipsis-vertical"></i></div>
                                        </li>
                                        <li class="group">
                                            <div class="avatar"><img src="{{ asset('user_assets/images/group.jpg') }}"
                                                    alt=""></div>
                                            <p class="GroupName">David Johnson</p>
                                            <p class="GroupDescrp">Lorem ipsum dolor sit amet.</p>
                                            <div class="time_online"><i class="fa-solid fa-ellipsis-vertical"></i></div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="exit_group">
                                    <a href=""><i class="fa-solid fa-right-from-bracket"></i> Exit Group</a>
                                </div>
                            </div>
                            <!-- <div class="modal-footer">
                                                        <button class="btn btn-primary" data-bs-target="#exampleModalToggle" data-bs-toggle="modal" data-bs-dismiss="modal">Back to Create Page</button>
                                                        <button class="btn btn-primary" data-bs-dismiss="modal">Submit</button>
                                                      </div> -->
                        </div>
                    </div>
                </div>

                <div class="main">
                    <div class="sideNav2 group-list">
                        @if (count($teams) > 0)
                            @foreach ($teams as $team)
                                <li class="group group-data" data-id="{{ $team->id }}">
                                    <div class="avatar"><img
                                            src="{{ $team->group_image ? Storage::url($team->group_image) : asset('user_assets/images/group.jpg') }}"
                                            alt=""></div>
                                    <p class="GroupName">{{ $team->name }}</p>
                                    <p class="GroupDescrp">{{ $team->lastMessage ? $team->lastMessage->message : '' }}</p>
                                    <div class="time_online">
                                        {{ $team->lastMessage ? $team->lastMessage->created_at->format('h:i A') : '' }}
                                    </div>
                                </li>
                            @endforeach
                        @else
                            <li class="group">
                                <p></p>
                                <p class="" style="color: black">No Group Found</p>
                            </li>
                        @endif
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
        $(".btn-inputfile").change(function() {
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
    </script>
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
    <script>
        $(document).ready(function() {
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
                                    $("#MessageForm").submit();
                                }
                            });
                    },
                    error: function(xhr) {
                        toastr.error('Something went wrong');
                    }
                });
            }

            $(document).on('click', '.group-data', function() {
                var teamId = $(this).data('id');
                loadChat(teamId);
            });

            function scrollChatToBottom(team_id) {
                var messages = document.getElementById("team-chat-container-" + team_id);
                messages.scrollTop = messages.scrollHeight;
            }

            // Send message

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

                        // append new message to the chat
                        var data = resp.chat;
                        var html = `<div class="message me">
                                        <p class="messageContent">${data.message}</p>
                                        <div class="messageDetails">
                                            <div class="messageTime">${data.created_at}</div>
                                        </div>
                                    </div>`;
                        $('#team-chat-container-' + data.team_id).append(html);
                        scrollChatToBottom(data.team_id);
                        
                    },
                    error: function(xhr) {
                        toastr.error('Something went wrong');
                    }
                });
            });
        });
    </script>
@endpush
