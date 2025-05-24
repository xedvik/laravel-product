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
        $user = User::where('email', $email)->first();
        return $user;
    }

    public function findById(int $id): ?User
    {
        return User::findOrFail($id);
    }
}