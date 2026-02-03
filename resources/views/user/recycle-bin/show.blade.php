@extends('user.layouts.master')
@section('title', $tableName . ' - Restore')
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border py-4">
            <!-- Header with Gradient Background -->
            <div class="page-header mb-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <a href="{{ route('user.recycle-bin.index') }}" class="btn-back mb-3">
                            <i class="fa fa-arrow-left"></i>
                            <span>Back to Restore</span>
                        </a>
                        <div class="d-flex align-items-center gap-3">
                            <div class="table-icon">
                                <i class="fa fa-database"></i>
                            </div>
                            <div>
                                <h2 class="mb-1 fw-bold">{{ $tableName }}</h2>
                                <p class="text-muted mb-0 small">Manage deleted records from this table</p>
                            </div>
                        </div>
                    </div>
                    <div class="header-actions">
                        <button type="button" class="btn-action btn-warning" onclick="restoreAll()">
                            <i class="fa fa-undo"></i>
                            <span>Restore All</span>
                        </button>
                        <button type="button" class="btn-action btn-danger" onclick="emptyBin()">
                            <i class="fa fa-bomb"></i>
                            <span>Empty Bin</span>
                        </button>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
                    <i class="fa fa-check-circle me-2"></i>
                    <strong>Success!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show custom-alert" role="alert">
                    <i class="fa fa-exclamation-circle me-2"></i>
                    <strong>Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Bulk Actions Bar -->
            <div class="bulk-actions-wrapper" id="bulkActionsBar">
                <div class="bulk-actions-content">
                    <div class="bulk-info">
                        <i class="fa fa-check-square text-primary me-2"></i>
                        <strong><span id="selectedCount">0</span> item(s) selected</strong>
                    </div>
                    <div class="bulk-buttons">
                        <button type="button" class="btn btn-sm btn-success rounded-pill px-3" onclick="bulkRestore()">
                            <i class="fa fa-undo me-1"></i> Restore Selected
                        </button>
                        <button type="button" class="btn btn-sm btn-danger rounded-pill px-3" onclick="bulkDelete()">
                            <i class="fa fa-trash me-1"></i> Delete Selected
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                            onclick="clearSelection()">
                            <i class="fa fa-times me-1"></i> Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Summary -->
            <div class="stats-summary mb-4">
                <div class="stat-box">
                    <div class="stat-icon bg-danger">
                        <i class="fa fa-trash-alt"></i>
                    </div>
                    <div class="stat-details">
                        <h4>{{ $deletedItems->total() }}</h4>
                        <p>Deleted Items</p>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon bg-primary">
                        <i class="fa fa-table"></i>
                    </div>
                    <div class="stat-details">
                        <h4>{{ count($columns) }}</h4>
                        <p>Visible Columns</p>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon bg-warning">
                        <i class="fa fa-clock"></i>
                    </div>
                    <div class="stat-details">
                        <h4>{{ $deletedItems->currentPage() }}/{{ $deletedItems->lastPage() }}</h4>
                        <p>Current Page</p>
                    </div>
                </div>
            </div>

            <!-- Data Table Card -->
            <div class="data-table-card">
                @if ($deletedItems->count() > 0)
                    <div class="table-header">
                        <h5>
                            <i class="fa fa-list me-2"></i>Deleted Records
                        </h5>
                    </div>

                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;" class="text-center checkbox-column">
                                        <input type="checkbox" class="form-check-input select-all-checkbox" id="selectAll">
                                    </th>
                                    @foreach ($columns as $column)
                                        <th>
                                            @if ($table === 'warehouse_products' && $column === 'product_id')
                                                <i class="fa fa-box text-primary me-1"></i>Product Name
                                            @elseif($table === 'warehouse_products' && $column === 'warehouse_id')
                                                <i class="fa fa-warehouse text-info me-1"></i>Warehouse Name
                                            @else
                                                {{ ucfirst(str_replace('_', ' ', $column)) }}
                                            @endif
                                        </th>
                                    @endforeach
                                    <th><i class="fa fa-calendar text-warning me-1"></i>Deleted At</th>
                                    <th style="width: 150px;" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deletedItems as $item)
                                    <tr class="table-row">
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input item-checkbox"
                                                value="{{ $item->id }}">
                                        </td>
                                        @foreach ($columns as $column)
                                            <td>
                                                @if ($column == 'id')
                                                    <span class="id-badge">#{{ $item->$column ?? 'N/A' }}</span>
                                                @elseif($table === 'warehouse_products' && $column === 'product_id')
                                                    <span
                                                        class="text-dark fw-semibold">{{ $item->product->name ?? 'N/A' }}</span>
                                                @elseif($table === 'warehouse_products' && $column === 'warehouse_id')
                                                    <span
                                                        class="text-dark fw-semibold">{{ $item->warehouse->name ?? 'N/A' }}</span>
                                                @elseif(in_array($column, ['created_at', 'updated_at']) && $item->$column)
                                                    <small
                                                        class="text-muted">{{ $item->$column->format('Y-m-d H:i') }}</small>
                                                @else
                                                    {{ Str::limit($item->$column ?? 'N/A', 50) }}
                                                @endif
                                            </td>
                                        @endforeach
                                        <td>
                                            <div class="deleted-info">
                                                <span class="time-badge">
                                                    <i
                                                        class="far fa-clock me-1"></i>{{ $item->deleted_at->diffForHumans() }}
                                                </span>
                                                <small
                                                    class="text-muted d-block mt-1">{{ $item->deleted_at->format('M d, Y H:i:s') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="action-btn restore-btn" title="Restore"
                                                    onclick="restoreItem({{ $item->id }})">
                                                    <i class="fa fa-undo"></i>
                                                </button>
                                                <button class="action-btn delete-btn" title="Delete Permanently"
                                                    onclick="deleteItem({{ $item->id }})">
                                                    <i class="fa fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($deletedItems->hasPages())
                        <div class="table-footer">
                            <div class="pagination-info">
                                Showing {{ $deletedItems->firstItem() }} to {{ $deletedItems->lastItem() }} of
                                {{ $deletedItems->total() }} items
                            </div>
                            <div class="pagination-wrapper">
                                {{ $deletedItems->links() }}
                            </div>
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fa fa-box-open"></i>
                        </div>
                        <h4>This Table is Clean!</h4>
                        <p>No deleted items found in the recycle bin.</p>
                        <a href="{{ route('user.recycle-bin.index') }}" class="btn btn-primary rounded-pill px-4 mt-3">
                            <i class="fa fa-arrow-left me-2"></i>Back to Restore
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Page Header */
        .page-header {
            background: #7851a9;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
            color: white;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: white;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            transform: translateX(-4px);
        }

        .table-icon {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
        }

        .table-icon i {
            font-size: 32px;
            color: white;
        }

        .page-header h2 {
            color: white;
        }

        .page-header .text-muted {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        .header-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-action i {
            font-size: 1.1rem;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }

        /* Custom Alerts */
        .custom-alert {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 1rem 1.25rem;
        }

        /* Bulk Actions */
        .bulk-actions-wrapper {
            display: none;
            margin-bottom: 1.5rem;
        }

        .bulk-actions-wrapper.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        .bulk-actions-content {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #3b82f6;
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .bulk-info {
            display: flex;
            align-items: center;
            font-size: 1.05rem;
            color: #1e40af;
        }

        .bulk-buttons {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        /* Stats Summary */
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .stat-box {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
        }

        .stat-box:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon i {
            font-size: 24px;
            color: white;
        }

        .stat-details h4 {
            margin: 0;
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1;
        }

        .stat-details p {
            margin: 0.25rem 0 0 0;
            font-size: 0.875rem;
            color: #64748b;
        }

        /* Data Table Card */
        .data-table-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .table-header {
            padding: 1.5rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .table-header h5 {
            margin: 0;
            font-weight: 600;
            color: #1e293b;
        }

        /* Custom Table */
        .custom-table {
            width: 100%;
            margin: 0;
        }

        .custom-table thead {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .custom-table thead th {
            padding: 1.125rem 1rem;
            font-weight: 600;
            font-size: 0.875rem;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
        }

        .custom-table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-row {
            transition: background-color 0.2s ease;
        }

        .table-row:hover {
            background-color: #f8fafc;
        }

        .id-badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .deleted-info {
            white-space: nowrap;
        }

        .time-badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            background: #f1f5f9;
            border-radius: 6px;
            font-size: 0.813rem;
            font-weight: 500;
            color: #64748b;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.938rem;
        }

        .restore-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .restore-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .delete-btn {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .delete-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }

        /* Table Footer */
        .table-footer {
            padding: 1.25rem 1.5rem;
            background: #f8fafc;
            border-top: 2px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .pagination-info {
            color: #64748b;
            font-size: 0.875rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
        }

        .empty-icon i {
            font-size: 3.5rem;
            color: white;
        }

        .empty-state h4 {
            color: #1e293b;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .empty-state p {
            color: #64748b;
            font-size: 1rem;
        }

        /* Animations */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header {
                padding: 1.5rem;
            }

            .table-icon {
                width: 48px;
                height: 48px;
            }

            .table-icon i {
                font-size: 24px;
            }

            .page-header h2 {
                font-size: 1.5rem;
            }

            .header-actions {
                width: 100%;
            }

            .btn-action {
                flex: 1;
                justify-content: center;
            }

            .stats-summary {
                grid-template-columns: 1fr;
            }

            .bulk-actions-content {
                flex-direction: column;
                align-items: stretch;
            }

            .bulk-buttons {
                flex-direction: column;
            }

            .table-footer {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        const tableName = "{{ $table }}";
        const csrfToken = "{{ csrf_token() }}";

        // Select All Functionality
        $('#selectAll').on('change', function() {
            $('.item-checkbox').prop('checked', this.checked);
            updateBulkActionsBar();
        });

        $('.item-checkbox').on('change', function() {
            updateBulkActionsBar();

            // Update select all checkbox
            const totalCheckboxes = $('.item-checkbox').length;
            const checkedCheckboxes = $('.item-checkbox:checked').length;
            $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
        });

        function updateBulkActionsBar() {
            const selectedCount = $('.item-checkbox:checked').length;
            $('#selectedCount').text(selectedCount);

            if (selectedCount > 0) {
                $('#bulkActionsBar').addClass('show');
            } else {
                $('#bulkActionsBar').removeClass('show');
            }
        }

        function clearSelection() {
            $('.item-checkbox').prop('checked', false);
            $('#selectAll').prop('checked', false);
            updateBulkActionsBar();
        }

        // Restore single item
        function restoreItem(id) {
            swal({
                title: "Restore Item?",
                text: "This item will be restored to its original state.",
                type: "question",
                confirmButtonText: "Yes, restore it!",
                confirmButtonColor: "#10b981",
                showCancelButton: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "{{ route('user.recycle-bin.restore', ['table' => ':table', 'id' => ':id']) }}"
                            .replace(':table', tableName).replace(':id', id),
                        type: 'POST',
                        data: {
                            _token: csrfToken
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                setTimeout(() => location.reload(), 1000);
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Failed to restore item');
                        }
                    });
                }
            });
        }

        // Delete single item permanently
        function deleteItem(id) {
            swal({
                title: "Permanently Delete?",
                text: "This action cannot be undone!",
                type: "warning",
                confirmButtonText: "Yes, delete permanently!",
                confirmButtonColor: "#ef4444",
                showCancelButton: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "{{ route('user.recycle-bin.force-delete', ['table' => ':table', 'id' => ':id']) }}"
                            .replace(':table', tableName).replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: csrfToken
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                setTimeout(() => location.reload(), 1000);
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Failed to delete item');
                        }
                    });
                }
            });
        }

        // Bulk restore
        function bulkRestore() {
            const selectedIds = $('.item-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            if (selectedIds.length === 0) {
                toastr.info('Please select items to restore');
                return;
            }

            swal({
                title: "Restore Selected Items?",
                text: `You are about to restore ${selectedIds.length} item(s).`,
                type: "question",
                confirmButtonText: "Yes, restore them!",
                confirmButtonColor: "#10b981",
                showCancelButton: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "{{ route('user.recycle-bin.bulk-restore', ['table' => ':table']) }}".replace(
                            ':table', tableName),
                        type: 'POST',
                        data: {
                            _token: csrfToken,
                            ids: selectedIds
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                setTimeout(() => location.reload(), 1000);
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Failed to restore items');
                        }
                    });
                }
            });
        }

        // Bulk delete
        function bulkDelete() {
            const selectedIds = $('.item-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            if (selectedIds.length === 0) {
                toastr.info('Please select items to delete');
                return;
            }

            swal({
                title: "Permanently Delete?",
                text: `You are about to PERMANENTLY delete ${selectedIds.length} item(s). This cannot be undone!`,
                type: "warning",
                confirmButtonText: "Yes, delete permanently!",
                confirmButtonColor: "#ef4444",
                showCancelButton: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "{{ route('user.recycle-bin.bulk-force-delete', ['table' => ':table']) }}"
                            .replace(':table', tableName),
                        type: 'POST',
                        data: {
                            _token: csrfToken,
                            ids: selectedIds
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                setTimeout(() => location.reload(), 1000);
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Failed to delete items');
                        }
                    });
                }
            });
        }

        // Restore all items
        function restoreAll() {
            swal({
                title: "Restore All Items?",
                text: "All deleted items in this table will be restored.",
                type: "question",
                confirmButtonText: "Yes, restore all!",
                confirmButtonColor: "#10b981",
                showCancelButton: true
            }).then((result) => {
                if (result.value) {
                    window.location.href = "{{ route('user.recycle-bin.restore-all', ['table' => ':table']) }}"
                        .replace(':table', tableName);
                }
            });
        }

        // Empty bin (delete all permanently)
        function emptyBin() {
            swal({
                title: "Empty Restore?",
                text: "All items will be PERMANENTLY deleted. This action cannot be undone!",
                type: "warning",
                confirmButtonText: "Yes, empty the bin!",
                confirmButtonColor: "#ef4444",
                showCancelButton: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "{{ route('user.recycle-bin.empty-bin', ['table' => ':table']) }}".replace(
                            ':table', tableName),
                        type: 'DELETE',
                        data: {
                            _token: csrfToken
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                setTimeout(() => location.reload(), 1000);
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Failed to empty bin');
                        }
                    });
                }
            });
        }

        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>
@endpush
