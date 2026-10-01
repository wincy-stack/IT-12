<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $activeOrders = Order::where('customer_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $completedOrders = Order::where('customer_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $totalSpent = Order::where('customer_id', $user->id)
            ->where('status', 'completed')
            ->sum('total_amount');

        $recentOrders = Order::with('items')
            ->where('customer_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('customer.dashboard', compact('user', 'activeOrders', 'completedOrders', 'totalSpent', 'recentOrders'));
    }

    public function ordersIndex(Request $request)
    {
        $user = Auth::user();
        $status = $request->get('status', 'all');

        $query = Order::with('items')->where('customer_id', $user->id)->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->paginate(10);

        $counts = Order::where('customer_id', $user->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('customer.orders.index', compact('user', 'orders', 'counts', 'status'));
    }

    public function createOrder()
    {
        $user = Auth::user();
        $containerSizes = \App\Models\Product::getContainerSizesArray();
        $serviceTypes = Order::SERVICE_TYPES;

        return view('customer.orders.create', compact('user', 'containerSizes', 'serviceTypes'));
    }

    public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'items'                     => ['nullable', 'array'],
            'items.*.container_size'    => ['required_with:items', 'in:500ml,1_gallon,5_gallon'],
            'items.*.service_type'      => ['required_with:items', 'in:refill,new_container'],
            'items.*.quantity'          => ['nullable', 'integer', 'min:0', 'max:500'],
            // Fallback for single item submission
            'container_size'            => ['nullable', 'in:500ml,1_gallon,5_gallon'],
            'service_type'              => ['nullable', 'in:refill,new_container'],
            'quantity'                  => ['nullable', 'integer', 'min:1', 'max:500'],
            'notes'                     => ['nullable', 'string', 'max:1000'],
        ]);

        $orderItems = [];

        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                $qty = (int) ($item['quantity'] ?? 0);
                if ($qty > 0) {
                    $size = $item['container_size'];
                    $price = Order::getPriceForSize($size);
                    $orderItems[] = [
                        'container_size' => $size,
                        'service_type'   => $item['service_type'] ?? 'refill',
                        'quantity'       => $qty,
                        'unit_price'     => $price,
                        'subtotal'       => $price * $qty,
                    ];
                }
            }
        } elseif (!empty($validated['container_size']) && !empty($validated['quantity'])) {
            $size = $validated['container_size'];
            $price = Order::getPriceForSize($size);
            $qty = (int) $validated['quantity'];
            $orderItems[] = [
                'container_size' => $size,
                'service_type'   => $validated['service_type'] ?? 'refill',
                'quantity'       => $qty,
                'unit_price'     => $price,
                'subtotal'       => $price * $qty,
            ];
        }

        if (empty($orderItems)) {
            return back()->withInput()->withErrors([
                'items' => 'Please choose a quantity of at least 1 for at least one container size.',
            ]);
        }

        $totalAmount = array_sum(array_column($orderItems, 'subtotal'));
        $totalQuantity = array_sum(array_column($orderItems, 'quantity'));
        $totalGallons = 0;
        foreach ($orderItems as $item) {
            $totalGallons += match ($item['container_size']) {
                '5_gallon' => $item['quantity'] * 5,
                '1_gallon' => $item['quantity'] * 1,
                '500ml'    => max(1, (int) round($item['quantity'] * 0.132)),
            };
        }

        // Generate unique order number
        $orderNumber = Order::generateOrderNumber();

        $firstItem = $orderItems[0];

        $order = DB::transaction(function () use ($validated, $orderNumber, $orderItems, $firstItem, $totalQuantity, $totalGallons, $totalAmount) {
            $order = Order::create([
                'customer_id'  => Auth::id(),
                'order_number' => $orderNumber,
                'status'       => 'pending',
                'container_size' => $firstItem['container_size'],
                'service_type'   => $firstItem['service_type'],
                'quantity'       => $totalQuantity,
                'unit_price'     => $firstItem['unit_price'],
                'gallons'        => $totalGallons,
                'total_amount'   => $totalAmount,
                'notes'          => $validated['notes'] ?? null,
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            return $order;
        });

        return redirect()->route('customer.dashboard')
            ->with('success', 'Order ' . $order->order_number . ' placed successfully! (' . $order->itemsSummary() . ')');
    }

    public function cancelOrder(Order $order)
    {
        if ($order->customer_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be cancelled.');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Order ' . $order->order_number . ' has been cancelled.');
    }
}
