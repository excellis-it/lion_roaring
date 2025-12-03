@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Update Member Privacy Policy Page
@endsection
@push('styles')
@endpush


@section('content')
     <div class="container-fluid">
         <div class="bg_white_border">
            <div class="card search_bar sales-report-card">
                <form action="{{ route('members-privacy-policies.store') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{$policy->id ?? ''}}">
                    <div class="sales-report-card-wrap mt-5">
                        <div class="row">
                            <div class="col-xl-12 col-md-12">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- meta description --}}
                                        <label for="floatingInputValue">Description*</label>
                                        <textarea name="description" id="description" cols="30" rows="10" placeholder="Description"
                                            class="form-control">{{ isset($policy->description) ? $policy->description : old('description') }}</textarea>
                                        @if ($errors->has('description'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('description') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- button --}}
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    // ClassicEditor.create(document.querySelector("#description"));
    $('#description').summernote({
        placeholder: 'Description*',
        tabsize: 2,
        height: 600
    });
</script>
@endpush
