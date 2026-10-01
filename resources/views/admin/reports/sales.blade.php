@extends('layouts.admin')

@section('title', 'Sales Report – Aqua De Smiley Admin')

@section('content')
<!-- Header with Print & Actions -->
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1>
            <i class="fa-solid fa-chart-line" style="color:var(--admin-light)"></i>
            Sales Revenue & Order Report
        </h1>
        <p>Comprehensive transaction analysis, container volume breakdown, and revenue tracking.</p>
    </div>
    <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
        <button type="button" onclick="window.print()" class="btn btn-outline">
            <i class="fa-solid fa-print"></i> Print / Export Report
        </button>
        <a href="{{ route('admin.reports.income') }}" class="btn btn-primary">
            <i class="fa-solid fa-coins"></i> View Income & Profit Report
        </a>
    </div>
</div>

<!-- Period Selection and Filter Bar -->
<div class="card filter-bar" style="margin-bottom:1.5rem;padding:1.25rem 1.5rem;">
    <form method="GET" action="{{ route('admin.reports.sales') }}" id="salesFilterForm">
        <div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:1rem;align-items:center;">
            <span style="font-size:.82rem;font-weight:700;color:var(--admin-light);text-transform:uppercase;margin-right:.5rem;">
                <i class="fa-regular fa-calendar"></i> Period:
            </span>
            @php
                $periods = [
                    'today'      => 'Today',
                    'yesterday'  => 'Yesterday',
                    'this_week'  => 'This Week',
                    'this_month' => 'This Month',
                    'last_month' => 'Last Month',
                    'this_year'  => 'This Year',
                    'all'        => 'All Time',
                ];
            @endphp
            @foreach($periods as $key => $lbl)
                <button type="submit" name="period" value="{{ $key }}" class="btn btn-sm {{ $period === $key ? 'btn-primary' : 'btn-outline' }}" style="padding:.35rem .85rem;">
                    {{ $lbl }}
                </button>
            @endforeach
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:1rem;align-items:flex-end;">
            <!-- Start Date -->
            <div>
                <label class="form-label" style="font-size:.78rem;">Custom Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-input" style="padding:.45rem .75rem;font-size:.85rem;">
            </div>

            <!-- End Date -->
            <div>
                <label class="form-label" style="font-size:.78rem;">Custom End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-input" style="padding:.45rem .75rem;font-size:.85rem;">
            </div>

            <!-- Payment Status -->
            <div>
                <label class="form-label" style="font-size:.78rem;">Payment Status</label>
                <select name="payment_status" class="form-select" style="padding:.45rem .75rem;font-size:.85rem;">
                    <option value="all">All Payment Status</option>
                    <option value="paid" {{ $paymentStatus === 'paid' ? 'selected' : '' }}>Paid Only</option>
                    <option value="unpaid" {{ $paymentStatus === 'unpaid' ? 'selected' : '' }}>Pending / Unpaid</option>
                </select>
            </div>

            <!-- Order Status -->
            <div>
                <label class="form-label" style="font-size:.78rem;">Order Status</label>
                <select name="order_status" class="form-select" style="padding:.45rem .75rem;font-size:.85rem;">
                    <option value="all">All Order Status</option>
                    <option value="completed" {{ $orderStatus === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="confirmed" {{ $orderStatus === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="pending" {{ $orderStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="cancelled" {{ $orderStatus === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Action buttons -->
            <div style="display:flex;gap:.5rem;">
                <input type="hidden" name="period" value="{{ $period === 'custom' || request('start_date') ? 'custom' : $period }}">
                <button type="submit" class="btn btn-primary btn-sm" style="flex:1;padding:.55rem 1rem;">
                    <i class="fa-solid fa-magnifying-glass"></i> Apply
                </button>
                <a href="{{ route('admin.reports.sales') }}" class="btn btn-outline btn-sm" style="padding:.55rem .85rem;" title="Reset Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Active Filter Banner (Visible in Print too) -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;padding:.75rem 1.25rem;background:rgba(139,92,246,.12);border:1px solid rgba(139,92,246,.25);border-radius:12px;">
    <div style="display:flex;align-items:center;gap:.6rem;">
        <i class="fa-solid fa-calendar-check" style="color:var(--admin-light);font-size:1.1rem;"></i>
        <div>
            <span style="font-size:.78rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;display:block;">Reporting Window</span>
            <strong style="color:#fff;font-size:1rem;">{{ $periodLabel }}</strong>
        </div>
    </div>
    <div style="text-align:right;">
        <span style="font-size:.78rem;color:var(--text-muted);display:block;">Generated on</span>
        <span style="font-size:.85rem;color:var(--text);font-weight:600;">{{ now()->format('M d, Y h:i A') }}</span>
    </div>
</div>

<!-- Top KPI Summary Cards -->
<div class="card-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon--green"><i class="fa-solid fa-sack-dollar"></i></div>
        <div>
            <div class="stat-value">₱{{ number_format($totalRevenue, 2) }}</div>
            <div class="stat-label">Realized Paid Revenue</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--blue"><i class="fa-solid fa-cash-register"></i></div>
        <div>
            <div class="stat-value">₱{{ number_format($grossSalesVolume, 2) }}</div>
            <div class="stat-label">Gross Sales Volume</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--purple"><i class="fa-solid fa-receipt"></i></div>
        <div>
            <div class="stat-value">{{ $totalOrdersCount }}</div>
            <div class="stat-label">Total Orders ({{ $paidOrdersCount }} Paid)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--amber"><i class="fa-solid fa-bottle-water"></i></div>
        <div>
            <div class="stat-value">{{ number_format($totalUnitsSold) }}</div>
            <div class="stat-label">Containers & Bottles Sold</div>
        </div>
    </div>
</div>

<!-- Secondary Stats Row -->
<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:1rem;margin-bottom:2rem;">
    <div class="card" style="padding:1rem 1.25rem;display:flex;align-items:center;justify-content:space-between;">
        <div>
            <div style="font-size:.78rem;color:var(--text-muted);">Average Order Value</div>
            <div style="font-size:1.25rem;font-weight:700;color:#fff;">₱{{ number_format($avgOrderValue, 2) }}</div>
        </div>
        <i class="fa-solid fa-calculator" style="font-size:1.5rem;color:rgba(139,92,246,.5);"></i>
    </div>
    <div class="card" style="padding:1rem 1.25rem;display:flex;align-items:center;justify-content:space-between;">
        <div>
            <div style="font-size:.78rem;color:var(--text-muted);">Pending Receivables</div>
            <div style="font-size:1.25rem;font-weight:700;color:{{ $unpaidAmount > 0 ? '#fbbf24' : '#34d399' }};">
                ₱{{ number_format($unpaidAmount, 2) }}
            </div>
        </div>
        <i class="fa-solid fa-clock" style="font-size:1.5rem;color:rgba(251,191,36,.5);"></i>
    </div>
    <div class="card" style="padding:1rem 1.25rem;display:flex;align-items:center;justify-content:space-between;">
        <div>
            <div style="font-size:.78rem;color:var(--text-muted);">Water Refills Sold</div>
            <div style="font-size:1.25rem;font-weight:700;color:#38bdf8;">
                {{ $salesByService['refill']['quantity'] }} refills
            </div>
        </div>
        <i class="fa-solid fa-arrows-rotate" style="font-size:1.5rem;color:rgba(56,189,248,.5);"></i>
    </div>
    <div class="card" style="padding:1rem 1.25rem;display:flex;align-items:center;justify-content:space-between;">
        <div>
            <div style="font-size:.78rem;color:var(--text-muted);">New Containers Sold</div>
            <div style="font-size:1.25rem;font-weight:700;color:#34d399;">
                {{ $salesByService['new_container']['quantity'] }} units
            </div>
        </div>
        <i class="fa-solid fa-box-open" style="font-size:1.5rem;color:rgba(16,185,129,.5);"></i>
    </div>
</div>

<!-- Visual Charts Section -->
<div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;margin-bottom:2rem;" class="charts-grid">
    <!-- Sales Trend Line Chart -->
    <div class="card">
        <h3 style="font-size:1.05rem;font-weight:700;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;">
            <i class="fa-solid fa-chart-area" style="color:var(--admin-light)"></i>
            Sales Revenue Trend (₱ PHP)
        </h3>
        <div style="position:relative;height:260px;">
            <canvas id="salesTrendChart"></canvas>
        </div>
    </div>

    <!-- Product / Container Breakdown Chart -->
    <div class="card">
        <h3 style="font-size:1.05rem;font-weight:700;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;">
            <i class="fa-solid fa-chart-pie" style="color:var(--admin-light)"></i>
            Volume by Container Size
        </h3>
        <div style="position:relative;height:260px;">
            <canvas id="containerPieChart"></canvas>
        </div>
    </div>
</div>

<!-- Container Size Sales Breakdown Table -->
<div class="card" style="margin-bottom:2rem;">
    <h3 style="font-size:1.05rem;font-weight:700;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;">
        <i class="fa-solid fa-boxes-stacked" style="color:var(--admin-light)"></i>
        Product & Container Performance Breakdown
    </h3>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Container Size / Product</th>
                    <th>Units Sold</th>
                    <th>Current Unit Rate</th>
                    <th>Gross Revenue (₱)</th>
                    <th>Share of Gross Sales</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesBySize as $key => $item)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.6rem;">
                            <i class="fa-solid {{ $item['icon'] }}" style="color:var(--admin-light);width:18px;"></i>
                            <span style="font-weight:600;color:#fff;">{{ $item['name'] }}</span>
                        </div>
                    </td>
                    <td>
                        <span style="font-weight:700;font-size:1rem;">{{ number_format($item['quantity']) }}</span>
                    </td>
                    <td>
                        <span style="color:var(--text-muted);">
                            ₱{{ number_format(\App\Models\Order::getPriceForSize($key), 2) }}
                        </span>
                    </td>
                    <td>
                        <span style="font-weight:700;color:#38bdf8;">₱{{ number_format($item['subtotal'], 2) }}</span>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:.75rem;">
                            <div style="flex:1;height:8px;background:rgba(255,255,255,.08);border-radius:99px;overflow:hidden;">
                                <div style="height:100%;width:{{ $item['share'] }}%;background:linear-gradient(90deg, var(--admin-purple), var(--accent));"></div>
                            </div>
                            <span style="font-size:.8rem;font-weight:700;color:var(--admin-light);width:45px;">
                                {{ $item['share'] }}%
                            </span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Detailed Orders Transaction Ledger -->
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:.5rem;">
        <h3 style="font-size:1.05rem;font-weight:700;display:flex;align-items:center;gap:.5rem;">
            <i class="fa-solid fa-list-ol" style="color:var(--admin-light)"></i>
            Transaction Details & Orders Ledger
        </h3>
        <span style="font-size:.82rem;color:var(--text-muted)">
            Showing {{ $orders->count() }} of {{ $orders->total() }} matching records
        </span>
    </div>

    @if($orders->isEmpty())
        <div style="text-align:center;padding:2.5rem 1rem;color:var(--text-muted);">
            <i class="fa-solid fa-receipt" style="font-size:2.5rem;color:#475569;margin-bottom:.75rem;display:block;"></i>
            No orders found for the selected criteria and period.
        </div>
    @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Order Items & Size</th>
                        <th>Payment Method</th>
                        <th>Payment Status</th>
                        <th>Order Status</th>
                        <th>Date & Time</th>
                        <th style="text-align:right;">Amount (₱)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>
                            <strong style="color:var(--accent);">{{ $order->order_number }}</strong>
                        </td>
                        <td>
                            <div style="font-weight:600;color:#fff;">{{ $order->customer->name ?? 'Walk-in / Unknown' }}</div>
                            <div style="font-size:.76rem;color:var(--text-muted);">{{ $order->customer->email ?? '' }}</div>
                        </td>
                        <td>
                            <span style="font-size:.85rem;color:#e2e8f0;">
                                {{ $order->itemsSummary() }}
                            </span>
                        </td>
                        <td>
                            <span style="text-transform:capitalize;font-size:.85rem;">
                                @if($order->payment_method === 'cash')
                                    <i class="fa-solid fa-money-bill-wave" style="color:#34d399;margin-right:.3rem;"></i> Cash
                                @elseif($order->payment_method === 'gcash')
                                    <i class="fa-solid fa-mobile-screen-button" style="color:#38bdf8;margin-right:.3rem;"></i> GCash
                                @elseif($order->payment_method === 'bank_transfer')
                                    <i class="fa-solid fa-building-columns" style="color:#a78bfa;margin-right:.3rem;"></i> Bank
                                @else
                                    <span style="color:#64748b;">—</span>
                                @endif
                            </span>
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
                        <td style="font-size:.82rem;color:var(--text-muted);">
                            {{ $order->created_at->format('M d, Y') }}
                            <div style="font-size:.75rem;">{{ $order->created_at->format('h:i A') }}</div>
                        </td>
                        <td style="text-align:right;">
                            <strong style="color:#38bdf8;font-size:.95rem;">
                                ₱{{ number_format($order->total_amount, 2) }}
                            </strong>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:1.25rem;">
            {{ $orders->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Sales Trend Chart
    const trendCtx = document.getElementById('salesTrendChart').getContext('2d');
    const trendLabels = @json($trendData['labels']);
    const trendValues = @json($trendData['values']);

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Gross Sales (₱)',
                data: trendValues,
                borderColor: '#a78bfa',
                backgroundColor: 'rgba(167, 139, 250, 0.15)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#8b5cf6',
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' Sales: ₱' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#94a3b8', font: { family: 'Outfit', size: 11 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: {
                        color: '#94a3b8',
                        font: { family: 'Outfit', size: 11 },
                        callback: function(value) { return '₱' + value; }
                    }
                }
            }
        }
    });

    // Container Size Donut Chart
    const pieCtx = document.getElementById('containerPieChart').getContext('2d');
    const pieLabels = [];
    const pieQuantities = [];
    @foreach($salesBySize as $sz)
        pieLabels.push('{{ $sz['short_name'] }}');
        pieQuantities.push({{ $sz['quantity'] }});
    @endforeach

    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: pieLabels,
            datasets: [{
                data: pieQuantities,
                backgroundColor: ['#0ea5e9', '#38bdf8', '#8b5cf6', '#10b981', '#f59e0b'],
                borderColor: '#0b182d',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#e2e8f0', font: { family: 'Outfit', size: 12 }, padding: 14 }
                }
            },
            cutout: '65%'
        }
    });
</script>
@endpush
@endsection
