@if (count($files) > 0)
    @foreach ($files as $key => $file)
        <tr>
            <td>{{ $files->firstItem() + $key }}</td>
            <td> {{ $file->file_name }}</td>
            <td> {{ $file->file_extension }}</td>
            <td>
                {{ $file->topic->topic_name ?? '--'}}
            </td>
            <td>
                <div class="d-flex">
                    @if (auth()->user()->can('View Leadership Development'))
                    <a href="{{ route('leadership-development.view', $file->id) }}" class="edit_icon me-2">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    @endif
                    @if (auth()->user()->can('Edit Leadership Development'))
                        <a href="{{ route('leadership-development.edit', $file->id) }}" class="delete_icon me-2">
                            <i class="fa-solid fa-edit"></i>
                        </a>
                    @endif
                    @if (auth()->user()->can('Download Leadership Development'))
                    <a href="{{ route('leadership-development.download', $file->id) }}" class="edit_icon me-2">
                        <i class="fa-solid fa-download"></i>
                    </a>
                    @endif
                    @if (auth()->user()->can('Delete Leadership Development'))
                        <a href="javascript:void(0)" id="delete" data-route="{{ route('leadership-development.delete', $file->id) }}"
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
