@extends('layouts.staff')

@section('title', 'Supplies & Stock – Aqua De Smiley')

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
    <div>
        <h1><i class="fa-solid fa-boxes-stacked" style="color:#38bdf8"></i> Supplies &amp; Stock</h1>
        <p>Track inventory levels and get low-stock alerts.</p>
    </div>
    <button onclick="document.getElementById('add-supply-modal').classList.add('open')" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Add Supply
    </button>
</div>

<!-- Low-stock alert banner -->
@if($lowStocks->count())
<div class="alert alert-warning" style="margin-bottom:1.25rem;">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <strong>{{ $lowStocks->count() }} item(s)</strong> are at or below minimum stock level!
</div>
@endif

<!-- Stats -->
<div class="card-grid" style="grid-template-columns:repeat(auto-fit,minmax(160px,1fr));margin-bottom:1.25rem;">
    <div class="stat-card">
        <div class="stat-icon si-blue"><i class="fa-solid fa-boxes-stacked"></i></div>
        <div><div class="stat-value">{{ $supplies->total() }}</div><div class="stat-label">Total Items</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-red"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div><div class="stat-value">{{ $lowStocks->count() }}</div><div class="stat-label">Low Stock</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-green"><i class="fa-solid fa-peso-sign"></i></div>
        <div>
            <div class="stat-value">₱{{ number_format(\App\Models\Supply::sum(\DB::raw('quantity * unit_price')),0) }}</div>
            <div class="stat-label">Stock Value</div>
        </div>
    </div>
</div>

<!-- Supplies Table -->
<div class="card">
    <div class="table-wrap">
        @if($supplies->isEmpty())
        <div style="text-align:center;padding:3rem;color:var(--text-muted);">
            <i class="fa-solid fa-box-open" style="font-size:2.5rem;margin-bottom:1rem;display:block;"></i>
            No supplies added yet.
        </div>
        @else
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Min. Stock</th>
                    <th>Unit Price</th>
                    <th>Value</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($supplies as $supply)
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $supply->name }}</div>
                        @if($supply->description)
                            <div style="font-size:.75rem;color:var(--text-muted);">{{ $supply->description }}</div>
                        @endif
                    </td>
                    <td>
                        <span style="background:rgba(139,92,246,.15);color:#a78bfa;padding:.2rem .65rem;border-radius:99px;font-size:.75rem;font-weight:600;">
                            {{ $supply->category }}
                        </span>
                    </td>
                    <td style="font-weight:700;">{{ $supply->quantity }} {{ $supply->unit }}</td>
                    <td style="color:var(--text-muted);">{{ $supply->minimum_stock }} {{ $supply->unit }}</td>
                    <td>₱{{ number_format($supply->unit_price,2) }}</td>
                    <td style="font-weight:600;color:#34d399;">₱{{ number_format($supply->quantity * $supply->unit_price,2) }}</td>
                    <td>
                        @if($supply->isLowStock())
                            <span class="status-badge" style="background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3);">
                                <i class="fa-solid fa-triangle-exclamation"></i> Low Stock
                            </span>
                        @else
                            <span class="status-badge" style="background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.3);">
                                <i class="fa-solid fa-circle-check"></i> OK
                            </span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('staff.supplies.restock', $supply) }}" method="POST" style="display:flex;gap:.4rem;align-items:center;">
                            @csrf @method('PATCH')
                            <input type="number" name="quantity" class="form-input" style="width:75px;padding:.3rem .55rem;font-size:.8rem;" min="0" value="{{ $supply->quantity }}" placeholder="Qty">
                            <button type="submit" class="btn btn-sm btn-primary" style="padding:.3rem .7rem;" title="Update stock">
                                <i class="fa-solid fa-floppy-disk"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    @if($supplies->hasPages())
    <div style="display:flex;justify-content:flex-end;margin-top:1rem;">
        {{ $supplies->links() }}
    </div>
    @endif
</div>

<!-- ── ADD SUPPLY MODAL ─────────────────────────────────────────── -->
<div id="add-supply-modal" class="modal-backdrop" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="modal-box">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
            <h2 style="font-size:1.1rem;font-weight:700;"><i class="fa-solid fa-plus" style="color:#38bdf8;margin-right:.4rem"></i>Add New Supply</h2>
            <button onclick="document.getElementById('add-supply-modal').classList.remove('open')" class="btn btn-ghost" style="font-size:1.1rem;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('staff.supplies.store') }}" method="POST" novalidate>
            @csrf

            <div class="form-row form-row-2">
                <div class="form-group">
                    <label class="form-label" for="s_name">Item Name</label>
                    <input id="s_name" type="text" name="name" class="form-input" placeholder="e.g. 5-Gallon Container" required>
                    @error('name')<div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="s_category">Category</label>
                    <select id="s_category" name="category" class="form-select">
                        <option>Container</option>
                        <option>Water</option>
                        <option>Chemical</option>
                        <option>Packaging</option>
                        <option>Equipment</option>
                        <option>General</option>
                    </select>
                </div>
            </div>

            <div class="form-row form-row-3">
                <div class="form-group">
                    <label class="form-label" for="s_qty">Quantity</label>
                    <input id="s_qty" type="number" name="quantity" class="form-input" min="0" value="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="s_min">Min. Stock</label>
                    <input id="s_min" type="number" name="minimum_stock" class="form-input" min="0" value="5" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="s_unit">Unit</label>
                    <select id="s_unit" name="unit" class="form-select">
                        <option>pcs</option>
                        <option>liters</option>
                        <option>kg</option>
                        <option>bottles</option>
                        <option>sets</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="s_price">Unit Price (₱)</label>
                    <input id="s_price" type="number" name="unit_price" class="form-input" min="0" step="0.01" value="0.00" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="s_desc">Description (optional)</label>
                    <textarea id="s_desc" name="description" class="form-textarea" placeholder="Brief description…"></textarea>
                </div>
            </div>

            <div style="display:flex;gap:.75rem;margin-top:.5rem;">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Supply</button>
                <button type="button" onclick="document.getElementById('add-supply-modal').classList.remove('open')" class="btn btn-outline">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
.modal-backdrop {
    display:none;position:fixed;inset:0;z-index:500;
    background:rgba(0,0,0,.6);backdrop-filter:blur(6px);
    align-items:center;justify-content:center;
}
.modal-backdrop.open { display:flex; }
.modal-box {
    background:#0b1a30;border:1px solid rgba(56,189,248,.2);
    border-radius:18px;padding:1.75rem;width:100%;max-width:560px;
    box-shadow:0 24px 64px rgba(0,0,0,.6);
    animation:modalIn .25s cubic-bezier(.16,1,.3,1) both;
    max-height:90vh;overflow-y:auto;
}
@keyframes modalIn {
    from{opacity:0;transform:scale(.94) translateY(12px);}
    to{opacity:1;transform:scale(1) translateY(0);}
}
</style>
@endpush
@endsection
