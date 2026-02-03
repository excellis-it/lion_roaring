@extends('user.layouts.master')
@section('title', 'Recycle Bin')
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0">Recycle Bin Management</h3>
                    <p class="text-muted small mb-0">Manage soft-deleted records across all tables</p>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Info Card -->
            <div class="card mb-4 border-0 shadow-sm bg-light">
                <div class="card-body">
                    <h5 class="mb-2"><i class="fa fa-info-circle text-primary me-2"></i>About Recycle Bin</h5>
                    <ul class="mb-0">
                        <li><strong>Restore:</strong> Recover deleted items back to active state</li>
                        <li><strong>Permanent Delete:</strong> Remove items forever (cannot be undone)</li>
                        <li><strong>Access:</strong> Only SUPER ADMIN users can manage the recycle bin</li>
                        <li><strong>Safety:</strong> All operations require confirmation to prevent accidents</li>
                    </ul>
                </div>
            </div>

            @php
                $totalDeletedItems = $recycleCounts->sum('count');
            @endphp

            @if ($totalDeletedItems > 0)
                <div class="alert alert-warning border-0 mb-4">
                    <i class="fa fa-exclamation-triangle me-2"></i>
                    <strong>{{ $totalDeletedItems }} deleted item(s)</strong> found across all tables.
                    Click on any table below to manage deleted records.
                </div>
            @endif

            <!-- Tables with Deleted Items -->
            @if ($recycleCounts->where('count', '>', 0)->count() > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fa fa-trash-alt text-danger me-2"></i>Tables with Deleted Items</h5>
                        <div class="row g-3">
                            @foreach ($recycleCounts as $tableData)
                                @if ($tableData['count'] > 0)
                                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                        <a href="{{ route('user.recycle-bin.show', $tableData['table']) }}"
                                            class="text-decoration-none">
                                            <div class="card border h-100 hover-shadow">
                                                <div class="card-body text-center">
                                                    <i class="fa fa-trash-alt text-danger fa-2x mb-3"></i>
                                                    <h3 class="text-danger mb-2">{{ $tableData['count'] }}</h3>
                                                    <p class="mb-0 text-dark fw-semibold">{{ $tableData['name'] }}</p>
                                                    <span class="badge bg-danger mt-2">View Items</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Clean Tables Section -->
            @if ($recycleCounts->where('count', 0)->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">
                            <i class="fa fa-check-circle text-success me-2"></i>Clean Tables (No Deleted Items)
                        </h5>
                        <div class="row g-2">
                            @foreach ($recycleCounts as $tableData)
                                @if ($tableData['count'] === 0)
                                    <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                                        <span class="badge bg-success w-100 p-2">
                                            <i class="fa fa-check me-1"></i> {{ $tableData['name'] }}
                                        </span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Empty State -->
            @if ($totalDeletedItems === 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-center py-5">
                            <i class="fa fa-check-circle text-success fa-5x mb-3"></i>
                            <h3>Recycle Bin is Empty</h3>
                            <p class="text-muted">All tables are clean. No deleted items to display.</p>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection

@push('styles')
    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
            transform: translateY(-2px);
        }
    </style>
@endpush

@push('scripts')
    <script>
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>
@endpush
