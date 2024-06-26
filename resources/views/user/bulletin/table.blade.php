@if (count($bulletins) > 0)
    @foreach ($bulletins as $key => $bulletin)
        <tr>
            <td>{{ $bulletins->firstItem() + $key }}</td>
            @if (auth()->user()->hasRole('ADMIN'))
            <td>
                {{ isset($bulletin->user->full_name) && !empty($bulletin->user->full_name) ? $bulletin->user->full_name : 'Unknown' }}
            </td>
            @endif
            <td> {{ $bulletin->title }}</td>
            <td> {{ $bulletin->description }}</td>
            <td>
                <div class="d-flex">
                    @if (auth()->user()->can('Edit Bulletin'))
                        <a href="{{ route('bulletins.edit', $bulletin->id) }}" class="delete_icon">
                            <i class="fa-solid fa-edit"></i>
                        </a> &nbsp; &nbsp;
                    @endif
                    @if (auth()->user()->can('Delete Bulletin'))
                        <a href="javascript:void(0)" id="delete" data-route="{{ route('bulletins.delete', $bulletin->id) }}"
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
                {!! $bulletins->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr>
        <td colspan="5" class="text-center">No data found</td>
    </tr>
@endif
