@if (count($organization_centers) > 0)
    @foreach ($organization_centers as $key => $organization_center)
        <tr>
            <td>{{ ($organization_centers->currentPage() - 1) * $organization_centers->perPage() + $loop->index + 1 }}
            </td>
            <td>{{ $organization_center->ourOrganization->name ? $organization_center->ourOrganization->name : 'N/A' }}
            </td>
            <td>{{ $organization_center->name ?? 'N/A' }}</td>
            <td>{{ $organization_center->slug ?? 'N/A' }}
            </td>
            <td>
                <div class="edit-1 d-flex align-items-center justify-content-center">
                    @if (auth()->user()->can('Edit Organization Center'))
                        <a title="Edit" href="{{ route('organization-centers.edit', $organization_center->id) }}">
                            <span class="edit-icon"><i class="ph ph-pencil-simple"></i></span>
                        </a>
                    @endif
                    @if (auth()->user()->can('Delete Organization Center'))
                        <a title="Delete"
                            data-route="{{ route('organization-centers.delete', $organization_center->id) }}"
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
                {!! $organization_centers->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr>
        <td colspan="5" class="text-center">No Organization Center Found</td>
    </tr>
@endif
