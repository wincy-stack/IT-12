@extends('layouts.staff')

@section('title', 'Staff Dashboard – Aqua De Smiley')

@section('content')
<div class="page-header">
    <h1><i class="fa-solid fa-gauge-high" style="color:#38bdf8"></i> Dashboard</h1>
    <p>Welcome back, <strong>{{ auth()->user()?->name ?? 'Staff' }}</strong>! Here's what's happening today.</p>
</div>

<!-- Stat Row -->
<div class="card-grid">
    <div class="stat-card">
        <div class="stat-icon si-amber"><i class="fa-solid fa-hourglass-half"></i></div>
        <div>
            <div class="stat-value">{{ $stats['pending'] }}</div>
            <div class="stat-label">Pending Orders</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-blue"><i class="fa-solid fa-circle-check"></i></div>
        <div>
            <div class="stat-value">{{ $stats['confirmed'] }}</div>
            <div class="stat-label">Confirmed Orders</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-green"><i class="fa-solid fa-clipboard-check"></i></div>
        <div>
            <div class="stat-value">{{ $stats['completed'] }}</div>
            <div class="stat-label">Completed Orders</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-blue"><i class="fa-solid fa-users"></i></div>
        <div>
            <div class="stat-value">{{ $stats['customers'] }}</div>
            <div class="stat-label">Total Customers</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-red"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div>
            <div class="stat-value">{{ $stats['low_stock'] }}</div>
            <div class="stat-label">Low Stock Items</div>
        </div>
    </div>
</div>

<!-- Two-col row -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;" class="dashboard-grid">

    <!-- Recent Orders -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-clock-rotate-left" style="color:#38bdf8"></i> Recent Orders</span>
            <a href="{{ route('staff.orders.index') }}" class="btn btn-sm btn-outline">View All</a>
        </div>
        @if($recentOrders->isEmpty())
            <p style="color:var(--text-muted);text-align:center;padding:2rem 0;">No orders yet.</p>
        @else
        <div style="display:flex;flex-direction:column;gap:.6rem;">
            @foreach($recentOrders as $order)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:.65rem .85rem;border-radius:9px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);">
                <div>
                    <div style="font-weight:600;font-size:.875rem;">{{ $order->order_number }}</div>
                    <div style="color:var(--text-muted);font-size:.78rem;">{{ $order->customer->name ?? '—' }}</div>
                </div>
                <span class="status-badge" style="background:{{ $order->statusColor() }}22;color:{{ $order->statusColor() }};border:1px solid {{ $order->statusColor() }}55;">
                    {{ $order->statusLabel() }}
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Low Stock Alert -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-boxes-stacked" style="color:#f87171"></i> Low Stock Alerts</span>
            <a href="{{ route('staff.supplies.index') }}" class="btn btn-sm btn-outline">View All</a>
        </div>
        @if($lowStocks->isEmpty())
            <p style="color:var(--text-muted);text-align:center;padding:2rem 0;"><i class="fa-solid fa-circle-check" style="color:#34d399"></i> All stock levels are good!</p>
        @else
        <div style="display:flex;flex-direction:column;gap:.6rem;">
            @foreach($lowStocks as $supply)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:.65rem .85rem;border-radius:9px;background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.18);">
                <div>
                    <div style="font-weight:600;font-size:.875rem;">{{ $supply->name }}</div>
                    <div style="color:var(--text-muted);font-size:.78rem;">{{ $supply->category }}</div>
                </div>
                <span style="color:#f87171;font-weight:700;font-size:.85rem;">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $supply->quantity }} {{ $supply->unit }}
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
@media(max-width:900px){.dashboard-grid{grid-template-columns:1fr!important;}}
</style>
@endpush
@endsection
