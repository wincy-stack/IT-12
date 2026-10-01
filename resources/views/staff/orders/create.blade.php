@extends('layouts.staff')

@section('title', 'New Order – Aqua De Smiley')

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
    <div>
        <h1><i class="fa-solid fa-circle-plus" style="color:#38bdf8"></i> New Order</h1>
        <p>Create a water order for a customer. You can combine multiple container sizes.</p>
    </div>
    <a href="{{ route('staff.orders.index') }}" class="btn btn-outline">
        <i class="fa-solid fa-arrow-left"></i> Order Tracking
    </a>
</div>

@if($errors->has('items'))
<div class="alert alert-error" style="max-width:820px;margin-bottom:1rem;">
    <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first('items') }}
</div>
@endif

<div class="card" style="max-width:820px;">
    <form action="{{ route('staff.orders.store') }}" method="POST" novalidate id="staffOrderForm">
        @csrf

        <div class="form-row form-row-2">
            <div class="form-group">
                <label class="form-label" for="customer_search">Customer</label>

                {{-- Hidden real input submitted with the form --}}
                <input type="hidden" id="customer_id" name="customer_id"
                       value="{{ old('customer_id', $preselected ?? '') }}" required>

                <div class="cs-wrapper" id="cs-wrapper">
                    {{-- Trigger / display field --}}
                    <div class="cs-trigger" id="cs-trigger" tabindex="0">
                        <i class="fa-solid fa-user" style="color:#38bdf8;margin-right:.45rem;"></i>
                        <span id="cs-label">— Select Customer —</span>
                        <i class="fa-solid fa-chevron-down cs-arrow" id="cs-arrow"></i>
                    </div>

                    {{-- Dropdown panel --}}
                    <div class="cs-panel" id="cs-panel">
                        <div class="cs-search-wrap">
                            <i class="fa-solid fa-magnifying-glass cs-search-icon"></i>
                            <input type="text" id="customer_search" class="cs-search-input"
                                   placeholder="Search by name or email…" autocomplete="off">
                        </div>
                        <ul class="cs-list" id="cs-list">
                            @foreach($customers as $c)
                            <li class="cs-item"
                                data-id="{{ $c->id }}"
                                data-label="{{ $c->name }} ({{ $c->email }})"
                                data-search="{{ strtolower($c->name . ' ' . $c->email) }}"
                                {{ (old('customer_id', $preselected ?? '') == $c->id) ? 'data-selected=true' : '' }}>
                                <i class="fa-solid fa-user-circle" style="color:#38bdf8;font-size:.85rem;"></i>
                                <span class="cs-item-name">{{ $c->name }}</span>
                                <span class="cs-item-email">{{ $c->email }}</span>
                            </li>
                            @endforeach
                        </ul>
                        <p class="cs-empty" id="cs-empty" style="display:none;">No customers found.</p>
                    </div>
                </div>

                @error('customer_id')<div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="status">Initial Status</label>
                <select id="status" name="status" class="form-select" required>
                    <option value="pending"   {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ old('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                @error('status')<div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>@enderror
            </div>
        </div>

        <!-- Container Sizes & Quantities Table -->
        <div class="form-group" style="margin-top:.75rem;margin-bottom:1.5rem;">
            <label class="form-label" style="font-weight:700;color:var(--text);font-size:.95rem;">
                <i class="fa-solid fa-boxes-stacked" style="color:#38bdf8;margin-right:.4rem;"></i> Container Items & Quantities
            </label>
            <div style="border:1px solid rgba(255,255,255,.09);border-radius:12px;overflow:hidden;background:rgba(255,255,255,.02);">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:rgba(14,165,233,.12);">
                            <th style="padding:.75rem 1rem;font-size:.8rem;text-transform:uppercase;color:var(--text-muted);">Container Size</th>
                            <th style="padding:.75rem 1rem;font-size:.8rem;text-transform:uppercase;color:var(--text-muted);">Unit Rate</th>
                            <th style="padding:.75rem 1rem;font-size:.8rem;text-transform:uppercase;color:var(--text-muted);width:220px;">Service Type</th>
                            <th style="padding:.75rem 1rem;font-size:.8rem;text-transform:uppercase;color:var(--text-muted);width:120px;">Quantity</th>
                            <th style="padding:.75rem 1rem;font-size:.8rem;text-transform:uppercase;color:var(--text-muted);text-align:right;">Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($containerSizes as $key => $size)
                        @php
                            $loopIndex = $loop->index;
                            $defaultQty = ($key === '5_gallon') ? 1 : 0;
                            $qty = old("items.{$loopIndex}.quantity", $defaultQty);
                            $service = old("items.{$loopIndex}.service_type", 'refill');
                        @endphp
                        <tr class="item-row" data-key="{{ $key }}" data-price="{{ $size['price'] }}" style="border-top:1px solid rgba(255,255,255,.06);">
                            <td style="padding:.85rem 1rem;">
                                <input type="hidden" name="items[{{ $loopIndex }}][container_size]" value="{{ $key }}">
                                <div style="display:flex;align-items:center;gap:.6rem;font-weight:600;">
                                    <i class="fa-solid {{ $size['icon'] }}" style="color:#38bdf8;"></i>
                                    <span>{{ $size['name'] }}</span>
                                </div>
                            </td>
                            <td style="padding:.85rem 1rem;font-weight:600;color:#38bdf8;">
                                ₱{{ number_format($size['price'], 2) }}
                            </td>
                            <td style="padding:.85rem 1rem;">
                                <select name="items[{{ $loopIndex }}][service_type]" class="form-select" style="padding:.35rem .6rem;font-size:.82rem;">
                                    @foreach($serviceTypes as $stKey => $st)
                                        <option value="{{ $stKey }}" {{ $service === $stKey ? 'selected' : '' }}>
                                            {{ $st['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="padding:.85rem 1rem;">
                                <input type="number" name="items[{{ $loopIndex }}][quantity]" class="form-input item-qty"
                                       min="0" max="500" value="{{ $qty }}" style="padding:.35rem .5rem;text-align:center;font-weight:700;">
                            </td>
                            <td style="padding:.85rem 1rem;text-align:right;font-weight:700;color:var(--accent);">
                                ₱<span class="row-total">{{ number_format($qty * $size['price'], 2) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="total_amount">Total Amount (₱)</label>
                <input id="total_amount" type="number" name="total_amount" class="form-input" step="0.01" min="0"
                    value="{{ old('total_amount', '30.00') }}" required>
                @error('total_amount')<div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>@enderror
                <div style="font-size:.75rem;color:var(--text-muted);margin-top:.25rem;">
                    Auto-calculated sum of all items above.
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="notes">Notes / Landmark (optional)</label>
                <textarea id="notes" name="notes" class="form-textarea" placeholder="Special instructions, gate color, landmark, etc.">{{ old('notes') }}</textarea>
                @error('notes')<div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>@enderror
            </div>
        </div>

        <div style="display:flex;gap:.85rem;margin-top:.5rem;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-circle-plus"></i> Place Order</button>
            <a href="{{ route('staff.orders.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

@push('styles')
<style>
/* ── Searchable Customer Dropdown ── */
.cs-wrapper {
    position: relative;
}
.cs-trigger {
    display: flex;
    align-items: center;
    gap: .3rem;
    padding: .6rem .85rem;
    background: var(--input-bg, rgba(255,255,255,.06));
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 10px;
    cursor: pointer;
    color: var(--text);
    font-size: .92rem;
    transition: border-color .2s, box-shadow .2s;
    user-select: none;
}
.cs-trigger:focus,
.cs-wrapper.open .cs-trigger {
    border-color: #38bdf8;
    box-shadow: 0 0 0 3px rgba(56,189,248,.18);
    outline: none;
}
.cs-arrow {
    margin-left: auto;
    font-size: .75rem;
    color: var(--text-muted);
    transition: transform .2s;
}
.cs-wrapper.open .cs-arrow { transform: rotate(180deg); }

.cs-panel {
    display: none;
    position: absolute;
    top: calc(100% + 6px);
    left: 0; right: 0;
    background: #1a2435;
    border: 1px solid rgba(56,189,248,.25);
    border-radius: 12px;
    box-shadow: 0 12px 40px rgba(0,0,0,.45);
    z-index: 9999;
    overflow: hidden;
    animation: csSlideIn .15s ease;
}
.cs-wrapper.open .cs-panel { display: block; }
@keyframes csSlideIn {
    from { opacity:0; transform: translateY(-6px); }
    to   { opacity:1; transform: translateY(0); }
}

.cs-search-wrap {
    position: relative;
    padding: .65rem .75rem;
    border-bottom: 1px solid rgba(255,255,255,.07);
}
.cs-search-icon {
    position: absolute;
    left: 1.25rem;
    top: 50%;
    transform: translateY(-50%);
    color: #38bdf8;
    font-size: .82rem;
    pointer-events: none;
}
.cs-search-input {
    width: 100%;
    padding: .45rem .75rem .45rem 2.1rem;
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 8px;
    color: var(--text, #e2e8f0);
    font-size: .88rem;
    outline: none;
    box-sizing: border-box;
    transition: border-color .2s;
}
.cs-search-input:focus { border-color: #38bdf8; }

.cs-list {
    list-style: none;
    margin: 0; padding: .35rem 0;
    max-height: 220px;
    overflow-y: auto;
}
.cs-list::-webkit-scrollbar { width: 5px; }
.cs-list::-webkit-scrollbar-thumb { background: rgba(56,189,248,.3); border-radius: 4px; }

.cs-item {
    display: flex;
    align-items: center;
    gap: .55rem;
    padding: .55rem 1rem;
    cursor: pointer;
    transition: background .15s;
    font-size: .88rem;
}
.cs-item:hover, .cs-item.focused {
    background: rgba(56,189,248,.12);
}
.cs-item.selected {
    background: rgba(56,189,248,.18);
    font-weight: 600;
}
.cs-item-name { color: var(--text, #e2e8f0); flex-shrink: 0; }
.cs-item-email {
    color: var(--text-muted, #94a3b8);
    font-size: .78rem;
    margin-left: auto;
    text-align: right;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 55%;
}
.cs-empty {
    padding: .75rem 1rem;
    color: var(--text-muted);
    font-size: .85rem;
    text-align: center;
}
</style>
@endpush

@push('scripts')
<script>
/* ── Order totals ── */
const itemRows  = document.querySelectorAll('.item-row');
const totalInput = document.getElementById('total_amount');

function calculateTotal() {
    let sum = 0;
    itemRows.forEach(row => {
        const price = parseFloat(row.dataset.price) || 0;
        const qtyInput = row.querySelector('.item-qty');
        const qty = parseInt(qtyInput.value) || 0;
        const lineTotal = price * qty;
        row.querySelector('.row-total').textContent = lineTotal.toFixed(2);
        sum += lineTotal;
    });
    totalInput.value = sum.toFixed(2);
}
document.querySelectorAll('.item-qty').forEach(i => i.addEventListener('input', calculateTotal));
calculateTotal();

/* ── Searchable customer dropdown ── */
(function () {
    const wrapper   = document.getElementById('cs-wrapper');
    const trigger   = document.getElementById('cs-trigger');
    const panel     = document.getElementById('cs-panel');
    const searchInput = document.getElementById('customer_search');
    const list      = document.getElementById('cs-list');
    const emptyMsg  = document.getElementById('cs-empty');
    const hiddenInput = document.getElementById('customer_id');
    const labelEl   = document.getElementById('cs-label');
    const items     = Array.from(list.querySelectorAll('.cs-item'));

    // Pre-select if old() value was set
    const preId = hiddenInput.value;
    if (preId) {
        const pre = items.find(i => i.dataset.id === preId);
        if (pre) selectItem(pre, false);
    }

    function openPanel() {
        wrapper.classList.add('open');
        searchInput.focus();
        filterItems('');
    }
    function closePanel() {
        wrapper.classList.remove('open');
        searchInput.value = '';
        filterItems('');
    }

    trigger.addEventListener('click', () => wrapper.classList.contains('open') ? closePanel() : openPanel());
    trigger.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openPanel(); } });

    document.addEventListener('click', e => {
        if (!wrapper.contains(e.target)) closePanel();
    });

    searchInput.addEventListener('input', () => filterItems(searchInput.value.toLowerCase().trim()));
    searchInput.addEventListener('keydown', handleSearchKey);

    function filterItems(q) {
        let visible = 0;
        items.forEach(item => {
            const match = !q || item.dataset.search.includes(q);
            item.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        emptyMsg.style.display = visible === 0 ? 'block' : 'none';
    }

    function getFocused() { return list.querySelector('.cs-item.focused'); }

    function handleSearchKey(e) {
        const visibles = items.filter(i => i.style.display !== 'none');
        if (!visibles.length) return;
        const focused = getFocused();
        let idx = visibles.indexOf(focused);

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (focused) focused.classList.remove('focused');
            idx = Math.min(idx + 1, visibles.length - 1);
            visibles[idx].classList.add('focused');
            visibles[idx].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (focused) focused.classList.remove('focused');
            idx = Math.max(idx - 1, 0);
            visibles[idx].classList.add('focused');
            visibles[idx].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (focused) selectItem(focused);
        } else if (e.key === 'Escape') {
            closePanel();
        }
    }

    items.forEach(item => {
        item.addEventListener('click', () => selectItem(item));
        item.addEventListener('mouseenter', () => {
            const f = getFocused();
            if (f) f.classList.remove('focused');
            item.classList.add('focused');
        });
    });

    function selectItem(item, close = true) {
        items.forEach(i => i.classList.remove('selected', 'focused'));
        item.classList.add('selected');
        hiddenInput.value = item.dataset.id;
        labelEl.textContent = item.dataset.label;
        labelEl.style.color = 'var(--text)';
        if (close) closePanel();
    }
})();
</script>
@endpush
@endsection
