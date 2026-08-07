<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['country_id', 'city_id']);
            $table->string('country')->nullable()->after('address');
            $table->string('state')->nullable()->after('country');
            $table->string('city')->nullable()->after('state');
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['country_id', 'city_id']);
            $table->string('country')->nullable()->after('address');
            $table->string('state')->nullable()->after('country');
            $table->string('city')->nullable()->after('state');
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['country', 'state', 'city']);
            $table->unsignedBigInteger('country_id')->nullable()->after('address');
            $table->unsignedBigInteger('city_id')->nullable()->after('country_id');
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['country', 'state', 'city']);
            $table->unsignedBigInteger('country_id')->nullable()->after('address');
            $table->unsignedBigInteger('city_id')->nullable()->after('country_id');
        });
    }
};
