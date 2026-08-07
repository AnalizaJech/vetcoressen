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
            $table->foreignId('appointment_reason_id')->nullable()->constrained('appointment_reasons');
            $table->time('end_time')->nullable()->after('fecha_hora');
            
            // Si el enum ya existe, no se puede modificar fácilmente en SQLite/MySQL antiguo sin Doctrine DBAL.
            // Es preferible usar un ALTER TABLE si el estado es un ENUM en BD.
            // Para el propósito de esta migración, si la tabla usa STRING para estado, lo dejamos intacto o lo alteramos:
            // DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('PENDIENTE', 'CONFIRMADA', 'EN_PROGRESO', 'COMPLETADA', 'CANCELADA', 'EMERGENCIA', 'EXCEDIDO') DEFAULT 'PENDIENTE'");
        });
        
        // Ejecutamos alteración directa si estamos seguros que es MySQL.
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('PENDIENTE', 'CONFIRMADA', 'EN_PROGRESO', 'COMPLETADA', 'CANCELADA', 'EMERGENCIA', 'EXCEDIDO', 'REAGENDA_REQUERIDA') DEFAULT 'PENDIENTE'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['appointment_reason_id']);
            $table->dropColumn(['appointment_reason_id', 'end_time']);
        });
        
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('PENDIENTE', 'CONFIRMADA', 'EN_PROGRESO', 'COMPLETADA', 'CANCELADA') DEFAULT 'PENDIENTE'");
        }
    }
};
