@extends('user.layouts.master')
@section('title')
    Save Order Status - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('order-status.store') }}" method="POST" id="orderStatusForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Save Order Status</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            {{-- Name --}}
                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="name" class="form-label">Status Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        value="{{ old('name') }}">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <input type="hidden" name="is_pickup"
                                value="{{ old('is_pickup', !empty($isPickupParam) && $isPickupParam ? 1 : 0) }}">

                            @if (!empty($isPickupParam) && $isPickupParam)
                                <div class="col-md-6 mb-3">
                                    <span class="badge bg-secondary">Pickup Status</span>
                                </div>
                            @endif

                            {{-- Sort Order --}}
                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number" name="sort_order" id="sort_order" class="form-control"
                                        value="{{ old('sort_order', 0) }}">
                                    @error('sort_order')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Is Active --}}
                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="is_active" class="form-label">Status</label>
                                    <select name="is_active" id="is_active" class="form-select">
                                        <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0" {{ old('is_active', 1) == 0 ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                    @error('is_active')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="w-100 text-end mt-3">
                            <button type="submit" class="btn btn-primary me-2">Save</button>
                            <a href="{{ route('order-status.index') }}" class="btn btn-primary">Cancel</a>
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
            $("#orderStatusForm").on("submit", function(e) {
                // e.preventDefault();
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
            });
        });
    </script>
@endpush
