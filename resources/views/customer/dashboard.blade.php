@extends('layouts.app')

@section('title', 'My Account – Aqua De Smiley')

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1><i class="fa-solid fa-droplet" style="color:#34d399;margin-right:.6rem"></i>Welcome back, {{ $user->name }}!</h1>
        <p>Manage your purified water deliveries, track orders, and order refills.</p>
    </div>
    <a href="{{ route('customer.orders.create') }}" class="btn btn-primary" style="padding:.75rem 1.6rem;font-size:1rem;box-shadow:0 4px 18px rgba(14,165,233,.45);">
        <i class="fa-solid fa-cart-plus"></i> Place Water Order
    </a>
</div>

<!-- Welcome Banner -->
<div class="card" style="margin-bottom:2rem;background:linear-gradient(135deg,rgba(14,165,233,.18),rgba(16,185,129,.12));border-color:rgba(56,189,248,.3);display:flex;align-items:center;justify-content:space-between;gap:1.5rem;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:1.25rem;">
        <div style="width:68px;height:68px;border-radius:50%;background:linear-gradient(135deg,#0ea5e9,#10b981);display:grid;place-items:center;font-size:1.75rem;font-weight:800;color:#fff;flex-shrink:0;box-shadow:0 4px 16px rgba(14,165,233,.3);">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <div style="font-size:1.3rem;font-weight:700;">{{ $user->name }}</div>
            <div style="color:#94a3b8;font-size:.9rem;margin-top:.2rem;">{{ $user->email }}</div>
            <div style="margin-top:.45rem;display:flex;gap:.5rem;flex-wrap:wrap;">
                <span style="display:inline-flex;align-items:center;gap:.35rem;padding:.2rem .75rem;border-radius:99px;font-size:.75rem;font-weight:600;background:rgba(16,185,129,.2);color:#34d399;border:1px solid rgba(16,185,129,.4);">
                    <i class="fa-solid fa-circle-check"></i> Active Customer
                </span>
                <span style="display:inline-flex;align-items:center;gap:.35rem;padding:.2rem .75rem;border-radius:99px;font-size:.75rem;font-weight:600;background:rgba(14,165,233,.2);color:#38bdf8;border:1px solid rgba(14,165,233,.4);">
                    <i class="fa-solid fa-droplet"></i> Clean & Pure Water
                </span>
            </div>
        </div>
    </div>
    <div style="display:flex;gap:.75rem;">
        <a href="{{ route('customer.orders.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Order Water
        </a>
        <a href="{{ route('customer.orders.index') }}" class="btn btn-outline">
            <i class="fa-solid fa-receipt"></i> Order History
        </a>
    </div>
</div>

<!-- Dynamic Stat Cards -->
<div class="card-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon--blue"><i class="fa-solid fa-clock"></i></div>
        <div>
            <div class="stat-value">{{ $activeOrders }}</div>
            <div class="stat-label">Active Orders</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--green"><i class="fa-solid fa-circle-check"></i></div>
        <div>
            <div class="stat-value">{{ $completedOrders }}</div>
            <div class="stat-label">Completed Orders</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--amber"><i class="fa-solid fa-peso-sign"></i></div>
        <div>
            <div class="stat-value">₱{{ number_format($totalSpent, 2) }}</div>
            <div class="stat-label">Total Purchases</div>
        </div>
    </div>
</div>

<!-- Container Size & Pricing Menu -->
<div class="card" style="margin-bottom:2rem;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:.5rem;">
        <div>
            <h2 style="font-size:1.15rem;font-weight:700;display:flex;align-items:center;gap:.5rem;">
                <i class="fa-solid fa-tags" style="color:var(--accent);"></i> Available Container Sizes & Pricing
            </h2>
            <p style="font-size:.85rem;color:var(--text-muted);margin-top:.2rem;">Choose your preferred size. Available for both <strong>Water Refill</strong> and <strong>New Container Purchase</strong>.</p>
        </div>
        <a href="{{ route('customer.orders.create') }}" class="btn btn-outline" style="font-size:.8rem;padding:.4rem .9rem;">
            Order Now <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));gap:1.25rem;">
        <!-- 500mL Bottle -->
        <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:14px;padding:1.25rem;display:flex;flex-direction:column;justify-content:space-between;transition:transform .2s, border-color .2s;">
            <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem;">
                    <div style="width:40px;height:40px;border-radius:10px;background:rgba(56,189,248,.15);color:var(--accent);display:grid;place-items:center;font-size:1.2rem;">
                        <i class="fa-solid fa-bottle-water"></i>
                    </div>
                    <span style="font-size:.72rem;padding:.2rem .6rem;border-radius:99px;background:rgba(14,165,233,.2);color:#38bdf8;font-weight:600;">Personal Size</span>
                </div>
                <div style="font-size:1.1rem;font-weight:700;">500mL Bottle</div>
                <div style="font-size:.8rem;color:var(--text-muted);margin-top:.3rem;">Compact drinking bottle on-the-go.</div>
            </div>
            <div style="margin-top:1.25rem;padding-top:.85rem;border-top:1px solid rgba(255,255,255,.06);display:flex;align-items:center;justify-content:space-between;">
                <div style="font-size:1.4rem;font-weight:800;color:var(--accent);">₱5.00</div>
                <a href="{{ route('customer.orders.create') }}" class="btn btn-outline" style="padding:.3rem .75rem;font-size:.8rem;">Select</a>
            </div>
        </div>

        <!-- 1-Gallon -->
        <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:14px;padding:1.25rem;display:flex;flex-direction:column;justify-content:space-between;transition:transform .2s, border-color .2s;">
            <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem;">
                    <div style="width:40px;height:40px;border-radius:10px;background:rgba(16,185,129,.15);color:#34d399;display:grid;place-items:center;font-size:1.2rem;">
                        <i class="fa-solid fa-jug-detergent"></i>
                    </div>
                    <span style="font-size:.72rem;padding:.2rem .6rem;border-radius:99px;background:rgba(16,185,129,.2);color:#34d399;font-weight:600;">Medium Size</span>
                </div>
                <div style="font-size:1.1rem;font-weight:700;">1-Gallon Bottle</div>
                <div style="font-size:.8rem;color:var(--text-muted);margin-top:.3rem;">Great for workstations and small homes.</div>
            </div>
            <div style="margin-top:1.25rem;padding-top:.85rem;border-top:1px solid rgba(255,255,255,.06);display:flex;align-items:center;justify-content:space-between;">
                <div style="font-size:1.4rem;font-weight:800;color:#34d399;">₱15.00</div>
                <a href="{{ route('customer.orders.create') }}" class="btn btn-outline" style="padding:.3rem .75rem;font-size:.8rem;">Select</a>
            </div>
        </div>

        <!-- 5-Gallon -->
        <div style="background:rgba(14,165,233,.08);border:1px solid rgba(56,189,248,.3);border-radius:14px;padding:1.25rem;display:flex;flex-direction:column;justify-content:space-between;transition:transform .2s, border-color .2s;">
            <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem;">
                    <div style="width:40px;height:40px;border-radius:10px;background:rgba(56,189,248,.25);color:var(--accent);display:grid;place-items:center;font-size:1.2rem;">
                        <i class="fa-solid fa-bucket"></i>
                    </div>
                    <span style="font-size:.72rem;padding:.2rem .6rem;border-radius:99px;background:rgba(14,165,233,.3);color:#38bdf8;font-weight:700;">Best Value</span>
                </div>
                <div style="font-size:1.1rem;font-weight:700;">5-Gallon Container</div>
                <div style="font-size:.8rem;color:var(--text-muted);margin-top:.3rem;">Standard dispenser container for households & offices.</div>
            </div>
            <div style="margin-top:1.25rem;padding-top:.85rem;border-top:1px solid rgba(255,255,255,.06);display:flex;align-items:center;justify-content:space-between;">
                <div style="font-size:1.4rem;font-weight:800;color:var(--accent);">₱30.00</div>
                <a href="{{ route('customer.orders.create') }}" class="btn btn-primary" style="padding:.3rem .75rem;font-size:.8rem;">Select</a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Section -->
<div class="card" style="margin-bottom:2rem;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:.5rem;">
        <h2 style="font-size:1.15rem;font-weight:700;display:flex;align-items:center;gap:.5rem;">
            <i class="fa-solid fa-clock-rotate-left" style="color:var(--accent);"></i> Recent Orders
        </h2>
        <a href="{{ route('customer.orders.index') }}" class="btn btn-outline" style="font-size:.8rem;padding:.4rem .9rem;">
            View All Orders <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    @if($recentOrders->isEmpty())
        <div style="text-align:center;padding:2.5rem 1rem;color:var(--text-muted);">
            <i class="fa-solid fa-box-open" style="font-size:2rem;margin-bottom:.75rem;display:block;color:var(--accent);opacity:.6;"></i>
            No orders placed yet. Place your first order today!
            <div style="margin-top:1rem;">
                <a href="{{ route('customer.orders.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Order Water Now
                </a>
            </div>
        </div>
    @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Service Type</th>
                        <th>Container</th>
                        <th>Qty</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                    <tr>
                        <td>
                            <span style="font-weight:700;color:var(--accent);">{{ $order->order_number }}</span>
                        </td>
                        <td>
                            <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.2rem .6rem;border-radius:6px;font-size:.78rem;font-weight:600;background:rgba(14,165,233,.2);color:#38bdf8;border:1px solid rgba(14,165,233,.35);">
                                {{ $order->serviceTypesSummary() }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:.85rem;">
                                {{ $order->itemsSummary() }}
                            </div>
                        </td>
                        <td><span style="font-weight:700;">{{ $order->totalQuantity() }}</span></td>
                        <td><span style="font-weight:700;color:var(--accent);">₱{{ number_format($order->total_amount, 2) }}</span></td>
                        <td>
                            <span class="status-badge"
                                  style="display:inline-block;padding:.25rem .75rem;border-radius:99px;font-size:.75rem;font-weight:600;background:{{ $order->statusColor() }}22;color:{{ $order->statusColor() }};border:1px solid {{ $order->statusColor() }}55;">
                                {{ $order->statusLabel() }}
                            </span>
                        </td>
                        <td style="color:var(--text-muted);font-size:.82rem;">{{ $order->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Account Information -->
<div class="card">
    <h2 style="font-size:1.15rem;font-weight:700;margin-bottom:1.25rem;">
        <i class="fa-regular fa-user" style="color:#38bdf8;margin-right:.5rem"></i>Account Information
    </h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:1rem;">
        <div style="display:flex;align-items:center;gap:1rem;padding:.75rem 1rem;border-radius:10px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);">
            <i class="fa-regular fa-user" style="width:20px;color:#64748b;"></i>
            <div>
                <div style="font-size:.75rem;color:#64748b;font-weight:500;text-transform:uppercase;letter-spacing:.05em;">Full Name</div>
                <div style="font-weight:600;margin-top:.1rem;">{{ $user->name }}</div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:1rem;padding:.75rem 1rem;border-radius:10px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);">
            <i class="fa-regular fa-envelope" style="width:20px;color:#64748b;"></i>
            <div>
                <div style="font-size:.75rem;color:#64748b;font-weight:500;text-transform:uppercase;letter-spacing:.05em;">Email</div>
                <div style="font-weight:600;margin-top:.1rem;">{{ $user->email }}</div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:1rem;padding:.75rem 1rem;border-radius:10px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);">
            <i class="fa-solid fa-shield-halved" style="width:20px;color:#64748b;"></i>
            <div>
                <div style="font-size:.75rem;color:#64748b;font-weight:500;text-transform:uppercase;letter-spacing:.05em;">Role</div>
                <div style="font-weight:600;margin-top:.1rem;color:#34d399;">Customer</div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:1rem;padding:.75rem 1rem;border-radius:10px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);">
            <i class="fa-regular fa-calendar" style="width:20px;color:#64748b;"></i>
            <div>
                <div style="font-size:.75rem;color:#64748b;font-weight:500;text-transform:uppercase;letter-spacing:.05em;">Member Since</div>
                <div style="font-weight:600;margin-top:.1rem;">{{ $user->created_at->format('F d, Y') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
