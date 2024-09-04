@if (count($meetings) > 0)
    @foreach ($meetings as $key => $meeting)
        <tr>
            <td>{{ $meetings->firstItem() + $key }}</td>
            <td>{{ $meeting->title ? $meeting->title : '-' }}</td>
            <td>{{ $meeting->start_time ? date('d M Y h:i A', strtotime($meeting->start_time)) : '-' }}</td>
            <td>{{ $meeting->end_time ? date('d M Y h:i A', strtotime($meeting->end_time)) : '-' }}</td>
            <td>{{ $meeting->meeting_link ? $meeting->meeting_link : '-' }}</td>
            <td>
                <div class="d-flex">
                    @if (auth()->user()->can('Edit Meeting Schedule') && $meeting->created_by == auth()->user()->id || auth()->user()->hasRole('ADMIN'))
                    <a href="{{ route('meetings.edit', $meeting->id) }}" class="delete_icon">
                        <i class="fa-solid fa-edit"></i>
                    </a> &nbsp; &nbsp;
                    @endif
                    @if (auth()->user()->can('View Meeting Schedule'))
                    <a href="{{ route('meetings.show', $meeting->id) }}" class="delete_icon">
                        <i class="fa-solid fa-eye"></i>
                    </a> &nbsp; &nbsp;
                    @endif
                    @if (auth()->user()->can('Delete Meeting Schedule') && $meeting->created_by == auth()->user()->id || auth()->user()->hasRole('ADMIN'))
                    <a href="javascript:void(0)" id="delete"
                        data-route="{{ route('meetings.delete', $meeting->id) }}" class="delete_icon">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
    <tr class="toxic">
        <td colspan="7">
            <div class="d-flex justify-content-center">
                {!! $meetings->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr>
        <td colspan="7" class="text-center">No data found</td>
    </tr>
@endif
