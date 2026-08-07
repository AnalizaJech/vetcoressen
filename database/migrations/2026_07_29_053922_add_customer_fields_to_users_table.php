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
        Schema::table('users', function (Blueprint $table) {
            $table->string('tipo_documento')->default('DNI')->after('phone');
            $table->string('numero_documento', 20)->nullable()->after('tipo_documento');
            $table->string('last_name', 150)->nullable()->after('name');
            $table->string('address', 255)->nullable()->after('numero_documento');
            $table->string('country', 255)->nullable()->after('address');
            $table->string('state', 255)->nullable()->after('country');
            $table->string('city', 255)->nullable()->after('state');
            $table->text('notes')->nullable()->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_documento',
                'numero_documento',
                'last_name',
                'address',
                'country',
                'state',
                'city',
                'notes'
            ]);
        });
    }
};
