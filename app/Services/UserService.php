<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\OwnershipRepositoryInterface;

class UserService
{
    private $userRepository;
    private $ownershipRepository;
    public function __construct(
        UserRepositoryInterface $userRepository,
        OwnershipRepositoryInterface $ownershipRepository
    ) {
        $this->userRepository = $userRepository;
        $this->ownershipRepository = $ownershipRepository;
    }

    public function findById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function getUserInfo(User $user): User
    {
        $user->balance = $this->userRepository->checkBalance($user);
        $user->ownerships = $this->ownershipRepository->getUserOwnerships($user->id);
        return $user;
    }
    public function getUserBalance(User $user): User
    {
        $user->balance = $this->userRepository->checkBalance($user);
        return $user;
    }
    public function updateUserBalance(User $user, int $amount):User
    {
        $this->userRepository->updateBalance($user, $amount);
        return $user;
    }
}