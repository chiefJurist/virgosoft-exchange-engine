<?php

namespace App\Events;

use App\Models\Trade;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderMatched
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Trade $trade, public User $buyer, public User $seller)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->buyer->id),
            new PrivateChannel('user.' . $this->seller->id)
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'trade' => [
                'id' => $this->trade->id,
                'symbol' => $this->trade->symbol,
                'price' => $this->trade->price,
                'amount' => $this->trade->amount,
                'commission' => $this->trade->commission,
            ],
            'buyer' => [
                'id' => $this->buyer->id,
                'balance' => $this->buyer->fresh()->balance,
                'assets' => $this->buyer->fresh()->assets,
            ],
            'seller' => [
                'id' => $this->seller->id,
                'balance' => $this->seller->fresh()->balance,
                'assets' => $this->seller->fresh()->assets,
            ],
        ];
    }

    public function broadcastAs(): string
    {
        return 'OrderMatched';
    }
}
