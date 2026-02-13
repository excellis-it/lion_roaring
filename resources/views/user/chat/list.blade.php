@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} - User Chat
@endsection
@push('styles')
    <style>
        .highlight {
            background-color: yellow;
            font-weight: bold;
        }

        .user-search-box {
            padding: 10px;
            background: #fff;
            border-bottom: 1px solid #eee;
        }

        .user-search-box .search-field {
            position: relative;
        }

        .user-search-box .search-field input {
            border-radius: 20px;
            padding-right: 35px;
            border: 1px solid #ddd;
        }

        .user-search-box .search-field i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
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
                    <div class="heading_hp">
                        <h2>Messaging</h2>
                    </div>
                </div>
                <div class="SideNavhead">
                    <h2>Chat</h2>
                </div>
                <div class="user-search-box">
                    <div class="search-field">
                        <input type="text" id="user-search" placeholder="Search users..." class="form-control">
                        <i class="fa fa-search"></i>
                    </div>
                </div>
                <input type="hidden" id="last_activate_user" value="0">
                <div class="main">
                    <div>
                        <div class="sideNav2 main-sidebar-chat-list" id="group-manage-{{ Auth::user()->id }}">

                            @if (count($users) > 0)
                                @foreach ($users as $user)
                                    <li class="group user-list" id="chat_list_user_{{ $user['id'] }}"
                                        data-id="{{ $user['id'] }}">
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
                                        <p class="GroupDescrp last-chat-{{ isset($user['last_message']) ? $user['last_message']['id'] : '' }}"
                                            id="message-app-{{ $user['id'] }}">
                                            @if (isset($user['last_message']['message']))
                                                {!! $user['last_message']['message'] !!}
                                            @endif
                                            @if (isset($user['last_message']) &&
                                                    $user['last_message']['message'] == null &&
                                                    $user['last_message']['attachment'] != null)
                                                <span><i class="ti ti-file"></i></span>
                                            @endif
                                        </p>
                                        <div class="time_online"
                                            id="last-chat-time-{{ isset($user['last_message']) ? $user['last_message']['id'] : '' }}">
                                            @if (isset($user['last_message']['created_at']))
                                                <p>{{ $user['last_message']['created_at']->format('h:i A') }}</p>
                                            @endif
                                        </div>
                                        @if (Helper::getCountUnseenMessage(Auth::user()->id, $user['id']) > 0)
                                            <div class="count-unseen" id="count-unseen-{{ $user['id'] }}">
                                                <span>
                                                    <p>{{ Helper::getCountUnseenMessage(Auth::user()->id, $user['id']) }}
                                                    </p>
                                                </span>
                                            </div>
                                        @endif

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
                var query = $('#search').val().toLowerCase();

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
                var container = $('.MessageContainer');
                var containerHeight = container.height();
                var targetPosition = target.offset().top + container.scrollTop() - container.offset().top;

                container.animate({
                    scrollTop: targetPosition - containerHeight / 2 + target.height() / 2
                }, 500);
            }

            $(document).on('input', '#search', debounce(searchAndHighlight, 300));

            $(document).on('keypress', '#search', function(e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault();

                    var highlighted = $('.highlight').closest('.message');
                    if (highlighted.length > 0) {
                        currentIndex = (currentIndex + 1) % highlighted.length;
                        scrollToHighlighted(highlighted.eq(currentIndex));
                    }
                }
            });

            // User search functionality
            $(document).on('keyup', '#user-search', function() {
                var value = $(this).val().toLowerCase();
                $(".user-list").each(function() {
                    var userName = $(this).find('.GroupName').text().toLowerCase();
                    if (userName.indexOf(value) > -1) {
                        $(this).attr('style', 'display: grid !important');
                    } else {
                        $(this).attr('style', 'display: none !important');
                    }
                });

                if ($(".user-list:visible").length === 0) {
                    if ($("#no-user-found").length === 0) {
                        $(".main-sidebar-chat-list").append(
                            '<p id="no-user-found" class="p-3 text-center">No users found</p>');
                    }
                } else {
                    $("#no-user-found").remove();
                }
            });
        });
    </script>
@endpush
