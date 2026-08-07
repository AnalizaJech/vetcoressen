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
        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn(['especie', 'raza']);
            $table->foreignId('species_id')->nullable()->after('name')->constrained('species');
            $table->foreignId('raza_id')->nullable()->after('species_id')->constrained('breeds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropForeign(['species_id']);
            $table->dropForeign(['raza_id']);
            $table->dropColumn(['species_id', 'raza_id']);
            $table->string('especie')->nullable()->after('name');
            $table->string('raza')->nullable()->after('especie');
        });
    }
};
