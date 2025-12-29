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
            <form action="{{ route('user.admin.menu.update') }}" method="POST">
                @csrf

                @foreach ($items as $type => $menuItems)
                    <div class="card shadow-sm border-0 mb-4">
                        {{-- Type Header --}}
                        <div class="card-header bg-light py-3 border-0">
                            <h5 class="mb-0 fw-semibold text-capitalize">
                                {{ $type ?? 'Other Menus' }}
                            </h5>
                        </div>

                        {{-- Menu Inputs --}}
                        <div class="card-body">
                            <div class="row g-4">
                                @foreach ($menuItems as $item)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-group">
                                            <label for="name_{{ $item->key }}" class="form-label fw-medium">
                                                {{ $item->default_name }}
                                            </label>

                                            <input type="text" id="name_{{ $item->key }}"
                                                name="names[{{ $item->key }}]"
                                                class="form-control @error('names.' . $item->key) is-invalid @enderror"
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
                        </div>
                    </div>
                @endforeach

                {{-- Submit Button --}}
                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4 py-2">
                        <i class="fa fa-save me-1"></i> Update Menus
                    </button>
                </div>
            </form>


        </div>
    </div>
    </div>
@endsection
