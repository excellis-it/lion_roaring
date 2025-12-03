@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Create Customer
@endsection
@push('styles')
@endpush


@section('content')
     <div class="container-fluid">
         <div class="bg_white_border">
            <div class="card search_bar sales-report-card">
                <div class="sales-report-card-wrap">
                    <div class="form-head">
                        <h4>Login Information</h4>
                    </div>
                    <form action="{{ route('customers.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row justify-content-between">
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Email Address*</label>
                                        <input type="text" class="form-control" id="floatingInputValue" name="email"
                                            value="{{ old('email') }}" placeholder="Email Address*">
                                        @if ($errors->has('email'))
                                            <div class="error" style="color:red;">{{ $errors->first('email') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Mobile*</label>
                                        <input type="text" class="form-control" id="floatingInputValue" name="phone"
                                            value="{{ old('phone') }}" placeholder="Mobile*">
                                        @if ($errors->has('phone'))
                                            <div class="error" style="color:red;">{{ $errors->first('phone') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Password*</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            value="{{ old('password') }}" placeholder="Password*">
                                            <span class="eye-btn-1" id="eye-button-1">
                                                <i class="ph ph-eye-slash" aria-hidden="true" id="togglePassword"></i>
                                            </span>
                                        @if ($errors->has('password'))
                                            <div class="error" style="color:red;">{{ $errors->first('password') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Confirm Password*</label>
                                        <input type="password" class="form-control" id="confirm_password"
                                            name="confirm_password" value="{{ old('confirm_password') }}"
                                            placeholder="Confirm Password*">
                                            <span class="eye-btn-1" id="eye-button-2">
                                                <i class="ph ph-eye-slash" aria-hidden="true" id="togglePassword"></i>
                                            </span>
                                        @if ($errors->has('confirm_password'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('confirm_password') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="sales-report-card-wrap mt-5">
                    <div class="form-head">
                        <h4>Personal Information</h4>
                    </div>

                    <div class="row">
                        {{-- user Name --}}
                        <div class="col-xl-12 col-md-12">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">User Name*</label>
                                    <input type="text" class="form-control" id="floatingInputValue" name="user_name"
                                        value="{{ old('user_name') }}" placeholder="User Name*">
                                    @if ($errors->has('user_name'))
                                        <div class="error" style="color:red;">{{ $errors->first('user_name') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-4">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">First Name*</label>
                                    <input type="text" class="form-control" id="floatingInputValue" name="first_name"
                                        value="{{ old('first_name') }}" placeholder="First Name*">
                                    @if ($errors->has('first_name'))
                                        <div class="error" style="color:red;">{{ $errors->first('first_name') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- middle Name --}}
                        <div class="col-xl-4 col-md-4">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Middle Name</label>
                                    <input type="text" class="form-control" id="floatingInputValue" name="middle_name"
                                        value="{{ old('middle_name') }}" placeholder="Middle Name">
                                    @if ($errors->has('middle_name'))
                                        <div class="error" style="color:red;">{{ $errors->first('middle_name') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- last Name --}}
                        <div class="col-xl-4 col-md-4">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Last Name*</label>
                                    <input type="text" class="form-control" id="floatingInputValue" name="last_name"
                                        value="{{ old('last_name') }}" placeholder="Last Name*">
                                    @if ($errors->has('last_name'))
                                        <div class="error" style="color:red;">{{ $errors->first('last_name') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Address*</label>
                                    <input type="text" class="form-control" id="floatingInputValue" name="address"
                                        value="{{ old('address') }}" placeholder="Address*">
                                    @if ($errors->has('address'))
                                        <div class="error" style="color:red;">{{ $errors->first('address') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Status*</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Select Status</option>
                                        <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @if ($errors->has('status'))
                                        <div class="error" style="color:red;">{{ $errors->first('status') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="btn-1">
                                <button type="submit">Create</button>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#eye-button-1').click(function() {
                $('#password').attr('type', $('#password').is(':password') ? 'text' : 'password');
                $(this).find('i').toggleClass('ph-eye-slash ph-eye');
            });
            $('#eye-button-2').click(function() {
                $('#confirm_password').attr('type', $('#confirm_password').is(':password') ? 'text' : 'password');
                $(this).find('i').toggleClass('ph-eye-slash ph-eye');
            });
        });
    </script>
@endpush
