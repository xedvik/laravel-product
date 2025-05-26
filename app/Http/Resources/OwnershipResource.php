<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OwnershipResource extends JsonResource
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
            'type' => $this->type,
            'unique_code' => $this->unique_code,
            'amount_paid' => $this->amount_paid,
            'rental_expires_at' => $this->rental_expires_at,
            'purchased_at' => $this->created_at,
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'description' => $this->product->description,
                'purchase_price' => $this->product->purchase_price,
                'rent_price_per_hour' => $this->product->rent_price_per_hour,
            ],
        ];
    }
}
