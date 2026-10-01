@extends('layouts.app')

@section('title', 'Place Water Order – Aqua De Smiley')

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1 style="display:flex;align-items:center;gap:.6rem;">
            <i class="fa-solid fa-cart-plus" style="color:var(--accent)"></i>
            Place Water Order
        </h1>
        <p>Choose your container sizes, quantities, and service types. You can combine multiple sizes in a single order!</p>
    </div>
    <a href="{{ route('customer.dashboard') }}" class="btn btn-outline">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

@if($errors->has('items'))
<div class="alert alert-error">
    <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first('items') }}
</div>
@endif

<form action="{{ route('customer.orders.store') }}" method="POST" id="multiOrderForm" novalidate>
    @csrf

    <div style="display:grid;grid-template-columns:1fr 380px;gap:2rem;align-items:start;" class="order-layout">
        
        <!-- Left: Container Selection & Details -->
        <div style="display:flex;flex-direction:column;gap:1.75rem;">

            <!-- STEP 1: Select Container Sizes & Quantities -->
            <div class="card order-section">
                <div class="section-title">
                    <span class="step-num">1</span>
                    <div>
                        <h2 style="font-size:1.15rem;font-weight:700;">Select Container Sizes & Quantities</h2>
                        <p style="font-size:.85rem;color:var(--text-muted);margin-top:.2rem;">
                            Pick any combination of <strong>500mL</strong>, <strong>1-Gallon</strong>, and <strong>5-Gallon</strong> containers.
                        </p>
                    </div>
                </div>

                <div class="container-cards-list" style="display:flex;flex-direction:column;gap:1rem;margin-top:1.25rem;">
                    @foreach($containerSizes as $key => $size)
                    @php
                        $loopIndex = $loop->index;
                        $oldQty = old("items.{$loopIndex}.quantity", 0);
                        $oldService = old("items.{$loopIndex}.service_type", 'refill');
                    @endphp
                    <div class="container-item-card {{ $oldQty > 0 ? 'active' : '' }}"
                         id="item_card_{{ $key }}"
                         data-key="{{ $key }}"
                         data-price="{{ $size['price'] }}"
                         data-name="{{ $size['name'] }}"
                         data-short="{{ $size['short_name'] }}">

                        <input type="hidden" name="items[{{ $loopIndex }}][container_size]" value="{{ $key }}">

                        <div class="item-card-header">
                            <!-- Left: Icon & Info -->
                            <div style="display:flex;align-items:center;gap:1rem;flex:1;">
                                <div class="item-icon-box">
                                    <i class="fa-solid {{ $size['icon'] }}"></i>
                                </div>
                                <div>
                                    <div style="display:flex;align-items:center;gap:.6rem;">
                                        <span style="font-weight:700;font-size:1.1rem;">{{ $size['name'] }}</span>
                                        <span class="price-tag">₱{{ number_format($size['price'], 2) }} / unit</span>
                                    </div>
                                    <div style="color:var(--text-muted);font-size:.82rem;margin-top:.2rem;">
                                        {{ $size['description'] }}
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Line Subtotal Indicator -->
                            <div class="item-subtotal-badge" id="subtotal_badge_{{ $key }}">
                                ₱<span class="subtotal-val">{{ number_format($oldQty * $size['price'], 2) }}</span>
                            </div>
                        </div>

                        <!-- Controls Row: Service Type & Quantity -->
                        <div class="item-card-controls">
                            <!-- Service Type -->
                            <div style="flex:1;min-width:200px;">
                                <label class="control-label" for="service_{{ $key }}">
                                    <i class="fa-solid fa-sliders" style="margin-right:.3rem"></i> Service Type
                                </label>
                                <select id="service_{{ $key }}" name="items[{{ $loopIndex }}][service_type]"
                                        class="form-input service-select" style="padding:.55rem .85rem;font-size:.85rem;">
                                    @foreach($serviceTypes as $stKey => $st)
                                        <option value="{{ $stKey }}" {{ $oldService === $stKey ? 'selected' : '' }}>
                                            {{ $st['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Quantity Stepper -->
                            <div>
                                <label class="control-label">
                                    <i class="fa-solid fa-calculator" style="margin-right:.3rem"></i> Quantity
                                </label>
                                <div class="qty-stepper">
                                    <button type="button" class="btn-step btn-step-minus" data-target="qty_{{ $key }}" aria-label="Decrease">
                                        <i class="fa-solid fa-minus"></i>
                                    </button>
                                    <input type="number" id="qty_{{ $key }}" name="items[{{ $loopIndex }}][quantity]"
                                           class="qty-input" min="0" max="500" value="{{ $oldQty }}"
                                           data-key="{{ $key }}" data-price="{{ $size['price'] }}"
                                           aria-label="Quantity for {{ $size['name'] }}">
                                    <button type="button" class="btn-step btn-step-plus" data-target="qty_{{ $key }}" aria-label="Increase">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        <!-- Notes -->
        <div class="card order-section">
            <div class="section-title">
                <span class="step-num">2</span>
                <div>
                    <h2 style="font-size:1.15rem;font-weight:700;">Notes (Optional)</h2>
                    <p style="font-size:.85rem;color:var(--text-muted);margin-top:.2rem;">Any special instructions or requests for your order.</p>
                </div>
            </div>

            <div style="margin-top:1.25rem;">
                <div class="form-group">
                    <label class="form-label" for="notes">Notes / Special Instructions</label>
                    <textarea id="notes" name="notes" class="form-input" rows="3"
                              placeholder="e.g. Leave at the counter, call before coming, etc.">{{ old('notes') }}</textarea>
                    @error('notes')<div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>@enderror
                </div>
            </div>
        </div>

    </div>

        <!-- Right: Sticky Order Summary Card -->
        <div style="position:sticky;top:84px;">
            <div class="card summary-card">
                <h3 style="font-size:1.15rem;font-weight:700;margin-bottom:1.25rem;display:flex;align-items:center;gap:.5rem;">
                    <i class="fa-solid fa-receipt" style="color:var(--accent);"></i> Order Summary
                </h3>

                <!-- Selected Items List -->
                <div id="summaryItemsList" style="display:flex;flex-direction:column;gap:.75rem;padding-bottom:1.25rem;border-bottom:1px solid rgba(255,255,255,.08);min-height:70px;">
                    <!-- Dynamically populated via JavaScript -->
                </div>

                <!-- Totals Breakdown -->
                <div style="display:flex;flex-direction:column;gap:.75rem;margin-top:1rem;padding-bottom:1.25rem;border-bottom:1px solid rgba(255,255,255,.08);">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="color:var(--text-muted);font-size:.9rem;">Total Containers</span>
                        <span id="summaryTotalQty" style="font-weight:700;font-size:.95rem;">0 units</span>
                    </div>
                </div>

                <!-- Grand Total -->
                <div style="margin-top:1.25rem;margin-bottom:1.5rem;display:flex;align-items:baseline;justify-content:space-between;">
                    <div>
                        <div style="font-size:.8rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;font-weight:600;">Total Amount</div>
                        <div style="font-size:.75rem;color:#34d399;margin-top:.2rem;"><i class="fa-solid fa-wallet"></i> Cash / Pay upon Pickup</div>
                    </div>
                    <div style="text-align:right;">
                        <div id="summaryGrandTotal" style="font-size:2rem;font-weight:800;color:var(--accent);letter-spacing:-.02em;line-height:1;">
                            ₱0.00
                        </div>
                    </div>
                </div>

                <button type="submit" id="btnSubmitOrder" class="btn btn-primary" style="width:100%;justify-content:center;padding:.85rem;font-size:1.05rem;box-shadow:0 6px 20px rgba(14,165,233,.45);">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Confirm & Place Order</span>
                </button>

                <p style="font-size:.75rem;color:var(--text-muted);text-align:center;margin-top:1rem;line-height:1.4;">
                    <i class="fa-solid fa-lock" style="margin-right:.3rem"></i> Fast, sealed & hygienic purified water.
                </p>
            </div>
        </div>

    </div>
</form>

@push('styles')
<style>
.order-section {
    border: 1px solid rgba(56,189,248,0.18);
}
.section-title {
    display: flex;
    align-items: flex-start;
    gap: .85rem;
}
.step-num {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--accent), var(--primary));
    color: #fff;
    font-size: .85rem;
    font-weight: 800;
    display: grid;
    place-items: center;
    flex-shrink: 0;
    margin-top: .1rem;
}

/* Container Item Cards */
.container-item-card {
    background: rgba(255,255,255,.03);
    border: 2px solid rgba(255,255,255,.07);
    border-radius: 14px;
    padding: 1.25rem;
    transition: all .2s cubic-bezier(0.4, 0, 0.2, 1);
}
.container-item-card:hover {
    border-color: rgba(56,189,248,.3);
    background: rgba(255,255,255,.05);
}
.container-item-card.active {
    background: rgba(14,165,233,.1);
    border-color: var(--accent);
    box-shadow: 0 0 20px rgba(56,189,248,.15);
}

.item-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}
.item-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: rgba(56,189,248,.15);
    color: var(--accent);
    display: grid;
    place-items: center;
    font-size: 1.4rem;
    flex-shrink: 0;
}
.price-tag {
    display: inline-block;
    padding: .2rem .65rem;
    border-radius: 99px;
    font-size: .8rem;
    font-weight: 700;
    background: rgba(14,165,233,.2);
    color: #38bdf8;
    border: 1px solid rgba(14,165,233,.35);
}
.item-subtotal-badge {
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--text-muted);
    transition: color .2s;
}
.container-item-card.active .item-subtotal-badge {
    color: var(--accent);
}

.item-card-controls {
    margin-top: 1rem;
    padding-top: .85rem;
    border-top: 1px solid rgba(255,255,255,.06);
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
}
.control-label {
    display: block;
    font-size: .78rem;
    color: var(--text-muted);
    font-weight: 600;
    margin-bottom: .35rem;
    text-transform: uppercase;
    letter-spacing: .05em;
}

/* Stepper */
.qty-stepper {
    display: flex;
    align-items: center;
    gap: .5rem;
}
.btn-step {
    width: 38px;
    height: 38px;
    border-radius: 9px;
    border: 1px solid var(--glass-border);
    background: rgba(255,255,255,.07);
    color: var(--text);
    display: grid;
    place-items: center;
    cursor: pointer;
    font-size: .95rem;
    transition: all .2s;
}
.btn-step:hover {
    background: rgba(56,189,248,.2);
    border-color: var(--accent);
    color: #fff;
}
.btn-step:active {
    transform: scale(.94);
}
.qty-input {
    width: 60px;
    height: 38px;
    text-align: center;
    border-radius: 9px;
    background: rgba(255,255,255,.06);
    border: 1px solid var(--glass-border);
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    font-size: 1.1rem;
    font-weight: 700;
}
.qty-input:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 2px rgba(56,189,248,.25);
}

/* Summary Card */
.summary-card {
    background: linear-gradient(180deg, rgba(15,30,60,0.85) 0%, rgba(10,22,40,0.95) 100%);
    border: 1px solid rgba(56,189,248,.25);
    box-shadow: 0 12px 40px rgba(0,0,0,.4);
}

@media(max-width: 960px) {
    .order-layout {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const itemCards = document.querySelectorAll('.container-item-card');
    const summaryItemsList = document.getElementById('summaryItemsList');
    const summaryTotalQty = document.getElementById('summaryTotalQty');
    const summaryGrandTotal = document.getElementById('summaryGrandTotal');
    const btnSubmitOrder = document.getElementById('btnSubmitOrder');

    // Stepper buttons
    document.querySelectorAll('.btn-step-minus').forEach(btn => {
        btn.addEventListener('click', function () {
            const inputId = this.dataset.target;
            const input = document.getElementById(inputId);
            let val = parseInt(input.value) || 0;
            if (val > 0) {
                input.value = val - 1;
                updateOrderSummary();
            }
        });
    });

    document.querySelectorAll('.btn-step-plus').forEach(btn => {
        btn.addEventListener('click', function () {
            const inputId = this.dataset.target;
            const input = document.getElementById(inputId);
            let val = parseInt(input.value) || 0;
            if (val < 500) {
                input.value = val + 1;
                updateOrderSummary();
            }
        });
    });

    // Inputs & select listeners
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('input', function () {
            let val = parseInt(this.value);
            if (isNaN(val) || val < 0) {
                this.value = 0;
            }
            updateOrderSummary();
        });
    });

    document.querySelectorAll('.service-select').forEach(select => {
        select.addEventListener('change', updateOrderSummary);
    });

    function updateOrderSummary() {
        let grandTotal = 0;
        let totalQty = 0;
        let selectedItemsHtml = '';

        itemCards.forEach(card => {
            const key = card.dataset.key;
            const price = parseFloat(card.dataset.price) || 0;
            const name = card.dataset.name;
            const shortName = card.dataset.short;

            const qtyInput = card.querySelector('.qty-input');
            const serviceSelect = card.querySelector('.service-select');
            const subtotalValSpan = card.querySelector('.subtotal-val');

            const qty = Math.max(0, parseInt(qtyInput.value) || 0);
            const lineSubtotal = qty * price;

            // Update line subtotal on card
            subtotalValSpan.textContent = lineSubtotal.toFixed(2);

            // Toggle active visual state
            if (qty > 0) {
                card.classList.add('active');
                totalQty += qty;
                grandTotal += lineSubtotal;

                const serviceLabel = serviceSelect.options[serviceSelect.selectedIndex].text;

                selectedItemsHtml += `
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;font-size:.88rem;gap:.5rem;">
                        <div>
                            <div style="font-weight:600;">${name}</div>
                            <div style="font-size:.75rem;color:var(--text-muted);">${serviceLabel} · ${qty} × ₱${price.toFixed(2)}</div>
                        </div>
                        <div style="font-weight:700;color:var(--accent);">₱${lineSubtotal.toFixed(2)}</div>
                    </div>
                `;
            } else {
                card.classList.remove('active');
            }
        });

        if (totalQty === 0) {
            summaryItemsList.innerHTML = `
                <div style="text-align:center;padding:1rem 0;color:var(--text-muted);font-size:.85rem;">
                    <i class="fa-solid fa-basket-shopping" style="font-size:1.5rem;display:block;margin-bottom:.4rem;opacity:.5;"></i>
                    No containers selected.<br>Use the + button to add items.
                </div>
            `;
            btnSubmitOrder.disabled = true;
            btnSubmitOrder.style.opacity = '0.5';
            btnSubmitOrder.style.cursor = 'not-allowed';
            btnSubmitOrder.innerHTML = `<i class="fa-solid fa-circle-check"></i> <span>Select Containers First</span>`;
        } else {
            summaryItemsList.innerHTML = selectedItemsHtml;
            btnSubmitOrder.disabled = false;
            btnSubmitOrder.style.opacity = '1';
            btnSubmitOrder.style.cursor = 'pointer';
            btnSubmitOrder.innerHTML = `<i class="fa-solid fa-circle-check"></i> <span>Place Order (₱${grandTotal.toFixed(2)})</span>`;
        }

        summaryTotalQty.textContent = totalQty + (totalQty === 1 ? ' unit' : ' units');
        summaryGrandTotal.textContent = '₱' + grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Initial update on page load
    updateOrderSummary();
});
</script>
@endpush
@endsection
