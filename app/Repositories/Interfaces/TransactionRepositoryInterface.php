<?php

namespace App\Repositories\Interfaces;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Product;

interface TransactionRepositoryInterface
{
    public function create(User $user, Product $product, string $type, int $amount): Transaction;
    public function getUserTransactions(int $userId): array;
    public function getProductTransactions(int $productId): array;
}
