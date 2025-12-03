@extends('user.layouts.master')
@section('title')
    {{ env('APP_NAME') }} | Update Service Page
@endsection
@push('styles')
@endpush


@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-0">Update Service</h3>
                    <p class="text-muted small mb-0">Manage service details</p>
                </div>
            </div>

            <form action="{{ route('services.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                @if (isset($services) && count($services) > 0)
                    <input type="hidden" name="column_count" id="column_count" value="{{ count($services) }}">
                @else
                    <input type="hidden" name="column_count" id="column_count" value="1">
                @endif
                <input type="hidden" name="our_organization_id" value="{{ $our_organization_id }}">
                <div class="sales-report-card-wrap mt-5">
                    <div class="row count-class" id="add-more">
                        @if (isset($services) && count($services) > 0)
                            @foreach ($services as $key => $item)
                                <div class="col-xl-5 col-md-5 mt-4">
                                    <div class="form-group-div">
                                        <div class="form-group">
                                            {{-- meta title --}}
                                            <label for="floatingInputValue">Image</label>
                                            <input type="file" class="form-control" id="floatingInputValue"
                                                accept="image/*" name="image[]" value="{{ $item->image }}"
                                                placeholder="Image">
                                            <input type="hidden" name="image_id[]" value="{{ $item->id }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 mt-4">
                                    <div class="form-group-div">
                                        <div class="form-group">
                                            {{-- banner_title --}}
                                            <label for="floatingInputValue">Content</label>
                                            <textarea name="content[]" id="content_{{ $key }}" cols="30" rows="10" placeholder="Content"
                                                class="form-control content">{{ $item->content }}</textarea>
                                            <span class="text-danger" id="job_opportunity_description_0"></span>
                                        </div>
                                    </div>
                                </div>
                                @if ($key == 0)
                                    <div class="col-xl-2 mt-4">
                                        <div class="btn-1">
                                            <button type="button" class="add-more"><i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-xl-2 mt-4">
                                        <div class="btn-1">
                                            <button type="button" class="remove"><i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="col-xl-5 col-md-5 mt-4">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- meta title --}}
                                        <label for="floatingInputValue"> Image</label>
                                        <input type="file" class="form-control" id="floatingInputValue" accept="image/*"
                                            name="image[]" value="" placeholder=" Title">
                                        <input type="hidden" name="image_id[]" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 mt-4">
                                <div class="form-group-div">
                                    <div class="form-group">
                                        {{-- banner_title --}}
                                        <label for="floatingInputValue"> Content</label>
                                        <textarea name="content[]" id="content_0" cols="30" rows="10" placeholder="Content"
                                            class="form-control content"></textarea>
                                        <span class="text-danger" id="job_opportunity_description_0"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 mt-4">
                                <div class="btn-1">
                                    <button type="button" class="add-more"><i class="fas fa-plus"></i> </button>
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
                        url: "{{ route('organization.image.delete') }}",
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
            // Initialize Summernote for existing elements
            $(".content").each(function(index, element) {
                $(element).summernote({
                    placeholder: 'Content',
                    tabsize: 2,
                    height: 400
                });
            });

            $(document).on("click", ".add-more", function() {
                var count = $("#add-more .col-xl-5").length;
                var column_count = $('#column_count').val();
                column_count = parseInt(column_count) + 1;
                $('#column_count').val(column_count);

                var html = `
            <div class="col-xl-5 col-md-5 mt-4">
                <div class="form-group-div">
                    <div class="form-group">
                        <label for="floatingInputValue"> Image</label>
                        <input type="file" class="form-control" name="image[]" accept="image/*" placeholder="Title">
                        <span class="text-danger" id="job_opportunity_title_${count}"></span>
                        <input type="hidden" name="image_id[]" value="">
                    </div>
                </div>
            </div>
            <div class="col-md-5 mt-4">
                <div class="form-group-div">
                    <div class="form-group">
                        <label for="floatingInputValue"> Content</label>
                        <textarea name="content[]" cols="30" rows="10" class="form-control content" placeholder="Content"></textarea>
                        <span class="text-danger" id="job_opportunity_description_${count}"></span>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 mt-4">
                <div class="btn-1">
                    <button type="button" class="remove"><i class="fas fa-minus"></i> </button>
                </div>
            </div>`;

                // Append the new elements
                $("#add-more").append(html);

                // Initialize Summernote only on the newly added textarea
                $("#add-more .content").last().summernote({
                    placeholder: 'Content',
                    tabsize: 2,
                    height: 400
                });
            });

            $(document).on("click", ".remove", function() {
                // Traverse from the remove button to the parent `.col-xl-2` and find its previous siblings
                $(this).closest('.col-xl-2').prev('.col-md-5').remove(); // Remove content column
                $(this).closest('.col-xl-2').prev('.col-xl-5').remove(); // Remove image column
                $(this).closest('.col-xl-2').remove(); // Remove the button column

                // Decrement the column count
                var column_count = $('#column_count').val();
                $('#column_count').val(column_count - 1);
            });

        });
    </script>
@endpush
