@extends('user.layouts.master')
@section('title')
    Dashboard - {{ env('APP_NAME') }} user change password
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <form action="{{route('user.password.update')}}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card w-100">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="heading_box mb-5">
                                            <h3>Change Password</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>Old Password*</label>
                                            <input type="password" class="form-control" id="old_password"
                                            name="old_password" placeholder="Old Password" value="{{ old('old_password') }}" required>
                                                <span class="eye-btn-1" id="eye-button-1">
                                                    <i class="fa fa-eye-slash" aria-hidden="true" id="togglePassword"></i>
                                                </span>
                                            @if ($errors->has('old_password'))
                                                <div class="error" style="color:red;">{{ $errors->first('old_password') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>New Password*</label>
                                            <input type="password" class="form-control" id="new_password"
                                                name="new_password" placeholder="New Password"
                                                value="{{ old('new_password') }}" required>
                                                <span class="eye-btn-1" id="eye-button-2">
                                                    <i class="fa fa-eye-slash" aria-hidden="true" id="togglePassword"></i>
                                                </span>
                                            @if ($errors->has('new_password'))
                                                <div class="error" style="color:red;">{{ $errors->first('new_password') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>Confirm Password*</label>
                                            <input type="password" class="form-control" id="confirm_password"
                                                name="confirm_password" placeholder="Confirm Password"
                                                value="{{ old('confirm_password') }}" required>
                                                <span class="eye-btn-1" id="eye-button-3">
                                                    <i class="fa fa-eye-slash" aria-hidden="true" id="togglePassword"></i>
                                                </span>
                                            @if ($errors->has('confirm_password'))
                                                <div class="error" style="color:red;">
                                                    {{ $errors->first('confirm_password') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="w-100 text-end d-flex align-items-center justify-content-end">
                                    <button type="submit" class="print_btn me-2">Update</button>
                                    <a href="{{ route('user.profile') }}" class="print_btn print_btn_vv"
                                    >Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#eye-button-1').click(function() {
            $('#old_password').attr('type', $('#old_password').is(':password') ? 'text' : 'password');
            $(this).find('i').toggleClass('fa-eye-slash fa-eye');
        });
        $('#eye-button-2').click(function() {
            $('#new_password').attr('type', $('#new_password').is(':password') ? 'text' : 'password');
            $(this).find('i').toggleClass('fa-eye-slash fa-eye');
        });
        $('#eye-button-3').click(function() {
            $('#confirm_password').attr('type', $('#confirm_password').is(':password') ? 'text' : 'password');
            $(this).find('i').toggleClass('fa-eye-slash fa-eye');
        });

        // BUG-005: enforce the mandatory new/confirm password rules on the client before submit
        function showPwError($input, msg) {
            $input.closest('.box_label').find('.js-pw-error').remove();
            $('<div class="error js-pw-error" style="color:red;"></div>').text(msg).appendTo($input.closest('.box_label'));
        }

        $('form').on('submit', function(e) {
            $('.js-pw-error').remove();
            let valid = true;
            const oldPw = $.trim($('#old_password').val());
            const newPw = $.trim($('#new_password').val());
            const confirmPw = $.trim($('#confirm_password').val());

            if (oldPw === '') { showPwError($('#old_password'), 'The old password field is required.'); valid = false; }

            if (newPw === '') {
                showPwError($('#new_password'), 'The new password field is required.');
                valid = false;
            } else if (!/^(?=.*[@$%&])[^\s]{8,}$/.test(newPw)) {
                showPwError($('#new_password'), 'The password must be at least 8 characters long and include at least one special character from @$%&.');
                valid = false;
            } else if (newPw === oldPw) {
                showPwError($('#new_password'), 'The new password must be different from the old password.');
                valid = false;
            }

            if (confirmPw === '') {
                showPwError($('#confirm_password'), 'The confirm password field is required.');
                valid = false;
            } else if (confirmPw !== newPw) {
                showPwError($('#confirm_password'), 'The confirm password does not match the new password.');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush
