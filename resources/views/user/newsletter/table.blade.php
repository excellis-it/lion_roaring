@if (count($newsletters) > 0)
    @foreach ($newsletters as $key => $newsletter)
        <tr>
            <td><input type="checkbox" class="row-checkbox" value="{{ $newsletter->id }}"></td>
            <td>{{ $newsletters->firstItem() + $key }}</td>
            {{-- <td> {{ $newsletter->name }}</td> --}}
            <td> {{ $newsletter->email }}</td>
            {{-- <td> {{ $newsletter->message ? $newsletter->message : '--' }}</td> --}}
            <td>
                <a href="javascript:void(0)" id="delete"
                    data-route="{{ route('user.newsletters.delete', $newsletter->id) }}" class="delete_icon">
                    <i class="fa-solid fa-trash"></i>
                </a>
            </td>
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
