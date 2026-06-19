<?php

namespace App\Models;

use App\Enums\OrderSide;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['user_id', 'symbol', 'side', 'price', 'amount', 'status'])]
class Order extends Model
{
    //
    protected function casts(): array
    {
        return [
            'price' => 'decimal:8',
            'amount' => 'decimal:8',
            'side' => OrderSide::class,
            'status' => OrderStatus::class
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function buyTrade(): HasOne
    {
        return $this->hasOne(Trade::class, 'buy_order_id');
    }

    public function sellTrade(): HasOne
    {
        return $this->hasOne(Trade::class, 'sell_order_id');
    }
}