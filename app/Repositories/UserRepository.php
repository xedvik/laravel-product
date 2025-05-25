<?php

namespace App\Repositories;

use App\Models\User;
use App\DTO\Auth\RegisterDTO;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function create(RegisterDTO $dto):?User
    {
        return User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);
    }

    public function findByEmail(string $email):?User
    {

        return User::where('email', $email)->first();
    }

    public function findById(int $id): ?User
    {
        return User::findOrFail($id);
    }

    public function checkBalance(User $user): int
    {
        return $user->balance;
    }

    public function updateBalance(User $user, int $amount): bool
    {
        return $user->update(['balance' => $amount]);
    }

    public function decreaseBalance(User $user, int $amount): bool
    {
        if ($user->balance < $amount) {
            return false;
        }

        return $user->update(['balance' => $user->balance - $amount]);
    }
}
