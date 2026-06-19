<?php

use App\Enums\OrderSide;
use App\Enums\OrderStatus;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('symbol', length: 8);
            $table->enum('side', OrderSide::cases());
            $table->decimal('price', 20, 8);
            $table->decimal('amount', 20, 8);
            $table->enum('status', OrderStatus::cases())->default(OrderStatus::Open);
            $table->timestamps();

            //Additional composite index to speed up queries 
            $table->index(['symbol', 'status', 'price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};