@extends('user.layouts.master')
@section('title')
    Update Policy and Guidance - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('policy-guidence.update', $policy->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Update Policy and Guidance</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="policy">Choose Policy and Guidance</label>

                                <input type="policy" name="policy" id="policy" class="form-control">
                                @if ($errors->has('policy'))
                                    <span class="error">{{ $errors->first('policy') }}</span>
                                @endif
                            </div>
                            </div>
                            {{-- type --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="type">Type</label>

                                <select name="type" id="type" class="form-control">
                                    <option value="">Select Type</option>
                                    <option value="Becoming Sovereign"
                                        {{ $policy->type == 'Becoming Sovereign' ? 'selected' : '' }}>Becoming Sovereign
                                    </option>
                                    <option value="Becoming Christ Like"
                                        {{ $policy->type == 'Becoming Christ Like' ? 'selected' : '' }}>Becoming Christ Like
                                    </option>
                                    <option value="Becoming a Leader"
                                        {{ $policy->type == 'Becoming a Leader' ? 'selected' : '' }}>Becoming a Leader</option>
                                </select>
                                @if ($errors->has('type'))
                                    <span class="error">{{ $errors->first('type') }}</span>
                                @endif
                            </div>
                            </div>
                            {{-- topics --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label>Topics *</label>
                                    <select name="topic_id" id="topics" class="form-control">
                                        <option value="">Select Topics</option>

                                    </select>
                                    @if ($errors->has('topics'))
                                        <span class="error">{{ $errors->first('topics') }}</span>
                                    @endif

                                </div>
                            </div>
                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Reupload</button>
                                <a href="{{ route('policy-guidence.index') }}" class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')

    @endpush
