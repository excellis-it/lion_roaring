@extends('user.layouts.master')
@section('title')
    Add Market Material - {{ env('APP_NAME') }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row mb-3">
                <div class="col-md-10">
                    <h3 class="mb-3">Add Market Material</h3>
                </div>
                <div class="col-md-2 text-end">
                    <a href="{{ route('market-materials.index') }}" class="btn btn-secondary w-100">Back</a>
                </div>
            </div>

            <form action="{{ route('market-materials.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="box_label">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ old('name') }}" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="box_label">
                            <label for="code">Code (API) <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="code" class="form-control"
                                value="{{ old('code') }}" placeholder="XAG" required>
                            @error('code')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-2 mb-3">
                        <div class="box_label">
                            <label for="sort_order">Sort Order</label>
                            <input type="number" name="sort_order" id="sort_order" class="form-control"
                                value="{{ old('sort_order', 0) }}" min="0">
                            @error('sort_order')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-2 mb-3">
                        <div class="box_label">
                            <label for="is_active">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                    value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                            @error('is_active')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="w-100 text-end mt-3">
                    <button type="submit" class="print_btn me-2">Save</button>
                    <a href="{{ route('market-materials.index') }}" class="print_btn print_btn_vv">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
