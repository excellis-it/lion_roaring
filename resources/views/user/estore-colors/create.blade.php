@extends('user.layouts.master')

@section('title')
    E-Store Color Management
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <div class="row">
                <div class="col-md-12">
                    <h4 class="title mb-5">Create New Color</h4>
                    <form action="{{ route('colors.store') }}" method="POST" id="create-color-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <div class="box_label">
                                    <label for="name">Color Name *</label>
                                    <input type="text" name="color_name" id="name" class="form-control">
                                    @if ($errors->has('color_name'))
                                        <span class="error">{{ $errors->first('color_name') }}</span>
                                    @endif

                                </div>

                            </div>



                            {{-- <div class="col-md-4 mb-2">
                                <div class="box_label">
                                    <label for="color">Color</label>
                                    <input type="color" name="color" id="color" class="form-control" style="min-height: 42px">
                                    @if ($errors->has('color'))
                                        <span class="error">{{ $errors->first('color') }}</span>
                                    @endif

                                </div>

                            </div> --}}

                            <div class="col-md-4 mb-2">
                                <div class="box_label">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>

                                </div>

                            </div>



                        </div>



                        <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                            <button type="submit" class="print_btn me-2">Save</button>
                            <a href="{{ route('colors.index') }}" class="print_btn print_btn_vv">Cancel</a>
                        </div>
                    </form>
                </div>

            </div>



        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $("#create-color-form").on("submit", function(e) {
                // e.preventDefault();
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
            });
        });
    </script>
@endpush
