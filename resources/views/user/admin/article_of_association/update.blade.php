@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Update Article of Association Page
@endsection
@push('styles')
@endpush


@section('content')
     <div class="container-fluid">
         <div class="bg_white_border">
          
                <form action="{{ route('articles-of-association.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $article->id ?? '' }}">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="country_code">Content Country</label>
                            <select onchange="window.location.href='?content_country_code='+$(this).val()"
                                name="content_country_code" id="content_country_code" class="form-control">
                                @foreach (\App\Models\Country::all() as $country)
                                    <option value="{{ $country->code }}"
                                        {{ request()->get('content_country_code', 'US') == $country->code ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
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
                                      <button type="submit" class="print_btn me-2 mt-2">Update</button>
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
@endsection

@push('scripts')
@endpush
