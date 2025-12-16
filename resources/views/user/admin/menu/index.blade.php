@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Menu Names
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-0">Menu Names</h3>
                    <p class="text-muted small mb-0">Menu Names</p>
                </div>
            </div>
            <form action="{{ route('user.admin.menu.update') }}" method="post">
                @csrf

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h4 class="mb-0 fw-semibold">Menu Names</h4>
                    </div>

                    <div class="card-body pt-0">
                        <div class="row g-4">
                            @foreach ($items as $item)
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label for="name_{{ $item->key }}" class="form-label fw-medium">
                                            {{ $item->default_name }}
                                        </label>

                                        <input type="text"
                                            class="form-control @error('names.' . $item->key) is-invalid @enderror"
                                            id="name_{{ $item->key }}" name="names[{{ $item->key }}]"
                                            value="{{ old('names.' . $item->key, $item->name ?? $item->default_name) }}"
                                            placeholder="Enter {{ strtolower($item->default_name) }}">

                                        @error('names.' . $item->key)
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary px-4 py-2">
                                <i class="fa fa-save me-1"></i> Update
                            </button>
                        </div>
                    </div>
                </div>

            </form>

        </div>
    </div>
    </div>
@endsection
