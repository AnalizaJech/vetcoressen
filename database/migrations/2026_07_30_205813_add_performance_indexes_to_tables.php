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
        Schema::table('appointments', function (Blueprint $table) {
            $table->index(['fecha_hora', 'status']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->index(['created_at', 'status']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(['fecha_hora', 'status']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['created_at', 'status']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'type']);
        });
    }
};
