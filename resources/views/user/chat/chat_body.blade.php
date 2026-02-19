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
            <button id="backButton" style="color:#000;"
                class="btn btn-light chat-back-button me-3 d-inline-flex align-items-center backButton">
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
        @include('user.chat.partials.messages', ['chats' => $chats])
    </div>

    <!-- File Upload Modal -->
    <div class="modal fade" id="fileUploadModal" tabindex="-1" aria-labelledby="fileUploadModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileUploadModalLabel">Send Files</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Drag and Drop Area -->
                    <div id="dropZone" class="drop-zone">
                        <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                        <p class="mb-2 text-dark">Drag and drop files here</p>
                        <p class="text-muted small">or</p>
                        <button type="button" class="btn btn-primary" id="selectFilesBtn">
                            <i class="fas fa-folder-open me-2"></i>Select Files
                        </button>
                        <input type="file" id="fileInput" style="display: none" multiple
                            accept="image/*,video/*,application/pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt">
                    </div>

                    <!-- Files Preview Area -->
                    <div id="filesPreviewContainer" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><span id="fileCount">0</span> file(s) selected</h6>
                            <button type="button" class="btn btn-primary" id="addMoreFiles">
                                <i class="fas fa-plus me-1"></i>Add More
                            </button>
                        </div>
                        <div id="filesList" class="files-list"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="sendFilesBtn">
                        <i class="fas fa-paper-plane me-2"></i>Send Files
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Pasted/Selected Files Preview Container -->
    <div id="file-name-display"
        style="display: none; padding: 10px; background: #f8f9fa; border-radius: 8px; margin-bottom: 10px;"></div>

    <form id="MessageForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" class="reciver_id" value="{{ $reciver->id }}">
        <div class="file-upload">
            <span id="hit-chat-file" style="cursor: pointer;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                    viewBox="0 0 24 24">
                    <path fill="currentColor" fill-rule="evenodd"
                        d="M9 7a5 5 0 0 1 10 0v8a7 7 0 1 1-14 0V9a1 1 0 0 1 2 0v6a5 5 0 0 0 10 0V7a3 3 0 1 0-6 0v8a1 1 0 1 0 2 0V9a1 1 0 1 1 2 0v6a3 3 0 1 1-6 0z"
                        clip-rule="evenodd" style="color:black"></path>
                </svg>
            </span>
            <input type="file" id="file2" style="display: none;" multiple
                accept="image/*,video/*,application/pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt">
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

    <style>
        .drop-zone {
            border: 2px dashed #6200ea;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
            color: #595959;
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
        }

        .file-preview-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .file-preview-content {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
            color: darkslategrey;
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

        .modal-body {
            padding: 20px;
        }

        .file-preview-item {
            position: relative;
        }
    </style>
@else
    <div class="icon_chat">
        <span><img src="{{ asset('user_assets/images/icon-chat.png') }}" alt=""></span>
        <h4>Seamless Real-Time Chat | Connect Instantly</h4>
        <p>Join our dynamic chat platform, where real-time communication is effortless. Engage in private and group
            conversations, manage your contacts, and stay connected with instant updates. Experience a secure and
            responsive
            interface, perfect for personal or professional use.</p>
    </div>
@endif
