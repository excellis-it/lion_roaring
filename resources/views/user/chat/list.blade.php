@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} - User Chat
@endsection
@push('styles')
@endpush
@section('content')
    @php
        use App\Helpers\Helper;
    @endphp
    <div class="container-fluid">
        <div class="bg_white_border">

            <div class="messaging_sec">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="heading_hp">
                        <h2>Messaging</h2>
                    </div>
                </div>
                <div class="SideNavhead">
                    <h2>Chat</h2>
                </div>
                <div class="main">
                    <div>
                        <div class="sideNav2" id="group-manage-{{ Auth::user()->id }}">
                            @if (count($users) > 0)
                                @foreach ($users as $user)
                                    <li class="group user-list" data-id="{{ $user['id'] }}">
                                        <div class="avatar">
                                            @if ($user['profile_picture'])
                                                <img src="{{ Storage::url($user['profile_picture']) }}" alt="">
                                            @else
                                                <img src="{{ asset('user_assets/images/profile_dummy.png') }}"
                                                    alt="">
                                            @endif
                                        </div>
                                        <p class="GroupName">{{ $user['first_name'] }} {{ $user['middle_name'] ?? '' }}
                                            {{ $user['last_name'] ?? '' }}</p>
                                        <p class="GroupDescrp last-chat-{{ isset($user['last_message']) ? $user['last_message']['id'] : '' }}"
                                            id="message-app-{{ $user['id'] }}">
                                            @if (isset($user['last_message']['message']))
                                                {{ $user['last_message']['message'] }}
                                            @endif
                                        </p>
                                        <div class="time_online"
                                            id="last-chat-time-{{ isset($user['last_message']) ? $user['last_message']['id'] : '' }}">
                                            @if (isset($user['last_message']['created_at']))
                                                <p>{{ $user['last_message']['created_at']->format('h:i A') }}</p>
                                            @endif
                                        </div>
                                        @if (Helper::getCountUnseenMessage(Auth::user()->id, $user['id']) > 0)
                                            <div class="count-unseen" id="count-unseen-{{ $user['id'] }}">
                                                <span>
                                                    <p>{{ Helper::getCountUnseenMessage(Auth::user()->id, $user['id']) }}
                                                    </p>
                                                </span>
                                            </div>
                                        @endif

                                    </li>
                                @endforeach
                            @else
                                <p>No users found</p>
                            @endif
                        </div>
                    </div>
                    <section class="Chat chat-module">
                        @include('user.chat.chat_body')
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
   
@endpush
