@extends('user.layouts.master')

@section('title')
    E-Store Promo Code Management
@endsection

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

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="name">Promo Code Name</label>
                                    <input type="text" name="code" id="name" class="form-control"
                                        value="{{ $promoCode->code }}">

                                </div>

                            </div>





                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="is_percentage">Percentage/Flat</label>
                                    <select name="is_percentage" id="is_percentage" class="form-control">
                                        <option value="1" {{ $promoCode->is_percentage == 1 ? 'selected' : '' }}>Percentage</option>
                                        <option value="0" {{ $promoCode->is_percentage == 0 ? 'selected' : '' }}>Flat</option>
                                    </select>

                                </div>

                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="discount">Discount <span id="discount-type"> ({{ $promoCode->is_percentage ? '%' : 'Flat' }})</span></label>
                                    <input type="number" step="any" name="discount_amount" id="discount" class="form-control"
                                        value="{{ $promoCode->discount_amount }}">

                                </div>

                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        value="{{ $promoCode->start_date }}">

                                </div>

                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="end_date">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control"
                                        value="{{ $promoCode->end_date }}">

                                </div>

                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1" {{ $promoCode->status == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0" {{ $promoCode->status == 0 ? 'selected' : '' }}>Inactive
                                        </option>
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
            $("#edit-promo-code-form").on("submit", function(e) {
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
