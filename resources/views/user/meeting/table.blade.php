@if (count($meetings) > 0)
    @foreach ($meetings as $key => $meeting)
        <tr id="single-meeting-{{$meeting->id}}">
            @include('user.meeting.show-single-meeting')
        </tr>
    @endforeach
    <tr class="toxic">
        <td colspan="9">
            <div class="d-flex justify-content-center">
                {!! $meetings->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr>
        <td colspan="9" class="text-center">No data found</td>
    </tr>
@endif
