<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Product;
use App\Repositories\Interfaces\TransactionRepositoryInterface;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function create(User $user, Product $product, string $type, int $amount): Transaction
    {
        return Transaction::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'type' => $type,
            'amount' => $amount,
        ]);
    }

    public function getUserTransactions(int $userId): array
    {
        return Transaction::where('user_id', $userId)
            ->with(['product'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function getProductTransactions(int $productId): array
    {
        return Transaction::where('product_id', $productId)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
}
