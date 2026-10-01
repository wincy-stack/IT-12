<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->default('General'); // e.g. Container, Chemical, Equipment
            $table->integer('quantity')->default(0);
            $table->integer('minimum_stock')->default(5);
            $table->decimal('unit_price', 8, 2)->default(0);
            $table->string('unit')->default('pcs'); // pcs, liters, kg, etc.
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplies');
    }
};
