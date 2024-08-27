@if (count($strategies) > 0)
    @foreach ($strategies as $key => $strategy)
        <tr>
            <td>{{ $strategies->firstItem() + $key }}</td>
            <td> {{ $strategy->file_name }}</td>
            <td> {{ $strategy->file_extension }}</td>
            <td>
                <div class="d-flex">
                    @if (auth()->user()->can('View Strategy'))
                    <a href="{{ route('strategy.view', $strategy->id) }}" class="edit_icon me-2">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    @endif
                    @if (auth()->user()->can('Download Strategy'))
                    <a href="{{ route('strategy.download', $strategy->id) }}" class="edit_icon me-2">
                        <i class="fa-solid fa-download"></i>
                    </a>
                    @endif
                    @if (auth()->user()->can('Delete Strategy'))
                        <a href="javascript:void(0)" id="delete" data-route="{{ route('strategy.delete', $strategy->id) }}"
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
                {!! $strategies->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr>
        <td colspan="4" class="text-center">No data found</td>
    </tr>
@endif
