@extends('user.layouts.master')
@section('title')
    Cms List - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">
            <form>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="row mb-3">
                                    <div class="col-md-10">
                                        {{-- <h3 class="mb-3">Ecclesias List</h3> --}}
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-md-10">
                                        <h3 class="mb-3 float-left">CMS List</h3>
                                    </div>
                                    <div class="col-md-2 ">
                                        <a href="{{ route('user.cms.create') }}" class="btn btn-primary w-100"><i
                                                class="fa-solid fa-plus"></i> Create Page</a>
                                    </div>
                                    {{-- <div class="col-lg-4">
                                        <div class="search-field float-right">
                                            <input type="text" name="search" id="search" placeholder="search..."
                                                required class="form-control">
                                            <button class="submit_search" id="search-button"> <span class=""><i
                                                        class="fa fa-search"></i></span></button>
                                        </div>
                                    </div> --}}
                                </div>
                                <div class="table-responsive">
                                    <table class="table align-middle bg-white color_body_text">
                                        <thead class="color_head">
                                            <tr>
                                                <th>ID </th>
                                                <th>CMS Name</th>
                                                <th>Update</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    1
                                                </td>
                                                <td>Home Page</td>
                                                <td>
                                                    <a href="{{ route('user.cms.edit', ['page' => 'home']) }}"
                                                        class="edit_icon me-2"> <i class="ti ti-edit"></i></a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    2
                                                </td>
                                                <td>Footer Section</td>
                                                <td>
                                                    <a href="{{ route('user.cms.edit', ['page' => 'footer']) }}"
                                                        class="edit_icon me-2"> <i class="ti ti-edit"></i></a>
                                                </td>
                                            </tr>
                                            @php
                                                $count = 3;
                                            @endphp
                                            @if (count($pages) > 0)
                                                @foreach ($pages as $key => $page)
                                                    <tr>
                                                        <td>
                                                            {{ $count++ }}
                                                        </td>
                                                        <td>{{ $page->page_name }}</td>
                                                        <td>
                                                            <div class="d-flex">
                                                                <a href="{{ route('user.cms.edit', ['page' => $page->slug]) }}"
                                                                    class="edit_icon me-2"> <i class="ti ti-edit"></i></a>

                                                                <a href="javascript:void(0);"
                                                                    data-route="{{ route('user.cms.delete', ['id' => $page->id]) }}"
                                                                    class="delete_icon" id="delete"> <i class="fa-solid fa-trash"></i> </a>
                                                            </div>


                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).on('click', '#delete', function(e) {
        swal({
                title: "Are you sure?",
                text: "To remove this page from the CMS",
                type: "warning",
                confirmButtonText: "Yes",
                showCancelButton: true
            })
            .then((result) => {
                if (result.value) {
                    window.location = $(this).data('route');
                } else if (result.dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        'Your stay here :)',
                        'error'
                    )
                }
            })
    });
</script>
@endpush
