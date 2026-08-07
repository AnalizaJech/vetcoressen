<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Productos y servicios del inventario
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->enum('type', ['PRODUCTO', 'SERVICIO'])->default('PRODUCTO');
            $table->string('categoria', 100)->nullable();
            $table->string('name', 150);
            $table->string('codigo_barras', 100)->nullable();
            $table->decimal('sale_price', 10, 2);
            $table->decimal('costo_compra', 10, 2)->nullable();
            $table->boolean('afecto_igv')->default(true);
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
