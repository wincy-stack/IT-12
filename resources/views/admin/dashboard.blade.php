@extends('layouts.admin')

@section('title', 'Admin Executive Dashboard – Aqua De Smiley')

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1>
            <i class="fa-solid fa-gauge-high" style="color:var(--admin-light)"></i>
            Admin Executive Dashboard
        </h1>
        <p>Welcome back, {{ auth()->user()->name }}! Station financial overview and operations control.</p>
    </div>
    <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline">
            <i class="fa-solid fa-tags"></i> Manage Products & Prices
        </a>
        <a href="{{ route('admin.reports.sales') }}" class="btn btn-primary">
            <i class="fa-solid fa-chart-line"></i> View Sales Report
        </a>
        <a href="{{ route('admin.reports.income') }}" class="btn btn-cyan">
            <i class="fa-solid fa-coins"></i> View Income Report
        </a>
    </div>
</div>

<!-- Primary Financial & Operational Stat Cards -->
<div class="card-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon--green"><i class="fa-solid fa-sack-dollar"></i></div>
        <div>
            <div class="stat-value">₱{{ number_format($totalRevenue, 2) }}</div>
            <div class="stat-label">Total Realized Revenue (Paid)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--purple"><i class="fa-solid fa-tags"></i></div>
        <div>
            <div class="stat-value">{{ $activeProducts }}</div>
            <div class="stat-label">Active Products & Pricing</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--blue"><i class="fa-solid fa-receipt"></i></div>
        <div>
            <div class="stat-value">{{ $totalOrders }}</div>
            <div class="stat-label">Total Orders ({{ $pendingOrders }} Pending)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--amber"><i class="fa-solid fa-users"></i></div>
        <div>
            <div class="stat-value">{{ $totalCustomers + $totalStaff }}</div>
            <div class="stat-label">Station Customers ({{ $totalCustomers }}) & Staff ({{ $totalStaff }})</div>
        </div>
    </div>
</div>

<!-- Quick Navigation Action Bar -->
<div class="card" style="margin-bottom:2rem;background:linear-gradient(135deg, rgba(139,92,246,.12), rgba(14,165,233,.08));border:1px solid rgba(139,92,246,.25);">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.5rem;color:#fff;">
                <i class="fa-solid fa-bolt" style="color:#fbbf24;"></i>
                Station Management Quick Actions
            </h2>
            <p style="font-size:.85rem;color:var(--text-muted);margin-top:.2rem;">
                Jump directly to price maintenance, revenue auditing, or station walk-in POS.
            </p>
        </div>
        <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline" style="background:rgba(255,255,255,.06);">
                <i class="fa-solid fa-tags" style="color:var(--admin-light)"></i> Product Pricing
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-outline" style="background:rgba(255,255,255,.06);">
                <i class="fa-solid fa-plus-circle" style="color:#38bdf8"></i> Add Product
            </a>
            <a href="{{ route('admin.reports.sales') }}" class="btn btn-outline" style="background:rgba(255,255,255,.06);">
                <i class="fa-solid fa-chart-line" style="color:#34d399"></i> Sales Report
            </a>
            <a href="{{ route('admin.reports.income') }}" class="btn btn-outline" style="background:rgba(255,255,255,.06);">
                <i class="fa-solid fa-coins" style="color:#fbbf24"></i> Income / P&L
            </a>
            <a href="{{ route('staff.payments.index') }}" class="btn btn-cyan">
                <i class="fa-solid fa-cash-register"></i> Walk-in POS
            </a>
        </div>
    </div>
</div>

<!-- Two-Column Grid: Recent Orders & Catalog Glance -->
<div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;" class="dashboard-grid">
    <!-- Left Column: Recent Orders Ledger -->
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
            <h3 style="font-size:1.05rem;font-weight:700;display:flex;align-items:center;gap:.5rem;">
                <i class="fa-solid fa-clock-rotate-left" style="color:var(--admin-light)"></i>
                Recent Customer Orders
            </h3>
            <a href="{{ route('admin.reports.sales') }}" style="font-size:.8rem;color:var(--accent);text-decoration:none;font-weight:600;">
                View All in Sales Report <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        @if($recentOrders->isEmpty())
            <div style="text-align:center;padding:2rem 1rem;color:var(--text-muted);">
                <i class="fa-solid fa-inbox" style="font-size:2rem;color:#475569;margin-bottom:.5rem;display:block;"></i>
                No orders recorded yet.
            </div>
        @else
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Items Summary</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th style="text-align:right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                        <tr>
                            <td>
                                <strong style="color:var(--accent);">{{ $order->order_number }}</strong>
                            </td>
                            <td>
                                <div style="font-weight:600;color:#fff;">{{ $order->customer->name ?? 'Walk-in Customer' }}</div>
                                <div style="font-size:.75rem;color:var(--text-muted);">{{ $order->created_at->diffForHumans() }}</div>
                            </td>
                            <td style="font-size:.82rem;">
                                {{ $order->itemsSummary() }}
                            </td>
                            <td>
                                @if($order->payment_status === 'paid')
                                    <span class="badge badge--success"><i class="fa-solid fa-check"></i> Paid</span>
                                @else
                                    <span class="badge badge--warning"><i class="fa-solid fa-clock"></i> Unpaid</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="background:rgba(255,255,255,.07);color:{{ $order->statusColor() }};border:1px solid {{ $order->statusColor() }}44;">
                                    {{ $order->statusLabel() }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <strong style="color:#38bdf8;">₱{{ number_format($order->total_amount, 2) }}</strong>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Right Column: Active Products & System Status -->
    <div style="display:flex;flex-direction:column;gap:1.5rem;">
        <!-- Product Pricing Quick Glance -->
        <div class="card">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <h3 style="font-size:1rem;font-weight:700;display:flex;align-items:center;gap:.4rem;">
                    <i class="fa-solid fa-tags" style="color:var(--admin-light)"></i>
                    Active Product Rates
                </h3>
                <a href="{{ route('admin.products.index') }}" style="font-size:.78rem;color:var(--accent);text-decoration:none;font-weight:600;">
                    Manage All
                </a>
            </div>

            <div style="display:flex;flex-direction:column;gap:.75rem;">
                @foreach($products as $p)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:.65rem .85rem;border-radius:10px;background:rgba(255,255,255,.03);border:1px solid var(--glass-border);">
                    <div style="display:flex;align-items:center;gap:.6rem;">
                        <i class="fa-solid {{ $p->icon ?: 'fa-droplet' }}" style="color:var(--admin-light);width:16px;"></i>
                        <div>
                            <div style="font-size:.85rem;font-weight:600;color:#fff;">{{ $p->name }}</div>
                            <div style="font-size:.72rem;color:var(--text-muted);">Cost: ₱{{ number_format($p->cost_price, 2) }} • Margin: {{ $p->profitMarginPercent() }}%</div>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <span style="font-size:.95rem;font-weight:800;color:#38bdf8;">₱{{ number_format($p->price, 2) }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            <div style="margin-top:1rem;text-align:center;">
                <a href="{{ route('admin.products.create') }}" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;">
                    <i class="fa-solid fa-plus"></i> Add New Product / Rate
                </a>
            </div>
        </div>

        <!-- Inventory Stock Alert / Status -->
        <div class="card">
            <h3 style="font-size:1rem;font-weight:700;margin-bottom:.85rem;display:flex;align-items:center;gap:.4rem;">
                <i class="fa-solid fa-shield-halved" style="color:#38bdf8"></i>
                Station Status & Inventory
            </h3>
            <div style="display:flex;flex-direction:column;gap:.75rem;font-size:.85rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="color:var(--text-muted);">Low Stock Supplies Alert:</span>
                    @if($lowSupplies > 0)
                        <span class="badge badge--warning"><i class="fa-solid fa-triangle-exclamation"></i> {{ $lowSupplies }} low items</span>
                    @else
                        <span class="badge badge--success"><i class="fa-solid fa-check"></i> All Healthy</span>
                    @endif
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="color:var(--text-muted);">Registered Staff Operators:</span>
                    <strong style="color:#fff;">{{ $totalStaff }} active</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="color:var(--text-muted);">Registered Customers:</span>
                    <strong style="color:#fff;">{{ $totalCustomers }} customers</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
