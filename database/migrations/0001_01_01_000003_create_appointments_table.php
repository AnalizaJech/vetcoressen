<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Citas veterinarias con estados y flags de notificación
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('pet_id')->constrained('pets')->cascadeOnDelete();
            $table->foreignId('veterinarian_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('fecha_hora');
            $table->string('reason', 150);
            $table->enum('status', [
                'PENDIENTE', 'CONFIRMADA', 'EN_PROGRESO',
                'COMPLETADA', 'CANCELADA', 'NO_ASISTIO',
            ])->default('PENDIENTE');
            $table->text('notes')->nullable();
            $table->boolean('notificado_sms')->default(false);
            $table->boolean('notificado_whatsapp')->default(false);
            $table->boolean('notificado_email')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
