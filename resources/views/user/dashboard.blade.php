@extends('user.layouts.master')
@section('title')
    Dashboard - {{ env('APP_NAME') }} user
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <form>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card w-100">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="heading_box mb-5">
                                            <h3>Heading</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <div class="box_label">
                                            <label>Name</label>
                                            <input type="text" class="form-control" value="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="box_label">
                                            <label>Email</label>
                                            <input type="email" class="form-control" value="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="box_label">
                                            <label>Gender</label>
                                            <select class="form-control" value="">
                                                <option selected>Man</option>
                                                <option>Woman</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="box_label">
                                            <label>Type</label>
                                            <select class="form-control" value="">
                                                <option selected>Man</option>
                                                <option>Woman</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="box_label">
                                            <label>Type</label>
                                            <textarea class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 mb-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-12">
                                                <label>Division</label>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                        id="inlineRadio1" value="option1">
                                                    <label class="form-check-label" for="inlineRadio1">None</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                        id="inlineRadio2" value="option2">
                                                    <label class="form-check-label" for="inlineRadio2">Full
                                                        Pay</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                        id="inlineRadio3" value="option3">
                                                    <label class="form-check-label" for="inlineRadio3">Table
                                                        Rce</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 mb-2">
                                        <div class="row align-items-center">
                                            <div class="col-md-12">
                                                <label>Division</label>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox1"
                                                        value="option1">
                                                    <label class="form-check-label" for="inlineCheckbox1">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox1"
                                                        value="option1">
                                                    <label class="form-check-label" for="inlineCheckbox1">No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3 mb-2">
                                        <div class="row align-items-center">
                                            <div class="col-md-12">
                                                <label>Toggle</label>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="toggle_box">
                                                    <input type="checkbox" id="switch" /><label
                                                        for="switch">Toggle</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="w-100 text-end d-flex align-items-center justify-content-end">
                                    <a class="print_btn me-2" >Save</a>
                                    <a class="print_btn print_btn_vv" >Cancel</a>
                                </div>

                            </div>
                        </div>
                        <div class="all_btn">
                            <a  class="add_btn btn_all">Add</a>
                            <a  class="delete_btn btn_all">Delete</a>
                            <a  class="another_btn btn_all">Another</a>
                            <a  class="exit_btn btn_all">Exit</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
