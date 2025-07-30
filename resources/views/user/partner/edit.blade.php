@extends('user.layouts.master')
@section('title')
    Edit Partners - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('partners.update', Crypt::encrypt($partner->id)) }}" method="POST" id="uploadForm">
                        @method('PUT')
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
                                            <input type="text" class="form-control" name="email"
                                                value="{{ $partner->email }}" placeholder="">
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
                                            <label>Phone*</label>
                                            <input type="tel" class="form-control" name="phone" id="mobile_code"
                                                value="{{ $partner->phone }}" placeholder="Enter Phone Number">
                                            @if ($errors->has('phone'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('phone') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- password --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label position-relative">
                                            <label>Password</label>
                                            <input type="password" class="form-control" name="password" id="password"
                                                value="{{ old('password') }}" placeholder="">
                                            <span class="eye-btn-1" id="eye-button-1">
                                                <i class="fa fa-eye-slash" aria-hidden="true" id="togglePassword"></i>
                                            </span>
                                            @if ($errors->has('password'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('password') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- confirm_password --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label position-relative">
                                            <label>Confirm Password</label>
                                            <input type="password" class="form-control" name="confirm_password"
                                                id="confirm_password" value="{{ old('confirm_password') }}" placeholder="">
                                            <span class="eye-btn-1" id="eye-button-2">
                                                <i class="fa fa-eye-slash" aria-hidden="true" id="togglePassword"></i>
                                            </span>
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
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>User Name *</label>
                                            <input type="text" class="form-control" name="user_name" readonly
                                                value="{{ $partner->user_name }}" placeholder="">
                                            @if ($errors->has('user_name'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('user_name') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>Roles *</label>
                                            <select class="form-control" name="role">
                                                <option value="">Select Role</option>
                                                @foreach ($roles as $item)
                                                    <option value="{{ $item->name }}"
                                                        {{ $partner->getRoleNames()->first() == $item->name ? 'selected' : '' }}>
                                                        {{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('role'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('role') }}
                                                </div>
                                            @endif

                                        </div>
                                    </div> --}}
                                    {{-- ecclesia_id --}}
                                    {{-- <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>Ecclesia </label>
                                            <select class="form-control" name="ecclesia_id">
                                                <option value="">Select Ecclesia</option>
                                                @foreach ($ecclessias as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ $partner->ecclesia_id == $item->id ? 'selected' : '' }}>
                                                        {{ $item->full_name ?? '' }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('ecclesia_id'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('ecclesia_id') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div> --}}

                                    <div class="col-md-4 mb-2" id="ecclesia_main_input">
                                        <div class="box_label">
                                            <label>Ecclesias </label>
                                            <select class="form-control" name="ecclesia_id">
                                                <option value="">Select Ecclesia</option>
                                                @foreach ($eclessias as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ $partner->ecclesia_id == $item->id ? 'selected' : '' }}>
                                                        {{ $item->name . '(' . $item->countryName->name . ')' ?? '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('ecclesia_id'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('ecclesia_id') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>First Name *</label>
                                            <input type="text" class="form-control" name="first_name"
                                                value="{{ $partner->first_name }}" placeholder="">
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
                                                value="{{ $partner->middle_name }}" placeholder="">
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
                                                value="{{ $partner->last_name }}" placeholder="">
                                            @if ($errors->has('last_name'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('last_name') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- country --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Country</label>
                                            <select name="country" id="country" class="form-control">
                                                <option value="">Select Country</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}"
                                                        @if ($partner->country == $country->id) selected @endif>
                                                        {{ $country->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('country'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('country') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- state --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>State</label>
                                            <select name="state" id="state" class="form-control">
                                                <option value="">Select State</option>
                                            </select>
                                            @if ($errors->has('state'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('state') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- city --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>City</label>
                                            <input type="text" class="form-control" name="city"
                                                value="{{ $partner->city }}" placeholder="">
                                            @if ($errors->has('city'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('city') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- zip --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Zip</label>
                                            <input type="text" class="form-control" name="zip"
                                                value="{{ $partner->zip }}" placeholder="">
                                            @if ($errors->has('zip'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('zip') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Address</label>
                                            <input type="text" class="form-control" name="address"
                                                value="{{ $partner->address }}" placeholder="">
                                            @if ($errors->has('address'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('address') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- address2 --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Address 2</label>
                                            <input type="text" class="form-control" name="address2"
                                                value="{{ $partner->address2 }}" placeholder="">
                                            @if ($errors->has('address2'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('address2') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>


                                </div>


                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <h5>Roles*</h5>


                                            @foreach ($roles as $role)
                                                <div class="form-check form-check-inline">
                                                    <input id="data-roles-{{ $role->id }}"
                                                        class="form-check-input data-roles" type="radio" name="role"
                                                        value="{{ $role->name }}"
                                                        data-permissions="{{ $role->permissions()->where('type', 1)->get() }}"
                                                        data-isecclesia="{{ $role->is_ecclesia }}"
                                                        {{ $partner->getRoleNames()->first() == $role->name ? 'checked' : '' }}
                                                        required>
                                                    <label class="form-check-label"
                                                        for="data-roles-{{ $role->id }}">{{ $role->name }}
                                                        <small>{{ $role->is_ecclesia == 1 ? '(ECCLESIA)' : '' }}</small></label>
                                                </div>
                                            @endforeach


                                        </div>
                                    </div>

                                </div>


                                @if ($errors->has('manage_ecclesia'))
                                    <div class="error" style="color:red !important;">
                                        * {{ $errors->first('manage_ecclesia') }}
                                    </div>
                                @endif

                                <div class="row mt-3" id="hoe_row" style="display: none">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <h5>Can manage this House Of ECCLESIA*</h5>

                                            @php
                                                // Convert manage_ecclesia to an array (handle null case)
                                                $selectedEcclesias =
                                                    isset($partner->manage_ecclesia) &&
                                                    $partner->manage_ecclesia !== null
                                                        ? explode(',', $partner->manage_ecclesia)
                                                        : [];
                                            @endphp

                                            @foreach ($eclessias as $eclessia)
                                                <div class="form-check form-check-inline">
                                                    <input id="data-eclessia-{{ $eclessia->id }}"
                                                        class="form-check-input data-eclessia" type="checkbox"
                                                        name="manage_ecclesia[]" value="{{ $eclessia->id }}"
                                                        {{ in_array($eclessia->id, $selectedEcclesias) ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="data-eclessia-{{ $eclessia->id }}">{{ $eclessia->name . ' (' . $eclessia->countryName->name . ')' }}
                                                    </label>
                                                </div>
                                            @endforeach



                                        </div>
                                    </div>

                                </div>


                                <div class="w-100 text-end d-flex align-items-center justify-content-end">
                                    <button type="submit" class="print_btn me-2">Update</button>
                                    <a class="print_btn print_btn_vv" href="{{ route('partners.index') }}">Cancel</a>
                                </div>

                            </div>
                        </div>
                    </form>


                    <div class="card card-body shadow-lg mt-2">
                        <h5 class="mt-0" id="Role_Name"></h5>
                        <div class="row container mt-1" id="permissions-container">
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $("#uploadForm").on("submit", function(e) {
                // e.preventDefault();
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#eye-button-1').click(function() {
                $('#password').attr('type', $('#password').is(':password') ? 'text' : 'password');
                $(this).find('i').toggleClass('fa-eye-slash fa-eye');
            });
            $('#eye-button-2').click(function() {
                $('#confirm_password').attr('type', $('#confirm_password').is(':password') ? 'text' :
                    'password');
                $(this).find('i').toggleClass('fa-eye-slash fa-eye');
            });
        });
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/css/intlTelInput.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/js/intlTelInput-jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/js/utils.min.js"></script>

    <script>
        function initializeIntlTelInput() {
            const phoneInput = $("#mobile_code");

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
                $(this).val('');
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
            const phoneInput = $("#mobile_code");
            const fullNumber = "{{ $partner->phone }}";

            if (fullNumber) {
                phoneInput.intlTelInput("setNumber", fullNumber);
            }
        }

        $(document).ready(function() {
            initializeIntlTelInput();
            setPhoneNumber();

            $('form').on('submit', function() {
                const phoneInput = $("#mobile_code");
                const fullNumber = phoneInput.intlTelInput('getNumber');
                const countryCode = phoneInput.intlTelInput('getSelectedCountryData').dialCode;
                const countryData = phoneInput.intlTelInput('getSelectedCountryData');
                const countryCodeName = countryData.iso2;


                $('<input>').attr({
                    type: 'hidden',
                    name: 'full_phone_number',
                    value: fullNumber
                }).appendTo('form');

                $('<input>').attr({
                    type: 'hidden',
                    name: 'country_code',
                    value: countryCode
                }).appendTo('form');

                $('<input>').attr({
                    type: 'hidden',
                    name: 'phone_country_code_name',
                    value: countryCodeName
                }).appendTo('form');
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            var country = $('#country').val();
            var state = {{ is_numeric($partner->state) && $partner->state != null ? $partner->state : 0 }};

            getStates(country, state);

            $('#country').change(function() {
                var country = $(this).val();
                getStates(country);
            });

            function getStates(country, state = 0) {
                // alert(country);
                $.ajax({
                    url: "{{ route('get.states') }}",
                    type: "get",
                    data: {
                        country: country
                    },
                    success: function(response) {
                        var states = response;
                        var html = '<option value="">Select State</option>';
                        states.forEach(stateObj => {
                            var selected = stateObj.id == state ? 'selected' : '';
                            html +=
                                `<option value="${stateObj.id}" ${selected}>${stateObj.name}</option>`;
                        });
                        $('#state').html(html);
                    }
                });
            }
        });
    </script>
    {{-- <script>
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
    </script> --}}
    <script>
        $(document).ready(function() {
            // Function to populate permissions based on the selected role
            function populatePermissions(roleName, permissions, is_ecclesia) {
                $("#Role_Name").text(roleName);



                if (is_ecclesia == 1) {
                    $("#hoe_row").show();
                    $("#ecclesia_main_input").hide();
                } else {
                    $("#hoe_row").hide();
                    $("#ecclesia_main_input").show();
                }

                var col1 = $('<div class="col-6"></div>');
                var col2 = $('<div class="col-6"></div>');

                // Create unordered lists for permissions
                var permissionsList1 = $('<ul></ul>');
                var permissionsList2 = $('<ul></ul>');

                // Divide the permissions list into two arrays
                var half = Math.ceil(permissions.length / 2); // Split into two halves
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

                // Append lists to columns
                col1.append(permissionsList1);
                col2.append(permissionsList2);

                // Append columns to container row, replacing content
                $('#permissions-container').html(col1).append(col2);
            }

            // On page load: Find the checked radio button
            var selectedRadio = $(".data-roles:checked");
            if (selectedRadio.length > 0) {
                var initialRoleName = selectedRadio.val();
                var initialPermissions = selectedRadio.data('permissions');
                var initialIsEcclesia = selectedRadio.data('isecclesia');
                if (initialPermissions) {
                    populatePermissions(initialRoleName, initialPermissions,
                        initialIsEcclesia); // Populate on page load
                }
            }

            // Event: Handle radio button change
            $(".data-roles").change(function(e) {
                e.preventDefault();
                var permissions = $(this).data('permissions'); // Fetch permissions
                var roleName = $(this).val(); // Fetch selected role name
                var is_ecclesia = $(this).data('isecclesia');
                if (permissions) {
                    populatePermissions(roleName, permissions, is_ecclesia); // Populate permissions
                }
            });
        });
    </script>
@endpush
