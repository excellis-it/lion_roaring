@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Edit Membership Tier
@endsection
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
                    <div class="col-md-6 mb-3">
                        <div class="box_label">
                            <label>Role</label>
                            <select name="role_id" class="form-control">
                                <option value="">-- select a role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ $tier->role_id == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
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
                            <div class="my-2"><button type="button" class="btn btn-success add-benefit">+ Add
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
