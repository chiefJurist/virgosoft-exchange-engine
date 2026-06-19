<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            //Only reveal if the order belongs to the user.
            'personal_order' => $this->when($request->user()->id === $this->user_id, true, false),
            'symbol' => $this->symbol,
            'side' => $this->side,
            'price' => $this->price,
            'status' => $this->status,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}