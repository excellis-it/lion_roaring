@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Edit B2B User Details
@endsection
@push('styles')
@endpush
@section('head')
    Edit B2B User Details
@endsection
@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="card search_bar sales-report-card">
                <div class="sales-report-card-wrap">
                    <div class="form-head">
                        <h4>Login Information</h4>
                    </div>
                    <form action="{{ route('sellers.update', $seller->id) }}" method="POST" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="row justify-content-between">
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Email Address*</label>
                                        <input type="text" class="form-control" id="floatingInputValue" name="email" value="{{ $seller->email }}"
                                            placeholder="Email Address*">
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
                                        <input type="text" class="form-control" id="floatingInputValue" name="phone" value="{{ $seller->phone }}"
                                            placeholder="Mobile*">
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
                                        <input type="text" class="form-control" id="floatingInputValue" name="password"
                                            placeholder="Password">
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
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="confirm_password" placeholder="Confirm Password">
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
                                    <input type="text" class="form-control" id="floatingInputValue" name="name" value="{{ $seller->name }}"
                                        placeholder="Full Name*">
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
                                    <input type="text" class="form-control" id="floatingInputValue" name="city" value="{{ $seller->city }}"
                                        placeholder="City">
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
                                    <input type="text" class="form-control" id="floatingInputValue" name="country" value="{{ $seller->country }}"
                                        placeholder="Country">
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
                                    <input type="text" class="form-control" id="floatingInputValue" name="address" value="{{ $seller->address }}"
                                        placeholder="Address*">
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
                                    <input type="text" class="form-control" id="floatingInputValue" name="pincode" value="{{ $seller->pincode }}"
                                        placeholder="Pin Code">
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
                                        <option value="1" {{($seller->status == 1) ? 'selected' : ''}}>Active</option>
                                        <option value="0" {{($seller->status == 0) ? 'selected' : ''}}>Inactive</option>
                                    </select>
                                    @if ($errors->has('status'))
                                        <div class="error" style="color:red;">{{ $errors->first('status') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="btn-1">
                                <button type="submit">Update User</button>
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
