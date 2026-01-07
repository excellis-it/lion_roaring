@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Edit Membership Tier
@endsection
@section('head')
    Edit Membership Tier
@endsection
@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="card search_bar sales-report-card">
                <form action="{{ route('admin.membership.update', $tier->id) }}" method="post">
                    @csrf
                    <div class="form-head">
                        <h4>Edit Tier</h4>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tier Name</label>
                                <input name="name" class="form-control" value="{{ $tier->name }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Slug</label>
                                <input name="slug" class="form-control" value="{{ $tier->slug }}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control">{{ $tier->description }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Cost</label>
                                <input name="cost" class="form-control" value="{{ $tier->cost }}">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label>Benefits</label>
                            <div id="benefits">
                                @foreach ($tier->benefits as $benefit)
                                    <div class="input-group mb-2">
                                        <input type="text" name="benefits[]" class="form-control"
                                            value="{{ $benefit->benefit }}">
                                        <span class="input-group-btn"><button type="button"
                                                class="btn btn-danger remove-benefit">-</button></span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="my-2"><button type="button" class="btn btn-success add-benefit">Add
                                    Benefit</button></div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Update</button>
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
