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



/**
     * Disregard possible extension alerts not up to date with laarvel 13.
     *
     * @disregard PHP0417
     */


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

    public function store(StoreOrderRequest $request)
    {
        //
        $validated = $request->validated();
        $user = $request->user();

        $symbol = $validated['symbol'];
        $side = $validated['side'];
        $price = $validated['price'];
        $amount = $validated['amount'];
        $total = ($price * $amount ) + ($price * $amount * 0.015); //added commission

        try {
            $order = DB::transaction(function () use ($user, $symbol, $side, $price, $amount, $total) {
                //lock user to prevent race conditions
                $user = User::lockForUpdate()->find($user->id);

                if ($side == 'buy') {
                    if ($user->balance < $total) {
                        throw new \Exception('Insufficient USD balance');
                    }

                    //update buyer's balance
                    $user->decrement('balance', $total);
                } else {
                    /** @disregard P1005 */
                    $asset = Asset::where('user_id', $user->id)
                        ->where('symbol', $symbol)
                        ->lockForUpdate()
                        ->first();

                    //check if user has enough asset balance
                    $amountWithCommission = ($amount) + ($amount * 0.015);
                    if (!$asset || $asset->amount < $amountWithCommission) {
                        throw new \Exception('Insufficient asset balance');
                    }

                    //move asset to locked amount
                    $asset->decrement('amount', $amountWithCommission);
                    $asset->increment('locked_amount', $amountWithCommission);
                }

                //create the order
                $order = Order::create([
                    'user_id' => $user->id,
                    'symbol' => $symbol,
                    'side' => $side,
                    'price' => $price,
                    'amount' => $amount,
                    'status' => OrderStatus::Open,
                ]);

                return $order;
            });

            //dispatch order and return the return order resource
            ProcessOrderMatch::dispatch($order);
            return new OrderResource($order);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
