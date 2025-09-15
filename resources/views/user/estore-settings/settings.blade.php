@extends('user.layouts.master')

@section('title')
    E-Store Settings Management
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <div class="row">
                <div class="col-md-12">
                    <h4 class="mb-4">E-Store Settings</h4>
                    <form action="{{ route('store-settings.update', $storeSetting->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Shipping Cost -->
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="shipping_cost">Shipping Cost</label>
                                    <input type="number" step="any" name="shipping_cost" id="shipping_cost"
                                        class="form-control @error('shipping_cost') is-invalid @enderror"
                                        value="{{ old('shipping_cost', $storeSetting->shipping_cost) }}">
                                    @error('shipping_cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Delivery Cost -->
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="delivery_cost">Delivery Cost</label>
                                    <input type="number" step="any" name="delivery_cost" id="delivery_cost"
                                        class="form-control @error('delivery_cost') is-invalid @enderror"
                                        value="{{ old('delivery_cost', $storeSetting->delivery_cost) }}">
                                    @error('delivery_cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tax Percentage -->
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="tax_percentage">Tax Percentage (%)</label>
                                    <input type="number" step="any" name="tax_percentage" id="tax_percentage"
                                        class="form-control @error('tax_percentage') is-invalid @enderror"
                                        value="{{ old('tax_percentage', $storeSetting->tax_percentage) }}">
                                    @error('tax_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Pickup Available -->
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="is_pickup_available">Pickup Available</label>
                                    <select name="is_pickup_available" id="is_pickup_available"
                                        class="form-control @error('is_pickup_available') is-invalid @enderror">
                                        <option value="1"
                                            {{ old('is_pickup_available', $storeSetting->is_pickup_available) ? 'selected' : '' }}>
                                            Yes</option>
                                        <option value="0"
                                            {{ !old('is_pickup_available', $storeSetting->is_pickup_available) ? 'selected' : '' }}>
                                            No</option>
                                    </select>
                                    @error('is_pickup_available')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Credit Card Percentage -->
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="credit_card_percentage">Credit Card Percentage (%)</label>
                                    <input type="number" step="any" name="credit_card_percentage"
                                        id="credit_card_percentage"
                                        class="form-control @error('credit_card_percentage') is-invalid @enderror"
                                        value="{{ old('credit_card_percentage', $storeSetting->credit_card_percentage) }}">
                                    @error('credit_card_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Save</button>
                                <a href="{{ route('store-settings.index') }}" class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
