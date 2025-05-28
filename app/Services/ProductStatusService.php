<?php

namespace App\Services;

use App\DTO\Products\ProductStatusDTO;
use App\Models\OwnerShip;
use App\Repositories\Interfaces\OwnershipRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Enums\OwnershipType;
use App\Enums\OwnershipStatus;

class ProductStatusService
{
    public function __construct(
        private OwnershipRepositoryInterface $ownershipRepository,
        private ProductRepositoryInterface $productRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * Централизованное определение статуса товара
     */
    public function determineProductStatus(OwnerShip|array $ownership): OwnershipStatus
    {
        if (is_array($ownership)) {
            $type = $ownership['type'];
            $rentalExpiresAt = $ownership['rental_expires_at'] ?? null;
        } else {
            $type = $ownership->type;
            $rentalExpiresAt = $ownership->rental_expires_at;
        }

        if ($type === OwnershipType::PURCHASE->value) {
            return OwnershipStatus::PURCHASED;
        }

        if ($type === OwnershipType::RENT->value) {
            if ($rentalExpiresAt && (is_string($rentalExpiresAt) ? strtotime($rentalExpiresAt) > time() : $rentalExpiresAt > now())) {
                return OwnershipStatus::RENTED_ACTIVE;
            }
            return OwnershipStatus::RENTED_EXPIRED;
        }

        return OwnershipStatus::UNKNOWN;
    }

    /**
     * Обновляет статус владения в базе данных
     */
    public function updateOwnershipStatus(OwnerShip $ownership): void
    {
        $newStatus = $this->determineProductStatus($ownership);

        if ($ownership->status !== $newStatus) {
            $ownership->update(['status' => $newStatus]);
        }
    }

    /**
     * Проверяет статус товара по уникальному коду
     */
    public function checkStatusByUniqueCode(string $uniqueCode): ?OwnerShip
    {
        $ownership = $this->ownershipRepository->findByUniqueCode($uniqueCode);

        if ($ownership) {
            $this->updateOwnershipStatus($ownership);
            $ownership = $ownership->fresh();
        }

        return $ownership;
    }

    /**
     * Проверяет статус товара для пользователя и генерирует уникальный код при первой проверке
     */
    public function checkUserProductStatus(ProductStatusDTO $dto): ?OwnerShip
    {
        $user = $this->userRepository->findById($dto->userId);
        $product = $this->productRepository->findById($dto->productId);

        if (!$user || !$product) {
            return null;
        }
        $ownership = $this->ownershipRepository->findUserOwnership($dto->userId, $dto->productId);

        if (!$ownership) {
            return null;
        }

        if (!$ownership->unique_code) {
            $this->ownershipRepository->generateUniqueCodeForOwnership($ownership->id);
            $ownership = $ownership->fresh();
        }

        // Обновляем статус
        $this->updateOwnershipStatus($ownership);
        $ownership = $ownership->fresh();

        return $ownership;
    }

        /**
     * Получает все владения пользователя с уникальными кодами
     */
    public function getUserOwnershipsWithCodes(int $userId): array
    {
        $ownerships = $this->ownershipRepository->getUserOwnerships($userId);

        // Генерируем уникальные коды для владений, у которых их нет, и обновляем статус
        foreach ($ownerships as &$ownership) {
            $ownershipModel = $this->ownershipRepository->findById($ownership['id']);

            if (!$ownership['unique_code']) {
                $ownership['unique_code'] = $this->ownershipRepository->generateUniqueCodeForOwnership($ownershipModel->id);
            }

            $this->updateOwnershipStatus($ownershipModel);
            $ownership['status'] = $ownershipModel->fresh()->status->value;
        }

        return $ownerships;
    }
}
