@if (count($contacts) > 0)
@foreach ($contacts as $key => $contact)
    <tr>
        <td> {{ ($contacts->currentPage()-1) * $contacts->perPage() + $loop->index + 1 }}</td>
        <td>{{ $contact->full_name }}</td>
        <td>{{ $contact->phone }}</td>
        <td>{{ $contact->email }}</td>
        <td>{{ $contact->message }}
        </td>
    </tr>
@endforeach
<tr style="box-shadow: none;">
    <td colspan="5">
        <div class="d-flex justify-content-center">
            {!! $contacts->links() !!}
        </div>
    </td>
</tr>
@else
<tr>
    <td colspan="5" class="text-center">No Contact Found</td>
</tr>
@endif
