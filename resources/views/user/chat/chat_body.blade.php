@php
    use App\Helpers\Helper;
@endphp
<style>
    .Send {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
        border: none;
        border-radius: 50%;
        background-color: #6200ea;
        cursor: pointer;
        width: 40px;
        height: 40px;
        transition: background-color 0.3s;
    }

    .Send:hover {
        background-color: #7c4dff;
    }

    .Send svg {
        display: block;
    }

    .Send.sendloading svg {
        display: none;
    }

    .send-dots-container {
        display: none;
        align-items: center;
        justify-content: space-between;
        width: 24px;
        height: 12px;
    }

    .Send.sendloading .send-dots-container {
        display: flex;
    }

    .send-dot {
        width: 6px;
        height: 6px;
        background-color: white;
        border-radius: 50%;
    }

    .Send.sendloading .send-dot:nth-child(1) {
        animation: bounce 0.8s infinite;
        animation-delay: 0s;
    }

    .Send.sendloading .send-dot:nth-child(2) {
        animation: bounce 0.8s infinite;
        animation-delay: 0.2s;
    }

    .Send.sendloading .send-dot:nth-child(3) {
        animation: bounce 0.8s infinite;
        animation-delay: 0.4s;
    }

    @keyframes bounce {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-6px);
        }
    }
</style>
@if (isset($is_chat))
    <div class="row align-items-center">
        <div class="col-xxl-3">
            <button id="backButton" style="color:#000;" class="btn btn-light me-3 d-inline-flex align-items-center backButton">
                    <i class="fa fa-arrow-left me-1"></i> Back
            </button>
        </div>
        <div class="col-xxl-5">
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
        </div>
        <div class="col-xxl-4">
            <div class="d-flex align-items-center">

                <div class="search-field w-100 mb-0">
                    <input type="text" name="search" id="search" placeholder="search..." required=""
                        class="form-control rounded_search">
                    <button class="submit_search" id="search-button"> <span class=""><i
                                class="fa fa-search"></i></span></button>
                </div>
                <div class="group_text_right clear-chat-button ms-4">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item clear-chat" data-reciver-id="{{ $reciver->id }}">Clear
                                    historical chats</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
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
                        <div class="message me" id="chat-message-{{ $chat->id }}">
                        @else
                            <div class="message you" id="chat-message-{{ $chat->id }}">
                    @endif
                    <div class="message-wrap">
                        <p class="messageContent" data-original-content="{{ $chat->message }}">

                            @if ($chat->attachment != null)
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
                                    <a href="{{ Storage::url($chat->attachment) }}" target="_blank"
                                        download="{{ $chat->attachment }}">
                                        <img src="{{ asset('user_assets/images/file.png') }}" alt="">
                                    </a>
                                @endif
                                <br>
                            @endif

                            @if ($chat->message != null)
                                {!! Helper::formatChatMessage($chat->message) !!}
                            @endif
                        </p>
                        @if ($chat->sender_id == Auth::user()->id)
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                    id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li><a class="dropdown-item remove-chat" data-chat-id="{{ $chat->id }}"
                                            data-del-from="me">Remove For Me</a></li>
                                    <li><a class="dropdown-item remove-chat" data-chat-id="{{ $chat->id }}"
                                            data-del-from="everyone">Remove For Everyone</a></li>
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="messageDetails">
                        <div class="messageTime">{{ $chat->created_at->format('h:i A') }}</div>

                        <div id="seen_{{ $chat->id }}">
                            @if ($chat->sender_id == Auth::user()->id)
                                @if ($chat->seen == 1)
                                    <i class="fas fa-check-double"></i>
                                @else
                                    <i class="fas fa-check"></i>
                                @endif
                            @endif
                        </div>
                    </div>
    </div>
@endforeach
@endforeach
@else
<p class="" style="color: black"></p>
@endif
</div>

<!-- File name display -->
<div id="file-name-display"
    style="display:none; margin-top: 5px; color: #555; font-size: 14px;background-color: #d5c8e5;" class="p-2 w-100">
</div>
<form id="MessageForm" enctype="multipart/form-data">
    @csrf
    <input type="hidden" class="reciver_id" value="{{ $reciver->id }}">
    {{-- file upload via form --}}
    <input type="file" id="file2" style="display: none" name="file">
    {{-- direct file upload --}}
    <input type="file" id="file" style="display: none" name="file">
    <div class="file-upload">
        <label for="file2" id="hit-chat-file">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                viewBox="0 0 24 24">
                <path fill="currentColor" fill-rule="evenodd"
                    d="M9 7a5 5 0 0 1 10 0v8a7 7 0 1 1-14 0V9a1 1 0 0 1 2 0v6a5 5 0 0 0 10 0V7a3 3 0 1 0-6 0v8a1 1 0 1 0 2 0V9a1 1 0 1 1 2 0v6a3 3 0 1 1-6 0z"
                    clip-rule="evenodd" style="color:black"></path>
            </svg>
        </label>
    </div>

    <textarea type="text" id="MessageInput" placeholder="Type a message..." rows="1" class="form-control"></textarea>
    <div>
        <button class="Send">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M0.82186 0.827412C0.565716 0.299519 0.781391 0.0763349 1.32445 0.339839L20.6267 9.70604C21.1614 9.96588 21.1578 10.4246 20.6421 10.7179L1.6422 21.526C1.11646 21.8265 0.873349 21.6115 1.09713 21.0513L4.71389 12.0364L15.467 10.2952L4.77368 8.9726L0.82186 0.827412Z"
                    fill="white" />
            </svg>
            <span class="send-dots-container">
                <span class="send-dot"></span>
                <span class="send-dot"></span>
                <span class="send-dot"></span>
            </span>
        </button>
    </div>
</form>
@else
<div class="icon_chat">
    <span><img src="{{ asset('user_assets/images/icon-chat.png') }}" alt=""></span>
    <h4>Seamless Real-Time Chat | Connect Instantly</h4>
    <p>Join our dynamic chat platform, where real-time communication is effortless. Engage in private and group
        conversations, manage your contacts, and stay connected with instant updates. Experience a secure and responsive
        interface, perfect for personal or professional use.</p>
</div>
@endif
