@extends('user.layouts.master')
@section('title')
    Edit Partners - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('partners.update', Crypt::encrypt($partner->id)) }}" method="POST">
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
                                                value="{{ $partner->phone }}" placeholder="">
                                            @if ($errors->has('phone'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('phone') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- password --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Password</label>
                                            <input type="password" class="form-control" name="password"
                                                value="{{ old('password') }}" placeholder="">
                                            @if ($errors->has('password'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('password') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- confirm_password --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Confirm Password</label>
                                            <input type="password" class="form-control" name="confirm_password"
                                                value="{{ old('confirm_password') }}" placeholder="">
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
                                    <div class="col-md-4 mb-2">
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
                                    </div>
                                    {{-- ecclesia_id --}}
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>Ecclesia </label>
                                            <select class="form-control" name="ecclesia_id">
                                                <option value="">Select Ecclesia</option>
                                                @foreach ($ecclessias as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ $partner->ecclesia_id == $item->id ? 'selected' : '' }}>
                                                        {{ $item->name }}</option>
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
                                <div class="w-100 text-end d-flex align-items-center justify-content-end">
                                    <button type="submit" class="print_btn me-2">Update</button>
                                    <a class="print_btn print_btn_vv" href="{{ route('partners.index') }}">Cancel</a>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
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

            let maskNumber = intlTelInputUtils.formatNumber(exampleNumber, selectedCountry.iso2, intlTelInputUtils.numberFormat.NATIONAL);
            maskNumber = maskNumber.replace('+' + dialCode + ' ', '');

            const mask = maskNumber.replace(/[0-9+]/g, '0');
            phoneInput.mask(mask, { placeholder: maskNumber });

            phoneInput.on('countrychange', function() {
                $(this).val('');
                const newSelectedCountry = $(this).intlTelInput('getSelectedCountryData');
                const newDialCode = newSelectedCountry.dialCode;
                const newExampleNumber = intlTelInputUtils.getExampleNumber(newSelectedCountry.iso2, 0, 0);

                let newMaskNumber = intlTelInputUtils.formatNumber(newExampleNumber, newSelectedCountry.iso2, intlTelInputUtils.numberFormat.NATIONAL);
                newMaskNumber = newMaskNumber.replace('+' + newDialCode + ' ', '');

                const newMask = newMaskNumber.replace(/[0-9+]/g, '0');
                phoneInput.mask(newMask, { placeholder: newMaskNumber });
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
@endpush
