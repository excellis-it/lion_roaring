@if (count($newsletters) > 0)
    @foreach ($newsletters as $key => $newsletter)
        <tr>
            <td>{{ $newsletters->firstItem() + $key }}</td>
            <td> {{ $newsletter->name }}</td>
            <td> {{ $newsletter->email }}</td>
            <td> {{ $newsletter->message ? $newsletter->message : '--' }}</td>
        </tr>
    @endforeach
    <tr class="toxic">
        <td colspan="4">
            <div class="d-flex justify-content-center">
                {!! $newsletters->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr>
        <td colspan="4" class="text-center">No data found</td>
    </tr>
@endif
