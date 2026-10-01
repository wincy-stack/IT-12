@extends('layouts.staff')

@section('title', 'Customers – Aqua De Smiley')

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
    <div>
        <h1><i class="fa-solid fa-users" style="color:#38bdf8"></i> Customers</h1>
        <p>View and manage all registered customer accounts.</p>
    </div>
    <a href="{{ route('staff.customers.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-user-plus"></i> New Customer
    </a>
</div>

<!-- Search Bar -->
<form method="GET" action="{{ route('staff.customers.index') }}" style="margin-bottom:1.25rem;display:flex;gap:.75rem;">
    <input type="text" name="search" class="form-input" style="max-width:320px;"
        placeholder="Search by name or email…" value="{{ request('search') }}">
    <button type="submit" class="btn btn-outline"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
    @if(request('search'))
        <a href="{{ route('staff.customers.index') }}" class="btn btn-ghost"><i class="fa-solid fa-xmark"></i> Clear</a>
    @endif
</form>

<!-- Stats row -->
<div class="card-grid" style="grid-template-columns:repeat(auto-fit,minmax(160px,1fr));margin-bottom:1.25rem;">
    <div class="stat-card">
        <div class="stat-icon si-blue"><i class="fa-solid fa-users"></i></div>
        <div><div class="stat-value">{{ $totalCustomers }}</div><div class="stat-label">Total</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-green"><i class="fa-solid fa-calendar-day"></i></div>
        <div><div class="stat-value">{{ $todayCustomers }}</div><div class="stat-label">Added Today</div></div>
    </div>
</div>

<!-- Customer Table -->
<div class="card">
    <div class="table-wrap">
        @if($customers->isEmpty())
        <div style="text-align:center;padding:3rem;color:var(--text-muted);">
            <i class="fa-solid fa-users-slash" style="font-size:2.5rem;margin-bottom:1rem;display:block;"></i>
            @if(request('search'))
                No customers match "{{ request('search') }}".
            @else
                No customers yet.
            @endif
        </div>
        @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Orders</th>
                    <th>Joined</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $i => $customer)
                <tr>
                    <td style="color:var(--text-muted);">{{ $customers->firstItem() + $i }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:.65rem;">
                            <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#0ea5e9,#0369a1);display:grid;place-items:center;font-weight:700;font-size:.82rem;flex-shrink:0;">
                                {{ strtoupper(substr($customer->name,0,1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;">{{ $customer->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="color:var(--text-muted);">{{ $customer->email }}</td>
                    <td>
                        <span style="background:rgba(14,165,233,.15);color:#38bdf8;padding:.2rem .65rem;border-radius:99px;font-size:.78rem;font-weight:700;">
                            {{ $customer->orders_count }}
                        </span>
                    </td>
                    <td style="color:var(--text-muted);font-size:.82rem;">{{ $customer->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('staff.orders.create', ['customer_id' => $customer->id]) }}" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-circle-plus"></i> Order
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    @if($customers->hasPages())
    <div style="display:flex;justify-content:flex-end;margin-top:1rem;">
        {{ $customers->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
