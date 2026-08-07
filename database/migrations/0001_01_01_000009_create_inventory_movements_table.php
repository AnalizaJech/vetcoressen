<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Kardex inmutable - registro de todo movimiento de inventario
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', [
                'ENTRADA_COMPRA', 'ENTRADA_AJUSTE',
                'SALIDA_VENTA', 'SALIDA_DISPENSACION', 'SALIDA_AJUSTE',
            ]);
            $table->integer('quantity')->comment('Positivo entradas, Negativo salidas');
            $table->decimal('costo_unitario', 10, 2)->nullable();
            $table->string('lote', 50)->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->string('reference_document', 50)->nullable();
            $table->integer('stock_anterior');
            $table->integer('stock_posterior');
            $table->string('referencia_tipo', 50)->nullable();
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
