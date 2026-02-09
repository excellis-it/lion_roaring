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
    <div id="add_admin" class="modal custom-modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Admin Information</h5>
                    <a href="javascript:void(0)" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-size: x-large; color:#fff;">&times;</span>
                    </a>
                </div>
                <div class="modal-body">
                    <form action="{{ route('user.admin.store') }}" method="POST" id="createForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    {{-- user_name --}}
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>User Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="user_name" id="user_name">
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
                        <div class="submit-section mt-4">
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
                    <h5 class="modal-title">Admin Information</h5>
                    <a href="javascript:void(0)" class="edit_close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-size: x-large;  color:#fff;">&times;</span>
                    </a>
                </div>
                <div class="modal-body" id="edit-admin-content">
                    @include('user.admin.admin.edit')
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0">Admin List</h3>
                    <p class="text-muted small mb-0">Manage super administrators</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if (auth()->user()->can('Create Admin List'))
                        <a href="javascript:void(0)" id="create-admin" class="btn btn-primary px-4"
                            data-bs-toggle="modal" data-bs-target="#add_admin">
                            <i class="fas fa-plus me-1"></i> Add Admin
                        </a>
                    @endif
                    <div class="search-field mb-0" style="min-width: 300px;">
                        <input type="text" name="search" id="search"
                            placeholder="search by name, username or email..." required class="form-control">
                        <button class="submit_search" id="search-button">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive" id="admins-data">
                <table class="table align-middle bg-white color_body_text display">
                    <thead class="color_head">
                        <tr class="header-row">
                            <th>User Name</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Created Date</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @include('user.admin.admin.table')
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Search functionality
            function fetch_data(query) {
                $.ajax({
                    url: "{{ route('user.admin.index') }}",
                    data: {
                        search: query
                    },
                    success: function(data) {
                        $('tbody').html(data.data);
                    }
                });
            }

            $(document).on('keyup', '#search', function() {
                var query = $('#search').val();
                fetch_data(query);
            });

            $(document).on('click', '#search-button', function() {
                var query = $('#search').val();
                fetch_data(query);
            });

            // Toggle password visibility
            $('#eye-button-1').click(function() {
                $('#password').attr('type', $('#password').is(':password') ? 'text' : 'password');
                $(this).find('i').toggleClass('ph-eye-slash ph-eye');
            });
            $('#eye-button-2').click(function() {
                $('#confirm_password').attr('type', $('#confirm_password').is(':password') ? 'text' :
                    'password');
                $(this).find('i').toggleClass('ph-eye-slash ph-eye');
            });

            // Close modals
            $('.edit_close, .close').on('click', function() {
                $('.modal').modal('hide');
            });
        });
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/css/intlTelInput.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/js/intlTelInput-jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/js/utils.min.js"></script>

    <script>
        function initializeIntlTelInput(selector, initialNumber = '') {
            const phoneInput = $(selector);
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

            if (initialNumber) {
                phoneInput.intlTelInput("setNumber", initialNumber);
            }

            const applyMask = () => {
                const selectedCountry = phoneInput.intlTelInput('getSelectedCountryData');
                const dialCode = selectedCountry.dialCode;
                const exampleNumber = intlTelInputUtils.getExampleNumber(selectedCountry.iso2, 0, 0);
                let maskNumber = intlTelInputUtils.formatNumber(exampleNumber, selectedCountry.iso2, intlTelInputUtils
                    .numberFormat.NATIONAL);
                maskNumber = maskNumber.replace('+' + dialCode + ' ', '');

                let mask = (dialCode && dialCode.length > 2) ? '999 999 999' : maskNumber.replace(/[0-9+]/g, '0');
                phoneInput.mask(mask, {
                    placeholder: 'Enter Phone Number'
                });
            };

            applyMask();
            phoneInput.on('countrychange', function() {
                $(this).val('');
                applyMask();
            });
        }

        $(document).ready(function() {
            initializeIntlTelInput("#phone");

            $('#createForm').on('submit', function(e) {
                e.preventDefault();
                const phoneInput = $("#phone");
                const fullNumber = phoneInput.intlTelInput('getNumber');
                const countryCode = phoneInput.intlTelInput('getSelectedCountryData').dialCode;

                let formData = new FormData(this);
                formData.append('full_phone_number', fullNumber);
                formData.append('country_code', countryCode);

                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');

                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        $('#loading').removeClass('loading');
                        $('#loading-content').removeClass('loading-content');
                        if (data.status == 'success') {
                            $('#add_admin').modal('hide');
                            location.reload();
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function(data) {
                        $('#loading').removeClass('loading');
                        $('#loading-content').removeClass('loading-content');
                        if (data.status == 422) {
                            $.each(data.responseJSON.errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        }
                    }
                });
            });

            $(document).on('click', '.edit-admins', function() {
                var id = $(this).data('id');
                var route = $(this).data('route');
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');

                $.ajax({
                    url: route,
                    type: 'POST',
                    data: {
                        id: id
                    },
                    success: function(data) {
                        $('#edit-admin-content').html(data.data);
                        $('#edit_admin').modal('show');
                        initializeIntlTelInput("#edit_phone", $("#edit_phone").val());
                        $('#loading').removeClass('loading');
                        $('#loading-content').removeClass('loading-content');
                    }
                });
            });

            $(document).on('submit', '#editForm', function(e) {
                e.preventDefault();
                const phoneInput = $("#edit_phone");
                const fullNumber = phoneInput.intlTelInput('getNumber');
                const countryCode = phoneInput.intlTelInput('getSelectedCountryData').dialCode;

                let formData = new FormData(this);
                formData.append('edit_full_phone_number', fullNumber);
                formData.append('edit_country_code', countryCode);

                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');

                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        $('#loading').removeClass('loading');
                        $('#loading-content').removeClass('loading-content');
                        if (data.status == 'success') {
                            $('#edit_admin').modal('hide');
                            location.reload();
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function(data) {
                        $('#loading').removeClass('loading');
                        $('#loading-content').removeClass('loading-content');
                        if (data.status == 422) {
                            $.each(data.responseJSON.errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        }
                    }
                });
            });
        });
    </script>
@endpush
