@extends('user.layouts.master')
@section('title')
    Send Mail - {{ env('APP_NAME') }}
@endsection
@push('styles')
<link href="https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/dist/bootstrap-tagsinput.css" rel="stylesheet">
<style>
    /* bootstrap-tagsinput.css file - add in local */

.bootstrap-tagsinput {
  background-color: #fff;
  border: 1px solid #ccc;
  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
  display: inline-block;
  padding: 4px 6px;
  color: #555;
  vertical-align: middle;
  border-radius: 4px;
  width: 100%;
  line-height: 22px;
  cursor: text;
}
.bootstrap-tagsinput input {
  border: none;
  box-shadow: none;
  outline: none;
  background-color: transparent;
  padding: 0 6px;
  margin: 0;
  width: auto;
  max-width: inherit;
}
.bootstrap-tagsinput.form-control input::-moz-placeholder {
  color: #777;
  opacity: 1;
}
.bootstrap-tagsinput.form-control input:-ms-input-placeholder {
  color: #777;
}
.bootstrap-tagsinput.form-control input::-webkit-input-placeholder {
  color: #777;
}
.bootstrap-tagsinput input:focus {
  border: none;
  box-shadow: none;
}
.bootstrap-tagsinput .tag {
    margin-right: 2px;
    margin-left: 2px;
    color: #fff;
    background: #7851a9;
    border-radius: 25px;
    padding: 4px;
}
.bootstrap-tagsinput .tag [data-role="remove"] {
  margin-left: 8px;
  cursor: pointer;
}
.bootstrap-tagsinput .tag [data-role="remove"]:after {
  content: "x";
  padding: 0px 2px;
}
.bootstrap-tagsinput .tag [data-role="remove"]:hover {
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
}
.bootstrap-tagsinput .tag [data-role="remove"]:hover:active {
  box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
}

</style>
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
                                            <input type="text" class="form-control" name="to" value="{{ old('to') }}" id="to"
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
                                            <input type="text" class="form-control" name="cc" value="{{ old('cc') }}" id="cc"
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
<script src="https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
<script>
    $("#to").tagsinput('items');
    $("#cc").tagsinput('items');
</script>
@endpush
