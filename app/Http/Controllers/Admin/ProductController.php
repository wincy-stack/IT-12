<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->get('search');
        $category = $request->get('category', 'all');
        $status   = $request->get('status', 'all');

        $query = Product::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('size_key', 'like', "%{$search}%");
            });
        }

        if ($category !== 'all' && !empty($category)) {
            $query->where('category', $category);
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $products = $query->orderBy('category')->orderBy('price')->paginate(12);

        // Aggregate statistics
        $stats = [
            'total'          => Product::count(),
            'active'         => Product::where('is_active', true)->count(),
            'avg_price'      => (float) Product::avg('price'),
            'refill_count'   => Product::where('category', 'Water Refill')->count(),
            'container_count'=> Product::where('category', 'New Container')->count(),
            'equip_count'    => Product::where('category', 'Accessories & Equipment')->count(),
        ];

        $categories = Product::select('category')->distinct()->pluck('category');

        return view('admin.products.index', compact('products', 'stats', 'categories', 'search', 'category', 'status'));
    }

    public function create()
    {
        $icons = [
            'fa-bottle-water'       => 'Bottle Water',
            'fa-jug-detergent'      => 'Gallon Jug',
            'fa-bucket'             => '5-Gallon Bucket / Container',
            'fa-droplet'            => 'Water Droplet',
            'fa-box-open'           => 'New Container Box',
            'fa-faucet'             => 'Dispenser Faucet / Tap',
            'fa-plug-circle-bolt'   => 'Electric Rechargeable Pump',
            'fa-filter'             => 'Filter',
            'fa-cubes'              => 'Ice / Cubes',
            'fa-hand-holding-droplet' => 'Pure Mineral Refill',
        ];

        return view('admin.products.create', compact('icons'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'size_key'    => ['nullable', 'string', 'max:50', 'unique:products,size_key'],
            'category'    => ['required', 'string', 'max:100'],
            'price'       => ['required', 'numeric', 'min:0'],
            'cost_price'  => ['required', 'numeric', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'unit'        => ['required', 'string', 'max:50'],
            'icon'        => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['icon'] = $validated['icon'] ?: 'fa-droplet';

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', "Product '{$validated['name']}' has been added successfully.");
    }

    public function edit(Product $product)
    {
        $icons = [
            'fa-bottle-water'       => 'Bottle Water',
            'fa-jug-detergent'      => 'Gallon Jug',
            'fa-bucket'             => '5-Gallon Bucket / Container',
            'fa-droplet'            => 'Water Droplet',
            'fa-box-open'           => 'New Container Box',
            'fa-faucet'             => 'Dispenser Faucet / Tap',
            'fa-plug-circle-bolt'   => 'Electric Rechargeable Pump',
            'fa-filter'             => 'Filter',
            'fa-cubes'              => 'Ice / Cubes',
            'fa-hand-holding-droplet' => 'Pure Mineral Refill',
        ];

        return view('admin.products.edit', compact('product', 'icons'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'size_key'    => ['nullable', 'string', 'max:50', Rule::unique('products', 'size_key')->ignore($product->id)],
            'category'    => ['required', 'string', 'max:100'],
            'price'       => ['required', 'numeric', 'min:0'],
            'cost_price'  => ['required', 'numeric', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'unit'        => ['required', 'string', 'max:50'],
            'icon'        => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['icon'] = $validated['icon'] ?: 'fa-droplet';

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', "Product '{$product->name}' updated successfully.");
    }

    public function updatePrice(Request $request, Product $product)
    {
        $validated = $request->validate([
            'price'      => ['required', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $updateData = ['price' => $validated['price']];
        if (isset($validated['cost_price'])) {
            $updateData['cost_price'] = $validated['cost_price'];
        }

        $product->update($updateData);

        return redirect()->back()
            ->with('success', "Price for '{$product->name}' updated to ₱" . number_format($product->price, 2) . ".");
    }

    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        $statusStr = $product->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Product '{$product->name}' has been {$statusStr}.");
    }

    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', "Product '{$name}' deleted successfully.");
    }
}
