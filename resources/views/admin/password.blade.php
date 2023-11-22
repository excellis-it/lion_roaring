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
                                            <input type="password" class="form-control" id="old_password"
                                                name="old_password" placeholder="Old Password*" value="{{ old('old_password') }}">
                                            <span class="eye-btn-1" id="eye-button-1">
                                                <i class="ph ph-eye-slash" aria-hidden="true" id="togglePassword"></i>
                                            </span>
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
                                            <input type="password" class="form-control" id="new_password"
                                                name="new_password" placeholder="New Password*" value="{{ old('new_password') }}">
                                            <span class="eye-btn-1" id="eye-button-2">
                                                <i class="ph ph-eye-slash" aria-hidden="true" id="togglePassword"></i>
                                            </span>
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
                                            <input type="password" class="form-control" id="confirm_password"
                                                name="confirm_password" placeholder="Confirm Password*" value="{{ old('confirm_password') }}">
                                            <span class="eye-btn-1" id="eye-button-3">
                                                <i class="ph ph-eye-slash" aria-hidden="true" id="togglePassword"></i>
                                            </span>
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
    <script>
        $(document).ready(function() {
            $('#eye-button-1').click(function() {
                $('#old_password').attr('type', $('#old_password').is(':password') ? 'text' : 'password');
                $(this).find('i').toggleClass('ph-eye-slash ph-eye');
            });
            $('#eye-button-2').click(function() {
                $('#new_password').attr('type', $('#new_password').is(':password') ? 'text' : 'password');
                $(this).find('i').toggleClass('ph-eye-slash ph-eye');
            });
            $('#eye-button-3').click(function() {
                $('#confirm_password').attr('type', $('#confirm_password').is(':password') ? 'text' : 'password');
                $(this).find('i').toggleClass('ph-eye-slash ph-eye');
            });
        });
    </script>
@endpush
