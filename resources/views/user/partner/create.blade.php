@extends('user.layouts.master')
@section('title')
    Create Partners - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('partners.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="heading_box mb-5">
                                            <h3>Add New User</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">                                    
                                    <div class="col-md-12 col-lg-7 mb-3">
                                        <div class="box_label">
                                            <label>What should we call you?</label>
                                            <input type="text" class="form-control" name="user_name"
                                                value="{{ old('user_name') }}" placeholder="Enter your profile name">
                                            @if ($errors->has('user_name'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('user_name') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-7 mb-3">
                                        <div class="box_label">
                                            <label>What’s your email?</label>
                                            <input type="text" class="form-control" name="email"
                                                value="{{ old('email') }}" placeholder="Enter your email address">
                                            @if ($errors->has('email'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('email') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="heading_box mb-5">
                                            <h3>Roles</h3>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <ul class="rols_ul">
                                            <li>
                                                <div class="toggle-redio">
                                                    <input type="radio" id="test1" name="radio-group" checked>
                                                    <label for="test1">LEADER</label>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="toggle-redio">
                                                    <input type="radio" id="test2" name="radio-group">
                                                    <label for="test2">LEADER NON-SOVEREIGN</label>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="toggle-redio">
                                                    <input type="radio" id="test3" name="radio-group">
                                                    <label for="test3">MEMBER</label>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="toggle-redio">
                                                    <input type="radio" id="test4" name="radio-group">
                                                    <label for="test4">MEMBER SOVEREIGN</label>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="toggle-redio">
                                                    <input type="radio" id="test5" name="radio-group">
                                                    <label for="test5">SUPER LEADER</label>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-lg-7 col-md-12">
                                        <h4>Leader</h4>
                                        <ul class="per_list">
                                            <li class="">Manage Profile</li>
                                            <li class="">Manage Password</li>
                                            <li class="">Manage Chat</li>
                                            <li class="">Create Team</li>
                                            <li class="">Delete Team</li>
                                            <li class="">Manage Team</li>
                                            <li class="">Manage Email</li>
                                            <li class="">Manage Becoming Sovereigns</li>
                                            <li class="">View Becoming Sovereigns</li>
                                            <li class="">Upload Becoming Sovereigns</li>
                                            <li class="">Edit Becoming Sovereigns</li>
                                            <li class="">Delete Becoming Sovereigns</li>
                                            <li class="">Download Becoming Sovereigns</li>
                                            <li class="">Manage Becoming Christ Like</li>
                                            <li class="">View Becoming Christ Like</li>
                                            <li class="">Upload Becoming Christ Like</li>
                                            <li class="">Download Becoming Christ Like</li>
                                            <li class="">Manage Becoming a Leader</li>
                                            <li class="">View Becoming a Leader</li>
                                            <li class="">Upload Becoming a Leader</li>
                                            <li class="">Edit Becoming a Leader</li>
                                            <li class="">Delete Becoming a Leader</li>
                                            <li class="">Download Becoming a Leader</li>
                                            <li class="">Manage Bulletin</li>
                                            <li class="">Edit Bulletin</li>
                                            <li class="">Create Bulletin</li>
                                            <li class="">Delete Bulletin</li>
                                            <li class="">Manage Job Postings</li>
                                            <li class="">View Job Postings</li>
                                            <li class="">Manage Meeting Schedule</li>
                                            <li class="">View Meeting Schedule</li>
                                            <li class="">Create Meeting Schedule</li>
                                            <li class="">Edit Meeting Schedule</li>
                                            <li class="">Delete Meeting Schedule</li>
                                            <li class="">Manage Event</li>
                                            <li class="">Create Event</li>
                                            <li class="">Edit Event</li>
                                            <li class="">Manage Partners</li>
                                            <li class="">Manage Help</li>
                                            <li class="">Manage Strategy</li>
                                            <li class="">Upload Strategy</li>
                                            <li class="">Download Strategy</li>
                                            <li class="">View Strategy</li>
                                            <li class="">Delete Strategy</li>
                                            <li class="">Manage Topic</li>
                                            <li class="">Edit Topic</li>
                                            <li class="">Create Topic</li>
                                            <li class="">Delete Topic</li>
                                        </ul>
                                    </div>

                                    
                                </div>
                                <div class="w-100 text-end d-flex align-items-center justify-content-end">
                                    <button type="submit" class="print_btn me-2">Save</button>
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
<script>
    $(document).ready(function() {
        $('#eye-button-1').click(function() {
            $('#password').attr('type', $('#password').is(':password') ? 'text' : 'password');
            $(this).find('i').toggleClass('fa-eye-slash fa-eye');
        });
        $('#eye-button-2').click(function() {
            $('#confirm_password').attr('type', $('#confirm_password').is(':password') ? 'text' : 'password');
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
            const fullNumber = "{{ old('full_phone_number') }}";

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
            getStates($('#country').val());

            $('#country').change(function() {
                var country = $(this).val();
                getStates(country);
            });


            function getStates(country) {
                $.ajax({
                    url: "{{ route('get.states') }}",
                    type: "get",
                    data: {
                        country: country
                    },
                    success: function(response) {
                        var states = response;
                        var html = '<option value="">Select State</option>';
                        states.forEach(state => {
                            html += '<option value="' + state.id + '">' + state.name +
                                '</option>';
                        });
                        $('#state').html(html);
                    }
                });
            }
        });
    </script>
@endpush
