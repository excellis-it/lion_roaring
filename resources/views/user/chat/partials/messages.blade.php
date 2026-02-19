@php
    use App\Helpers\Helper;
@endphp

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
                            <a href="{{ Storage::url($chat->attachment) }}" target="_blank" class="file-download"
                                data-download-url="{{ Storage::url($chat->attachment) }}"
                                data-file-name="{{ $chat->attachment_name ?? pathinfo($chat->attachment, PATHINFO_BASENAME) }}">
                                <img src="{{ Storage::url($chat->attachment) }}" alt=""
                                    style="max-width: 200px; max-height: 200px;">
                            </a>
                        @elseif (in_array($ext, ['mp4', 'webm', 'ogg']))
                            <video width="200" height="200" controls>
                                <source src="{{ Storage::url($chat->attachment) }}" type="video/{{ $ext }}">
                            </video>
                        @else
                            <a href="{{ Storage::url($chat->attachment) }}" target="_blank" class="file-download"
                                data-download-url="{{ Storage::url($chat->attachment) }}"
                                data-file-name="{{ $chat->attachment_name ?? pathinfo($chat->attachment, PATHINFO_BASENAME) }}">
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
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1"
                            data-bs-toggle="dropdown" aria-expanded="false">
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
@endif
