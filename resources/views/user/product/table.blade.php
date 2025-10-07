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

            <td> {{ $product->category ? $product->category->name : '' }}</td>
            <td> {{ $product->slug }}</td>
            {{-- <td> {{ $product->price ? '$' . $product->price : '' }}</td> --}}
            {{-- <td> {{ $product->sku }}</td>  --}}
            {{-- <td> {{ $product->affiliate_link }}</td> --}}
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
                    @if (auth()->user()->can('Edit Estore Products'))
                        <a href="{{ route('products.edit', $product->id) }}" class="edit_icon">
                            <i class="fa-solid fa-edit"></i>
                        </a> &nbsp; &nbsp;
                    @endif
                    @if (auth()->user()->can('Edit Estore Products'))
                        @if ($product->product_type == 'simple')
                            <a href="{{ route('products.simple.stocks', $product->id) }}" class="edit_icon"
                                title="Simple Product Stock" data-bs-toggle="tooltip" data-bs-placement="top"
                              >
                                <i class="fa-solid fa-box-open"></i>
                            </a> &nbsp; &nbsp;
                        @else
                            <a href="{{ route('products.variations', $product->id) }}" class="edit_icon"
                                title="Product Variations" data-bs-toggle="tooltip" data-bs-placement="top"
                                >
                                <i class="fa-solid fa-th"></i>
                            </a> &nbsp; &nbsp;
                        @endif
                    @endif
                    @if (auth()->user()->can('Delete Estore Products'))
                        <a href="javascript:void(0)" id="delete"
                            data-route="{{ route('products.delete', $product->id) }}" class="delete_icon">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    @endif
                </div>


                @if (auth()->user()->isWarehouseAdmin())
                    <div class="d-flex">

                        <a href="{{ route('ware-houses.select-warehouse', $product->id) }}" class="delete_icon"
                            title="Product Variations">
                            <i class="fa-solid fa-th-list"></i>
                        </a> &nbsp; &nbsp;

                    </div>
                @endif


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
