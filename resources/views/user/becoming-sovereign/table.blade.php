@if (count($files) > 0)
    @foreach ($files as $key => $file)
        <tr>
            <td>{{ $files->firstItem() + $key }}</td>
            <td> {{ $file->file_name }}</td>
            <td> {{ $file->file_extension }}</td>
            <td> {{ $file->topic->topic_name ?? '--' }}</td>
            <td>
                <div class="d-flex">
                    @if (auth()->user()->can('View Becoming Sovereigns'))
                    <a href="{{ route('becoming-sovereign.view', $file->id). '?topic=' . ($new_topic ?? '') }}" class="edit_icon me-2">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    @endif
                    @if (auth()->user()->can('Edit Becoming Sovereigns'))
                        <a href="{{ route('becoming-sovereign.edit', $file->id). '?topic=' . ($new_topic ?? '') }}" class="delete_icon me-2">
                            <i class="fa-solid fa-edit"></i>
                        </a>
                    @endif
                    @if (auth()->user()->can('Download Becoming Sovereigns'))
                    <a href="{{ route('becoming-sovereign.download', $file->id) }}" class="edit_icon me-2">
                        <i class="fa-solid fa-download"></i>
                    </a>
                    @endif
                    @if (auth()->user()->can('Delete Becoming Sovereigns'))
                        <a href="javascript:void(0)" id="delete" data-route="{{ route('becoming-sovereign.delete', $file->id). '?topic=' . ($new_topic ?? '') }}"
                            class="delete_icon">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
    <tr class="toxic">
        <td colspan="4">
            <div class="d-flex justify-content-center">
                {!! $files->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr>
        <td colspan="4" class="text-center">No data found</td>
    </tr>
@endif
