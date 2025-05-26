<?php

namespace App\Services;
use App\Repositories\Interfaces\OwnershipRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\DTO\Rent\RentDTO;
use App\DTO\Rent\ExtendRentDTO;
use App\Models\OwnerShip;
use App\Enums\OwnershipType;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\ProductAlreadyOwnedException;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\InvalidRentDurationException;
use App\Exceptions\ProductNotAvailableException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\OwnershipNotFoundException;
use App\Exceptions\RentExpiredException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RentService
{
    public function __construct(
        private OwnershipRepositoryInterface $ownershipRepository,
        private ProductRepositoryInterface $productRepository,
        private UserRepositoryInterface $userRepository,
        private TransactionRepositoryInterface $transactionRepository
    ) {
    }

    public function rentProduct(RentDTO $dto): OwnerShip
    {
        return DB::transaction(function () use ($dto) {
            //  Проверка существования товара и пользователя
            $product = $this->productRepository->findById($dto->product_id);
            $user = $this->userRepository->findById($dto->user_id);

            if (!$product) {
                throw new ProductNotFoundException('Товар не найден');
            }
            if (!$user) {
                throw new UserNotFoundException('Пользователь не найден');
            }

            //  Проверка доступности товара для аренды
            if ($this->ownershipRepository->isProductPurchased($dto->product_id)) {
                throw new ProductNotAvailableException('Товар уже куплен и недоступен для аренды');
            }
            //  Проверка, что пользователь уже не арендует этот товар
            if ($this->ownershipRepository->hasUserActiveRental($dto->user_id, $dto->product_id)) {
                throw new ProductAlreadyOwnedException('Вы уже арендуете этот товар');
            }
            //  Проверка, что товар не арендован другим пользователем
            if ($this->ownershipRepository->hasActiveRental($dto->product_id)) {
                throw new ProductNotAvailableException('Товар уже арендован другим пользователем');
            }

            //  Расчет стоимости
            $totalCost = $product->rent_price_per_hour * $dto->hours;

            //  Проверка баланса пользователя
            if ($user->balance < $totalCost) {
                throw new InsufficientBalanceException('Недостаточно средств для аренды');
            }

            //  Списываем средства с баланса
            if (!$this->userRepository->decreaseBalance($user, $totalCost)) {
                throw new InsufficientBalanceException('Ошибка при списании средств');
            }

            //  Создаем запись о владении
            $ownership = $this->ownershipRepository->createOwnership(
                user: $user,
                product: $product,
                type: $dto->type,
                amountPaid: $totalCost,
                uniqueCode: $dto->unique_code,
                hours: $dto->hours
            );

            //  Создаем транзакцию
            $this->transactionRepository->create(
                user: $user,
                product: $product,
                type: $dto->type->value,
                amount: $totalCost
            );

            //  Логирование
            Log::info('Товар успешно арендован', [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'amount' => $totalCost,
                'hours' => $dto->hours,
                'expires_at' => $ownership->rental_expires_at,
                'unique_code' => $ownership->unique_code
            ]);

            return $ownership;
        });
    }

    public function extendRent(ExtendRentDTO $dto): OwnerShip
    {
        return DB::transaction(function () use ($dto) {

            $ownership = $this->ownershipRepository->findById($dto->ownership_id);

            if (!$ownership) {
                throw new OwnershipNotFoundException('Аренда не найдена');
            }

            if ($ownership->type !== OwnershipType::RENT->value) {
                throw new OwnershipNotFoundException('Указанная запись не является арендой');
            }

            if ($ownership->rental_expires_at <= now()) {
                throw new RentExpiredException('Аренда уже истекла, продление невозможно');
            }

            $product = $this->productRepository->findById($ownership->product_id);
            $user = $this->userRepository->findById($ownership->user_id);

            if (!$product) {
                throw new ProductNotFoundException('Товар не найден');
            }

            if (!$user) {
                throw new UserNotFoundException('Пользователь не найден');
            }

            //  Рассчитываем общее время аренды после продления
            $currentTotalHours = $ownership->created_at->diffInHours($ownership->rental_expires_at);
            $newTotalHours = $currentTotalHours + $dto->additional_hours;

            if ($newTotalHours > 24) {
                throw new InvalidRentDurationException(
                    "Общее время аренды не может превышать 24 часа. Текущее время: {$currentTotalHours}ч, запрошено: {$dto->additional_hours}ч"
                );
            }

            //  Рассчитываем стоимость продления
            $additionalCost = $product->rent_price_per_hour * $dto->additional_hours;

            if ($user->balance < $additionalCost) {
                throw new InsufficientBalanceException('Недостаточно средств для продления аренды');
            }

            //  Списываем средства с баланса
            if (!$this->userRepository->decreaseBalance($user, $additionalCost)) {
                throw new InsufficientBalanceException('Ошибка при списании средств');
            }

            //  Продлеваем аренду
            $extendedOwnership = $this->ownershipRepository->extendRental(
                $ownership,
                $dto->additional_hours,
                $additionalCost
            );

            //  Создаем транзакцию для продления
            $this->transactionRepository->create(
                user: $user,
                product: $product,
                type: $ownership->type,
                amount: $additionalCost
            );

            //  Логирование
            Log::info('Аренда успешно продлена', [
                'ownership_id' => $ownership->id,
                'user_id' => $user->id,
                'product_id' => $product->id,
                'additional_hours' => $dto->additional_hours,
                'additional_cost' => $additionalCost,
                'new_expires_at' => $extendedOwnership->rental_expires_at,
                'total_hours' => $newTotalHours,
                'total_paid' => $extendedOwnership->amount_paid,
            ]);

            return $extendedOwnership;
        });
    }
}
