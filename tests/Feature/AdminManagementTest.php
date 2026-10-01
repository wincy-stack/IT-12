<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Admin Manager', 'password' => bcrypt('admin123'), 'role' => 'admin']
        );

        $this->staff = User::firstOrCreate(
            ['email' => 'staff@gmail.com'],
            ['name' => 'Staff Member', 'password' => bcrypt('staff123'), 'role' => 'staff']
        );

        $this->customer = User::firstOrCreate(
            ['email' => 'customer_test@example.com'],
            ['name' => 'Test Customer', 'password' => bcrypt('password'), 'role' => 'customer']
        );
    }

    public function test_admin_can_view_dashboard()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSeeText('Admin Executive Dashboard');
        $response->assertSeeText('Manage Products');
        $response->assertSeeText('View Sales Report');
        $response->assertSeeText('View Income Report');
    }

    public function test_admin_can_view_products_and_prices()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.products.index'));
        $response->assertStatus(200);
        $response->assertSeeText('Products');
        $response->assertSeeText('Prices Management');
        $response->assertSeeText('5-Gallon Container');
        $response->assertSeeText('500mL Bottle');
    }

    public function test_admin_can_add_new_product()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name'        => 'Alkaline Mineral 5-Gal Refill',
            'category'    => 'Water Refill',
            'price'       => 45.00,
            'cost_price'  => 12.00,
            'stock'       => 150,
            'unit'        => 'container',
            'icon'        => 'fa-bucket',
            'description' => 'Special pH 8.5+ enhanced mineral water refill',
            'is_active'   => 1,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'name'  => 'Alkaline Mineral 5-Gal Refill',
            'price' => 45.00,
            'cost_price' => 12.00,
        ]);
    }

    public function test_admin_can_update_product_price()
    {
        $product = Product::where('size_key', '5_gallon')->first();
        $this->assertNotNull($product);

        $response = $this->actingAs($this->admin)->patch(route('admin.products.price', $product), [
            'price'      => 35.00,
            'cost_price' => 9.50,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'id'    => $product->id,
            'price' => 35.00,
            'cost_price' => 9.50,
        ]);

        // Verify Order model dynamically picks up the new price
        $this->assertEquals(35.00, Order::getPriceForSize('5_gallon'));
    }

    public function test_admin_can_toggle_product_status()
    {
        $product = Product::first();
        $initialStatus = $product->is_active;

        $response = $this->actingAs($this->admin)->patch(route('admin.products.toggle', $product));
        $response->assertRedirect();

        $this->assertDatabaseHas('products', [
            'id'        => $product->id,
            'is_active' => !$initialStatus,
        ]);
    }

    public function test_admin_can_view_sales_report_with_order_data()
    {
        $order = Order::create([
            'order_number'   => 'ORD-9901',
            'customer_id'    => $this->customer->id,
            'status'         => 'completed',
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'total_amount'   => 60.00,
        ]);

        OrderItem::create([
            'order_id'       => $order->id,
            'container_size' => '5_gallon',
            'service_type'   => 'refill',
            'quantity'       => 2,
            'unit_price'     => 30.00,
            'subtotal'       => 60.00,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.sales'));
        $response->assertStatus(200);
        $response->assertSeeText('Sales Revenue');
        $response->assertSeeText('Order Report');
        $response->assertSeeText('ORD-9901');
        $response->assertSeeText('60.00');
    }

    public function test_admin_can_view_income_report_with_profit_and_margins()
    {
        $order = Order::create([
            'order_number'   => 'ORD-9902',
            'customer_id'    => $this->customer->id,
            'status'         => 'completed',
            'payment_status' => 'paid',
            'payment_method' => 'gcash',
            'total_amount'   => 100.00,
        ]);

        OrderItem::create([
            'order_id'       => $order->id,
            'container_size' => '5_gallon',
            'service_type'   => 'refill',
            'quantity'       => 2,
            'unit_price'     => 30.00,
            'subtotal'       => 60.00,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.income'));
        $response->assertStatus(200);
        $response->assertSeeText('Income');
        $response->assertSeeText('Profitability Report');
        $response->assertSeeText('Gross Operating Revenue');
    }

    public function test_customer_and_staff_cannot_access_admin_management()
    {
        // Customer redirected with error
        $responseCustomer = $this->actingAs($this->customer)->get(route('admin.products.index'));
        $responseCustomer->assertRedirect(route('customer.dashboard'));
        $responseCustomer->assertSessionHas('error');

        // Staff redirected with error
        $responseStaff = $this->actingAs($this->staff)->get(route('admin.products.index'));
        $responseStaff->assertRedirect(route('staff.dashboard'));
        $responseStaff->assertSessionHas('error');

        // Customer cannot access reports
        $responseRep = $this->actingAs($this->customer)->get(route('admin.reports.sales'));
        $responseRep->assertRedirect(route('customer.dashboard'));
    }
}
