{{-- <td>{{ $meetings->firstItem() + $key }}</td> --}}
<td>{{ $meeting->title ? $meeting->title : '-' }}</td>
<td>{{ $meeting->start_time ? date('d M Y h:i A', strtotime($meeting->start_time)) : '-' }}</td>
<td>{{ $meeting->end_time ? date('d M Y h:i A', strtotime($meeting->end_time)) : '-' }}</td>
<td>
    {{-- meeting link clickable with a good design --}}
    @if ($meeting->meeting_link)
        <a href="{{ $meeting->meeting_link }}" target="_blank" class="edit_icon">
            {{ $meeting->meeting_link }}
        </a>
    @else
        -
    @endif
</td>
<td>{{ $meeting->country->name ?? '-' }}</td>
<td>
    {{-- added time human --}}
    {{ $meeting->created_at ? $meeting->created_at->diffForHumans() : '-' }}
</td>
<td>
    <div class="d-flex">


        @if (auth()->user()->can('Edit Meeting Schedule') || auth()->user()->hasRole('SUPER ADMIN'))
            <a href="{{ route('meetings.edit', $meeting->id) }}" class="delete_icon">
                <i class="fa-solid fa-edit"></i>
            </a> &nbsp; &nbsp;
        @endif
        @if (auth()->user()->can('View Meeting Schedule'))
            <a href="{{ route('meetings.show', $meeting->id) }}" class="delete_icon">
                <i class="fa-solid fa-eye"></i>
            </a> &nbsp; &nbsp;
        @endif

        @if (auth()->user()->can('Delete Meeting Schedule'))
            <a href="javascript:void(0)" id="delete" data-route="{{ route('meetings.delete', $meeting->id) }}"
                class="delete_icon">
                <i class="fa-solid fa-trash"></i>
            </a> &nbsp; &nbsp; &nbsp;
        @endif

        @if (auth()->user()->can('View Meeting Schedule') && $meeting->meeting_link != null)
            {{-- <a href="{{ route('meetings.join-meeting', $meeting->id) }}" class="edit_icon">
                <i class="fa-solid fa-video"></i>
            </a> &nbsp; &nbsp; --}}
            <a href="{{ $meeting->meeting_link }}" target="_blank" class="edit_icon">
                <i class="fa-solid fa-video"></i>
            </a> &nbsp; &nbsp;
        @endif
    </div>
</td>
