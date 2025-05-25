<?php

namespace App\Services;

use App\DTO\PurchaseDTO;
use App\Models\OwnerShip;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\OwnershipRepositoryInterface;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\ProductAlreadyOwnedException;
use App\Exceptions\ProductNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ProductRepositoryInterface $productRepository,
        private OwnershipRepositoryInterface $ownershipRepository,
        private TransactionRepositoryInterface $transactionRepository
    ) {
    }

    public function purchaseProduct(PurchaseDTO $dto): OwnerShip
    {
        return DB::transaction(function () use ($dto) {
            // Получаем пользователя и товар
            $user = $this->userRepository->findById($dto->user_id);
            $product = $this->productRepository->findById($dto->product_id);

            if (!$product) {
                throw new ProductNotFoundException('Товар не найден');
            }

            // Проверяем, не владеет ли пользователь уже этим товаром
            if ($this->ownershipRepository->userOwnsProduct($dto)) {
                throw new ProductAlreadyOwnedException('Пользователь уже владеет этим товаром');
            }

            // Проверяем баланс пользователя
            if ($user->balance < $product->purchase_price) {
                throw new InsufficientBalanceException('Недостаточно средств для покупки');
            }

            // Списываем средства с баланса
            if (!$this->userRepository->decreaseBalance($user, $product->purchase_price)) {
                throw new InsufficientBalanceException('Ошибка при списании средств');
            }

            // Создаем запись о владении
            $ownership = $this->ownershipRepository->createOwnership(
                user: $user,
                product: $product,
                type: $dto->type,
                amountPaid: $product->purchase_price,
                uniqueCode: $dto->unique_code
            );

            // Создаем транзакцию
            $this->transactionRepository->create(
                user: $user,
                product: $product,
                type: 'purchase',
                amount: $product->purchase_price
            );

            Log::info('Товар успешно куплен', [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'amount' => $product->purchase_price
            ]);

            return $ownership;
        });
    }
}
