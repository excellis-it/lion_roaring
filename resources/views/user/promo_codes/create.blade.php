@extends('user.layouts.master')

@section('title')
    {{ env('APP_NAME') }} | Create Promo Code
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <form action="{{ route('user.promo-codes.store') }}" method="POST">
                @csrf
                <div class="form-head">
                    <h4>Create Promo Code</h4>
                </div>

                <div class="row">
                    {{-- Code --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Promo Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" name="code"
                                value="{{ old('code') }}" placeholder="e.g., SAVE20">
                            @error('code')
                                <div class="test-danger" style="color:red;">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Use uppercase letters and numbers only</small>
                        </div>
                    </div>



                    {{-- Discount Type --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Discount Type <span class="text-danger">*</span></label>
                            <select class="form-control @error('is_percentage') is-invalid @enderror" id="discount_type"
                                name="is_percentage">
                                <option value="0" {{ old('is_percentage') == '0' ? 'selected' : '' }}>Fixed Amount ($)
                                </option>
                                <option value="1" {{ old('is_percentage') == '1' ? 'selected' : '' }}>Percentage (%)
                                </option>
                            </select>
                            @error('is_percentage')
                                <div class="test-danger" style="color:red;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Discount Amount --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Discount Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01"
                                class="form-control @error('discount_amount') is-invalid @enderror" name="discount_amount"
                                value="{{ old('discount_amount') }}" placeholder="e.g., 20">
                            @error('discount_amount')
                                <div class="test-danger" style="color:red;">{{ $message }}</div>
                            @enderror
                            <small class="text-muted" id="discount-hint">Enter the discount amount</small>
                        </div>
                    </div>

                    {{-- Scope --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Scope <span class="text-danger">*</span></label>
                            <select class="form-control @error('scope_type') is-invalid @enderror" id="scope_type"
                                name="scope_type">
                                <option value="all_tiers" {{ old('scope_type') == 'all_tiers' ? 'selected' : '' }}>All
                                    Membership Tiers</option>
                                <option value="selected_tiers"
                                    {{ old('scope_type') == 'selected_tiers' ? 'selected' : '' }}>Specific Tiers</option>
                                <option value="all_users" {{ old('scope_type') == 'all_users' ? 'selected' : '' }}>All
                                    Users</option>
                                <option value="selected_users"
                                    {{ old('scope_type') == 'selected_users' ? 'selected' : '' }}>Specific Users</option>
                            </select>
                            @error('scope_type')
                                <div class="test-danger" style="color:red;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Tier Selection --}}
                    <div class="col-md-12" id="tier-selection" style="display: none;">
                        <div class="form-group">
                            <label>Select Tiers</label>
                            <div class="row">
                                @foreach ($tiers as $tier)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="tier_ids[]"
                                                value="{{ $tier->id }}" id="tier{{ $tier->id }}"
                                                {{ in_array($tier->id, old('tier_ids', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tier{{ $tier->id }}">
                                                {{ $tier->name }} (${{ $tier->cost }})
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('tier_ids')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- User Selection --}}
                    <div class="col-md-12" id="user-selection" style="display: none;">
                        <div class="form-group">
                            <label>Select Users</label>
                            <select class="form-control @error('user_ids') is-invalid @enderror select2" name="user_ids[]"
                                multiple size="8">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ in_array($user->id, old('user_ids', [])) ? 'selected' : '' }}>
                                        {{ $user->first_name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_ids')
                                <div class="test-danger" style="color:red;">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Hold Ctrl/Cmd to select multiple users</small>
                        </div>
                    </div>

                    {{-- Start Date --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                name="start_date" value="{{ old('start_date') }}">
                            @error('start_date')
                                <div class="test-danger" style="color:red;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- End Date --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                name="end_date" value="{{ old('end_date') }}">
                            @error('end_date')
                                <div class="test-danger" style="color:red;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Usage Limit --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Maximum Total Uses</label>
                            <input type="number" class="form-control @error('usage_limit') is-invalid @enderror"
                                name="usage_limit" value="{{ old('usage_limit') }}"
                                placeholder="Leave empty for unlimited">
                            @error('usage_limit')
                                <div class="test-danger" style="color:red;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Per User Limit --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Maximum Uses Per User</label>
                            <input type="number" class="form-control @error('per_user_limit') is-invalid @enderror"
                                name="per_user_limit" value="{{ old('per_user_limit', 1) }}" placeholder="e.g., 1">
                            @error('per_user_limit')
                                <div class="test-danger" style="color:red;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="status" value="1"
                                    {{ old('status', true) ? 'checked' : '' }}>
                                <label class="form-check-label">Active</label>
                            </div>
                            <small class="text-muted">Inactive promo codes cannot be used</small>
                        </div>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check me-2"></i>Create Promo Code
                        </button>
                        <a href="{{ route('user.promo-codes.index') }}" class="btn btn-primary">
                            <i class="ti ti-x me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2 with proper configuration
            $('.select2').select2({
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });
            // Update discount hint based on type
            $('#discount_type').on('change', function() {
                const hint = $(this).val() === '1' ?
                    'Enter percentage (e.g., 20 for 20% off)' :
                    'Enter dollar amount (e.g., 10 for $10 off)';
                $('#discount-hint').text(hint);
            });

            // Show/hide tier and user selection based on scope
            $('#scope_type').on('change', function() {
                const scope = $(this).val();
                $('#tier-selection').toggle(scope === 'selected_tiers');
                $('#user-selection').toggle(scope === 'selected_users' || scope === 'all_users');
            });

            // Trigger on page load
            $('#scope_type').trigger('change');
            $('#discount_type').trigger('change');
        });
    </script>
@endpush
