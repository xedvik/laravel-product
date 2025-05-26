<?php

namespace App\Repositories\Interfaces;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Product;

interface TransactionRepositoryInterface
{
    // Метод для создания транзакции
    public function create(User $user, Product $product, string $type, int $amount): Transaction;
    // Метод для получения всех транзакций пользователя
    public function getUserTransactions(int $userId): array;
    // Метод для получения всех транзакций товара
    public function getProductTransactions(int $productId): array;
}
