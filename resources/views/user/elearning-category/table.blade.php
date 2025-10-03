@if (count($categories) > 0)
    @foreach ($categories as $key => $category)
        <tr>
            <td>{{ $categories->firstItem() + $key }}</td>
            <td>
                <div class="d-flex">
                    <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}"
                        style="width: 50px; height: 50px; object-fit: cover;">
                </div>
            </td>
            <td> {{ $category->name }}</td>
            <td> {{ $category->slug }}</td>
            <td>
                <div class="d-flex">
                    @if (auth()->user()->can('Edit Elearning Category'))
                        <a href="{{ route('elearning-categories.edit', $category->id) }}" class="delete_icon">
                            <i class="fa-solid fa-edit"></i>
                        </a> &nbsp; &nbsp;
                    @endif
                    @if (auth()->user()->can('Delete Elearning Category'))
                        @if ($category->main == 0)
                            <a href="javascript:void(0)" id="delete"
                                data-route="{{ route('elearning-categories.delete', $category->id) }}"
                                class="delete_icon">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        @endif
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
    <tr class="toxic">
        <td colspan="5">
            <div class="d-flex justify-content-center">
                {!! $categories->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr>
        <td colspan="5" class="text-center">No data found</td>
    </tr>
@endif
