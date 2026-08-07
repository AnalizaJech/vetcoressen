<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Alter Products table
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'base_imponible')) {
                $table->decimal('base_imponible', 10, 2)->after('sale_price')->default(0);
            }
            if (!Schema::hasColumn('products', 'igv_monto')) {
                $table->decimal('igv_monto', 10, 2)->after('base_imponible')->default(0);
            }
            if (!Schema::hasColumn('products', 'precio_final') && Schema::hasColumn('products', 'sale_price')) {
                $table->renameColumn('sale_price', 'precio_final');
            }
            if (!Schema::hasColumn('products', 'tipo_afectacion_igv_old') && Schema::hasColumn('products', 'afecto_igv')) {
                $table->renameColumn('afecto_igv', 'tipo_afectacion_igv_old');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'tipo_afectacion_igv')) {
                $table->enum('tipo_afectacion_igv', ['Gravado', 'Exonerado', 'Inafecto'])->default('Gravado')->after('tipo_afectacion_igv_old');
            }
            if (!Schema::hasColumn('products', 'margen_ganancia')) {
                $table->decimal('margen_ganancia', 5, 2)->nullable()->after('codigo_barras');
            }
        });

        // Use DB statement to alter enum
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE products MODIFY COLUMN type ENUM('PRODUCTO', 'SERVICIO', 'MEDICAMENTO', 'ALIMENTO', 'ACCESORIO') DEFAULT 'PRODUCTO'");
        }

        // Alter sale_details table
        Schema::table('sale_details', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_details', 'base_imponible')) {
                $table->decimal('base_imponible', 10, 2)->after('quantity')->default(0);
            }
            if (!Schema::hasColumn('sale_details', 'igv_monto')) {
                $table->decimal('igv_monto', 10, 2)->after('base_imponible')->default(0);
            }
            if (!Schema::hasColumn('sale_details', 'precio_final_unitario') && Schema::hasColumn('sale_details', 'unit_price')) {
                $table->renameColumn('unit_price', 'precio_final_unitario');
            }
            if (Schema::hasColumn('sale_details', 'afecto_igv')) {
                $table->dropColumn('afecto_igv');
            }
        });

        // Alter suppliers table
        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'country_id')) {
                $table->unsignedBigInteger('country_id')->nullable()->after('address');
            }
            if (!Schema::hasColumn('suppliers', 'city_id')) {
                $table->unsignedBigInteger('city_id')->nullable()->after('country_id');
            }
        });

        // Alter branches table
        Schema::table('branches', function (Blueprint $table) {
            if (!Schema::hasColumn('branches', 'country_id')) {
                $table->unsignedBigInteger('country_id')->nullable()->after('address');
            }
            if (!Schema::hasColumn('branches', 'city_id')) {
                $table->unsignedBigInteger('city_id')->nullable()->after('country_id');
            }
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE products MODIFY COLUMN type ENUM('PRODUCTO', 'SERVICIO') DEFAULT 'PRODUCTO'");
        }

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'precio_final')) {
                $table->renameColumn('precio_final', 'sale_price');
            }
            if (Schema::hasColumn('products', 'base_imponible')) {
                $table->dropColumn('base_imponible');
            }
            if (Schema::hasColumn('products', 'igv_monto')) {
                $table->dropColumn('igv_monto');
            }
            if (Schema::hasColumn('products', 'tipo_afectacion_igv')) {
                $table->dropColumn('tipo_afectacion_igv');
            }
            if (Schema::hasColumn('products', 'tipo_afectacion_igv_old')) {
                $table->renameColumn('tipo_afectacion_igv_old', 'afecto_igv');
            }
            if (Schema::hasColumn('products', 'margen_ganancia')) {
                $table->dropColumn('margen_ganancia');
            }
        });

        Schema::table('sale_details', function (Blueprint $table) {
            if (Schema::hasColumn('sale_details', 'base_imponible')) {
                $table->dropColumn('base_imponible');
            }
            if (Schema::hasColumn('sale_details', 'igv_monto')) {
                $table->dropColumn('igv_monto');
            }
            if (Schema::hasColumn('sale_details', 'precio_final_unitario')) {
                $table->renameColumn('precio_final_unitario', 'unit_price');
            }
            if (!Schema::hasColumn('sale_details', 'afecto_igv')) {
                $table->boolean('afecto_igv')->default(true);
            }
        });

        Schema::table('suppliers', function (Blueprint $table) {
            if (Schema::hasColumn('suppliers', 'country_id')) {
                $table->dropColumn('country_id');
            }
            if (Schema::hasColumn('suppliers', 'city_id')) {
                $table->dropColumn('city_id');
            }
        });

        Schema::table('branches', function (Blueprint $table) {
            if (Schema::hasColumn('branches', 'country_id')) {
                $table->dropColumn('country_id');
            }
            if (Schema::hasColumn('branches', 'city_id')) {
                $table->dropColumn('city_id');
            }
        });
    }
};
