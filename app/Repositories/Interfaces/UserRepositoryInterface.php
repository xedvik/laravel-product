<?php

namespace App\Repositories\Interfaces;

use App\DTO\Auth\RegisterDTO;
use App\Models\User;

interface UserRepositoryInterface
{
    // Метод для создания пользователя
    public function create(RegisterDTO $dto): ?User;
    // Метод для поиска пользователя по email
    public function findByEmail(string $email): ?User;
    // Метод для поиска пользователя по ID
    public function findById(int $id): ?User;

    // Метод для проверки баланса пользователя
    public function checkBalance(User $user): int;
    // Метод для обновления баланса пользователя
    public function updateBalance(User $user, int $amount): bool;
    // Метод для уменьшения баланса пользователя
    public function decreaseBalance(User $user, int $amount): bool;
}
