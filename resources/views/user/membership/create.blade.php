@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Create Membership Tier
@endsection
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <form action="{{ route('user.membership.store') }}" method="post">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="heading_box mb-4">
                            <h3>Create Tier</h3>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="box_label">
                            <label>Tier Name *</label>
                            <input name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="box_label">
                            <label>Slug *</label>
                            <input name="slug" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="box_label">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="box_label">
                            <label>Plan Type *</label>
                            <select name="pricing_type" class="form-control" id="pricing_type" required>
                                <option value="amount" selected>Amount (USD)</option>
                                <option value="token">Life Force Energy (Token)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" id="amount_cost_wrap">
                        <div class="box_label">
                            <label>Amount (USD) *</label>
                            <input name="cost" class="form-control" type="number" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3 d-none" id="token_value_wrap">
                        <div class="box_label">
                            <label>Life Force Energy Tokens *</label>
                            <input name="life_force_energy_tokens" class="form-control" type="number" step="0.01"
                                min="0">
                        </div>
                    </div>
                    <div class="col-md-12 mb-3 d-none" id="agree_desc_wrap">
                        <div class="box_label">
                            <label>Agree Description *</label>
                            <textarea name="agree_description" class="form-control" rows="5"></textarea>
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="card border-0 shadow-sm" style="background: #f8f9fa; border-radius: 15px;">
                            <div
                                class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0 text-primary"><i class="fas fa-shield-alt me-2"></i> Permissions</h5>
                                    <small class="text-muted">Select the specific accesses for this membership tier</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="select-all-permissions"
                                        style="cursor: pointer; width: 2.5em; height: 1.25em;">
                                    <label class="form-check-label ms-2 fw-bold text-dark" for="select-all-permissions"
                                        style="cursor: pointer;">Select All</label>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    @foreach ($allPermissions as $permission)
                                        <div class="col-xl-3 col-lg-4 col-md-6">
                                            <div class="permission-item p-2 mb-2 rounded border bg-white shadow-sm h-100 d-flex align-items-center"
                                                style="cursor: pointer; transition: all 0.2s;">
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input permission-checkbox" type="checkbox"
                                                        name="permissions[]" value="{{ $permission->name }}"
                                                        id="perm-{{ $permission->id }}"
                                                        {{ is_array(old('permissions')) && in_array($permission->name, old('permissions')) ? 'checked' : '' }}
                                                        style="cursor: pointer; width: 1.2em; height: 1.2em;">
                                                    <label class="form-check-label ms-2" for="perm-{{ $permission->id }}"
                                                        style="cursor: pointer; font-size: 0.95rem; font-weight: 500;">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if ($errors->has('permissions'))
                                    <div class="error mt-3" style="color:red !important; font-weight: bold;">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $errors->first('permissions') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="box_label">
                            <label>Benefits</label>
                            <div id="benefits">
                                <div class="input-group mb-2">
                                    <input type="text" name="benefits[]" class="form-control">
                                    <button type="button" class="btn btn-success add-benefit">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="w-100 text-end">
                            <button type="submit" class="print_btn">Create</button>
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

        // Permissions logic
        function updateSelectAllState() {
            var total = $('.permission-checkbox').length;
            var checked = $('.permission-checkbox:checked').length;
            $('#select-all-permissions').prop('checked', total > 0 && total === checked);
        }

        $('#select-all-permissions').change(function() {
            $('.permission-checkbox').prop('checked', $(this).prop('checked'));
        });

        $(document).on('change', '.permission-checkbox', function() {
            updateSelectAllState();
        });

        $(document).on('click', '.permission-item', function(e) {
            if (!$(e.target).is('input') && !$(e.target).is('label')) {
                var checkbox = $(this).find('input[type="checkbox"]');
                checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
            }
        });

        $(document).ready(function() {
            updateSelectAllState();
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
