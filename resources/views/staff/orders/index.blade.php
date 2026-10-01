@extends('layouts.staff')

@section('title', 'Order Tracking – Aqua De Smiley')

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
    <div>
        <h1><i class="fa-solid fa-clipboard-list" style="color:#38bdf8"></i> Order Tracking</h1>
        <p>Monitor and update the status of all water orders.</p>
    </div>
    <a href="{{ route('staff.orders.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-circle-plus"></i> New Order
    </a>
</div>

<!-- Status Filter Tabs -->
<div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1.5rem;">
    @php
        $statuses = [
            'all'       => ['All',       '#94a3b8'],
            'pending'   => ['Pending',   '#fbbf24'],
            'confirmed' => ['Confirmed', '#38bdf8'],
            'completed' => ['Completed', '#34d399'],
            'cancelled' => ['Cancelled', '#f87171'],
        ];
        $current = request('status', 'all');
    @endphp
    @foreach($statuses as $key => [$label, $color])
        <a href="{{ route('staff.orders.index', ['status' => $key]) }}"
           style="padding:.35rem 1rem;border-radius:99px;font-size:.8rem;font-weight:600;text-decoration:none;
                  transition:all .2s;
                  {{ $current === $key
                      ? "background:{$color}33;color:{$color};border:1px solid {$color}55;"
                      : 'background:rgba(255,255,255,.05);color:#94a3b8;border:1px solid rgba(255,255,255,.09);' }}">
            {{ $label }}
            <span style="margin-left:.3rem;opacity:.8;">
                ({{ $key === 'all' ? $counts->sum() : ($counts[$key] ?? 0) }})
            </span>
        </a>
    @endforeach
</div>

<!-- Orders Table -->
<div class="card">
    <div class="table-wrap">
        @if($orders->isEmpty())
        <div style="text-align:center;padding:3rem;color:var(--text-muted);">
            <i class="fa-solid fa-box-open" style="font-size:2.5rem;margin-bottom:1rem;display:block;"></i>
            No orders found for this filter.
        </div>
        @else
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Container &amp; Qty</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td><span style="font-weight:700;color:#38bdf8;">{{ $order->order_number }}</span></td>
                    <td>
                        <div style="font-weight:600;">{{ $order->customer->name ?? '—' }}</div>
                        <div style="font-size:.75rem;color:var(--text-muted);">{{ $order->customer->email ?? '' }}</div>
                    </td>
                    <td>
                        @if($order->items && $order->items->isNotEmpty())
                            <div style="display:flex;flex-direction:column;gap:.25rem;">
                                @foreach($order->items as $item)
                                    <div>
                                        <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.15rem .5rem;border-radius:6px;font-size:.72rem;font-weight:600;background:{{ $item->serviceTypeColor() }}22;color:{{ $item->serviceTypeColor() }};border:1px solid {{ $item->serviceTypeColor() }}44;">
                                            <i class="fa-solid {{ $item->serviceTypeIcon() }}"></i>
                                            {{ $item->serviceTypeLabel() }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.2rem .55rem;border-radius:6px;font-size:.75rem;font-weight:600;background:{{ $order->serviceTypeColor() }}22;color:{{ $order->serviceTypeColor() }};border:1px solid {{ $order->serviceTypeColor() }}44;">
                                <i class="fa-solid {{ $order->serviceTypeIcon() }}"></i>
                                {{ $order->serviceTypeLabel() }}
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($order->items && $order->items->isNotEmpty())
                            <div style="display:flex;flex-direction:column;gap:.25rem;">
                                @foreach($order->items as $item)
                                    <div style="font-weight:600;display:flex;align-items:center;gap:.35rem;font-size:.82rem;">
                                        <i class="fa-solid {{ $item->containerSizeIcon() }}" style="color:#38bdf8;font-size:.8rem;"></i>
                                        {{ $item->quantity }} × {{ $item->containerSizeShort() }}
                                        <span style="font-size:.72rem;color:var(--text-muted);font-weight:400;">(₱{{ number_format($item->subtotal, 2) }})</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div style="font-weight:600;display:flex;align-items:center;gap:.35rem;">
                                <i class="fa-solid {{ $order->containerSizeIcon() }}" style="color:#38bdf8;font-size:.85rem;"></i>
                                {{ $order->quantity ?? $order->gallons }} × {{ $order->containerSizeShort() }}
                            </div>
                            <div style="font-size:.72rem;color:var(--text-muted);">
                                ₱{{ number_format($order->unitPrice(), 2) }} each
                            </div>
                        @endif
                    </td>
                    <td style="font-weight:600;">₱{{ number_format($order->total_amount,2) }}</td>
                    <td>
                        <span class="status-badge"
                            style="background:{{ $order->statusColor() }}22;color:{{ $order->statusColor() }};border:1px solid {{ $order->statusColor() }}55;">
                            {{ $order->statusLabel() }}
                        </span>
                    </td>
                    <td style="color:var(--text-muted);font-size:.8rem;">{{ $order->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display:flex;gap:.4rem;align-items:center;flex-wrap:wrap;">
                            <form action="{{ route('staff.orders.update', $order) }}" method="POST" style="display:flex;gap:.4rem;align-items:center;margin:0;">
                                @csrf @method('PATCH')
                                <select name="status" class="form-select" style="padding:.3rem .6rem;font-size:.75rem;width:auto;">
                                    @foreach(['pending','confirmed','completed','cancelled'] as $s)
                                        <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('_',' ',$s)) }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary" style="padding:.3rem .7rem;" title="Update Status">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                </button>
                            </form>
                            @if($order->payment_status === 'unpaid')
                                <a href="{{ route('staff.payments.index', ['order_id' => $order->id, 'tab' => 'awaiting']) }}" class="btn btn-sm btn-success" style="padding:.3rem .7rem;text-decoration:none;display:inline-flex;align-items:center;gap:.3rem;" title="Go to Payment Process">
                                    <i class="fa-solid fa-peso-sign"></i> Pay
                                </a>
                            @else
                                <a href="{{ route('staff.payments.index', ['order_id' => $order->id, 'tab' => 'completed']) }}" style="display:inline-flex;align-items:center;gap:.3rem;padding:.3rem .6rem;border-radius:6px;font-size:.75rem;font-weight:600;background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.3);text-decoration:none;" title="View Payment in POS">
                                    <i class="fa-solid fa-circle-check"></i> Paid
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    @if($orders instanceof \Illuminate\Pagination\LengthAwarePaginator && $orders->hasPages())
    <div style="display:flex;justify-content:flex-end;margin-top:1rem;">
        {{ $orders->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Payment Modal -->
<style>
@keyframes payModalIn {
    from { opacity:0; transform:translateY(32px) scale(.97); }
    to   { opacity:1; transform:translateY(0)   scale(1);   }
}
@keyframes payBackdropIn {
    from { opacity:0; }
    to   { opacity:1; }
}
#paymentModal.visible {
    animation: payBackdropIn .25s ease forwards;
}
#paymentModal.visible #paymentCard {
    animation: payModalIn .3s cubic-bezier(.22,.68,0,1.2) forwards;
}
</style>

<div id="paymentModal" style="
    display:none;
    position:fixed; inset:0; z-index:1000;
    justify-content:center; align-items:center;
    /* Rich layered backdrop */
    background:
        radial-gradient(ellipse 70% 55% at 50% 35%, rgba(56,189,248,.13) 0%, transparent 70%),
        radial-gradient(ellipse 50% 40% at 80% 75%, rgba(99,102,241,.12) 0%, transparent 60%),
        linear-gradient(135deg, rgba(2,6,23,.97) 0%, rgba(7,15,40,.96) 50%, rgba(2,6,23,.97) 100%);
    backdrop-filter: blur(18px) saturate(160%);
    -webkit-backdrop-filter: blur(18px) saturate(160%);
">
    <div id="paymentCard" style="
        width:100%; max-width:440px;
        position:relative;
        /* Glassmorphic card */
        background: linear-gradient(145deg, rgba(255,255,255,.07) 0%, rgba(255,255,255,.03) 100%);
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 20px;
        padding: 2.25rem 2rem;
        box-shadow:
            0 0 0 1px rgba(56,189,248,.08),
            0 24px 60px rgba(0,0,0,.65),
            0 0 80px rgba(56,189,248,.07),
            inset 0 1px 0 rgba(255,255,255,.1);
    ">
        <!-- Decorative glow orb -->
        <div style="
            position:absolute; top:-60px; right:-40px;
            width:180px; height:180px; border-radius:50%;
            background: radial-gradient(circle, rgba(56,189,248,.18) 0%, transparent 70%);
            pointer-events:none;
        "></div>
        <div style="
            position:absolute; bottom:-50px; left:-30px;
            width:140px; height:140px; border-radius:50%;
            background: radial-gradient(circle, rgba(99,102,241,.15) 0%, transparent 70%);
            pointer-events:none;
        "></div>

        <!-- Header -->
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.75rem;">
            <div style="display:flex;align-items:center;gap:.75rem;">
                <div style="
                    width:42px;height:42px;border-radius:12px;
                    background:linear-gradient(135deg,#10b981,#059669);
                    display:flex;align-items:center;justify-content:center;
                    box-shadow:0 4px 14px rgba(16,185,129,.4);
                ">
                    <i class="fa-solid fa-cash-register" style="color:#fff;font-size:1rem;"></i>
                </div>
                <div>
                    <h2 style="margin:0;font-size:1.2rem;font-weight:700;letter-spacing:-.01em;">Process Payment</h2>
                    <p style="margin:0;color:var(--text-muted);font-size:.75rem;margin-top:.1rem;">Complete the transaction</p>
                </div>
            </div>
            <button type="button" onclick="closePaymentModal()" style="
                width:32px;height:32px;border-radius:8px;
                background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
                color:var(--text-muted);font-size:1rem;cursor:pointer;
                display:flex;align-items:center;justify-content:center;
                transition:background .2s,color .2s;
            " onmouseover="this.style.background='rgba(255,255,255,.12)';this.style.color='#fff'" onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.color=''">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Divider -->
        <div style="height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,.1),transparent);margin-bottom:1.75rem;"></div>

        <form id="paymentForm" method="POST">
            @csrf @method('PATCH')
            <input type="hidden" name="action" value="pay">

            <!-- Amount Due -->
            <div style="
                background:linear-gradient(135deg,rgba(56,189,248,.12),rgba(99,102,241,.08));
                border:1px solid rgba(56,189,248,.2);
                border-radius:14px;padding:1rem 1.25rem;margin-bottom:1.25rem;
            ">
                <div style="font-size:.72rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:rgba(148,163,184,.7);margin-bottom:.35rem;">Total Amount Due</div>
                <div style="font-size:2rem;font-weight:800;color:#38bdf8;line-height:1;letter-spacing:-.02em;">₱<span id="modalTotal">0.00</span></div>
            </div>

            <!-- Payment Method -->
            <div style="margin-bottom:1.1rem;">
                <label style="display:block;font-size:.78rem;font-weight:600;color:rgba(148,163,184,.9);margin-bottom:.45rem;letter-spacing:.03em;">PAYMENT METHOD</label>
                <select name="payment_method" class="form-select" style="padding:.65rem 1rem;border-radius:10px;font-weight:500;">
                    <option value="cash">💵 Cash</option>
                    <option value="gcash">📱 GCash</option>
                </select>
            </div>

            <!-- Amount Tendered -->
            <div style="margin-bottom:1.1rem;">
                <label style="display:block;font-size:.78rem;font-weight:600;color:rgba(148,163,184,.9);margin-bottom:.45rem;letter-spacing:.03em;">AMOUNT TENDERED (₱)</label>
                <input type="number" name="amount_tendered" class="form-input" id="amountTendered" step="0.01" min="0"
                    style="padding:.75rem 1rem;font-size:1.2rem;font-weight:700;border-radius:10px;letter-spacing:.01em;">
            </div>

            <!-- Change -->
            <div style="
                background:linear-gradient(135deg,rgba(16,185,129,.12),rgba(5,150,105,.06));
                border:1px solid rgba(16,185,129,.2);
                border-radius:14px;padding:1rem 1.25rem;margin-bottom:1.75rem;
            ">
                <div style="font-size:.72rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:rgba(148,163,184,.7);margin-bottom:.35rem;">Change</div>
                <div style="font-size:1.6rem;font-weight:800;color:#34d399;line-height:1;letter-spacing:-.02em;">₱<span id="modalChange">0.00</span></div>
            </div>

            <!-- Submit -->
            <button type="submit" style="
                width:100%;padding:1rem;font-size:1rem;font-weight:700;
                border:none;border-radius:12px;cursor:pointer;
                background:linear-gradient(135deg,#38bdf8,#6366f1);
                color:#fff;letter-spacing:.02em;
                box-shadow:0 4px 20px rgba(56,189,248,.35);
                transition:opacity .2s,transform .15s,box-shadow .2s;
                display:flex;align-items:center;justify-content:center;gap:.6rem;
            " onmouseover="this.style.opacity='.88';this.style.boxShadow='0 6px 28px rgba(56,189,248,.5)';this.style.transform='translateY(-1px)'" onmouseout="this.style.opacity='1';this.style.boxShadow='0 4px 20px rgba(56,189,248,.35)';this.style.transform='translateY(0)'">
                <i class="fa-solid fa-check-double"></i> Confirm Payment
            </button>
        </form>
    </div>
</div>

<script>
    let currentTotal = 0;
    function openPaymentModal(orderId, total) {
        const modal = document.getElementById('paymentModal');
        modal.style.display = 'flex';
        // Small delay so display:flex applies before animation class
        requestAnimationFrame(() => modal.classList.add('visible'));
        document.getElementById('paymentForm').action = '/staff/orders/' + orderId;
        currentTotal = parseFloat(total) || 0;
        document.getElementById('modalTotal').textContent = currentTotal.toFixed(2);
        
        const input = document.getElementById('amountTendered');
        input.value = currentTotal.toFixed(2);
        input.focus();
        input.select();
        calculateChange();
    }
    
    function closePaymentModal() {
        const modal = document.getElementById('paymentModal');
        modal.classList.remove('visible');
        setTimeout(() => { modal.style.display = 'none'; }, 200);
    }
    
    function calculateChange() {
        const input = document.getElementById('amountTendered');
        const tendered = parseFloat(input.value) || 0;
        const change = Math.max(0, tendered - currentTotal);
        document.getElementById('modalChange').textContent = change.toFixed(2);
    }
    
    document.getElementById('amountTendered').addEventListener('input', calculateChange);
</script>
@endsection
