@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Create Role Permission
@endsection
@push('styles')
@endpush
@section('head')
    Create Role Permission
@endsection

@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="card search_bar sales-report-card">
                <div class="sales-report-card-wrap">
                    <form action="{{ route('admin.roles.store') }}" method="POST">
                        @csrf
                        <div class="row justify-content-between">
                            <div class="col-md-4">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Role Name*</label>
                                        <input type="text" class="form-control" id="floatingInputValue" name="role_name"
                                            value="{{ old('role_name') }}" placeholder="Enter Role Name">
                                        @if ($errors->has('role_name'))
                                            <div class="error" style="color:red;">{{ $errors->first('role_name') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 ">
                                <div class="table table-bordered" data-toggle="lists">
                                    @if (!empty($permissions))
                                        @php
                                            $modules = [
                                                'My Profile',
                                                'My Password',
                                                'Admin List',
                                                'Donations',
                                                'Contact Us Messages',
                                                'Newsletters',
                                                'Testimonials',
                                                'Our Governance',
                                                'Our Organization',
                                                'Organization Center',
                                                'Services',
                                                'Home Page',
                                                'Details Page',
                                                'Organizations Page',
                                                'About Us Page',
                                                'Faq',
                                                'Gallery',
                                                'Ecclesia Association Page',
                                                'Principle and Business Page',
                                                'Contact Us Page',
                                                'Article of Association Page',
                                                'Footer',
                                                'Register Page Agreement Page',
                                                'Member Privacy Policy Page',
                                                'PMA Terms Page',
                                                'All Users',
                                                'Members Access',
                                            ];

                                        @endphp
                                        <table class="table mb-0 table-bordered">
                                            <thead>
                                                <tr>
                                                    <th style="width: 50px; text-align: center;">
                                                        <div class="custom-checkbox">
                                                            <input type="checkbox" id="checkAll"
                                                                class=" js-check-selected-row">
                                                        </div>
                                                    </th>
                                                    <th>Select All</th>
                                                    <th>Manage</th>
                                                    {{-- <th>View</th> --}}
                                                    <th>Create</th>
                                                    <th>Update</th>
                                                    <th>Delete</th>
                                                    {{-- <th>Upload</th>
                                                    <th>Download</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody class="list">
                                                @foreach ($modules as $new => $module)
                                                    <tr>
                                                        <td style="width: 50px; text-align: center;">
                                                            @if (in_array('Manage ' . $module, (array) $permissions))
                                                                @if ($key = array_search('Manage ' . $module, $permissions))
                                                                    <div class="toggle-check">
                                                                        <div class="form-check form-switch">
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
                                                        <td>
                                                            @if (in_array('Manage ' . $module, (array) $permissions))
                                                                @if ($key = array_search('Manage ' . $module, $permissions))
                                                                    <div class="toggle-check">
                                                                        <div class="form-check form-switch">
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
                                                        {{-- <td>
                                                            @if (in_array('View ' . $module, (array) $permissions))
                                                                @if ($key = array_search('View ' . $module, $permissions))
                                                                    <div class="toggle-check">
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                role="switch" name="permissions[]"
                                                                                value="{{ $key }}"
                                                                                data-id="{{ $new }}"
                                                                                id="flexSwitchCheckChecked">
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </td> --}}
                                                        <td>
                                                            @if (in_array('Create ' . $module, (array) $permissions))
                                                                @if ($key = array_search('Create ' . $module, $permissions))
                                                                    <div class="toggle-check">
                                                                        <div class="form-check form-switch">
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

                                                        <td>
                                                            @if (in_array('Edit ' . $module, (array) $permissions))
                                                                @if ($key = array_search('Edit ' . $module, $permissions))
                                                                    <div class="toggle-check">
                                                                        <div class="form-check form-switch">
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
                                                        <td>
                                                            @if (in_array('Delete ' . $module, (array) $permissions))
                                                                @if ($key = array_search('Delete ' . $module, $permissions))
                                                                    <div class="toggle-check">
                                                                        <div class="form-check form-switch">
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

                                                        {{-- <td>
                                                            @if (in_array('Upload ' . $module, (array) $permissions))
                                                                @if ($key = array_search('Upload ' . $module, $permissions))
                                                                    <div class="toggle-check">
                                                                        <div class="form-check form-switch">
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
                                                        <td>
                                                            @if (in_array('Download ' . $module, (array) $permissions))
                                                                @if ($key = array_search('Download ' . $module, $permissions))
                                                                    <div class="toggle-check">
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                role="switch" name="permissions[]"
                                                                                value="{{ $key }}"
                                                                                data-id="{{ $new }}"
                                                                                id="flexSwitchCheckChecked">
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </td> --}}
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
                        <div class="row d-flex justify-content-end">


                            <div class="btn-1 p-3">
                                <button type="submit">Create</button>
                                <a href="{{ route('admin.roles.index') }}"> <button type="button">Cancel</button></a>
                            </div>

                        </div>
                </div>
                </form>
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
