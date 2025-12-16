@extends('user.layouts.master')
@section('title')
    Admin - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <style>
        .dataTables_filter {
            margin-bottom: 10px !important;
        }

        .eye-btn-1 {
            top: 39px;
            right: 24px;
        }
    </style>
@endpush


@section('content')
    <section id="loading">
        <div id="loading-content"></div>
    </section>

     <div class="container-fluid">
         <div class="bg_white_border">

            <div class="card card-body">


                <form action="{{ route('admin.store') }}" method="POST" id="createForm" enctype="multipart/form-data"
                    autocomplete="off" autocomplete="new-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            {{-- <div class="profile-img-wrap edit-img">
                        <img class="inline-block" alt="admin"
                            src="{{ asset('admin_assets/img/profiles/avatar-02.jpg') }}">
                        <div class="fileupload btn">
                            <span class="btn-text">upload</span>
                            <input class="upload" type="file" name="profile_picture"
                                id="profile_picture">
                        </div>
                    </div> --}}
                            <div class="row">
                                {{-- user_name --}}
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>User Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="user_name" id="user_name"
                                            autocomplete="new-data">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>First Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="first_name" id="first_name">
                                    </div>
                                </div>
                                {{-- middle_name --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Middle Name</label>
                                        <input type="text" class="form-control" name="middle_name" id="middle_name">
                                    </div>
                                </div>
                                {{-- last_name --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Last Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="last_name" id="last_name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="email" id="email">
                                    </div>
                                </div>
                                {{-- phone --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Phone<span class="text-danger">*</span></label><br>
                                        <input type="text" class="form-control" name="phone" id="phone">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Password<span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password" id="password"
                                    autocomplete="new-password">
                                <span class="eye-btn-1" id="eye-button-1">
                                    <i class="ph ph-eye-slash" aria-hidden="true" id="togglePassword"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Confirm Password<span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="confirm_password" id="confirm_password"
                                    autocomplete="new-password">
                                <span class="eye-btn-1" id="eye-button-2">
                                    <i class="ph ph-eye-slash" aria-hidden="true" id="togglePassword"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <h5>Roles</h5>


                                @foreach ($roles as $role)
                                    <div class="form-check form-check-inline">
                                        <input id="data-roles-{{ $role->id }}" class="form-check-input data-roles"
                                            type="radio" name="role_name" value="{{ $role->name }}"
                                            data-permissions="{{ $role->permissions()->where('type', 2)->get() }}">
                                        <label class="form-check-label"
                                            for="data-roles-{{ $role->id }}">{{ $role->name }}</label>
                                    </div>
                                @endforeach


                            </div>
                        </div>

                    </div>

                    <div class="submit-section d-flex justify-content-end">
                        <button class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>


                <h5 class="mt-1" id="Role_Name"></h5>
                <div class="row container mt-1" id="permissions-container">



                </div>


            </div>


    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $(".data-roles").change(function(e) {
                e.preventDefault();
                var permissions = $(this).data('permissions');
                var role_name = $(this).val();
                console.log(permissions);
                $("#Role_Name").text(role_name);

                var col1 = $('<div class="col-6"></div>');
                var col2 = $('<div class="col-6"></div>');

                // Create an unordered list to hold the permissions for each column
                var permissionsList1 = $('<ul></ul>');
                var permissionsList2 = $('<ul></ul>');

                // Divide the permissions list into two arrays
                var half = Math.ceil(permissions.length / 2); // To split the list into two equal parts
                var firstHalf = permissions.slice(0, half);
                var secondHalf = permissions.slice(half);

                // Add permissions to the first column
                $.each(firstHalf, function(index, permission) {
                    var listItem = $('<li></li>').text(permission.name);
                    permissionsList1.append(listItem);
                });

                // Add permissions to the second column
                $.each(secondHalf, function(index, permission) {
                    var listItem = $('<li></li>').text(permission.name);
                    permissionsList2.append(listItem);
                });

                // Append the lists to the respective columns
                col1.append(permissionsList1);
                col2.append(permissionsList2);

                // Append the columns to the container row, replacing the content
                $('#permissions-container').html(col1).append(col2);

            });

        });
    </script>

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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/css/intlTelInput.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/js/intlTelInput-jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/js/utils.min.js"></script>
    <script>
        function initializeIntlTelInput() {
            const phoneInput = $("#phone");

            phoneInput.intlTelInput({
                geoIpLookup: function(callback) {
                    $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                        const countryCode = (resp && resp.country) ? resp.country : "US";
                        callback(countryCode);
                    });
                },
                initialCountry: "auto",
                separateDialCode: true,
            });

            const selectedCountry = phoneInput.intlTelInput('getSelectedCountryData');
            const dialCode = selectedCountry.dialCode;
            const exampleNumber = intlTelInputUtils.getExampleNumber(selectedCountry.iso2, 0, 0);

            let maskNumber = intlTelInputUtils.formatNumber(exampleNumber, selectedCountry.iso2, intlTelInputUtils
                .numberFormat.NATIONAL);
            maskNumber = maskNumber.replace('+' + dialCode + ' ', '');

            // Define the mask
            let mask;
            if (dialCode && dialCode.length > 2) {
                // Use a fixed mask pattern for countries with dial codes of length greater than 2
                mask = '999 999 999';
                maskNumber = '999 999 999';
            } else {
                // Dynamically create a mask by replacing digits with 0 for shorter dial codes
                mask = maskNumber.replace(/[0-9+]/g, '0');
            }

            // Apply the mask with the placeholder
            phoneInput.mask(mask, {
                placeholder: 'Enter Phone Number',
            });

            phoneInput.on('countrychange', function() {
                $(this).val(''); // Clear the input field when country changes
                const newSelectedCountry = $(this).intlTelInput('getSelectedCountryData');
                const newDialCode = newSelectedCountry.dialCode;
                const newExampleNumber = intlTelInputUtils.getExampleNumber(newSelectedCountry.iso2, 0, 0);

                let newMaskNumber = intlTelInputUtils.formatNumber(newExampleNumber, newSelectedCountry.iso2,
                    intlTelInputUtils.numberFormat.NATIONAL);
                newMaskNumber = newMaskNumber.replace('+' + newDialCode + ' ', '');

                let newMask;

                if (newDialCode.length > 2) {
                    // If dial code length is more than 2, use a 999 999 999 mask (or a similar format)
                    newMask = '999 999 999';
                    newMaskNumber = '999 999 999';
                } else {
                    // Otherwise, replace all digits with 0
                    newMask = newMaskNumber.replace(/[0-9+]/g, '0');
                }

                phoneInput.mask(newMask, {
                    placeholder: 'Enter Phone Number',
                });
            });
        }

        function setPhoneNumber() {
            const phoneInput = $("#phone");
            const fullNumber = "{{ old('full_phone_number') }}";

            if (fullNumber) {
                phoneInput.intlTelInput("setNumber", fullNumber);
            }
        }

        $(document).ready(function() {
            initializeIntlTelInput();
            setPhoneNumber();

            $('#createForm').on('submit', function(e) {
                e.preventDefault();
                const phoneInput = $("#phone");
                const fullNumber = phoneInput.intlTelInput('getNumber');
                const countryCode = phoneInput.intlTelInput('getSelectedCountryData').dialCode;

                $('<input>').attr({
                    type: 'hidden',
                    name: 'full_phone_number',
                    value: fullNumber
                }).appendTo('#createForm');

                $('<input>').attr({
                    type: 'hidden',
                    name: 'country_code',
                    value: countryCode
                }).appendTo('#createForm');

                var form = $(this);
                var url = form.attr('action');
                var type = form.attr('method');
                var data = new FormData(form[0]);
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
                $.ajax({
                    url: url,
                    type: type,
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.status == 'success') {
                            $('#loading').removeClass('loading');
                            $('#loading-content').removeClass('loading-content');
                            $('#add_ecclessia').modal('hide');
                            location.reload();

                        } else {
                            $('#loading').removeClass('loading');
                            $('#loading-content').removeClass('loading-content');
                            toastr.error(data.message);
                        }
                    },
                    error: function(data) {
                        // validation error
                        if (data.status == 422) {
                            var errors = data.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                            $('#loading').removeClass('loading');
                            $('#loading-content').removeClass('loading-content');
                        }
                    }
                });
            });
        });
    </script>
@endpush
