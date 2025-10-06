@extends('user.layouts.master')

@section('title')
    E-Store Promo Code Management
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <div class="row">
                <div class="col-md-12">
                    <h4 class="title mb-5">Create New Promo Code</h4>
                    <form action="{{ route('store-promo-codes.store') }}" method="POST" id="create-promo-code-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="name">Promo Code Name</label>
                                    <input type="text" name="code" id="name" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="scope_type">Applicability</label>
                                    <select name="scope_type" id="scope_type" class="form-control">
                                        @php $scopeType = old('scope_type', 'all'); @endphp
                                        <option value="all" {{ $scopeType === 'all' ? 'selected' : '' }}>All Orders
                                        </option>
                                        <option value="all_users" {{ $scopeType === 'all_users' ? 'selected' : '' }}>
                                            All Users
                                        </option>
                                        <option value="selected_users"
                                            {{ $scopeType === 'selected_users' ? 'selected' : '' }}>
                                            Selected Users
                                        </option>
                                        <option value="all_products" {{ $scopeType === 'all_products' ? 'selected' : '' }}>
                                            All Products
                                        </option>
                                        <option value="selected_products"
                                            {{ $scopeType === 'selected_products' ? 'selected' : '' }}>
                                            Selected Products
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2 scope-field scope-users d-none">
                                <div class="box_label">
                                    <label for="user_ids">Select Users</label>
                                    @php $selectedUsers = collect(old('user_ids', []))->map(fn ($id) => (int) $id)->all(); @endphp
                                    <select name="user_ids[]" id="user_ids" class="form-control" multiple>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ in_array($user->id, $selectedUsers, true) ? 'selected' : '' }}>
                                                {{ trim($user->full_name) ?: $user->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2 scope-field scope-products d-none">
                                <div class="box_label">
                                    <label for="product_ids">Select Products</label>
                                    @php $selectedProducts = collect(old('product_ids', []))->map(fn ($id) => (int) $id)->all(); @endphp
                                    <select name="product_ids[]" id="product_ids" class="form-control" multiple>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ in_array($product->id, $selectedProducts, true) ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="is_percentage">Percentage/Flat</label>
                                    <select name="is_percentage" id="is_percentage" class="form-control">
                                        <option value="1" selected>Percentage</option>
                                        <option value="0">Flat</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="discount">Discount <span id="discount-type"> (%)</span></label>
                                    <input type="number" step="any" name="discount_amount" id="discount"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="end_date">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

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
    <script>
        $(document).ready(function() {
            const toggleScopeFields = () => {
                const scope = $('#scope_type').val();
                $('.scope-field').addClass('d-none');
                if (scope === 'selected_users') {
                    $('.scope-users').removeClass('d-none');
                }
                if (scope === 'selected_products') {
                    $('.scope-products').removeClass('d-none');
                }
            };

            toggleScopeFields();
            $("#scope_type").change(toggleScopeFields);

            $("#create-promo-code-form").on("submit", function(e) {
                // e.preventDefault();
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
            });

            $("#is_percentage").change(function() {
                if ($(this).val() == 1) {
                    $("#discount-type").text(" (%)");
                } else {
                    $("#discount-type").text(" (Flat)");
                }
            });
        });
    </script>
@endpush
