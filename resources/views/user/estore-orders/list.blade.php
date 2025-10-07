@extends('user.layouts.master')

@section('title')
    E-Store Orders Management
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">


            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-3">Orders List</h3>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('user.store-orders.reports') }}" class="btn btn-primary me-2">
                                        <i class="fas fa-chart-bar"></i> Reports
                                    </a>
                                    {{-- <button type="button" class="btn btn-success" onclick="exportOrders()">
                                        <i class="fas fa-download"></i> Export CSV
                                    </button> --}}
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Filters -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="status-filter">Order Status</label>
                                    <select class="form-control" id="status-filter">
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="processing">Processing</option>
                                        <option value="shipped">Shipped</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="payment-status-filter">Payment Status</label>
                                    <select class="form-control" id="payment-status-filter">
                                        <option value="">All Payment Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="paid">Paid</option>
                                        <option value="failed">Failed</option>
                                        <option value="refunded">Refunded</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="date-from">From Date</label>
                                    <input type="date" class="form-control" id="date-from" placeholder="From Date">
                                </div>
                                <div class="col-md-2">
                                    <label for="date-to">To Date</label>
                                    <input type="date" class="form-control" id="date-to" placeholder="To Date">
                                </div>
                                <div class="col-md-2">
                                    <label for="search-filter">Search</label>
                                    <input type="text" class="form-control" id="search-filter"
                                        placeholder="Search Order Number.">
                                </div>
                            </div>

                            <!-- Orders Table -->
                            <div class="table-responsive" id="orders-table-container">
                                <!-- Table content will be loaded here via AJAX -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateStatusForm">
                    <div class="modal-body">
                        <input type="hidden" id="order-id" name="order_id">
                        <input type="hidden" id="current-status" name="current_status">

                        <div class="mb-3">
                            <label for="order-status" class="form-label">Order Status</label>
                            <select class="form-control" id="order-status" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>

                        <div class="mb-3" hidden>
                            <label for="payment-status" class="form-label">Payment Status</label>
                            <select class="form-control" id="payment-status" name="payment_status">
                                <option value="">Don't Change</option>
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="failed">Failed</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="order-notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="order-notes" name="notes" rows="3"
                                placeholder="Add any notes about this status change..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const STATUS_SEQUENCE = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $(document).ready(function() {
            loadOrdersTable();

            // Filter event handlers
            $('#status-filter, #payment-status-filter, #date-from, #date-to').on('change', function() {
                loadOrdersTable();
            });

            $('#search-filter').on('keyup', debounce(function() {
                loadOrdersTable();
            }, 500));

            // Update status form submission
            $('#updateStatusForm').on('submit', function(e) {
                e.preventDefault();
                updateOrderStatus();
            });
        });


        // every 5 sec fetch orders
        setInterval(function() {
            loadOrdersTable();
        }, 5000);

        function loadOrdersTable() {
            $.ajax({
                url: '{{ route('user.store-orders.fetch-data') }}',
                type: 'GET',
                data: {
                    status: $('#status-filter').val(),
                    payment_status: $('#payment-status-filter').val(),
                    date_from: $('#date-from').val(),
                    date_to: $('#date-to').val(),
                    search: $('#search-filter').val()
                },
                success: function(response) {
                    $('#orders-table-container').html(response);
                },
                error: function() {
                    toastr.error('Failed to load orders');
                }
            });
        }

        function openUpdateStatusModal(orderId, currentStatus, currentPaymentStatus, notes) {
            if (['delivered', 'cancelled'].includes(currentStatus)) {
                toastr.warning('This order status is final and cannot be changed.');
                return;
            }
            $('#order-id').val(orderId);
            $('#current-status').val(currentStatus);
            $('#payment-status').val('');
            $('#order-notes').val(notes || '');
            $('#updateStatusModal').modal('show');

            $('#order-status option').each(function() {
                const optionValue = $(this).val();
                const optionIndex = STATUS_SEQUENCE.indexOf(optionValue);
                const currentIndex = STATUS_SEQUENCE.indexOf(currentStatus);
                const isPreviousStep = optionIndex !== -1 && optionIndex < currentIndex && optionValue !==
                    currentStatus;
                $(this).prop('disabled', isPreviousStep);
            });
            $('#order-status').val(currentStatus);
        }

        function updateOrderStatus() {
            const currentStatus = $('#current-status').val();
            const targetStatus = $('#order-status').val();
            const currentIndex = STATUS_SEQUENCE.indexOf(currentStatus);
            const targetIndex = STATUS_SEQUENCE.indexOf(targetStatus);
            if (['delivered', 'cancelled'].includes(currentStatus)) {
                toastr.warning('Finalized orders cannot be updated.');
                return;
            }
            if (targetIndex !== -1 && currentIndex !== -1 && targetIndex < currentIndex) {
                toastr.warning('Cannot revert an order to a previous status.');
                return;
            }
            const formData = new FormData($('#updateStatusForm')[0]);

            $.ajax({
                url: '{{ route('user.store-orders.update-status') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        $('#updateStatusModal').modal('hide');
                        loadOrdersTable();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Failed to update order status');
                }
            });
        }

        function deleteOrder(orderId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('user.store-orders.delete', ':id') }}'.replace(':id', orderId),
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status) {
                                toastr.success(response.message);
                                loadOrdersTable();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            toastr.error('Failed to delete order');
                        }
                    });
                }
            });
        }

        function exportOrders() {
            const params = new URLSearchParams({
                status: $('#status-filter').val(),
                payment_status: $('#payment-status-filter').val(),
                date_from: $('#date-from').val(),
                date_to: $('#date-to').val(),
                search: $('#search-filter').val()
            });

            window.open('{{ route('user.store-orders.export') }}?' + params.toString(), '_blank');
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    </script>
@endpush
