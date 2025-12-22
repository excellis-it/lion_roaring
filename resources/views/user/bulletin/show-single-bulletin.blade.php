<td>
    {{ $bulletins->firstItem() + $key }}
</td>
@if (auth()->user()->hasRole('SUPER ADMIN'))
    <td>
        {{ isset($bulletin->user->full_name) && !empty($bulletin->user->full_name) ? $bulletin->user->full_name : 'Unknown' }}
    </td>
@endif
<td id="bulletin-title-{{ $bulletin->id }}"> {{ $bulletin->title }}</td>
<td id="bulletin-description-{{ $bulletin->id }}"> {{ $bulletin->description }}</td>
<td>
    {{ $bulletin->country->name ?? ''}}
</td>
<td>
    <div class="d-flex">
        @if (auth()->user()->can('Edit Bulletin'))
            <a href="{{ route('bulletins.edit', $bulletin->id) }}" class="delete_icon">
                <i class="fa-solid fa-edit"></i>
            </a> &nbsp; &nbsp;
        @endif
        @if (auth()->user()->can('Delete Bulletin'))
            <a href="javascript:void(0)" id="bulletin-delete" data-route="{{ route('bulletins.delete', $bulletin->id) }}"
                data-bulletin-id="{{ $bulletin->id }}" class="delete_icon">
                <i class="fa-solid fa-trash"></i>
            </a>
        @endif
    </div>
</td>
