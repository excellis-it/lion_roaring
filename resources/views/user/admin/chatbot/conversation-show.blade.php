@extends('user.layouts.master')
@section('title')
    Transcript #{{ $conversation->id }} - Chatbot Assistant
@endsection

@push('styles')
    <style>
        .transcript-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 15px;
            max-height: 600px;
            overflow-y: auto;
        }

        .msg-bubble {
            max-width: 80%;
            padding: 10px 15px;
            border-radius: 18px;
            position: relative;
            font-size: 14px;
            line-height: 1.5;
        }

        .msg-text p {
            margin-bottom: 5px;
        }

        .msg-text p:last-child {
            margin-bottom: 0;
        }

        .msg-text ul,
        .msg-text ol {
            margin-bottom: 5px;
            padding-left: 20px;
        }

        .msg-bubble.user {
            align-self: flex-end;
            background: #643271;
            color: white;
            border-bottom-right-radius: 2px;
        }

        .msg-bubble.bot {
            align-self: flex-start;
            background: #f1f0f0;
            color: #222;
            border-bottom-left-radius: 2px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .msg-info {
            font-size: 11px;
            margin-top: 4px;
            color: #888;
        }

        .user .msg-info {
            text-align: right;
            color: rgba(255, 255, 255, 0.8);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="dashboard-top-heading d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="{{ route('user.admin.chatbot.conversations') }}" class="text-decoration-none small"><i
                            class="fas fa-arrow-left"></i> Back to History</a>
                    <h3 class="mt-2">Chat Transcript #{{ $conversation->id }}</h3>
                    <p class="text-muted">
                        @if ($conversation->user)
                            {{ $conversation->user->full_name }}
                        @else
                            Guest: {{ $conversation->guest_name ?? 'Anonymous' }}
                        @endif
                        &bull; {{ $conversation->created_at->format('M d, Y H:i') }}
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge bg-light text-dark border p-2">Session: {{ $conversation->session_id }}</span>
                </div>
            </div>

            <div class="transcript-container shadow-inner border">
                @forelse($conversation->messages as $msg)
                    <div class="msg-bubble {{ $msg->sender }}">
                        <div class="msg-text">
                            @if ($msg->sender == 'bot')
                                {!! $msg->message !!}
                            @else
                                {{ $msg->message }}
                            @endif
                        </div>
                        <div class="msg-info">{{ $msg->created_at->format('H:i') }} &bull; {{ ucfirst($msg->sender) }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">No messages recorded in this session.</div>
                @endforelse
            </div>

            <div class="mt-4 p-3 bg-light rounded-3 border">
                <h5>Session Metadata</h5>
                <div class="row small mt-2">
                    <div class="col-md-3"><strong>Language:</strong> <span
                            class="text-uppercase">{{ $conversation->language ?? 'en' }}</span></div>
                    <div class="col-md-3"><strong>Device Token:</strong> {{ Str::limit($conversation->session_id, 8) }}
                    </div>
                    <div class="col-md-3"><strong>Started At:</strong> {{ $conversation->created_at }}</div>
                    <div class="col-md-3"><strong>Updated At:</strong> {{ $conversation->updated_at }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
