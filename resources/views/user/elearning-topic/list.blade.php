@extends('user.layouts.master')
@section('title')
    Elearning Topics List - {{ env('APP_NAME') }}
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
                                        @if (auth()->user()->can('Create Elearning Topic'))
                                            <a href="{{ route('elearning-topics.create') }}" class="btn btn-primary w-100">+
                                                Add
                                                Elearning Topic</a>
                                        @endif

                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-md-8">
                                        <h3 class="mb-3 float-left">Elearning Topic List</h3>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table align-middle bg-white color_body_text">
                                        <thead class="color_head">
                                            <tr>
                                                <th>ID </th>
                                                <th>Topic</th>
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
                                                        <td>
                                                            <div class="d-flex">
                                                                @if (auth()->user()->can('Edit Elearning Topic'))
                                                                    <a href="{{ route('elearning-topics.edit', Crypt::encrypt($topic->id)) }}"
                                                                        class="edit_icon me-2">
                                                                        <i class="ti ti-edit"></i>
                                                                    </a>
                                                                @endif
                                                                @if (auth()->user()->can('Delete Elearning Topic'))
                                                                    <a href="javascript:void(0);"
                                                                        data-route="{{ route('elearning-topics.delete', Crypt::encrypt($topic->id)) }}"
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
                                                    <td colspan="3">
                                                        <div class="d-flex justify-content-center">
                                                            {!! $topics->links() !!}
                                                        </div>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr class="toxic">
                                                    <td colspan="3" class="text-center">No Data Found</td>
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
                    text: "To delete this elearning topic.",
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
