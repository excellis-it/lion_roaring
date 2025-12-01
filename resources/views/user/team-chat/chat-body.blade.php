@php
    use App\Helpers\Helper;
@endphp
@if (isset($is_chat))
    <div class="groupChatHead">
        <button id="backButton" style="color:#000;"
            class="btn btn-light chat-back-button me-3 d-inline-flex align-items-center backButton">
            <i class="fa fa-arrow-left me-1"></i> Back
        </button>
        <div class="main_avtar team-image-{{ $team['id'] }}"><img
                src="{{ $team['group_image'] ? Storage::url($team['group_image']) : asset('user_assets/images/group.png') }}"
                alt=""></div>

        <div class="group_text">
            <p class="GroupName group-name-{{ $team['id'] }}">{{ $team['name'] ?? '' }}</p>
            <span
                id="all-member-{{ $team['id'] }}">{{ $team_member_name ? (strlen($team_member_name) > 60 ? substr($team_member_name, 0, 60) . '...' : $team_member_name) : '' }}</span>
        </div>
        <div class="group_text_right">
            <div class="d-flex align-items-center">
                <div class="search-field w-100 mb-0">
                    <input type="text" name="search" id="search-chat" placeholder="search..." required=""
                        class="form-control rounded_search">
                    <button class="submit_search" id="search-button"> <span class=""><i
                                class="fa fa-search"></i></span></button>
                </div>
                <div class="dropdown ms-4">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item group-info" data-team-id="{{ $team['id'] }}">Group
                                info</a></li>
                        @if (Helper::checkAdminTeam(auth()->user()->id, $team['id']) == true)
                            <li><a class="dropdown-item delete-group" data-team-id="{{ $team['id'] }}">Delete
                                    group</a>
                            </li>
                        @endif

                        @if (Helper::checkAdminTeam(auth()->user()->id, $team['id']) == true)
                            <li><a class="dropdown-item clear-all-conversation" data-team-id="{{ $team['id'] }}">Clear
                                    all historical conversation </a>
                            </li>
                        @endif

                    </ul>
                </div>
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
                        <div class="message me" id="team-chat-message-{{ $chat->id }}">
                            <div class="message-wrap">
                                <p class="messageContent">
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
                                        {!! Helper::formatChatMessage($chat->message) !!}
                                    @endif
                                </p>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                        id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                        {{-- <li><a class="dropdown-item clear-chat-only-me"  data-reciver-id="{{ $reciver->id }}">Clear chats for me</a></li> --}}
                                        <li><a class="dropdown-item team-remove-chat"
                                                data-chat-id="{{ $chat->id }}" data-team-id="{{ $chat->team_id }}"
                                                data-del-from="me">Remove For Me</a></li>
                                        <li><a class="dropdown-item team-remove-chat"
                                                data-chat-id="{{ $chat->id }}" data-team-id="{{ $chat->team_id }}"
                                                data-del-from="everyone">Remove For Everyone</a></li>
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
                                            <img src="{{ asset('user_assets/images/profile_dummy.png') }}"
                                                alt="">
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
                                                    <img src="{{ asset('user_assets/images/file.png') }}"
                                                        alt="">
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
    </div>
    <div id="group-member-form-{{ $team['id'] }}-{{ auth()->user()->id }}">
        @if (Helper::checkRemovedFromTeam($team['id'], auth()->user()->id) == false)
            <!-- Team File Upload Modal -->
            <div class="modal fade" id="teamFileUploadModal" tabindex="-1"
                aria-labelledby="teamFileUploadModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="teamFileUploadModalLabel">Send Files to Group</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Drag and Drop Area -->
                            <div id="teamDropZone" class="drop-zone">
                                <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                                <p class="mb-2">Drag and drop files here</p>
                                <p class="text-muted small">or</p>
                                <button type="button" class="btn btn-primary" id="teamSelectFilesBtn">
                                    <i class="fas fa-folder-open me-2"></i>Select Files
                                </button>
                                <input type="file" id="teamFileInput" style="display: none" multiple
                                    accept="image/*,video/*,application/pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt">
                            </div>

                            <!-- Files Preview Area -->
                            <div id="teamFilesPreviewContainer" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0"><span id="teamFileCount">0</span> file(s) selected</h6>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        id="teamAddMoreFiles">
                                        <i class="fas fa-plus me-1"></i>Add More
                                    </button>
                                </div>
                                <div id="teamFilesList" class="files-list"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="teamSendFilesBtn">
                                <i class="fas fa-paper-plane me-2"></i>Send Files
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <form id="TeamMessageForm" enctype="multipart/form-data">
                @csrf
                <div class="file-upload">
                    <span id="hit-team-chat-file" style="cursor: pointer;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            viewBox="0 0 24 24">
                            <path fill="currentColor" fill-rule="evenodd"
                                d="M9 7a5 5 0 0 1 10 0v8a7 7 0 1 1-14 0V9a1 1 0 0 1 2 0v6a5 5 0 0 0 10 0V7a3 3 0 1 0-6 0v8a1 1 0 1 0 2 0V9a1 1 0 1 1 2 0v6a3 3 0 1 1-6 0z"
                                clip-rule="evenodd" style="color:black"></path>
                        </svg>
                    </span>
                </div>

                <textarea type="text" id="TeamMessageInput" placeholder="Type a message..." rows="1" class="form-control"></textarea>
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

            <style>
                .drop-zone {
                    border: 2px dashed #6200ea;
                    border-radius: 10px;
                    padding: 40px;
                    text-align: center;
                    background: #f8f9fa;
                    transition: all 0.3s ease;
                    cursor: pointer;
                }

                .drop-zone.dragover {
                    background: #e3f2fd;
                    border-color: #2196f3;
                    transform: scale(1.02);
                }

                .files-list {
                    max-height: 400px;
                    overflow-y: auto;
                }

                .file-preview-item {
                    background: #fff;
                    border: 1px solid #e0e0e0;
                    border-radius: 8px;
                    padding: 15px;
                    margin-bottom: 15px;
                    transition: all 0.3s ease;
                    position: relative;
                }

                .file-preview-item:hover {
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                }

                .file-preview-content {
                    display: flex;
                    gap: 15px;
                    margin-bottom: 10px;
                }

                .file-preview-thumbnail {
                    width: 80px;
                    height: 80px;
                    border-radius: 6px;
                    object-fit: cover;
                    flex-shrink: 0;
                }

                .file-preview-icon {
                    width: 80px;
                    height: 80px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: #f5f5f5;
                    border-radius: 6px;
                    font-size: 32px;
                    color: #6200ea;
                    flex-shrink: 0;
                }

                .file-preview-info {
                    flex: 1;
                    min-width: 0;
                }

                .file-preview-name {
                    font-weight: 500;
                    margin-bottom: 5px;
                    word-break: break-word;
                }

                .file-preview-size {
                    color: #666;
                    font-size: 0.875rem;
                }

                .file-message-input {
                    width: 100%;
                    border: 1px solid #ddd;
                    border-radius: 6px;
                    padding: 8px 12px;
                    font-size: 0.875rem;
                    transition: border-color 0.3s;
                }

                .file-message-input:focus {
                    outline: none;
                    border-color: #6200ea;
                }

                .remove-file-btn {
                    position: absolute;
                    top: 10px;
                    right: 10px;
                    background: #dc3545;
                    color: white;
                    border: none;
                    border-radius: 50%;
                    width: 28px;
                    height: 28px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: all 0.3s;
                }

                .remove-file-btn:hover {
                    background: #c82333;
                    transform: scale(1.1);
                }
            </style>
        @else
            {{-- show message --}}
            <div class="justify-content-center">
                <div class="text-center">
                    <h4 style="color:#be2020 !important; front-size:1.25rem;">Sorry! you are not able to send message
                        in this group.</h4>
                </div>
            </div>
        @endif
    </div>
@else
    <div class="icon_chat">
        <span><img src="{{ asset('user_assets/images/icon-chat.png') }}" alt=""></span>
        <h4>Seamless Real-Time Chat | Connect Instantly</h4>
        <p>Join our dynamic chat platform, where real-time communication is effortless. Engage in private and group
            conversations, manage your contacts, and stay connected with instant updates. Experience a secure and
            responsive interface, perfect for personal or professional use.</p>
    </div>
@endif
