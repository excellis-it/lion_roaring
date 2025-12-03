@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Edit Customer Details
@endsection
@push('styles')
@endpush
@section('head')
    Edit Customer Details
@endsection
@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="card search_bar sales-report-card">
                <div class="sales-report-card-wrap">
                    <div class="form-head">
                        <h4>Login Information</h4>
                    </div>
                    <form action="{{ route('customers.update', $customer->id) }}" method="POST" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="row justify-content-between">
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Email Address*</label>
                                        <input type="text" class="form-control" id="floatingInputValue" name="email"
                                            value="{{ $customer->email }}" placeholder="Email Address*">
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
                                            value="{{ $customer->phone }}" placeholder="Mobile*">
                                        @if ($errors->has('phone'))
                                            <div class="error" style="color:red;">{{ $errors->first('phone') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Password</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Password">
                                        <span class="eye-btn-1" id="eye-button-1">
                                            <i class="ph ph-eye" aria-hidden="true" id="togglePassword"></i>
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
                                        <label for="floatingInputValue">Confirm Password</label>
                                        <input type="password" class="form-control" id="confirm_password"
                                            name="confirm_password" placeholder="Confirm Password">
                                        <span class="eye-btn-1" id="eye-button-2">
                                            <i class="ph ph-eye" aria-hidden="true" id="togglePassword"></i>
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
                        <div class="col-xl-12 col-md-12">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">User Name*</label>
                                    <input type="text" class="form-control" id="floatingInputValue" name="user_name"
                                        value="{{ ($customer->user_name) ? $customer->user_name : old('user_name') }}" placeholder="User Name*">
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
                                        value="{{($customer->first_name) ? $customer->first_name : old('first_name') }}" placeholder="First Name*" >
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
                                        value="{{ ($customer->middle_name) ? $customer->middle_name : old('middle_name') }}" placeholder="Middle Name">
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
                                        value="{{ ($customer->last_name) ? $customer->last_name : old('last_name') }}" placeholder="Last Name*">
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
                                        value="{{ $customer->address }}" placeholder="Address*">
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
                                        <option value="1" {{ $customer->status == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0" {{ $customer->status == 0 ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                    @if ($errors->has('status'))
                                        <div class="error" style="color:red;">{{ $errors->first('status') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="btn-1">
                                <button type="submit">Update</button>
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
                $('#confirm_password').attr('type', $('#confirm_password').is(':password') ? 'text' :
                    'password');
                $(this).find('i').toggleClass('ph-eye-slash ph-eye');
            });
        });
    </script>
@endpush
