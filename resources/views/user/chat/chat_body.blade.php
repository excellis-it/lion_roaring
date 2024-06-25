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
                        @else
                            <div class="message you">
                    @endif
                    <p class="messageContent">
                        @if ($chat->message != null)
                            {{ $chat->message }}
                        @else
                            @php
                                $ext = pathinfo($chat->attachment, PATHINFO_EXTENSION);
                            @endphp
                            @if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']))
                                <a href="{{ Storage::url($chat->attachment) }}" target="_blank">
                                    <img src="{{ Storage::url($chat->attachment) }}" alt=""
                                        style="max-width: 200px; max-height: 200px;">
                                </a>
                            @elseif (in_array($ext, ['mp4', 'webm', 'ogg']))
                                <video width="200" height="200" controls>
                                    <source src="{{ Storage::url($chat->attachment) }}"
                                        type="video/{{ $ext }}">
                                </video>
                            @else
                                <a href="{{ Storage::url($chat->attachment) }}" target="_blank" download="{{ $chat->attachment }}">
                                    <img src="{{ asset('user_assets/images/file.png') }}" alt="">
                                </a>
                            @endif
                        @endif
                    </p>
                    <div class="messageDetails">
                        <div class="messageTime">{{ $chat->created_at->format('h:i A') }}</div>
                        @if ($chat->sender_id == Auth::user()->id)
                            @if ($chat->seen == 1)
                                <i class="fas fa-check-double"></i>
                            @else
                                <i class="fas fa-check"></i>
                            @endif
                        @endif
                    </div>
    </div>
@endforeach
@endforeach
@else
<p class="" style="color: black"></p>
@endif
</div>
<form id="MessageForm">
    <input type="hidden" class="reciver_id" value="{{ $reciver->id }}">
    {{-- file upoad --}}
    <input type="file" id="file" style="display: none">
    <div class="file-upload">
        <label for="file">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path fill="currentColor" fill-rule="evenodd"
                    d="M9 7a5 5 0 0 1 10 0v8a7 7 0 1 1-14 0V9a1 1 0 0 1 2 0v6a5 5 0 0 0 10 0V7a3 3 0 1 0-6 0v8a1 1 0 1 0 2 0V9a1 1 0 1 1 2 0v6a3 3 0 1 1-6 0z"
                    clip-rule="evenodd" style="color:black"></path>
            </svg>
        </label>
    </div>
    <input type="text" id="MessageInput" placeholder="Type a message...">
    <div>
        <button class="Send">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
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
