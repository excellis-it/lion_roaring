@if (count($our_organizations) > 0)
@foreach ($our_organizations as $key => $our_organizatio)
    <tr>
        <td>{{ ($our_organizations->currentPage()-1) * $our_organizations->perPage() + $loop->index + 1 }}</td>
        <td>{{ $our_organizatio->name ?? 'N/A' }}</td>
        <td>{{ $our_organizatio->slug ?? 'N/A' }}
        </td>
        <td>
            <div class="edit-1 d-flex align-items-center justify-content-center">
                <a title="Edit" href="{{ route('our-organizations.edit', $our_organizatio->id) }}">
                    <span class="edit-icon"><i class="ph ph-pencil-simple"></i></span>
                </a>
                <a title="Delete" data-route="{{ route('our-organizations.delete', $our_organizatio->id) }}"
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
            {!! $our_organizations->links() !!}
        </div>
    </td>
</tr>
@else
<tr>
    <td colspan="4" class="text-center">No Organization Found</td>
</tr>
@endif
