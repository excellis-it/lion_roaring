<div class="filter_resilt">
    <div class="row">
        <div class="col-md-6">
            <div class="filter_res_text" id="count-product">
                @include('ecom.partials.count-product', [
                    'products_count' => $products_count,
                    'category' => $category,
                ])

            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <div class="">
                    <select class="latest_filter" name="latest_filter" id="latest_filter">
                        <option value="Latest" selected>Latest</option>
                        <option value="A to Z">A to Z</option>
                        <option value="Z to A">Z to A</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mx-3" id="products">
    @include('ecom.partials.product-item', ['products' => $products, 'products_count' => $products_count])
    <div id="loading" style="display: none; ">
        <div style="justify-content: center; align-items: center; display:flex">
            <img src="{{ asset('ecom_assets/images/loader.gif') }}" alt="Loading..." height="50" width="50" />
        </div>
    </div>
</div>
