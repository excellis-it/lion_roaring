@extends('user.layouts.master')

@section('title')
    Create Warehouse Administrator
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="title mb-4">Create New Warehouse Administrator</h4>

                    {{-- @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif --}}

                    <form action="{{ route('warehouse-admins.store') }}" method="POST" id="create-admin-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" id="first_name" class="form-control"
                                        value="{{ old('first_name') }}">
                                    @if ($errors->has('first_name'))
                                        <span class="error">{{ $errors->first('first_name') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" id="last_name" class="form-control"
                                        value="{{ old('last_name') }}">
                                    @if ($errors->has('last_name'))
                                        <span class="error">{{ $errors->first('last_name') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="user_name">Username <span class="text-danger">*</span></label>
                                    <input type="text" name="user_name" id="user_name" class="form-control"
                                        value="{{ old('user_name') }}">
                                    @if ($errors->has('user_name'))
                                        <span class="error">{{ $errors->first('user_name') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control"
                                        value="{{ old('email') }}">
                                    @if ($errors->has('email'))
                                        <span class="error">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="phone">Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" id="phone" class="form-control"
                                        value="{{ old('phone') }}">
                                    @if ($errors->has('phone'))
                                        <span class="error">{{ $errors->first('phone') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="password">Password <span class="text-danger">*</span></label>
                                    <input autocomplete="new-password" type="password" name="password" id="password"
                                        class="form-control">
                                    <small class="text-muted">Minimum 8 characters</small>
                                    @if ($errors->has('password'))
                                        <span class="error">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="box_label">
                                    <label for="password_confirmation">Confirm Password <span
                                            class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control">
                                    @if ($errors->has('password_confirmation'))
                                        <span class="error">{{ $errors->first('password_confirmation') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="">
                                    <label class="mb-3">Assign Warehouses <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="mb-3">
                                        <div class="mb-5">
                                            @foreach ($warehouses as $warehouse)
                                                <div class="mb-2">
                                                    <div class="form-check">
                                                        <label class="form-check-label"
                                                            for="warehouse{{ $warehouse->id }}">
                                                            {{ $warehouse->name }}
                                                        </label>
                                                        <input class="form-check-input" type="checkbox" name="warehouses[]"
                                                            value="{{ $warehouse->id }}"
                                                            id="warehouse{{ $warehouse->id }}"
                                                            {{ in_array($warehouse->id, old('warehouses', [])) ? 'checked' : '' }}>

                                                    </div>


                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-100 text-end d-flex align-items-center justify-content-end mt-4">
                            <button type="submit" class="print_btn me-2">Create Admin</button>
                            <a href="{{ route('warehouse-admins.index') }}" class="print_btn print_btn_vv">Cancel</a>
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
            $("#create-admin-form").on("submit", function(e) {
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
            });
        });
    </script>
@endpush
