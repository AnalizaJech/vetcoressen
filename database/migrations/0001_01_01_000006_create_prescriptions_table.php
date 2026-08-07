<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Prescripciones médicas vinculadas a historias clínicas
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->foreignId('medical_record_id')->constrained('medical_records')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('medicamento', 150);
            $table->string('dosage', 100);
            $table->string('frequency', 100)->nullable();
            $table->string('duration', 100)->nullable();
            $table->string('via_administracion', 50)->default('ORAL');
            $table->integer('duracion_dias')->nullable();
            $table->text('indicaciones')->nullable();
            $table->integer('cantidad_dispensada')->default(0);
            $table->boolean('dispensado')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
