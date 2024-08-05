@extends('user.layouts.master')
@section('title')
    Ecclesias List - {{ env('APP_NAME') }}
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
                                    <div class="col-md-2 float-right">
                                        <a href="{{ route('ecclesias.create') }}" class="btn btn-primary w-100">+ Add
                                            ecclesia</a>
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-md-8">
                                        <h3 class="mb-3 float-left">Ecclesia List</h3>
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
                                                <th>Ecclesia Name</th>
                                                <th>
                                                    Country
                                                </th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($ecclesias) > 0)
                                            @foreach ($ecclesias as $key => $ecclesia)
                                                <tr>
                                                    <td>
                                                        {{ $ecclesias->firstItem() + $key }}
                                                    </td>
                                                    <td>{{ $ecclesia->name }}</td>
                                                    <td>
                                                        {{ $ecclesia->country ? $ecclesia->countryName->name : '-' }}
                                                    </td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <a href="{{route('ecclesias.edit', Crypt::encrypt($ecclesia->id))}}" class="edit_icon me-2">
                                                                <i class="ti ti-edit"></i>
                                                            </a>
                                                            <a href="javascript:void(0);" data-route="{{ route('ecclesias.delete', Crypt::encrypt($ecclesia->id)) }}" class="delete_icon" id="delete">
                                                                <i class="ti ti-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            {{-- pagination --}}
                                            <tr class="toxic">
                                                <td colspan="4" >
                                                    <div class="d-flex justify-content-center">
                                                        {!! $ecclesias->links() !!}
                                                    </div>
                                                </td>
                                            </tr>
                                            @else
                                            <tr class="toxic">
                                                <td colspan="4" class="text-center">No Data Found</td>
                                            </tr>
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
                    text: "To delete this Ecclesia",
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
