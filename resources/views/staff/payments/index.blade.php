@extends('layouts.staff')

@section('title', 'Walk-in POS & Payment Process – Aqua De Smiley')

@section('content')
<div class="pos-container">
    <!-- Header -->
    <div class="pos-page-header">
        <div>
            <h1>
                <i class="fa-solid fa-cash-register" style="color:var(--accent);"></i>
                Walk-in POS &amp; Cashier
            </h1>
            <p>Process cash payments and release refilled containers to customers at the counter.</p>
        </div>

        <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap;">
            <a href="{{ route('staff.payments.index', ['tab' => 'awaiting']) }}"
               class="pos-tab-pill {{ $tab === 'awaiting' ? 'active' : '' }}">
                <i class="fa-solid fa-hourglass-half"></i>
                Awaiting Payment &amp; Release
                <span class="pos-badge">{{ $counts['awaiting'] }}</span>
            </a>
            <a href="{{ route('staff.payments.index', ['tab' => 'completed']) }}"
               class="pos-tab-pill {{ $tab === 'completed' ? 'active' : '' }}">
                <i class="fa-solid fa-circle-check"></i>
                Completed / Settled
                <span class="pos-badge">{{ $counts['completed'] }}</span>
            </a>
            <a href="{{ route('staff.orders.create') }}" class="btn btn-sm btn-outline" style="padding:.45rem .85rem;">
                <i class="fa-solid fa-plus"></i> New Walk-in Order
            </a>
        </div>
    </div>

    <!-- Main Two-Column Layout -->
    <div class="pos-grid">

        <!-- ── LEFT PANEL: ORDERS LIST ── -->
        <div class="pos-card pos-card-left">
            <div class="pos-card-header">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;width:100%;">
                    <div>
                        <h2 class="pos-card-title">
                            {{ $tab === 'completed' ? 'Completed & Released Orders' : 'Orders Awaiting Payment' }}
                        </h2>
                        <p style="margin:.25rem 0 0;font-size:.82rem;color:#94a3b8;">
                            Click any customer in the list below to process payment (Cash, GCash, PayPal) and release.
                        </p>
                    </div>
                    
                    <!-- Search Input -->
                    <form action="{{ route('staff.payments.index') }}" method="GET" style="display:flex;align-items:center;gap:.4rem;">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        <div style="position:relative;">
                            <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:.8rem;"></i>
                            <input type="text" name="q" value="{{ $search ?? '' }}"
                                   placeholder="Search customer or order #…"
                                   class="pos-search-input">
                        </div>
                        @if(!empty($search))
                            <a href="{{ route('staff.payments.index', ['tab' => $tab]) }}" class="pos-search-clear" title="Clear filter">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="pos-table-container">
                @if($orders->isEmpty())
                    <div class="pos-empty-state">
                        <div class="pos-empty-icon">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <h3>No orders in this list</h3>
                        <p>
                            @if(!empty($search))
                                No orders matched "<strong>{{ $search }}</strong>".
                            @elseif($tab === 'awaiting')
                                All customer orders have been paid and released.
                            @else
                                No settled orders recorded yet.
                            @endif
                        </p>
                        @if($tab === 'awaiting')
                            <a href="{{ route('staff.orders.create') }}" class="btn btn-sm btn-primary" style="margin-top:1rem;">
                                <i class="fa-solid fa-plus"></i> Create Walk-in Order
                            </a>
                        @endif
                    </div>
                @else
                    <table class="pos-table" id="ordersTable">
                        <thead>
                            <tr>
                                <th style="width:105px;">ORDER #</th>
                                <th>CUSTOMER</th>
                                <th>CONTAINER / SERVICE</th>
                                <th style="text-align:right;width:105px;">TOTAL</th>
                                <th style="text-align:center;width:110px;">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            @php
                                $isSelected = $selectedOrder && $selectedOrder->id === $order->id;
                                $isPaid = $order->isPaid();
                                $isReleased = $order->isReleased();
                                $containerText = $order->containerSummary();
                                $serviceText = $order->serviceSummary();
                            @endphp
                            <tr class="pos-row {{ $isSelected ? 'is-selected' : '' }}"
                                data-id="{{ $order->id }}"
                                data-number="{{ $order->order_number }}"
                                data-customer="{{ $order->customer?->name ?? 'Walk-in Customer' }}"
                                data-customer-info="{{ $order->customer?->phone ?? $order->customer?->email ?? '' }}"
                                data-container="{{ $containerText }}"
                                data-service="{{ $serviceText }}"
                                data-total="₱{{ number_format($order->total_amount, 2) }}"
                                data-total-raw="{{ $order->total_amount }}"
                                data-is-paid="{{ $isPaid ? '1' : '0' }}"
                                data-is-released="{{ $isReleased ? '1' : '0' }}"
                                data-payment-method="{{ $order->payment_method ?? 'cash' }}"
                                data-status="{{ $order->status }}"
                                data-pay-url="{{ route('staff.payments.pay', $order) }}"
                                data-release-url="{{ route('staff.payments.release', $order) }}">
                                <td class="col-number">
                                    <span class="order-num-pill">#{{ ltrim($order->order_number, 'ORD-0') ?: $order->order_number }}</span>
                                </td>
                                <td class="col-customer">
                                    <div style="display:flex;align-items:center;gap:.6rem;">
                                        <div class="customer-avatar-circle">
                                            <i class="fa-solid fa-circle-user"></i>
                                        </div>
                                        <div>
                                            <div class="customer-name-text">{{ $order->customer?->name ?? 'Walk-in Customer' }}</div>
                                            @if($order->customer?->phone || $order->customer?->email)
                                                <div class="customer-contact-text">{{ $order->customer?->phone ?? $order->customer?->email }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="col-service">
                                    <span class="container-text">{{ $containerText }}</span>
                                    <span class="service-badge">{{ $serviceText }}</span>
                                </td>
                                <td class="col-total" style="text-align:right;">
                                    ₱{{ number_format($order->total_amount, 2) }}
                                </td>
                                <td class="col-status" style="text-align:center;">
                                    @if(!$isPaid)
                                        <span class="status-pill status-pill-unpaid">
                                            <i class="fa-regular fa-clock"></i> Unpaid
                                        </span>
                                    @elseif(!$isReleased)
                                        <span class="status-pill status-pill-paid">
                                            <i class="fa-solid fa-circle-check"></i> Paid
                                        </span>
                                    @else
                                        <span class="status-pill status-pill-released">
                                            <i class="fa-solid fa-box-check"></i> Released
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <!-- ── RIGHT PANEL: SELECTED ORDER CASHIER ACTIONS ── -->
        <div class="pos-card pos-card-right" id="posDetailCard">
            @php
                $isPaid = $selectedOrder ? $selectedOrder->isPaid() : false;
                $isReleased = $selectedOrder ? $selectedOrder->isReleased() : false;
                $containerText = $selectedOrder ? $selectedOrder->containerSummary() : '';
                $serviceText = $selectedOrder ? $selectedOrder->serviceSummary() : '';
                $orderNumStr = $selectedOrder ? (ltrim($selectedOrder->order_number, 'ORD-0') ?: $selectedOrder->order_number) : '';
                $customerNameStr = $selectedOrder ? ($selectedOrder->customer?->name ?? 'Walk-in Customer') : '';
                $totalStr = $selectedOrder ? ('₱' . number_format($selectedOrder->total_amount, 2)) : '₱0.00';
                $pmMethod = $selectedOrder ? ($selectedOrder->payment_method ?? 'cash') : 'cash';
            @endphp
            <div id="posDetailContent" style="{{ $selectedOrder ? '' : 'display:none;' }}">
                <div class="detail-header">
                    <div class="order-title-line">
                        <h3 id="detailOrderNumber">Order #{{ $orderNumStr }}</h3>
                    </div>
                    <div id="detailCustomerName" class="detail-customer-sub">
                        {{ $customerNameStr }}
                    </div>
                </div>

                <div class="detail-rows">
                    <div class="detail-row">
                        <span class="row-label">Container</span>
                        <span class="row-val" id="detailContainer">{{ $containerText }}</span>
                    </div>

                    <div class="detail-row">
                        <span class="row-label">Service</span>
                        <span class="row-val" id="detailService">{{ $serviceText }}</span>
                    </div>
                </div>

                <div class="detail-divider"></div>

                <!-- Total Due -->
                <div class="detail-total-row">
                    <span class="total-label">Total Due</span>
                    <span class="total-amount" id="detailTotal">{{ $totalStr }}</span>
                </div>

                <!-- Payment Method Selector (Buttons: Cash, GCash, PayPal) -->
                <div class="pm-section" id="pmSection">
                    <span class="pm-section-label">Select Payment Method:</span>
                    <div class="pm-options" id="pmOptions">
                        <!-- Cash -->
                        <button type="button" class="pm-card {{ $pmMethod === 'cash' ? 'is-active' : '' }}"
                                data-method="cash" id="pmBtn_cash" {{ $isPaid ? 'disabled' : '' }}>
                            <span class="pm-icon pm-icon-cash"><i class="fa-solid fa-money-bill-wave"></i></span>
                            <span class="pm-name">Cash</span>
                        </button>
                        <!-- GCash -->
                        <button type="button" class="pm-card {{ $pmMethod === 'gcash' ? 'is-active' : '' }}"
                                data-method="gcash" id="pmBtn_gcash" {{ $isPaid ? 'disabled' : '' }}>
                            <span class="pm-icon pm-icon-gcash"><i class="fa-solid fa-mobile-screen-button"></i></span>
                            <span class="pm-name">GCash</span>
                        </button>
                        <!-- PayPal -->
                        <button type="button" class="pm-card {{ $pmMethod === 'paypal' ? 'is-active' : '' }}"
                                data-method="paypal" id="pmBtn_paypal" {{ $isPaid ? 'disabled' : '' }}>
                            <span class="pm-icon pm-icon-paypal"><i class="fa-brands fa-paypal"></i></span>
                            <span class="pm-name">PayPal</span>
                        </button>
                    </div>
                </div>

                <!-- Action Button 1: Mark Payment Received -->
                <form id="formPay" action="{{ $selectedOrder ? route('staff.payments.pay', $selectedOrder) : '#' }}" method="POST" style="margin-bottom:.5rem;">
                    @csrf
                    <input type="hidden" name="payment_method" id="paymentMethodInput" value="{{ $pmMethod }}">
                    <button type="submit" id="btnMarkPaid"
                            class="btn-pos-pay {{ $isPaid ? 'is-paid' : '' }}"
                            {{ $isPaid ? 'disabled' : '' }}>
                        <i class="fa-solid {{ $isPaid ? 'fa-check-circle' : 'fa-hand-holding-dollar' }}"></i>
                        <span id="btnMarkPaidText">
                            @php
                                $pmLabels = ['cash' => 'Cash', 'gcash' => 'GCash', 'paypal' => 'PayPal'];
                                $pmLabel  = $pmLabels[$pmMethod] ?? 'Cash';
                            @endphp
                            {{ $isPaid ? 'Payment Received (' . $pmLabel . ')' : 'Mark Payment Received (' . $pmLabel . ')' }}
                        </span>
                    </button>
                </form>

                <!-- Indicator: Payment received — ready to release -->
                <div id="indicatorReady" class="pos-ready-indicator" style="{{ $isPaid ? 'display:flex;' : 'display:none;' }}">
                    <span class="ready-dot">●</span>
                    <span>Payment received — ready to release</span>
                </div>

                <!-- Action Button 2: Release Order to Customer -->
                <form id="formRelease" action="{{ $selectedOrder ? route('staff.payments.release', $selectedOrder) : '#' }}" method="POST">
                    @csrf
                    <button type="submit" id="btnReleaseOrder"
                            class="btn-pos-release {{ $isReleased ? 'is-released' : '' }}"
                            {{ $isReleased ? 'disabled' : '' }}>
                        <i class="fa-solid {{ $isReleased ? 'fa-circle-check' : 'fa-box-check' }}"></i>
                        <span id="btnReleaseText">{{ $isReleased ? 'Order Released' : 'Release Order to Customer' }}</span>
                    </button>
                </form>

                <!-- Receipt print action for convenience -->
                <div style="text-align:center;margin-top:1.25rem;">
                    <button type="button" onclick="printReceipt()" class="btn-receipt-link">
                        <i class="fa-solid fa-receipt"></i> Print Cashier Receipt Slip
                    </button>
                </div>
            </div>

            <!-- No Order Selected State -->
            <div id="posEmptyDetailState" class="pos-empty-state" style="{{ $selectedOrder ? 'display:none;' : '' }}padding:4rem 1.5rem;">
                <div class="pos-empty-icon" style="color:#94a3b8;">
                    <i class="fa-solid fa-arrow-left"></i>
                </div>
                <h3>No Customer Selected</h3>
                <p>Click on any customer in the list on the left to process their payment and release their container.</p>
            </div>
        </div>

    </div>
</div>

<!-- ── HIDDEN PRINTABLE RECEIPT TEMPLATE ── -->
<div id="printReceiptArea" style="display:none;">
    <div style="max-width:320px;margin:0 auto;font-family:monospace;padding:1.5rem;text-align:center;border:1px dashed #000;">
        <h2 style="font-size:1.15rem;margin:0 0 .25rem;text-transform:uppercase;">Aqua De Smiley</h2>
        <p style="font-size:.75rem;margin:0 0 .75rem;">Pure &amp; Clean Water Refilling Station<br>Walk-in Cash POS</p>
        <div style="border-top:1px dashed #000;margin:.5rem 0;"></div>
        <div style="display:flex;justify-content:space-between;font-size:.8rem;margin-bottom:.25rem;">
            <span>Order No:</span>
            <strong id="rcptOrderNo">—</strong>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:.8rem;margin-bottom:.25rem;">
            <span>Customer:</span>
            <span id="rcptCustomer">—</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:.8rem;margin-bottom:.25rem;">
            <span>Items:</span>
            <span id="rcptContainer" style="text-align:right;max-width:180px;">—</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:.8rem;margin-bottom:.25rem;">
            <span>Payment:</span>
            <span id="rcptPaymentMethod">Cash (Settled)</span>
        </div>
        <div style="border-top:1px dashed #000;margin:.5rem 0;"></div>
        <div style="display:flex;justify-content:space-between;font-size:1rem;font-weight:bold;margin:.5rem 0;">
            <span>TOTAL PAID:</span>
            <span id="rcptTotal">₱0.00</span>
        </div>
        <div style="border-top:1px dashed #000;margin:.5rem 0;"></div>
        <p style="font-size:.7rem;margin-top:.75rem;color:#555;">
            Thank you for refilling with us!<br>
            Please keep your containers hygienic &amp; sealed.<br>
            {{ date('M d, Y h:i A') }}
        </p>
    </div>
</div>

@push('styles')
<style>
/* ─── POS PAGE LAYOUT ───────────────────────────────────────────── */
.pos-container {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 .25rem;
}

.pos-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.pos-page-header h1 {
    font-size: 1.5rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: .6rem;
    color: #ffffff;
    margin: 0;
}

.pos-page-header p {
    color: var(--text-muted);
    font-size: .88rem;
    margin-top: .25rem;
}

.pos-tab-pill {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .45rem 1rem;
    border-radius: 99px;
    font-size: .8rem;
    font-weight: 600;
    text-decoration: none;
    background: rgba(255,255,255,.05);
    color: #94a3b8;
    border: 1px solid rgba(255,255,255,.1);
    transition: all .2s;
}

.pos-tab-pill:hover {
    color: #fff;
    background: rgba(255,255,255,.1);
}

.pos-tab-pill.active {
    background: rgba(56,189,248,.15);
    color: #38bdf8;
    border-color: rgba(56,189,248,.35);
}

.pos-badge {
    background: rgba(255,255,255,.12);
    padding: .1rem .45rem;
    border-radius: 99px;
    font-size: .72rem;
    font-weight: 700;
}

.pos-tab-pill.active .pos-badge {
    background: #0284c7;
    color: #ffffff;
}

/* ─── TWO COLUMN GRID ───────────────────────────────────────────── */
.pos-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 390px;
    gap: 1.5rem;
    align-items: start;
}

@media (max-width: 992px) {
    .pos-grid {
        grid-template-columns: 1fr;
    }
}

/* ─── CARDS STYLING — DARK THEME ────────────────────────────────── */
.pos-card {
    background: linear-gradient(145deg, rgba(255,255,255,.055) 0%, rgba(255,255,255,.025) 100%);
    border-radius: 16px;
    box-shadow:
        0 0 0 1px rgba(255,255,255,.08),
        0 16px 48px rgba(0,0,0,.5),
        inset 0 1px 0 rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.1);
    color: #e2e8f0;
    overflow: hidden;
    backdrop-filter: blur(4px);
}

.pos-card-left {
    min-height: 520px;
}

.pos-card-right {
    padding: 1.75rem;
    position: sticky;
    top: calc(var(--topbar-h) + 1.25rem);
}

.pos-card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,.07);
}

.pos-card-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: #f1f5f9;
    margin: 0;
    letter-spacing: -.01em;
}

/* ─── SEARCH INPUT ─────────────────────────────────────────────── */
.pos-search-input {
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 8px;
    padding: .45rem .85rem .45rem 2.2rem;
    font-size: .84rem;
    font-family: inherit;
    color: #e2e8f0;
    width: 220px;
    transition: all .2s;
}

.pos-search-input::placeholder { color: #64748b; }

.pos-search-input:focus {
    outline: none;
    border-color: rgba(56,189,248,.5);
    background: rgba(56,189,248,.07);
    box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.12);
}

.pos-search-clear {
    width: 28px;
    height: 28px;
    display: inline-grid;
    place-items: center;
    border-radius: 6px;
    color: #94a3b8;
    text-decoration: none;
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.1);
}

.pos-search-clear:hover {
    background: rgba(255,255,255,.13);
    color: #f1f5f9;
}

/* ─── POS TABLE ─────────────────────────────────────────────────── */
.pos-table-container {
    overflow-x: auto;
}

.pos-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .88rem;
    text-align: left;
}

.pos-table th {
    background: rgba(255,255,255,.03);
    color: #64748b;
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    padding: .85rem 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,.07);
}

.pos-table td {
    padding: 1.1rem 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,.05);
    color: #cbd5e1;
    vertical-align: middle;
}

.pos-row {
    cursor: pointer;
    transition: background-color .15s ease;
}

.pos-row:hover {
    background-color: rgba(255,255,255,.04);
}

.pos-row.is-selected {
    background: linear-gradient(90deg, rgba(56,189,248,.1), rgba(99,102,241,.07)) !important;
    border-left: 3px solid #38bdf8;
}

.col-number {
    font-weight: 500;
    color: #94a3b8;
}

.customer-name {
    font-weight: 600;
    color: #f1f5f9;
}

.container-text {
    color: #cbd5e1;
    font-weight: 500;
}

.col-total {
    font-weight: 700;
    color: #38bdf8;
    font-size: .95rem;
}

/* ─── CUSTOMER QUICK PICKER BANNER ──────────────────────────────── */
.pos-customer-picker-banner {
    background: rgba(15, 23, 42, 0.65);
    border: 1px solid rgba(56, 189, 248, 0.22);
    border-radius: 10px;
    padding: .6rem .85rem;
    margin-top: .75rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    flex-wrap: wrap;
}

.pos-picker-inner {
    display: flex;
    align-items: center;
    gap: .6rem;
    flex: 1;
    min-width: 250px;
}

.pos-picker-label {
    font-size: .8rem;
    font-weight: 700;
    color: #e2e8f0;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: .4rem;
}

.pos-picker-dropdown {
    flex: 1;
    background: rgba(30, 41, 59, 0.9);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #f1f5f9;
    padding: .45rem .75rem;
    border-radius: 8px;
    font-size: .85rem;
    font-weight: 600;
    outline: none;
    cursor: pointer;
    transition: all .2s ease;
}

.pos-picker-dropdown:focus {
    border-color: #38bdf8;
    box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.25);
}

/* Table Action Buttons */
.btn-pos-table-pick {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .3rem;
    padding: .35rem .75rem;
    border-radius: 7px;
    font-size: .75rem;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    border: none;
    transition: all .15s ease;
}

.btn-pos-table-pay {
    background: #ffffff;
    color: #0f172a;
    box-shadow: 0 2px 8px rgba(0,0,0,0.22);
}
.btn-pos-table-pay:hover {
    background: #e2e8f0;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255,255,255,0.2);
}

.btn-pos-table-release {
    background: linear-gradient(135deg, #10b981, #059669);
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(16,185,129,0.3);
}
.btn-pos-table-release:hover {
    opacity: .92;
    transform: translateY(-1px);
}

.badge-pos-table-done {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    font-size: .75rem;
    font-weight: 600;
    color: #94a3b8;
    background: rgba(255,255,255,0.06);
    padding: .25rem .55rem;
    border-radius: 6px;
    border: 1px solid rgba(255,255,255,0.08);
}

/* ─── RIGHT PANEL DETAIL STYLES ─────────────────────────────────── */
.detail-header {
    margin-bottom: 1.25rem;
}

.detail-header h3 {
    font-size: 1.35rem;
    font-weight: 800;
    color: #f1f5f9;
    margin: 0;
    letter-spacing: -.02em;
}

.detail-customer-sub {
    font-size: .92rem;
    color: #38bdf8;
    margin-top: .2rem;
    font-weight: 500;
}

.detail-rows {
    display: flex;
    flex-direction: column;
    gap: .75rem;
    margin-bottom: 1.25rem;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: .9rem;
}

.row-label {
    color: #64748b;
    font-weight: 400;
}

.row-val {
    color: #e2e8f0;
    font-weight: 600;
    text-align: right;
}

.detail-divider {
    border-top: 1px solid rgba(255,255,255,.07);
    margin: 1.25rem 0;
}

/* Total Due row */
.detail-total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.25rem;
}

.total-label {
    font-size: 1.05rem;
    font-weight: 700;
    color: #e2e8f0;
}

.total-amount {
    font-size: 1.25rem;
    font-weight: 800;
    color: #38bdf8;
    letter-spacing: -.01em;
}

/* ─── PAYMENT METHOD SELECTOR ───────────────────────────────── */
.pm-section {
    margin-bottom: 1rem;
}

.pm-section-label {
    display: block;
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: #64748b;
    margin-bottom: .6rem;
}

.pm-options {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: .5rem;
}

.pm-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .35rem;
    padding: .75rem .5rem;
    border-radius: 10px;
    border: 1.5px solid rgba(255,255,255,.1);
    background: rgba(255,255,255,.04);
    cursor: pointer;
    transition: all .2s ease;
    position: relative;
    overflow: hidden;
    user-select: none;
}

.pm-card input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.pm-card:hover:not(.is-locked) {
    border-color: rgba(255,255,255,.22);
    background: rgba(255,255,255,.08);
}

.pm-card.is-active {
    border-color: #38bdf8;
    background: rgba(56,189,248,.12);
    box-shadow: 0 0 0 3px rgba(56,189,248,.15);
}

.pm-card.is-locked {
    opacity: .55;
    cursor: default;
    pointer-events: none;
}

.pm-icon {
    font-size: 1.3rem;
    width: 2.2rem;
    height: 2.2rem;
    border-radius: 50%;
    display: grid;
    place-items: center;
    background: rgba(255,255,255,.07);
    transition: background .2s;
}

.pm-card.is-active .pm-icon { background: rgba(56,189,248,.18); }

.pm-icon-cash   { color: #34d399; }
.pm-icon-gcash  { color: #0fa7de; }
.pm-icon-paypal { color: #009cde; }

.pm-name {
    font-size: .75rem;
    font-weight: 700;
    color: #94a3b8;
    text-align: center;
    line-height: 1.2;
    transition: color .2s;
}

.pm-card.is-active .pm-name { color: #38bdf8; }

/* Helper note */
.pos-helper-note {
    font-size: .78rem;
    color: #64748b;
    line-height: 1.45;
    margin-bottom: 1.5rem;
}

/* Blue Button: Mark Payment Received */
.btn-pos-pay {
    width: 100%;
    background: linear-gradient(135deg, #0ea5e9, #0284c7);
    color: #ffffff;
    font-weight: 700;
    font-size: .95rem;
    font-family: inherit;
    border: none;
    border-radius: 10px;
    padding: .85rem 1.25rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    transition: all .2s ease;
    box-shadow: 0 4px 16px rgba(14, 165, 233, 0.35);
}

.btn-pos-pay:hover:not(:disabled) {
    opacity: .9;
    transform: translateY(-1px);
    box-shadow: 0 6px 22px rgba(14, 165, 233, 0.5);
}

.btn-pos-pay.is-paid {
    background: rgba(255,255,255,.06);
    color: #64748b;
    cursor: default;
    box-shadow: none;
    border: 1px solid rgba(255,255,255,.1);
}

/* Green indicator */
.pos-ready-indicator {
    display: flex;
    align-items: center;
    gap: .45rem;
    color: #34d399;
    font-size: .85rem;
    font-weight: 600;
    margin: 1rem 0 .75rem;
    padding: 0 .2rem;
}

.ready-dot {
    font-size: 1rem;
    line-height: 1;
    color: #34d399;
}

/* Green Button: Release Order to Customer */
.btn-pos-release {
    width: 100%;
    background: linear-gradient(135deg, #10b981, #059669);
    color: #ffffff;
    font-weight: 700;
    font-size: .95rem;
    font-family: inherit;
    border: none;
    border-radius: 10px;
    padding: .85rem 1.25rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    transition: all .2s ease;
    box-shadow: 0 4px 16px rgba(16, 185, 129, 0.35);
}

.btn-pos-release:hover:not(:disabled) {
    opacity: .9;
    transform: translateY(-1px);
    box-shadow: 0 6px 22px rgba(16, 185, 129, 0.5);
}

.btn-pos-release.is-released {
    background: rgba(255,255,255,.06);
    color: #64748b;
    cursor: default;
    box-shadow: none;
    border: 1px solid rgba(255,255,255,.1);
}

.btn-receipt-link {
    background: none;
    border: none;
    color: #64748b;
    font-size: .8rem;
    cursor: pointer;
    text-decoration: underline;
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    font-family: inherit;
    transition: color .2s;
}

.btn-receipt-link:hover {
    color: #38bdf8;
}

/* Empty State */
.pos-empty-state {
    text-align: center;
    padding: 4rem 1.5rem;
    color: #64748b;
}

.pos-empty-icon {
    font-size: 2.5rem;
    color: #38bdf8;
    margin-bottom: 1rem;
}

.pos-empty-state h3 {
    color: #e2e8f0;
    font-size: 1.15rem;
    font-weight: 700;
    margin-bottom: .35rem;
}
</style>
@endpush

@push('scripts')
<script>
const PM_LABELS = { cash: 'Cash', gcash: 'GCash', paypal: 'PayPal' };

document.addEventListener('DOMContentLoaded', function () {
    const table              = document.getElementById('ordersTable');
    const btnMarkPaid        = document.getElementById('btnMarkPaid');
    const btnMarkPaidText    = document.getElementById('btnMarkPaidText');
    const indicatorReady     = document.getElementById('indicatorReady');
    const btnReleaseOrder    = document.getElementById('btnReleaseOrder');
    const btnReleaseText     = document.getElementById('btnReleaseText');
    const formPay            = document.getElementById('formPay');
    const formRelease        = document.getElementById('formRelease');
    const detailOrderNumber  = document.getElementById('detailOrderNumber');
    const detailCustomerName = document.getElementById('detailCustomerName');
    const detailContainer    = document.getElementById('detailContainer');
    const detailService      = document.getElementById('detailService');
    const detailTotal        = document.getElementById('detailTotal');
    const paymentMethodInput = document.getElementById('paymentMethodInput');
    const pmCards            = document.querySelectorAll('.pm-card');
    const posDetailContent   = document.getElementById('posDetailContent');
    const posEmptyState      = document.getElementById('posEmptyDetailState');

    // ── Active payment method tracker ─────────────────────────────
    let currentMethod = paymentMethodInput ? paymentMethodInput.value : 'cash';

    function getSelectedMethod() {
        return currentMethod || 'cash';
    }

    function setMethodActive(val) {
        currentMethod = val;
        pmCards.forEach(card => {
            const match = (card.dataset.method === val);
            card.classList.toggle('is-active', match);
        });
        if (paymentMethodInput) paymentMethodInput.value = val;
    }

    function lockPaymentMethod(lock) {
        pmCards.forEach(card => {
            if (lock) {
                card.classList.add('is-locked');
                card.disabled = true;
            } else {
                card.classList.remove('is-locked');
                card.disabled = false;
            }
        });
    }

    // ── Payment method button clicks ──────────────────────────────
    pmCards.forEach(card => {
        card.addEventListener('click', function () {
            if (this.classList.contains('is-locked') || this.disabled) return;
            const val = this.dataset.method;
            setMethodActive(val);
            if (btnMarkPaid && !btnMarkPaid.classList.contains('is-paid')) {
                btnMarkPaidText.textContent = 'Mark Payment Received (' + (PM_LABELS[val] || val) + ')';
            }
        });
    });

    // ── Show detail panel for a given row ─────────────────────────
    function activateRow(row) {
        if (!row) return;

        // Highlight row
        if (table) {
            table.querySelectorAll('.pos-row').forEach(r => r.classList.remove('is-selected'));
        }
        row.classList.add('is-selected');

        const d = row.dataset;
        const cleanNum = d.number.replace(/^ORD-0*/, '') || d.number;

        // Show detail panel
        if (posDetailContent) posDetailContent.style.display = 'block';
        if (posEmptyState)    posEmptyState.style.display    = 'none';

        // Update detail fields
        if (detailOrderNumber)  detailOrderNumber.textContent  = 'Order #' + cleanNum;
        if (detailCustomerName) detailCustomerName.textContent = d.customer;
        if (detailContainer)    detailContainer.textContent    = d.container;
        if (detailService)      detailService.textContent      = d.service;
        if (detailTotal)        detailTotal.textContent        = d.total;

        // Update form actions
        if (formPay)     formPay.action     = d.payUrl;
        if (formRelease) formRelease.action = d.releaseUrl;

        const isPaid     = (d.isPaid === '1');
        const isReleased = (d.isReleased === '1');

        // Payment method
        const method = d.paymentMethod || 'cash';
        setMethodActive(method);
        lockPaymentMethod(isPaid);

        // Pay button state
        if (btnMarkPaid) {
            if (isPaid) {
                btnMarkPaid.classList.add('is-paid');
                btnMarkPaid.disabled = true;
                btnMarkPaidText.textContent = 'Payment Received (' + (PM_LABELS[method] || 'Cash') + ')';
                btnMarkPaid.querySelector('i').className = 'fa-solid fa-check-circle';
            } else {
                btnMarkPaid.classList.remove('is-paid');
                btnMarkPaid.disabled = false;
                btnMarkPaidText.textContent = 'Mark Payment Received (' + (PM_LABELS[method] || 'Cash') + ')';
                btnMarkPaid.querySelector('i').className = 'fa-solid fa-hand-holding-dollar';
            }
        }

        // Ready indicator
        if (indicatorReady) indicatorReady.style.display = isPaid ? 'flex' : 'none';

        // Release button state
        if (btnReleaseOrder) {
            if (isReleased) {
                btnReleaseOrder.classList.add('is-released');
                btnReleaseOrder.disabled = true;
                btnReleaseText.textContent = 'Order Released';
                btnReleaseOrder.querySelector('i').className = 'fa-solid fa-circle-check';
            } else {
                btnReleaseOrder.classList.remove('is-released');
                btnReleaseOrder.disabled = false;
                btnReleaseText.textContent = 'Release Order to Customer';
                btnReleaseOrder.querySelector('i').className = 'fa-solid fa-box-check';
            }
        }
    }

    // ── Table row clicks ──────────────────────────────────────────
    if (table) {
        table.querySelectorAll('.pos-row').forEach(row => {
            row.addEventListener('click', () => activateRow(row));
        });

        // Auto-activate initially selected row
        const initialSelected = table.querySelector('.pos-row.is-selected');
        if (initialSelected) {
            initialSelected.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    // ── Pay form AJAX submit ──────────────────────────────────────
    if (formPay) {
        formPay.addEventListener('submit', function (e) {
            e.preventDefault();
            const method      = getSelectedMethod();
            const methodLabel = PM_LABELS[method] || 'Cash';

            btnMarkPaid.disabled = true;
            btnMarkPaidText.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing…';

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('payment_method', method);

            fetch(formPay.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    btnMarkPaid.classList.add('is-paid');
                    btnMarkPaid.disabled = true;
                    btnMarkPaidText.textContent = 'Payment Received (' + methodLabel + ')';
                    btnMarkPaid.querySelector('i').className = 'fa-solid fa-check-circle';
                    lockPaymentMethod(true);
                    if (indicatorReady) indicatorReady.style.display = 'flex';

                    // Update row data
                    const selectedRow = table ? table.querySelector('.pos-row.is-selected') : null;
                    if (selectedRow) {
                        selectedRow.dataset.isPaid = '1';
                        selectedRow.dataset.paymentMethod = method;

                        // Update status pill in table
                        const statusTd = selectedRow.querySelector('.col-status');
                        if (statusTd) {
                            statusTd.innerHTML = '<span class="status-pill status-pill-paid"><i class="fa-solid fa-circle-check"></i> Paid</span>';
                        }
                    }
                } else {
                    alert('Error: ' + (data.message || 'Could not record payment.'));
                    btnMarkPaid.disabled = false;
                    btnMarkPaidText.textContent = 'Mark Payment Received (' + methodLabel + ')';
                }
            })
            .catch(() => formPay.submit());
        });
    }

    // ── Print receipt ─────────────────────────────────────────────
    window.printReceipt = function () {
        const selectedRow = table ? table.querySelector('.pos-row.is-selected') : null;
        if (!selectedRow) { alert('Please select a customer order first.'); return; }

        const d = selectedRow.dataset;
        const cleanNum = d.number.replace(/^ORD-0*/, '') || d.number;
        const method = currentMethod || 'cash';
        const methodLabel = PM_LABELS[method] || 'Cash';

        document.getElementById('rcptOrderNo').textContent   = '#' + cleanNum;
        document.getElementById('rcptCustomer').textContent  = d.customer;
        document.getElementById('rcptContainer').textContent = d.container;
        document.getElementById('rcptTotal').textContent     = d.total;
        const rcptPm = document.getElementById('rcptPaymentMethod');
        if (rcptPm) rcptPm.textContent = methodLabel + ' (Settled)';

        const printContent = document.getElementById('printReceiptArea').innerHTML;
        const w = window.open('', '', 'width=450,height=600');
        w.document.write('<html><head><title>Receipt #' + cleanNum + '</title></head>');
        w.document.write('<body style="margin:0;padding:20px;font-family:monospace;">');
        w.document.write(printContent);
        w.document.write('</body></html>');
        w.document.close();
        w.focus();
        setTimeout(() => { w.print(); w.close(); }, 300);
    };
});
</script>
@endpush
@endsection
