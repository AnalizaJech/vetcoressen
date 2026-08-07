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
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users'); // Cajero
            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();
            $table->decimal('opening_amount', 10, 2);
            $table->decimal('calculated_amount', 10, 2)->default(0); // Sum of sales
            $table->decimal('real_amount', 10, 2)->nullable(); // User input when closing
            $table->decimal('difference', 10, 2)->nullable(); // real - calculated
            $table->text('notes')->nullable();
            $table->enum('status', ['ABIERTA', 'CERRADA'])->default('ABIERTA');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};
