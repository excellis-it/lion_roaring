@if (count($our_governances) > 0)
@foreach ($our_governances as $key => $our_governance)
    <tr>
        <td>{{ ($our_governances->currentPage()-1) * $our_governances->perPage() + $loop->index + 1 }}</td>
        <td>{{ $our_governance->name ?? 'N/A' }}</td>
        <td>{{ $our_governance->slug ?? 'N/A' }}
        </td>
        <td>
            <div class="edit-1 d-flex align-items-center justify-content-center">
                <a title="Edit" href="{{ route('our-governances.edit', $our_governance->id) }}">
                    <span class="edit-icon"><i class="ph ph-pencil-simple"></i></span>
                </a>
                <a title="Delete" data-route="{{ route('our-governances.delete', $our_governance->id) }}"
                    href="javascript:void(0);" id="delete">
                    <span class="trash-icon"><i class="ph ph-trash"></i></span>
                </a>
            </div>
        </td>
    </tr>
@endforeach
<tr style="box-shadow: none;">
    <td colspan="4">
        <div class="d-flex justify-content-center">
            {!! $our_governances->links() !!}
        </div>
    </td>
</tr>
@else
<tr>
    <td colspan="4" class="text-center">No Governance Found</td>
</tr>
@endif
