<?php
namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function getUserInfo(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }
    public function updateUserBalance(User $user, User $model): bool
    {
        return $user->id === $model->id && $user->role === 'admin';
    }
    public function getUserBalance(User $user, User $model): bool
    {
        return $user->id === $model->id && $user->role === 'admin';
    }


}