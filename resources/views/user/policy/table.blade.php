@if (count($policies) > 0)
    @foreach ($policies as $key => $policy)
        <tr>
            <td>{{ $policies->firstItem() + $key }}</td>
            <td> {{ $policy->file_name }}</td>
            <td> {{ $policy->file_extension }}</td>
            <td>
                <div class="d-flex">
                    @if (auth()->user()->can('View Policy'))
                        <a href="{{ route('policy-guidence.view', $policy->id) }}" class="edit_icon me-2">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    @endif
                    @if (auth()->user()->can('Download Policy'))
                        <a href="{{ route('policy-guidence.download', $policy->id) }}"
                            class="edit_icon me-2 file-download"
                            data-download-url="{{ route('policy-guidence.download', $policy->id) }}"
                            data-file-name="{{ $policy->file_name }}.{{ $policy->file_extension }}">
                            <i class="fa-solid fa-download"></i>
                        </a>
                    @endif
                    @if (auth()->user()->can('Delete Policy'))
                        <a href="javascript:void(0)" id="delete"
                            data-route="{{ route('policy-guidence.delete', $policy->id) }}" class="delete_icon">
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
                {!! $policies->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr>
        <td colspan="4" class="text-center">No data found</td>
    </tr>
@endif
