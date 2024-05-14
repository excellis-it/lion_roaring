@if (isset($is_chat))
    <div class="ChatHead">
        <div class="main_avtar">
            @if ($reciver->profile_picture)
                <img src="{{ Storage::url($reciver->profile_picture) }}" alt="">
            @else
                <img src="{{ asset('user_assets/images/profile_dummy.png') }}" alt="">
            @endif
        </div>
        <p class="GroupName">{{ $reciver->full_name }}</p>
    </div>
    <div class="MessageContainer" id="chat-container-{{ $reciver->id }}">
        @if ($chats->count() > 0)
            @foreach ($chats->groupBy(function ($chat) {
        if ($chat->created_at->format('d M Y') == date('d M Y')) {
            return 'Today';
        } elseif ($chat->created_at->format('d M Y') == date('d M Y', strtotime('-1 day'))) {
            return 'Yesterday';
        } else {
            return $chat->created_at->format('d M Y');
        }
    }) as $date => $groupedChats)
                <div class="messageSeperator"><span>{{ $date }}</span></div>
                @foreach ($groupedChats as $chat)
                    @if ($chat->sender_id == Auth::user()->id)
                        <div class="message me">
                            <p class="messageContent">{{ $chat->message }}</p>
                            <div class="messageDetails">
                                <div class="messageTime">{{ $chat->created_at->format('h:i A') }}</div>
                                @if ($chat->seen == 1)
                                    <i class="fas fa-check-double"></i>
                                @else
                                    <i class="fas fa-check"></i>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="message you">
                            <p class="messageContent">{{ $chat->message }}</p>
                            <div class="messageDetails">
                                <div class="messageTime">{{ $chat->created_at->format('h:i A') }}</div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endforeach
        @else
            <p class="" style="color: black"></p>
        @endif
    </div>
    <form id="MessageForm">
        <input type="hidden" class="reciver_id" value="{{ $reciver->id }}">
        <input type="text" id="MessageInput" placeholder="Type a message...">
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
        <h4>Lorem ipsum dolor sit amet consectetur adipisicing elit.</h4>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
    </div>
@endif
