<h4>
    @if ($category_id != '')
        @if (is_array($category))
            {{ implode(', ', array_column($category, 'name')) }}
        @else
            {{ $category['name'] ?? '' }}
        @endif
    @else
        All Products
    @endif
    ({{ $products_count  ?? '' }} Products Found)
</h4>
