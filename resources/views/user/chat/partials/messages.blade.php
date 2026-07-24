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
                            $ext = strtolower(pathinfo($chat->attachment, PATHINFO_EXTENSION));
                            $videoMime = [
                                'mp4' => 'video/mp4',
                                'm4v' => 'video/mp4',
                                'webm' => 'video/webm',
                                'ogg' => 'video/ogg',
                                'mov' => 'video/quicktime',
                            ];
                        @endphp
                        @php
                            $mediaUrl = \App\Helpers\Helper::chatMediaUrl($chat->attachment) ?: Storage::url($chat->attachment);
                            // Detect type from original when compressed path is .webp/.jpg of an image
                            $mediaExt = strtolower(pathinfo(parse_url($mediaUrl, PHP_URL_PATH) ?? $chat->attachment, PATHINFO_EXTENSION));
                            if (!in_array($mediaExt, ['jpg','jpeg','png','gif','svg','webp','mp4','webm','ogg','mov','m4v'], true)) {
                                $mediaExt = $ext;
                            }
                        @endphp
                        @if (in_array($mediaExt, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']))
                            <a href="{{ $mediaUrl }}" class="chat-image-preview"
                                data-image-url="{{ $mediaUrl }}"
                                data-file-name="{{ $chat->attachment_name ?? pathinfo($chat->attachment, PATHINFO_BASENAME) }}">
                                <img class="chat-image-attachment" src="{{ $mediaUrl }}" alt=""
                                    style="max-width: 280px; max-height: 360px; width: auto; height: auto;">
                            </a>
                        @elseif (in_array($mediaExt, ['mp4', 'webm', 'ogg', 'mov', 'm4v']))
                            {{-- Thumbnail only; click opens video player modal --}}
                            <button type="button" class="chat-video-preview"
                                data-video-url="{{ $mediaUrl }}"
                                data-file-name="{{ $chat->attachment_name ?? pathinfo($chat->attachment, PATHINFO_BASENAME) }}"
                                data-mime="{{ $videoMime[$mediaExt] ?? 'video/mp4' }}"
                                aria-label="Play video">
                                <video class="chat-video-attachment" muted playsinline preload="metadata"
                                    style="max-width: 280px; max-height: 360px; width: auto; height: auto;">
                                    <source src="{{ $mediaUrl }}" type="{{ $videoMime[$mediaExt] ?? 'video/mp4' }}">
                                </video>
                                <span class="chat-video-play-icon" aria-hidden="true"><i class="fa-solid fa-play"></i></span>
                            </button>
                        @else
                            <a href="{{ $mediaUrl }}" target="_blank" class="file-download"
                                data-download-url="{{ $mediaUrl }}"
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
