<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\OwnershipType;


class ProductStatusResource extends JsonResource
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
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'description' => $this->product->description,
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'ownership_type' => $this->type,
            'unique_code' => $this->unique_code,
            'amount_paid' => $this->amount_paid,
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->getLabel(),
                'description' => $this->status->getDescription(),
            ],
            'rental_info' => $this->when($this->type === OwnershipType::RENT->value, [
                'expires_at' => $this->rental_expires_at?->format('Y-m-d H:i:s'),
                'remaining_hours' => $this->rental_expires_at ?
                    max(0, now()->diffInHours($this->rental_expires_at, false)) : 0,
            ]),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
