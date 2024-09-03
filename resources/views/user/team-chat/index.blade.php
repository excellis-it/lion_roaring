@extends('user.layouts.master')
@section('title')
    Team - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <style>
        .highlight {
            background-color: yellow;
            font-weight: bold;
        }
    </style>
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

            $(document).on('keyup', '#member-search', function() {
                // Get the search term, trim any leading/trailing spaces, and convert to lowercase
                var searchTerm = $(this).val().toLowerCase().trim();

                $('#add-member .member-item').each(function() {
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
            var debounceTimer;
            var currentIndex = -1;

            function debounce(func, wait) {
                return function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(func, wait);
                };
            }

            function searchAndHighlight() {
                var query = $('#search-chat').val().toLowerCase();

                // Reset all messages to their original state
                $('.messageContent').each(function() {
                    var originalContent = $(this).data('originalContent');
                    if (originalContent) {
                        $(this).html(originalContent);
                    }
                });

                var highlighted = [];

                if (query) {
                    $('.messageContent').each(function() {
                        var content = $(this).text();
                        var lowerContent = content.toLowerCase();
                        if (lowerContent.includes(query)) {
                            if (!$(this).data('originalContent')) {
                                $(this).data('originalContent', content);
                            }

                            var regex = new RegExp('(' + query + ')', 'gi');
                            var newContent = content.replace(regex, '<span class="highlight">$1</span>');
                            $(this).html(newContent);

                            highlighted.push($(this).closest('.message'));
                        }
                    });

                    if (highlighted.length > 0) {
                        currentIndex = 0;
                        scrollToHighlighted(highlighted[currentIndex]);
                    }
                }
            }

            function scrollToHighlighted(target) {
                var container = target.closest('.MessageContainer'); // Closest container
                var containerHeight = container.height();
                var targetPosition = target.position().top + container.scrollTop();

                container.animate({
                    scrollTop: targetPosition - containerHeight / 2 + target.height() / 2
                }, 500);
            }

            $(document).on('input', '#search-chat', debounce(searchAndHighlight, 300));

            $(document).on('keypress', '#search-chat', function(e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault();

                    var highlighted = $('.highlight').closest('.message');
                    if (highlighted.length > 0) {
                        currentIndex = (currentIndex + 1) % highlighted.length;
                        scrollToHighlighted(highlighted.eq(currentIndex));
                    }
                }
            });
        });
    </script>

@endpush
