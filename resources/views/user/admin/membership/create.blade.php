@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Create Membership Tier
@endsection

@section('content')
     <div class="container-fluid">
         <div class="bg_white_border">
            <div class="card search_bar sales-report-card">
                <form action="{{ route('admin.membership.store') }}" method="post">
                    @csrf
                    <div class="form-head">
                        <h4>Create Tier</h4>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tier Name</label>
                                <input name="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Slug</label>
                                <input name="slug" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Cost</label>
                                <input name="cost" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Role</label>
                                <select name="role_id" class="form-control">
                                    <option value="">-- select a role --</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label>Benefits</label>
                            <div id="benefits">
                                <div class="input-group mb-2">
                                    <input type="text" name="benefits[]" class="form-control">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-success add-benefit">+</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '.add-benefit', function() {
            $('#benefits').append(
                '<div class="input-group mb-2"><input type="text" name="benefits[]" class="form-control"><div class="input-group-btn"><button type="button" class="btn btn-danger remove-benefit">-</button></div></div>'
                );
        });
        $(document).on('click', '.remove-benefit', function() {
            $(this).closest('.input-group').remove();
        });
    </script>
@endpush
