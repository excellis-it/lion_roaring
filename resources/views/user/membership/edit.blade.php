@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Edit Membership Tier
@endsection
@push('styles')
    <style>
        .permissions-card {
            border: 1px solid #e0e6ed;
            border-radius: 8px;
            background: #fff;
            overflow: hidden;
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
            padding: 12px 20px;
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
            font-size: 0.95rem;
        }

        .perm-count-badge {
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
            padding: 0 20px 20px 60px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            display: none;
        }

        .category-row.expanded .permissions-grid {
            display: grid;
        }

        .perm-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .perm-label {
            font-size: 0.85rem;
            color: #475569;
            cursor: pointer;
            margin-bottom: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
            width: 1.15em;
            height: 1.15em;
            cursor: pointer;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <form action="{{ route('user.membership.update', $tier->id) }}" method="post">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="heading_box mb-4">
                            <h3>Edit Tier</h3>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="box_label">
                            <label>Tier Name *</label>
                            <input name="name" class="form-control" value="{{ $tier->name }}" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="box_label">
                            <label>Slug *</label>
                            <input name="slug" class="form-control" value="{{ $tier->slug }}" required>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="box_label">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $tier->description }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="box_label">
                            <label>Plan Type *</label>
                            <select name="pricing_type" class="form-control" id="pricing_type" required>
                                <option value="amount"
                                    {{ ($tier->pricing_type ?? 'amount') === 'amount' ? 'selected' : '' }}>Amount (USD)
                                </option>
                                <option value="token"
                                    {{ ($tier->pricing_type ?? 'amount') === 'token' ? 'selected' : '' }}>Life Force Energy
                                    (Token)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" id="amount_cost_wrap">
                        <div class="box_label">
                            <label>Amount (USD) *</label>
                            <input name="cost" class="form-control" value="{{ $tier->cost }}" type="number"
                                step="0.01" min="0">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3 d-none" id="token_value_wrap">
                        <div class="box_label">
                            <label>Life Force Energy Tokens *</label>
                            <input name="life_force_energy_tokens" class="form-control"
                                value="{{ $tier->life_force_energy_tokens }}" type="number" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="col-md-12 mb-3 d-none" id="agree_desc_wrap">
                        <div class="box_label">
                            <label>Agree Description *</label>
                            <textarea name="agree_description" class="form-control" rows="5">{{ $tier->agree_description }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="permissions-card">
                            <div class="permissions-header">
                                <div class="form-check d-flex align-items-center mb-0">
                                    <input class="form-check-input" type="checkbox" id="select-all-permissions">
                                    <label class="form-check-label ms-2 fw-medium text-muted" for="select-all-permissions"
                                        style="font-size: 14px;">
                                        Select All Permissions
                                    </label>
                                </div>

                                <div class="search-wrapper">
                                    <div class="search-input-group">
                                        <input type="text" id="permission-search" autocomplete="off"
                                            placeholder="Search modules or permissions">
                                        <i class="fas fa-search position-absolute"
                                            style="right: 12px; top: 12px; color: #cbd5e1;"></i>
                                    </div>
                                    <button type="button" class="btn-outline-action" id="collapse-all">Collapse
                                        All</button>
                                    <button type="button" class="btn-outline-action" id="expand-all">Expand All</button>
                                </div>
                            </div>

                            <div class="permissions-body p-4">
                                @foreach ($categorizedPermissions as $mainCategory => $subCategories)
                                    @php
                                        $mainCategoryPermCount = 0;
                                        foreach ($subCategories as $subPerms) {
                                            $mainCategoryPermCount += count(array_intersect($subPerms, $allPermsArray));
                                        }
                                    @endphp

                                    @if ($mainCategoryPermCount > 0)
                                        <div class="main-category-block mb-4 shadow-sm"
                                            style="border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;">
                                            <div class="main-category-header p-3 d-flex justify-content-between align-items-center"
                                                style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                                                <h5 class="mb-0"
                                                    style="color: #1e293b; font-size: 1.1rem; font-weight: 700;">
                                                    <i class="fas fa-layer-group me-2" style="color: #64748b;"></i>
                                                    {{ $mainCategory }}
                                                </h5>
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="badge bg-soft-info text-info px-3 py-2"
                                                        style="background-color: #e0f2fe; color: #0369a1; border-radius: 6px; font-size: 0.85rem;">
                                                        {{ $mainCategoryPermCount }} total permissions
                                                    </span>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-secondary toggle-main-category"
                                                        style="padding: 2px 8px;">
                                                        <i class="fas fa-chevron-up"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="main-category-content">
                                                @foreach ($subCategories as $subCategory => $permissions)
                                                    @php
                                                        $availablePermissions = array_intersect(
                                                            $permissions,
                                                            $allPermsArray,
                                                        );
                                                    @endphp
                                                    @if (count($availablePermissions) > 0)
                                                        <div class="category-row border-bottom"
                                                            data-category="{{ strtolower($subCategory) }}">
                                                            <div class="category-trigger py-3 px-4">
                                                                <div class="form-check mb-0 me-3">
                                                                    <input
                                                                        class="form-check-input select-category-permissions"
                                                                        type="checkbox"
                                                                        id="cat-check-{{ Str::slug($mainCategory . '-' . $subCategory) }}">
                                                                </div>
                                                                <div class="category-info">
                                                                    <span class="category-name"
                                                                        style="color: #334155; font-weight: 600;">{{ $subCategory }}</span>
                                                                    <span class="perm-count-badge ms-2"
                                                                        style="color: #64748b; font-size: 0.8rem;">({{ count($availablePermissions) }}
                                                                        perms)</span>
                                                                </div>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="selection-count-badge zero me-3">0</span>
                                                                    <i class="fas fa-chevron-down arrow-icon"
                                                                        style="color: #94a3b8;"></i>
                                                                </div>
                                                            </div>
                                                            <div class="permissions-grid px-5 pb-4">
                                                                @foreach ($availablePermissions as $permName)
                                                                    @php
                                                                        $perm = $allPermissions
                                                                            ->where('name', $permName)
                                                                            ->first();
                                                                        $permId = $perm ? $perm->id : 0;
                                                                    @endphp
                                                                    <div class="perm-item"
                                                                        data-name="{{ strtolower($permName) }}">
                                                                        <div class="form-check mb-0">
                                                                            <input
                                                                                class="form-check-input permission-checkbox"
                                                                                type="checkbox" name="permissions[]"
                                                                                value="{{ $permName }}"
                                                                                id="perm-{{ $permId }}"
                                                                                {{ (isset($currentPermissions) && in_array($permName, $currentPermissions)) || (is_array(old('permissions')) && in_array($permName, old('permissions'))) ? 'checked' : '' }}>
                                                                            <label class="perm-label ms-2"
                                                                                for="perm-{{ $permId }}"
                                                                                title="{{ $permName }}"
                                                                                style="font-size: 0.9rem; color: #475569;">
                                                                                {{ $permName }}
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        @if ($errors->has('permissions'))
                            <div class="error mt-3" style="color:red !important; font-weight: bold;">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                {{ $errors->first('permissions') }}
                            </div>
                        @endif
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="box_label">
                            <label>Benefits</label>
                            <div id="benefits">
                                @foreach ($tier->benefits as $benefit)
                                    <div class="input-group mb-2">
                                        <input type="text" name="benefits[]" class="form-control"
                                            value="{{ $benefit->benefit }}">
                                        <button type="button" class="btn btn-danger remove-benefit">-</button>
                                    </div>
                                @endforeach
                            </div>
                            <div class="my-2"><button type="button" class="btn btn-primary add-benefit">+ Add
                                    Benefit</button></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="w-100 text-end">
                            <button type="submit" class="print_btn">Update</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function syncMembershipPricingFields() {
            var type = ($('#pricing_type').val() || 'amount');
            if (type === 'token') {
                $('#amount_cost_wrap').addClass('d-none');
                $('#token_value_wrap').removeClass('d-none');
                $('#agree_desc_wrap').removeClass('d-none');
            } else {
                $('#amount_cost_wrap').removeClass('d-none');
                $('#token_value_wrap').addClass('d-none');
                $('#agree_desc_wrap').addClass('d-none');
            }
        }

        $(document).on('change', '#pricing_type', syncMembershipPricingFields);
        $(document).ready(syncMembershipPricingFields);

        $(document).on('click', '.add-benefit', function() {
            $('#benefits').append(
                '<div class="input-group mb-2"><input type="text" name="benefits[]" class="form-control"><button type="button" class="btn btn-danger remove-benefit">-</button></div>'
            );
        });
        $(document).on('click', '.remove-benefit', function() {
            $(this).closest('.input-group').remove();
        });

        // Accordion and Permissions logic
        $(document).ready(function() {
            // Function to update Selection Counts and All/Category checkbox states
            function updateSelectionStates() {
                var totalPerms = $('.permission-checkbox').length;
                var totalChecked = $('.permission-checkbox:checked').length;

                // Update Global Select All
                $('#select-all-permissions').prop('checked', totalPerms > 0 && totalPerms === totalChecked);

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
                });
            }

            // Init counts
            updateSelectionStates();

            // Toggle Main Category
            $(document).on('click', '.toggle-main-category', function() {
                var $content = $(this).closest('.main-category-block').find('.main-category-content');
                var $icon = $(this).find('i');
                $content.slideToggle();
                $icon.toggleClass('fa-chevron-up fa-chevron-down');
            });

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
            $('#select-all-permissions').change(function() {
                var isChecked = $(this).prop('checked');
                $('.permission-checkbox, .select-category-permissions').prop('checked', isChecked);
                updateSelectionStates();
            });

            // Category Select All
            $(document).on('change', '.select-category-permissions', function() {
                var isChecked = $(this).prop('checked');
                $(this).closest('.category-row')
                    .find('.permission-checkbox')
                    .prop('checked', isChecked);
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
                    var hasVisiblePerm = false;

                    $row.find('.perm-item').each(function() {
                        var $item = $(this);
                        var permName = $item.data('name');

                        if (permName.includes(val) || catName.includes(val)) {
                            $item.show();
                            hasVisiblePerm = true;
                        } else {
                            $item.hide();
                        }
                    });

                    if (hasVisiblePerm) {
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

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>
@endpush
