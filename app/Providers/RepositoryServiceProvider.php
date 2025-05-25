<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Repositories\Interfaces\OwnershipRepositoryInterface;
use App\Repositories\OwnershipRepository;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\TransactionRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(OwnershipRepositoryInterface::class, OwnershipRepository::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
    }
}
