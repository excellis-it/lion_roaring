@extends('user.layouts.master')

@section('title')
    E-Store Warehouse Products List
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <div class="row mb-3 align-items-center">
                <div class="col-md-7">
                    <h3 class="mb-0"><span style="color: #f6bc41; font-weight:600;">{{$wareHouse->name ?? '-'}}</span> Products Stocks</h3>
                </div>
                <div class="col-md-3 text-md-end mt-2 mt-md-0">
                    <div class="form-check form-switch d-inline-flex align-items-center gap-2 mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" id="showActiveOnly">
                        <label class="form-check-label" for="showActiveOnly">Show active only</label>
                    </div>
                </div>
                <div class="col-md-2 mt-2 mt-md-0">
                    <a href="{{ route('ware-houses.index') }}" class="btn btn-primary w-100"><i
                            class="fa-solid fa-arrow-left"></i>
                        Back</a>

                </div>
            </div>
            <div class="table-responsive">
                <table class="table" id="warehouseStockTable">
                    <thead>
                        <tr>
                            <th>SR.</th>
                            <th>SKU</th>
                            <th>Product Name</th>
                            <th>Color</th>
                            <th>Size</th>
                            <th>Price</th>
                            <th>Global Stock Quantity</th>
                            <th>Warehouse Stock Quantity</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($warehouseProducts as $key => $warehouseProduct)
                            @php $isActive = ($warehouseProduct->warehouse_quantity ?? 0) > 0; @endphp
                            <tr class="warehouse-stock-row" data-active="{{ $isActive ? 1 : 0 }}">
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $warehouseProduct->sku ?? '' }}</td>
                                <td>{{ $warehouseProduct->product?->name ?? '-' }}</td>
                                <td>{{ $warehouseProduct->colorDetail?->color_name ?? '-' }}</td>
                                <td>{{ $warehouseProduct->sizeDetail?->size ?? '-' }}</td>
                                <td>{{ $warehouseProduct->price ?? '' }}</td>
                                <td>{{ $warehouseProduct->stock_quantity ?? '' }}</td>
                                <td>{{ $warehouseProduct->warehouse_quantity ?? '' }}</td>
                                <td>
                                    @if ($isActive)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Passive</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p id="noActiveRows" class="text-muted text-center py-3 d-none">No active products in this warehouse.</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const toggle = document.getElementById('showActiveOnly');
            const rows = document.querySelectorAll('#warehouseStockTable .warehouse-stock-row');
            const emptyMsg = document.getElementById('noActiveRows');

            function apply() {
                const activeOnly = toggle.checked;
                let visible = 0;
                rows.forEach(function (row) {
                    const show = !activeOnly || row.dataset.active === '1';
                    row.style.display = show ? '' : 'none';
                    if (show) visible++;
                });
                emptyMsg.classList.toggle('d-none', visible !== 0);
            }

            toggle.addEventListener('change', apply);
        })();
    </script>
@endpush
