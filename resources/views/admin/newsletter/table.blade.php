@if (count($newsletters) > 0)
    @foreach ($newsletters as $key => $newsletter)
        <tr>
            <td> {{ ($newsletters->currentPage() - 1) * $newsletters->perPage() + $loop->index + 1 }}</td>
            <td>{{ $newsletter->full_name }}</td>
            <td>{{ $newsletter->email }}</td>
            <td>{{ $newsletter->message }}
            </td>
            <td>
                <div class="edit-1 d-flex align-items-center justify-content-center">
                    @if (auth()->user()->can('Delete Newsletters'))
                        <a title="Delete" data-route="{{ route('newsletters.delete', $newsletter->id) }}"
                            href="javascript:void(0);" id="delete">
                            <span class="trash-icon"><i class="ph ph-trash"></i></span>
                        </a>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
    <tr style="box-shadow: none;">
        <td colspan="5">
            <div class="d-flex justify-content-center">
                {!! $newsletters->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr>
        <td colspan="5" class="text-center">No Newsletter Found</td>
    </tr>
@endif
