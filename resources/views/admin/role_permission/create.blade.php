@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Create Role Permission
@endsection
@push('styles')
    <style>
        .permissions-card {
            border: 1px solid #e0e6ed;
            border-radius: 8px;
            background: #fff;
            overflow: hidden;
            margin-top: 20px;
        }

        .permissions-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: #f8fbff;
            border-bottom: 1px solid #e0e6ed;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .search-wrapper {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .search-input-group {
            position: relative;
        }

        .search-input-group input {
            padding-right: 35px;
            border-radius: 6px;
            border: 1px solid #d1d9e3;
            height: 38px;
            font-size: 14px;
            width: 250px;
        }

        .category-row {
            border-bottom: 1px solid #f0f3f7;
            transition: all 0.2s;
        }

        .category-row:last-child {
            border-bottom: none;
        }

        .category-trigger {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            cursor: pointer;
            user-select: none;
        }

        .category-trigger:hover {
            background-color: #fcfdfe;
        }

        .category-info {
            display: flex;
            align-items: center;
            flex-grow: 1;
            gap: 10px;
        }

        .category-name {
            font-weight: 600;
            color: #334155;
            font-size: 1rem;
        }

        .module-count-badge {
            color: #94a3b8;
            font-size: 0.8rem;
            font-weight: 400;
        }

        .selection-count-badge {
            background: #3b82f6;
            color: white;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-right: 15px;
            min-width: 25px;
            text-align: center;
        }

        .selection-count-badge.zero {
            background: #cbd5e1;
        }

        .arrow-icon {
            transition: transform 0.3s;
            color: #64748b;
        }

        .category-row.expanded .arrow-icon {
            transform: rotate(180deg);
        }

        .permissions-grid {
            padding: 0 20px 20px 20px;
            display: none;
        }

        .category-row.expanded .permissions-grid {
            display: block;
        }

        .module-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 10px;
        }

        .module-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .module-name {
            font-weight: 600;
            color: #475569;
            font-size: 0.9rem;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
        }

        .action-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .action-label {
            font-size: 0.8rem;
            color: #64748b;
            cursor: pointer;
            margin-bottom: 0;
        }

        .btn-outline-action {
            border: 1px solid #3b82f6;
            color: #3b82f6;
            background: white;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 13px;
            transition: all 0.2s;
        }

        .btn-outline-action:hover {
            background: #3b82f6;
            color: white;
        }

        .form-check-input {
            width: 1.1em;
            height: 1.1em;
            cursor: pointer;
        }
    </style>
@endpush
@section('head')
    Create Role Permission
@endsection

@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="card search_bar sales-report-card">
                <div class="sales-report-card-wrap">
                    <form action="{{ route('admin.roles.store') }}" method="POST">
                        @csrf
                        <div class="row justify-content-between">
                            <div class="col-md-4">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Role Name*</label>
                                        <input type="text" class="form-control" id="floatingInputValue" name="role_name"
                                            value="{{ old('role_name') }}" placeholder="Enter Role Name">
                                        @if ($errors->has('role_name'))
                                            <div class="error" style="color:red;">{{ $errors->first('role_name') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                @if (!empty($permissions))
                                    @php
                                        $categorizedModules = [
                                            'User Management' => [
                                                'My Profile',
                                                'My Password',
                                                'Admin List',
                                                'All Users',
                                                'Members Access',
                                            ],
                                            'Content Management' => [
                                                'Home Page',
                                                'Details Page',
                                                'Organizations Page',
                                                'About Us Page',
                                                'Principle and Business Page',
                                                'Article of Association Page',
                                                'Register Page Agreement Page',
                                                'Member Privacy Policy Page',
                                                'PMA Terms Page',
                                                'Footer',
                                                'Gallery',
                                                'Testimonials',
                                                'Faq',
                                            ],
                                            'Communication' => [
                                                'Contact Us Messages',
                                                'Newsletters',
                                                'Contact Us Page',
                                            ],
                                            'Governance' => [
                                                'Our Governance',
                                                'Our Organization',
                                                'Organization Center',
                                                'Services',
                                                'Ecclesia Association Page',
                                            ],
                                            'Contributions' => ['Donations'],
                                        ];

                                        $allModules = collect($categorizedModules)->flatten()->toArray();
                                        $availableModules = [];
                                        foreach ($modules as $m) {
                                            $availableModules[] = $m;
                                        }

                                        // Ensure any module not in categorization is put in 'Others'
                                        $otherModules = array_diff($availableModules, $allModules);
                                        if (!empty($otherModules)) {
                                            $categorizedModules['Others'] = array_values($otherModules);
                                        }
                                    @endphp

                                    <div class="permissions-card">
                                        <div class="permissions-header">
                                            <div class="form-check d-flex align-items-center mb-0">
                                                <input class="form-check-input" type="checkbox" id="checkAll">
                                                <label class="form-check-label ms-2 fw-medium text-muted" for="checkAll"
                                                    style="font-size: 14px;">
                                                    Select All Permissions
                                                </label>
                                            </div>

                                            <div class="search-wrapper">
                                                <div class="search-input-group">
                                                    <input type="text" id="permission-search" autocomplete="off"
                                                        placeholder="Search modules or actions">
                                                    <i class="fas fa-search position-absolute"
                                                        style="right: 12px; top: 12px; color: #cbd5e1;"></i>
                                                </div>
                                                <button type="button" class="btn-outline-action" id="collapse-all">Collapse
                                                    All</button>
                                                <button type="button" class="btn-outline-action" id="expand-all">Expand
                                                    All</button>
                                            </div>
                                        </div>

                                        <div class="permissions-body">
                                            @foreach ($categorizedModules as $category => $catModules)
                                                @php
                                                    $displayModules = array_intersect($catModules, $availableModules);
                                                @endphp
                                                @if (count($displayModules) > 0)
                                                    <div class="category-row" data-category="{{ strtolower($category) }}">
                                                        <div class="category-trigger">
                                                            <div class="form-check mb-0 me-3">
                                                                <input class="form-check-input select-category-permissions"
                                                                    type="checkbox"
                                                                    id="cat-check-{{ Str::slug($category) }}">
                                                            </div>
                                                            <div class="category-info">
                                                                <span class="category-name">{{ $category }}</span>
                                                                <span
                                                                    class="module-count-badge">({{ count($displayModules) }}
                                                                    modules)</span>
                                                            </div>
                                                            <span class="selection-count-badge zero">0</span>
                                                            <i class="fas fa-chevron-down arrow-icon"></i>
                                                        </div>
                                                        <div class="permissions-grid">
                                                            <div class="row">
                                                                @foreach ($displayModules as $module)
                                                                    <div class="col-md-6 mb-3 module-container"
                                                                        data-name="{{ strtolower($module) }}">
                                                                        <div class="module-item">
                                                                            <div class="module-header">
                                                                                <span
                                                                                    class="module-name">{{ $module }}</span>
                                                                                <div class="form-check mb-0">
                                                                                    <input
                                                                                        class="form-check-input select-module-all"
                                                                                        type="checkbox"
                                                                                        id="mod-{{ Str::slug($module) }}">
                                                                                    <label
                                                                                        class="form-check-label small text-muted ms-1"
                                                                                        for="mod-{{ Str::slug($module) }}">All</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="actions-grid">
                                                                                @foreach (['Manage', 'Create', 'Edit', 'Delete'] as $action)
                                                                                    @php
                                                                                        $permName =
                                                                                            $action . ' ' . $module;
                                                                                        $permId = array_search(
                                                                                            $permName,
                                                                                            $permissions,
                                                                                        );
                                                                                    @endphp
                                                                                    @if ($permId !== false)
                                                                                        <div class="action-item"
                                                                                            data-action="{{ strtolower($action) }}">
                                                                                            <div class="form-check mb-0">
                                                                                                <input
                                                                                                    class="form-check-input permission-checkbox"
                                                                                                    type="checkbox"
                                                                                                    name="permissions[]"
                                                                                                    value="{{ $permId }}"
                                                                                                    id="perm-{{ $permId }}"
                                                                                                    data-module="{{ Str::slug($module) }}">
                                                                                                <label class="action-label"
                                                                                                    for="perm-{{ $permId }}">{{ $action }}</label>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-danger" role="alert">
                                        No Permissions Found
                                    </div>
                                @endif
                                @if ($errors->has('permissions'))
                                    <span class="text-danger"
                                        style="color: red !important">{{ $errors->first('permissions') }}</span>
                                @endif
                            </div>
                        </div>
                </div>
                <div class="row d-flex justify-content-end">


                    <div class="btn-1 p-3">
                        <button type="submit">Create</button>
                        <a href="{{ route('admin.roles.index') }}"> <button type="button">Cancel</button></a>
                    </div>

                </div>
            </div>
            </form>
        </div>
    </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Function to update Selection Counts and All/Category checkbox states
            function updateSelectionStates() {
                var totalPerms = $('.permission-checkbox').length;
                var totalChecked = $('.permission-checkbox:checked').length;

                // Update Global Select All
                $('#checkAll').prop('checked', totalPerms > 0 && totalPerms === totalChecked);

                // Update Category-level states
                $('.category-row').each(function() {
                    var $row = $(this);
                    var $perms = $row.find('.permission-checkbox');
                    var $catCheck = $row.find('.select-category-permissions');
                    var $countBadge = $row.find('.selection-count-badge');

                    var catTotal = $perms.length;
                    var catChecked = $perms.filter(':checked').length;

                    $catCheck.prop('checked', catTotal > 0 && catTotal === catChecked);
                    $countBadge.text(catChecked);

                    if (catChecked > 0) {
                        $countBadge.removeClass('zero');
                    } else {
                        $countBadge.addClass('zero');
                    }

                    // Update Module-level states within this category
                    $row.find('.module-item').each(function() {
                        var $modItem = $(this);
                        var modTotal = $modItem.find('.permission-checkbox').length;
                        var modChecked = $modItem.find('.permission-checkbox:checked').length;
                        $modItem.find('.select-module-all').prop('checked', modTotal > 0 &&
                            modTotal === modChecked);
                    });
                });
            }

            // Init counts
            updateSelectionStates();

            // Toggle Accordion
            $(document).on('click', '.category-trigger', function(e) {
                if ($(e.target).closest('.form-check').length) return;
                $(this).closest('.category-row').toggleClass('expanded');
            });

            // Expand All
            $('#expand-all').click(function() {
                $('.category-row').addClass('expanded');
            });

            // Collapse All
            $('#collapse-all').click(function() {
                $('.category-row').removeClass('expanded');
            });

            // Global Select All
            $('#checkAll').change(function() {
                var isChecked = $(this).prop('checked');
                $('.permission-checkbox, .select-category-permissions, .select-module-all').prop('checked',
                    isChecked);
                updateSelectionStates();
            });

            // Category Select All
            $(document).on('change', '.select-category-permissions', function() {
                var isChecked = $(this).prop('checked');
                var $row = $(this).closest('.category-row');
                $row.find('.permission-checkbox, .select-module-all').prop('checked', isChecked);
                updateSelectionStates();
            });

            // Module Select All
            $(document).on('change', '.select-module-all', function() {
                var isChecked = $(this).prop('checked');
                $(this).closest('.module-item').find('.permission-checkbox').prop('checked', isChecked);
                updateSelectionStates();
            });

            // Individual Permission Change
            $(document).on('change', '.permission-checkbox', function() {
                updateSelectionStates();
            });

            // Search Logic
            $('#permission-search').on('keyup', function() {
                var val = $(this).val().toLowerCase();

                $('.category-row').each(function() {
                    var $row = $(this);
                    var catName = $row.data('category');
                    var hasVisibleModule = false;

                    $row.find('.module-container').each(function() {
                        var $modContainer = $(this);
                        var modName = $modContainer.data('name');
                        var hasVisibleAction = false;

                        // Check actions too
                        $modContainer.find('.action-item').each(function() {
                            var $actionItem = $(this);
                            var actionName = $actionItem.data('action');
                            if (actionName.includes(val) || modName.includes(val) ||
                                catName.includes(val)) {
                                $actionItem.show();
                                hasVisibleAction = true;
                            } else {
                                $actionItem.hide();
                            }
                        });

                        if (hasVisibleAction) {
                            $modContainer.show();
                            hasVisibleModule = true;
                        } else {
                            $modContainer.hide();
                        }
                    });

                    if (hasVisibleModule) {
                        $row.show();
                        if (val.length > 0) {
                            $row.addClass('expanded');
                        }
                    } else {
                        $row.hide();
                    }
                });

                if (val.length === 0) {
                    $('.category-row').removeClass('expanded');
                }
            });
        });
    </script>
@endpush
