<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Mascotas vinculadas a clientes
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('especie', 50);
            $table->string('raza', 100)->nullable();
            $table->enum('gender', ['M', 'H'])->comment('M = Macho, H = Hembra');
            $table->string('color', 50)->nullable();
            $table->date('birth_date')->nullable();
            $table->decimal('current_weight', 8, 2)->nullable()->comment('Peso en kg');
            $table->string('foto')->nullable();
            $table->boolean('esterilizado')->default(false);
            $table->boolean('fallecido')->default(false);
            $table->text('medical_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
