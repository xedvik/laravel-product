<?php

namespace App\Repositories\Interfaces;

use App\DTO\Auth\RegisterDTO;
use App\DTO\Auth\LoginDTO;
use App\Models\User;

interface UserRepositoryInterface
{
    public function create(RegisterDTO $dto): ?User;
    public function findByEmail(string $email): ?User;
}
