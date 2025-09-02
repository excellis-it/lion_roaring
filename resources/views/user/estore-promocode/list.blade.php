@extends('user.layouts.master')

@section('title')
    E-Store Promo Code Management
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <div class="row mb-3">
                <div class="col-md-10">
                    <h3 class="mb-3">Promo Codes List</h3>
                </div>
                <div class="col-md-2 float-right">

                    <a href="{{ route('store-promo-codes.create') }}" class="btn btn-primary w-100"><i
                            class="fa-solid fa-upload"></i>
                        Create Code</a>

                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Promo Code</th>
                            <th>Discount</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($promoCodes as $key => $promoCode)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $promoCode->code }}</td>
                                <td>{{ $promoCode->discount_amount }} <span>
                                        ({{ $promoCode->is_percentage ? '%' : 'Flat' }})</span></td>
                                <td>{{ \Carbon\Carbon::parse($promoCode->start_date)->format('d-m-Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($promoCode->end_date)->format('d-m-Y') }}</td>
                                <td>{{ $promoCode->status == 1 ? 'Active' : 'Inactive' }}</td>
                                <td class="d-flex">
                                    <a href="{{ route('store-promo-codes.edit', $promoCode->id) }}"
                                        class="edit_icon me-2"><i class="fa-solid fa-edit"></i></a>
                                    <a href="javascript:void(0)" id="delete"
                                        data-route="{{ route('store-promo-codes.delete', $promoCode->id) }}"
                                        class="delete_icon">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>

                </table>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '#delete', function(e) {
            swal({
                    title: "Are you sure?",
                    text: "To delete this file.",
                    type: "warning",
                    confirmButtonText: "Yes",
                    showCancelButton: true
                })
                .then((result) => {
                    if (result.value) {
                        window.location = $(this).data('route');
                    } else if (result.dismiss === 'cancel') {
                        swal(
                            'Cancelled',
                            'Your stay here :)',
                            'error'
                        )
                    }
                })
        });
    </script>
@endpush
