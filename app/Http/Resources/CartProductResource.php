<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'price' => $this->pivot->unit_price,
            'quantity' => $this->pivot->quantity,
            'total' => $this->pivot->unit_price * $this->pivot->quantity
        ];
    }
}
