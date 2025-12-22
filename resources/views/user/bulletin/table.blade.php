@if (count($bulletins) > 0)
    @foreach ($bulletins as $key => $bulletin)

        <tr id="single-bulletin-{{$bulletin->id}}">

            @include('user.bulletin.show-single-bulletin')

        </tr>
    @endforeach
    <tr class="toxic">
        <td colspan="6">
            <div class="d-flex justify-content-center">
                {!! $bulletins->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr>
        <td colspan="6" class="text-center">No data found</td>
    </tr>
@endif
