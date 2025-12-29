@if (count($our_governances) > 0)
    @foreach ($our_governances as $key => $our_governance)
        <tr data-id="{{ $our_governance->id }}">
            <td class="d-flex align-items-center">
                <span class="drag-handle me-2" style="cursor:move"><i class="fas fa-arrows-alt"></i></span>
                <span class="order-number">{{ $our_governance->order_no ?? '—' }}</span>
            </td>
            {{-- <td>{{ ($our_governances->currentPage() - 1) * $our_governances->perPage() + $loop->index + 1 }}</td> --}}
            <td>{{ $our_governance->name ?? 'N/A' }}</td>
            <td>{{ $our_governance->slug ?? 'N/A' }}
            </td>
            <td>
                <div class="edit-1 d-flex align-items-center justify-content-center">
                    @if (auth()->user()->can('Delete Our Governance'))
                        <a title="Edit" href="{{ route('user.admin.our-governances.edit', $our_governance->id) }}">
                            <span class="edit-icon"><i class="fas fa-edit"></i></span>
                        </a>
                    @endif
                    @if (auth()->user()->can('Create Our Governance'))
                        <a title="Delete"
                            data-route="{{ route('user.admin.our-governances.delete', $our_governance->id) }}"
                            href="javascript:void(0);" id="delete">
                            <span class="trash-icon"><i class="fas fa-trash"></i></span>
                        </a>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
    <tr style="box-shadow: none;">
        <td colspan="5">
            <div class="d-flex justify-content-center">
                {!! $our_governances->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr>
        <td colspan="5" class="text-center">No Governance Found</td>
    </tr>
@endif
