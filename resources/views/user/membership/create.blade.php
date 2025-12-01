@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Create Membership Tier
@endsection
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <form action="{{ route('user.membership.store') }}" method="post">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="heading_box mb-4">
                            <h3>Create Tier</h3>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="box_label">
                            <label>Tier Name *</label>
                            <input name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="box_label">
                            <label>Slug *</label>
                            <input name="slug" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="box_label">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="box_label">
                            <label>Cost</label>
                            <input name="cost" class="form-control" type="number" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="box_label">
                            <label>Role</label>
                            <select name="role_id" class="form-control">
                                <option value="">-- select a role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="box_label">
                            <label>Benefits</label>
                            <div id="benefits">
                                <div class="input-group mb-2">
                                    <input type="text" name="benefits[]" class="form-control">
                                    <button type="button" class="btn btn-success add-benefit">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="w-100 text-end">
                            <button type="submit" class="print_btn">Create</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '.add-benefit', function() {
            $('#benefits').append(
                '<div class="input-group mb-2"><input type="text" name="benefits[]" class="form-control"><button type="button" class="btn btn-danger remove-benefit">-</button></div>'
            );
        });
        $(document).on('click', '.remove-benefit', function() {
            $(this).closest('.input-group').remove();
        });
    </script>
@endpush
