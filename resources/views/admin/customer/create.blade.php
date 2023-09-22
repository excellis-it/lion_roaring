@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Create Customer
@endsection
@push('styles')
@endpush
@section('head')
    Create Customer
@endsection

@section('content')
    <div class="main-content">
        <div class="inner_page">
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
                                        <input type="text" class="form-control" id="floatingInputValue" name="password"
                                            value="{{ old('password') }}" placeholder="Password*">
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
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="confirm_password" value="{{ old('confirm_password') }}"
                                            placeholder="Confirm Password*">
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
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Full Name*</label>
                                    <input type="text" class="form-control" id="floatingInputValue" name="name"
                                        value="{{ old('name') }}" placeholder="Full Name*">
                                    @if ($errors->has('name'))
                                        <div class="error" style="color:red;">{{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">City</label>
                                    <input type="text" class="form-control" id="floatingInputValue" name="city"
                                        value="{{ old('city') }}" placeholder="City">
                                    @if ($errors->has('city'))
                                        <div class="error" style="color:red;">{{ $errors->first('city') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- country --}}
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group-div">
                                <div class="form-group">
                                    <label for="floatingInputValue">Country</label>
                                    <input type="text" class="form-control" id="floatingInputValue" name="country"
                                        value="{{ old('country') }}" placeholder="Country">
                                    @if ($errors->has('country'))
                                        <div class="error" style="color:red;">{{ $errors->first('country') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
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
                                    <label for="floatingInputValue">Pin Code</label>
                                    <input type="text" class="form-control" id="floatingInputValue" name="pincode"
                                        value="{{ old('pincode') }}" placeholder="Pin Code">
                                    @if ($errors->has('pincode'))
                                        <div class="error" style="color:red;">{{ $errors->first('pincode') }}</div>
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
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    @if ($errors->has('status'))
                                        <div class="error" style="color:red;">{{ $errors->first('status') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="btn-1">
                                <button type="submit">Create B2B User</button>
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
@endpush
