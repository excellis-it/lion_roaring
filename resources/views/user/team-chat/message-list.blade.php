@php
    use App\Helpers\Helper;
@endphp
@if ($team_chats->count() > 0)
    @php
        $groupedChats = $team_chats->groupBy(function ($team_chat) {
            if ($team_chat->created_at->format('d M Y') == date('d M Y')) {
                return 'Today';
            } elseif ($team_chat->created_at->format('d M Y') == date('d M Y', strtotime('-1 day'))) {
                return 'Yesterday';
            } else {
                return $team_chat->created_at->format('d M Y');
            }
        });
    @endphp
    @foreach ($groupedChats as $date => $chats)
        <div class="messageSeperator"><span>{{ $date }}</span></div>
        @foreach ($chats as $chat)
            @if ($chat->user_id == Auth::user()->id)
                <div class="message me" id="team-chat-message-{{ $chat->id }}">
                    <div class="message-wrap">
                        <p class="messageContent">
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
                                @if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']))
                                    <a href="{{ \App\Helpers\Helper::chatMediaUrl($chat->attachment) ?: Storage::url($chat->attachment) }}" class="chat-image-preview"
                                        data-image-url="{{ \App\Helpers\Helper::chatMediaUrl($chat->attachment) ?: Storage::url($chat->attachment) }}"
                                        data-file-name="{{ $chat->attachment_name ?? pathinfo($chat->attachment, PATHINFO_BASENAME) }}">
                                        <img class="chat-image-attachment" src="{{ \App\Helpers\Helper::chatMediaUrl($chat->attachment) ?: Storage::url($chat->attachment) }}" alt=""
                                            style="max-width: 280px; max-height: 360px; width: auto; height: auto;">
                                    </a>
                                @elseif (in_array($ext, ['mp4', 'webm', 'ogg', 'mov', 'm4v']))
                                    <button type="button" class="chat-video-preview"
                                        data-video-url="{{ \App\Helpers\Helper::chatMediaUrl($chat->attachment) ?: Storage::url($chat->attachment) }}"
                                        data-file-name="{{ $chat->attachment_name ?? pathinfo($chat->attachment, PATHINFO_BASENAME) }}"
                                        data-mime="{{ $videoMime[$ext] ?? 'video/mp4' }}"
                                        aria-label="Play video">
                                        <video class="chat-video-attachment" muted playsinline preload="metadata"
                                            style="max-width: 280px; max-height: 360px; width: auto; height: auto;">
                                            <source src="{{ \App\Helpers\Helper::chatMediaUrl($chat->attachment) ?: Storage::url($chat->attachment) }}"
                                                type="{{ $videoMime[$ext] ?? 'video/mp4' }}">
                                        </video>
                                        <span class="chat-video-play-icon" aria-hidden="true"><i class="fa-solid fa-play"></i></span>
                                    </button>
                                @else
                                    <a href="{{ \App\Helpers\Helper::chatMediaUrl($chat->attachment) ?: Storage::url($chat->attachment) }}" target="_blank"
                                        class="file-download" data-download-url="{{ \App\Helpers\Helper::chatMediaUrl($chat->attachment) ?: Storage::url($chat->attachment) }}"
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
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                {{-- <li><a class="dropdown-item clear-chat-only-me"  data-reciver-id="{{ $reciver->id }}">Clear chats for me</a></li> --}}
                                <li><a class="dropdown-item team-remove-chat" data-chat-id="{{ $chat->id }}"
                                        data-team-id="{{ $chat->team_id }}" data-del-from="me">Remove For Me</a></li>
                                <li><a class="dropdown-item team-remove-chat" data-chat-id="{{ $chat->id }}"
                                        data-team-id="{{ $chat->team_id }}" data-del-from="everyone">Remove For
                                        Everyone</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="messageDetails">
                        <div class="messageTime">{{ $chat->created_at->format('h:i A') }}</div>
                        {{-- <i class="fas fa-check-double"></i> --}}
                    </div>
                </div>
            @else
                <div class="message you" id="team-chat-message-{{ $chat->id }}">
                    <div class="d-flex">
                        <div class="member_image">
                            <span>
                                @if ($chat->user)
                                    <img src="{{ $chat->user->profile_picture ? Storage::url($chat->user->profile_picture) : asset('user_assets/images/profile_dummy.png') }}"
                                        alt="">
                                @else
                                    <img src="{{ asset('user_assets/images/profile_dummy.png') }}" alt="">
                                @endif


                            </span>
                        </div>
                        <div class="message_group">
                            <p class="messageContent">
                                <span class="namemember">{{ $chat->user ? $chat->user->full_name : '' }}</span>

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
                                    @if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']))
                                        <a href="{{ \App\Helpers\Helper::chatMediaUrl($chat->attachment) ?: Storage::url($chat->attachment) }}"
                                            class="chat-image-preview"
                                            data-image-url="{{ \App\Helpers\Helper::chatMediaUrl($chat->attachment) ?: Storage::url($chat->attachment) }}"
                                            data-file-name="{{ $chat->attachment_name ?? pathinfo($chat->attachment, PATHINFO_BASENAME) }}">
                                            <img class="chat-image-attachment" src="{{ \App\Helpers\Helper::chatMediaUrl($chat->attachment) ?: Storage::url($chat->attachment) }}" alt=""
                                                style="max-width: 280px; max-height: 360px; width: auto; height: auto;">
                                        </a>
                                    @elseif (in_array($ext, ['mp4', 'webm', 'ogg', 'mov', 'm4v']))
                                        <button type="button" class="chat-video-preview"
                                            data-video-url="{{ \App\Helpers\Helper::chatMediaUrl($chat->attachment) ?: Storage::url($chat->attachment) }}"
                                            data-file-name="{{ $chat->attachment_name ?? pathinfo($chat->attachment, PATHINFO_BASENAME) }}"
                                            data-mime="{{ $videoMime[$ext] ?? 'video/mp4' }}"
                                            aria-label="Play video">
                                            <video class="chat-video-attachment" muted playsinline preload="metadata"
                                                style="max-width: 280px; max-height: 360px; width: auto; height: auto;">
                                                <source src="{{ \App\Helpers\Helper::chatMediaUrl($chat->attachment) ?: Storage::url($chat->attachment) }}"
                                                    type="{{ $videoMime[$ext] ?? 'video/mp4' }}">
                                            </video>
                                            <span class="chat-video-play-icon" aria-hidden="true"><i class="fa-solid fa-play"></i></span>
                                        </button>
                                    @else
                                        <a href="{{ \App\Helpers\Helper::chatMediaUrl($chat->attachment) ?: Storage::url($chat->attachment) }}" target="_blank"
                                            class="file-download"
                                            data-download-url="{{ \App\Helpers\Helper::chatMediaUrl($chat->attachment) ?: Storage::url($chat->attachment) }}"
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
                            <div class="messageDetails">
                                <div class="messageTime">{{ $chat->created_at->format('h:i A') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endforeach
@endif
