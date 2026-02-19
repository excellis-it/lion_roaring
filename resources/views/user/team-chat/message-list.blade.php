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
                                        <source src="{{ Storage::url($chat->attachment) }}"
                                            type="video/{{ $ext }}">
                                    </video>
                                @else
                                    <a href="{{ Storage::url($chat->attachment) }}" target="_blank"
                                        class="file-download" data-download-url="{{ Storage::url($chat->attachment) }}"
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
                                        $ext = pathinfo($chat->attachment, PATHINFO_EXTENSION);
                                    @endphp
                                    @if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']))
                                        <a href="{{ Storage::url($chat->attachment) }}" target="_blank"
                                            class="file-download"
                                            data-download-url="{{ Storage::url($chat->attachment) }}"
                                            data-file-name="{{ $chat->attachment_name ?? pathinfo($chat->attachment, PATHINFO_BASENAME) }}">
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
                                            class="file-download"
                                            data-download-url="{{ Storage::url($chat->attachment) }}"
                                            data-file-name="{{ $chat->attachment_name ?? pathinfo($chat->attachment, PATHINFO_BASENAME) }}">
                                            <img src="{{ asset('user_assets/images/file.png') }}" alt="">
                                        </a>
                                    @endif
                                    <br>
                                @endif

                                @if ($chat->message != null)
                                    {!! nl2br($chat->message) !!}
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
