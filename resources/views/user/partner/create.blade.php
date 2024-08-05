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
                                            <h3>Login Information</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Email *</label>
                                            <input type="email" class="form-control" name="email"
                                                value="{{ old('email') }}" placeholder="">
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
                                            <label>Phone *</label>
                                            <input type="text" class="form-control" name="phone" id="mobile_code"
                                                value="{{ old('phone') }}" placeholder="">
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
                                            <label>Password *</label>
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
                                            <label>Confirm Password *</label>
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
                                            <input type="text" class="form-control" name="user_name"
                                                value="{{ old('user_name') }}" placeholder="">
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
                                                        {{ old('role') == $item->name ? 'selected' : '' }}>
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
                                    {{-- eclessias --}}
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>Ecclesias </label>
                                            <select class="form-control" name="ecclesia_id">
                                                <option value="">Select Ecclesia</option>
                                                @foreach ($eclessias as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('ecclesia_id') == $item->id ? 'selected' : '' }}>
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
                                                value="{{ old('first_name') }}" placeholder="">
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
                                                value="{{ old('middle_name') }}" placeholder="">
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
                                                value="{{ old('last_name') }}" placeholder="">
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
                                            <label>Country *</label>
                                            <select name="country" id="country" class="form-control">
                                                <option value="">Select Country</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}"
                                                        @if (old('country') == $country->id) selected @endif
                                                        {{ $country->code == 'US' ? 'selected' : '' }}>
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
                                            <label>State *</label>
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
                                            <label>City *</label>
                                            <input type="text" class="form-control" name="city"
                                                value="{{ old('city') }}" placeholder="">
                                            @if ($errors->has('city'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('city') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Zip *</label>
                                            <input type="text" class="form-control" name="zip"
                                                value="{{ old('zip') }}" placeholder="">
                                            @if ($errors->has('zip'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('zip') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Address *</label>
                                            <input type="text" class="form-control" name="address"
                                                value="{{ old('address') }}" placeholder="">
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
                                                value="{{ old('address2') }}" placeholder="">
                                            @if ($errors->has('address2'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('address2') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- zip --}}

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.7.0/build/css/intlTelInput.css">
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.7.0/build/js/intlTelInput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.7.0/build/js/utils.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.7/dist/inputmask.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.querySelector("#mobile_code");
            const iti = window.intlTelInput(input, {
                separateDialCode: true,
                initialCountry: "us",
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.7.0/build/js/utils.js",
            });

            // Function to apply the mask based on the selected country
            const applyMask = (countryData) => {
                const mask = getMaskForCountry(countryData.iso2);
                Inputmask(mask).mask(input);
            };

            // Get the mask for the country
            const getMaskForCountry = (countryCode) => {
                const masks = {
                    // Add all the country masks as you've defined
                    us: '999 999-9999',
                    gb: '99999 999999',
                    in: '99999-99999',
                    br: '99999-999',
                    au: '9999 999 999',
                    de: '99999 999999',
                    dk: '99 99 99 99',
                    fr: '99 99 99 99 99',
                    it: '999 999 9999',
                    ru: '999 999-99-99',
                    mx: '999 999 9999',
                    jp: '999-9999-9999',
                    cn: '999 9999 9999',
                    sg: '9999 9999',
                    pt: '999 999 999',
                    kr: '999-9999-9999',
                    pl: '99 999 99 99',
                    th: '9999 9999',
                    tw: '9999 999 999',
                    cz: '999 999 999',
                    sk: '999 999 999',
                    ph: '9999 999 9999',
                    my: '999 999 9999',
                    id: '9999 999 9999',
                    vn: '999 999 999',
                    nl: '99 9999999',
                    be: '999 99 99 99',
                    fi: '999 9999999',
                    se: '999-999 99 99',
                    no: '999 99 999',
                    pl: '99 999 99 99',
                    hu: '99 999 9999',
                    at: '999 9999999',
                    ch: '999 999 9999',
                    za: '999 999 9999',
                    gr: '999 999 9999',
                    ro: '999 999 999',
                    hr: '999 999 9999',
                    bg: '999 999 9999',
                    tr: '999 999 9999',
                    is: '999 9999',
                    ie: '999 999 9999',
                    es: '999 999 999',
                    ar: '999 999 9999',
                    cl: '999 999 9999',
                    co: '999 999 9999',
                    // ... other country masks
                };
                return masks[countryCode] || '9999999999';
            };

            // Apply mask initially
            applyMask(iti.getSelectedCountryData());

            // Apply mask on country change
            input.addEventListener('countrychange', function() {
                applyMask(iti.getSelectedCountryData());
            });

            // Add event listener to the form
            const form = document.querySelector("form");
            form.addEventListener("submit", function(event) {
                // Get only the country full code number
                const countryCode = iti.getSelectedCountryData().dialCode;
                const fullNumber = iti.getNumber();
                // Set the full number as a hidden input's value
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'country_code'; // Name this as needed
                hiddenInput.value = countryCode;
                form.appendChild(hiddenInput);
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
                            html += '<option value="' + state.id + '">' + state.name + '</option>';
                        });
                        $('#state').html(html);
                    }
                });
            }
        });
    </script>
@endpush
