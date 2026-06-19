<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can cancel any order.
     */
    public function cancel(User $user, Order $order): bool
    {
        return $user->id === $order->user_id;
    }
}
