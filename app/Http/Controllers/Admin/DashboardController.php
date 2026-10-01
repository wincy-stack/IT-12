<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Supply;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStaff     = User::where('role', 'staff')->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalAdmins    = User::where('role', 'admin')->count();

        $totalProducts  = Product::count();
        $activeProducts = Product::where('is_active', true)->count();

        $totalRevenue   = (float) Order::where('payment_status', 'paid')->sum('total_amount');
        $todayRevenue   = (float) Order::where('payment_status', 'paid')->whereDate('created_at', today())->sum('total_amount');
        $totalOrders    = Order::count();
        $pendingOrders  = Order::where('status', 'pending')->count();
        $lowSupplies    = Supply::whereColumn('quantity', '<=', 'minimum_stock')->count();

        $recentOrders   = Order::with(['customer', 'items'])->latest()->take(6)->get();
        $products       = Product::where('is_active', true)->take(5)->get();

        return view('admin.dashboard', compact(
            'totalStaff',
            'totalCustomers',
            'totalAdmins',
            'totalProducts',
            'activeProducts',
            'totalRevenue',
            'todayRevenue',
            'totalOrders',
            'pendingOrders',
            'lowSupplies',
            'recentOrders',
            'products'
        ));
    }
}
