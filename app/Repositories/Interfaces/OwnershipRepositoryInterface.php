<?php

namespace App\Repositories\Interfaces;

use App\DTO\PurchaseDTO;
use App\Models\OwnerShip;
use App\Models\User;
use App\Models\Product;
use App\Enums\OwnershipType;

interface OwnershipRepositoryInterface
{
    public function userOwnsProduct(PurchaseDTO $dto): bool;
    public function createOwnership(User $user, Product $product, OwnershipType $type, int $amountPaid, ?string $uniqueCode = null): OwnerShip;
    public function getUserOwnerships(int $userId): array;
}
