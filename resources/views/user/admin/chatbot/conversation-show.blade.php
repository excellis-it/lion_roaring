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
            padding: 12px 18px;
            border-radius: 20px;
            position: relative;
        }

        .msg-bubble.user {
            align-self: flex-end;
            background: var(--chatbot-primary, #643271);
            color: white;
            border-bottom-right-radius: 5px;
        }

        .msg-bubble.bot {
            align-self: flex-start;
            background: white;
            color: #333;
            border-bottom-left-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .msg-info {
            font-size: 10px;
            margin-top: 5px;
            opacity: 0.7;
        }

        .user .msg-info {
            text-align: right;
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
                        <div class="msg-text">{{ $msg->message }}</div>
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
