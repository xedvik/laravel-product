<?php

namespace App\Repositories\Interfaces;

use App\DTO\Auth\RegisterDTO;
use App\Models\User;

interface UserRepositoryInterface
{
    public function create(RegisterDTO $dto): ?User;
    public function findByEmail(string $email): ?User;
    public function findById(int $id): ?User;

    public function checkBalance(User $user): int;
    public function updateBalance(User $user, int $amount): bool;
    public function decreaseBalance(User $user, int $amount): bool;
}
