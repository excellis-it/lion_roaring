@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Reject Ecclessia
@endsection
@push('styles')
@endpush


@section('content')
     <div class="container-fluid">
         <div class="bg_white_border">
            <div class="card search_bar sales-report-card">
                <div class="sales-report-card-wrap">
                    <form action="{{ route('members.reject', $partner->id) }}" method="post" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="row justify-content-between">
                            <div class="col-xl-12 col-md-12">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        <label for="floatingInputValue">Reason*</label>
                                        <textarea type="text" class="form-control" id="reason" name="reason" placeholder="Reason*">{{ old('reason') }} </textarea>
                                        @if ($errors->has('reason'))
                                            <div class="error" style="color:red;">{{ $errors->first('reason') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="btn-1">
                                    <button type="submit">Rejected</button>
                                    <a href="{{route('members.index')}}"> <button type="button">Cancel</button></a>
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

@endpush
