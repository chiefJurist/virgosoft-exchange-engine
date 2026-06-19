<?php

namespace App\Services;

use App\Enums\OrderSide;
use App\Enums\OrderStatus;
use App\Events\OrderMatched;
use App\Models\Asset;
use App\Models\Order;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderMatchingService
{
    public function matchOrder(Order $newOrder)
    {
        DB::transaction(function () use ($newOrder) {
            // lock the new order to prevent duplicate processing
            Log::info('about to match');
            $newOrder = Order::lockForUpdate()->find($newOrder->id);
            //skip order if it is no longer open
            if ($newOrder->status !== OrderStatus::Open) {
                return;
            }
            //find a matching order of the order side
            $matchedOrder = $this->findMatch($newOrder);
            if (!$matchedOrder) {
                return; //no match found so order stays open
            }
            //determine buy and sell orders
            if ($newOrder->side === OrderSide::Buy) {
                $buyOrder = $newOrder;
                $sellOrder = $matchedOrder;
            } else {
                $buyOrder = $matchedOrder;
                $sellOrder = $newOrder;
            }

            //lock both users
            $buyer = User::lockForUpdate()->find($buyOrder->user_id);
            $seller = User::lockForUpdate()->find($sellOrder->user_id);

            //settlement price, amount, trade value and commissions
            $settlementPrice = $sellOrder->price;
            $amount = $sellOrder->amount;
            $tradeValue = $settlementPrice * $amount;
            $commission = $tradeValue * 0.015;
            $assetCommission = $amount * 0.015;

            //refund buyer the price difference minus commission
            $buyerPaid = $buyOrder->price * $amount * 1.015;
            $buyerOwes = $tradeValue * 1.015;
            $refund = ($buyerPaid - $buyerOwes);

            if ($refund > 0) {
                $buyer->increment('balance', $refund);
            }

            //credit seller's balance
            $seller->increment('balance', $tradeValue);

            //Credit buyer's asset
            /** @disregard P1005 */
            $buyerAsset = Asset::where('user_id', $buyer->id)
                ->where('symbol', $buyOrder->symbol)
                ->lockForUpdate()
                ->first();

            if ($buyerAsset) {
                $buyerAsset->increment('amount', $amount);
            } else {
                Asset::create([
                    'user_id' => $buyer->id,
                    'symbol' => $buyOrder->symbol,
                    'amount' => $amount,
                    'locked_amount' => 0
                ]);
            }
            
            //release seller's locked amount
            /** @disregard P1005 */
            $sellerAsset = Asset::where('user_id', $seller->id)
                ->where('symbol', $sellOrder->symbol)
                ->lockForUpdate()
                ->first();

            $locked_amount = $amount + $assetCommission;
            $sellerAsset->decrement('locked_amount', $locked_amount);

            //mark both orders as filled
            $buyOrder->update(['status' => OrderStatus::Filled]);
            $sellOrder->update(['status' => OrderStatus::Filled]);

            //Create trade record
            $trade = Trade::create([
                'buy_order_id' => $buyOrder->id,
                'sell_order_id' => $sellOrder->id,
                'symbol' => $buyOrder->symbol,
                'price' => $settlementPrice,
                'amount' => $amount,
                'commission' => $commission,
                'asset_commission' => $assetCommission,
            ]);

            //Broadcast OrderMatched event
            event(new OrderMatched($trade, $buyer, $seller));

        });
    }

    private function findMatch(Order $order) : ?Order
    {
        if ($order->side === OrderSide::Buy) {
            //find the cheapest sell order where price is <= buy price
            /** @disregard P1005 */
            return Order::where('symbol', $order->symbol)
                ->where('side', OrderSide::Sell)
                ->where('status', OrderStatus::Open)
                ->where('price', '<=', $order->price)
                ->where('amount', $order->amount)
                ->lockForUpdate()
                ->orderBy('price')
                ->orderBy('created_at')
                ->first();
        } else {
            //find the most expensive buy order where price is >= sell price
            /** @disregard P1005 */
            return Order::where('symbol', $order->symbol)
                ->where('side', OrderSide::Buy)
                ->where('status', OrderStatus::Open)
                ->where('price', '>=', $order->price)
                ->where('amount', $order->amount)
                ->lockForUpdate()
                ->orderByDesc('price')
                ->orderBy('created_at')
                ->first();
        }
    }
}