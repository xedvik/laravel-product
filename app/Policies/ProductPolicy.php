<?php

namespace App\Policies;

use App\DTO\Products\ProductAuthorizationDTO;
use App\Models\User;


class ProductPolicy
{

    public function create(User $user, ProductAuthorizationDTO $product): bool
    {
        return $user->role === 'admin';
    }


    public function update(User $user, ProductAuthorizationDTO $product): bool
    {
        return $user->role === 'admin';
    }


    public function delete(User $user, ProductAuthorizationDTO $product): bool
    {
        return $user->role === 'admin';
    }


}
