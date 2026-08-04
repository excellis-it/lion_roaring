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

            <div class="messaging_sec chat-layout">
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
                                            @php
                                                $avatarFallback = asset('user_assets/images/profile_dummy.png');
                                                $avatarUrl = \App\Helpers\Helper::publicStorageUrl($user['profile_picture'] ?? null)
                                                    ?: $avatarFallback;
                                            @endphp
                                            <img src="{{ $avatarUrl }}" alt=""
                                                onerror="this.onerror=null;this.src='{{ $avatarFallback }}';">
                                        </div>
                                        <p class="GroupName notranslate" translate="no">{!! no_translate($user['full_name'] ?? trim(implode(' ', array_filter([$user['first_name'] ?? null, $user['middle_name'] ?? null, $user['last_name'] ?? null])))) !!}</p>
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
                var query = ($('#search').val() || '').trim();
                var $messages = $('.MessageContainer .message');
                var $contents = $('.MessageContainer .messageContent');
                var highlighted = [];

                // Restore original HTML for any previously modified bubbles
                $contents.each(function() {
                    var originalHtml = $(this).data('searchOriginalHtml');
                    if (originalHtml !== undefined) {
                        $(this).html(originalHtml);
                    }
                });

                $messages.removeClass('chat-search-match chat-search-dimmed');
                $('.MessageContainer').removeClass('chat-is-searching');

                if (!query) {
                    currentIndex = -1;
                    return;
                }

                $('.MessageContainer').addClass('chat-is-searching');
                var escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                var regex = new RegExp('(' + escaped + ')', 'gi');
                var queryLower = query.toLowerCase();

                $contents.each(function() {
                    var $el = $(this);
                    var $message = $el.closest('.message');

                    if ($el.data('searchOriginalHtml') === undefined) {
                        $el.data('searchOriginalHtml', $el.html());
                    }

                    var text = $el.text();
                    if (text && text.toLowerCase().indexOf(queryLower) !== -1) {
                        highlightChatTextNodes(this, regex);
                        $message.addClass('chat-search-match');
                        highlighted.push($message);
                    } else {
                        // Keep full history visible for context; only dim non-matches
                        $message.addClass('chat-search-dimmed');
                    }
                });

                if (highlighted.length > 0) {
                    currentIndex = 0;
                    scrollToHighlighted(highlighted[0]);
                } else {
                    currentIndex = -1;
                }
            }

            function highlightChatTextNodes(root, regex) {
                var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, {
                    acceptNode: function(node) {
                        if (!node.nodeValue || !node.nodeValue.trim()) {
                            return NodeFilter.FILTER_REJECT;
                        }
                        if ($(node.parentNode).closest('script, style, .chat-search-highlight').length) {
                            return NodeFilter.FILTER_REJECT;
                        }
                        return NodeFilter.FILTER_ACCEPT;
                    }
                });

                var textNodes = [];
                while (walker.nextNode()) {
                    textNodes.push(walker.currentNode);
                }

                textNodes.forEach(function(textNode) {
                    var text = textNode.nodeValue;
                    regex.lastIndex = 0;
                    if (!regex.test(text)) {
                        return;
                    }
                    regex.lastIndex = 0;

                    var frag = document.createDocumentFragment();
                    var lastIndex = 0;
                    var match;
                    while ((match = regex.exec(text)) !== null) {
                        if (match.index > lastIndex) {
                            frag.appendChild(document.createTextNode(text.slice(lastIndex, match.index)));
                        }
                        var mark = document.createElement('mark');
                        mark.className = 'chat-search-highlight';
                        mark.textContent = match[0];
                        frag.appendChild(mark);
                        lastIndex = match.index + match[0].length;
                    }
                    if (lastIndex < text.length) {
                        frag.appendChild(document.createTextNode(text.slice(lastIndex)));
                    }
                    textNode.parentNode.replaceChild(frag, textNode);
                });
            }

            function scrollToHighlighted(target) {
                var container = $('.MessageContainer');
                if (!target || !target.length || !container.length) {
                    return;
                }
                var containerHeight = container.height();
                var targetPosition = target.offset().top + container.scrollTop() - container.offset().top;

                container.animate({
                    scrollTop: targetPosition - containerHeight / 2 + target.height() / 2
                }, 500);
            }

            $(document).on('input', '#search', debounce(searchAndHighlight, 300));
            $(document).on('click', '#search-button', function(e) {
                e.preventDefault();
                searchAndHighlight();
            });

            $(document).on('keypress', '#search', function(e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault();

                    var highlighted = $('.MessageContainer .message.chat-search-match');
                    if (highlighted.length > 0) {
                        currentIndex = (currentIndex + 1) % highlighted.length;
                        scrollToHighlighted(highlighted.eq(currentIndex));
                    }
                }
            });

            // User search functionality
            $(document).on('keyup', '#user-search', function() {
                var value = $(this).val().toLowerCase().replace(/\s+/g, ' ').trim();
                $(".user-list").each(function() {
                    var userName = $(this).find('.GroupName').text().toLowerCase().replace(/\s+/g, ' ').trim();
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
