@if (isset($edit))
    <form action="{{ route('admin.update') }}" method="POST" id="editForm" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <input type="hidden" id="hidden_id" name="id" value="{{ $admin->id ?? '' }}">
                <div class="row">
                    {{-- user_name --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>User Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="edit_user_name" id="edit_user_name"
                                value="{{ $admin->user_name ?? '' }}">
                        </div>
                    </div>
                    {{-- first_name --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>First Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="edit_first_name" id="edit_first_name"
                                value="{{ $admin->first_name ?? '' }}">
                        </div>
                    </div>
                    {{-- middle_name --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Middle Name</label>
                            <input type="text" class="form-control" name="edit_middle_name" id="edit_middle_name"
                                value="{{ $admin->middle_name ?? '' }}">
                        </div>
                    </div>
                    {{-- last_name --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Last Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="edit_last_name" id="edit_last_name"
                                value="{{ $admin->last_name ?? '' }}">
                        </div>
                    </div>
                    {{-- email --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="edit_email" id="edit_email"
                                value="{{ $admin->email ?? '' }}">
                        </div>
                    </div>
                    {{-- phone --}}
                    <div class="col-md-6" style="display: grid">
                        <div class="form-group">
                            <label>Phone<span class="text-danger">*</span></label><br>
                            <input type="text" class="form-control" name="edit_phone" id="edit_phone"
                                value="{{ $admin->phone ?? '' }}">
                            <input type="hidden" class="form-control" name="edit_phone2" id="edit_phone2"
                                value="{{ $admin->phone ?? '' }}">
                        </div>
                    </div>

                    {{-- role --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <h5>Roles</h5>


                            @foreach ($roles as $role)
                                <div class="form-check form-check-inline">
                                    <input id="data-roles-{{ $role->id }}" class="form-check-input data-roles"
                                        type="radio" name="role_name" value="{{ $role->name }}"
                                        data-permissions="{{ $role->permissions()->where('type', 2)->get() }}"
                                        {{ $admin->getRoleNames()->first() == $role->name ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                        for="data-roles-{{ $role->id }}">{{ $role->name }}</label>
                                </div>
                            @endforeach


                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="submit-section">
            <button class="btn btn-primary submit-btn">Update</button>
        </div>
    </form>
@endif
