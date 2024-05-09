@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Create Plan
@endsection
@push('styles')
@endpush
@section('head')
    Create Plan
@endsection

@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="card search_bar sales-report-card">
                <div class="sales-report-card-wrap">
                    <form action="{{ route('plans.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row justify-content-between">
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Plan Name*</label>
                                        <input type="text" class="form-control" id="floatingInputValue" name="plan_name"
                                            value="{{ old('plan_name') }}" placeholder="Plan Name*">
                                        @if ($errors->has('plan_name'))
                                            <div class="error" style="color:red;">{{ $errors->first('plan_name') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Plan Price ($)*</label>
                                        <input type="text" class="form-control" id="floatingInputValue" name="plan_price"
                                            value="{{ old('plan_price') }}" placeholder="Plan Price ($)*">
                                        @if ($errors->has('plan_price'))
                                            <div class="error" style="color:red;">{{ $errors->first('plan_price') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Plan Validity (Month)*</label>
                                        <input type="text" class="form-control" id="floatingInputValue"
                                            name="plan_validity" value="{{ old('plan_validity') }}"
                                            placeholder="Plan Validity (Month)*">
                                        @if ($errors->has('plan_validity'))
                                            <div class="error" style="color:red;">{{ $errors->first('plan_validity') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Status*</label>
                                        <select name="plan_status" id="plan_status" class="form-control">
                                            <option value="">Select Status</option>
                                            <option value="1" {{ old('plan_status') == 1 ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="0" {{ old('plan_status') == 0 ? 'selected' : '' }}>Inactive
                                            </option>
                                        </select>
                                        @if ($errors->has('plan_status'))
                                            <div class="error" style="color:red;">{{ $errors->first('plan_status') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12 col-md-12">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Plan Description*</label>
                                        <textarea type="text" class="form-control" id="plan_description"
                                            name="plan_description"
                                            placeholder="Plan Description*">{{ old('plan_description') }} </textarea>
                                        @if ($errors->has('plan_description'))
                                            <div class="error" style="color:red;">{{ $errors->first('plan_description') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="btn-1">
                                    <button type="submit">Create</button>
                                </div>
                            </div>
                        </div>
                </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/28.0.0/classic/ckeditor.js"></script>
    <script>
        $(document).ready(function() {
            $('#eye-button-1').click(function() {
                $('#password').attr('type', $('#password').is(':password') ? 'text' : 'password');
                $(this).find('i').toggleClass('ph-eye-slash ph-eye');
            });
            $('#eye-button-2').click(function() {
                $('#confirm_password').attr('type', $('#confirm_password').is(':password') ? 'text' :
                    'password');
                $(this).find('i').toggleClass('ph-eye-slash ph-eye');
            });
        });
    </script>

<script>
    $(document).ready(function() {
        ClassicEditor.create(document.querySelector("#plan_description"));
    });
</script>
@endpush
