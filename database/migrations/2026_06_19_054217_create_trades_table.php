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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buy_order_id')->constrained(table: 'orders')->onDelete('cascade');
            $table->foreignId('sell_order_id')->constrained(table: 'orders')->onDelete('cascade');
            $table->string('symbol', length: 8);
            $table->decimal('price', 20, 8);
            $table->decimal('amount', 20, 8);
            $table->decimal('commission', 20, 8);
            $table->decimal('asset_commission', 20, 8);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};