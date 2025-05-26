<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\DTO\Products\ProductAuthorizationDTO;
use App\Policies\ProductPolicy;
use App\Policies\ErrorPolicy;
use App\Models\User;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [

    ];

    public function boot(): void
    {
        $this->registerPolicies();
        Gate::policy(ProductAuthorizationDTO::class, ProductPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        // Регистрируем Gate для проверки прав на просмотр детальных ошибок
        Gate::define('view-detailed-errors', [ErrorPolicy::class, 'viewDetailedErrors']);
    }
}
