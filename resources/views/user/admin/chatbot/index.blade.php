@extends('user.layouts.master')
@section('title')
    Chatbot Dashboard - {{ env('APP_NAME') }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="dashboard-top-heading mb-4">
                <h3>Chatbot Assistant Management</h3>
                <p class="text-muted">Monitor and configure your AI assistant</p>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-primary text-white p-3 h-100">
                        <div class="d-flex align-items-center">
                            <div class="fs-1 me-3"><i class="fas fa-comments"></i></div>
                            <div>
                                <h4 class="mb-0">{{ $totalConversations }}</h4>
                                <small>Total Conversations</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-success text-white p-3 h-100">
                        <div class="d-flex align-items-center">
                            <div class="fs-1 me-3"><i class="fas fa-smile"></i></div>
                            <div>
                                <h4 class="mb-0">{{ $helpfulRate }}%</h4>
                                <small>Helpful Rate</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-info text-white p-3 h-100">
                        <div class="d-flex align-items-center">
                            <div class="fs-1 me-3"><i class="fas fa-key"></i></div>
                            <div>
                                <h4 class="mb-0">{{ $topKeywords->count() }}</h4>
                                <small>Active Keywords</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-dark text-white p-3 h-100" style="cursor: pointer"
                        onclick="window.location.href='{{ route('user.admin.chatbot.conversations') }}'">
                        <div class="d-flex align-items-center">
                            <div class="fs-1 me-3"><i class="fas fa-history"></i></div>
                            <div>
                                <h4 class="mb-0">History</h4>
                                <small>View All Chats</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm p-4 h-100">
                        <h5>Top Performing Keywords</h5>
                        <div class="table-responsive mt-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Keyword</th>
                                        <th>Usage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topKeywords as $kw)
                                        <tr>
                                            <td>{{ $kw->keyword }}</td>
                                            <td>{{ $kw->usage_count }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <a href="{{ route('user.admin.chatbot.keywords') }}"
                            class="btn btn-outline-primary btn-sm mt-auto">Manage All Keywords</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm p-4 h-100">
                        <h5>Recent Activity</h5>
                        <div class="list-group list-group-flush mt-3">
                            @foreach ($recentAnalytics as $analytic)
                                <div class="list-group-item px-0 border-0 mb-2">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ str_replace('_', ' ', ucfirst($analytic->event_type)) }}</strong>
                                        <small class="text-muted">{{ $analytic->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-0 small text-muted">
                                        @if ($analytic->conversation->user)
                                            User: {{ $analytic->conversation->user->full_name }}
                                        @else
                                            Guest: {{ $analytic->conversation->guest_name ?? 'Anonymous' }}
                                        @endif
                                        in section <em>{{ $analytic->section }}</em>
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
