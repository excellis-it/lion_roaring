@if (count($newsletters) > 0)
@foreach ($newsletters as $key => $newsletter)
    <tr>
        <td> {{ ($newsletters->currentPage()-1) * $newsletters->perPage() + $loop->index + 1 }}</td>
        <td>{{ $newsletter->full_name }}</td>
        <td>{{ $newsletter->email }}</td>
        <td>{{ $newsletter->message }}
        </td>
    </tr>
@endforeach
<tr style="box-shadow: none;">
    <td colspan="4">
        <div class="d-flex justify-content-center">
            {!! $newsletters->links() !!}
        </div>
    </td>
</tr>
@else
<tr>
    <td colspan="4" class="text-center">No Newsletter Found</td>
</tr>
@endif
