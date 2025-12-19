@extends('user.layouts.master')
@section('title')
    Topics List - {{ env('APP_NAME') }}
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
                                    </div>
                                    <div class="col-md-2 float-right">
                                        @if (Auth::user()->can('Create Topic'))
                                            <a href="{{ route('topics.create') }}" class="btn btn-primary w-100">+ Add
                                                Topic</a>
                                        @endif

                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-md-8">
                                        <h3 class="mb-3 float-left">Topic List</h3>
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
                                                <th>Topic</th>
                                                <th>
                                                    Education Type
                                                </th>
                                                <th>
                                                    Country
                                                </th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($topics) > 0)
                                                @foreach ($topics as $key => $topic)
                                                    <tr>
                                                        <td>
                                                            {{ $topics->firstItem() + $key }}
                                                        </td>
                                                        <td>{{ $topic->topic_name }}</td>
                                                        <td>{{ $topic->education_type }}</td>
                                                        <td>{{ $topic->country->name ?? '' }}</td>
                                                        <td>
                                                            <div class="d-flex">
                                                                @if (Auth::user()->can('Edit Topic'))
                                                                    <a href="{{ route('topics.edit', Crypt::encrypt($topic->id)) }}"
                                                                        class="edit_icon me-2">
                                                                        <i class="ti ti-edit"></i>
                                                                    </a>
                                                                @endif
                                                                @if (Auth::user()->can('Delete Topic'))
                                                                    <a href="javascript:void(0);"
                                                                        data-route="{{ route('topics.delete', Crypt::encrypt($topic->id)) }}"
                                                                        class="delete_icon" id="delete">
                                                                        <i class="ti ti-trash"></i>
                                                                    </a>
                                                                @endif

                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                {{-- pagination --}}
                                                <tr class="toxic">
                                                    <td colspan="5">
                                                        <div class="d-flex justify-content-center">
                                                            {!! $topics->links() !!}
                                                        </div>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr class="toxic">
                                                    <td colspan="5" class="text-center">No Data Found</td>
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
                    text: "To delete this topic.",
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
