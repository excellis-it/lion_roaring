@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Profile
@endsection
@push('styles')
@endpush
@section('head')
    Change Password
@endsection

@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card search_bar sales-report-card">
                        <form action="{{ route('admin.password.update') }}" method="post">
                            @csrf
                            <div class="row justify-content-between">
                                <div class="col-xl-12">
                                    <div class="form-group-div">
                                        <div class="form-group">
                                            <label for="floatingInputValue">Old Password*</label>
                                            <input type="text" class="form-control" id="floatingInputValue"
                                                name="old_password" placeholder="Old Password*">
                                            @if ($errors->has('old_password'))
                                                <div class="error" style="color:red;">{{ $errors->first('old_password') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="form-group-div">
                                        <div class="form-group">
                                            <label for="floatingInputValue">New Password*</label>
                                            <input type="text" class="form-control" id="floatingInputValue"
                                                name="new_password" placeholder="New Password*">
                                            @if ($errors->has('new_password'))
                                                <div class="error" style="color:red;">{{ $errors->first('new_password') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="form-group-div">
                                        <div class="form-group">
                                            <label for="floatingInputValue">Confirm Password*</label>
                                            <input type="text" class="form-control" id="floatingInputValue"
                                                name="confirm_password" placeholder="Confirm Password*">
                                            @if ($errors->has('confirm_password'))
                                                <div class="error" style="color:red;">
                                                    {{ $errors->first('confirm_password') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12 text-center">
                                    <div class="btn-1">
                                        <button type="submit">Change password</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
@endpush
