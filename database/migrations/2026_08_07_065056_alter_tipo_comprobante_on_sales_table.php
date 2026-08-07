<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE sales MODIFY COLUMN tipo_comprobante VARCHAR(30) DEFAULT 'NOTA_VENTA'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE sales MODIFY COLUMN tipo_comprobante ENUM('TICKET', 'BOLETA', 'FACTURA') DEFAULT 'TICKET'");
    }
};
