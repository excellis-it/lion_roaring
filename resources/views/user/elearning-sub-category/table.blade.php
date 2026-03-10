@if (count($subcategories) > 0)
    @foreach ($subcategories as $key => $subcategory)
        <tr>
            <td>{{ $subcategories->firstItem() + $key }}</td>
            <td>
                <div class="d-flex">
                    @if ($subcategory->image)
                        <img src="{{ Storage::url($subcategory->image) }}" alt="{{ $subcategory->name }}"
                            style="width: 50px; height: 50px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('ecom_assets/images/no-image.png') }}';">
                    @else
                        <img src="{{ asset('ecom_assets/images/no-image.png') }}" alt="no-image"
                            style="width: 50px; height: 50px; object-fit: cover;">
                    @endif
                </div>
            </td>
            <td> {{ $subcategory->category->name ?? 'N/A' }}</td>
            <td> {{ $subcategory->name }}</td>
            <td> {{ $subcategory->slug }}</td>
            <td>
                <div class="d-flex">
                    @if (auth()->user()->can('Edit Elearning Sub Category'))
                        <a href="{{ route('elearning-sub-categories.edit', $subcategory->id) }}" class="delete_icon">
                            <i class="fa-solid fa-edit"></i>
                        </a> &nbsp; &nbsp;
                    @endif
                    @if (auth()->user()->can('Delete Elearning Sub Category'))
                        <a href="javascript:void(0)" id="delete"
                            data-route="{{ route('elearning-sub-categories.delete', $subcategory->id) }}"
                            data-id="{{ $subcategory->id }}" class="delete_icon">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
    <tr class="toxic">
        <td colspan="6">
            <div class="d-flex justify-content-center">
                {!! $subcategories->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr>
        <td colspan="6" class="text-center">No data found</td>
    </tr>
@endif
