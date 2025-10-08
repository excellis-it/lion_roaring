@extends('user.layouts.master')

@section('title')
    E-Store Promo Code Management
@endsection
@push('styles')
   <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/css/bootstrap-select.min.css" />
    <style>
        .text-danger {
            color: red !important;
            font-size: 0.875em !important;
        }

        .is-invalid {
            border-color: red !important;
        }

        .dropdown-menu.show {
            z-index: 999999;
        }

        .bootstrap-select>.dropdown-toggle.bs-placeholder {
            border: 1px solid #ced4da;
            color: rgb(55, 54, 54);
        }

        .bootstrap-select .dropdown-menu li {
            padding: 5px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <div class="row">
                <div class="col-md-12">
                    <h4 class="title">Edit Promo Code</h4>
                    <form action="{{ route('store-promo-codes.update', $promoCode->id) }}" method="POST"
                        id="edit-promo-code-form">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            {{-- Promo Code Name --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="name">Promo Code Name</label>
                                    <input type="text" name="code" id="name"
                                        class="form-control @error('code') is-invalid @enderror"
                                        value="{{ old('code', $promoCode->code) }}">
                                    @error('code')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Applicability --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="scope_type">Applicability</label>
                                    @php
                                        $scopeType = old('scope_type', $promoCode->scope_type);
                                        $selectedUsers = collect(old('user_ids', $promoCode->user_ids ?? []))
                                            ->map(fn($id) => (int) $id)
                                            ->all();
                                        $selectedProducts = collect(old('product_ids', $promoCode->product_ids ?? []))
                                            ->map(fn($id) => (int) $id)
                                            ->all();
                                    @endphp
                                    <select name="scope_type" id="scope_type"
                                        class="form-control @error('scope_type') is-invalid @enderror">
                                        <option value="all" {{ $scopeType === 'all' ? 'selected' : '' }}>All Orders
                                        </option>
                                        <option value="all_users" {{ $scopeType === 'all_users' ? 'selected' : '' }}>All
                                            Users</option>
                                        <option value="selected_users"
                                            {{ $scopeType === 'selected_users' ? 'selected' : '' }}>Selected Users</option>
                                        <option value="all_products" {{ $scopeType === 'all_products' ? 'selected' : '' }}>
                                            All Products</option>
                                        <option value="selected_products"
                                            {{ $scopeType === 'selected_products' ? 'selected' : '' }}>Selected Products
                                        </option>
                                    </select>
                                    @error('scope_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Selected Users --}}
                            <div
                                class="col-md-6 mb-2 scope-field scope-users {{ $scopeType === 'selected_users' ? '' : 'd-none' }}">
                                <div class="box_label">
                                    <label for="user_ids">Select Users</label>
                                    <select name="user_ids[]" id="user_ids"
                                        class="form-control selectpicker @error('user_ids') is-invalid @enderror" multiple>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ in_array($user->id, $selectedUsers, true) ? 'selected' : '' }}>
                                                {{ trim($user->full_name) ?: $user->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_ids')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Selected Products --}}
                            <div
                                class="col-md-6 mb-2 scope-field scope-products {{ $scopeType === 'selected_products' ? '' : 'd-none' }}">
                                <div class="box_label">
                                    <label for="product_ids">Select Products</label>
                                    <select name="product_ids[]" id="product_ids"
                                        class="form-control selectpicker @error('product_ids') is-invalid @enderror" multiple>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ in_array($product->id, $selectedProducts, true) ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_ids')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Percentage / Flat --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="is_percentage">Percentage/Flat</label>
                                    <select name="is_percentage" id="is_percentage"
                                        class="form-control @error('is_percentage') is-invalid @enderror">
                                        <option value="1"
                                            {{ old('is_percentage', $promoCode->is_percentage) == 1 ? 'selected' : '' }}>
                                            Percentage</option>
                                        <option value="0"
                                            {{ old('is_percentage', $promoCode->is_percentage) == 0 ? 'selected' : '' }}>
                                            Flat</option>
                                    </select>
                                    @error('is_percentage')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Discount Amount --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="discount">Discount <span
                                            id="discount-type">({{ old('is_percentage', $promoCode->is_percentage) ? '%' : 'Flat' }})</span></label>
                                    <input type="number" step="any" name="discount_amount" id="discount"
                                        class="form-control @error('discount_amount') is-invalid @enderror"
                                        value="{{ old('discount_amount', $promoCode->discount_amount) }}">
                                    @error('discount_amount')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Start Date --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" name="start_date" id="start_date"
                                        class="form-control @error('start_date') is-invalid @enderror"
                                        value="{{ old('start_date', date('Y-m-d', strtotime($promoCode->start_date))) }}"
                                        min="{{ date('Y-m-d') }}">
                                    @error('start_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- End Date --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="end_date">End Date</label>
                                    <input type="date" name="end_date" id="end_date"
                                        class="form-control @error('end_date') is-invalid @enderror"
                                        value="{{ old('end_date', date('Y-m-d', strtotime($promoCode->end_date))) }}"
                                        min="{{ date('Y-m-d') }}">
                                    @error('end_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="status">Status</label>
                                    <select name="status" id="status"
                                        class="form-control @error('status') is-invalid @enderror">
                                        <option value="1"
                                            {{ old('status', $promoCode->status) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0"
                                            {{ old('status', $promoCode->status) == 0 ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                            <button type="submit" class="print_btn me-2">Save</button>
                            <a href="{{ route('store-promo-codes.index') }}" class="print_btn print_btn_vv">Cancel</a>
                        </div>
                    </form>
                </div>

            </div>



        </div>
    </div>
@endsection

@push('scripts')
   <!-- bootstrap-select (modern) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/js/bootstrap-select.min.js"></script>
    <script>
        $('.selectpicker').selectpicker();
    </script>
    <script>
        $(document).ready(function() {
            // Toggle Scope Fields
            const toggleScopeFields = () => {
                const scope = $('#scope_type').val();
                $('.scope-field').addClass('d-none');
                if (scope === 'selected_users') $('.scope-users').removeClass('d-none');
                if (scope === 'selected_products') $('.scope-products').removeClass('d-none');
            };
            toggleScopeFields();
            $("#scope_type").change(toggleScopeFields);

            // Discount Type Toggle
            $("#is_percentage").change(function() {
                $("#discount-type").text($(this).val() == 1 ? " (%)" : " (Flat)");
            });

            // Date Validation
            $("#start_date").change(function() {
                const startDate = $(this).val();
                if (startDate) {
                    $("#end_date").attr('min', startDate);
                    const endDate = $("#end_date").val();
                    if (endDate && endDate < startDate) $("#end_date").val('');
                }
            });
            $("#end_date").change(function() {
                const startDate = $("#start_date").val();
                if (startDate && $(this).val() < startDate) {
                    alert('End date must be after start date');
                    $(this).val('');
                }
            });

            // Loading indicator on submit
            $("#edit-promo-code-form").on("submit", function() {
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
            });
        });
    </script>
@endpush
