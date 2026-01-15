@if (isset($edit))
    <form action="{{ route('ecclessias.update') }}" method="POST" id="editForm" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <input type="hidden" id="hidden_id" name="id" value="{{ $eclessia->id ?? '' }}">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Roles<span class="text-danger">*</span></label>
                            <select name="role" id="role" class="form-control">
                                <option value="">Select Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}"
                                        {{ $eclessia->getFirstUserRoleName() == $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- user_name --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>User Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="edit_user_name" id="edit_user_name"
                                value="{{ $eclessia->user_name ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Country<span class="text-danger">*</span></label>
                            <select name="country" id="edit_country" class="form-control">
                                <option value="">Select Country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}"
                                        {{ $eclessia->country == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>First Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="edit_first_name" id="edit_first_name"
                                value="{{ $eclessia->first_name ?? '' }}">
                        </div>
                    </div>
                    {{-- middle_name --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Middle Name</label>
                            <input type="text" class="form-control" name="edit_middle_name" id="edit_middle_name"
                                value="{{ $eclessia->middle_name ?? '' }}">
                        </div>
                    </div>
                    {{-- last name --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Last Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="edit_last_name" id="edit_last_name"
                                value="{{ $eclessia->last_name ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email<span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="edit_email" id="edit_email"
                                value="{{ $eclessia->email ?? '' }}">
                        </div>
                    </div>
                    {{-- phone --}}
                    <div class="col-md-6">
                        <div class="form-group" style="display: grid">
                            <label>Phone<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="edit_phone" id="edit_phone"
                                value="{{ $eclessia->phone ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Password<span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" id="password_2">
                            <span class="eye-btn-1" id="eye-button-3">
                                <i class="ph ph-eye-slash" aria-hidden="true" id="togglePassword"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Confirm Password<span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password_2">
                            <span class="eye-btn-1" id="eye-button-4">
                                <i class="ph ph-eye-slash" aria-hidden="true" id="togglePassword"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="submit-section">
            <button class="btn btn-primary submit-btn">Update</button>
        </div>
    </form>
    <script>
        $(document).ready(function() {
            $('#eye-button-3').click(function() {
                $('#password_2').attr('type', $('#password_2').is(':password') ? 'text' : 'password');
                $(this).find('i').toggleClass('ph-eye-slash ph-eye');
            });
            $('#eye-button-4').click(function() {
                $('#confirm_password_2').attr('type', $('#confirm_password_2').is(':password') ? 'text' :
                    'password');
                $(this).find('i').toggleClass('ph-eye-slash ph-eye');
            });
        });
    </script>
@endif
