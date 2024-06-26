@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Profile
@endsection
@push('styles')
@endpush
@section('head')
    Profile
@endsection
@section('content')
<div class="main-content">
    <div class="inner_page">
        <div class="card search_bar sales-report-card">
            <form action="{{ route('admin.profile.update') }}"
            method="post">
            @csrf
                <div class="row justify-content-between">
                    <div class="col-xl-3 col-md-6">
                        <div class="form-group-div">
                            <div class="form-group">
                                <label for="floatingInputValue">First Name</label>
                                <input type="text" class="form-control" id="floatingInputValue" name="first_name" value="{{ Auth::user()->first_name }}"
                                    placeholder="First Name" >
                                @if ($errors->has('first_name'))
                                    <div class="error" style="color:red;">{{ $errors->first('first_name') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="form-group-div">
                            <div class="form-group">
                                <label for="floatingInputValue">Last Name</label>
                                <input type="text" class="form-control" id="floatingInputValue" name="last_name" value="{{ Auth::user()->last_name }}"
                                    placeholder="Last Name" >
                                @if ($errors->has('last_name'))
                                    <div class="error" style="color:red;">{{ $errors->first('last_name') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="form-group-div">
                            <div class="form-group">
                                <label for="floatingInputValue">Phone Number</label>
                                <input type="text" class="form-control" id="floatingInputValue" name="phone_number" value="{{ Auth::user()->phone }}"
                                    placeholder="Phone Number" value="Phone Number">
                                @if ($errors->has('phone_number'))
                                    <div class="error" style="color:red;">{{ $errors->first('phone_number') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="form-group-div">
                            <div class="form-group">
                                <label for="floatingInputValue">Email ID</label>
                                <input type="text" class="form-control" id="floatingInputValue" name="email" value="{{ Auth::user()->email }}"
                                    placeholder="Email ID" value="Email ID">
                                @if ($errors->has('email'))
                                    <div class="error" style="color:red;">{{ $errors->first('email') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="btn-1">
                            <button type="submit">Update</button>
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
