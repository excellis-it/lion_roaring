@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Update Details Page
@endsection
@push('styles')
@endpush


@section('content')
     <div class="container-fluid">
         <div class="bg_white_border">
                <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-0">Details Update</h3>
                    <p class="text-muted small mb-0">Update details page</p>
                </div>
            </div>
                <form action="{{ route('user.admin.details.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
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
                    <div class="sales-report-card-wrap mt-5">
                        <div class="row count-class" id="add-more">
                            @if (isset($details) && count($details) > 0)
                                @foreach ($details as $key => $item)
                                    <div class="col-xl-5 col-md-5 mt-4">
                                        <div class="form-group-div">
                                            <div class="form-group">
                                                <label for="floatingInputValue">Image</label>
                                                <input type="file" class="form-control" id="floatingInputValue"
                                                    accept="image/*" name="image[]" value="{{ $item->image }}"
                                                    placeholder="Image">
                                                <input type="hidden" name="image_id[]" value="{{ $item->id }}">
                                                <img src="{{ Storage::url($item->image) }}" alt="Image"
                                                    style="width: 100px; height: auto; margin-top:10px;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5 mt-4">
                                        <div class="form-group-div">
                                            <div class="form-group">
                                                <label for="floatingInputValue">Description*</label>
                                                <textarea name="description[]" id="content_{{ $key }}" cols="30" rows="10" required
                                                    placeholder="Description" class="form-control description">{{ $item->description }}</textarea>
                                                <span class="text-danger"
                                                    id="job_opportunity_description_{{ $key }}"></span>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($key == 0)
                                        <div class="col-xl-2 mt-4">
                                            <div class="btn-1">
                                                <button type="button" class="add-more"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-xl-2 mt-4">
                                            <div class="btn-1">
                                                <button type="button" class="remove"><i class="fas fa-minus"></i></button>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="col-xl-5 col-md-5 mt-4">
                                    <div class="form-group-div">
                                        <div class="form-group">
                                            <label for="floatingInputValue">Image*</label>
                                            <input type="file" class="form-control" id="floatingInputValue" required
                                                accept="image/*" name="image[]" value="" placeholder="Title">
                                            <input type="hidden" name="image_id[]" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 mt-4">
                                    <div class="form-group-div">
                                        <div class="form-group">
                                            <label for="floatingInputValue">Description*</label>
                                            <textarea name="description[]" id="card_description_0" cols="30" rows="10" placeholder="Description" required
                                                class="form-control description"></textarea>
                                            <span class="text-danger" id="job_opportunity_description_0"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-2 mt-4">
                                    <div class="btn-1">
                                        <button type="button" class="add-more"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-xl-12">
                            <div class="btn-1">
                                  <button type="submit" class="print_btn me-2 mt-2">Update</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>


    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.remove-image').click(function() {
                var id = $(this).data('id');
                var token = $("meta[name='csrf-token']").attr("content");
                // show confirm alert
                if (!confirm("Do you really want to delete this image?")) {
                    return false;
                } else {
                    $.ajax({
                        url: "{{ route('user.admin.organization.image.delete') }}",
                        type: 'GET',
                        data: {
                            "id": id,
                            "_token": token,
                        },
                        success: function() {
                            toastr.success('Image Deleted Successfully');
                            $('#' + id).remove();
                        }
                    });
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Initialize Summernote for existing description fields
            $(".description").each(function(index, element) {
                $(element).summernote({
                    placeholder: 'Description*',
                    tabsize: 2,
                    height: 400
                });
            });

            // Function to handle the add-more button click
            $(document).on("click", ".add-more", function() {
                var count = $("#add-more .col-xl-5").length;

                var html = `
                <div class="col-xl-5 col-md-5 mt-4">
                    <div class="form-group-div">
                        <div class="form-group">
                            <label for="floatingInputValue">Image*</label>
                            <input type="file" class="form-control" name="image[]" accept="image/*" placeholder="Title">
                            <span class="text-danger" id="job_opportunity_title_${count}"></span>
                            <input type="hidden" name="image_id[]" value="">
                        </div>
                    </div>
                </div>
                <div class="col-md-5 mt-4">
                    <div class="form-group-div">
                        <div class="form-group">
                            <label for="floatingInputValue">Description*</label>
                            <textarea name="description[]" cols="30" rows="10" class="form-control description" placeholder="Description*"></textarea>
                            <span class="text-danger" id="job_opportunity_description_${count}"></span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 mt-4">
                    <div class="btn-1">
                        <button type="button" class="remove"><i class="fas fa-minus"></i></button>
                    </div>
                </div>`;

                // Append the new fields
                $("#add-more").append(html);

                // Initialize Summernote for the newly added description field
                $("#add-more .description").last().summernote({
                    placeholder: 'Description*',
                    tabsize: 2,
                    height: 400
                });
            });

            // Remove functionality
            $(document).on("click", ".remove", function() {
                $(this).closest('.col-xl-2').prev('.col-md-5').remove(); // Remove description column
                $(this).closest('.col-xl-2').prev('.col-xl-5').remove(); // Remove image column
                $(this).closest('.col-xl-2').remove(); // Remove button column
            });
        });
    </script>
@endpush
