<td>{{ $collaboration->title ? $collaboration->title : '-' }}</td>
<td>{{ $collaboration->start_time?->format('d M Y h:i A') ?? '-' }}</td>
<td>{{ $collaboration->end_time?->format('d M Y h:i A') ?? '-' }}</td>
<td>{{ $collaboration->time_zone ?? 'UTC' }}</td>
<td>
    @php
        $isCreator = $collaboration->user_id == auth()->id();
        $invitation = $collaboration->invitations->where('user_id', auth()->id())->first();
        $hasAccepted = $invitation && $invitation->status == 'accepted';
    @endphp

    @if ($isCreator)
        <span class=" ">Creator</span>
    @elseif($hasAccepted)
        <span class=" ">Accepted</span>
    @elseif($invitation)
        <span class=" ">Pending</span>
    @else
        <span class=" ">Not Invited</span>
    @endif
</td>
<td>{{ $collaboration->user ? $collaboration->user->full_name : '-' }}</td>
<td>
    {{ $collaboration->country->name ?? '-' }}
</td>
<td>
    <div class="d-flex">
        @php
            $isCreator = $collaboration->user_id == auth()->id();
            $invitation = $collaboration->invitations->where('user_id', auth()->id())->first();
            $hasAccepted = $invitation && $invitation->status == 'accepted';
        @endphp

        {{-- Show "Accept Invitation" button if user is invited but hasn't accepted --}}
        @if (!$isCreator && $invitation && $invitation->status == 'pending')
            <a href="javascript:void(0)" id="accept-invitation"
                data-route="{{ route('private-collaborations.accept-invitation', $collaboration->id) }}"
                data-id="{{ $collaboration->id }}" class="btn btn-primary me-2">
                <i class="fa-solid fa-check"></i> Accept Invite
            </a>
        @endif

        {{-- Show view/join button only if creator or has accepted --}}
        @if (auth()->user()->can('View Private Collaboration') && ($isCreator || $hasAccepted))
            <a href="{{ route('private-collaborations.show', $collaboration->id) }}" class="delete_icon me-2">
                <i class="fa-solid fa-eye"></i>
            </a>

            @if ($collaboration->meeting_link || $collaboration->host_meeting_link)
                @php
                    $videoUrl = $isCreator
                        ? ($collaboration->host_meeting_link ?: $collaboration->meeting_link)
                        : $collaboration->meeting_link;
                @endphp
                <a href="{{ $videoUrl }}" target="_blank" rel="noopener noreferrer" class="edit_icon me-2"
                    title="{{ $isCreator ? 'Start Meeting' : 'Join Meeting' }}">
                    <i class="fa-solid fa-video"></i>
                </a>
            @endif
        @endif

        {{-- Edit and Delete only for creator or super admin --}}
        @if (auth()->user()->can('Edit Private Collaboration') && ($isCreator || auth()->user()->hasNewRole('SUPER ADMIN')))
            <a href="{{ route('private-collaborations.edit', $collaboration->id) }}" class="delete_icon me-2">
                <i class="fa-solid fa-edit"></i>
            </a>
        @endif

        @if (auth()->user()->can('Delete Private Collaboration') && ($isCreator || auth()->user()->hasNewRole('SUPER ADMIN')))
            <a href="javascript:void(0)" id="delete"
                data-route="{{ route('private-collaborations.delete', $collaboration->id) }}" class="delete_icon me-2">
                <i class="fa-solid fa-trash"></i>
            </a>
        @endif
    </div>
</td>
