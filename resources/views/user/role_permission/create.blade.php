@extends('user.layouts.master')
@section('title')
    Create Role Permission - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Create Role Permission </h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <div class="box_label">
                                    <label>Role Name</label>
                                    <input type="text" class="form-control" value="{{ old('role_name') }}" placeholder=""
                                        name="role_name" required>
                                    @if ($errors->has('role_name'))
                                        <span class="text-danger"
                                            style="color: red !important">{{ $errors->first('role_name') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 mb-2 mt-1">
                                <div class="box_label">
                                    <label>Is ECCLESIA?</label>
                                    <select name="is_ecclesia" id="" class="form-control" required>
                                        <option value="" disabled selected>
                                            Select
                                        </option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                    @if ($errors->has('is_ecclesia'))
                                        <span class="text-danger"
                                            style="color: red !important">{{ $errors->first('is_ecclesia') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 col-md-12 p-0">
                                <div class="table-responsive table-center-align" data-toggle="lists">
                                    @if (!empty($permissions))
                                        @php
                                            $modules = [
                                                'Profile',
                                                'Password',
                                                'Chat',
                                                'Team',
                                                'Email',
                                                'Topic',
                                                'Becoming Sovereigns',
                                                'Becoming Christ Like',
                                                'Becoming a Leader',
                                                'File',
                                                'Bulletin',
                                                'Job Postings',
                                                'Meeting Schedule',
                                                'Private Collaboration',
                                                'Event',
                                                'Partners',
                                                'Strategy',
                                                'Policy',
                                                'Help',
                                                'Role Permission',
                                                'Elearning CMS',
                                                'Elearning Category',
                                                'Elearning Product',
                                                'Estore CMS',
                                                'Estore Users',
                                                'Estore Category',
                                                'Estore Sizes',
                                                'Estore Colors',
                                                'Estore Products',
                                                'Estore Settings',
                                                'Estore Warehouse',
                                                'Estore Orders',
                                                'Order Status',
                                                'Email Template',
                                                'User Activity',
                                            ];

                                        @endphp
                                        <table class="table mb-0 table-bordered table-center-align">
                                            <thead>
                                                <tr>
                                                    <th style="width: 50px; text-align: center;">
                                                        <div class=" custom-checkbox">
                                                            <input type="checkbox" id="checkAll"
                                                                class="form-check-input manage-cl js-check-selected-row">
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
                                                @foreach ($modules as $new => $module)
                                                    <tr>
                                                        <td style="width: 150px; text-align: center;">
                                                            @if (in_array('Manage ' . $module, (array) $permissions))
                                                                @if ($key = array_search('Manage ' . $module, $permissions))
                                                                    <div class="toggle-check">
                                                                        <div class="">
                                                                            <input class="form-check-input manage-cl"
                                                                                type="checkbox" role="switch"
                                                                                name="permissions[]"
                                                                                value="{{ $key }}"
                                                                                data-id="{{ $new }}"
                                                                                id="flexSwitchCheckChecked">
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td>{{ ucfirst($module) }} </td>
                                                        <td style="width: 150px; text-align: center;">
                                                            @if (in_array('Manage ' . $module, (array) $permissions))
                                                                @if ($key = array_search('Manage ' . $module, $permissions))
                                                                    <div class="toggle-check">
                                                                        <div class="">
                                                                            <input class="form-check-input manage-cl"
                                                                                type="checkbox" role="switch"
                                                                                name="permissions[]"
                                                                                value="{{ $key }}"
                                                                                data-id="{{ $new }}"
                                                                                id="flexSwitchCheckChecked">
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td style="width: 150px; text-align: center;">
                                                            @if (in_array('View ' . $module, (array) $permissions))
                                                                @if ($key = array_search('View ' . $module, $permissions))
                                                                    <div class="toggle-check">
                                                                        <div class="">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                role="switch" name="permissions[]"
                                                                                value="{{ $key }}"
                                                                                data-id="{{ $new }}"
                                                                                id="flexSwitchCheckChecked">
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td style="width: 150px; text-align: center;">
                                                            @if (in_array('Create ' . $module, (array) $permissions))
                                                                @if ($key = array_search('Create ' . $module, $permissions))
                                                                    <div class="toggle-check">
                                                                        <div class="">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                role="switch" name="permissions[]"
                                                                                value="{{ $key }}"
                                                                                data-id="{{ $new }}"
                                                                                id="flexSwitchCheckChecked">
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </td>

                                                        <td style="width: 150px; text-align: center;">
                                                            @if (in_array('Edit ' . $module, (array) $permissions))
                                                                @if ($key = array_search('Edit ' . $module, $permissions))
                                                                    <div class="toggle-check">
                                                                        <div class="">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                role="switch" name="permissions[]"
                                                                                value="{{ $key }}"
                                                                                data-id="{{ $new }}"
                                                                                id="flexSwitchCheckChecked">
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td style="width: 150px; text-align: center;">
                                                            @if (in_array('Delete ' . $module, (array) $permissions))
                                                                @if ($key = array_search('Delete ' . $module, $permissions))
                                                                    <div class="toggle-check">
                                                                        <div class="">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                role="switch" name="permissions[]"
                                                                                value="{{ $key }}"
                                                                                data-id="{{ $new }}"
                                                                                id="flexSwitchCheckChecked">
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </td>

                                                        <td style="width: 150px; text-align: center;">
                                                            @if (in_array('Upload ' . $module, (array) $permissions))
                                                                @if ($key = array_search('Upload ' . $module, $permissions))
                                                                    <div class="toggle-check">
                                                                        <div class="">
                                                                            <input class="form-check-input"
                                                                                type="checkbox" role="switch"
                                                                                name="permissions[]"
                                                                                value="{{ $key }}"
                                                                                data-id="{{ $new }}"
                                                                                id="flexSwitchCheckChecked">
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td style="width: 150px; text-align: center;">
                                                            @if (in_array('Download ' . $module, (array) $permissions))
                                                                @if ($key = array_search('Download ' . $module, $permissions))
                                                                    <div class="toggle-check">
                                                                        <div class="">
                                                                            <input class="form-check-input"
                                                                                type="checkbox" role="switch"
                                                                                name="permissions[]"
                                                                                value="{{ $key }}"
                                                                                data-id="{{ $new }}"
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
                                        <div class="alert alert-danger" role="alert">
                                            No Permissions Found
                                        </div>
                                    @endif
                                    @if ($errors->has('permissions'))
                                        <span class="text-danger"
                                            style="color: red !important">{{ $errors->first('permissions') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                            <button type="submit" class="print_btn me-2">Save</button>
                            <a href="{{ route('roles.index') }}" class="print_btn print_btn_vv">Cancel</a>
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
    <script>
        // when i uncheck manage only uncheck the row of view, create, update, delete, upload, download
        $('.manage-cl').click(function() {
            var id = $(this).data('id');
            if ($(this).is(':checked')) {
                $('input[data-id="' + id + '"]').prop('checked', true);
            } else {
                $('input[data-id="' + id + '"]').prop('checked', false);
            }
        });
    </script>
@endpush
