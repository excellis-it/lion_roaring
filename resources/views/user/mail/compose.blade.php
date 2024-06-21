@extends('user.layouts.master')
@section('title')
    Send Mail - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('mail.send') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="heading_box mb-5">
                                            <h3>Send Mail</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>To*</label>
                                            <input type="text" class="form-control" name="to" value="{{ old('to') }}"
                                                placeholder="">
                                            @if ($errors->has('to'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('to') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- phone --}}
                                    <div class="col-md-6 mb-2">
                                        <div class="box_label">
                                            <label>CC</label>
                                            <input type="text" class="form-control" name="cc" value="{{ old('cc') }}"
                                                placeholder="">
                                            @if ($errors->has('cc'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('cc') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- password --}}
                                    <div class="col-md-12 mb-2">
                                        <div class="box_label">
                                            <label>Subject *</label>
                                            <input type="text" class="form-control" name="subject"
                                                value="{{ old('subject') }}" placeholder="">
                                            @if ($errors->has('subject'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('subject') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- confirm_password --}}
                                    <div class="col-md-12 mb-2">
                                        <div class="box_label">
                                            <label>Message*</label>
                                            <textarea class="form-control" name="message" value="{{ old('message') }}" rows="30" cols="5"
                                                placeholder=""></textarea>
                                            @if ($errors->has('message'))
                                                <div class="error" style="color:red !important;">
                                                    {{ $errors->first('message') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>


                                </div>
                                <div class="w-100 text-end d-flex align-items-center justify-content-end">
                                    <button type="submit" class="print_btn me-2"><i class="fa fa-paper-plane" aria-hidden="true"></i> Send</button>
                                    <a class="print_btn print_btn_vv" href="{{ route('mail.index') }}">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
