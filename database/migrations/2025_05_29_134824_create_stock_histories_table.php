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
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('symbol');
            $table->string('name');
            $table->decimal('open', 10, 2);
            $table->decimal('high', 10, 2);
            $table->decimal('low', 10, 2);
            $table->decimal('close', 10, 2);
            $table->timestamps();

            $table->index(['user_id', 'symbol'], 'user_symbol_index');
            $table->index(['created_at'], 'created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
    }
};
