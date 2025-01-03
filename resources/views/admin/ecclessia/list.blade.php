@extends('admin.layouts.master')
@section('title')
    Ecclessia - {{ env('APP_NAME') }}
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
@section('head')
    Manage Ecclessia
@endsection
@section('create_button')
    @if (auth()->user()->can('Create Ecclessia'))
        <a href="javascript:void(0)" id="create-ecclessia" class="btn btn-primary" data-bs-toggle="modal"
            data-bs-target="#add_ecclessia">Add
            Ecclessia </a>
    @endif
@endsection

@section('content')
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div id="add_ecclessia" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ecclessia Information</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('ecclessias.store') }}" method="POST" id="createForm"
                        enctype="multipart/form-data">
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>User Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="user_name" id="user_name">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Country<span class="text-danger">*</span></label>
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
                                        <div class="form-group" style="display: grid">
                                            <label>Phone<span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control" name="phone" id="phone">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Password<span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password" id="password">
                                    <span class="eye-btn-1" id="eye-button-1">
                                        <i class="ph ph-eye-slash" aria-hidden="true" id="togglePassword"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Confirm Password<span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="confirm_password"
                                        id="confirm_password">
                                    <span class="eye-btn-1" id="eye-button-2">
                                        <i class="ph ph-eye-slash" aria-hidden="true" id="togglePassword"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="submit-section">
                            <button class="btn btn-primary submit-btn">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="edit_admin" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ecclessia Information</h5>
                    <button type="button" class="edit_close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="edit-eclessia">
                    @include('admin.ecclessia.edit')

                </div>
            </div>
        </div>
    </div>
    <div class="main-content">
        <div class="inner_page">

            <div class="card table_sec stuff-list-table">
                <div class="row justify-content-end">
                    <div class="col-md-6">
                        <div class="row g-1 justify-content-end">

                            {{-- <div class="col-md-3 pl-0 ml-2">
                                <button class="btn btn-primary button-search" id="search-button"> <span class=""><i
                                            class="ph ph-magnifying-glass"></i></span> Search</button>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <div class="table-responsive" id="contacts-data">
                    <table id="example" class="dd table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>
                                    User Name
                                </th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>
                                    Phone
                                </th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ecclessias as $ecclessia)
                                <tr>
                                    <td>{{ $ecclessia->user_name }}</td>
                                    <td>{{ $ecclessia->full_name }}</td>
                                    <td>{{ $ecclessia->email }}</td>
                                    <td>{{ $ecclessia->phone }}</td>
                                    <td>{{ date('d M Y', strtotime($ecclessia->created_at)) }}</td>
                                    <td align="center">
                                        <div class="edit-1 d-flex align-items-center justify-content-center">
                                            @if (auth()->user()->can('Edit Ecclessia'))
                                                <a class="edit-admins edit-icon" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#edit_admin" data-id="{{ $ecclessia->id }}"
                                                    data-route="{{ route('ecclessias.edit', $ecclessia->id) }}"> <span
                                                        class="edit-icon"><i class="ph ph-pencil-simple"></i></span></a>
                                            @endif
                                            @if (auth()->user()->can('Delete Ecclessia'))
                                                <a href="{{ route('ecclessias.delete', $ecclessia->id) }}"
                                                    onclick="return confirm('Are you sure to delete this ecclessia?')">
                                                    <span class="trash-icon"><i class="ph ph-trash"></i></span></a>
                                            @endif
                                        </div>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    {{-- create-ecclessia --}}
    <script>
        $(document).ready(function() {
            $('#create-ecclessia').on('click', function() {
                $('#add_ecclessia').modal('show');
            });
        });
    </script>




    {{-- editForm  submit --}}

    {{-- close --}}
    <script>
        $(document).ready(function() {
            $('.edit_close').on('click', function() {
                $('#edit_admin').modal('hide');
            });

            $('.close').on('click', function() {
                $('#add_ecclessia').modal('hide');
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
    <script>
        $(document).ready(function() {

            $('.edit-admins').on('click', function() {
                var id = $(this).data('id');
                var route = $(this).data('route');
                var img_url = $('#img-' + id).data('url');
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: route,
                    type: 'POST',
                    data: {
                        id: id,
                    },
                    dataType: 'JSON',
                    success: async function(data) {
                        try {
                            // Open modal

                            await $('#edit_admin').modal('show');
                            await $('#edit-eclessia').html(data.data);
                            await initializeIntlTelInput();
                            await $('#loading').removeClass('loading');
                            await $('#loading-content').removeClass('loading-content');
                        } catch (error) {
                            console.log(error);
                        }
                    }
                });
            });

            function initializeIntlTelInput() {
                const phoneInput = $("#edit_phone");

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

                let maskNumber = intlTelInputUtils.formatNumber(exampleNumber, selectedCountry.iso2,
                    intlTelInputUtils
                    .numberFormat.NATIONAL);
                maskNumber = maskNumber.replace('+' + dialCode + ' ', '');

                let mask;
                if (dialCode && dialCode.length > 2) {
                    mask = '999 999 999';
                    maskNumber = '999 999 999';
                } else {
                    mask = maskNumber.replace(/[0-9+]/g, '0');
                }

                phoneInput.mask(mask, {
                    placeholder: 'Enter Phone Number',
                });

                phoneInput.on('countrychange', function() {
                    $(this).val('');
                    const newSelectedCountry = $(this).intlTelInput('getSelectedCountryData');
                    const newDialCode = newSelectedCountry.dialCode;
                    const newExampleNumber = intlTelInputUtils.getExampleNumber(newSelectedCountry.iso2, 0,
                        0);

                    let newMaskNumber = intlTelInputUtils.formatNumber(newExampleNumber, newSelectedCountry
                        .iso2,
                        intlTelInputUtils.numberFormat.NATIONAL);
                    newMaskNumber = newMaskNumber.replace('+' + newDialCode + ' ', '');

                    let newMask;

                    if (newDialCode.length > 2) {
                        newMask = '999 999 999';
                        newMaskNumber = '999 999 999';
                    } else {
                        newMask = newMaskNumber.replace(/[0-9+]/g, '0');
                    }

                    phoneInput.mask(newMask, {
                        placeholder: 'Enter Phone Number',
                    });
                });
            }

            // Pre-fill phone number when editing
            function setPhoneNumber(number) {
                const phoneInput = $("#edit_phone");
                const fullNumber = number;

                if (fullNumber) {
                    phoneInput.intlTelInput("setNumber", fullNumber);
                }
            }

            $(document).ready(function() {
                initializeIntlTelInput();
                setPhoneNumber();

                $(document).on('submit', '#editForm', function(e) {
                    e.preventDefault();

                    const phoneInput = $("#edit_phone");
                    const fullNumber = phoneInput.intlTelInput('getNumber');
                    const countryCode = phoneInput.intlTelInput('getSelectedCountryData').dialCode;

                    // Append full phone number and country code to the form
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'edit_full_phone_number',
                        value: fullNumber
                    }).appendTo('#editForm');

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'edit_country_code',
                        value: countryCode
                    }).appendTo('#editForm');

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
                                $('#edit_admin').modal('hide');
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
        });
    </script>
@endpush
