@php
    use App\Helpers\Helper;
@endphp
@if (count($users) > 0)
    @foreach ($users as $user)
        <li class="group user-list" id="chat_list_user_{{ $user['id'] }}" data-id="{{ $user['id'] }}">
            <div class="avatar">
                @if ($user['profile_picture'])
                    <img src="{{ Storage::url($user['profile_picture']) }}" alt="">
                @else
                    <img src="{{ asset('user_assets/images/profile_dummy.png') }}" alt="">
                @endif
            </div>
            <p class="GroupName">{{ $user['first_name'] }} {{ $user['middle_name'] ?? '' }}
                {{ $user['last_name'] ?? '' }}</p>
            <p class="GroupDescrp last-chat-{{ isset($user['last_message']) ? $user['last_message']['id'] : '' }}"
                id="message-app-{{ $user['id'] }}">
                @if (isset($user['last_message']['message']))
                    {{ $user['last_message']['message'] }}
                @endif

                @if (isset($user['last_message']) &&
                        $user['last_message']['message'] == null &&
                        $user['last_message']['attachment'] != null)
                    <span><i class="ti ti-file"></i></span>
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
