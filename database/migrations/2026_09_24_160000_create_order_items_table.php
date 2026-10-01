<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('container_size'); // 500ml, 1_gallon, 5_gallon
            $table->string('service_type')->default('refill'); // refill, new_container
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 8, 2)->default(0.00);
            $table->decimal('subtotal', 8, 2)->default(0.00);
            $table->timestamps();
        });

        // Migrate existing order rows to order_items
        $existingOrders = DB::table('orders')->get();
        foreach ($existingOrders as $o) {
            $containerSize = $o->container_size ?? '5_gallon';
            $serviceType = $o->service_type ?? 'refill';
            $qty = $o->quantity ?? ($o->gallons ?? 1);
            $unitPrice = $o->unit_price ?? 30.00;
            $subtotal = $o->total_amount ?? ($qty * $unitPrice);

            DB::table('order_items')->insert([
                'order_id'       => $o->id,
                'container_size' => $containerSize,
                'service_type'   => $serviceType,
                'quantity'       => $qty,
                'unit_price'     => $unitPrice,
                'subtotal'       => $subtotal,
                'created_at'     => $o->created_at ?? now(),
                'updated_at'     => $o->updated_at ?? now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
