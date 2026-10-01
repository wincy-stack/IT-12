@extends('layouts.app')

@section('title', 'My Orders – Aqua De Smiley')

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1 style="display:flex;align-items:center;gap:.6rem;">
            <i class="fa-solid fa-receipt" style="color:var(--accent)"></i>
            My Orders & Tracking
        </h1>
        <p>Track the live status of your water refills and new container deliveries.</p>
    </div>
    <a href="{{ route('customer.orders.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Place New Order
    </a>
</div>

<!-- Status Filter Tabs -->
<div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1.5rem;">
    @php
        $statuses = [
            'all'       => ['All Orders', '#94a3b8'],
            'pending'   => ['Pending',    '#fbbf24'],
            'confirmed' => ['Confirmed',  '#38bdf8'],
            'completed' => ['Completed',  '#34d399'],
            'cancelled' => ['Cancelled',  '#f87171'],
        ];
        $current = request('status', 'all');
    @endphp
    @foreach($statuses as $key => [$label, $color])
        <a href="{{ route('customer.orders.index', ['status' => $key]) }}"
           style="padding:.35rem 1rem;border-radius:99px;font-size:.8rem;font-weight:600;text-decoration:none;
                  transition:all .2s;
                  {{ $current === $key
                      ? "background:{$color}33;color:{$color};border:1px solid {$color}55;"
                      : 'background:rgba(255,255,255,.05);color:#94a3b8;border:1px solid rgba(255,255,255,.09);' }}">
            {{ $label }}
            <span style="margin-left:.3rem;opacity:.85;">
                ({{ $key === 'all' ? $counts->sum() : ($counts[$key] ?? 0) }})
            </span>
        </a>
    @endforeach
</div>

<!-- Orders Table -->
<div class="card">
    <div class="table-wrapper">
        @if($orders->isEmpty())
        <div style="text-align:center;padding:3.5rem 1rem;color:var(--text-muted);">
            <div style="width:64px;height:64px;border-radius:50%;background:rgba(56,189,248,.1);display:grid;place-items:center;margin:0 auto 1.25rem;font-size:1.75rem;color:var(--accent);">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <h3 style="font-size:1.15rem;font-weight:600;color:var(--text);">No orders found</h3>
            <p style="margin-top:.4rem;font-size:.9rem;">You don't have any orders matching this filter.</p>
            <div style="margin-top:1.25rem;">
                <a href="{{ route('customer.orders.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Order Water Now
                </a>
            </div>
        </div>
        @else
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Service Type</th>
                    <th>Container Size</th>
                    <th>Quantity</th>
                    <th>Total (₱)</th>
                    <th>Status</th>
                    <th>Date Placed</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td>
                        <span style="font-weight:700;color:var(--accent);letter-spacing:.02em;">{{ $order->order_number }}</span>
                    </td>
                    <td>
                        @if($order->items && $order->items->isNotEmpty())
                            <div style="display:flex;flex-direction:column;gap:.3rem;">
                                @foreach($order->items as $item)
                                    <div>
                                        <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.15rem .5rem;border-radius:6px;font-size:.74rem;font-weight:600;background:{{ $item->serviceTypeColor() }}22;color:{{ $item->serviceTypeColor() }};border:1px solid {{ $item->serviceTypeColor() }}44;">
                                            <i class="fa-solid {{ $item->serviceTypeIcon() }}"></i>
                                            {{ $item->serviceTypeLabel() }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span style="display:inline-flex;align-items:center;gap:.35rem;padding:.2rem .65rem;border-radius:6px;font-size:.78rem;font-weight:600;background:{{ $order->serviceTypeColor() }}22;color:{{ $order->serviceTypeColor() }};border:1px solid {{ $order->serviceTypeColor() }}44;">
                                <i class="fa-solid {{ $order->serviceTypeIcon() }}"></i>
                                {{ $order->serviceTypeLabel() }}
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($order->items && $order->items->isNotEmpty())
                            <div style="display:flex;flex-direction:column;gap:.35rem;">
                                @foreach($order->items as $item)
                                    <div style="display:flex;align-items:center;gap:.4rem;font-size:.85rem;font-weight:600;">
                                        <i class="fa-solid {{ $item->containerSizeIcon() }}" style="color:var(--accent);font-size:.8rem;"></i>
                                        <span>{{ $item->quantity }} × {{ $item->containerSizeLabel() }}</span>
                                        <span style="color:var(--text-muted);font-weight:400;font-size:.75rem;">(₱{{ number_format($item->subtotal, 2) }})</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div style="display:flex;align-items:center;gap:.45rem;font-weight:600;">
                                <i class="fa-solid {{ $order->containerSizeIcon() }}" style="color:var(--accent);font-size:.85rem;"></i>
                                <span>{{ $order->quantity }} × {{ $order->containerSizeLabel() }}</span>
                            </div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-top:.15rem;">
                                ₱{{ number_format($order->unitPrice(), 2) }} / unit
                            </div>
                        @endif
                    </td>
                    <td>
                        <span style="font-weight:700;font-size:.95rem;">{{ $order->totalQuantity() }} {{ $order->totalQuantity() === 1 ? 'unit' : 'units' }}</span>
                    </td>
                    <td>
                        <span style="font-weight:700;color:var(--accent);font-size:1.05rem;">
                            ₱{{ number_format($order->total_amount, 2) }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge"
                              style="display:inline-block;padding:.25rem .75rem;border-radius:99px;font-size:.78rem;font-weight:600;background:{{ $order->statusColor() }}22;color:{{ $order->statusColor() }};border:1px solid {{ $order->statusColor() }}55;">
                            {{ $order->statusLabel() }}
                        </span>
                    </td>
                    <td style="color:var(--text-muted);font-size:.82rem;white-space:nowrap;">
                        {{ $order->created_at->format('M d, Y') }}
                        <div style="font-size:.72rem;">{{ $order->created_at->format('h:i A') }}</div>
                    </td>
                    <td>
                        @if($order->status === 'pending')
                        <form action="{{ route('customer.orders.cancel', $order) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to cancel Order {{ $order->order_number }}?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline" style="padding:.25rem .6rem;font-size:.75rem;border-color:rgba(239,68,68,.3);color:#fca5a5;">
                                <i class="fa-solid fa-ban"></i> Cancel
                            </button>
                        </form>
                        @else
                        <span style="color:var(--text-muted);font-size:.8rem;">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    @if($orders instanceof \Illuminate\Pagination\LengthAwarePaginator && $orders->hasPages())
    <div style="display:flex;justify-content:flex-end;margin-top:1.25rem;">
        {{ $orders->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
