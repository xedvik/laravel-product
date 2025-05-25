<?php

namespace App\Policies;

use App\Models\User;

class ErrorPolicy
{

    /**
     * Определяет, может ли пользователь видеть детальные ошибки
     */
    public function viewDetailedErrors(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $user->role === 'admin';
    }

}
