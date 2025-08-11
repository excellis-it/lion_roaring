@extends('user.layouts.master')

@section('title')
    E-Store Size Management
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <div class="row">
                <div class="col-md-12">
                    <h4 class="title mb-5">Create New Size</h4>
                    <form action="{{ route('sizes.store') }}" method="POST" id="create-size-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="name">Size Name</label>
                                    <input type="text" name="name" id="name" class="form-control">

                                </div>

                            </div>

                            <div class="col-md-6 mb-2">
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
                            <a href="{{ route('sizes.index') }}" class="print_btn print_btn_vv">Cancel</a>
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
            $("#create-size-form").on("submit", function(e) {
                // e.preventDefault();
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
            });
        });
    </script>
@endpush
