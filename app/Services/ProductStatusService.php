<?php

namespace App\Services;

use App\DTO\Products\ProductStatusDTO;
use App\Models\OwnerShip;
use App\Repositories\Interfaces\OwnershipRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;

class ProductStatusService
{
    public function __construct(
        private OwnershipRepositoryInterface $ownershipRepository,
        private ProductRepositoryInterface $productRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * Проверяет статус товара по уникальному коду
     */
    public function checkStatusByUniqueCode(string $uniqueCode): ?OwnerShip
    {
        return $this->ownershipRepository->findByUniqueCode($uniqueCode);
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

        return $ownership;
    }

    /**
     * Получает все владения пользователя с уникальными кодами
     */
    public function getUserOwnershipsWithCodes(int $userId): array
    {
        $ownerships = $this->ownershipRepository->getUserOwnerships($userId);

        // Генерируем уникальные коды для владений, у которых их нет
        foreach ($ownerships as &$ownership) {
            if (!$ownership['unique_code']) {
                $ownershipModel = $this->ownershipRepository->findById($ownership['id']);
                $ownership['unique_code'] = $this->ownershipRepository->generateUniqueCodeForOwnership($ownershipModel->id);
            }
        }

        return $ownerships;
    }
}
