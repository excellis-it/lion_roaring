@extends('user.layouts.master')
@section('title')
    Update Role Permission - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card w-100">
                            <div class="card-body">
                                <form action="{{ route('roles.update', Crypt::encrypt($role->id)) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @method('PUT')
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="heading_box mb-5">
                                                <h3>Update Role Premission </h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mb-2">
                                            <div class="box_label">
                                                <label>Name</label>
                                                <input type="text" class="form-control" value="{{ $role->name }}" name="role_name"
                                                    placeholder=""
                                                    {{ $role->name == 'LEADER' || $role->name == 'CUSTOMER' ? 'readonly' : '' }}>
                                                    @if ($errors->has('role_name'))
                                                        @error('role_name')
                                                            <span class="text-danger" style="color: red !important"> {{ $message }}</span>
                                                        @enderror
                                                    @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 p-0">
                                            <div class="table-responsive border-bottom" data-toggle="lists">
                                                @if (!empty($permissions))
                                                    @php
                                                        $modules = [
                                                            'Profile',
                                                            'Bulletin',
                                                            'Password',
                                                            'Partners',
                                                            'Team',
                                                            'Activity',
                                                            'File',
                                                            'Chat',
                                                            'Education',
                                                            'Calendar',
                                                            'Email',
                                                            'Sovereigns',
                                                            'Help',
                                                        ];

                                                    @endphp
                                                    <table class="table mb-0 table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 50px; text-align: center;">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" id="checkAllEdit"
                                                                            class="custom-control-input js-check-selected-row">
                                                                    </div>
                                                                </th>
                                                                <th>Select All</th>
                                                                <th>Manage</th>
                                                                <th>View</th>
                                                                <th>Create</th>
                                                                <th>Update</th>
                                                                <th>Delete</th>

                                                                <th>Upload</th>
                                                                <th>Download</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="list">
                                                            @foreach ($modules as $module)
                                                                <tr>
                                                                    <td></td>
                                                                    <td>{{ ucfirst($module) }} </td>
                                                                    <td>
                                                                        @if (in_array('Manage ' . $module, (array) $permissions))
                                                                            @if ($key = array_search('Manage ' . $module, $permissions))
                                                                                <div class="toggle-check">
                                                                                    <div class="form-check form-switch">
                                                                                        {{--   {{ Form::checkbox('permissions[]', $key, $role->permission, ['class' => 'form-check-input isscheck isscheck_' . str_replace(' ', '', $module), 'id' => 'permission' . $key]) }} --}}
                                                                                        <input class="form-check-input"
                                                                                            type="checkbox" role="switch"
                                                                                            name="permissions[]"
                                                                                            value="{{ $key }}"
                                                                                            @if (in_array($key, $role->permissions()->pluck('id')->toArray())) checked @endif
                                                                                            id="flexSwitchCheckChecked">
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if (in_array('View ' . $module, (array) $permissions))
                                                                            @if ($key = array_search('View ' . $module, $permissions))
                                                                                <div class="toggle-check">
                                                                                    <div class="form-check form-switch">
                                                                                        <input class="form-check-input"
                                                                                            type="checkbox"
                                                                                            @if (in_array($key, $role->permissions()->pluck('id')->toArray())) checked @endif
                                                                                            role="switch"
                                                                                            name="permissions[]"
                                                                                            value="{{ $key }}"
                                                                                            id="flexSwitchCheckChecked">
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if (in_array('Create ' . $module, (array) $permissions))
                                                                            @if ($key = array_search('Create ' . $module, $permissions))
                                                                                <div class="toggle-check">
                                                                                    <div class="form-check form-switch">
                                                                                        <input class="form-check-input"
                                                                                            type="checkbox" role="switch"
                                                                                            name="permissions[]"
                                                                                            value="{{ $key }}"
                                                                                            @if (in_array($key, $role->permissions()->pluck('id')->toArray())) checked @endif
                                                                                            id="flexSwitchCheckChecked">
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        @endif
                                                                    </td>

                                                                    <td>
                                                                        @if (in_array('Edit ' . $module, (array) $permissions))
                                                                            @if ($key = array_search('Edit ' . $module, $permissions))
                                                                                <div class="toggle-check">
                                                                                    <div class="form-check form-switch">
                                                                                        <input class="form-check-input"
                                                                                            type="checkbox" role="switch"
                                                                                            name="permissions[]"
                                                                                            @if (in_array($key, $role->permissions()->pluck('id')->toArray())) checked @endif
                                                                                            value="{{ $key }}"
                                                                                            id="flexSwitchCheckChecked">
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if (in_array('Delete ' . $module, (array) $permissions))
                                                                            @if ($key = array_search('Delete ' . $module, $permissions))
                                                                                <div class="toggle-check">
                                                                                    <div class="form-check form-switch">
                                                                                        <input class="form-check-input"
                                                                                            type="checkbox"
                                                                                            @if (in_array($key, $role->permissions()->pluck('id')->toArray())) checked @endif
                                                                                            role="switch"
                                                                                            name="permissions[]"
                                                                                            value="{{ $key }}"
                                                                                            id="flexSwitchCheckChecked">
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        @endif
                                                                    </td>

                                                                    <td>
                                                                        @if (in_array('Upload ' . $module, (array) $permissions))
                                                                            @if ($key = array_search('Upload ' . $module, $permissions))
                                                                                <div class="toggle-check">
                                                                                    <div class="form-check form-switch">
                                                                                        <input class="form-check-input"
                                                                                            type="checkbox"
                                                                                            @if (in_array($key, $role->permissions()->pluck('id')->toArray())) checked @endif
                                                                                            role="switch"
                                                                                            name="permissions[]"
                                                                                            value="{{ $key }}"
                                                                                            id="flexSwitchCheckChecked">
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if (in_array('Download ' . $module, (array) $permissions))
                                                                            @if ($key = array_search('Download ' . $module, $permissions))
                                                                                <div class="toggle-check">
                                                                                    <div class="form-check form-switch">
                                                                                        <input class="form-check-input"
                                                                                            type="checkbox"
                                                                                            @if (in_array($key, $role->permissions()->pluck('id')->toArray())) checked @endif
                                                                                            role="switch"
                                                                                            name="permissions[]"
                                                                                            value="{{ $key }}"
                                                                                            id="flexSwitchCheckChecked">
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                        </tbody>
                                                    </table>
                                                @else
                                                    <p>No permissions available</p>
                                                @endif
                                               @if ($errors->has('permissions'))
                                                    @error('permissions')
                                                        <span class="text-danger" style="color: red !important">{{ $message }}</span>
                                                    @enderror
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                        <button type="submit" class="print_btn me-2">Save</button>
                                        <a href="{{ route('roles.index') }}" class="print_btn print_btn_vv"
                                            href="">Cancel</a>
                                    </div>
                                </form>
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
            $("#checkAll").click(function() {
                $('input:checkbox').prop('checked', this.checked);
            });

            // Handle individual checkboxes
            $('input:checkbox').not("#checkAll").click(function() {
                if (!this.checked) {
                    $("#checkAll").prop('checked', false);
                }
            });
        });
    </script>
@endpush
