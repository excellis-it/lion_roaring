@if (count($collaborations) > 0)
    @foreach ($collaborations as $key => $collaboration)
        <tr id="single-collaboration-{{ $collaboration->id }}">
            @include('user.private_collaboration.show-single-collaboration')
        </tr>
    @endforeach
    <tr class="toxic">
        <td colspan="6">
            <div class="d-flex justify-content-center">
                {!! $collaborations->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr>
        <td colspan="6" class="text-center">No data found</td>
    </tr>
@endif
