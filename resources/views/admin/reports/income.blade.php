@extends('layouts.admin')

@section('title', 'Income & Profit Report – Aqua De Smiley Admin')

@section('content')
<!-- Header with Print & Actions -->
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1>
            <i class="fa-solid fa-coins" style="color:var(--admin-light)"></i>
            Income & Profitability Report
        </h1>
        <p>Profit & Loss (P&L) breakdown, product margin analysis, direct unit costs, and net earnings.</p>
    </div>
    <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
        <button type="button" onclick="window.print()" class="btn btn-outline">
            <i class="fa-solid fa-print"></i> Print Statement
        </button>
        <a href="{{ route('admin.reports.sales') }}" class="btn btn-outline">
            <i class="fa-solid fa-chart-line"></i> Sales Volume Report
        </a>
    </div>
</div>

<!-- Period Selection Bar -->
<div class="card filter-bar" style="margin-bottom:1.5rem;padding:1.25rem 1.5rem;">
    <form method="GET" action="{{ route('admin.reports.income') }}">
        <div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:1rem;align-items:center;">
            <span style="font-size:.82rem;font-weight:700;color:var(--admin-light);text-transform:uppercase;margin-right:.5rem;">
                <i class="fa-regular fa-calendar"></i> Reporting Period:
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

        <div style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:1;min-width:180px;">
                <label class="form-label" style="font-size:.78rem;">Custom Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-input" style="padding:.45rem .75rem;font-size:.85rem;">
            </div>
            <div style="flex:1;min-width:180px;">
                <label class="form-label" style="font-size:.78rem;">Custom End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-input" style="padding:.45rem .75rem;font-size:.85rem;">
            </div>
            <div>
                <input type="hidden" name="period" value="{{ request('start_date') ? 'custom' : $period }}">
                <button type="submit" class="btn btn-primary btn-sm" style="padding:.55rem 1.25rem;">
                    <i class="fa-solid fa-filter"></i> Apply Custom Dates
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Active Period Badge -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;padding:.75rem 1.25rem;background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.25);border-radius:12px;">
    <div style="display:flex;align-items:center;gap:.6rem;">
        <i class="fa-solid fa-scale-balanced" style="color:#34d399;font-size:1.15rem;"></i>
        <div>
            <span style="font-size:.78rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;display:block;">Accounting Window</span>
            <strong style="color:#fff;font-size:1rem;">{{ $periodLabel }}</strong>
        </div>
    </div>
    <div style="text-align:right;">
        <span style="font-size:.78rem;color:var(--text-muted);display:block;">Generated on</span>
        <span style="font-size:.85rem;color:var(--text);font-weight:600;">{{ now()->format('M d, Y h:i A') }}</span>
    </div>
</div>

<!-- Financial Summary KPI Cards -->
<div class="card-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon--blue"><i class="fa-solid fa-cash-register"></i></div>
        <div>
            <div class="stat-value">₱{{ number_format($grossRevenue, 2) }}</div>
            <div class="stat-label">Gross Operating Revenue</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--rose"><i class="fa-solid fa-boxes-packing"></i></div>
        <div>
            <div class="stat-value">₱{{ number_format($totalCOGS, 2) }}</div>
            <div class="stat-label">Cost of Goods Sold (COGS)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--purple"><i class="fa-solid fa-chart-pie"></i></div>
        <div>
            <div class="stat-value">₱{{ number_format($grossProfit, 2) }}</div>
            <div class="stat-label">Gross Profit ({{ $grossMargin }}% Margin)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--green"><i class="fa-solid fa-vault"></i></div>
        <div>
            <div class="stat-value" style="color:#34d399;">₱{{ number_format($netIncome, 2) }}</div>
            <div class="stat-label">Net Income ({{ $netMargin }}% Net Margin)</div>
        </div>
    </div>
</div>

<!-- Multi-bar Trend Chart (Revenue vs Costs vs Profit) -->
<div class="card" style="margin-bottom:2rem;">
    <h3 style="font-size:1.05rem;font-weight:700;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;">
        <i class="fa-solid fa-chart-column" style="color:var(--admin-light)"></i>
        Revenue, Expenses & Profit Trend (₱ PHP)
    </h3>
    <div style="position:relative;height:280px;">
        <canvas id="incomeTrendChart"></canvas>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:2rem;" class="financial-statement-grid">
    <!-- Formal Profit & Loss Statement (Income Statement) -->
    <div class="card">
        <h3 style="font-size:1.05rem;font-weight:700;margin-bottom:1.25rem;display:flex;align-items:center;gap:.5rem;">
            <i class="fa-solid fa-file-invoice-dollar" style="color:var(--admin-light)"></i>
            Profit & Loss (P&L) Summary Statement
        </h3>
        <div class="table-wrapper">
            <table>
                <tbody>
                    <!-- REVENUE SECTION -->
                    <tr style="background:rgba(56,189,248,.1);">
                        <td colspan="2" style="font-weight:700;color:#38bdf8;text-transform:uppercase;font-size:.78rem;letter-spacing:.05em;">
                            1. Operating Revenues
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-left:1.5rem;color:var(--text);">Purified Water Refills & Container Sales</td>
                        <td style="text-align:right;font-weight:600;color:#fff;">₱{{ number_format($grossRevenue, 2) }}</td>
                    </tr>
                    <tr style="border-top:2px solid rgba(255,255,255,.15);font-weight:700;">
                        <td style="color:#38bdf8;">Total Realized Revenue</td>
                        <td style="text-align:right;color:#38bdf8;font-size:1.05rem;">₱{{ number_format($grossRevenue, 2) }}</td>
                    </tr>

                    <!-- COGS SECTION -->
                    <tr style="background:rgba(244,63,94,.1);">
                        <td colspan="2" style="font-weight:700;color:#fb7185;text-transform:uppercase;font-size:.78rem;letter-spacing:.05em;">
                            2. Direct Costs (COGS)
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-left:1.5rem;color:var(--text);">Direct Container & Refill Unit Costs</td>
                        <td style="text-align:right;color:#fca5a5;">(₱{{ number_format($totalCOGS, 2) }})</td>
                    </tr>
                    <tr style="border-top:2px solid rgba(255,255,255,.15);font-weight:700;">
                        <td style="color:#fb7185;">Total Cost of Goods Sold</td>
                        <td style="text-align:right;color:#fca5a5;">(₱{{ number_format($totalCOGS, 2) }})</td>
                    </tr>

                    <!-- GROSS PROFIT -->
                    <tr style="background:rgba(139,92,246,.15);font-weight:700;">
                        <td style="color:#c4b5fd;">3. Gross Operating Profit</td>
                        <td style="text-align:right;color:#c4b5fd;font-size:1.05rem;">₱{{ number_format($grossProfit, 2) }}</td>
                    </tr>

                    <!-- OPERATING EXPENSES -->
                    <tr style="background:rgba(245,158,11,.1);">
                        <td colspan="2" style="font-weight:700;color:#fbbf24;text-transform:uppercase;font-size:.78rem;letter-spacing:.05em;">
                            4. Operating Overhead Benchmarks
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-left:1.5rem;color:var(--text);">Filtration, Maintenance & Utilities (12% Benchmark)</td>
                        <td style="text-align:right;color:#fcd34d;">(₱{{ number_format($operatingOverhead, 2) }})</td>
                    </tr>

                    <!-- NET INCOME -->
                    <tr style="background:rgba(16,185,129,.2);border-top:3px solid #10b981;font-weight:800;">
                        <td style="color:#34d399;font-size:1.05rem;">5. Net Operating Income</td>
                        <td style="text-align:right;color:#34d399;font-size:1.25rem;">₱{{ number_format($netIncome, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="color:var(--text-muted);font-size:.8rem;padding-left:1.5rem;">Net Operating Margin</td>
                        <td style="text-align:right;font-weight:700;color:#34d399;">{{ $netMargin }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Station Financial Insights & Metrics -->
    <div class="card">
        <h3 style="font-size:1.05rem;font-weight:700;margin-bottom:1.25rem;display:flex;align-items:center;gap:.5rem;">
            <i class="fa-solid fa-lightbulb" style="color:#fbbf24"></i>
            Financial Health & Performance Indicators
        </h3>
        
        <div style="display:flex;flex-direction:column;gap:1.25rem;">
            <div style="padding:1rem;border-radius:12px;background:rgba(255,255,255,.03);border:1px solid var(--glass-border);">
                <div style="display:flex;justify-content:space-between;margin-bottom:.4rem;">
                    <span style="font-weight:600;font-size:.88rem;color:var(--text);">Gross Margin Efficiency</span>
                    <strong style="color:#a78bfa;">{{ $grossMargin }}%</strong>
                </div>
                <div style="height:8px;background:rgba(255,255,255,.08);border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:{{ min(100, $grossMargin) }}%;background:linear-gradient(90deg, #8b5cf6, #38bdf8);"></div>
                </div>
                <small style="color:var(--text-muted);font-size:.74rem;margin-top:.4rem;display:block;">
                    Percentage of top-line revenue retained after direct container and refilling unit costs.
                </small>
            </div>

            <div style="padding:1rem;border-radius:12px;background:rgba(255,255,255,.03);border:1px solid var(--glass-border);">
                <div style="display:flex;justify-content:space-between;margin-bottom:.4rem;">
                    <span style="font-weight:600;font-size:.88rem;color:var(--text);">Net Profit Retained</span>
                    <strong style="color:#34d399;">{{ $netMargin }}%</strong>
                </div>
                <div style="height:8px;background:rgba(255,255,255,.08);border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:{{ min(100, $netMargin) }}%;background:linear-gradient(90deg, #10b981, #34d399);"></div>
                </div>
                <small style="color:var(--text-muted);font-size:.74rem;margin-top:.4rem;display:block;">
                    Take-home profit after factoring operating benchmark utilities, supplies and filtration costs.
                </small>
            </div>

            <div style="padding:1rem;border-radius:12px;background:rgba(56,189,248,.08);border:1px solid rgba(56,189,248,.2);">
                <h4 style="font-size:.88rem;font-weight:700;color:#38bdf8;margin-bottom:.4rem;">
                    <i class="fa-solid fa-circle-info"></i> Pricing Optimization Recommendation
                </h4>
                <p style="font-size:.8rem;color:var(--text-muted);line-height:1.45;">
                    Maintaining current refilling rates yields a strong margin above 60%. To maximize profit further, promote container bundles and accessory sales (e.g. electric rechargeable pumps) which generate high unit profitability.
                </p>
                <div style="margin-top:.75rem;">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline btn-sm" style="font-size:.78rem;padding:.3rem .75rem;">
                        <i class="fa-solid fa-sliders"></i> Manage Catalog Rates
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Profitability Matrix Table -->
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:.5rem;">
        <h3 style="font-size:1.05rem;font-weight:700;display:flex;align-items:center;gap:.5rem;">
            <i class="fa-solid fa-cubes-stacked" style="color:var(--admin-light)"></i>
            Product-by-Product Profitability Matrix
        </h3>
        <span style="font-size:.8rem;color:var(--text-muted)">
            Calculated from realized order items and assigned unit costs
        </span>
    </div>

    @if(empty($profitByProduct))
        <div style="text-align:center;padding:2.5rem 1rem;color:var(--text-muted);">
            <i class="fa-solid fa-coins" style="font-size:2.5rem;color:#475569;margin-bottom:.75rem;display:block;"></i>
            No paid order items recorded for this reporting period.
        </div>
    @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Container / Product</th>
                        <th>Units Sold</th>
                        <th>Gross Revenue (₱)</th>
                        <th>Total Cost (COGS)</th>
                        <th>Gross Profit (₱)</th>
                        <th>Profit Margin %</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($profitByProduct as $key => $row)
                    <tr>
                        <td>
                            <strong style="color:#fff;">{{ $row['name'] }}</strong>
                            <span style="font-size:.75rem;color:var(--text-muted);margin-left:.4rem;">({{ $row['short_name'] }})</span>
                        </td>
                        <td>
                            <span style="font-weight:700;font-size:1rem;">{{ number_format($row['quantity']) }}</span>
                        </td>
                        <td>
                            <span style="font-weight:700;color:#38bdf8;">₱{{ number_format($row['revenue'], 2) }}</span>
                        </td>
                        <td>
                            <span style="color:#fca5a5;">₱{{ number_format($row['cost'], 2) }}</span>
                        </td>
                        <td>
                            <span style="font-weight:700;color:#34d399;">+₱{{ number_format($row['gross_profit'], 2) }}</span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:.75rem;">
                                <div style="flex:1;height:8px;background:rgba(255,255,255,.08);border-radius:99px;overflow:hidden;">
                                    <div style="height:100%;width:{{ min(100, $row['margin']) }}%;background:linear-gradient(90deg, #10b981, #38bdf8);"></div>
                                </div>
                                <span style="font-size:.82rem;font-weight:700;color:#34d399;width:50px;">
                                    {{ $row['margin'] }}%
                                </span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@push('scripts')
<script>
    const incCtx = document.getElementById('incomeTrendChart').getContext('2d');
    const trendLabels = @json($incomeTrend['labels']);
    const revenues    = @json($incomeTrend['revenues']);
    const expenses    = @json($incomeTrend['expenses']);
    const profits     = @json($incomeTrend['profits']);

    new Chart(incCtx, {
        type: 'bar',
        data: {
            labels: trendLabels,
            datasets: [
                {
                    label: 'Gross Revenue (₱)',
                    data: revenues,
                    backgroundColor: 'rgba(56, 189, 248, 0.7)',
                    borderColor: '#38bdf8',
                    borderWidth: 1.5,
                    borderRadius: 5,
                },
                {
                    label: 'Total Expenses / COGS (₱)',
                    data: expenses,
                    backgroundColor: 'rgba(244, 63, 94, 0.6)',
                    borderColor: '#fb7185',
                    borderWidth: 1.5,
                    borderRadius: 5,
                },
                {
                    label: 'Net Profit (₱)',
                    data: profits,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: '#34d399',
                    borderWidth: 1.5,
                    borderRadius: 5,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { color: '#e2e8f0', font: { family: 'Outfit', size: 12 }, padding: 15 }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.dataset.label + ': ₱' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2});
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
</script>
@endpush
@endsection
