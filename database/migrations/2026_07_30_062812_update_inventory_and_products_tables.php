<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('costo_compra');
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->foreignId('product_batch_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
            $table->dropColumn(['lote', 'fecha_vencimiento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->string('lote', 50)->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->dropForeign(['product_batch_id']);
            $table->dropColumn('product_batch_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('costo_compra', 10, 2)->nullable();
        });
    }
};
