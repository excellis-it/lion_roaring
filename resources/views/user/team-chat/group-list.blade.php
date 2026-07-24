@php
    use App\Helpers\Helper;
@endphp
@if (count($teams) > 0)
    @foreach ($teams as $team)
        <li class="group group-data  {{ isset($team_id) && $team['id'] == $team_id ? 'active' : '' }}"
            data-id="{{ $team['id'] }}">
            <div class="avatar team-image-{{ $team['id'] }} position-relative">
                @php
                    $groupImgUrl = \App\Helpers\Helper::publicStorageUrl($team['group_image'] ?? null)
                        ?: asset('user_assets/images/group.jpg');
                    $groupImgFallback = asset('user_assets/images/group.jpg');
                @endphp
                <img src="{{ $groupImgUrl }}" alt=""
                    onerror="this.onerror=null;this.src='{{ $groupImgFallback }}';">

                <div class="count-unseen" id="count-team-unseen-{{ $team['id'] }}">
                    @if (Helper::getTeamCountUnseenMessage(Auth::user()->id, $team['id']) > 0)
                        <span>
                            <p>{{ Helper::getTeamCountUnseenMessage(Auth::user()->id, $team['id']) }}
                            </p>
                        </span>
                    @endif
                </div>
            </div>
            <p class="GroupName group-name-{{ $team['id'] }}">{{ $team['name'] }}</p>
            <p
                class="GroupDescrp team-last-chat-{{ Helper::userLastMessage($team['id'], auth()->user()->id) ? Helper::userLastMessage($team['id'], auth()->user()->id)->id : '' }}">
                {!! Helper::userLastMessage($team['id'], auth()->user()->id)
                    ? (Helper::userLastMessage($team['id'], auth()->user()->id)->message
                        ? Helper::userLastMessage($team['id'], auth()->user()->id)->message
                        : (Helper::userLastMessage($team['id'], auth()->user()->id)->attachment
                            ? '<i class="fa-solid fa-file"></i> File uploaded'
                            : ''))
                    : '' !!}</p>
            <div class="time_online"
                id="team-last-chat-time-{{ Helper::userLastMessage($team['id'], auth()->user()->id) ? Helper::userLastMessage($team['id'], auth()->user()->id)->id : '' }}">
                {{ Helper::userLastMessage($team['id'], auth()->user()->id) ? Helper::userLastMessage($team['id'], auth()->user()->id)->created_at->format('h:i A') : '' }}
            </div>



        </li>
    @endforeach
@else
    <li class="group">
        <p></p>
        <p class="" style="color: black">No Group Found</p>
    </li>
@endif
