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
        Schema::table('sales', function (Blueprint $table) {
            $table->string('nubefact_enlace_pdf')->nullable();
            $table->string('nubefact_enlace_xml')->nullable();
            $table->string('nubefact_enlace_cdr')->nullable();
            $table->string('nubefact_sunat_ticket_numero')->nullable();
            $table->text('nubefact_error')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'nubefact_enlace_pdf',
                'nubefact_enlace_xml',
                'nubefact_enlace_cdr',
                'nubefact_sunat_ticket_numero',
                'nubefact_error'
            ]);
        });
    }
};
