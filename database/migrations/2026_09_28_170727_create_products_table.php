<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('size_key')->nullable()->unique(); // '500ml', '1_gallon', '5_gallon', etc.
            $table->string('category')->default('Water Refill'); // 'Water Refill', 'New Container', 'Accessories & Equipment'
            $table->decimal('price', 8, 2)->default(0.00); // Selling price
            $table->decimal('cost_price', 8, 2)->default(0.00); // Production/Purchase cost per unit
            $table->integer('stock')->default(100);
            $table->string('unit')->default('bottle'); // bottle, container, pc, set
            $table->string('icon')->default('fa-droplet');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
