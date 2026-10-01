@extends('layouts.admin')

@section('title', 'Products & Prices – Aqua De Smiley Admin')

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1>
            <i class="fa-solid fa-tags" style="color:var(--admin-light)"></i>
            Products & Prices Management
        </h1>
        <p>Set selling rates, maintain unit costs, manage inventory, and configure station products.</p>
    </div>
    <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
        <a href="{{ route('admin.reports.sales') }}" class="btn btn-outline">
            <i class="fa-solid fa-chart-line"></i> View Sales Report
        </a>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus-circle"></i> Add New Product
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="card-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon--purple"><i class="fa-solid fa-boxes-stacked"></i></div>
        <div>
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total Catalog Products</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--green"><i class="fa-solid fa-circle-check"></i></div>
        <div>
            <div class="stat-value">{{ $stats['active'] }}</div>
            <div class="stat-label">Active For Customers / POS</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--blue"><i class="fa-solid fa-peso-sign"></i></div>
        <div>
            <div class="stat-value">₱{{ number_format($stats['avg_price'], 2) }}</div>
            <div class="stat-label">Average Catalog Price</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--amber"><i class="fa-solid fa-bottle-water"></i></div>
        <div>
            <div class="stat-value">{{ $stats['refill_count'] }}</div>
            <div class="stat-label">Refill Sizes Available</div>
        </div>
    </div>
</div>

<!-- Filter & Search Toolbar -->
<div class="card" style="margin-bottom:1.5rem;padding:1.1rem 1.4rem;">
    <form method="GET" action="{{ route('admin.products.index') }}" style="display:flex;flex-wrap:wrap;gap:.9rem;align-items:center;">
        <div style="flex:1;min-width:220px;position:relative;">
            <input type="text" name="search" value="{{ $search }}" class="form-input" placeholder="Search product name, category or size key…" style="padding-left:2.4rem;">
            <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:.9rem;top:50%;transform:translateY(-50%);color:#64748b;"></i>
        </div>

        <div style="width:190px;">
            <select name="category" class="form-select" onchange="this.form.submit()">
                <option value="all">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>

        <div style="width:160px;">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active Only</option>
                <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
            </select>
        </div>

        <div style="display:flex;gap:.5rem;">
            <button type="submit" class="btn btn-primary btn-sm" style="padding:.6rem 1rem;">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            @if($search || $category !== 'all' || $status !== 'all')
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline btn-sm" style="padding:.6rem .9rem;">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Products Table -->
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem;">
        <h2 style="font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.5rem;">
            <i class="fa-solid fa-list-check" style="color:var(--admin-light)"></i>
            Product Catalog & Pricing Matrix
        </h2>
        <span style="font-size:.8rem;color:var(--text-muted)">
            Prices set here update customer refill rates & staff POS immediately.
        </span>
    </div>

    @if($products->isEmpty())
        <div style="text-align:center;padding:3rem 1rem;">
            <i class="fa-solid fa-box-open" style="font-size:3rem;color:#475569;margin-bottom:1rem;display:block;"></i>
            <h3 style="font-size:1.15rem;font-weight:600;color:var(--text);">No Products Found</h3>
            <p style="color:var(--text-muted);margin:.5rem 0 1.25rem;">Try modifying your search or add a new water station product.</p>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Add First Product
            </a>
        </div>
    @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Product & Details</th>
                        <th>Category</th>
                        <th>Selling Price</th>
                        <th>Unit Cost</th>
                        <th>Unit Profit / Margin</th>
                        <th>Stock Level</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:.75rem;">
                                <div style="width:40px;height:40px;border-radius:10px;background:rgba(139,92,246,.15);border:1px solid rgba(139,92,246,.3);display:grid;place-items:center;font-size:1.15rem;color:var(--admin-light);flex-shrink:0;">
                                    <i class="fa-solid {{ $product->icon ?: 'fa-droplet' }}"></i>
                                </div>
                                <div>
                                    <div style="font-weight:700;color:#fff;">{{ $product->name }}</div>
                                    <div style="font-size:.76rem;color:var(--text-muted);display:flex;align-items:center;gap:.4rem;">
                                        @if($product->size_key)
                                            <span style="background:rgba(56,189,248,.15);color:#38bdf8;padding:.1rem .4rem;border-radius:4px;font-family:monospace;font-size:.72rem;">
                                                size: {{ $product->size_key }}
                                            </span>
                                        @endif
                                        <span>per {{ $product->unit }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge--purple">{{ $product->category }}</span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:.4rem;">
                                <span style="font-size:1.05rem;font-weight:800;color:#38bdf8;">
                                    ₱{{ number_format($product->price, 2) }}
                                </span>
                                <button type="button" class="btn btn-outline btn-sm" onclick="openPriceModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, {{ $product->cost_price }})" title="Quick edit price" style="padding:.2rem .45rem;font-size:.7rem;">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                            </div>
                        </td>
                        <td>
                            <span style="color:#94a3b8;font-weight:600;">
                                ₱{{ number_format($product->cost_price, 2) }}
                            </span>
                        </td>
                        <td>
                            <div>
                                <span style="color:#34d399;font-weight:700;">+₱{{ number_format($product->unitProfit(), 2) }}</span>
                                <span style="font-size:.75rem;color:rgba(52,211,153,.8);margin-left:.3rem;">
                                    ({{ $product->profitMarginPercent() }}%)
                                </span>
                            </div>
                        </td>
                        <td>
                            <span style="font-weight:600;{{ $product->stock <= 10 ? 'color:#fbbf24;' : 'color:var(--text);' }}">
                                {{ $product->stock }} {{ Str::plural($product->unit, $product->stock) }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('admin.products.toggle', $product) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" style="background:none;border:none;cursor:pointer;padding:0;" title="Click to toggle status">
                                    @if($product->is_active)
                                        <span class="badge badge--success"><i class="fa-solid fa-check"></i> Active</span>
                                    @else
                                        <span class="badge badge--danger"><i class="fa-solid fa-ban"></i> Disabled</span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td style="text-align:right;">
                            <div style="display:inline-flex;gap:.4rem;">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline btn-sm" title="Edit Full Specifications">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm" style="color:#f87171;border-color:rgba(239,68,68,.3);" title="Delete Product">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:1.25rem;">
            {{ $products->links() }}
        </div>
    @endif
</div>

<!-- Quick Price Edit Modal -->
<div id="quickPriceModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:999;backdrop-filter:blur(6px);place-items:center;padding:1.5rem;">
    <div class="card" style="max-width:440px;width:100%;background:#0b182d;border:1px solid rgba(139,92,246,.35);box-shadow:0 12px 40px rgba(0,0,0,.6);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
            <h3 style="font-size:1.15rem;font-weight:700;display:flex;align-items:center;gap:.5rem;">
                <i class="fa-solid fa-pen-to-square" style="color:var(--admin-light)"></i>
                Quick Update Price
            </h3>
            <button type="button" onclick="closePriceModal()" style="background:none;border:none;color:#94a3b8;font-size:1.2rem;cursor:pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div id="modalProductName" style="font-weight:600;color:var(--accent);margin-bottom:1.25rem;font-size:.95rem;"></div>

        <form id="quickPriceForm" method="POST" action="">
            @csrf
            @method('PATCH')

            <div class="form-group">
                <label class="form-label">Selling Price (₱ PHP)</label>
                <div style="position:relative;">
                    <span style="position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-weight:700;">₱</span>
                    <input type="number" step="0.50" min="0" name="price" id="modalPriceInput" class="form-input" required style="padding-left:2.2rem;font-size:1.1rem;font-weight:700;color:#38bdf8;">
                </div>
                <small style="color:var(--text-muted);font-size:.75rem;margin-top:.25rem;display:block;">
                    Customer orders and Staff walk-in POS will bill this new amount immediately.
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">Production / Acquisition Cost Price (₱ PHP)</label>
                <div style="position:relative;">
                    <span style="position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-weight:700;">₱</span>
                    <input type="number" step="0.50" min="0" name="cost_price" id="modalCostInput" class="form-input" style="padding-left:2.2rem;font-size:1rem;color:#e2e8f0;">
                </div>
                <small style="color:var(--text-muted);font-size:.75rem;margin-top:.25rem;display:block;">
                    Used in the Income & Profit Report to compute net margins accurately.
                </small>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:.75rem;margin-top:1.5rem;">
                <button type="button" class="btn btn-outline" onclick="closePriceModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-check"></i> Save Price Change
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openPriceModal(id, name, price, costPrice) {
        const modal = document.getElementById('quickPriceModal');
        const form = document.getElementById('quickPriceForm');
        const nameEl = document.getElementById('modalProductName');
        const priceInput = document.getElementById('modalPriceInput');
        const costInput = document.getElementById('modalCostInput');

        form.action = `/admin/products/${id}/price`;
        nameEl.textContent = name;
        priceInput.value = parseFloat(price).toFixed(2);
        costInput.value = parseFloat(costPrice).toFixed(2);

        modal.style.display = 'grid';
    }

    function closePriceModal() {
        document.getElementById('quickPriceModal').style.display = 'none';
    }

    window.addEventListener('click', function(e) {
        const modal = document.getElementById('quickPriceModal');
        if (e.target === modal) {
            closePriceModal();
        }
    });
</script>
@endpush
@endsection
