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
            $table->decimal('current_stock', 10, 2)->default(0)->change();
            $table->decimal('minimum_stock', 10, 2)->default(0)->change();
            $table->enum('classification', ['A', 'B', 'C'])->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('current_stock')->default(0)->change();
            $table->integer('minimum_stock')->default(0)->change();
            $table->dropColumn('classification');
        });
    }
};
