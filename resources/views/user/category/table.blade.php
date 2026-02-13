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

            <td>
                <div class="d-flex">
                    @if ($category->background_image)
                        <img src="{{ Storage::url($category->background_image) }}" alt="{{ $category->name }}"
                            style="width: 50px; height: 50px; object-fit: cover;">
                    @endif
                </div>
            </td>
            <td>
                <div class="d-flex">
                    @if ($category->size_measurements_image)
                        <img src="{{ Storage::url($category->size_measurements_image) }}" alt="{{ $category->name }}"
                            style="width: 50px; height: 50px; object-fit: cover;">
                    @endif
                </div>
            </td>
            <td>
                {{ $category->parent_tree ?: '—' }}
            </td>

            <td> {{ $category->name }}</td>
            <td> {{ $category->slug }}</td>
            <td>
                <div class="d-flex">
                    @if (auth()->user()->can('Edit Estore Category'))
                        <a href="{{ route('categories.edit', $category->id) }}" class="delete_icon">
                            <i class="fa-solid fa-edit"></i>
                        </a> &nbsp; &nbsp;
                    @endif
                    @if (auth()->user()->can('Delete Estore Category'))
                        @if ($category->main == 0)
                            <a href="javascript:void(0)" id="delete"
                                data-route="{{ route('categories.delete', $category->id) }}" class="delete_icon">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        @endif
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
    <tr class="toxic">
        <td colspan="7">
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
