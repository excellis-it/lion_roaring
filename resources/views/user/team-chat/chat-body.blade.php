@if (isset($is_chat))
    <div class="groupChatHead">
        <div class="main_avtar"><img
                src="{{ $team['group_image'] ? Storage::url($team['group_image']) : asset('user_assets/images/group.png') }}"
                alt=""></div>
        <div class="group_text">
            <p class="GroupName">{{ $team['name'] ?? '' }}</p>
            <span>{{ $team_member_name ? (strlen($team_member_name) > 60 ? substr($team_member_name, 0, 60) . '...' : $team_member_name) : '' }}</span>
        </div>
        <div class="group_text_right">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <li><a class="dropdown-item" data-bs-toggle="modal" href="#groupInfo">Group
                            info</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="MessageContainer" id="team-chat-container-{{ $team['id'] }}">
        @if ($team_chats->count() > 0)
            @foreach ($team_chats->groupBy(function ($team_chat) {
        if ($team_chat->created_at->format('d M Y') == date('d M Y')) {
            return 'Today';
        } elseif ($team_chat->created_at->format('d M Y') == date('d M Y', strtotime('-1 day'))) {
            return 'Yesterday';
        } else {
            return $team_chat->created_at->format('d M Y');
        }
    }) as $date => $groupedChats)
                <div class="messageSeperator"><span>{{ $date }}</span></div>
                @foreach ($groupedChats as $chat)
                    @if ($chat->user_id == Auth::user()->id)
                        <div class="message me">
                            <p class="messageContent"> {{ $chat->message }} </p>
                            <div class="messageDetails">
                                <div class="messageTime">{{ $chat->created_at->format('h:i A') }}</div>
                                {{-- <i class="fas fa-check-double"></i> --}}
                            </div>
                        </div>
                    @else
                        <div class="message you">
                            <div class="d-flex">
                                <div class="member_image">
                                    <span><img src="{{ $chat->user->profile_picture ? Storage::url($chat->user->profile_picture) : asset('user_assets/images/profile_dummy.png') }}"
                                            alt=""></span>
                                </div>
                                <div class="message_group">
                                    <p class="messageContent">
                                        <span class="namemember">{{ $chat->user->full_name }}</span>
                                        {{ $chat->message }}
                                    </p>
                                    <div class="messageDetails">
                                        <div class="messageTime">{{ $chat->created_at->format('h:i A') }}</div>
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endforeach
        @endif
    </div>
    <form id="TeamMessageForm">
        <input type="text" id="TeamMessageInput" placeholder="Type a message...">
        <input type="hidden" id="team_id" value="{{ $team['id'] }}" class="team_id">
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
    </form>
@else
    <div class="icon_chat">
        <span><img src="{{ asset('user_assets/images/icon-chat.png') }}" alt=""></span>
        <h4>Seamless Real-Time Chat | Connect Instantly</h4>
        <p>Join our dynamic chat platform, where real-time communication is effortless. Engage in private and group
            conversations, manage your contacts, and stay connected with instant updates. Experience a secure and
            responsive interface, perfect for personal or professional use.</p>
    </div>
@endif
