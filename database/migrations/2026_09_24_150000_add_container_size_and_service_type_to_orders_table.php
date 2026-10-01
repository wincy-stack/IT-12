<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('container_size')->default('5_gallon')->after('status'); // 500ml, 1_gallon, 5_gallon
            $table->string('service_type')->default('refill')->after('container_size'); // refill, new_container
            $table->integer('quantity')->default(1)->after('service_type');
            $table->decimal('unit_price', 8, 2)->default(30.00)->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['container_size', 'service_type', 'quantity', 'unit_price']);
        });
    }
};
