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
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="shipping_cost">Shipping Cost</label>
                                    <input type="number" step="any" name="shipping_cost" id="shipping_cost"
                                        class="form-control" value="{{ $storeSetting->shipping_cost ?? '' }}">
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="delivery_cost">Delivery Cost</label>
                                    <input type="number" step="any" name="delivery_cost" id="delivery_cost"
                                        class="form-control" value="{{ $storeSetting->delivery_cost ?? '' }}">
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="tax_percentage">Tax Percentage (%)</label>
                                    <input type="number" step="any" name="tax_percentage" id="tax_percentage"
                                        class="form-control" value="{{ $storeSetting->tax_percentage ?? '' }}">
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="is_pickup_available">Pickup Available</label>
                                    <select name="is_pickup_available" id="is_pickup_available" class="form-control">
                                        <option value="1" {{ $storeSetting->is_pickup_available ? 'selected' : '' }}>
                                            Yes</option>
                                        <option value="0" {{ !$storeSetting->is_pickup_available ? 'selected' : '' }}>
                                            No</option>
                                    </select>
                                </div>
                            </div>

                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Save</button>
                                <a href="{{ route('store-settings.index') }}" class="print_btn print_btn_vv">Cancel</a>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
