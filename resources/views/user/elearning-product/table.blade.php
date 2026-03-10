@if (count($products) > 0)
    @foreach ($products as $key => $product)
        <tr>
            <td>{{ $products->firstItem() + $key }}</td>
            <td>
                <div class="d-flex align-items-center">
                    @if (isset($product->main_image) && $product->main_image != null)
                        <img src="{{ Storage::url($product->main_image) }}" class="product-img-card" alt="product"
                            onerror="this.onerror=null;this.src='{{ asset('ecom_assets/images/no-image.png') }}';">
                    @else
                        <img src="{{ asset('ecom_assets/images/no-image.png') }}" class="product-img-card" alt="no-image">
                    @endif
                </div>
            </td>
            <td>
                <div class="truncate-150" title="{{ $product->name }}">
                    {{ $product->name }}
                </div>
            </td>
            <td>
                <span class="badge-custom badge-info">{{ $product->category ? $product->category->name : 'N/A' }}</span>
            </td>
            <td>
                <span class="truncate-120" title="{{ $product->subcategory ? $product->subcategory->name : '--' }}">
                    {{ $product->subcategory ? $product->subcategory->name : '--' }}
                </span>
            </td>
            <td>
                <div class="truncate-120"
                    title="{{ $product->elearningTopic ? $product->elearningTopic->topic_name : '' }}">
                    {{ $product->elearningTopic ? $product->elearningTopic->topic_name : 'No Topic' }}
                </div>
            </td>
            <td>
                <div class="truncate-120 text-muted" title="{{ $product->slug }}">
                    {{ $product->slug }}
                </div>
            </td>
            <td>
                <a href="{{ $product->affiliate_link }}" target="_blank" class="affiliate-link-item"
                    title="{{ $product->affiliate_link }}">
                    {{ $product->affiliate_link }}
                </a>
            </td>
            <td>
                @if ($product->status == 1)
                    <span class="badge-custom badge-active">Active</span>
                @else
                    <span class="badge-custom badge-inactive">Inactive</span>
                @endif
            </td>
            <td>
                @if ($product->feature_product == 1)
                    <span class="badge-custom badge-yes">Yes</span>
                @else
                    <span class="badge-custom badge-no">No</span>
                @endif
            </td>
            <td class="info-cell">
                <b>{{ $product->user?->full_name ?? 'Bot' }}</b>
                <span>{{ $product->created_at->format('d M Y') }}</span>
            </td>
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
