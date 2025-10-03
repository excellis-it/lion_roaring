@if (count($products) > 0)
    @foreach ($products as $key => $product)
        <tr>
            <td>{{ $products->firstItem() + $key }}</td>
            <td>
                <div class="d-flex">
                    @if (isset($product->main_image) && $product->main_image != null)
                        <img src="{{ Storage::url($product->main_image) }}" alt="{{ $product->main_image }}"
                            style="width: 50px; height: 50px; object-fit: cover;">
                    @endif
                </div>
            </td>
            <td> {{ $product->name }}</td>
            <td> {{ $product->slug }}</td>
            <td> {{ $product->category ? $product->category->name : '' }}</td>
            {{-- <td> {{ $product->price  ? '$' . $product->price : '' }}</td>
            <td> {{ $product->quantity }}</td>
            <td> {{ $product->sku }}</td> --}}
            <td> {{ $product->affiliate_link }}</td>
            {{-- status --}}
            <td>
                @if ($product->status == 1)
                    <span class=" badge-success">Active</span>
                @else
                    <span class=" badge-danger">Inactive</span>
                @endif
            </td>
            {{-- feature_product --}}
            <td>
                @if ($product->feature_product == 1)
                    <span class=" badge-success">Yes</span>
                @else
                    <span class=" badge-danger">No</span>
                @endif
            </td>
            <td> {{ $product->created_at->format('d M Y') }}</td>
            <td>
                <div class="d-flex">
                    @if (auth()->user()->can('Edit Elearning Product'))
                        <a href="{{ route('elearning.edit', $product->id) }}" class="delete_icon">
                            <i class="fa-solid fa-edit"></i>
                        </a> &nbsp; &nbsp;
                    @endif
                    @if (auth()->user()->can('Delete Elearning Product'))
                        <a href="javascript:void(0)" id="delete"
                            data-route="{{ route('elearning.delete', $product->id) }}" class="delete_icon">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
    <tr class="toxic">
        <td colspan="12">
            <div class="d-flex justify-content-center">
                {!! $products->links() !!}
            </div>
        </td>
    </tr>
@else
    <tr>
        <td colspan="12" class="text-center">No data found</td>
    </tr>
@endif
