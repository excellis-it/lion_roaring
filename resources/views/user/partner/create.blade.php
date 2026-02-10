@extends('user.layouts.master')
@section('title')
    Create Partners - {{ env('APP_NAME') }}
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
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('partners.store') }}" method="POST" autocomplete="new" autofill="off"
                        id="uploadForm">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="heading_box mb-5">
                                            <h3>Login Information</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    {{-- user_name --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>User Name *</label>
                                            <input type="text" class="form-control" name="user_name"
                                                value="{{ old('user_name') }}" placeholder="" autocomplete="new-data">
                                            @if ($errors->has('user_name'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('user_name') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Email *</label>
                                            <input type="text" class="form-control" name="email"
                                                value="{{ old('email') }}" placeholder="">
                                            @if ($errors->has('email'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('email') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Lion Roaring ID *</label>
                                            <div class="input-group">
                                                <span class="input-group-text"
                                                    id="basic-addon1">{{ $generated_id_part }}</span>
                                                <input type="text" class="form-control" name="lion_roaring_id_suffix"
                                                    value="{{ old('lion_roaring_id_suffix') }}"
                                                    placeholder="Enter last 4 digits" maxlength="4">
                                            </div>
                                            @if ($errors->has('lion_roaring_id_suffix'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('lion_roaring_id_suffix') }}
                                                </div>
                                            @endif
                                            <input type="hidden" name="generated_id_part" value="{{ $generated_id_part }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Roar ID *</label>
                                            <input type="text" class="form-control" name="roar_id"
                                                value="{{ old('roar_id') }}" placeholder="">
                                            @if ($errors->has('roar_id'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('roar_id') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- user_type --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>User Type *</label>
                                            <select class="form-control" name="user_type">
                                                <option value="">Select User Type</option>
                                                @foreach ($allowedUserTypes as $type)
                                                    <option value="{{ $type }}"
                                                        {{ old('user_type') == $type ? 'selected' : '' }}>
                                                        {{ $type }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('user_type'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('user_type') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- phone --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Phone *</label>
                                            <input type="tel" class="form-control" name="phone" id="mobile_code"
                                                value="{{ old('full_phone_number') }}" placeholder="Enter Phone Number">
                                            @if ($errors->has('phone'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('phone') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- password --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label position-relative">
                                            <label>Password *</label>
                                            <input type="password" class="form-control" name="password" id="password"
                                                value="{{ old('password') }}" placeholder="" autocomplete="new-password">
                                            <span class="eye-btn-1" id="eye-button-1">
                                                <i class="fa fa-eye-slash" aria-hidden="true" id="togglePassword"></i>
                                            </span>
                                            @if ($errors->has('password'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('password') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- confirm_password --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label position-relative">
                                            <label>Confirm Password *</label>
                                            <input type="password" class="form-control" name="confirm_password"
                                                id="confirm_password" value="{{ old('confirm_password') }}"
                                                placeholder="">
                                            <span class="eye-btn-1" id="eye-button-2">
                                                <i class="fa fa-eye-slash" aria-hidden="true" id="togglePassword"></i>
                                            </span>
                                            @if ($errors->has('confirm_password'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('confirm_password') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>


                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="heading_box mb-5">
                                            <h3>Personal Information</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                    {{-- <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>Roles *</label>
                                            <select class="form-control" name="role">
                                                <option value="">Select Role</option>
                                                @foreach ($roles as $item)
                                                    <option value="{{ $item->name }}"
                                                        {{ old('role') == $item->name ? 'selected' : '' }}>
                                                        {{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('role'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('role') }}
                                                </div>
                                            @endif

                                        </div>
                                    </div> --}}
                                    {{-- eclessias --}}
                                    <div class="col-md-4 mb-2" id="ecclesia_main_input">
                                        <div class="box_label">
                                            <label>Ecclesias </label>
                                            <select class="form-control" name="ecclesia_id">
                                                <option value="">Select Ecclesia</option>
                                                @foreach ($eclessias as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('ecclesia_id') == $item->id ? 'selected' : '' }}>
                                                        {{ $item->name . '(' . $item->countryName->name . ')' ?? '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('ecclesia_id'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('ecclesia_id') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>First Name *</label>
                                            <input type="text" class="form-control" name="first_name"
                                                value="{{ old('first_name') }}" placeholder="">
                                            @if ($errors->has('first_name'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('first_name') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- middle_name --}}
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>Middle Name</label>
                                            <input type="text" class="form-control" name="middle_name"
                                                value="{{ old('middle_name') }}" placeholder="">
                                            @if ($errors->has('middle_name'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('middle_name') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- last_name --}}
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>Last Name *</label>
                                            <input type="text" class="form-control" name="last_name"
                                                value="{{ old('last_name') }}" placeholder="">
                                            @if ($errors->has('last_name'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('last_name') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- country --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Country *</label>
                                            <select name="country" id="country" class="form-control">
                                                <option value="">Select Country</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}"
                                                        @if (old('country') == $country->id) selected @endif
                                                        {{ $country->code == 'US' ? 'selected' : '' }}>
                                                        {{ $country->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('country'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('country') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- state --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>State *</label>
                                            <select name="state" id="state" class="form-control">
                                                <option value="">Select State</option>
                                            </select>
                                            @if ($errors->has('state'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('state') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- city --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>City *</label>
                                            <input type="text" class="form-control" name="city"
                                                value="{{ old('city') }}" placeholder="">
                                            @if ($errors->has('city'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('city') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Zip *</label>
                                            <input type="text" class="form-control" name="zip"
                                                value="{{ old('zip') }}" placeholder="">
                                            @if ($errors->has('zip'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('zip') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Address *</label>
                                            <input type="text" class="form-control" name="address"
                                                value="{{ old('address') }}" placeholder="">
                                            @if ($errors->has('address'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('address') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- address2 --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Address 2</label>
                                            <input type="text" class="form-control" name="address2"
                                                value="{{ old('address2') }}" placeholder="">
                                            @if ($errors->has('address2'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('address2') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- zip --}}

                                </div>


                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <h5>{{ App\Helpers\Helper::getMenuName('role_permission', 'Role Permission') }}*
                                            </h5>


                                            @foreach ($roles as $role)
                                                <div class="form-check form-check-inline">
                                                    <input id="data-roles-{{ $role->id }}"
                                                        class="form-check-input data-roles" type="radio" name="role"
                                                        value="{{ $role->name }}"
                                                        data-permissions="{{ json_encode($role->permissions->pluck('name')) }}"
                                                        data-isecclesia="{{ $role->is_ecclesia }}"
                                                        {{ old('role') == $role->name ? 'checked' : '' }} required>
                                                    <label class="form-check-label"
                                                        for="data-roles-{{ $role->id }}">{{ $role->name }}
                                                        <small>{{ $role->is_ecclesia == 1 ? '(ECCLESIA)' : '' }}</small></label>
                                                </div>
                                            @endforeach


                                        </div>
                                    </div>

                                </div>

                                @if ($errors->has('manage_ecclesia'))
                                    <div class="error" style="color:red !important;">
                                        * {{ $errors->first('manage_ecclesia') }}
                                    </div>
                                @endif

                                <div class="row mt-3" id="hoe_row" style="display: none">
                                    <div class="col-md-12">
                                        <div class="card border-0 shadow-sm"
                                            style="background: #fdfdfe; border-radius: 15px; border: 1px solid #e0e0e0 !important;">
                                            <div
                                                class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="mb-0 text-success"><i class="fas fa-home me-2"></i> House
                                                        Of ECCLESIA*</h5>
                                                    <small class="text-muted">Select the houses this user can
                                                        manage</small>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="select-all-ecclesias"
                                                        style="cursor: pointer; width: 2.5em; height: 1.25em;">
                                                    <label class="form-check-label ms-2 fw-bold text-dark"
                                                        for="select-all-ecclesias" style="cursor: pointer;">Select
                                                        All</label>
                                                </div>
                                            </div>
                                            <div class="card-body p-4">
                                                <div class="row g-3">
                                                    @foreach ($eclessias as $eclessia)
                                                        <div class="col-xl-3 col-lg-4 col-md-6">
                                                            <div class="ecclesia-item p-2 mb-2 rounded border bg-white shadow-sm h-100 d-flex align-items-center"
                                                                style="transition: all 0.2s;">
                                                                <div class="form-check mb-0">
                                                                    <input id="data-eclessia-{{ $eclessia->id }}"
                                                                        class="form-check-input data-eclessia"
                                                                        type="checkbox" name="manage_ecclesia[]"
                                                                        value="{{ $eclessia->id }}"
                                                                        {{ is_array(old('manage_ecclesia')) && in_array($eclessia->id, old('manage_ecclesia')) ? 'checked' : '' }}
                                                                        style="cursor: pointer; width: 1.25em; height: 1.25em;">
                                                                    <label class="form-check-label ms-2"
                                                                        for="data-eclessia-{{ $eclessia->id }}"
                                                                        style="cursor: pointer; font-size: 0.9rem;">
                                                                        {{ $eclessia->name }} <br>
                                                                        <small
                                                                            class="text-muted">{{ $eclessia->countryName->name }}</small>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Membership Tier Selection (Only for MEMBER_NON_SOVEREIGN) -->
                                <div class="row mt-4 d-none" id="membership-tier-section">
                                    <div class="col-md-12">
                                        <div class="card border-0 shadow-sm"
                                            style="background: #f8f9fa; border-radius: 15px;">
                                            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                                                <h5 class="mb-0 text-primary"><i class="fas fa-crown me-2"></i> Membership
                                                    Tier*</h5>
                                                <small class="text-muted">Select the membership plan for this
                                                    member</small>
                                            </div>
                                            <div class="card-body p-4">
                                                <div class="row g-3">
                                                    @foreach ($membershipTiers as $tier)
                                                        <div class="col-xl-4 col-md-6">
                                                            <div class="membership-item p-3 mb-2 rounded border bg-white shadow-sm h-100"
                                                                style="cursor: pointer; transition: all 0.2s;">
                                                                <div class="form-check position-relative h-100">
                                                                    <input class="form-check-input membership-radio"
                                                                        type="radio" name="membership_tier_id"
                                                                        value="{{ $tier->id }}"
                                                                        id="tier-{{ $tier->id }}"
                                                                        {{ old('membership_tier_id') == $tier->id ? 'checked' : '' }}
                                                                        style="cursor: pointer;">
                                                                    <label class="form-check-label ms-2 d-block"
                                                                        for="tier-{{ $tier->id }}"
                                                                        style="cursor: pointer;">
                                                                        <div class="fw-bold text-dark">{{ $tier->name }}
                                                                        </div>
                                                                        <div class="small text-muted">
                                                                            {{ $tier->pricing_type == 'token' ? $tier->life_force_energy_tokens . ' Tokens' : '$' . $tier->cost }}
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @if ($errors->has('membership_tier_id'))
                                                    <div class="error mt-3"
                                                        style="color:red !important; font-weight: bold;">
                                                        <i class="fas fa-exclamation-circle me-1"></i>
                                                        {{ $errors->first('membership_tier_id') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <div class="row mt-4" id="permissions-section">
                                    <div class="col-md-12">
                                        <div class="permissions-card">
                                            <div class="permissions-header">
                                                <div class="form-check d-flex align-items-center mb-0">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="select-all-permissions">
                                                    <label class="form-check-label ms-2 fw-medium text-muted"
                                                        for="select-all-permissions" style="font-size: 14px;">
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
                                                    <button type="button" class="btn-outline-action"
                                                        id="collapse-all">Collapse All</button>
                                                    <button type="button" class="btn-outline-action"
                                                        id="expand-all">Expand All</button>
                                                </div>
                                            </div>

                                            <div class="permissions-body">
                                                @foreach ($categorizedPermissions as $category => $permissions)
                                                    @php
                                                        $availablePermissions = array_intersect(
                                                            $permissions,
                                                            $allPermsArray,
                                                        );
                                                    @endphp
                                                    @if (count($availablePermissions) > 0)
                                                        <div class="category-row"
                                                            data-category="{{ strtolower($category) }}">
                                                            <div class="category-trigger">
                                                                <div class="form-check mb-0 me-3">
                                                                    <input
                                                                        class="form-check-input select-category-permissions"
                                                                        type="checkbox"
                                                                        id="cat-check-{{ Str::slug($category) }}">
                                                                </div>
                                                                <div class="category-info">
                                                                    <span class="category-name">{{ $category }}</span>
                                                                    <span
                                                                        class="perm-count-badge">({{ count($availablePermissions) }}
                                                                        perms)</span>
                                                                </div>
                                                                <span class="selection-count-badge zero">0</span>
                                                                <i class="fas fa-chevron-down arrow-icon"></i>
                                                            </div>
                                                            <div class="permissions-grid">
                                                                @foreach ($availablePermissions as $permName)
                                                                    @php
                                                                        $permId = $allPermissions
                                                                            ->where('name', $permName)
                                                                            ->first()->id;
                                                                    @endphp
                                                                    <div class="perm-item"
                                                                        data-name="{{ strtolower($permName) }}">
                                                                        <div class="form-check mb-0">
                                                                            <input
                                                                                class="form-check-input permission-checkbox"
                                                                                type="checkbox" name="permissions[]"
                                                                                value="{{ $permName }}"
                                                                                id="perm-{{ $permId }}"
                                                                                {{ is_array(old('permissions')) && in_array($permName, old('permissions')) ? 'checked' : '' }}>
                                                                            <label class="perm-label ms-1"
                                                                                for="perm-{{ $permId }}"
                                                                                title="{{ $permName }}">
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

                                        @if ($errors->has('permissions'))
                                            <div class="error mt-3" style="color:red !important; font-weight: bold;">
                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                {{ $errors->first('permissions') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="w-100 text-end d-flex align-items-center justify-content-end mt-4">
                                    <button type="submit" class="print_btn me-2">Save</button>
                                    <a class="print_btn print_btn_vv" href="{{ route('partners.index') }}">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>


                    {{-- <div class="card card-body shadow-lg mt-2">
                        <h5 class="mt-0" id="Role_Name"></h5>
                        <div class="row container mt-1" id="permissions-container">
                        </div>
                    </div> --}}


                </div>


            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $("#uploadForm").on("submit", function(e) {
                // e.preventDefault();
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#eye-button-1').click(function() {
                $('#password').attr('type', $('#password').is(':password') ? 'text' : 'password');
                $(this).find('i').toggleClass('fa-eye-slash fa-eye');
            });
            $('#eye-button-2').click(function() {
                $('#confirm_password').attr('type', $('#confirm_password').is(':password') ? 'text' :
                    'password');
                $(this).find('i').toggleClass('fa-eye-slash fa-eye');
            });
        });
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/css/intlTelInput.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/js/intlTelInput-jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/js/utils.min.js"></script>
    <script>
        function initializeIntlTelInput() {
            const phoneInput = $("#mobile_code");

            phoneInput.intlTelInput({
                geoIpLookup: function(callback) {
                    $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                        const countryCode = (resp && resp.country) ? resp.country : "US";
                        callback(countryCode);
                    });
                },
                initialCountry: "auto",
                separateDialCode: true,
            });

            const selectedCountry = phoneInput.intlTelInput('getSelectedCountryData');
            const dialCode = selectedCountry.dialCode;
            const exampleNumber = intlTelInputUtils.getExampleNumber(selectedCountry.iso2, 0, 0);

            let maskNumber = intlTelInputUtils.formatNumber(exampleNumber, selectedCountry.iso2, intlTelInputUtils
                .numberFormat.NATIONAL);
            maskNumber = maskNumber.replace('+' + dialCode + ' ', '');

            // Define the mask
            let mask;
            if (dialCode && dialCode.length > 2) {
                // Use a fixed mask pattern for countries with dial codes of length greater than 2
                mask = '999 999 999';
                maskNumber = '999 999 999';
            } else {
                // Dynamically create a mask by replacing digits with 0 for shorter dial codes
                mask = maskNumber.replace(/[0-9+]/g, '0');
            }

            // Apply the mask with the placeholder
            phoneInput.mask(mask, {
                placeholder: 'Enter Phone Number',
            });

            phoneInput.on('countrychange', function() {
                $(this).val(''); // Clear the input field when country changes
                const newSelectedCountry = $(this).intlTelInput('getSelectedCountryData');
                const newDialCode = newSelectedCountry.dialCode;
                const newExampleNumber = intlTelInputUtils.getExampleNumber(newSelectedCountry.iso2, 0, 0);

                let newMaskNumber = intlTelInputUtils.formatNumber(newExampleNumber, newSelectedCountry.iso2,
                    intlTelInputUtils.numberFormat.NATIONAL);
                newMaskNumber = newMaskNumber.replace('+' + newDialCode + ' ', '');

                let newMask;

                if (newDialCode.length > 2) {
                    // If dial code length is more than 2, use a 999 999 999 mask (or a similar format)
                    newMask = '999 999 999';
                    newMaskNumber = '999 999 999';
                } else {
                    // Otherwise, replace all digits with 0
                    newMask = newMaskNumber.replace(/[0-9+]/g, '0');
                }

                phoneInput.mask(newMask, {
                    placeholder: 'Enter Phone Number',
                });
            });
        }

        function setPhoneNumber() {
            const phoneInput = $("#mobile_code");
            const fullNumber = "{{ old('full_phone_number') }}";

            if (fullNumber) {
                phoneInput.intlTelInput("setNumber", fullNumber);
            }
        }

        $(document).ready(function() {
            initializeIntlTelInput();
            setPhoneNumber();

            $('form').on('submit', function() {
                const phoneInput = $("#mobile_code");
                const fullNumber = phoneInput.intlTelInput('getNumber');
                const countryCode = phoneInput.intlTelInput('getSelectedCountryData').dialCode;
                const countryData = phoneInput.intlTelInput('getSelectedCountryData');
                const countryCodeName = countryData.iso2;

                $('<input>').attr({
                    type: 'hidden',
                    name: 'full_phone_number',
                    value: fullNumber
                }).appendTo('form');

                $('<input>').attr({
                    type: 'hidden',
                    name: 'country_code',
                    value: countryCode
                }).appendTo('form');

                $('<input>').attr({
                    type: 'hidden',
                    name: 'phone_country_code_name',
                    value: countryCodeName
                }).appendTo('form');
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            getStates($('#country').val());
            getEcclesias();

            $('#country').change(function() {
                var country = $(this).val();
                getStates(country);
                getEcclesias();
            });

            $('select[name="user_type"]').change(function() {
                getEcclesias();
            });

            function getStates(country) {
                $.ajax({
                    url: "{{ route('get.states') }}",
                    type: "get",
                    data: {
                        country: country
                    },
                    success: function(response) {
                        var states = response;
                        var html = '<option value="">Select State</option>';
                        states.forEach(state => {
                            html += '<option value="' + state.id + '">' + state.name +
                                '</option>';
                        });
                        $('#state').html(html);
                    }
                });
            }

            function getEcclesias() {
                var country = $('#country').val();
                if (!userType) {
                    $('select[name="ecclesia_id"]').html('<option value="">Select Ecclesia</option>');
                    $('#hoe_row .row.g-3').html('');
                    return;
                }

                if (userType === 'Regional' && !country) {
                    $('select[name="ecclesia_id"]').html('<option value="">Select Ecclesia</option>');
                    $('#hoe_row .row.g-3').html('');
                    return;
                }

                // If Regional, we need a country to filter. If Global, we show all (country is null/empty for the request)
                var filterCountry = (userType === 'Regional') ? country : '';

                $.ajax({
                    url: "{{ route('get.ecclesias') }}",
                    type: "get",
                    data: {
                        country: filterCountry
                    },
                    success: function(response) {
                        // Update the single select dropdown
                        var selectHtml = '<option value="">Select Ecclesia</option>';
                        // Update the checkboxes in hoe_row
                        var checkboxHtml = '';

                        response.forEach(eclessia => {
                            // Dropdown
                            selectHtml += '<option value="' + eclessia.id + '">' +
                                eclessia.name + '(' + (eclessia.country_name ? eclessia
                                    .country_name.name : '') + ')' + '</option>';

                            // Checkboxes
                            checkboxHtml += '<div class="col-xl-3 col-lg-4 col-md-6">' +
                                '<div class="ecclesia-item p-2 mb-2 rounded border bg-white shadow-sm h-100 d-flex align-items-center" style="transition: all 0.2s;">' +
                                '<div class="form-check mb-0">' +
                                '<input id="data-eclessia-' + eclessia.id +
                                '" class="form-check-input data-eclessia" type="checkbox" name="manage_ecclesia[]" value="' +
                                eclessia.id +
                                '" style="cursor: pointer; width: 1.25em; height: 1.25em;">' +
                                '<label class="form-check-label ms-2" for="data-eclessia-' +
                                eclessia.id + '" style="cursor: pointer; font-size: 0.9rem;">' +
                                eclessia.name + '<br>' +
                                '<small class="text-muted">' + (eclessia.country_name ? eclessia
                                    .country_name.name : '') + '</small>' +
                                '</label>' +
                                '</div>' +
                                '</div>' +
                                '</div>';
                        });

                        $('select[name="ecclesia_id"]').html(selectHtml);
                        $('#hoe_row .row.g-3').html(checkboxHtml);

                        // Re-trigger count updates if necessary
                        if (typeof updateSelectAllEcclesiasState === 'function') {
                            updateSelectAllEcclesiasState();
                        }
                    }
                });
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            // Function to update "Select All" state for ecclesias
            function updateSelectAllEcclesiasState() {
                var total = $('.data-eclessia').length;
                var checked = $('.data-eclessia:checked').length;
                $('#select-all-ecclesias').prop('checked', total > 0 && total === checked);
            }

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

            // Select/Unselect All Toggle for Ecclesias
            $('#select-all-ecclesias').change(function() {
                $('.data-eclessia').prop('checked', $(this).prop('checked'));
            });

            // Individual Permission Click
            $(document).on('change', '.permission-checkbox', function() {
                updateSelectionStates();
            });

            // Individual Ecclesia Click
            $(document).on('change', '.data-eclessia', function() {
                updateSelectAllEcclesiasState();
            });

            // Make the entire card clickable
            $(document).on('click', '.permission-item, .ecclesia-item', function(e) {
                if (!$(e.target).is('input') && !$(e.target).is('label')) {
                    var checkbox = $(this).find('input[type="checkbox"]');
                    checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
                }
            });

            $(".data-roles").change(function(e) {
                e.preventDefault();
                var permissions = $(this).data('permissions');
                var is_ecclesia = $(this).data('isecclesia');

                if (is_ecclesia == 1) {
                    $("#hoe_row").show();
                    $("#ecclesia_main_input").hide();
                } else {
                    $("#hoe_row").hide();
                    $("#ecclesia_main_input").show();
                }

                // Uncheck all permissions first
                $('.permission-checkbox').prop('checked', false);

                // Check permissions corresponding to the selected role
                if (permissions && Array.isArray(permissions)) {
                    permissions.forEach(function(permName) {
                        $('.permission-checkbox[value="' + permName + '"]').prop('checked', true);
                    });
                }

                // Update Select All state after role change
                updateSelectionStates();
            });

            function togglePermissionsAndMembership() {
                var selectedRole = $('input[name="role"]:checked').val();
                if (selectedRole === 'MEMBER_NON_SOVEREIGN') {
                    $('#permissions-section').addClass('d-none');
                    $('#membership-tier-section').removeClass('d-none');
                    // Ensure at least one tier is selected if none is
                    if ($('input[name="membership_tier_id"]:checked').length === 0) {
                        $('input[name="membership_tier_id"]').first().prop('checked', true);
                    }
                } else {
                    $('#permissions-section').removeClass('d-none');
                    $('#membership-tier-section').addClass('d-none');
                }
            }

            $(document).on('change', 'input[name="role"]', function() {
                togglePermissionsAndMembership();
                var is_ecclesia = $(this).data(
                    'isecclesia'); // Corrected from 'is-ecclesia' to 'isecclesia'
                if (is_ecclesia == 1) {
                    $('#house-of-ecclesia-section').removeClass('d-none');
                } else {
                    $('#house-of-ecclesia-section').addClass('d-none');
                }
            });

            $(document).on('click', '.membership-item', function(e) {
                if (!$(e.target).is('input') && !$(e.target).is('label')) {
                    $(this).find('input[type="radio"]').prop('checked', true).trigger('change');
                }
            });

            // Initial calls
            updateSelectionStates();
            updateSelectAllEcclesiasState();
            togglePermissionsAndMembership(); // Call on initial load

            // Handle initial visibility for old role
            var checkedRole = $(".data-roles:checked");
            if (checkedRole.length > 0) {
                var is_ecclesia = checkedRole.data('isecclesia');
                if (is_ecclesia == 1) {
                    $("#hoe_row").show();
                    $("#ecclesia_main_input").hide();
                } else {
                    $("#hoe_row").hide();
                    $("#ecclesia_main_input").show();
                }
            }
        });
    </script>
    <style>
        .ecclesia-item:hover {
            border-color: #198754 !important;
            background-color: #f0fff4 !important;
            transform: translateY(-2px);
        }

        .data-eclessia:checked+.form-check-label {
            color: #198754;
            font-weight: 600;
        }
    </style>
@endpush
