@extends('user.layouts.master')
@section('title', 'Recycle Bin')
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border py-4">
            <!-- Header Section with Gradient -->
            <div class="recycle-bin-header mb-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-box me-3">
                                <i class="fa fa-trash-restore-alt"></i>
                            </div>
                            <div>
                                <h2 class="mb-0 fw-bold">Recycle Bin Management</h2>
                                <p class="text-muted mb-0 small">Restore or permanently delete soft-deleted records</p>
                            </div>
                        </div>
                    </div>
                    @php
                        $totalDeletedItems = $recycleCounts->sum('count');
                    @endphp
                    <div class="stats-badge">
                        <div class="stat-item">
                            <div class="stat-value">{{ $totalDeletedItems }}</div>
                            <div class="stat-label">Total Deleted</div>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
                    <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Quick Info Panel -->
            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="info-card">
                        <div class="info-icon bg-primary">
                            <i class="fa fa-undo"></i>
                        </div>
                        <div class="info-content">
                            <h6>Restore Items</h6>
                            <p>Recover deleted items back to active state</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="info-card">
                        <div class="info-icon bg-danger">
                            <i class="fa fa-trash-alt"></i>
                        </div>
                        <div class="info-content">
                            <h6>Permanent Delete</h6>
                            <p>Remove items forever (cannot be undone)</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="info-card">
                        <div class="info-icon bg-warning">
                            <i class="fa fa-shield-alt"></i>
                        </div>
                        <div class="info-content">
                            <h6>Super Admin Only</h6>
                            <p>Restricted access for enhanced security</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="info-card">
                        <div class="info-icon bg-success">
                            <i class="fa fa-check-double"></i>
                        </div>
                        <div class="info-content">
                            <h6>Safe Operations</h6>
                            <p>All actions require confirmation</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tables with Deleted Items -->
            @if ($recycleCounts->where('count', '>', 0)->count() > 0)
                <div class="section-header mb-3">
                    <h5 class="mb-0">
                        <i class="fa fa-exclamation-circle text-danger me-2"></i>
                        Tables with Deleted Items
                    </h5>
                    <span class="badge bg-danger">{{ $recycleCounts->where('count', '>', 0)->count() }} Tables</span>
                </div>

                <div class="row g-3 mb-4">
                    @foreach ($recycleCounts as $tableData)
                        @if ($tableData['count'] > 0)
                            <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6 col-sm-6">
                                <a href="{{ route('user.recycle-bin.show', $tableData['table']) }}"
                                    class="text-decoration-none">
                                    <div class="table-card has-items">
                                        <div class="table-card-icon">
                                            <i class="fa fa-database"></i>
                                        </div>
                                        <div class="table-card-content">
                                            <h4 class="count">{{ $tableData['count'] }}</h4>
                                            <p class="table-name">{{ $tableData['name'] }}</p>
                                            <span class="view-badge">
                                                <i class="fa fa-arrow-right me-1"></i>View Items
                                            </span>
                                        </div>
                                        <div class="table-card-overlay"></div>
                                    </div>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            <!-- Clean Tables Section -->
            @if ($recycleCounts->where('count', 0)->count() > 0)
                <div class="section-header mb-3">
                    <h5 class="mb-0">
                        <i class="fa fa-check-circle text-success me-2"></i>
                        Clean Tables
                    </h5>
                    <span class="badge bg-success">{{ $recycleCounts->where('count', 0)->count() }} Tables</span>
                </div>

                <div class="clean-tables-grid mb-4">
                    @foreach ($recycleCounts as $tableData)
                        @if ($tableData['count'] === 0)
                            <div class="clean-table-badge">
                                <i class="fa fa-check-circle me-1"></i>
                                <span>{{ $tableData['name'] }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            <!-- Empty State -->
            @if ($totalDeletedItems === 0)
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <h3>Recycle Bin is Sparkling Clean!</h3>
                    <p>All tables are clean. No deleted items to display.</p>
                    <div class="empty-state-decoration">
                        <i class="fa fa-trash-alt"></i>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Header Styling */
        .recycle-bin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
            color: white;
        }

        .icon-box {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
        }

        .icon-box i {
            font-size: 32px;
            color: white;
        }

        .recycle-bin-header h2 {
            color: white;
        }

        .recycle-bin-header .text-muted {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        .stats-badge {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 1.5rem;
            min-width: 150px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.95);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Info Cards */
        .info-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: start;
            gap: 1rem;
            transition: all 0.3s ease;
            height: 100%;
        }

        .info-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .info-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .info-icon i {
            font-size: 20px;
            color: white;
        }

        .info-content h6 {
            margin: 0 0 0.25rem 0;
            font-weight: 600;
            color: #1e293b;
            font-size: 0.95rem;
        }

        .info-content p {
            margin: 0;
            font-size: 0.813rem;
            color: #64748b;
            line-height: 1.4;
        }

        /* Section Headers */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .section-header h5 {
            font-weight: 600;
            color: #1e293b;
        }

        /* Table Cards */
        .table-card {
            position: relative;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            border: 2px solid transparent;
        }

        .table-card.has-items {
            border-color: #fee2e2;
        }

        .table-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(239, 68, 68, 0.2);
            border-color: #ef4444;
        }

        .table-card-icon {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            padding: 1.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .table-card-icon i {
            font-size: 2rem;
            color: white;
        }

        .table-card-content {
            padding: 1.25rem;
            text-align: center;
        }

        .table-card-content .count {
            font-size: 2.25rem;
            font-weight: 700;
            color: #ef4444;
            margin: 0 0 0.5rem 0;
            line-height: 1;
        }

        .table-card-content .table-name {
            font-size: 0.938rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0 0 1rem 0;
            min-height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .table-card-content .view-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border-radius: 6px;
            font-size: 0.813rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .table-card:hover .view-badge {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        }

        /* Clean Tables */
        .clean-tables-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 0.75rem;
        }

        .clean-table-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.2);
            transition: all 0.3s ease;
        }

        .clean-table-badge:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .clean-table-badge i {
            font-size: 1rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            position: relative;
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            border-radius: 12px;
            overflow: hidden;
        }

        .empty-state-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        }

        .empty-state-icon i {
            font-size: 3.5rem;
            color: white;
        }

        .empty-state h3 {
            color: #1e293b;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .empty-state p {
            color: #64748b;
            font-size: 1.125rem;
        }

        .empty-state-decoration {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.03;
        }

        .empty-state-decoration i {
            font-size: 20rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .recycle-bin-header {
                padding: 1.5rem;
            }

            .icon-box {
                width: 48px;
                height: 48px;
            }

            .icon-box i {
                font-size: 24px;
            }

            .recycle-bin-header h2 {
                font-size: 1.5rem;
            }

            .stats-badge {
                padding: 1rem;
                min-width: 120px;
            }

            .stat-value {
                font-size: 2rem;
            }

            .info-card {
                padding: 1rem;
            }

            .clean-tables-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                gap: 0.5rem;
            }
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
