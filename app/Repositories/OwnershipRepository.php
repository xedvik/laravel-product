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

    /**
     * Проверяет, владеет ли пользователь товаром
     */
    public function userOwnsProduct(PurchaseDTO $dto): bool
    {
        return OwnerShip::where('user_id', $dto->user_id)
            ->where('product_id', $dto->product_id)
            ->where('type', $dto->type->value)
            ->exists();
    }

    /**
     * Создает запись о владении
     */
    public function createOwnership(User $user, Product $product, OwnershipType $type, int $amountPaid, ?string $uniqueCode = null, ?int $hours = null): OwnerShip
    {
        return OwnerShip::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'type' => $type->value,
            'unique_code' => $uniqueCode ?? Str::uuid(),
            'amount_paid' => $amountPaid,
            'rental_expires_at' => $type === OwnershipType::RENT ? now()->addHours($hours) : null,
        ]);
    }

    /**
     * Получает все владения пользователя
     */
    public function getUserOwnerships(int $userId): array
    {
        return OwnerShip::where('user_id', $userId)
            ->with(['product'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Проверяет, есть ли активная аренда товара (исключая указанного пользователя)
     */
    public function hasActiveRental(int $productId): bool
    {
        $query = OwnerShip::where('product_id', $productId)
            ->where('type', OwnershipType::RENT->value)
            ->where('rental_expires_at', '>', now());


        return $query->exists();
    }

    /**
     * Проверяет, куплен ли товар кем-то
     */
    public function isProductPurchased(int $productId): bool
    {
        return OwnerShip::where('product_id', $productId)
            ->where('type', OwnershipType::PURCHASE->value)
            ->exists();
    }

    /**
     * Проверяет, есть ли у пользователя активная аренда этого товара
     */
    public function hasUserActiveRental(int $userId, int $productId): bool
    {
        return OwnerShip::where('user_id', $userId)
            ->where('product_id', $productId)
            ->where('type', OwnershipType::RENT->value)
            ->where('rental_expires_at', '>', now())
            ->exists();
    }

    public function findById(int $id): OwnerShip
    {
        return OwnerShip::findOrFail($id);
    }
    public function extendRental(OwnerShip $ownership, int $additionalHours, int $additionalCost): OwnerShip
    {
        $ownership->update([
            'rental_expires_at' => $ownership->rental_expires_at->addHours($additionalHours),
            'amount_paid' => $ownership->amount_paid + $additionalCost,
        ]);

        return $ownership->fresh();
    }
}
