@extends('user.layouts.master')

@section('title')
    E-Store Settings Management
@endsection

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <div class="row">
                <div class="col-md-12">
                    <h4 class="title mb-5">E-Store Settings</h4>
                    <form action="{{ route('store-settings.update', $storeSetting->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @php $hasShippingRules = !empty($storeSetting->shipping_rules) && is_array($storeSetting->shipping_rules) && count($storeSetting->shipping_rules) > 0; @endphp

                        <div class="row">
                            <!-- Shipping Cost -->
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="shipping_cost">Shipping Cost</label>
                                    <input type="number" step="any" name="shipping_cost" id="shipping_cost"
                                        class="form-control @error('shipping_cost') is-invalid @enderror"
                                        value="{{ old('shipping_cost', $storeSetting->shipping_cost) }}"
                                        @if ($hasShippingRules) disabled @endif>

                                    <small id="flat-rates-note" class="text-muted"
                                        style="display: {{ $hasShippingRules ? 'block' : 'none' }};">
                                        Legacy flat rates are ignored while quantity-based shipping rules are configured.
                                    </small>

                                    @error('shipping_cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Delivery Cost -->
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="delivery_cost">Delivery Cost</label>
                                    <input type="number" step="any" name="delivery_cost" id="delivery_cost"
                                        class="form-control @error('delivery_cost') is-invalid @enderror"
                                        value="{{ old('delivery_cost', $storeSetting->delivery_cost) }}"
                                        @if ($hasShippingRules) disabled @endif>

                                    @error('delivery_cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tax Percentage -->
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="tax_percentage">Tax Percentage (%)</label>
                                    <input type="number" step="any" name="tax_percentage" id="tax_percentage"
                                        class="form-control @error('tax_percentage') is-invalid @enderror"
                                        value="{{ old('tax_percentage', $storeSetting->tax_percentage) }}">
                                    @error('tax_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Pickup Available -->
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="is_pickup_available">Pickup Available</label>
                                    <select name="is_pickup_available" id="is_pickup_available"
                                        class="form-control @error('is_pickup_available') is-invalid @enderror">
                                        <option value="1"
                                            {{ old('is_pickup_available', $storeSetting->is_pickup_available) ? 'selected' : '' }}>
                                            Yes</option>
                                        <option value="0"
                                            {{ !old('is_pickup_available', $storeSetting->is_pickup_available) ? 'selected' : '' }}>
                                            No</option>
                                    </select>
                                    @error('is_pickup_available')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Credit Card Percentage -->
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="credit_card_percentage">Credit Card Percentage (%)</label>
                                    <input type="number" step="any" name="credit_card_percentage"
                                        id="credit_card_percentage"
                                        class="form-control @error('credit_card_percentage') is-invalid @enderror"
                                        value="{{ old('credit_card_percentage', $storeSetting->credit_card_percentage) }}">
                                    @error('credit_card_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Max Refundable Days --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="refund_max_days">Refund Period (in days)</label>
                                    <input type="number" step="1" name="refund_max_days" id="refund_max_days"
                                        class="form-control @error('refund_max_days') is-invalid @enderror"
                                        value="{{ old('refund_max_days', $storeSetting->refund_max_days) }}">
                                    @error('refund_max_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Shipping Rules (quantity based) --}}
                            <div class="col-12 mb-3">
                                <div class="box_label">
                                    <p>Quantity-based Shipping Rules</p>
                                    <p class="small text-muted mt-1">Define shipping/delivery cost buckets based on total
                                        ordered
                                        item quantity. Leave empty to use legacy flat rates.</p>

                                    <div class="table-responsive">
                                        <table class="table table-sm" id="shipping-rules-table">
                                            <thead>
                                                <tr>
                                                    <th>Min Qty</th>
                                                    <th>Max Qty (optional)</th>
                                                    <th>Shipping Cost ($)</th>
                                                    <th>Delivery Cost ($)</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>

                                    <input type="hidden" name="shipping_rules" id="shipping_rules_input"
                                        value="{{ old('shipping_rules') ? json_encode(old('shipping_rules')) : json_encode($storeSetting->shipping_rules ?? []) }}">

                                    <button type="button" class="btn btn-sm btn-primary"
                                        id="add-shipping-rule">Add Rule</button>
                                    @error('shipping_rules')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Buttons -->
                            @if (auth()->user()->can('Edit Estore Settings'))
                                <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">

                                    <button type="submit" class="print_btn me-2">Save</button>

                                    <a href="{{ route('store-settings.index') }}" class="print_btn print_btn_vv">Cancel</a>
                                </div>
                            @endif
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            const rulesInput = document.getElementById('shipping_rules_input');
            const tableBody = document.querySelector('#shipping-rules-table tbody');
            let rules = [];

            try {
                rules = JSON.parse(rulesInput.value || '[]') || [];
            } catch (e) {
                rules = [];
            }

            function render() {
                tableBody.innerHTML = '';
                rules.forEach((r, idx) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><input type="number" min="0" step="1" class="form-control form-control-sm min-qty" value="${r.min_qty ?? 0}"></td>
                        <td><input type="number" min="0" step="1" class="form-control form-control-sm max-qty" value="${r.max_qty ?? ''}"></td>
                        <td><input type="number" min="0" step="0.01" class="form-control form-control-sm shipping-cost" value="${r.shipping_cost ?? 0}"></td>
                        <td><input type="number" min="0" step="0.01" class="form-control form-control-sm delivery-cost" value="${r.delivery_cost ?? 0}"></td>
                        <td><button type="button" class="btn btn-sm btn-primary remove-row"><span class="fa fa-trash"></span></button></td>
                    `;
                    tableBody.appendChild(tr);

                    tr.querySelector('.remove-row').addEventListener('click', function() {
                        rules.splice(idx, 1);
                        render();
                    });

                    ['min-qty', 'max-qty', 'shipping-cost', 'delivery-cost'].forEach(cls => {
                        tr.querySelector('.' + cls).addEventListener('input', function() {
                            const rowIdx = Array.from(tableBody.children).indexOf(tr);
                            const min = parseInt(tr.querySelector('.min-qty').value || 0, 10);
                            const maxVal = tr.querySelector('.max-qty').value;
                            const max = maxVal === '' ? null : parseInt(maxVal, 10);
                            const ship = parseFloat(tr.querySelector('.shipping-cost').value ||
                                0);
                            const del = parseFloat(tr.querySelector('.delivery-cost').value ||
                                0);
                            rules[rowIdx] = {
                                min_qty: min,
                                max_qty: max,
                                shipping_cost: ship,
                                delivery_cost: del
                            };
                            updateHidden();
                        });
                    });
                });
                updateHidden();
            }

            function updateHidden() {
                // sort by min_qty asc
                rules.sort((a, b) => (a.min_qty || 0) - (b.min_qty || 0));
                rulesInput.value = JSON.stringify(rules);
                // toggle flat rates inputs when rules exist
                const hasRules = rules.length > 0;
                const shipInput = document.getElementById('shipping_cost');
                const delInput = document.getElementById('delivery_cost');
                const note = document.getElementById('flat-rates-note');
                if (shipInput) shipInput.disabled = hasRules;
                if (delInput) delInput.disabled = hasRules;
                if (note) note.style.display = hasRules ? 'block' : 'none';
            }

            document.getElementById('add-shipping-rule').addEventListener('click', function() {
                rules.push({
                    min_qty: 0,
                    max_qty: null,
                    shipping_cost: 0,
                    delivery_cost: 0
                });
                render();
            });

            // init render
            render();

            // Ensure hidden input is updated just before form submit
            const form = document.querySelector('form');
            form.addEventListener('submit', function() {
                updateHidden();
            });
        })();
    </script>
@endpush
