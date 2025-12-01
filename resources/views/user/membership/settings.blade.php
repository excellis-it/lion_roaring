@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Membership Settings
@endsection
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <form action="{{ route('user.membership.settings') }}" method="post">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="heading_box mb-4">
                            <h3>Measurement Settings</h3>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="box_label">
                            <label>Measurement Label</label>
                            <input type="text" class="form-control" name="label"
                                value="{{ $measurement->label ?? '' }}" placeholder="e.g., USD/year">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="box_label">
                            <label>Yearly Dues</label>
                            <input name="yearly_dues" class="form-control" type="number" step="0.01"
                                value="{{ $measurement->yearly_dues ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="box_label">
                            <label>Measurement Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $measurement->description ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="w-100 text-end">
                            <button type="submit" class="print_btn">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>
@endpush
