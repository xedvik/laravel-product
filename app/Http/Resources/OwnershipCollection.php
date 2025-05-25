<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OwnershipCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ownerships' => OwnershipResource::collection($this->collection),
        ];
    }

    public function with(Request $request): array
    {
        return [
            'meta' => [
                'total' => $this->collection->count(),
                'generated_at' => now()->toISOString(),
                'user_id' => auth()->id(),
            ],
        ];
    }
}
