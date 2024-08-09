@extends('user.layouts.master')
@section('title')
    Dashboard - {{ env('APP_NAME') }} user profile
@endsection
@push('styles')
@endpush
@section('content')
    @php
        use App\Helpers\Helper;
    @endphp
    <div class="container-fluid">
        <div class="bg_white_border">
            <!--  Row 1 -->

            <div class="row">
                <div class="col-lg-12">
                    {{-- @if (auth()->user()->hasRole('MEMBER'))
                        <div
                            class="expiery_date
                @if (isset(auth()->user()->userLastSubscription) && auth()->user()->userLastSubscription != null) @if (Helper::expireTo(auth()->user()->userLastSubscription->subscription_expire_date) <= 10)
                today-expire @endif
                @endif">
                            @if (isset(auth()->user()->userLastSubscription) && auth()->user()->userLastSubscription != null)
                                @if (Helper::expireTo(auth()->user()->userLastSubscription->subscription_expire_date) == 0)
                                    Today is the last day of your plan
                                @elseif (Helper::expireTo(auth()->user()->userLastSubscription->subscription_expire_date) == 1)
                                    Tomorrow is the last day of your plan
                                @elseif (Helper::expireTo(auth()->user()->userLastSubscription->subscription_expire_date) < 0)
                                    Expired
                                @else
                                    @if (Helper::expireTo(auth()->user()->userLastSubscription->subscription_expire_date) <= 10)
                                        Your plan will expire within
                                        {{ Helper::expireTo(auth()->user()->userLastSubscription->subscription_expire_date) }}
                                        days
                                    @else
                                        Your plan will expire in
                                        {{ Helper::expireTo(auth()->user()->userLastSubscription->subscription_expire_date) }}
                                        days
                                    @endif
                                @endif
                            @else
                                <p class="text-danger">No Ongoing Plan</p>
                            @endif
                        </div>
                    @endif --}}


                    <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-lg-12 col-md-12">
                                <div class="d-block d-md-flex align-items-center">
                                    <div class="left_img me-3 profile_img">
                                        <span>
                                            @if (Auth::user()->profile_picture)
                                                <img src="{{ Storage::url(Auth::user()->profile_picture) }}" alt=""
                                                    id="blah">
                                            @else
                                                <img src="{{ asset('user_assets/images/logo.png') }}" alt=""
                                                    id="blah" />
                                            @endif
                                        </span>
                                        <div class="profile_eidd">
                                            <input type="file" id="edit_profile" onchange="readURL(this);"
                                                name="profile_picture" />
                                            <label for="edit_profile"><i class="ti ti-edit"></i></label>
                                        </div>
                                    </div>
                                    <div class="right_text profile-info">
                                        <p>Hello!</p>
                                        <h2> {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h2>
                                        <p>{{ Auth::user()->email }}</p>
                                        <span>

                                            <b>
                                                 {{ Auth::user()->ecclesia ? 'Ecclesia: '. Auth::user()->ecclesia->name : '' }}
                                            </b>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row  mb-0">
                            <div class="col-lg-12">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>First Name*</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name"
                                                placeholder="First Name" value="{{ Auth::user()->first_name }}">
                                            @if ($errors->has('first_name'))
                                                <div class="error" style="color:red;">{{ $errors->first('first_name') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- middle name --}}
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>Middle Name</label>
                                            <input type="text" class="form-control" id="middle_name" name="middle_name"
                                                placeholder="Middle Name" value="{{ Auth::user()->middle_name }}">
                                            @if ($errors->has('middle_name'))
                                                <div class="error" style="color:red;">{{ $errors->first('middle_name') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- last name --}}
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>Last Name*</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name"
                                                placeholder="Last Name" value="{{ Auth::user()->last_name }}">
                                            @if ($errors->has('last_name'))
                                                <div class="error" style="color:red;">{{ $errors->first('last_name') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- phone number --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Phone Number*</label>
                                            <input type="text" class="form-control" id="mobile_code" name="phone_number"
                                                placeholder="Phone Number" value="{{ Auth::user()->phone }}">
                                            @if ($errors->has('phone_number'))
                                                <div class="error" style="color:red;">{{ $errors->first('phone_number') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Zip*</label>
                                            <input type="text" class="form-control" id="zip" name="zip"
                                                placeholder="Zip" value="{{ Auth::user()->zip }}">
                                            @if ($errors->has('zip'))
                                                <div class="error" style="color:red;">{{ $errors->first('zip') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- country --}}
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>Country*</label>
                                           <select class="form-control" name="country" id="country">
                                                <option value="">Select Country</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}"
                                                        {{ Auth::user()->country == $country->id ? 'selected' : '' }}>
                                                        {{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('country'))
                                                <div class="error" style="color:red;">{{ $errors->first('country') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- state --}}
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>State*</label>
                                            <select class="form-control" name="state" id="state">
                                                <option value="">Select State</option>
                                            </select>
                                            @if ($errors->has('state'))
                                                <div class="error" style="color:red;">{{ $errors->first('state') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- city --}}
                                    <div class="col-md-4 mb-2">
                                        <div class="box_label">
                                            <label>City*</label>
                                           <input type="text" class="form-control" id="city" name="city"
                                                placeholder="City" value="{{ Auth::user()->city }}">
                                            @if ($errors->has('city'))
                                                <div class="error" style="color:red;">{{ $errors->first('city') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- zip --}}

                                    {{-- address --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Address 1*</label>
                                            <input type="text" class="form-control" id="address" name="address"
                                                placeholder="Address" value="{{ Auth::user()->address }}">
                                            @if ($errors->has('address'))
                                                <div class="error" style="color:red;">{{ $errors->first('address') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- address2 --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>Address 2</label>
                                            <input type="text" class="form-control" id="address2" name="address2"
                                                placeholder="Address 2" value="{{ Auth::user()->address2 }}">
                                            @if ($errors->has('address2'))
                                                <div class="error" style="color:red;">{{ $errors->first('address2') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="w-100 text-end d-flex align-items-center justify-content-end">
                                        <button class="print_btn" type="submit">Update</button>
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
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#blah')
                        .attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
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
                var country = $('#country').val();
                var state = {{ is_numeric(auth()->user()->state) && auth()->user()->state != null ? auth()->user()->state : 0 }};

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
