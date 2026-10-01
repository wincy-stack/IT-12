@extends('layouts.admin')

@section('title', 'Edit Product – ' . $product->name)

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1>
            <i class="fa-solid fa-pen-to-square" style="color:var(--admin-light)"></i>
            Edit Product: {{ $product->name }}
        </h1>
        <p>Update pricing, unit costs, inventory stock level, and catalog presentation.</p>
    </div>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline">
        <i class="fa-solid fa-arrow-left"></i> Back to Products
    </a>
</div>

<div class="card" style="max-width:850px;">
    <form action="{{ route('admin.products.update', $product) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:1.25rem;">
            <!-- Product Name -->
            <div class="form-group">
                <label class="form-label" for="name">Product Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" class="form-input" required>
                @error('name')<div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>@enderror
            </div>

            <!-- Category -->
            <div class="form-group">
                <label class="form-label" for="category">Category *</label>
                <select id="category" name="category" class="form-select" required>
                    <option value="Water Refill" {{ old('category', $product->category) === 'Water Refill' ? 'selected' : '' }}>Water Refill</option>
                    <option value="New Container" {{ old('category', $product->category) === 'New Container' ? 'selected' : '' }}>New Container Purchase</option>
                    <option value="Accessories & Equipment" {{ old('category', $product->category) === 'Accessories & Equipment' ? 'selected' : '' }}>Accessories & Equipment</option>
                    <option value="Supplies & Seals" {{ old('category', $product->category) === 'Supplies & Seals' ? 'selected' : '' }}>Supplies & Seals</option>
                </select>
                @error('category')<div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>@enderror
            </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:1.25rem;">
            <!-- Selling Price -->
            <div class="form-group">
                <label class="form-label" for="price">Selling Price (₱ PHP) *</label>
                <input type="number" step="0.50" min="0" id="price" name="price" value="{{ old('price', $product->price) }}" class="form-input" required style="font-weight:700;color:#38bdf8;">
                <small style="color:var(--text-muted);font-size:.72rem;">Customer rate charged at checkout.</small>
                @error('price')<div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>@enderror
            </div>

            <!-- Cost Price -->
            <div class="form-group">
                <label class="form-label" for="cost_price">Unit Production / Cost (₱ PHP) *</label>
                <input type="number" step="0.50" min="0" id="cost_price" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" class="form-input" required>
                <small style="color:var(--text-muted);font-size:.72rem;">Used in Income Report net profit computation.</small>
                @error('cost_price')<div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>@enderror
            </div>

            <!-- Size Key -->
            <div class="form-group">
                <label class="form-label" for="size_key">
                    Size Identifier Key
                    <span style="font-size:.72rem;color:var(--text-muted);font-weight:normal;">(optional)</span>
                </label>
                <input type="text" id="size_key" name="size_key" value="{{ old('size_key', $product->size_key) }}" class="form-input" placeholder="e.g. 5_gallon, 1_gallon, 500ml">
                @error('size_key')<div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>@enderror
            </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:1.25rem;">
            <!-- Stock -->
            <div class="form-group">
                <label class="form-label" for="stock">Current Stock Level *</label>
                <input type="number" min="0" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" class="form-input" required>
                @error('stock')<div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>@enderror
            </div>

            <!-- Unit -->
            <div class="form-group">
                <label class="form-label" for="unit">Unit of Measure *</label>
                <select id="unit" name="unit" class="form-select" required>
                    <option value="bottle" {{ old('unit', $product->unit) === 'bottle' ? 'selected' : '' }}>Bottle</option>
                    <option value="container" {{ old('unit', $product->unit) === 'container' ? 'selected' : '' }}>Container</option>
                    <option value="pc" {{ old('unit', $product->unit) === 'pc' ? 'selected' : '' }}>Piece (pc)</option>
                    <option value="pack" {{ old('unit', $product->unit) === 'pack' ? 'selected' : '' }}>Pack</option>
                </select>
                @error('unit')<div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>@enderror
            </div>

            <!-- Icon -->
            <div class="form-group">
                <label class="form-label" for="icon">Display Icon</label>
                <select id="icon" name="icon" class="form-select">
                    @foreach($icons as $iconClass => $iconName)
                        <option value="{{ $iconClass }}" {{ old('icon', $product->icon) === $iconClass ? 'selected' : '' }}>
                            {{ $iconName }} ({{ $iconClass }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Description -->
        <div class="form-group">
            <label class="form-label" for="description">Description & Customer Notes</label>
            <textarea id="description" name="description" rows="3" class="form-textarea">{{ old('description', $product->description) }}</textarea>
            @error('description')<div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>@enderror
        </div>

        <!-- Active Status -->
        <div class="form-group" style="margin-top:.5rem;">
            <label style="display:flex;align-items:center;gap:.6rem;cursor:pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--admin-purple);">
                <span style="font-weight:600;font-size:.9rem;">Active and available for customer orders & walk-in POS</span>
            </label>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:1.75rem;padding-top:1.25rem;border-top:1px solid var(--glass-border);">
            <div style="font-size:.8rem;color:var(--text-muted)">
                Created on {{ $product->created_at->format('M d, Y') }} • Last updated {{ $product->updated_at->diffForHumans() }}
            </div>
            <div style="display:flex;gap:.75rem;">
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-check"></i> Update Product
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
