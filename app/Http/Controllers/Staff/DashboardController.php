<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Supply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // ─── Dashboard ─────────────────────────────────────────────────────
    public function index()
    {
        $stats = [
            'pending'   => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'completed' => Order::where('status', 'completed')->whereDate('updated_at', today())->count(),
            'customers' => User::where('role', 'customer')->count(),
            'low_stock' => Supply::whereColumn('quantity', '<=', 'minimum_stock')->count(),
        ];

        $recentOrders = Order::with('customer')
            ->latest()
            ->take(5)
            ->get();

        $lowStocks = Supply::whereColumn('quantity', '<=', 'minimum_stock')->get();

        return view('staff.dashboard', compact('stats', 'recentOrders', 'lowStocks'));
    }

    // ─── Orders ────────────────────────────────────────────────────────
    public function ordersIndex(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = Order::with('customer', 'staff', 'items')->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->paginate(12);

        $counts = Order::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('staff.orders.index', compact('orders', 'counts', 'status'));
    }

    public function ordersCreate(Request $request)
    {
        $customers      = User::where('role', 'customer')->orderBy('name')->get();
        $containerSizes = \App\Models\Product::getContainerSizesArray();
        $serviceTypes   = Order::SERVICE_TYPES;
        $preselected    = $request->get('customer_id');

        return view('staff.orders.create', compact('customers', 'containerSizes', 'serviceTypes', 'preselected'));
    }

    public function ordersStore(Request $request)
    {
        $validated = $request->validate([
            'customer_id'               => ['required', 'exists:users,id'],
            'items'                     => ['nullable', 'array'],
            'items.*.container_size'    => ['required_with:items', 'in:500ml,1_gallon,5_gallon'],
            'items.*.service_type'      => ['required_with:items', 'in:refill,new_container'],
            'items.*.quantity'          => ['nullable', 'integer', 'min:0', 'max:500'],
            // Backwards compatibility for single item submission
            'container_size'            => ['nullable', 'in:500ml,1_gallon,5_gallon'],
            'service_type'              => ['nullable', 'in:refill,new_container'],
            'quantity'                  => ['nullable', 'integer', 'min:1'],
            'total_amount'              => ['nullable', 'numeric', 'min:0'],
            'status'                    => ['required', 'in:pending,confirmed'],
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
                'items' => 'Please specify a quantity of at least 1 for at least one container size.',
            ]);
        }

        $calcTotalAmount = array_sum(array_column($orderItems, 'subtotal'));
        $totalAmount = (!empty($validated['total_amount']) && is_numeric($validated['total_amount'])) ? (float) $validated['total_amount'] : $calcTotalAmount;
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

        $order = Order::create([
            'customer_id'    => $validated['customer_id'],
            'staff_id'       => Auth::id(),
            'order_number'   => $orderNumber,
            'status'         => $validated['status'],
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

        return redirect()->route('staff.orders.index')
            ->with('success', 'Order ' . $orderNumber . ' placed successfully! (' . $order->itemsSummary() . ')');
    }

    public function ordersUpdate(Request $request, Order $order)
    {
        if ($request->input('action') === 'pay') {
            $validated = $request->validate([
                'payment_method'  => ['required', 'in:cash,gcash'],
                'amount_tendered' => ['nullable', 'numeric', 'min:0'],
            ]);

            $order->update([
                'payment_status' => 'paid',
                'payment_method' => $validated['payment_method'],
                'paid_at'        => now(),
            ]);

            return back()->with('success', 'Payment of ₱' . number_format($order->total_amount, 2) . ' received for Order ' . $order->order_number . '.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,completed,cancelled'],
        ]);

        $order->update($validated);

        return back()->with('success', 'Order ' . $order->order_number . ' updated to "' . $order->statusLabel() . '".');
    }

    // ─── Customers ─────────────────────────────────────────────────────
    public function customersIndex(Request $request)
    {
        $query = User::where('role', 'customer')
            ->withCount('orders')
            ->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers      = $query->paginate(12);
        $totalCustomers = User::where('role', 'customer')->count();
        $todayCustomers = User::where('role', 'customer')->whereDate('created_at', today())->count();

        return view('staff.customers.index', compact('customers', 'totalCustomers', 'todayCustomers'));
    }

    public function customersCreate()
    {
        return view('staff.customers.create');
    }

    public function customersStore(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password'],
            'role'     => 'customer',
        ]);

        return redirect()->route('staff.customers.index')
            ->with('success', 'Customer account for "' . $validated['name'] . '" created successfully!');
    }

    // ─── Supplies ──────────────────────────────────────────────────────
    public function suppliesIndex()
    {
        $supplies  = Supply::orderBy('name')->paginate(15);
        $lowStocks = Supply::whereColumn('quantity', '<=', 'minimum_stock')->get();

        return view('staff.supplies.index', compact('supplies', 'lowStocks'));
    }

    public function suppliesStore(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'category'      => ['required', 'string', 'max:100'],
            'quantity'      => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'unit_price'    => ['required', 'numeric', 'min:0'],
            'unit'          => ['required', 'string', 'max:50'],
            'description'   => ['nullable', 'string', 'max:500'],
        ]);

        Supply::create($validated);

        return redirect()->route('staff.supplies.index')
            ->with('success', '"' . $validated['name'] . '" added to inventory.');
    }

    public function suppliesRestock(Request $request, Supply $supply)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $supply->update(['quantity' => $validated['quantity']]);

        return back()->with('success', '"' . $supply->name . '" stock updated to ' . $validated['quantity'] . ' ' . $supply->unit . '.');
    }

    public function paymentsIndex(Request $request)
    {
        $tab = $request->get('tab', 'awaiting'); // 'awaiting' or 'completed'
        $search = trim($request->get('q', ''));

        $selectedId = $request->get('order_id');

        // If specific order is requested and no explicit tab is set, navigate to the right tab
        if ($selectedId && !$request->has('tab')) {
            $checkOrder = Order::find($selectedId);
            if ($checkOrder && $checkOrder->isPaid() && $checkOrder->isReleased()) {
                $tab = 'completed';
            }
        }

        $query = Order::with(['customer', 'items', 'staff']);

        if ($tab === 'completed') {
            $query->where(function ($q) {
                $q->where('status', 'completed')
                  ->orWhereNotNull('released_at');
            });
        } else {
            // Awaiting payment or awaiting release
            $query->where('status', '!=', 'cancelled')
                  ->where(function ($q) {
                      $q->where('payment_status', 'unpaid')
                        ->orWhere('status', '!=', 'completed')
                        ->orWhereNull('released_at');
                  });
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest()->get();

        // If specific order was requested by ID but wasn't in current query, prepend it
        if ($selectedId && !$orders->contains('id', (int) $selectedId)) {
            $specific = Order::with(['customer', 'items', 'staff'])->find($selectedId);
            if ($specific) {
                $orders->prepend($specific);
            }
        }

        $selectedOrder = $orders->firstWhere('id', (int) $selectedId) ?? $orders->first();

        $counts = [
            'awaiting'  => Order::where('status', '!=', 'cancelled')
                ->where(fn($q) => $q->where('payment_status', 'unpaid')->orWhere('status', '!=', 'completed')->orWhereNull('released_at'))
                ->count(),
            'completed' => Order::where('status', 'completed')->orWhereNotNull('released_at')->count(),
        ];

        return view('staff.payments.index', compact('orders', 'selectedOrder', 'tab', 'search', 'counts'));
    }

    public function markPaymentReceived(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_method' => ['nullable', 'in:cash,gcash,paypal'],
        ]);

        $method = $validated['payment_method'] ?? 'cash';

        $order->update([
            'payment_status' => 'paid',
            'payment_method' => $method,
            'paid_at'        => now(),
            'staff_id'       => Auth::id() ?? $order->staff_id,
            'status'         => ($order->status === 'pending') ? 'confirmed' : $order->status,
        ]);

        $methodLabel = match ($method) {
            'gcash'  => 'GCash',
            'paypal' => 'PayPal',
            default  => 'Cash',
        };

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Payment of ₱" . number_format($order->total_amount, 2) . " received via {$methodLabel}.",
                'order'   => $order->fresh()->load('customer', 'items'),
            ]);
        }

        return redirect()->route('staff.payments.index', ['order_id' => $order->id])
            ->with('success', "Payment for Order {$order->order_number} marked as received ({$methodLabel}).");
    }

    public function releaseOrder(Request $request, Order $order)
    {
        $order->update([
            'status'         => 'completed',
            'payment_status' => 'paid',
            'paid_at'        => $order->paid_at ?? now(),
            'released_at'    => now(),
            'staff_id'       => Auth::id() ?? $order->staff_id,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Order {$order->order_number} has been released to {$order->customer?->name}!",
                'order'   => $order->fresh()->load('customer', 'items'),
            ]);
        }

        return redirect()->route('staff.payments.index')
            ->with('success', "Order {$order->order_number} has been released to {$order->customer?->name}!");
    }
}
