<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Campos de examen físico por sistemas para la historia clínica
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            // Examen físico general
            $table->string('examen_mucosas', 50)->nullable()->after('respiratory_rate');
            $table->string('examen_linfonodos', 50)->nullable()->after('examen_mucosas');
            $table->tinyInteger('condicion_corporal')->nullable()->after('examen_linfonodos'); // BCS 1-9
            $table->tinyInteger('nivel_dolor')->nullable()->after('condicion_corporal');       // 0-10
            $table->string('nivel_hidratacion', 30)->nullable()->after('nivel_dolor');

            // Examen por sistemas (todos opcionales)
            $table->text('examen_piel_pelaje')->nullable()->after('nivel_hidratacion');
            $table->text('examen_ojos_oidos')->nullable()->after('examen_piel_pelaje');
            $table->text('examen_cardiovascular')->nullable()->after('examen_ojos_oidos');
            $table->text('examen_respiratorio')->nullable()->after('examen_cardiovascular');
            $table->text('examen_digestivo')->nullable()->after('examen_respiratorio');
            $table->text('examen_musculoesqueletico')->nullable()->after('examen_digestivo');
            $table->text('examen_neurologico')->nullable()->after('examen_musculoesqueletico');
            $table->text('examen_urinario')->nullable()->after('examen_neurologico');
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn([
                'examen_mucosas', 'examen_linfonodos', 'condicion_corporal',
                'nivel_dolor', 'nivel_hidratacion',
                'examen_piel_pelaje', 'examen_ojos_oidos', 'examen_cardiovascular',
                'examen_respiratorio', 'examen_digestivo', 'examen_musculoesqueletico',
                'examen_neurologico', 'examen_urinario',
            ]);
        });
    }
};
