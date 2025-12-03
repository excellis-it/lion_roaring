@extends('admin.layouts.master')
@section('title')
    All Gallery Details - {{ env('APP_NAME') }}
@endsection
@push('styles')
    <style>
        .dataTables_filter {
            margin-bottom: 10px !important;
        }
    </style>
@endpush
@section('head')
    All Gallery Details
@endsection
@section('create_button')
    @if (auth()->user()->can('Create Gallery'))
        <a href="{{ route('gallery.create') }}" class="btn btn-primary">+ Create New Gallery</a>
    @endif
@endsection
@section('content')
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div class="main-content">
        <div class="inner_page">

            <div class="card table_sec stuff-list-table">
                <div class="row justify-content-end">
                    <div class="col-md-6">
                        <div class="row g-1 justify-content-end">
                            {{-- <div class="col-md-8 pr-0">
                                <div class="search-field prod-search">
                                    <input type="text" name="search" id="search" placeholder="search..." required
                                        class="form-control">
                                    <a href="javascript:void(0)" class="prod-search-icon"><i
                                            class="ph ph-magnifying-glass"></i></a>
                                </div>
                            </div> --}}
                            <div class="col-md-4">
                                {{--
                                <select name="content_country_code" id="content_country_code" class="form-control">
                                    @foreach (\App\Models\Country::all() as $country)
                                        <option value="{{ $country->code }}"
                                            {{ request()->get('content_country_code', 'US') == $country->code ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="country_code">Content Country</label> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive" id="gallery-data">
                    <table class="table table-bordered" class="display">
                        <thead>
                            <tr>
                                <th class="sorting" data-tippy-content="Sort by Id" data-sorting_type="asc"
                                    data-column_name="id" style="cursor: pointer">Id<span id="id_icon"></span>
                                </th>
                                <th>
                                    Image</th>
                                <th>Content Country</th>

                            </tr>
                        </thead>
                        <tbody>
                            @if (count($gallery) > 0)
                                @foreach ($gallery as $key => $item)
                                    <tr>
                                        <td> {{ ($gallery->currentPage() - 1) * $gallery->perPage() + $loop->index + 1 }}
                                        </td>

                                        <td><a href="{{ Storage::url($item->image) }}" target="_blank"><img
                                                    src="{{ Storage::url($item->image) }}" alt="gallery"
                                                    style="width: 30%; height: 100px; border-radius:50%"></a></td>
                                        <td>{{ $item->country?->name ?? '' }}</td>
                                        <td>
                                            <div class="edit-1 d-flex align-items-center justify-content-center">
                                                @if (auth()->user()->can('Edit Gallery'))
                                                    <a title="Edit " href="{{ route('gallery.edit', $item->id) }}">
                                                        <span class="edit-icon"><i class="ph ph-pencil-simple"></i></span>
                                                    </a>
                                                @endif
                                                @if (auth()->user()->can('Delete Gallery'))
                                                    <a title="Delete " data-route="{{ route('gallery.delete', $item->id) }}"
                                                        href="javascript:void(0);" id="delete">
                                                        <span class="trash-icon"><i class="ph ph-trash"></i></span>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr style="box-shadow: none;">
                                    <td colspan="3">
                                        <div class="d-flex justify-content-center">
                                            {!! $gallery->links() !!}
                                        </div>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="3" class="text-center">No Gallery Found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '#delete', function(e) {
            swal({
                    title: "Are you sure?",
                    text: "To delete this gallery.",
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

        $(document).ready(function() {
            // on change content_country_code should reload the page with the selected country code as query param
            $('#content_country_code').on('change', function() {
                var country_code = $(this).val();
                window.location.href = "{{ route('gallery.index') }}" + "?content_country_code=" +
                    country_code;
            });
        });
    </script>
@endpush
