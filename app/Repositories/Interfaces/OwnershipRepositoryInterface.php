<?php

namespace App\Repositories\Interfaces;

use App\DTO\PurchaseDTO;
use App\Models\OwnerShip;
use App\Models\User;
use App\Models\Product;
use App\Enums\OwnershipType;

interface OwnershipRepositoryInterface
{
    // Метод для проверки, владеет ли пользователь товаром
    public function userOwnsProduct(PurchaseDTO $dto): bool;
    // Метод для создания владения
    public function createOwnership(User $user, Product $product, OwnershipType $type, int $amountPaid, ?string $uniqueCode = null, ?int $hours = null): OwnerShip;
    // Метод для получения всех владений пользователя
    public function getUserOwnerships(int $userId): array;

    // Методы для проверки доступности товара для аренды
    public function hasActiveRental(int $productId, ?int $excludeUserId = null): bool;
    // Метод для проверки, куплен ли товар
    public function isProductPurchased(int $productId): bool;
    // Метод для проверки, есть ли у пользователя активная аренда этого товара
    public function hasUserActiveRental(int $userId, int $productId): bool;
    // Метод для поиска владения по ID
    public function findById(int $id): OwnerShip;
    // Метод для продления аренды
    public function extendRental(OwnerShip $ownership, int $additionalHours, int $additionalCost): OwnerShip;
}
