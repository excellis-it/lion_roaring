@extends('user.layouts.master')
@section('title')
    Chat History - Chatbot Assistant
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="dashboard-top-heading mb-4">
                <h3>Conversation History</h3>
                <p class="text-muted">Review previous interactions with the chatbot</p>
            </div>

            <!-- Filter Section -->
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <form action="{{ route('user.admin.chatbot.conversations') }}" method="GET"
                        class="row g-3 bg-light p-3 rounded-3 border">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Search</label>
                            <input type="text" name="q" class="form-control"
                                placeholder="Name, Guest Name, or Session ID" value="{{ request('q') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Date</label>
                            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                        </div>

                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="print_btn ">Filter</button>
                            <a href="{{ route('user.admin.chatbot.conversations') }}"
                                class=" print_btn ">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle bg-white">
                    <thead class="color_head">
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Device/Session</th>
                            <th>Messages</th>
                            <th>Language</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($conversations as $conv)
                            <tr>
                                <td>
                                    <div>{{ $conv->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $conv->created_at->format('H:i A') }}</small>
                                </td>
                                <td>
                                    @if ($conv->user)
                                        <div class="fw-bold">{{ $conv->user->full_name }}</div>
                                        <small class="text-muted">Registered Member</small>
                                    @else
                                        <div class="fw-bold">{{ $conv->guest_name ?? 'Guest User' }}</div>
                                        <small class="text-muted">Guest</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="small text-truncate" style="max-width: 150px;"
                                        title="{{ $conv->session_id }}">
                                        {{ $conv->session_id }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $conv->messages_count }}
                                        messages</span>
                                </td>
                                <td>
                                    <span class="text-uppercase">{{ $conv->language ?? 'en' }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('user.admin.chatbot.conversations.show', $conv->id) }}"
                                        class="btn btn-sm btn-outline-primary rounded-pill">View Transcript</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-ghost fs-1 text-muted mb-3 d-block"></i>
                                    No conversations found yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $conversations->links() }}
            </div>
        </div>
    </div>
@endsection
