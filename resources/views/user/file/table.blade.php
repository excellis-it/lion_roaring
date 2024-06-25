@if (count($files) > 0)
    @foreach ($files as $key => $file)
        <tr>
            <td>{{ $files->firstItem() + $key }}</td>
            <td> {{ $file->file_name }}</td>
            <td> {{ $file->file_extension }}</td>
            <td> {{ $file->type }}</td>
            <td>
                <div class="d-flex">
                    @if (auth()->user()->can('Edit File'))
                        <a href="{{ route('file.edit', $file->id) }}" class="delete_icon">
                            <i class="fa-solid fa-edit"></i>
                        </a> &nbsp; &nbsp;
                    @endif

                    <a href="{{ route('file.download', $file->id) }}" class="edit_icon me-2">
                        <i class="fa-solid fa-download"></i>
                    </a>
                    @if (auth()->user()->can('Delete File'))
                        <a href="javascript:void(0)" id="delete" data-route="{{ route('file.delete', $file->id) }}"
                            class="delete_icon">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
    <tr class="toxic">
        <td colspan="5">
            <div class="d-flex justify-content-center">
                {!! $files->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr>
        <td colspan="5" class="text-center">No data found</td>
    </tr>
@endif
