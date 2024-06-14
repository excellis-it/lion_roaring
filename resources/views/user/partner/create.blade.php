@extends('user.layouts.master')
@section('title')
    Create Partners - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('partners.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="heading_box mb-5">
                                            <h3>Login Information</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Email *</label>
                                            <input type="email" class="form-control" name="email"
                                                value="{{ old('email') }}" placeholder="">
                                            @if ($errors->has('email'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('email') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- phone --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Phone *</label>
                                            <input type="text" class="form-control" name="phone"
                                                value="{{ old('phone') }}" placeholder="">
                                            @if ($errors->has('phone'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('phone') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- password --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Password *</label>
                                            <input type="password" class="form-control" name="password"
                                                value="{{ old('password') }}" placeholder="">
                                            @if ($errors->has('password'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('password') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- confirm_password --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Confirm Password *</label>
                                            <input type="password" class="form-control" name="confirm_password"
                                                value="{{ old('confirm_password') }}" placeholder="">
                                            @if ($errors->has('confirm_password'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('confirm_password') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>


                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="heading_box mb-5">
                                            <h3>Personal Information</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    {{-- user_name --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>User Name *</label>
                                            <input type="text" class="form-control" name="user_name"
                                                value="{{ old('user_name') }}" placeholder="">
                                            @if ($errors->has('user_name'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('user_name') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Roles *</label>
                                            <select class="form-control" name="role">
                                                <option value="">Select Role</option>
                                                @foreach ($roles as $item)
                                                    <option value="{{ $item->name }}"
                                                        {{ old('role') == $item->name ? 'selected' : '' }}>
                                                        {{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('role'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('role') }}
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>First Name *</label>
                                            <input type="text" class="form-control" name="first_name"
                                                value="{{ old('first_name') }}" placeholder="">
                                            @if ($errors->has('first_name'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('first_name') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- middle_name --}}
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>Middle Name</label>
                                            <input type="text" class="form-control" name="middle_name"
                                                value="{{ old('middle_name') }}" placeholder="">
                                            @if ($errors->has('middle_name'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('middle_name') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- last_name --}}
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>Last Name *</label>
                                            <input type="text" class="form-control" name="last_name"
                                                value="{{ old('last_name') }}" placeholder="">
                                            @if ($errors->has('last_name'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('last_name') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-md-12 mb-2">
                                        <div class="box_label">
                                            <label>Address</label>
                                            <input type="text" class="form-control" name="address"
                                                value="{{ old('address') }}" placeholder="">
                                            @if ($errors->has('address'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('address') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>


                                </div>
                                <div class="w-100 text-end d-flex align-items-center justify-content-end">
                                    <button type="submit" class="print_btn me-2">Save</button>
                                    <a class="print_btn print_btn_vv" href="{{ route('partners.index') }}">Cancel</a>
                                </div>
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
