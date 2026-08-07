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
            $table->string('principio_activo', 150)->nullable()->after('categoria');
            $table->string('presentacion', 50)->nullable()->after('principio_activo');
            $table->string('weight', 50)->nullable()->after('presentacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['principio_activo', 'presentacion', 'weight']);
        });
    }
};
