<?php

namespace App\Repositories;

use App\Models\OwnerShip;
use App\Models\User;
use App\Models\Product;
use App\Repositories\Interfaces\OwnershipRepositoryInterface;
use App\DTO\PurchaseDTO;
use App\Enums\OwnershipType;
use Illuminate\Support\Str;

class OwnershipRepository implements OwnershipRepositoryInterface
{

    public function userOwnsProduct(PurchaseDTO $dto): bool
    {
        return OwnerShip::where('user_id', $dto->user_id)
            ->where('product_id', $dto->product_id)
            ->where('type', $dto->type->value)
            ->exists();
    }

    public function createOwnership(User $user, Product $product, OwnershipType $type, int $amountPaid, ?string $uniqueCode = null): OwnerShip
    {
        return OwnerShip::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'type' => $type->value,
            'unique_code' => $uniqueCode ?? Str::uuid(),
            'amount_paid' => $amountPaid,
            'rental_expires_at' => $type === OwnershipType::RENT ? now()->addHours(1) : null,
        ]);
    }

    public function getUserOwnerships(int $userId): array
    {
        return OwnerShip::where('user_id', $userId)
            ->with(['product'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
}
