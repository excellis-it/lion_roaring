@if (count($contacts) > 0)
    @foreach ($contacts as $key => $contact)
        <tr>
            <td> {{ ($contacts->currentPage() - 1) * $contacts->perPage() + $loop->index + 1 }}</td>
            <td>{{ $contact->full_name }}</td>
            <td>{{ $contact->phone }}</td>
            <td>{{ $contact->email }}</td>
            <td>{{ $contact->message }}
            </td>
            <td>
                <div class="edit-1 d-flex align-items-center justify-content-center">
                    @if (auth()->user()->can('Delete Contact Us Messages'))
                        <a title="Delete" data-route="{{ route('contact-us.delete', $contact->id) }}"
                            href="javascript:void(0);" id="delete">
                            <span class="trash-icon"><i class="fas fa-trash"></i></span>
                        </a>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
    <tr style="box-shadow: none;">
        <td colspan="6">
            <div class="d-flex justify-content-center">
                {!! $contacts->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr>
        <td colspan="6" class="text-center">No Contact Found</td>
    </tr>
@endif
