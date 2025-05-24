<?php

namespace App\Services;

use App\DTO\Auth\RegisterDTO;
use App\DTO\Auth\LoginDTO;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

use Illuminate\Support\Facades\Hash;
class UserService
{
    private $userRepository;
    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function create(RegisterDTO $dto):?User
    {
        $user = $this->userRepository->create($dto);
        if (!$user) {
            return null;
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->token = $token;
        return $user;
    }

    public function login(LoginDTO $dto):?User
    {
        $user = $this->userRepository->findByEmail($dto->email);
        if (!$user || !Hash::check($dto->password, $user->password)) {
            return null;
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->token = $token;
        return $user;
    }

    public function logout(User $user): bool
    {
        // Удаляем все токены пользователя
        return $user->tokens()->delete();
    }
}