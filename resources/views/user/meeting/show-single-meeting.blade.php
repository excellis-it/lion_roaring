{{-- <td>{{ $meetings->firstItem() + $key }}</td> --}}
<td>{{ $meeting->title ? $meeting->title : '-' }}</td>
<td>{{ $meeting->start_time ? date('d M Y h:i A', strtotime($meeting->start_time)) : '-' }}</td>
<td>{{ $meeting->end_time ? date('d M Y h:i A', strtotime($meeting->end_time)) : '-' }}</td>
<td>{{ $meeting->meeting_link ? $meeting->meeting_link : '-' }}</td>
<td>
    {{-- added time human --}}
    {{ $meeting->created_at ? $meeting->created_at->diffForHumans() : '-' }}
</td>
<td>
    <div class="d-flex">
        @if ((auth()->user()->can('Edit Meeting Schedule') && $meeting->user_id == auth()->user()->id) || auth()->user()->hasRole('ADMIN'))
        <a href="{{ route('meetings.edit', $meeting->id) }}" class="delete_icon">
            <i class="fa-solid fa-edit"></i>
        </a> &nbsp; &nbsp;
        @endif
        @if (auth()->user()->can('View Meeting Schedule'))
        <a href="{{ route('meetings.show', $meeting->id) }}" class="delete_icon">
            <i class="fa-solid fa-eye"></i>
        </a> &nbsp; &nbsp;
        @endif
        @if ((auth()->user()->can('Delete Meeting Schedule') && $meeting->user_id == auth()->user()->id) || auth()->user()->hasRole('ADMIN'))
        <a href="javascript:void(0)" id="delete"
            data-route="{{ route('meetings.delete', $meeting->id) }}" class="delete_icon">
            <i class="fa-solid fa-trash"></i>
        </a>
        @endif
    </div>
</td>
