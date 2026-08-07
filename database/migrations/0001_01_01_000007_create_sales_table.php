<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Ventas (facturación)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('cajero_id')->constrained('users')->cascadeOnDelete();
            $table->enum('tipo_comprobante', ['TICKET', 'BOLETA', 'FACTURA'])->default('TICKET');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('igv', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('payment_method', ['EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'YAPE_PLIN'])->default('EFECTIVO');
            $table->enum('status', ['PAGADO', 'ANULADO'])->default('PAGADO');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
