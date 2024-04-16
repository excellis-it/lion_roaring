@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Edit Plan Details
@endsection
@push('styles')
@endpush
@section('head')
    Edit Plan Details
@endsection
@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="card search_bar sales-report-card">
                <div class="sales-report-card-wrap">
                    <form action="{{ route('plans.update', $plan->id) }}" method="POST" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="row justify-content-between">
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Plan name*</label>
                                        <input type="text" class="form-control" id="floatingInputValue" name="plan_name"
                                            value="{{ $plan->plan_name }}" placeholder="Plan name*">
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
                                            value="{{ $plan->plan_price }}" placeholder="Plan Price ($)*">
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
                                            name="plan_validity"
                                            value="{{ $plan->plan_validity ? $plan->plan_validity : old('plan_validity') }}"
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
                                            <option value="1" {{ $plan->plan_status == 1 ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="0" {{ $plan->plan_status == 0 ? 'selected' : '' }}>Inactive
                                            </option>
                                        </select>
                                        @if ($errors->has('plan_status'))
                                            <div class="error" style="color:red;">{{ $errors->first('plan_status') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12 col-md-12">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Plan Description*</label>
                                        <textarea type="text" class="form-control" id="floatingInputValue"
                                            name="plan_description"
                                            placeholder="Plan Description*">{{ $plan->plan_description ? $plan->plan_description : old('plan_description') }}</textarea>
                                        @if ($errors->has('plan_description'))
                                            <div class="error" style="color:red;">{{ $errors->first('plan_description') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="btn-1">
                                    <button type="submit">Update</button>
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
@endpush
