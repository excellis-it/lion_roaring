@extends('admin.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Update Article of Association Page
@endsection
@push('styles')
@endpush
@section('head')
    Update Article of Association Page
@endsection

@section('content')
    <div class="main-content">
        <div class="inner_page">
            <div class="card search_bar sales-report-card">
                <form action="{{ route('articles-of-association.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $article->id ?? '' }}">
                    <div class="sales-report-card-wrap">
                        <div class="row justify-content-between">
                            {{-- courses --}}
                            <div class="col-md-6">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- banner_title --}}
                                        <label for="floatingInputValue">PDF Upload</label>
                                        <input type="file" class="form-control" id="floatingInputValue" name="pdf"
                                            value="{{ old('pdf') }}">
                                        @if ($errors->has('pdf'))
                                            <div class="error" style="color:red;">
                                                {{ $errors->first('pdf') }}</div>
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
                    @if (isset($article->pdf))
                        <div class="sales-report-card-wrap mt-5">
                            <div class="row justify-content-between">

                                <div class="col-md-12">
                                    <iframe src="{{ Storage::url($article->pdf) }}" frameborder="0" width="100%"
                                        height="600px"></iframe>
                                </div>
                            </div>
                        </div>
                    @endif

                </form>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
@endpush
