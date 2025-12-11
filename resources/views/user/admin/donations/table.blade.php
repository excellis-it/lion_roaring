@if (count($donations) > 0)
@foreach ($donations as $key => $donation)
    <tr>
        <td> {{ ($donations->currentPage()-1) * $donations->perPage() + $loop->index + 1 }}</td>
        <td>{{ $donation->transaction_id ?? 'N/A' }}</td>
        <td>${{ $donation->donation_amount ?? 'N/A' }}</td>
        <td>{{ $donation->full_name ?? 'N/A' }}</td>
        <td>{{ $donation->email ?? 'N/A' }}</td>
        <td>{{ $donation->address ?? 'N/A' }}
        </td>
        {{-- city --}}
        <td>{{ $donation->city ?? 'N/A' }}</td>
        {{-- state --}}
        <td>{{ $donation->states ? $donation->states->name : 'N/A' }}</td>
        {{-- zip --}}
        <td>{{ $donation->postcode ?? 'N/A' }}</td>
        {{-- country --}}
        <td>{{ $donation->country->name ?? 'N/A' }}</td>
        {{-- donation_amount --}}

        {{-- transaction_id --}}

        {{-- payment_status --}}
        <td>{{ $donation->payment_status ?? 'N/A' }}</td>
        {{-- created_at --}}
        <td>{{ $donation->created_at->format('d M Y') }}</td>
        <td>
            <div class="edit-1 d-flex align-items-center justify-content-center">
                <a title="Delete" data-route="{{ route('user.admin.donations.delete', $donation->id) }}"
                    href="javascript:void(0);" id="delete">
                    <span class="trash-icon"><i class="fas fa-trash"></i></span>
                </a>
            </div>
        </td>
    </tr>
@endforeach
<tr class="toxic">
    <td colspan="13" class="text-left">
        <div class="d-flex justify-content-between">
            <div class="">
                 (Showing {{ $donations->firstItem() }} – {{ $donations->lastItem() }} Donation of
                {{$donations->total() }} Donations)
            </div>
            <div>{!! $donations->links() !!}</div>
        </div>
    </td>
</tr>
@else
<tr class="toxic">
    <td colspan="13" class="text-center">No Donation Found</td>
</tr>
@endif
