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
            if (Schema::hasColumn('pets', 'especie')) {
                $table->dropColumn('especie');
            }
            if (Schema::hasColumn('pets', 'raza')) {
                $table->dropColumn('raza');
            }
            if (!Schema::hasColumn('pets', 'species_id')) {
                $table->foreignId('species_id')->nullable()->constrained('species')->nullOnDelete();
            }
            if (!Schema::hasColumn('pets', 'raza_id')) {
                $table->foreignId('raza_id')->nullable()->constrained('breeds')->nullOnDelete();
            }
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
            $table->string('especie', 50)->nullable();
            $table->string('raza', 100)->nullable();
        });
    }
};
