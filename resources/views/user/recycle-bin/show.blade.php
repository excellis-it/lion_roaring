@extends('user.layouts.master')
@section('title', $tableName . ' - Recycle Bin')
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="{{ route('user.recycle-bin.index') }}" class="btn btn-sm btn-secondary mb-2">
                        <i class="fa fa-arrow-left"></i> Back to Recycle Bin
                    </a>
                    <h3 class="mb-0">{{ $tableName }}</h3>
                    <p class="text-muted small mb-0">Manage deleted records from this table</p>
                </div>
                <div>
                    <button type="button" class="btn btn-warning" onclick="restoreAll()">
                        <i class="fa fa-undo"></i> Restore All
                    </button>
                    <button type="button" class="btn btn-danger" onclick="emptyBin()">
                        <i class="fa fa-times-circle"></i> Empty Bin
                    </button>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Bulk Actions Bar -->
            <div class="bulk-actions mb-3" id="bulkActionsBar">
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><span id="selectedCount">0</span> item(s) selected</strong>
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-success" onclick="bulkRestore()">
                                    <i class="fa fa-undo"></i> Restore Selected
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="bulkDelete()">
                                    <i class="fa fa-trash"></i> Delete Selected
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="clearSelection()">
                                    <i class="fa fa-times"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fa fa-table me-2"></i>Deleted Records
                        </h5>
                        <span class="badge bg-danger">
                            {{ $deletedItems->total() }} Total Items
                        </span>
                    </div>

                    @if ($deletedItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;" class="text-center">
                                            <input type="checkbox" class="form-check-input select-all-checkbox"
                                                id="selectAll">
                                        </th>
                                        @foreach ($columns as $column)
                                            <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                                        @endforeach
                                        <th>Deleted At</th>
                                        <th style="width: 200px;" class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($deletedItems as $item)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input item-checkbox"
                                                    value="{{ $item->id }}">
                                            </td>
                                            @foreach ($columns as $column)
                                                <td>
                                                    @if ($column == 'id')
                                                        <strong class="text-primary">{{ $item->$column ?? 'N/A' }}</strong>
                                                    @elseif(in_array($column, ['created_at', 'updated_at']) && $item->$column)
                                                        <small
                                                            class="text-muted">{{ $item->$column->format('Y-m-d H:i') }}</small>
                                                    @else
                                                        {{ Str::limit($item->$column ?? 'N/A', 50) }}
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <i class="far fa-clock"></i> {{ $item->deleted_at->diffForHumans() }}
                                                </span>
                                                <br>
                                                <small
                                                    class="text-muted">{{ $item->deleted_at->format('M d, Y H:i:s') }}</small>
                                            </td>
                                            <td class="text-end">
                                                <div class="edit-1 d-flex align-items-center gap-2 justify-content-end">
                                                    <a title="Restore" href="javascript:void(0);"
                                                        onclick="restoreItem({{ $item->id }})">
                                                        <span class="edit-icon text-success">
                                                            <i class="fa fa-undo"></i>
                                                        </span>
                                                    </a>
                                                    <a title="Delete Permanently" href="javascript:void(0);"
                                                        onclick="deleteItem({{ $item->id }})">
                                                        <span class="trash-icon">
                                                            <i class="fa fa-trash"></i>
                                                        </span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if ($deletedItems->hasPages())
                            <div class="mt-3">
                                {{ $deletedItems->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-inbox text-muted fa-4x mb-3"></i>
                            <h4 class="text-muted">No Deleted Items</h4>
                            <p class="text-muted">This table has no items in the recycle bin.</p>
                            <a href="{{ route('user.recycle-bin.index') }}" class="btn btn-primary mt-3">
                                <i class="fa fa-arrow-left"></i> Back to Recycle Bin
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection

@push('styles')
    <style>
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .bulk-actions {
            display: none;
        }

        .bulk-actions.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-check-input {
            cursor: pointer;
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
                showCancelButton: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: `/user/recycle-bin/${tableName}/${id}/restore`,
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
                showCancelButton: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: `/user/recycle-bin/${tableName}/${id}/force-delete`,
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
                showCancelButton: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: `/user/recycle-bin/${tableName}/bulk-restore`,
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
                showCancelButton: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: `/user/recycle-bin/${tableName}/bulk-force-delete`,
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
                showCancelButton: true
            }).then((result) => {
                if (result.value) {
                    window.location.href = `/user/recycle-bin/${tableName}/restore-all`;
                }
            });
        }

        // Empty bin (delete all permanently)
        function emptyBin() {
            swal({
                title: "Empty Recycle Bin?",
                text: "All items will be PERMANENTLY deleted. This action cannot be undone!",
                type: "warning",
                confirmButtonText: "Yes, empty the bin!",
                showCancelButton: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: `/user/recycle-bin/${tableName}/empty-bin`,
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
