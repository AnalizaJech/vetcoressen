<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Modificar columna tipo de ENUM a VARCHAR y actualizar datos antiguos
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE products MODIFY COLUMN type VARCHAR(50) DEFAULT 'Accesorio'");
        }
        DB::statement("UPDATE products SET type = 'Accesorio' WHERE type = 'PRODUCTO'");
        DB::statement("UPDATE products SET type = 'Servicio' WHERE type = 'SERVICIO'");

        // 2. Agregar el campo requiere_receta
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('requiere_receta')->default(false)->after('afecto_igv');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('requiere_receta');
        });

        // 3. Revertir cambios de tipo a ENUM (Puede perder datos si hay valores nuevos)
        DB::statement("UPDATE products SET type = 'PRODUCTO' WHERE type != 'Servicio'");
        DB::statement("UPDATE products SET type = 'SERVICIO' WHERE type = 'Servicio'");
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE products MODIFY COLUMN type ENUM('PRODUCTO', 'SERVICIO') DEFAULT 'PRODUCTO'");
        }
    }
};
