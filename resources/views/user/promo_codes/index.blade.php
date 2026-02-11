@extends('user.layouts.master')

@section('title')
    {{ env('APP_NAME') }} | Promo Codes
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="sales-report-card-wrap">
                <div class="form-head d-flex justify-content-between align-items-center mb-3">
                    <h3>Promo Codes</h3>
                    <div>
                        @can('Create Promo Code')
                            <a href="{{ route('user.promo-codes.create') }}" class="btn btn-primary">
                                <i class="fa-solid fa-plus"></i> Add Promo Code
                            </a>
                        @endcan
                    </div>
                </div>

              

                <div class="table-responsive mt-3">
                    <table class="table align-middle bg-white color_body_text">
                        <thead class="color_head">
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Scope</th>
                                <th>Valid Period</th>
                                <th>Usage</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($promoCodes as $index => $promoCode)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong class="text-primary">{{ $promoCode->code }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $promoCode->is_percentage ? 'Percentage' : 'Fixed Amount' }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>
                                            @if ($promoCode->is_percentage)
                                                {{ $promoCode->discount_amount }}%
                                            @else
                                                ${{ number_format($promoCode->discount_amount, 2) }}
                                            @endif
                                        </strong>
                                    </td>
                                    <td>
                                        @if ($promoCode->scope_type === 'all_tiers')
                                            <span class="badge bg-success">All Tiers</span>
                                        @elseif ($promoCode->scope_type === 'selected_tiers')
                                            <span class="badge bg-warning">Specific Tiers</span>
                                        @elseif ($promoCode->scope_type === 'all_users')
                                            <span class="badge bg-info">All Users</span>
                                        @else
                                            <span class="badge bg-secondary">Specific Users</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <strong>From:</strong>
                                            {{ $promoCode->start_date ? $promoCode->start_date->format('M d, Y') : 'N/A' }}
                                        </div>
                                        <div>
                                            <strong>To:</strong>
                                            {{ $promoCode->end_date ? $promoCode->end_date->format('M d, Y') : 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $usageCount = $promoCode->usage_count;
                                            $totalLimit = $promoCode->usage_limit ?? '∞';
                                            $perUserLimit = $promoCode->per_user_limit ?? '∞';
                                        @endphp
                                        <div>
                                            <strong>Total:</strong> {{ $usageCount }} / {{ $totalLimit }}
                                        </div>
                                        <div>
                                            <strong>Per User:</strong> {{ $perUserLimit }}
                                        </div>
                                    </td>
                                    <td>
                                        @if ($promoCode->status)
                                            @if ($promoCode->end_date && $promoCode->end_date->isPast())
                                                <span class="badge bg-danger">Expired</span>
                                            @elseif ($promoCode->usage_limit && $promoCode->usage_count >= $promoCode->usage_limit)
                                                <span class="badge bg-warning">Limit Reached</span>
                                            @else
                                                <span class="badge bg-success">Active</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            @can('Edit Promo Code')
                                                <a href="{{ route('user.promo-codes.edit', $promoCode->id) }}"
                                                    class="edit_icon me-2" title="Edit">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                            @endcan
                                            @can('Delete Promo Code')
                                                <a href="javascript:void(0);" data-id="{{ $promoCode->id }}"
                                                    class="delete_icon delete-promo" title="Delete">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="ti ti-discount fs-3"></i>
                                            <p class="mt-2">No promo codes found</p>
                                            @can('Create Promo Code')
                                                <a href="{{ route('user.promo-codes.create') }}"
                                                    class="btn btn-primary btn-sm mt-2">
                                                    <i class="ti ti-plus"></i> Create First Promo Code
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($promoCodes->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {!! $promoCodes->links() !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '.delete-promo', function(e) {
            e.preventDefault();
            const promoId = $(this).data('id');
            const deleteUrl = "{{ url('user/promo-codes') }}/" + promoId;

            swal({
                    title: "Are you sure?",
                    text: "Do you want to delete this promo code? This action cannot be undone.",
                    type: "warning",
                    confirmButtonText: "Yes, Delete",
                    showCancelButton: true
                })
                .then((result) => {
                    if (result.value) {
                        // Create a form and submit it
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = deleteUrl;

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';

                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';

                        form.appendChild(csrfToken);
                        form.appendChild(methodField);
                        document.body.appendChild(form);
                        form.submit();
                    } else if (result.dismiss === 'cancel') {
                        swal(
                            'Cancelled',
                            'Promo code is safe :)',
                            'info'
                        )
                    }
                });
        });

        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>
@endpush
