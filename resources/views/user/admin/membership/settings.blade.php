@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Membership Settings
@endsection
@section('content')
     <div class="container-fluid">
         <div class="bg_white_border">
          
                <form action="{{ route('admin.membership.settings') }}" method="post">
                    @csrf
                    <div class="sales-report-card-wrap">
                        <div class="form-head">
                            <h4>Measurement Settings</h4>
                        </div>
                        <div class="row justify-content-between">
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label>Measurement Label</label>
                                        <input type="text" class="form-control" name="label"
                                            value="{{ $measurement->label ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label>Measurement Description</label>
                                        <textarea name="description" class="form-control">{{ $measurement->description ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label>Yearly Dues</label>
                                        <input name="yearly_dues" class="form-control"
                                            value="{{ $measurement->yearly_dues ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="btn-1 mt-4">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
