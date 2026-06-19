<?php

namespace App\Http\Controllers;

use App\Enums\OrderSide;
use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Jobs\ProcessOrderMatch;
use App\Models\Asset;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'symbol' => 'required|string|in:BTC,ETH'
        ]);
        $symbol = $validated['symbol'];

        /** @disregard P1005 */
        $buys = Order::where('symbol', $symbol)
            ->where('side', OrderSide::Buy)
            ->where('status', OrderStatus::Open)
            ->orderByDesc('price')
            ->orderBy('created_at')
            ->get();

        /** @disregard P1005 */
        $sells = Order::where('symbol', $symbol)
            ->where('side', OrderSide::Sell)
            ->where('status', OrderStatus::Open)
            ->orderByDesc('price')
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'symbol' => $symbol,
            'buys' => OrderResource::collection($buys),
            'sells' => OrderResource::collection($sells),
        ]);
    }
}
