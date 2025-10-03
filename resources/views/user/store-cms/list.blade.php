@extends('user.layouts.master')
@section('title')
    Store Cms List - {{ env('APP_NAME') }}
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
                                <div class="row mb-3">
                                    <div class="col-12">
                                        @php
                                            $requiredSlugs = [
                                                'privacy-policy',
                                                'terms-and-condition',
                                                'products',
                                                'product-details',
                                                'cart',
                                                'checkout',
                                                'my-orders',
                                                'order-details',
                                                'order-success',
                                                'order-tracking',
                                                'wishlist',
                                                'profile',
                                                'change-password',
                                                'product-not-available',
                                            ];
                                            $existing = isset($pages) ? $pages->pluck('slug')->toArray() : [];
                                            $missing = array_values(array_diff($requiredSlugs, $existing));
                                        @endphp
                                        @if (count($missing) > 0)
                                            <div class="alert alert-secondary">
                                                <strong>Page Banner Status:</strong>
                                                <div class="row mt-2">
                                                    @foreach ($requiredSlugs as $slug)
                                                        <div class="col-md-3 col-sm-6 mb-2">
                                                            @if (in_array($slug, $existing))
                                                                <span class="badge bg-success">{{ $slug }} ✓</span>
                                                            @else
                                                                <span class="badge bg-warning text-dark">{{ $slug }}
                                                                    missing</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <small class="text-muted">Create a CMS page with any missing slug and upload
                                                    a
                                                    Page Banner Image to set that page's background. Pages without a CMS
                                                    banner
                                                    fall back to the Home CMS banner, then the default image.</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-md-10">
                                        <h3 class="mb-3 float-left">Store CMS List</h3>
                                    </div>
                                    <div class="col-md-2 ">
                                        @if (auth()->user()->can('Create Estore CMS'))
                                            <a href="{{ route('user.store-cms.create') }}" class="btn btn-primary w-100"><i
                                                    class="fa-solid fa-plus"></i> Create Page</a>
                                        @endif
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
                                                    @if (auth()->user()->can('Edit Estore CMS'))
                                                        <a href="{{ route('user.store-cms.edit', ['page' => 'home']) }}"
                                                            class="edit_icon me-2"> <i class="ti ti-edit"></i></a>
                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    2
                                                </td>
                                                <td>Footer Section</td>
                                                <td>
                                                    @if (auth()->user()->can('Edit Estore CMS'))
                                                        <a href="{{ route('user.store-cms.edit', ['page' => 'footer']) }}"
                                                            class="edit_icon me-2"> <i class="ti ti-edit"></i></a>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    3
                                                </td>
                                                <td>Contact Page</td>
                                                <td>
                                                    @if (auth()->user()->can('Edit Estore CMS'))
                                                        <a href="{{ route('user.store-cms.contact') }}"
                                                            class="edit_icon me-2">
                                                            <i class="ti ti-edit"></i></a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @php
                                                $count = 4;
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
                                                                @if (auth()->user()->can('Edit Estore CMS'))
                                                                    <a href="{{ route('user.store-cms.edit', ['page' => $page->slug]) }}"
                                                                        class="edit_icon me-2"> <i
                                                                            class="ti ti-edit"></i></a>
                                                                @endif
                                                                @if (auth()->user()->can('Delete Estore CMS'))
                                                                    <a href="javascript:void(0);"
                                                                        data-route="{{ route('user.store-cms.delete', ['id' => $page->id]) }}"
                                                                        class="delete_icon" id="delete"> <i
                                                                            class="fa-solid fa-trash"></i> </a>
                                                                @endif
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
