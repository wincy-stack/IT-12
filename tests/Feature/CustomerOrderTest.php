<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        if (!User::where('role', 'customer')->exists()) {
            User::create([
                'name'     => 'Test Customer',
                'email'    => 'customer_test@example.com',
                'password' => bcrypt('password'),
                'role'     => 'customer',
            ]);
        }
    }

    public function test_customer_can_view_order_creation_page()
    {
        $customer = User::where('role', 'customer')->first();

        $response = $this->actingAs($customer)->get(route('customer.orders.create'));
        $response->assertStatus(200);
        $response->assertSee('500mL Bottle');
        $response->assertSee('5.00');
        $response->assertSee('1-Gallon Bottle');
        $response->assertSee('15.00');
        $response->assertSee('5-Gallon Container');
        $response->assertSee('30.00');
        $response->assertSee('Water Refill');
        $response->assertSee('New Container Purchase');
    }

    public function test_customer_can_order_500ml_bottle_at_5_pesos()
    {
        $customer = User::where('role', 'customer')->first();

        $response = $this->actingAs($customer)->post(route('customer.orders.store'), [
            'container_size'   => '500ml',
            'service_type'     => 'refill',
            'quantity'         => 4,
            'delivery_address' => 'Test Address 123',
            'notes'            => 'Leave at door',
        ]);

        $response->assertRedirect(route('customer.dashboard'));

        $this->assertDatabaseHas('orders', [
            'customer_id'    => $customer->id,
            'container_size' => '500ml',
            'service_type'   => 'refill',
            'quantity'       => 4,
            'unit_price'     => 5.00,
            'total_amount'   => 20.00, // 4 * 5.00
        ]);
    }

    public function test_customer_can_order_1_gallon_at_15_pesos()
    {
        $customer = User::where('role', 'customer')->first();

        $response = $this->actingAs($customer)->post(route('customer.orders.store'), [
            'container_size'   => '1_gallon',
            'service_type'     => 'new_container',
            'quantity'         => 2,
            'delivery_address' => 'Test Address 456',
        ]);

        $response->assertRedirect(route('customer.dashboard'));

        $this->assertDatabaseHas('orders', [
            'customer_id'    => $customer->id,
            'container_size' => '1_gallon',
            'service_type'   => 'new_container',
            'quantity'       => 2,
            'unit_price'     => 15.00,
            'total_amount'   => 30.00, // 2 * 15.00
        ]);
    }

    public function test_customer_can_order_5_gallon_at_30_pesos()
    {
        $customer = User::where('role', 'customer')->first();

        $response = $this->actingAs($customer)->post(route('customer.orders.store'), [
            'container_size'   => '5_gallon',
            'service_type'     => 'refill',
            'quantity'         => 3,
            'delivery_address' => 'Test Address 789',
        ]);

        $response->assertRedirect(route('customer.dashboard'));

        $this->assertDatabaseHas('orders', [
            'customer_id'    => $customer->id,
            'container_size' => '5_gallon',
            'service_type'   => 'refill',
            'quantity'       => 3,
            'unit_price'     => 30.00,
            'total_amount'   => 90.00, // 3 * 30.00
        ]);
    }

    public function test_customer_cannot_pick_invalid_container_size()
    {
        $customer = User::where('role', 'customer')->first();

        $response = $this->actingAs($customer)->post(route('customer.orders.store'), [
            'container_size'   => '10_gallon', // invalid
            'service_type'     => 'refill',
            'quantity'         => 1,
            'delivery_address' => 'Test Address',
        ]);

        $response->assertSessionHasErrors('container_size');
    }

    public function test_customer_cannot_pick_invalid_service_type()
    {
        $customer = User::where('role', 'customer')->first();

        $response = $this->actingAs($customer)->post(route('customer.orders.store'), [
            'container_size'   => '5_gallon',
            'service_type'     => 'rental', // invalid
            'quantity'         => 1,
            'delivery_address' => 'Test Address',
        ]);

        $response->assertSessionHasErrors('service_type');
    }

    public function test_staff_can_place_order_with_container_size_and_service_type()
    {
        $staff = User::where('role', 'staff')->first();
        $customer = User::where('role', 'customer')->first();

        $response = $this->actingAs($staff)->post(route('staff.orders.store'), [
            'customer_id'      => $customer->id,
            'container_size'   => '1_gallon',
            'service_type'     => 'new_container',
            'quantity'         => 3,
            'total_amount'     => 45.00,
            'status'           => 'confirmed',
            'delivery_address' => 'Staff Order Address',
        ]);

        $response->assertRedirect(route('staff.orders.index'));

        $this->assertDatabaseHas('orders', [
            'customer_id'    => $customer->id,
            'staff_id'       => $staff->id,
            'container_size' => '1_gallon',
            'service_type'   => 'new_container',
            'quantity'       => 3,
            'unit_price'     => 15.00,
            'total_amount'   => 45.00,
            'status'         => 'confirmed',
        ]);
    }

    public function test_customer_can_order_multiple_container_sizes_in_single_order()
    {
        $customer = User::where('role', 'customer')->first();

        // Ordering 2 of 500mL (@ 5.00 = 10.00) and 3 of 1-gallon (@ 15.00 = 45.00) -> Total 55.00
        $response = $this->actingAs($customer)->post(route('customer.orders.store'), [
            'items' => [
                [
                    'container_size' => '500ml',
                    'service_type'   => 'refill',
                    'quantity'       => 2,
                ],
                [
                    'container_size' => '1_gallon',
                    'service_type'   => 'new_container',
                    'quantity'       => 3,
                ],
                [
                    'container_size' => '5_gallon',
                    'service_type'   => 'refill',
                    'quantity'       => 0,
                ],
            ],
            'delivery_address' => '123 Multi-Size Street, Manila',
            'notes'            => 'Combo order delivery',
        ]);

        $response->assertRedirect(route('customer.dashboard'));

        $this->assertDatabaseHas('orders', [
            'customer_id'      => $customer->id,
            'total_amount'     => 55.00,
        ]);

        $order = Order::where('customer_id', $customer->id)->latest('id')->first();
        $this->assertCount(2, $order->items()->get());

        $this->assertDatabaseHas('order_items', [
            'order_id'       => $order->id,
            'container_size' => '500ml',
            'service_type'   => 'refill',
            'quantity'       => 2,
            'unit_price'     => 5.00,
            'subtotal'       => 10.00,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id'       => $order->id,
            'container_size' => '1_gallon',
            'service_type'   => 'new_container',
            'quantity'       => 3,
            'unit_price'     => 15.00,
            'subtotal'       => 45.00,
        ]);
    }

    public function test_customer_cannot_submit_zero_containers()
    {
        $customer = User::where('role', 'customer')->first();

        $response = $this->actingAs($customer)->post(route('customer.orders.store'), [
            'items' => [
                ['container_size' => '500ml',    'service_type' => 'refill', 'quantity' => 0],
                ['container_size' => '1_gallon', 'service_type' => 'refill', 'quantity' => 0],
                ['container_size' => '5_gallon', 'service_type' => 'refill', 'quantity' => 0],
            ],
            'delivery_address' => 'Test Address',
        ]);

        $response->assertSessionHasErrors('items');
    }

    public function test_staff_can_place_order_with_multiple_container_sizes()
    {
        $staff = User::where('role', 'staff')->first();
        $customer = User::where('role', 'customer')->first();

        // 4 of 500ml (@ 5 = 20) + 2 of 5_gallon (@ 30 = 60) -> Total 80
        $response = $this->actingAs($staff)->post(route('staff.orders.store'), [
            'customer_id' => $customer->id,
            'items' => [
                ['container_size' => '500ml',    'service_type' => 'refill',        'quantity' => 4],
                ['container_size' => '1_gallon', 'service_type' => 'refill',        'quantity' => 0],
                ['container_size' => '5_gallon', 'service_type' => 'new_container', 'quantity' => 2],
            ],
            'status'           => 'confirmed',
            'delivery_address' => 'Staff Multi Address',
        ]);

        $response->assertRedirect(route('staff.orders.index'));

        $this->assertDatabaseHas('orders', [
            'customer_id'  => $customer->id,
            'staff_id'     => $staff->id,
            'total_amount' => 80.00,
            'status'       => 'confirmed',
        ]);

        $order = Order::where('customer_id', $customer->id)->latest('id')->first();
        $this->assertCount(2, $order->items);
    }
}
