@extends('user.layouts.master')
@section('title')
    Save File - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('file.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Save Multiple Files</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            @if (auth()->user()->user_type == 'Global')
                                {{-- country --}}
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label>Country *</label>
                                        <select name="country_id" id="countries" class="form-control">
                                            <option value="">Select Country</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}"
                                                    {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('country_id'))
                                            <span class="error">{{ $errors->first('country_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="file">Choose File</label>

                                    <input type="file" name="file[]" id="file" class="form-control" multiple>
                                    @if ($errors->has('file'))
                                        <span class="error">{{ $errors->first('file') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- type --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label>Type</label>

                                    <select name="type" id="type" class="form-control">
                                        <option value="">Select Type</option>
                                        <option value="Becoming Sovereign"
                                            {{ old('type') == 'Becoming Sovereign' ? 'selected' : '' }}>Becoming Sovereign
                                        </option>
                                        <option value="Becoming Christ Like"
                                            {{ old('type') == 'Becoming Christ Like' ? 'selected' : '' }}>Becoming Christ
                                            Like</option>
                                        <option value="Becoming a Leader"
                                            {{ old('type') == 'Becoming a Leader' ? 'selected' : '' }}>Becoming a Leader
                                        </option>
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
                                <button type="submit" class="print_btn me-2">Save</button>
                                <a href="{{ route('file.index') }}" class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            $(document).ready(function() {
                $("#uploadForm").on("submit", function(e) {
                    // e.preventDefault();
                    $('#loading').addClass('loading');
                    $('#loading-content').addClass('loading-content');
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                $('#type').change(function() {
                    var type = $(this).val();
                    var country_id = $('#countries').val();
                    var url = "{{ route('topics.getTopics', ':type') }}";
                    url = url.replace(':type', type);
                    $.ajax({
                        url: url,
                        type: 'GET',
                        data: {
                            country_id: country_id
                        },
                        success: function(resp) {
                            var html = '<option value="">Select Topics</option>';
                            var oldTopic = "{{ old('topic_id') }}";
                            $.each(resp.data, function(index, value) {
                                if (oldTopic == value.id) {
                                    html += '<option value="' + value.id + '" selected>' +
                                        value.topic_name + '</option>';
                                } else {
                                    html += '<option value="' + value.id + '">' + value
                                        .topic_name + '</option>';
                                }
                            });
                            $('#topics').html(html);
                        }
                    });
                });
            });
        </script>
    @endpush
