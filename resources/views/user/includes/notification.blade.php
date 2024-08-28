@if (isset($is_notification))
    @if ($notifications->count() > 0)
        @foreach ($notifications as $notification)
            <li>
                <a href="javascript:void(0);" class="top-text-block">
                    <div class="top-text-heading">{!! $notification->message !!}</div>
                    <div class="top-text-light">{{ $notification->created_at->diffForHumans() }}</div>
                </a>
            </li>
        @endforeach
    @else
        <li>
            <a href="#" class="top-text-block">
                <div class="top-text-heading">No new notifications</div>
                <div class="top-text-light">Just now</div>
            </a>
        </li>
    @endif
@endif
