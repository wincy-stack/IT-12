<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboard;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboard;

// ------------------------------------------------------------------
// Root – redirect to login
// ------------------------------------------------------------------
Route::get('/', fn () => redirect()->route('login'));

// ------------------------------------------------------------------
// Authentication Routes (guests only)
// ------------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ------------------------------------------------------------------
// Admin Routes
// ------------------------------------------------------------------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Products & Prices Management
    Route::get('/products',                     [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create',              [ProductController::class, 'create'])->name('products.create');
    Route::post('/products',                    [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit',       [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}',           [ProductController::class, 'update'])->name('products.update');
    Route::patch('/products/{product}/price',   [ProductController::class, 'updatePrice'])->name('products.price');
    Route::patch('/products/{product}/toggle',  [ProductController::class, 'toggleStatus'])->name('products.toggle');
    Route::delete('/products/{product}',        [ProductController::class, 'destroy'])->name('products.destroy');

    // Sales & Income Reports
    Route::get('/reports/sales',                [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/income',               [ReportController::class, 'income'])->name('reports.income');
});

// ------------------------------------------------------------------
// Staff Routes
// ------------------------------------------------------------------
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [StaffDashboard::class, 'index'])->name('dashboard');

    // Orders
    Route::get('/orders',           [StaffDashboard::class, 'ordersIndex'])->name('orders.index');
    Route::get('/orders/create',    [StaffDashboard::class, 'ordersCreate'])->name('orders.create');
    Route::post('/orders',          [StaffDashboard::class, 'ordersStore'])->name('orders.store');
    Route::patch('/orders/{order}', [StaffDashboard::class, 'ordersUpdate'])->name('orders.update');

    // Customers
    Route::get('/customers',         [StaffDashboard::class, 'customersIndex'])->name('customers.index');
    Route::get('/customers/create',  [StaffDashboard::class, 'customersCreate'])->name('customers.create');
    Route::post('/customers',        [StaffDashboard::class, 'customersStore'])->name('customers.store');

    // Supplies & Stock
    Route::get('/supplies',                     [StaffDashboard::class, 'suppliesIndex'])->name('supplies.index');
    Route::post('/supplies',                    [StaffDashboard::class, 'suppliesStore'])->name('supplies.store');
    Route::patch('/supplies/{supply}/restock',  [StaffDashboard::class, 'suppliesRestock'])->name('supplies.restock');

    // Payment Process & Walk-in POS
    Route::get('/payments',                  [StaffDashboard::class, 'paymentsIndex'])->name('payments.index');
    Route::post('/payments/{order}/pay',     [StaffDashboard::class, 'markPaymentReceived'])->name('payments.pay');
    Route::post('/payments/{order}/release', [StaffDashboard::class, 'releaseOrder'])->name('payments.release');
});

// ------------------------------------------------------------------
// Customer Routes
// ------------------------------------------------------------------
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard',              [CustomerDashboard::class, 'index'])->name('dashboard');
    Route::get('/orders',                 [CustomerDashboard::class, 'ordersIndex'])->name('orders.index');
    Route::get('/orders/create',          [CustomerDashboard::class, 'createOrder'])->name('orders.create');
    Route::post('/orders',                [CustomerDashboard::class, 'storeOrder'])->name('orders.store');
    Route::patch('/orders/{order}/cancel', [CustomerDashboard::class, 'cancelOrder'])->name('orders.cancel');
});
